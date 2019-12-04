<?php
/**
 * 2007-2017 PrestaShop
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
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!class_exists('Ws_WishList')) {
    require_once(_PS_MODULE_DIR_.'advansedwishlist/Ws_WishList.php');
}

class AdvansedWishlistAjaxModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $this->module_instance = new advansedwishlist();
        $this->context = Context::getContext();
        $this->ajax = true;
    }
    
    public function initContent()
    {
        parent::initContent();
        
        $action = Tools::getValue('action');
        $id_wishlist = (int)Tools::getValue('id_wishlist');
        
        if ($this->context->customer->isLogged()) {
            if ($id_wishlist && Ws_WishList::exists($id_wishlist, $this->context->customer->id) === true) {
                $this->context->cookie->id_wishlist = (int)$id_wishlist;
            }
            if ((int)$this->context->cookie->id_wishlist > 0 && !Ws_WishList::exists($this->context->cookie->id_wishlist, $this->context->customer->id)) {
                $this->context->cookie->id_wishlist = '';
            }
            
            
            if (empty($this->context->cookie->id_wishlist) === true || $this->context->cookie->id_wishlist == false) {
                $this->context->smarty->assign('error', true);
            }
            
            if (!empty($action) && method_exists($this, 'action' . Tools::ucfirst(Tools::toCamelCase($action)))) {
                return $this->{'action' . Tools::toCamelCase($action)}($id_wishlist);
            }
        }
    }

    protected function actionAdd($id_wishlist = false)
    {
        $id_product = (int)Tools::getValue('id_product');
        $quantity = (int)Tools::getValue('quantity');
        $id_product_attribute = (int)Tools::getValue('id_product_attribute');
        $currency = (int)$this->context->currency->id;
         
        if (!isset($this->context->cookie->id_wishlist) || $this->context->cookie->id_wishlist == '') {
            $wishlist = new Ws_WishList();
            $wishlist->id_shop = $this->context->shop->id;
            $wishlist->id_shop_group = $this->context->shop->id_shop_group;
            $wishlist->default = 1;
        
            $mod_wishlist = new Advansedwishlist();
            $wishlist->name = $mod_wishlist->default_wishlist_name;
            $wishlist->id_customer = (int)$this->context->customer->id;
            list($us, $s) = explode(' ', microtime());
            srand($s * $us);
            $wishlist->token = Tools::strtoupper(Tools::substr(sha1(uniqid(rand(), true)._COOKIE_KEY_.$this->context->customer->id), 0, 16));
            $wishlist->add();
            $this->context->cookie->id_wishlist = (int)$wishlist->id;
        }
         
        Ws_WishList::addProduct($this->context->cookie->id_wishlist, $this->context->customer->id, $id_product, $id_product_attribute, $quantity);
        $product = new Product($id_product, true, (int)$this->context->language->id);
        $product->ws_link = $this->context->link->getProductLink($id_product);
        $product->ws_cart_link = $this->context->link->getPageLink('cart', true, null, 'add=1&id_product='.$id_product);
        $product->ws_price = Product::convertPriceWithCurrency(array('price' => $product->price, 'currency' => $currency), $this->context->smarty);
        
        $images = $product->getImages($this->context->language->id);
        foreach ($images as $k => $image) {
            if ($image['cover']) {
                $tt_img = $product->id.'-'.$image['id_image'];
                break;
            }
        }
        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            $f_small = ImageType::getFormattedName('small');
        } else {
            $f_small = ImageType::getFormatedName('small');
        }
        
        $product->ws_img_link = $this->context->link->getImageLink($product->link_rewrite, $tt_img, $f_small);
        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            $product->ws_version = 'advansedwishlistis17';
        } else {
            $product->ws_version = 'advansedwishlistis16';
        }
        $this->ajaxDie(Tools::jsonEncode($product));
    }
    
    protected function actionAddProductFromCart()
    {
        $id_product = (int)Tools::getValue('id_product');
        $id_product_attribute = (int)Tools::getValue('id_product_attribute');
        $id_customer = (int)Tools::getValue('id_customer');

        if (Ws_WishList::addProduct($this->context->cookie->id_wishlist, $id_customer, $id_product, $id_product_attribute, 1)) {
            echo "success";
        }
    }
    
    protected function actionDelete($id_wishlist)
    {
        $id_product = (int)Tools::getValue('id_product');
        $quantity = (int)Tools::getValue('quantity');
        $id_product_attribute = (int)Tools::getValue('id_product_attribute');

        Ws_WishList::removeProduct($this->context->cookie->id_wishlist, $this->context->customer->id, $id_product, $id_product_attribute);
    }
    
    protected function actionSendWL($id_wishlist = false)
    {
        if (empty($id_wishlist) === true) {
            die('Invalid wishlist');
        }
         
        // for ($i = 1; Tools::getValue('email'.$i) != ''; ++$i) {
        //     $to = Tools::getValue('email'.$i);
        //     $wishlist = Ws_WishList::exists($id_wishlist, $this->context->customer->id, true);
        //     if ($wishlist === false) {
        //         exit($this->module_instance->l('Invalid wishlist', 'sendwishlist'));
        //     }
        //     if (Ws_WishList::addEmail($id_wishlist, $to) === false) {
        //         exit($this->module_instance->l('Wishlist send error', 'sendwishlist'));
        //     }
        //     $toName = Configuration::get('PS_SHOP_NAME');
        //     $customer = $this->context->customer;
        //     if (Validate::isLoadedObject($customer)) {
        //         Mail::Send(
        //             $this->context->language->id,
        //             'wishlist',
        //             sprintf(Mail::l('Message from %1$s %2$s', $this->context->language->id), $customer->lastname, $customer->firstname),
        //             array(
        //             '{lastname}' => $customer->lastname,
        //             '{firstname}' => $customer->firstname,
        //             '{wishlist}' => $wishlist['name'],
        //             '{message}' => $this->context->link->getModuleLink('advansedwishlist', 'view', array('token' => $wishlist['token']))
        //             ),
        //             $to,
        //             $toName,
        //             $customer->email,
        //             $customer->firstname.' '.$customer->lastname,
        //             null,
        //             null,
        //             _PS_MODULE_DIR_.'advansedwishlist/mails/'
        //         );
        //     }
        // }
    }
    
    protected function actionUpdateQtyFromCart()
    {
        $id_wishlist = (int)Tools::getValue('id_wishlist');
        $id_product = (int)Tools::getValue('id_product');
        $id_product_attribute = (int)Tools::getValue('id_product_attribute');
        $quantity = (int)Tools::getValue('quantity');
        
        Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'ws_wishlist_product` SET
        `quantity` = '.(int)($quantity).'
        WHERE `id_wishlist` = '.(int)($id_wishlist).'
        AND `id_product` = '.(int)($id_product).'
        AND `id_product_attribute` = '.(int)($id_product_attribute)
        );
    }
}
