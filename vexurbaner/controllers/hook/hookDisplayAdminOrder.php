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
class VexUrbanerhookDisplayAdminOrderController
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
        $order = new Order($params['id_order']);
        $cart_id = $this->context->cart->id;
        $order_urbaner = Vex_Request_Sql::getOrder((int) $cart_id);
        $order_State = $order->getCurrentOrderState();
        $status = Configuration::get(VexUrbaner::CONFIG_STATUS);
        if (!empty($order_urbaner)) {
            foreach ($order_urbaner as $order) {
                if ($status == $order_State->id) {
                    if ($order['response'] == 0) {
                        $request = new VexUrbanerRequest($this);
                        $request->createOrder($cart_id, $order['id_wsl']);
                    }
                }
            }
        }
    }
}
