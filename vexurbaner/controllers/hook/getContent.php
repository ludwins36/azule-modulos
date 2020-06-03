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
class VexUrbanergetContentController
{
    public function __construct($module, $file, $path)
    {
        require_once dirname($file).'/classes/class-license.php';
        require_once dirname($file).'/sql/request.php';
        $this->file = $file;
        $this->module = $module;
        $this->context = Context::getContext();
        $this->_path = $path;
    }

    private function getStatuses()
    {
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        $list_status = array();
        foreach ($statuses as $status) {
            $list_ = array(
                'id' => $status['id_order_state'],
                'name' => $status['name'],
            );
            array_push($list_status, $list_);
        }

        return $list_status;
    }

    /**
     * Set values for the inputs.
     */
    public function run($module)
    {
        $store = Vex_Request_Sql::getStoreWsName('aerobie');

        print_r($store);



        if (((bool) Tools::isSubmit('submitVex_urbanerModule')) == true) {
            $this->postProcess();
        }

        if ($id = Tools::getValue('submitOrderDeleteAction')) {
            Db::getInstance()->delete('vex_urbaner_orders', 'id_vex_urbaner = '.(string) $id);
        }

        $holidays = Vex_Request_Sql::getHolidays();
        // TODO crear paginador y filtro en el tpl
        $orders = Vex_Request_Sql::getOrders(true);

        $orders_pendientes = Vex_Request_Sql::getOrders(false);

        //status de las ordenes de prestashop
        $statuses = $this->getStatuses();

        $url = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->module->name
            .'&tab_module='.$this->module->tab
            .'&module_name='.$this->module->name
            .'&token='.Tools::getAdminTokenLite('AdminModules');

        $query = 'SELECT count(*) nb FROM `'._DB_PREFIX_.'vex_urbaner_stores`';

        $numStores = Db::getInstance()
            ->getValue($query);

        $type_prices = VexUrbanerRequest::getPriceType($this->module->getUrl().'client/payment_methods/');
        $price = Configuration::get('type_price');

        $stores = Vex_Request_Sql::getStores();

        $horarys = Vex_Request_Sql::getHorarys();
        $stat = array(
            'status' => !empty(Configuration::get('vex_urbaner_status')) ?
                Configuration::get('vex_urbaner_status') : $this->module->l('Pago aceptado'),
            'change' => !empty(Configuration::get('vex_urbaner_change')) ?
                Configuration::get('vex_urbaner_change') : 0,
            'newStatus' => !empty(Configuration::get('vex_urbaner_newStatus'))
                ? Configuration::get('vex_urbaner_newStatus') : $this->module->l('Enviado'),
        );

        $type = array(
            Configuration::get('TYPE_URBANER1'),
            Configuration::get('TYPE_URBANER2'),
        );

        $keys = array(
            Configuration::get(VexUrbaner::CONFIG_API_KEY),
            Configuration::get(VexUrbaner::CONFIG_KEY_GOOGLE_MAPS),
        );

        $this->context->smarty->assign(
            array(
                'type' => $type,
                'priceType' => $type_prices,
                'price_' => $price,
                'return' => Configuration::get('IS_RETURN'),
                'status' => $stat,
                'stores' => $stores,
                'orders' => $orders,
                'pendientes' => $orders_pendientes,
                'url' => $url,
                'holidays' => $holidays,
                'statuses' => $statuses,
                'dias' => $this->module->diasSemana,
                'horarys' => $horarys,
                'keys' => $keys,
                'test' => Configuration::get('urbaner_test_mode'),
                'numStores' => $numStores == 0 ? 1 : $numStores,
            )
        );

        $output = $this->context->smarty->fetch($module.'views/templates/admin/configure_panel.tpl');
        $licenseCtrl = new VexUrbanerLicense($this);
        $licenseCtrl->verify();

        $license = Configuration::get(VexUrbaner::CONFIG_LICENSE, null, null, null, '');
        $activated = Configuration::get('VEX_URBANER_ACTIVATED', null, null, null, '0');

        return  $output;

        return $this->renderForm();
        if (!empty($license) && $activated) {
        }
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitVex_urbanerModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($this->licenseFields());
    }

    private function licenseFields()
    {
        $license_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->module->l('License'),
                    'icon' => 'icon-user',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('License'),
                        'name' => VexUrbaner::CONFIG_LICENSE,
                        'class' => 'fixed-width-xxl',
                        'required' => true,
                    ),
                ),
                'submit' => array(
                    'title' => $this->module->l('Save'),
                ),
            ),
        );

        return array($license_form);
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $data = array(
            VexUrbaner::CONFIG_LICENSE => Configuration::get(VexUrbaner::CONFIG_LICENSE),
            'PE_CONFIG_TESTMODE' => Configuration::get('PE_CONFIG_TESTMODE'),
        );

        return $data;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        // TODO definir logica de price tpl

        if (Tools::getIsset(VexUrbaner::CONFIG_LICENSE)) {
            $licenseCtrl = new VexUrbanerLicense($this);
            $license = Tools::getValue(VexUrbaner::CONFIG_LICENSE);

            Configuration::updateValue(VexUrbaner::CONFIG_LICENSE, $license);

            if (empty($license)) {
                $this->context->controller->errors[] = $this->module->l('License code required!');
                $licenseCtrl->disabled();
            } else {
                $licenseCtrl->activate($license);
            }
        } else {
            $api_google = Tools::getValue(VexUrbaner::CONFIG_KEY_GOOGLE_MAPS);
            $api_urbaner = Tools::getValue(VexUrbaner::CONFIG_API_KEY);
            $status = Tools::getValue('urbaner_status');
            $changeStat = Tools::getValue('urbaner_change');
            $newstatus = Tools::getValue('urbaner_newStatus');
            $type = Tools::getValue('type_urbaner_1');
            $type2 = Tools::getValue('type_urbaner_2');
            $return = Tools::getValue('is_return');
            $type_price = Tools::getValue('type_price');
            Configuration::updateValue('urbaner_test_mode', Tools::getValue('PE_CONFIG_TESTMODE'));
            $keys = VexUrbanerRequest::checkCredentials();

            if ($keys == false) {
                $this->context->controller->errors[] =
                    $this->module->l('Debe ingresar las credenciales de Urbaner! ');
            }
            if (Tools::getValue('submitStoreAction') == 1) {
                $this->addStore(Tools::getValue('submitStoreId'));
            }
            if (Tools::isSubmit('submitStoreDeleteUrbaner') == true) {
                // TODO eliminar tienda
                $id = Tools::getValue('submitStoreDeleteUrbaner');
                Db::getInstance()->delete('vex_urbaner_stores', 'id = '.(int) $id);
            }

            if (Tools::getValue('submitVex_urbanerHorarys') == 1) {
                $listHorarys = Vex_Request_Sql::getHorarys();
                $c = 0;
                foreach ($this->module->diasSemana as $day) {
                    $start = Tools::getValue('start'.$day);
                    $end = Tools::getValue('end'.$day);
                    $data = array();
                    if (!empty(Tools::getValue('check'.$day))) {
                        $val = true;
                        if (empty($start)) {
                            $this->context->controller->errors[] =
                                $this->module->l('Debe colocar una fecha de inicio para el dia '.$day);
                            $val = false;
                        }
                        if (empty($end)) {
                            $this->context->controller->errors[] =
                                $this->module->l('Debe colocar una fecha de Cierre para el dia '.$day);
                            $val = false;
                        }
                        if ($start >= $end) {
                            $this->context->controller->errors[] =
                                $this->module->l('La fecha de inicio no debe ser mayor que la de cierre, en el dia '.$day);
                            $val = false;
                        }

                        if ($val) {
                            $data = array(
                                'start' => pSQL(trim($start)),
                                'end' => pSQL(trim($end)),
                                'status' => 1,
                                'day' => $day,
                            );
                        }
                    } else {
                        $data['status'] = 0;
                        $data['day'] = $day;
                    }

                    if (empty($listHorarys[$c])) {
                        $data['id'] = $c + 1;

                        Db::getInstance()
                            ->insert('vex_urbaner_horarys', $data);
                    } else {
                        Db::getInstance()
                            ->update('vex_urbaner_horarys', $data, "day = '$day'");
                    }
                    ++$c;
                }
            }
            if (Tools::isSubmit('submitVex_urbanerHolidays') == true) {
                $date = Tools::getValue('holiday_date');
                $concep = Tools::getValue('holiday_descrip');
                $del = Tools::getValue('submitDeleteHoliday');
                $query = 'SELECT count(*) nb FROM `'._DB_PREFIX_.'vex_urbaner_holidays` WHERE date = "$date"';
                $holiday = Db::getInstance()->getValue($query);

                if ($holiday == 0) {
                    if (!empty($date)) {
                        $data = array(
                            'date' => $date,
                            'concep' => $concep,
                        );
                        Db::getInstance()
                            ->insert('vex_urbaner_holidays', $data);
                    }
                    if ($del > 0) {
                        Db::getInstance()->delete('vex_urbaner_holidays', 'id = '.(int) $del);
                    }
                }
            }

            if (!empty($status)) {
                Configuration::updateValue('vex_urbaner_status', $status);
            }
            if (!empty($changeStat)) {
                Configuration::updateValue('vex_urbaner_change', $changeStat);
            }

            if (!empty($newstatus)) {
                Configuration::updateValue('vex_urbaner_newStatus', $newstatus);
            }

            if (!empty($api_google)) {
                Configuration::updateValue(VexUrbaner::CONFIG_KEY_GOOGLE_MAPS, $api_google);
            }

            if (!empty($api_urbaner)) {
                Configuration::updateValue(VexUrbaner::CONFIG_API_KEY, $api_urbaner);
            }

            if (!empty($type)) {
                Configuration::updateValue('TYPE_EXPREXT', $type);
            }

            if (!empty($type_price)) {
                Configuration::updateValue('type_price', $type_price);
            }

            if (!empty($type2)) {
                Configuration::updateValue('TYPE_NEXT', $type2);
            }

            Configuration::updateValue('IS_RETURN', $return);
            Configuration::updateValue('TYPE_URBANER1', $type);
            Configuration::updateValue('TYPE_URBANER2', $type2);
        }
    }

    private function addStore($id)
    {
        $id = Tools::getValue('submitStoreIdUrbaner');
        $address = Tools::getValue('address');
        $address2 = Tools::getValue('address2');
        $phone = Tools::getValue('phone');
        $lat = Tools::getValue('lat');
        $lnt = Tools::getValue('lnt');
        $zipCode = Tools::getValue('zipCode');
        $persone = Tools::getValue('persone');
        $nameStore = Tools::getValue('name');
        $time = Tools::getValue('time');
        $mail = Tools::getValue('mail');

        $store = Vex_Request_Sql::getStoreID($id);

        if (empty($nameStore)) {
            $this->context->controller->errors[] =
                $this->module->l('Debe colocar el nombre de la '.
                    (string) $id);
        } elseif (empty($address)) {
            $this->context->controller->errors[] =
                $this->module->l('Debe ingresar la dirección de la tienda '.
                    (string) $id);
        } elseif (empty($phone)) {
            $this->context->controller->errors[] =
                $this->module->l('Debe ingresar un número de telefono para la tienda '.
                    (string) $id);
        } elseif (empty($lat) || empty($lnt)) {
            $this->context->controller->errors[] =
                $this->module->l('Debe colocar las coordenadas de la tienda '.
                    (string) $id);
        } elseif (empty($zipCode)) {
            $this->context->controller->errors[] =
                $this->module->l('Debe ingresar el codigo Postal de la tienda '.
                    (string) $id);
        } elseif (empty($persone)) {
            $this->context->controller->errors[] =
                $this->module->l('Debe ingresar una persona de contacto para la tienda '.
                    (string) $id);
        } else {
            $storeWs = Vex_Request_Sql::getStoreWsName($nameStore);
            if ($storeWs == false) {
                $this->context->controller->errors[] =
                    $this->module->l('La tienda '.$nameStore.'no existe.');

                return;
            }
            $idWs = $storeWs['id_ws_seller'];

            $data = array(
                'id' => $id,
                'id_ws' => $idWs,
                'name_ws' => $nameStore,
                'zip_code' => $zipCode,
                'persone' => $persone,
                'address' => $address,
                'address_2' => $address2,
                'phone' => $phone,
                'mail' => $mail,
                'time' => $time,
                'lnt' => $lnt,
                'lat' => $lat,
            );

            if (!empty($store)) {
                $isStore = Vex_Request_Sql::getStoreWsId($idWs);
                if ($isStore) {
                    if ($store['name_ws'] != $nameStore) {
                        $this->context->controller->errors[] =
                        $this->module->l('Ya existe una tienda con el nombre '.$nameStore);

                        return false;
                    }
                }
                $query = 'SELECT count(*) nb FROM `'._DB_PREFIX_.'vex_urbaner_stores`';
                $count = Db::getInstance()->getValue($query);

                if ($count > $id) {
                    ++$data['id'];
                }
                $i = (int) $data['id'];
                Db::getInstance()
                    ->update('vex_urbaner_stores', $data, "id = $i");
                $this->context->controller->confirmations[] =
                    $this->module->l("Tienda $i actualizada correctamente");
            } else {
                $data['id'] = $id;
                Db::getInstance()
                    ->insert('vex_urbaner_stores', $data);
                $this->context->controller->confirmations[] =
                    $this->module->l('Tienda creada correctamente');
            }
        }
    }
}
