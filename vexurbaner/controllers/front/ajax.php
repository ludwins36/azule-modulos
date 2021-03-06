
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
require_once dirname(__FILE__) . '/../../classes/class-request-urbaner.php';

class VexUrbanerAjaxModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->ajax = true;
    }

    public function postProcess()
    {
            $address    = Tools::getValue('address');
            $address2   = Tools::getValue('address2');
            $message    = Tools::getValue('message');
            $this->context->cookie->__set('urbanerAddress', $address);
            $this->context->cookie->__set('urbanerAddress2', $address2);
            $this->context->cookie->__set('messageUrbaner', $message);
            // echo json_encode($address);
            // echo json_encode($address2);
            echo $message;
            exit();
    }
}
