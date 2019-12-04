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

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(dirname(__FILE__).'/Ws_WishList.php');

class Advansedwishlist extends Module
{
    protected $config_form = false;
    private $_html = '';

    public function __construct()
    {
        $this->name = 'advansedwishlist';
        $this->tab = 'front_office_features';
        $this->version = '1.7.1';
        $this->author = 'Snegurka';
        $this->need_instance = 0;
        $this->module_key = 'fcdeb86309ac51aa0e914a05c472d2b9';

        $this->bootstrap = true;

        parent::__construct();
        
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $base_dir_ssl = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
        } else {
            $base_dir_ssl = _PS_BASE_URL_.__PS_BASE_URI__;
        }
                
        ## prestashop 1.7 ##
        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            $smarty = Context::getContext()->smarty;
            
            $smarty->assign(
                array(
                        $this->name.'is17' => 1,
                )
            );
        } else {
            $smarty = $this->context->smarty;

            $smarty->assign(
                array(
                        $this->name.'is17' => 0,
                )
            );
        }
        ## prestashop 1.7 ##
        $smarty->assign(
            array(
                        'base_dir' => $base_dir_ssl,
                        'wl_custom_font' => Configuration::get('ADVANSEDWISHLIST_ICONS_FONT'),
                        'advansedwishlist_ajax_controller_url' => $this->context->link->getModuleLink('advansedwishlist', 'ajax'),
            )
        );

        $this->displayName = $this->l('Advanced Wish List');
        $this->description = $this->l('display Advanced Wish List');
        $this->default_wishlist_name = $this->l('My wishlist');
        $this->controllers = array('mywishlist', 'view');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('ADVANSEDWISHLIST_NAV', true);
        Configuration::updateValue('ADVANSEDWISHLIST_TOP', true);
        Configuration::updateValue('ADVANSEDWISHLIST_TAX', true);
        Configuration::updateValue('ADVANSEDWISHLIST_LEFT', true);
        
        include(dirname(__FILE__).'/sql/install.php');
        
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $hookCartTpl = 'displayShoppingCart';
        } else {
            $hookCartTpl = 'displayShoppingCartFooter';
        }
        
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('leftColumn') &&
            $this->registerHook('productActions') &&
            $this->registerHook('displayProductWSExtra') &&
            $this->registerHook('displayCategoryWSExtra') &&
            $this->registerHook('displayCartExtraProductActions') &&
            $this->registerHook($hookCartTpl) &&
            $this->registerHook('cart') &&
            $this->registerHook('customerAccount') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('adminCustomers') &&
            $this->registerHook('displayProductListFunctionalButtons') &&
            //$this->registerHook('displayProductListReviews') &&
            $this->registerHook('displayProductButtons') &&
            $this->registerHook('top')  &&
            $this->registerHook('footer')  &&
            $this->registerHook('displayMyAccountBlock')  &&
            $this->registerHook('displayMyAccountBlockfooter')  &&
            $this->registerHook('displayNav1')  &&
            $this->registerHook('displayNav');
    }

    public function uninstall()
    {
        Configuration::deleteByName('ADVANSEDWISHLIST_NAV');

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $this->_html  = '';
        
        $mod_activ = Db::getInstance()->ExecuteS('SELECT id_module as id, active FROM ' . _DB_PREFIX_ . 'module WHERE name = "blockwishlist" AND active = 1');
        
        if ($mod_activ) {
            $sQuery = 'SELECT count(*) FROM ' . _DB_PREFIX_ . 'wishlist';
            $countBWl = Db::getInstance()->getValue($sQuery);
             
            $sQuery2 = 'SELECT count(*) FROM ' . _DB_PREFIX_ . 'ws_wishlist';
            $countWs_Wl = Db::getInstance()->getValue($sQuery2);
        
            if (!empty($countBWl) && empty($countWs_Wl)) {
                $this->context->smarty->assign(
                    array(
                                'sURI' => $this->local_path,
                                'sLoadingImg' => $this->local_path,
                                'countOld' => count($countBWl),
                    )
                );
                $this->_html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/data-import.tpl');
            }
        }
        if (Tools::isSubmit('wsOldModuleImportButton')) {
            $this->_html .= Ws_WishList::importData();
            $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminModules').'&configure='.$this->name);
        }
        
        if (Tools::getValue('id_customer')) {
            $this->_html .= '<script>init_tabs(20);</script>';
        }
        
        if (((bool)Tools::isSubmit('submitAdvansedwishlistModule')) == true) {
            $this->postProcess();
        } elseif ((Tools::isSubmit('viewadvansedwishlist') || Tools::isSubmit('viewws_wishlist_product')) && $id = Tools::getValue('id_product')) {
            Tools::redirect($this->context->link->getProductLink($id));
        } elseif (Tools::isSubmit('submitSettings')) {
            $activated = Tools::getValue('activated');
            if ($activated != 0 && $activated != 1) {
                $this->html .= '<div class="alert error alert-danger">'.$this->l('Activate module : Invalid choice.').'</div>';
            }
            $this->html .= $this->displayConfirmation($this->l('Settings updated'));
        }
        
        $this->_html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        
        if (Tools::getValue('id_customer') && Tools::getValue('id_wishlist')) {
            $renderList = $this->renderList((int)Tools::getValue('id_wishlist'));
        } else {
            $renderList = false;
        }
        
        $this->context->controller->addJs($this->_path.'/views/js/back.js');
        
        $this->context->smarty->assign(array(
                //'module_dir' => $this->_path,
                'configForm' => $this->renderConfigForm(),
                'customerList' => $this->renderCustomerList(),
                'renderList' => $renderList,
                'productList' => $this->renderProductList(),
                'module_name' => $this->name,
        ));
        
        $this->_html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/content_block.tpl');
        
        return $this->_html;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderConfigForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAdvansedwishlistModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($this->getConfigForm());
    }
    
    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderCustomerList()
    {
        $helper = new HelperForm();
    
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
    
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAdvansedwishlistModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
    
        $helper->tpl_vars = array(
                'fields_value' => $this->getCustomerListValues(), /* Add values for your inputs */
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id,
        );
    
        return $helper->generateForm($this->getCustomerList());
    }
    
    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderProductList()
    {
        $products = Ws_WishList::getTopProductsList();
        
        $fields_list = array(
                'id_product' => array(
                        'title' => $this->l('Product Id'),
                        'type' => 'text',
                ),
                'image' => array(
                    'title' => $this->l('Product image'),
                    'type' => 'image',
                ),
                'name' => array(
                        'title' => $this->l('Product Name'),
                        'width' => 140,
                        'type' => 'text',
                ),
                'qty_sum' => array(
                        'title' => $this->l('Quantity of WishLists'),
                        'width' => 140,
                        'type' => 'text',
                ),
        );
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->actions = array('view');
        $helper->show_toolbar = false;
        $helper->listTotal = count($products);
        
        $helper->module = $this;
        $helper->identifier = 'id_product';
        $helper->title = $this->l('Top products in customer wishlists');
        $helper->table = 'ws_wishlist_product';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        
        /* Paginate the result */
        $page = ($page = Tools::getValue('submitFilter'.$this->name)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue($this->name.'_pagination')) ? $pagination : 50;
        $products = $this->paginateProductsBO($products, $page, $pagination);
        
        return $helper->generateList($products, $fields_list);
    }
    
    public function paginateProductsBO($products, $page = 1, $pagination = 50)
    {
        if (count($products) > $pagination) {
            $products = array_slice($products, $pagination * ($page - 1), $pagination);
        }
    
        return $products;
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $fields_form = array();
        $fields_form[0]= array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('General Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                        array(
                                'type' => 'switch',
                                'label' => $this->l('Allow only one WishList'),
                                'name' => 'ADVANSEDWISHLIST_ONE',
                                'is_bool' => true,
                                'desc' => $this->l('If the option is enabled - your client will not be able to create multiple WishLists'),
                                'values' => array(
                                        array(
                                                'id' => 'one_on',
                                                'value' => true,
                                                'label' => $this->l('Enabled')
                                        ),
                                        array(
                                                'id' => 'one_off',
                                                'value' => false,
                                                'label' => $this->l('Disabled')
                                        )
                                ),
                        ),
                        array(
                                'type' => 'switch',
                                'label' => $this->l('Display Tax'),
                                'name' => 'ADVANSEDWISHLIST_TAX',
                                'is_bool' => true,
                                'desc' => $this->l('Display Price applied tax rate on wishlist page'),
                                'values' => array(
                                        array(
                                                'id' => 'tax_on',
                                                'value' => true,
                                                'label' => $this->l('Enabled')
                                        ),
                                        array(
                                                'id' => 'tax_off',
                                                'value' => false,
                                                'label' => $this->l('Disabled')
                                        )
                                ),
                        ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        
        $fields_form[1]= array(
                'form' => array(
                        'legend' => array(
                                'title' => $this->l('Icons and Buttons'),
                                'icon' => 'icon-cogs',
                        ),
                        'input' => array(
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Display in Nav'),
                                        'name' => 'ADVANSEDWISHLIST_NAV',
                                        'is_bool' => true,
                                        'desc' => $this->l('Display block wishlist in nav hook'),
                                        'values' => array(
                                                array(
                                                        'id' => 'nav_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'nav_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Display in Top'),
                                        'name' => 'ADVANSEDWISHLIST_TOP',
                                        'is_bool' => true,
                                        'desc' => $this->l('Display block wishlist in top hook'),
                                        'values' => array(
                                                array(
                                                        'id' => 'top_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'top_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Display the title in "Top and Nav"'),
                                        'name' => 'ADVANSEDWISHLIST_DISPLAY_TITLE',
                                        'is_bool' => true,
                                        'desc' => $this->l('Display the title "Advanced WishLists" near the icon'),
                                        'values' => array(
                                                array(
                                                        'id' => 'title_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'title_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Display the title in products button'),
                                        'name' => 'ADVANSEDWISHLIST_DISPLAY_TITLE2',
                                        'is_bool' => true,
                                        'desc' => $this->l('Display the title "Advanced WishLists" near the icon'),
                                        'values' => array(
                                                array(
                                                        'id' => 'title2_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'title2_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Button on top'),
                                        'name' => 'ADVANSEDWISHLIST_BTN_TOP',
                                        'is_bool' => true,
                                        'desc' => $this->l('Display the button on top of the product image (products list page)'),
                                        'values' => array(
                                                array(
                                                        'id' => 'btn_top_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'btn_top_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'color',
                                        'label' => $this->l('Color "Icon Heart"'),
                                        'desc' => $this->l('Choose a color for your Icon'),
                                        'name' => 'ADVANSEDWISHLIST_ICON_COLOR',
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Display in column'),
                                        'name' => 'ADVANSEDWISHLIST_LEFT',
                                        'is_bool' => true,
                                        'desc' => $this->l('Display block wishlist in the left or right column hook'),
                                        'values' => array(
                                                array(
                                                        'id' => 'left_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'left_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                        ),
                        'submit' => array(
                                'title' => $this->l('Save'),
                        ),
                ),
        );
        
        $icons_font = array();
        $icons_font[1] = array(
                'id_font' => '1',
                'name_font' => 'material icons',
        );
        
        $icons_font[2] = array(
                'id_font' => '2',
                'name_font' => 'font awesome',
        );
        $icons_font[3] = array(
                'id_font' => '3',
                'name_font' => 'custom font',
        );
        
        $fields_form[2]= array(
                'form' => array(
                        'legend' => array(
                                'title' => $this->l('Extended Options for the Developers'),
                                'icon' => 'icon-cogs',
                        ),
                        'description' => $this->l('These are additional options for template developers. Usually, you do not need to change it.'),
                        'input' => array(
                                array(
                                        'type' => 'select',
                                        'label' => $this->l('Icons font:'),
                                        'name' => 'ADVANSEDWISHLIST_ICONS_FONT',
                                        'desc' => $this->l('You can choose the font that your theme uses'),
                                        'options' => array(
                                                'default' => array('value' => 0, 'label' => $this->l('Choose font')),
                                                'query' => $icons_font,
                                                'id' => 'id_font',
                                                'name' => 'name_font'
                                        ),
                                ),
                        ),
                ),
        );
        return $fields_form;
    }
    
    /**
     * Create the structure of your form.
     */
    protected function getCustomerList()
    {
        $fields_form = array();
    
        $t_customers = Ws_WishList::getCustomers();
    
        $wl_customers = array();
        foreach ($t_customers as $c) {
            $wl_customers[$c['id_customer']]['id_customer'] = $c['id_customer'];
            $wl_customers[$c['id_customer']]['name'] = $c['firstname'].' '.$c['lastname'];
        }
         
        $fields_form[3]= array(
                'form' => array(
                        'legend' => array(
                                'title' => $this->l('Listing'),
                                'icon' => 'icon-cogs'
                        ),
                        'input' => array(
                                array(
                                        'type' => 'select',
                                        'label' => $this->l('Customers :'),
                                        'name' => 'id_customer',
                                        'options' => array(
                                                'default' => array('value' => 0, 'label' => $this->l('Choose customer')),
                                                'query' => $wl_customers,
                                                'id' => 'id_customer',
                                                'name' => 'name'
                                        ),
                                )
                        ),
                ),
        );
    
        if ($id_customer = Tools::getValue('id_customer')) {
            $wishlists = Ws_WishList::getByIdCustomer($id_customer);
            $fields_form[3]['form']['input'][] = array(
                    'type' => 'select',
                    'label' => $this->l('Wishlist :'),
                    'name' => 'id_wishlist',
                    'options' => array(
                            'default' => array('value' => 0, 'label' => $this->l('Choose wishlist')),
                            'query' => $wishlists,
                            'id' => 'id_wishlist',
                            'name' => 'name'
                    ),
            );
        }
    
        return $fields_form;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
                'ADVANSEDWISHLIST_LEFT' => Configuration::get('ADVANSEDWISHLIST_LEFT'),
                'ADVANSEDWISHLIST_NAV' => Configuration::get('ADVANSEDWISHLIST_NAV'),
                'ADVANSEDWISHLIST_TOP' => Configuration::get('ADVANSEDWISHLIST_TOP'),
                'ADVANSEDWISHLIST_DISPLAY_TITLE' => Configuration::get('ADVANSEDWISHLIST_DISPLAY_TITLE'),
                'ADVANSEDWISHLIST_DISPLAY_TITLE2' => Configuration::get('ADVANSEDWISHLIST_DISPLAY_TITLE2'),
                'ADVANSEDWISHLIST_BTN_TOP' => Configuration::get('ADVANSEDWISHLIST_BTN_TOP'),
                'ADVANSEDWISHLIST_ICON_COLOR' => Configuration::get('ADVANSEDWISHLIST_ICON_COLOR'),
                'ADVANSEDWISHLIST_ONE' => Configuration::get('ADVANSEDWISHLIST_ONE'),
            'ADVANSEDWISHLIST_TAX' => Configuration::get('ADVANSEDWISHLIST_TAX'),
            'ADVANSEDWISHLIST_ICONS_FONT' => Configuration::get('ADVANSEDWISHLIST_ICONS_FONT'),
        );
    }
    
    /**
     * Set values for the inputs.
     */
    protected function getCustomerListValues()
    {
        return array(
                'id_customer' => Tools::getValue('id_customer'),
                'id_wishlist' => Tools::getValue('id_wishlist'),
        );
    }
    

    public function renderList($id_wishlist)
    {
        $wishlist = new Ws_WishList($id_wishlist);
        $products = Ws_WishList::getProductByIdCustomer($id_wishlist, $wishlist->id_customer, $this->context->language->id);
    
        foreach ($products as $key => $val) {
            $image = Image::getCover($val['id_product']);
            
            //$image = Product::getCover((int)$id_product);
            if (version_compare(_PS_VERSION_, '1.7', '>')) {
                $f_small = ImageType::getFormattedName('small');
            } else {
                $f_small = ImageType::getFormatedName('small');
            }
            
            $products[$key]['image'] = $this->context->link->getImageLink($val['link_rewrite'], $image['id_image'], $f_small);
        }
    
        $fields_list = array(
                'image' => array(
                        'title' => $this->l('Image'),
                        'type' => 'image',
                ),
                'name' => array(
                        'title' => $this->l('Product'),
                        'type' => 'text',
                ),
                'attributes_small' => array(
                        'title' => $this->l('Combination'),
                        'type' => 'text',
                ),
                'quantity' => array(
                        'title' => $this->l('Quantity'),
                        'type' => 'text',
                ),
                'priority' => array(
                        'title' => $this->l('Priority'),
                        'type' => 'priority',
                        'values' => array($this->l('High'), $this->l('Medium'), $this->l('Low')),
                ),
        );
    
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->no_link = true;
        $helper->actions = array('view');
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->identifier = 'id_product';
        $helper->title = $this->l('Product list');
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->tpl_vars = array('priority' => array($this->l('High'), $this->l('Medium'), $this->l('Low')));
    
        return $helper->generateList($products, $fields_list);
    }
    
    /**
     * Save form data.
     */
    protected function postProcess()
    {
        if (!Tools::getValue('id_customer')) {
            Configuration::updateValue('ADVANSEDWISHLIST_LEFT', Tools::getValue('ADVANSEDWISHLIST_LEFT'));
            Configuration::updateValue('ADVANSEDWISHLIST_NAV', Tools::getValue('ADVANSEDWISHLIST_NAV'));
            Configuration::updateValue('ADVANSEDWISHLIST_TOP', Tools::getValue('ADVANSEDWISHLIST_TOP'));
            Configuration::updateValue('ADVANSEDWISHLIST_DISPLAY_TITLE', Tools::getValue('ADVANSEDWISHLIST_DISPLAY_TITLE'));
            Configuration::updateValue('ADVANSEDWISHLIST_DISPLAY_TITLE2', Tools::getValue('ADVANSEDWISHLIST_DISPLAY_TITLE2'));
            Configuration::updateValue('ADVANSEDWISHLIST_BTN_TOP', Tools::getValue('ADVANSEDWISHLIST_BTN_TOP'));
            Configuration::updateValue('ADVANSEDWISHLIST_ICON_COLOR', Tools::getValue('ADVANSEDWISHLIST_ICON_COLOR'));
            Configuration::updateValue('ADVANSEDWISHLIST_ONE', Tools::getValue('ADVANSEDWISHLIST_ONE'));
            Configuration::updateValue('ADVANSEDWISHLIST_TAX', Tools::getValue('ADVANSEDWISHLIST_TAX'));
            Configuration::updateValue('ADVANSEDWISHLIST_ICONS_FONT', Tools::getValue('ADVANSEDWISHLIST_ICONS_FONT'));
            $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
        } else {
            $this->_html .= $this->displayConfirmation($this->l('List'));
        }
    }
    
    public function hookDisplayNav($params)
    {
        if (Configuration::get('ADVANSEDWISHLIST_NAV')) {
            $data_template = $this->wishlistBildShortContent();
            $this->context->smarty->assign($data_template);
            return $this->display(__FILE__, 'blockwishlist_top.tpl');
        }
    }
    
    public function hookDisplayNav1($params)
    {
        if (Configuration::get('ADVANSEDWISHLIST_NAV')) {
            $data_template = $this->wishlistBildShortContent();
            $this->context->smarty->assign($data_template);
            return $this->display(__FILE__, 'blockwishlist_top.tpl');
        }
    }
    
    public function hookDisplayNav2($params)
    {
        if (Configuration::get('ADVANSEDWISHLIST_NAV')) {
            $data_template = $this->wishlistBildShortContent();
            $this->context->smarty->assign($data_template);
            return $this->display(__FILE__, 'blockwishlist_top.tpl');
        }
    }
    
    public function hookDisplayTopColumn($params)
    {
        if (Configuration::get('ADVANSEDWISHLIST_TOP')) {
            $data_template = $this->wishlistBildShortContent();
            $this->context->smarty->assign($data_template);
            return $this->display(__FILE__, 'blockwishlist_top.tpl');
        }
    }
        
    public function hookTop($params)
    {
        if (Configuration::get('ADVANSEDWISHLIST_TOP')) {
            $data_template = $this->wishlistBildShortContent();
            $this->context->smarty->assign($data_template);
            $this->context->smarty->assign(array('hook_name' => 'top'));
            
            return $this->display(__FILE__, 'blockwishlist_top.tpl');
        }
            /*
        if ($this->context->customer->isLogged()) {
            $wishlists = Ws_Wishlist::getByIdCustomer($this->context->customer->id);
            if (empty($this->context->cookie->id_wishlist) === true ||
            Ws_WishList::exists($this->context->cookie->id_wishlist, $this->context->customer->id) === false)
            {
                if (!sizeof($wishlists))
                {
        
                    $wishlist = new Ws_WishList();
                    $wishlist->id_shop = 1;
                    $wishlist->id_shop_group = 1;
                    $wishlist->counter = 1;
                    $wishlist->name = 'my wishlist';
        
                    $wishlist->id_customer = (int)($this->context->customer->id);
                    list($us, $s) = explode(' ', microtime());
                    srand($s * $us);
                    $wishlist->token = strtoupper(substr(sha1(uniqid(rand(), true)._COOKIE_KEY_.$this->context->customer->id), 0, 16));
                    $wishlist->add();
                    $this->context->cookie->id_wishlist = (int)($wishlist->id);
        
                    $id_wishlist = (int)($wishlist->id);
                }
                else
                {
                    $id_wishlist = (int)($wishlists[0]['id_wishlist']);
                    $this->context->cookie->id_wishlist = (int)($id_wishlist);
                }
            }
            else
                $id_wishlist = $this->context->cookie->id_wishlist;
        
            $products_count = Ws_WishList::getInfosByIdCustomer($this->context->customer->id);

            
            
            $this->smarty->assign(
                    array(
                            'id_wishlist' => $id_wishlist,
                            'isLogged' => true,
                            'wishlist_products' => ($id_wishlist == false ? false : Ws_WishList::getProductByIdCustomer($id_wishlist,
                                    $this->context->customer->id, $this->context->language->id, null, true)),
                            'wishlists' => $wishlists,
                            'products_count' => $products_count[0]["nbProducts"],
                            'ptoken' => Tools::getToken(false)
                    )
            );
        }
        else
            $this->smarty->assign(array('wishlist_products' => false, 'wishlists' => false));
        
        return $this->display(__FILE__, 'blockwishlist_top.tpl');
        
        */
    }
    
    public function wishlistBildShortContent()
    {
        $products_count = 0;
        $wishlists = false;
        $id_wishlist = 0;
         
        if ($this->context->customer->isLogged()) {
            $wishlists = Ws_Wishlist::getByIdCustomer($this->context->customer->id);
        
            if (empty($this->context->cookie->id_wishlist) === true ||
            Ws_WishList::exists($this->context->cookie->id_wishlist, $this->context->customer->id) === false) {
                if (!count($wishlists)) {
                    $id_wishlist = 0;
                } else {
                    foreach ($wishlists as $t_wishlist) {
                        if (isset($t_wishlist['default']) && $t_wishlist['default'] == 1) {
                            $id_wishlist = (int)$t_wishlist['id_wishlist'];
                            $this->context->cookie->id_wishlist = (int)$id_wishlist;
                        }
                    }
                }
            } else {
                $id_wishlist = $this->context->cookie->id_wishlist;
            }

            if ($t_products_count = Ws_Wishlist::getInfosByIdCustomer($this->context->customer->id, true)) {
                $products_count = $t_products_count[0]["nbProducts"];
            }
        }
        
        $data_template = array(
                        'logged' => $this->context->customer->isLogged(),
                        'icon_color' => Configuration::get('ADVANSEDWISHLIST_ICON_COLOR'),
                        'products_count' => (int)$products_count,
                        'wishlists' => $wishlists,
                        'id_wishlist' => $id_wishlist,
                        'show_text' => Configuration::get('ADVANSEDWISHLIST_DISPLAY_TITLE'),
                        'show_btn_text' => Configuration::get('ADVANSEDWISHLIST_DISPLAY_TITLE2'),
                        'hook_name' => 'nav',
                        'static_token' => Tools::getToken(false),
                        'single_mode' => Configuration::get('ADVANSEDWISHLIST_ONE'),
                        'advansedwishlist_ajax_controller_url' => $this->context->link->getModuleLink('advansedwishlist', 'ajax'),
                
        );
        return $data_template;
    }
    
    public function hookRightColumn($params)
    {
        if (Configuration::get('ADVANSEDWISHLIST_LEFT')) {
            $data_template = $this->wishlistBildFullContent();
            $this->context->smarty->assign($data_template);

            return ($this->display(__FILE__, 'blockwishlist.tpl'));
        }
    }

    public function hookLeftColumn($params)
    {
        return $this->hookRightColumn($params);
    }
    
    public function wishlistBildFullContent()
    {
        $wishlists = false;
        $id_wishlist = 0;
         
        if ($this->context->customer->isLogged()) {
            $wishlists = Ws_Wishlist::getByIdCustomer($this->context->customer->id);
    
            if (empty($this->context->cookie->id_wishlist) === true ||
            Ws_WishList::exists($this->context->cookie->id_wishlist, $this->context->customer->id) === false) {
                if (!count($wishlists)) {
                    $id_wishlist = 0;
                } else {
                    foreach ($wishlists as $t_wishlist) {
                        if (isset($t_wishlist['default']) && $t_wishlist['default'] == 1) {
                            $id_wishlist = (int)$t_wishlist['id_wishlist'];
                            $this->context->cookie->id_wishlist = (int)$id_wishlist;
                        }
                    }
                }
            } else {
                $id_wishlist = $this->context->cookie->id_wishlist;
            }
            
            $products = Ws_WishList::getProductByIdCustomer($id_wishlist, $this->context->customer->id, $this->context->language->id, null, true);
            
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
            }
        }
        
        $data_template = array(
                'logged' => $this->context->customer->isLogged(),
                'wishlists' => $wishlists,
                'module_link' => $this->context->link->getModuleLink('advansedwishlist', 'mywishlist'),
                'id_wishlist' => $id_wishlist,
                //'wishlist_products' => ($id_wishlist == false ? false : Ws_WishList::getLightProductByIdCustomer($id_wishlist, $this->context->customer->id, $this->context->language->id, null, true)),
                'wishlist_products' => ($id_wishlist == false ? false : $products),
                'advansedwishlist_ajax_controller_url' => $this->context->link->getModuleLink('advansedwishlist', 'ajax'),
        );
        return $data_template;
    }
    
    public function hookFooter($params)
    {
        if (!isset($this->context->controller->php_self) || $this->context->controller->php_self == 'product') {
            return;
        }
        if ($this->context->customer->isLogged()) {
            $wishlists = Ws_Wishlist::getByIdCustomer($this->context->customer->id);
            if (empty($this->context->cookie->id_wishlist) === true ||
            Ws_WishList::exists($this->context->cookie->id_wishlist, $this->context->customer->id) === false) {
                if (!count($wishlists)) {
                    $id_wishlist = 0;
                } else {
                    foreach ($wishlists as $t_wishlist) {
                        if (isset($t_wishlist['default']) && $t_wishlist['default'] == 1) {
                            $id_wishlist = (int)$t_wishlist['id_wishlist'];
                            $this->context->cookie->id_wishlist = (int)$id_wishlist;
                        }
                    }
                }
            } else {
                $id_wishlist = $this->context->cookie->id_wishlist;
            }
        } else {
            $wishlists = array();
            $id_wishlist = 0;
        }
        
        $this->smarty->assign(
            array(
                        'logged' => $this->context->customer->isLogged(),
                        'wishlists' => $wishlists,
                        'static_token' => Tools::getToken(false),
                        'single_mode' => Configuration::get('ADVANSEDWISHLIST_ONE'),
                        'id_wishlist' => $id_wishlist,
                        'advansedwishlist_ajax_controller_url' => $this->context->link->getModuleLink('advansedwishlist', 'ajax'),
            )
        );
        return $this->display(__FILE__, 'blockwishlist_footer.tpl');
    }
    
    public function hookProductActions($params)
    {
        $data_template = $this->wishlistBildExtra($params);
        $this->context->smarty->assign($data_template);
        
        return $this->display(__FILE__, 'blockwishlist-extra.tpl');
    }
    
    public function hookDisplayProductWSExtra($params)
    {
        return $this->hookProductActions($params);
    }
    
    public function hookDisplayCategoryWSExtra($params)
    {
        $data_template = $this->wishlistBildButton($params);
        $this->context->smarty->assign($data_template);
        return $this->display(__FILE__, 'blockwishlist_button.tpl');
    }
    
    public function hookDisplayProductButtons($params)
    {
        $data_template = $this->wishlistBildExtra($params);
        $this->context->smarty->assign($data_template);
        
        return $this->display(__FILE__, 'blockwishlist-extra.tpl');
    }
    
    public function wishlistBildExtra($params)
    {
        $wishlists = false;
        $groups = Tools::getValue('group');
        $id_product = (int)Tools::getValue('id_product');
        if (!empty($groups)) {
            $idProductAttribute = (int) Product::getIdProductAttributeByIdAttributes(
                $id_product,
                $groups,
                true
            );
        } else {
            $idProductAttribute = (int)Tools::getValue('id_product_attribute');
        }
        
        if ($this->context->customer->isLogged()) {
            $wishlists = Ws_Wishlist::getByIdCustomer($this->context->customer->id);
        
            if (!count($wishlists)) {
                $this->smarty->assign(array(
                        'issetProduct' => false,
                        'id_wishlist' => 0,
                ));
            } elseif (count($wishlists) > 1) {
                $this->smarty->assign(array(
                        'issetProduct' => false,
                ));
            } else {
                $this->smarty->assign(array(
                        'issetProduct' => Ws_WishList::issetProduct($wishlists[0]['id_wishlist'], (int)$id_product, (int)$idProductAttribute),
                        'id_wishlist' => $wishlists[0]['id_wishlist'],
                ));
            }
        }
        
         
        $data_template = array(
                'logged' => $this->context->customer->isLogged(),
                'show_btn_text' => false,
                'show_btn_top' => true,
                'id_product' => (int)$id_product,
                'id_product_attribute' => (int)$idProductAttribute,
                'single_mode' => Configuration::get('ADVANSEDWISHLIST_ONE'),
                //'login_link' => $this->context->link->getPageLink('my-account', true),
                'wishlists' => $wishlists,
    
        );
        return $data_template;
    }
    
    public function hookDisplayProductListFunctionalButtons($params)
    {
        $data_template = $this->wishlistBildButton($params);
        $this->context->smarty->assign($data_template);
        return $this->display(__FILE__, 'blockwishlist_button.tpl');
    }
    
    public function hookDisplayLeoWishlistButton($params)
    {
    	$data_template = $this->wishlistBildButton($params);
    	$this->context->smarty->assign($data_template);
    	return $this->display(__FILE__, 'blockwishlist_button.tpl');
    }
    
    public function hookDisplayProductListReviews($params)
    {
        $data_template = $this->wishlistBildButton($params);
        $this->context->smarty->assign($data_template);
        return $this->display(__FILE__, 'blockwishlist_button.tpl');
    }
    
    public function wishlistBildButton($params)
    {
        $wishlists = false;

        if ($this->context->customer->isLogged()) {
            $wishlists = Ws_Wishlist::getByIdCustomer($this->context->customer->id);
            
            if (!count($wishlists)) {
                $this->smarty->assign(array(
                        'issetProduct' => false,
                        'id_wishlist' => 0,
                ));
            } elseif (count($wishlists) > 1) {
                $this->smarty->assign(array(
                        'issetProduct' => false,
                ));
            } else {
                $this->smarty->assign(array(
                        'issetProduct' => Ws_WishList::issetProduct($wishlists[0]['id_wishlist'], (int)($params['product']['id_product']), (int)Tools::getValue('id_product_attribute')),
                        'id_wishlist' => $wishlists[0]['id_wishlist'],
                ));
            }
        }
    
        $data_template = array(
                'logged' => $this->context->customer->isLogged(),
                'ws_product' => $params['product'],
                'show_text' => Configuration::get('ADVANSEDWISHLIST_DISPLAY_TITLE2'),
                'show_btn_text' => Configuration::get('ADVANSEDWISHLIST_DISPLAY_TITLE2'),
                'show_btn_top' => Configuration::get('ADVANSEDWISHLIST_BTN_TOP'),
                'login_link' => $this->context->link->getPageLink('my-account', true),
                'wishlists' => $wishlists,
        );
        return $data_template;
    }
    
    public function hookCustomerAccount($params)
    {
        $this->context->smarty->assign(array(
                'single_mode' => Configuration::get('ADVANSEDWISHLIST_ONE'),
        ));
        return $this->display(__FILE__, 'my-account.tpl');
    }
    
    public function hookDisplayMyAccountBlock($params)
    {
        $this->context->smarty->assign(array(
                'single_mode' => Configuration::get('ADVANSEDWISHLIST_ONE'),
        ));
        return $this->display(__FILE__, 'my-account-block.tpl');
    }
    
    public function hookDisplayMyAccountBlockfooter($params)
    {
        $this->context->smarty->assign(array(
                'single_mode' => Configuration::get('ADVANSEDWISHLIST_ONE'),
        ));
        return $this->display(__FILE__, 'my-account-block.tpl');
    }
    
    public function hookDisplayShoppingCartFooter($params)
    {
        $data_template = $this->wishlistBildFullContent();
        $this->context->smarty->assign($data_template);
        
        return ($this->display(__FILE__, 'wishlist_cart.tpl'));
    }
    
    public function hookDisplayShoppingCart($params)
    {
        $data_template = $this->wishlistBildFullContent();
        $this->context->smarty->assign($data_template);
         
        return ($this->display(__FILE__, 'wishlist_cart.tpl'));
    }
    
    public function hookDisplayCartExtraProductActions($params)
    {
        $product = $params['product'];
        $id_customer = 0;
        $id_address_delivery = 0;
        
        if ($this->context->customer->isLogged()) {
            $id_customer = $this->context->customer->id;

            $id_address_delivery = $params['cart']->id_address_delivery;
             
            if (empty($id_address_delivery)) {
                $id_address_delivery = 0;
            }
        }
        
        $this->context->smarty->assign(array(
                'product' => $product,
                'logged' => $this->context->customer->isLogged(),
                'id_customer' => $id_customer,
                'id_address_delivery' => $id_address_delivery,
        ));
        return ($this->display(__FILE__, 'ws_wishlist_cart_extra.tpl'));
    }
    
    public function hookAdminCustomers($params)
    {
        $products_count = 0;
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        
        $this->context->controller->addjqueryPlugin('fancybox');
        
        if ($this->context->customer->isLogged()) {
            $wishlists = Ws_Wishlist::getByIdCustomer($this->context->customer->id);
            if (empty($this->context->cookie->id_wishlist) === true ||
            Ws_WishList::exists($this->context->cookie->id_wishlist, $this->context->customer->id) === false) {
                if (!count($wishlists)) {
                    $id_wishlist = 0;
                } else {
                    foreach ($wishlists as $t_wishlist) {
                        if (isset($t_wishlist['default']) && $t_wishlist['default'] == 1) {
                            $id_wishlist = (int)$t_wishlist['id_wishlist'];
                            $this->context->cookie->id_wishlist = (int)$id_wishlist;
                        }
                    }
                }
            } else {
                $id_wishlist = $this->context->cookie->id_wishlist;
            }
        } else {
            $wishlists = false;
            $id_wishlist = 0;
        }
        
        $this->smarty->assign(
            array(
                        'logged' => $this->context->customer->isLogged(),
                        'wishlists' => $wishlists,
                        'static_token' => Tools::getToken(false),
                        'single_mode' => Configuration::get('ADVANSEDWISHLIST_ONE'),
                        'id_wishlist' => $id_wishlist,
                        'advansedwishlist_ajax_controller_url' => $this->context->link->getModuleLink('advansedwishlist', 'ajax'),
            )
        );
        
        return $this->display(__FILE__, 'views/templates/hook/header.tpl');
    }
    
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            //$this->context->controller->addJs($this->_path.'/views/js/back.js');
        }
    }
    
    /**
     * Support for customers template
     */
    public function hookDisplayUserinfo()
    {
        if (Configuration::get('ADVANSEDWISHLIST_TOP')) {
            $data_template = $this->wishlistBildShortContent();
            $this->context->smarty->assign($data_template);
            $this->context->smarty->assign(array('hook_name' => 'top'));
             
            return $this->display(__FILE__, 'blockwishlist_top.tpl');
        }
    }
}
