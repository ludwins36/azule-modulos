<?php
/**
* 2007-2015 PrestaShop
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
*  @author    Snegurka <site@web-esse.ru>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!class_exists('PaymentBoard')) {
    require_once(_PS_MODULE_DIR_.'bulletinboard/classes/PaymentBoard.php');
}

if (!class_exists('WsSeller')) {
    require_once(_PS_MODULE_DIR_.'bulletinboard/classes/WsSeller.php');
}

class WsSellerOrders extends ObjectModel
{
    public $id_ws_order;
    public $id_order;
    public $id_ws_seller;
    
    public $id_product;
    public $id_product_attribute;

    
    public static $definition = array(
            'table' => 'ws_seller_order',
            'primary' => 'id_ws_order',
            'fields' => array(
                    'id_order' => array('type' => self::TYPE_INT, 'required' => true),
                    'id_ws_seller' => array('type' => self::TYPE_INT, 'required' => true),
                    'id_product' => array('type' => self::TYPE_INT, 'required' => true),
                    'id_product_attribute' => array('type' => self::TYPE_INT, 'required' => true),
            ),
    );
    
    public static function getOrders($id_seller)
    {
        return Db::getInstance()->executeS(
            'SELECT so.*, o.*, CONCAT(c.`firstname`, \' \', c.`lastname`) AS customer, ostl.`name` as status, pl.`name` as pr_name
            FROM `'._DB_PREFIX_.'ws_seller_order` so
            INNER JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = so.`id_order`)
            INNER JOIN `'._DB_PREFIX_.'customer` c ON (o.`id_customer` = c.`id_customer`)
            INNER JOIN `'._DB_PREFIX_.'order_state_lang` ostl ON (ostl.`id_order_state` = o.`current_state` AND ostl.`id_lang` = '.(int)Context::getContext()->language->id.') 
            INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = so.`id_product` AND pl.`id_lang` = '.(int)Context::getContext()->language->id.')
            WHERE so.`id_ws_seller` = '.(int)$id_seller
        );
    }
    
    public static function issetOrder($id_order, $id_ws_seller)
    {
        $result = Db::getInstance()->getRow('SELECT wso.`id_order`
        FROM '._DB_PREFIX_.'ws_seller_order wso
        WHERE wso.`id_order` = '.(int)$id_order.($id_ws_seller ? ' AND wso.`id_ws_seller` = '.(int)$id_ws_seller : ''));
        return (is_array($result) && count($result) ? true : false);
    }
    
    public static function getOrderDetail($id_order)
    {
        return Db::getInstance()->getRow(
            'SELECT o.`reference`, a.*, CONCAT(c.`firstname`, \' \', c.`lastname`) AS customer, c.`email` 
            FROM `'._DB_PREFIX_.'orders` o
            INNER JOIN `'._DB_PREFIX_.'customer` c ON (o.`id_customer` = c.`id_customer`) 
            INNER JOIN `'._DB_PREFIX_.'address` a ON (a.`id_address` = o.`id_address_delivery`)
            WHERE o.`id_order` = '.(int)$id_order
        );
    }
    
    public static function getProductsByOrderId($id_order)
    {
        return Db::getInstance()->executeS(
            'SELECT so.*, s.*, pl.`name` as pr_name, CONCAT(c.`firstname`, \' \', c.`lastname`) AS customer, c.`email` 
            FROM `'._DB_PREFIX_.'ws_seller_order` so
            INNER JOIN `'._DB_PREFIX_.'ws_seller` s ON (s.`id_ws_seller` = so.`id_ws_seller`) 
            INNER JOIN `'._DB_PREFIX_.'customer` c ON (s.`id_customer` = c.`id_customer`) 
            INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = so.`id_product` AND pl.`id_lang` = '.(int)Context::getContext()->language->id.')
            WHERE so.`id_order` = '.(int)$id_order
        );
    }
}
