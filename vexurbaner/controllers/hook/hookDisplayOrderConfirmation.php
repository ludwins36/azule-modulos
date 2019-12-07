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
class VexUrbanerhookDisplayOrderConfirmationController
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
        $data = $this->context->cookie->dataRequesUrbanes;
        $data = json_decode($data);

        $order = $params['order'];
        $cart_id = $order->id_cart;
        $dataR = VexUrbanerRequest::getAddress($cart_id);
        $carrier_id = $order->id_carrier;
        $id_carrier_old = Configuration::get('VEX_URBANER_CARRIER_ID');
        $oldOrder = Vex_Request_Sql::getOrder($cart_id);
        $coor = json_decode($this->context->cookie->latLonUrbaner);

        if ($id_carrier_old == $carrier_id) {
            // if (empty($oldOrder)) {
            foreach ($data as $dat) {
                $date = 0;
                if ($dat->time > 0) {
                    $date = date('Y-m-d H:s', $dat->time);
                }
                $order_State = $order->getCurrentOrderState();
                $status = Configuration::get(VexUrbaner::CONFIG_STATUS);
                $today = date('Y/m/d H:i');
                $address = $dataR['address'];
                $query = array(
                    'id_vex_urbaner' => (int) $cart_id,
                    'id_wsl' => $dat->id_order,
                    'type' => $dat->type,
                    'id_traking' => 0,
                    'referency' => $order->reference,
                    'date_creation' => $today,
                    'response' => 0,
                    'urlTracking' => '',
                    'status' => $this->module->l('Pendiente'),
                    'date' => $date,
                    'price' => $dat->price,
                    'address' => $address,
                    'lanLot' => $coor->lat . ' , ' . $coor->lnt,
                    'vehicle_id' => '2',
                );
                Db::getInstance()
                    ->insert('vex_urbaner_orders', $query);
                // if ($status == $order_State->id) {
                $resource = new VexUrbanerRequest($this);
                $r = $resource->createOrder($cart_id, $dat->id_order);
                print_r(var_dump($r));
                print_r('prueba');
                // }
            }
            // }
        }
    }
}
