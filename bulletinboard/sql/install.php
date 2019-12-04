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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ws_seller` (
		`id_ws_seller` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`id_customer` int(4) NOT NULL,
		`name` varchar(64) NOT NULL,
		`payment_acc` varchar(128) DEFAULT NULL,
		`date_add` datetime NOT NULL,
		`date_upd` datetime NOT NULL,
		`active` tinyint(1) NOT NULL DEFAULT "0",
		PRIMARY KEY (`id_ws_seller`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ws_seller_lang` (
`id_ws_seller` int(10) unsigned NOT NULL,
`id_lang` int(10) unsigned NOT NULL,
`description` text,
`short_description` text,
`meta_title` varchar(128) DEFAULT NULL,
`meta_keywords` varchar(255) DEFAULT NULL,
`meta_description` varchar(255) DEFAULT NULL,
		 PRIMARY KEY (`id_ws_seller`,`id_lang`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ws_seller_product` (
  `id_ws_seller` int(4) unsigned NOT NULL,
  `id_product` int(4) unsigned NOT NULL,
  PRIMARY KEY (`id_ws_seller`,`id_product`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ws_seller_payment` (
			`id_payment` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_product` INT( 10 ) UNSIGNED NULL ,
			`id_order` INT( 10 ) UNSIGNED NULL ,
			`summ` DECIMAL( 20, 6 ) NOT NULL DEFAULT "0",
			`description` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`status` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT "0",
			`id_ws_seller` INT( 10 ) UNSIGNED NOT NULL ,
			`date_add` DATETIME NOT NULL ,
			`date_upd` DATETIME NOT NULL ,
			PRIMARY KEY ( `id_payment` )
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ws_seller_order` (
			`id_ws_order` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_order` INT( 10 ) unsigned NOT NULL,
			`id_ws_seller`  int(4) unsigned NOT NULL,
			`id_product`  int(4) unsigned NOT NULL,
			`id_product_attribute`  int(4) unsigned NOT NULL,
			PRIMARY KEY ( `id_ws_order` )
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
