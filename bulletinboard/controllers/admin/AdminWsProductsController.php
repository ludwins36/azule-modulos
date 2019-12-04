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
*  @copyright 2007-2018 Snegurka WS
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminWsProductsController extends ModuleAdminController
{

    private $_name_controller = 'AdminWsProducts';
    
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'product';
        $this->className = 'Product';
        $this->lang = true;
        
        $this->context = Context::getContext();

        parent::__construct();
    }
    
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
    }
    
    public function renderList()
    {
        $this->_select = 'sp.id_ws_seller, b.name product_name, s.name shop_name, a.date_add, a.active';
        $this->_join = '
        '.Shop::addSqlAssociation('product', 'a').'
        LEFT JOIN '._DB_PREFIX_.'ws_seller_product sp ON (sp.id_product = a.id_product)
        LEFT JOIN '._DB_PREFIX_.'ws_seller s ON (s.id_ws_seller = sp.id_ws_seller)';
        $this->_where = 'AND sp.id_ws_seller != "" AND b.id_shop = '.(int)$this->context->shop->id;
         
        if (Tools::isSubmit('submitFilter')) {
            if (Tools::getValue('productFilter_id_product') != '') {
                $this->_where = ' AND a.id_product = '.(int)Tools::getValue('productFilter_id_product');
            }
        
            if (Tools::getValue('productFilter_name') != '') {
                $this->_where = ' AND a.name LIKE "%'.(string)Tools::getValue('productFilter_name').'%"';
            }
        
            if (Tools::getValue('productFilter_shop_name') != '') {
                $this->_where = ' AND s.name LIKE "%'.(string)Tools::getValue('productFilter_shop_name').'%"';
            }
        
            $arrayDateAdd = Tools::getValue('productFilter_date_add');
            if ($arrayDateAdd[0] != '' && $arrayDateAdd[1] != '') {
                $this->_where = ' AND a.date_add BETWEEN "'.$arrayDateAdd[0].'" AND "'.$arrayDateAdd[1].'"';
            }
        
            if (Tools::getValue('productFilter_active') != '') {
                $this->_where = ' AND a.active = '.(int)Tools::getValue('productFilter_active');
            }
        }
        
        if (Tools::getValue('productOrderway')) {
            $this->_orderBy = (string)Tools::getValue('productOrderby');
            $this->_orderWay = (string)Tools::getValue('productOrderway');
        }
        
        //$this->addRowAction('view');
        $this->addRowAction('edit');
        //$this->addRowAction('delete');
        
        $this->fields_list = array(
            'id_product' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'product_name' => array(
                'title' => $this->l('Product name'),
                'havingFilter' => true,
            ),
                'shop_name' => array(
                        'title' => $this->l('Shop'),
                        'havingFilter' => true,
                ),
            'date_add' => array(
                'title' => $this->l('Date add'),
                'type' => 'datetime',
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'class' => 'fixed-width-sm'
            )
        );
        $this->bulk_actions = array(
                'delete' => array(
                        'text' => $this->l('Delete selected'),
                        'confirm' => $this->l('Delete selected items?'),
                        'icon' => 'icon-trash'
                )
        );
            
        return parent::renderList();
    }
    
    public function postProcess()
    {
        if (Tools::isSubmit('updateproduct')) {
            $id_product = (int)Tools::getValue('id_product');
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts').'&id_product='.$id_product.'&updateproduct');
        }
    }
}
