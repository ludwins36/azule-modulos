<?php

/**
 * 2007-2019 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2019 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/classes/class-license.php';
require_once dirname(__FILE__) . '/classes/class-curl.php';
require_once dirname(__FILE__) . '/classes/class-request-urbaner.php';
require_once dirname(__FILE__) . '/sql/request.php';

class VexUrbaner extends CarrierModule
{
    const CONFIG_LICENSE = 'VEX_URBANER_LICENSE';
    const CONFIG_API_KEY = 'VEX_URBANER_API_KEY';
    const CONFIG_API_SECRET = 'VEX_URBANER_API_SECRET';
    const CONFIG_KEY_GOOGLE_MAPS = 'VEX_URBANER_GOOGLE_MAPS_KEY';
    const CONFIG_PRICE = 'VEX_URBANER_PRICE';
    const CONFIG_AMOUNT = 'VEX_URBANER_AMOUNT';
    const CONFIG_STATUS = 'vex_urbaner_status';

    const URBANER_STATUS_CANC = 'CANCELED';
    // TODO las api de urbaner
    const API_CODE_TEST = 'https://api.sandbox.urbaner.com/api/';
    const API_CODE_LIVE = 'https://middleware.urbaner.com/api/';
    const CONGIG_AMOUNT_ORDER = 'AMOUNT_ORDER';

    const DIMENTIONS_1 = 30 * 30 * 30;
    const DIMENTIONS_2 = 40 * 40 * 40;

    const PESO_1 = 3;
    const PESO_2 = 12;

    public function __construct()
    {
        $this->name = 'vexurbaner';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'Vex_soluciones';
        /*
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();
        $this->displayName = $this->l('Urbaner');

        $this->description = $this->l('rastrea tus productos de forma facil y rapida');

        $this->confirmUninstall = $this->l('esta seguro que desea desistalar el modulo');
        $this->request = new VexUrbanerRequest($this);

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->limited_currencies = array('PE');
        $this->iso_code = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));
        $this->diasSemana = array(
            $this->l('Lunes'),
            $this->l('Martes'),
            $this->l('Miercoles'),
            $this->l('Jueves'),
            $this->l('Viernes'),
            $this->l('Sabado'),
            $this->l('Domingo'),
        );
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update.
     */
    public function install()
    {
        Logger::addLog('Urbaner: Install module');
        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');

            return false;
        }

        if (in_array($this->iso_code, $this->limited_countries) == false) {
            $this->_errors[] = $this->l('This module is not available in your country');
            // return false;
        }

        Configuration::updateValue('urbaner_test_mode', '1');

        $carrier = $this->addCarrier();
        $this->addZones($carrier);
        $this->addGroups($carrier);
        $this->addRanges($carrier);

        $this->installDb();
        $this->setFeatureProduct();

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionCarrierProcess') &&
            $this->registerHook('displayCarrierExtraContent') &&
            $this->registerHook('displayOrderConfirmation') &&
            $this->registerHook('displayAdminOrder') &&
            $this->registerHook('displayOrderDetail') &&
            $this->registerHook('updateCarrier');
    }

    public function installDb()
    {
        $sql_file = Tools::file_get_contents(dirname(__FILE__) . '/sql/installDb.sql');
        $sql_file = str_replace('{PREFIXE}', _DB_PREFIX_, $sql_file);
        $query = explode('-- REQUEST --', $sql_file);

        foreach ($query as $q) {
            if ($q != '') {
                if (Db::getInstance()->execute($q) === false) {
                    Logger::addLog(
                        '[' . $this->l('Urbaner') . '][' . time() . '] ' .
                            $this->l('installation :  An error occured on the query : ') . $q
                    );
                    include dirname(__FILE__) . '/sql/uninstall.php';

                    return false;
                }
            }
        }

        return true;
    }

    public function uninstall()
    {
        $this->deleteCarriers();

        // include dirname(__FILE__).'/sql/uninstall.php';
        $feature = new Feature(Configuration::get('vex_urbaner_id_feature'));
        $feature->delete();

        return parent::uninstall();
    }

    private function setFeatureProduct()
    {
        $values = array(
            '15', '30', '45', '60',
        );
        $name = $this->l('Tiempo de Prepación');
        $feature = new Feature();
        $feature->name = array_fill_keys(Language::getIDs(), (string) $name);
        $feature->position = Feature::getHigherPosition() + 1;
        $feature->add();

        foreach ($values as $value) {
            FeatureValue::addFeatureValueImport($feature->id, $value);
        }

        Configuration::updateValue('vex_urbaner_id_feature', $feature->id);
    }

    protected function deleteCarriers()
    {
        $tmp_carrier_id = Configuration::get('VEX_URBANER_CARRIER_ID');
        $carrier = new Carrier($tmp_carrier_id);

        $carrier->deleted = 1;
        try {
            $carrier->save();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getHookController($hook_name)
    {
        // Include the controller file
        require_once dirname(__FILE__) . '/controllers/hook/' . $hook_name . '.php';

        // Build dynamically the controller name
        $controller_name = $this->name . $hook_name . 'Controller';

        // Instantiate controller
        $controller = new $controller_name($this, __FILE__, $this->_path);

        // Return the controller
        return $controller;
    }

    /**
     * Load the configuration form.
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        $controller = $this->getHookController('getContent');

        return $controller->run($this->local_path);
    }

    public function getOrderShippingCostExternal($params)
    {
        if ($params == null) {
            return false;
        }

        return true;
    }

    public function getOrderShippingCost($params, $shipping_cost)
    {
        if ($params == null || $shipping_cost == null) {
            return false;
        }

        if (VexUrbanerRequest::checkCredentials() == false) {
            return false;
        }

        if (empty(Vex_Request_Sql::getHoraryActive())) {
            return false;
        }

        if (empty(Vex_Request_Sql::getStores())) {
            return false;
        }


        $amount = $this->context->cookie->priceUrbaner;

        $currency_ = new Currency($params->id_currency);
        if ($currency_->iso_code == 'USD') {
            $id_dolar = Currency::getIdByIsoCode('PEN', $params->id_shop_group);
            $currency_soles = Currency::getCurrencyInstance($id_dolar);
            $amount = $amount / $currency_soles->conversion_rate;
        }



        if (!empty($amount) && $amount > 0) {
            return $amount;
        }

        return $shipping_cost;
    }

    protected function addCarrier()
    {
        $carrier = new Carrier();
        $carrier->name = $this->l('Urbaner');
        $carrier->is_module = true;
        $carrier->deleted = 0;
        $carrier->active = 1;
        $carrier->range_behavior = 1;
        $carrier->need_range = 1;
        $carrier->shipping_external = true;
        $carrier->range_behavior = 0;
        $carrier->external_module_name = $this->name;
        $carrier->shipping_method = 2;

        // Configuration::updateValue('ID_REFERENCE', $carrier->copyCarrierData());
        foreach (Language::getLanguages() as $lang) {
            $carrier->delay[$lang['id_lang']] = $this->l('Envío Express');
        }

        if ($carrier->add() == true) {
            @copy(dirname(__FILE__) .
                '/views/img/logo.jpg', _PS_SHIP_IMG_DIR_ .
                '/' . (int) $carrier->id . '.jpg');
            Configuration::updateValue('VEX_URBANER_CARRIER_ID', (int) $carrier->id);

            return $carrier;
        }

        return false;
    }

    protected function addGroups($carrier)
    {
        $groups_ids = array();
        $groups = Group::getGroups(Context::getContext()->language->id);
        foreach ($groups as $group) {
            $groups_ids[] = $group['id_group'];
        }
        $carrier->setGroups($groups_ids);
    }

    protected function addRanges($carrier)
    {
        $range_price = new RangePrice();
        $range_price->id_carrier = $carrier->id;
        $range_price->delimiter1 = '0';
        $range_price->delimiter2 = '10000';
        $range_price->add();

        $range_weight = new RangeWeight();
        $range_weight->id_carrier = $carrier->id;
        $range_weight->delimiter1 = '0';
        $range_weight->delimiter2 = '10000';
        $range_weight->add();
    }

    protected function addZones($carrier)
    {
        $zones = Zone::getZones();

        foreach ($zones as $zon) {
            if ($zon['name'] == 'Urbaner-Zone') {
                $carrier->addZone($zon['id_zone']);

                return false;
            }
        }

        $zone = new Zone();
        $zone->name = 'Urbaner-Zone';
        $zone->save();

        $id_country = Country::getByIso($this->iso_code);

        $country = new Country($id_country);
        $country->id_zone = $zone->id;
        $country->save();
        $states = State::getStatesByIdCountry($country->id);

        if (is_array($states) && count($states) > 0) {
            foreach ($states as $state) {
                if ($state['name'] == 'Lima') {
                    $state_valid = new State($state['id_state']);
                    $state_valid->id_zone = $zone->id;
                    $state_valid->save();
                }
            }
        } else {
            $state_new = new State();
            $state_new->name = 'Lima';
            $state_new->id_country = $country->id;
            $state_new->id_zone = $zone->id;
            $state_new->iso_code = ' PE-LIM';
            $state_new->save();
        }
        $carrier->addZone($zone->id);
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');

        $this->context->controller->addJS($this->_path . '/views/js/front.js');
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        $this->context->controller->addJS($this->_path . 'views/js/back.js');
        $this->context->controller->addCSS($this->_path . 'views/css/back.css');
    }

    public function hookDisplayAdminOrder($params)
    {
        $controller = $this->getHookController('hookDisplayAdminOrder');

        return $controller->run($params);
    }

    public function hookDisplayOrderDetail($params)
    {
        $controller = $this->getHookController('hookDisplayOrderDetail');

        return $controller->run($params);
    }

    public function hookDisplayOrderConfirmation($params)
    {
        $controller = $this->getHookController('hookDisplayOrderConfirmation');

        return $controller->run($params);
    }

    public function hookDisplayCarrierExtraContent($params)
    {
        $controller = $this->getHookController('hookDisplayCarrierExtraContent');

        return $controller->run($params);
    }

    public function hookActionCarrierProcess($params)
    {

        $controller = $this->getHookController('hookActionCarrierProcess');

        return $controller->run($params);
    }

    public function hookUpdateCarrier($params)
    {
        /**
         * Not needed since 1.5
         * You can identify the carrier by the id_reference.
         */
        $id_carrier_old = (int) $params['id_carrier'];
        $id_carrier_new = (int) $params['carrier']->id;
        if ($id_carrier_old == (int) Configuration::get('VEX_URBANER_CARRIER_ID')) {
            Configuration::updateValue('VEX_URBANER_CARRIER_ID', $id_carrier_new);
        }
    }

    public function is_test_mode()
    {
        return '1' == Configuration::get('urbaner_test_mode', null, null, null, '0');
    }

    public function getUrl()
    {
        if ($this->is_test_mode()) {
            return self::API_CODE_TEST;
        }

        return self::API_CODE_LIVE;
    }
}
