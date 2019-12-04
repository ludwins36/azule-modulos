<?php
/**
 * HarDevel LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://hardevel.com/License.txt
 *
 * @category  HarDevel
 * @package   HarDevel_multicurrency
 * @author    HarDevel
 * @copyright Copyright (c) 2012 - 2015 HarDevel LLC. (http://hardevel.com)
 * @license   http://hardevel.com/License.txt
 */

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_0_14()
{
    return Db::getInstance()->execute('
        ALTER TABLE `'._DB_PREFIX_.'hardevel_multicurrency` ADD `reduction` FLOAT NOT NULL AFTER `id_wholesale_currency`, ADD `reduction_type`  varchar(10) NOT NULL AFTER `reduction`, ADD `id_reduction_currency` int(11) NOT NULL AFTER `reduction_type`
    ');
}
