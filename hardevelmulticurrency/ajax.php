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

define('_PS_ADMIN_DIR_', getcwd().'/../../'); //for 1.5-1.6 admin cookie
include(dirname(__FILE__).'/../../config/config.inc.php');
if (_PS_VERSION_ < '1.5')
    include(dirname(__FILE__).'/../../init.php');
include_once(dirname(__FILE__).'/hardevelmulticurrency.php');

$hardevelmulticurrency = new hardevelmulticurrency();
echo Tools::jsonEncode($hardevelmulticurrency->ajax());