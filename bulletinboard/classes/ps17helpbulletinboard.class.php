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

class Ps17helpBulletinBoard
{
    private $_name = 'bulletinboard';

    public function setMissedVariables()
    {
        $smarty = Context::getContext()->smarty;

        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }

        if ($custom_ssl_var == 1) {
            $base_dir_ssl = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
        } else {
            $base_dir_ssl = _PS_BASE_URL_.__PS_BASE_URI__;
        }

        $smarty->assign('base_dir_ssl', $base_dir_ssl);

        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            $smarty->assign($this->_name.'is17', 1);
        }
    }
}
