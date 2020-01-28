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
class VexUrbanerhookActionCarrierProcessController
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

        $pricesGeneral = 0;
        $dataOrders = array();

        $resource = new VexUrbanerRequest($this);
        // $resource->sendEMail($this->context->cart->id, 19);
        $id_feature = Configuration::get('vex_urbaner_id_feature');
        $dias_feriados = Vex_Request_Sql::getHolidays();

        $time_preparation = 0;
        $dimencions = 0;
        $peso = 0;

        $id = $this->context->cart->id;
        $cred = VexUrbanerRequest::checkCredentials();

        $ordes_products = [];


        if (!$coor = VexUrbanerRequest::coordinates($id)) {
            return false;
        }

        if (!$cred) {
            return false;
        }
        $horarys = VexUrbanerRequest::getHorarysFront();
        $day = $horarys['horarys'][1][0] . ' ' . $horarys['horarys'][1][2];

        foreach ($products as $value) {
            $product = new Product($value['id_product']);

            $features = $product->getFrontFeatures($this->context->language->id);
            $dimencions += $value['width'] * $value['height'] * $value['depth'];
            $peso += $value['width'];
            foreach ($features as $feature) {
                if ($feature['id_feature'] ==  $id_feature) {
                    if ($time_preparation < (int) $feature['value']) {
                        $time_preparation = (int) $feature['value'];
                    }
                }
            }
            $prod = Vex_Request_Sql::getProduct($value['id_product']);
            $val = $prod['id_ws_seller'];
            array_push($ordes_products, $val);
        }

        $lista_simple = array_values(array_unique($ordes_products));


        if (is_array($lista_simple) && count($lista_simple) > 0) {
            $priceU = 0;
            $type = 0;
            $time = 0;
            foreach ($lista_simple as $order) {
                $store = Vex_Request_Sql::getStoreWsId($order);
                // $storeWs = Vex_Request_Sql::getStoreWsSellerId($order);
                $data = array(
                    'destinations' => array(
                        array(
                            'latlon' => $store['lat'] . ' , ' . $store['lnt'],
                        ),
                        array(
                            'latlon' => $coor['lat'] . ' , ' . $coor['lnt'],
                        ),
                    ),
                    'vehicle_type_id' => '2',
                    'is_return' => 'false',
                );

                $url = $this->module->getUrl() . 'cli/price/';
                $result = VexUrbanerRequest::getPriceUrbaner($url, $data);


                if (empty($result->error)) {
                    $result = json_decode($result->response);

                    foreach ($result->prices as $price) {
                        if ($store['time'] > 1) {
                            if ($price->order_type == 'EXPRESS') {
                                $pricesGeneral += (float) $price->price;
                                $priceU = (float) $price->price;
                                $type = 1;
                                $time = strtotime($day) + 432000;

                                switch (date('N', $time)) {
                                    case 5:
                                        (int) $time += 259200;
                                        break;

                                    case 6:
                                        (int) $time += 172800;
                                        break;

                                    case 7:
                                        (int) $time += 86400;
                                        break;
                                }
                            }
                        } else {
                            switch (date('N', $time)) {
                                case '5':
                                    if ($price->order_type == 'EXPRESS') {
                                        $pricesGeneral += (float) $price->price;
                                        $priceU = (float) $price->price;
                                        $type = 1;
                                        $time = strtotime($day) + 259200;
                                    }
                                    break;
                                case '6':
                                    if ($price->order_type == 'EXPRESS') {
                                        $pricesGeneral += (float) $price->price;
                                        $priceU = (float) $price->price;
                                        $type = 1;
                                        $time = strtotime($day) + 172800;
                                    }
                                    break;
                                default:
                                    if ($price->order_type == 'NEXTDAY') {
                                        $pricesGeneral += (float) $price->price;
                                        $priceU = (float) $price->price;
                                        $type = 3;
                                        $time = 0;
                                    }
                                    break;
                            }
                            foreach ($dias_feriados as $hdias) {
                                if ($hdias['date'] == date('Y-m-d', $time)) {
                                    $time += 86400;
                                }
                            }
                        }
                    }

                    $dat = array(
                        'id_order' => $order,
                        'price' => $priceU,
                        'type' => $type,
                        'time' => $time,
                    );

                    array_push($dataOrders, $dat);
                } else {
                    // looger, enviar mail
                    array_push($dataOrders, $result->error_message);

                }
            }
        }

        $this->context->cookie->__set('priceUrbaner', $pricesGeneral);
        $this->context->cookie->__set('latLonUrbaner', json_encode($coor));
        $this->context->cookie->__set('dataRequesUrbanes', json_encode($dataOrders));
    }
}
