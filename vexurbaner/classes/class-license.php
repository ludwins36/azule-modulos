<?php

/**
 * 2007-2019 PrestaShop
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

require_once 'class-curl.php';

class VexUrbanerLicense
{

    const LICENSE_SECRET_KEY = '587423b988e403.69821411';
    const LICENSE_SERVER_URL = 'https://www.pasarelasdepagos.com';
    const ITEM_REFERENCE = 'Urbaner - Prestashop Carrier';


    protected $module = null;


    protected $context;

    public function __construct($module)
    {
        $this->module = $module;
        $this->context = Context::getContext();
    }


    public function verify()
    {
        $license = Configuration::get('VEX_URBANER_LICENSE', null, null, null, '');

        if (!empty($license)) {
            Tools::refreshCACertFile();
            $data = array(
                'slm_action'  => 'slm_check',
                'secret_key'  => self::LICENSE_SECRET_KEY,
                'license_key' => $license,
            );
            $curl = new VexUrbanerCurl();
            $curl->setOpt(CURLOPT_CAINFO, _PS_CACHE_CA_CERT_FILE_);
            $curl->get(self::LICENSE_SERVER_URL, $data);
            if ($curl->isSuccess()) {
                $license_data = @json_decode($curl->response);

                if ($license_data !== null && json_last_error() === JSON_ERROR_NONE) {
                    if ($license_data->result == 'success') {
                        if ($license_data->max_allowed_domains > count($license_data->registered_domains)) {
                            // validated license
                            return true;
                        } else {
                            foreach ($license_data->registered_domains as $item) {
                                if ($item->registered_domain == $_SERVER['SERVER_NAME']) {
                                    return true;
                                }
                            }
                            $this->context->controller->errors[] = $this->module->l('Urbaner: Reached
                             maximum allowable domains.');
                        }
                    } else {
                        $this->context->controller->errors[] = $license_data->message;
                    }
                } else {
                    $this->context->controller->errors[] = $this->module->l('Urbaner: Communication error.');
                }
            } else {
                $this->context->controller->errors[] = $this->module->l('Urbaner: Unexpected
                 Error! The query returned with an error.');
            }
        }
        $this->disabled();
        return false;
    }

    public function disabled()
    {
        Configuration::updateValue('VEX_SOLUCIONES_ACTIVATED', '0');
        Configuration::updateValue('VEX_SOLUCIONES_LAST_DATE', '');
    }

    public function activate($license)
    {
        Tools::refreshCACertFile();

        $data = array(
            'slm_action'        => 'slm_activate',
            'secret_key'        => self::LICENSE_SECRET_KEY,
            'license_key'       => $license,
            'registered_domain' => $_SERVER['SERVER_NAME'],
            'item_reference'    => urlencode(self::ITEM_REFERENCE),
        );

        $curl = new VexUrbanerCurl();
        $curl->setOpt(CURLOPT_CAINFO, _PS_CACHE_CA_CERT_FILE_);
        $curl->get(self::LICENSE_SERVER_URL, $data);

        if ($curl->isSuccess()) {
            $license_data = @json_decode($curl->response);
            if ($license_data !== null && json_last_error() === JSON_ERROR_NONE) {
                if ($license_data->result == 'success') {
                    Configuration::updateValue('VEX_URBANER_ACTIVATED', '1');
                    Configuration::updateValue('VEX_URBANER_LAST_DATE', time());

                    $this->context->controller->confirmations[] = $license_data->message;
                    return;
                } else {
                    $this->context->controller->errors[] = $license_data->message;
                }
            } else {
                $this->context->controller->errors[] = $this->module->l('Urbaner: Communication error.');
            }
        } else {
            $this->context->controller->errors[] = $this->module->l('Urbaner: Unexpected
             Error! The query returned with an error.');
        }

        $this->disabled();
    }
}
