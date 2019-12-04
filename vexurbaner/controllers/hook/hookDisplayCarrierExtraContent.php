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


class VexUrbanerhookDisplayCarrierExtraContentController
{
    public function __construct($module, $file, $path)
    {
        require_once dirname($file) . '/classes/class-request-urbaner.php';
        require_once dirname($file) . '/sql/request.php';

        $this->file = $file;
        $this->module = $module;
        $this->context = Context::getContext();
        $this->_path = $path;
    }


    /**
     * Set values for the inputs.
     */

    public function run($params)
    {
        $cart = new Cart($this->context->cart->id);
        $products = $cart->getProducts();


        $time_preparation = 0;
        $id_feature = Configuration::get('vex_urbaner_id_feature');
        $dimencions = 0;
        $peso = 0;
        $is_v = '';
        $store = Vex_Request_Sql::getStoreID(1);
        $id = $this->context->cart->id;
        $coor = VexUrbanerRequest::coordinates($id);
        $cred = VexUrbanerRequest::checkCredentials();

        if (!$coor) {
            return false;
        }

        if (!$cred) {
            return false;
        }

        foreach ($products as $value) {
            $product = new Product($value['id_product']);
            $features = $product->getFrontFeatures($this->context->language->id);
            $dimencions += $value['width'] *  $value['height'] * $value['depth'];
            $peso += $value['width'];
            foreach ($features as $feature) {
                if ($feature['id_feature'] == $id_feature) {
                    if ($time_preparation < (int) $feature['value']) {
                        $time_preparation = (int) $feature['value'];
                    }
                }
            }
        }

        if ($dimencions < VexUrbaner::DIMENTIONS_1 && $peso < VexUrbaner::PESO_1) {
            $is_v = 1;
        } else if ($dimencions < VexUrbaner::DIMENTIONS_2 && $peso < VexUrbaner::PESO_2) {
            $is_v = 2;
        } else {
            $is_v = 3;
        }

        $horarys = VexUrbanerRequest::getHorarysFront($time_preparation);

        if ($horarys) {
            $apiMpas = Configuration::get(VexUrbaner::CONFIG_KEY_GOOGLE_MAPS);
            if (empty($apiMpas)) {
                return;
            }

            $this->context->smarty->assign(
                array(
                    'latTienda'    => $store['lat'],
                    'lonTienda'    => $store['lnt'],
                    'latCliente'   => $coor['lat'],
                    'lonCliente'   => $coor['lnt'],
                    'cred'         => $cred,
                    'id'           => $params['carrier']['id'],
                    'idVh'         => $is_v,
                    'googleKey'    => $apiMpas,
                    'semana'       => Json_encode($horarys['horarys']),
                    'horarys'      => $horarys['horarys'],
                    'today'        => $horarys['today'],
                    'time_p'       => $time_preparation,
                    'return'       => 'false',
                    'image' => ' ../modules/' . $this->module->name . '/views/img/store.jpg'
                )
            );
            return $this->module->display($this->file, 'views/templates/hook/displayCarrierExtraContent.tpl');
        }
    }
}
