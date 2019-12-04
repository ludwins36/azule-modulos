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
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

// Include Module
include_once(dirname(__FILE__).'/../../advansedwishlist.php');
// Include Models
include_once(dirname(__FILE__).'/../../Ws_WishList.php');

class AdvansedWishlistDefaultModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();

        $this->context = Context::getContext();
    }

    public function init()
    {
        if (!$this->context->customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication');
        }
        $this->display_header = false;
        $this->display_footer = false;
        parent::init();
    }
    
    public function initContent()
    {
        parent::initContent();
        
        $id_wishlist = (int)Tools::getValue('id_wishlist');
        $id_product = (int)Tools::getValue('id_product');
        $id_product_attribute = (int)Tools::getValue('id_product_attribute');
        $quantity = (int)Tools::getValue('quantity');
        $priority = Tools::getValue('priority');
        $wishlist = new Ws_WishList((int)($id_wishlist));
        $refresh = ((Tools::getValue('refresh') == 'true') ? 1 : 0);
        
        
        if (empty($id_wishlist) === false) {
            switch (Tools::getValue('action')) {
                case 'update':
                    Ws_WishList::updateProduct($id_wishlist, $id_product, $id_product_attribute, $priority, $quantity);
                    break;
                case 'delete':
                    Ws_WishList::removeProduct($id_wishlist, (int)$this->context->customer->id, $id_product, $id_product_attribute);
                    break;
            }

            $products = Ws_WishList::getProductByIdCustomer($id_wishlist, $this->context->customer->id, $this->context->language->id);
            $bought = Ws_WishList::getBoughtProduct($id_wishlist);
            
           
            for ($i = 0; $i < sizeof($products); ++$i) {
                $obj = new Product((int)($products[$i]['id_product']), false, $this->context->language->id);
                if (!Validate::isLoadedObject($obj)) {
                    continue;
                } else {
                    if ($products[$i]['id_product_attribute'] != 0) {
                        $combination_imgs = $obj->getCombinationImages($this->context->language->id);
                        if (isset($combination_imgs[$products[$i]['id_product_attribute']][0])) {
                            $products[$i]['cover'] = $obj->id.'-'.$combination_imgs[$products[$i]['id_product_attribute']][0]['id_image'];
                        } else {
                            $cover = Product::getCover($obj->id);
                            $products[$i]['cover'] = $obj->id.'-'.$cover['id_image'];
                        }
                    } else {
                        $images = $obj->getImages($this->context->language->id);
                        foreach ($images as $k => $image) {
                            if ($image['cover']) {
                                $products[$i]['cover'] = $obj->id.'-'.$image['id_image'];
                                break;
                            }
                        }
                    }
                    if (!isset($products[$i]['cover'])) {
                        $products[$i]['cover'] = $this->context->language->iso_code.'-default';
                    }
                }
                $products[$i]['bought'] = array();
                for ($j = 0, $k = 0; $j < sizeof($bought); ++$j) {
                    if ($bought[$j]['id_product'] == $products[$i]['id_product'] and
                        $bought[$j]['id_product_attribute'] == $products[$i]['id_product_attribute']) {
                        $products[$i]['bought'][$k++] = $bought[$j];
                    }
                }
            }

            $productBoughts = array();
            
            foreach ($products as $product) {
                if (sizeof($product['bought'])) {
                    $productBoughts[] = $product;
                }
            }
            
                $this->context->smarty->assign(array(
                        'products' => $products,
                        'productsBoughts' => $productBoughts,
                        'id_wishlist' => $id_wishlist,
                        'refresh' => $refresh,
                        'token_wish' => $wishlist->token,
                        'wishlists' => Ws_WishList::getByIdCustomer($this->context->cookie->id_customer)
                ));
            if (version_compare(_PS_VERSION_, '1.7', '>')) {
                 $this->setTemplate('module:advansedwishlist/views/templates/front/managewishlist.tpl');
            } else {
                $this->setTemplate('managewishlist.tpl');
            }
        }
    }
}
