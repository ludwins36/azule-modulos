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
class VexUrbanerhookDisplayOrderDetailController
{
    public function __construct($module, $file, $path)
    {
        require_once dirname($file).'/classes/class-request-urbaner.php';
        require_once dirname($file).'/sql/request.php';

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
        $order = $params['order'];
        // TODO programar status de la orden de urbaner
        $carrier = new Carrier($order->id_carrier);

        if ($carrier->external_module_name == $this->module->name) {
            $my_orders = Vex_Request_Sql::getOrder($order->id_cart);

            $this->context->smarty->assign(
                        array(
                            'orders' => $my_orders,
                        )
                    );

            return $this->module->display($this->file, 'views/templates/front/traking.tpl');
        }
    }
}
