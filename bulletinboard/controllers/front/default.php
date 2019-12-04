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
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

// Include Module
require_once(_PS_MODULE_DIR_.'bulletinboard/bulletinboard.php');
// Include Models
if (!class_exists('Board')) {
    require_once(_PS_MODULE_DIR_.'bulletinboard/classes/Board.php');
}

if (!class_exists('WsSeller')) {
    require_once(_PS_MODULE_DIR_.'bulletinboard/classes/WsSeller.php');
}

use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class BulletinboardDefaultModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();

        $this->context = Context::getContext();
    }
    
    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'product_list.css');
    }
    

    public function initContent()
    {
        parent::initContent();

        if (Tools::isSubmit('action')) {
        // TODO mail
        } else {
            $seller_id = (int)Tools::getValue('id_seller');
            $seller = new WsSeller($seller_id, $this->context->language->id);
            
            $this_link = $this->context->link->getModuleLink('bulletinboard', 'default', array('id_seller' => $seller_id));
            $p = (int)Tools::getValue('p');
            $pagin_on = false;
            $pages = false;
            $step = 6;
            
            $start = (int)(($p - 1)*$step);
            if ($start < 0) {
                $start = 0;
            }
            
            $count_products = Board::getCountProducts($seller_id);
            
            if ($count_products > $step) {
                $pagin_on = true;
                $pages = Board::pagingProducts($start, $step, $count_products, $this_link);
            }
            
            if (version_compare(_PS_VERSION_, '1.7', '>')) {
                $newProducts = Board::getAllProductsBySeller($seller_id, $start, $step);
                
                $context = new ProductSearchContext($this->context);
                $query = new ProductSearchQuery();
                
                $query
                ->setResultsPerPage($step)
                ->setPage($p)
                ;
                $query->setSortOrder(new SortOrder('product', 'position', 'asc'));
                
                $assembler = new ProductAssembler($this->context);
                $presenterFactory = new ProductPresenterFactory($this->context);
                $presentationSettings = $presenterFactory->getPresentationSettings();
                $presenter = new ProductListingPresenter(
                    new ImageRetriever(
                        $this->context->link
                    ),
                    $this->context->link,
                    new PriceFormatter(),
                    new ProductColorsRetriever(),
                    $this->context->getTranslator()
                );
                
                $products_for_template = array();
                
                if (is_array($newProducts)) {
                    foreach ($newProducts as $rawProduct) {
                        $products_for_template[] = $presenter->present(
                            $presentationSettings,
                            $assembler->assembleProduct($rawProduct),
                            $this->context->language
                        );
                    }
                }
            } else {
                $products_for_template = Board::getAllProductsBySeller($seller_id, $start, $step);
            }
            
            $this->context->smarty->assign(array(
                    'seller' => $seller,
                    'products' => $products_for_template,
                    'pagin_on' => $pagin_on,
                    'pages'    => $pages,
                    'this_link' => $this_link,
            ));
            if (version_compare(_PS_VERSION_, '1.7', '>')) {
                $this->setTemplate('module:'.$this->module->name.'/views/templates/front/display_seller17.tpl');
            } else {
                $this->setTemplate('display_seller.tpl');
            }
        }
    }
}
