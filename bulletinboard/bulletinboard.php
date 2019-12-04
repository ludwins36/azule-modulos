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

/*
 * WsSeller - ws_seller + Seller function
 * WsSellerOrder - Seller Orders
 * Board - ws_seller_product + product function
 * 
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!class_exists('Board')) {
    require_once(_PS_MODULE_DIR_.'bulletinboard/classes/Board.php');
}

if (!class_exists('WsSeller')) {
    require_once(_PS_MODULE_DIR_.'bulletinboard/classes/WsSeller.php');
}

class Bulletinboard extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'bulletinboard';
        $this->tab = 'advertising_marketing';
        $this->version = '1.5.1';
        $this->author = 'Snegurka';
        $this->need_instance = 0;
        $this->module_key = 'be7643a7ce48cf11957ef3514e486d07';
        
        $this->confirmUninstall = $this->l('Are you sure? All module data will be lost after uninstalling the module');

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;
        $this->dir_mails = dirname(__FILE__).'/mails/';

        parent::__construct();
        
        ## prestashop 1.7 ##
        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            require_once(_PS_MODULE_DIR_.$this->name.'/classes/ps17helpbulletinboard.class.php');
            $ps17help = new Ps17helpBulletinBoard();
            $ps17help->setMissedVariables();
        } else {
            $smarty = $this->context->smarty;
            $smarty->assign($this->name.'is17', 0);
        }
        ## prestashop 1.7 ##

        $this->displayName = $this->l('Simple marketplace');
        $this->description = $this->l('Simple marketplace for clients');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('BULLETINBOARD_ORDER_MODE', true);
        Configuration::updateValue('BULLETINBOARD_CATEGORY', '3,4,8');
        Configuration::updateValue('BULLETINBOARD_GROUP', serialize(array(3)));
        Configuration::updateValue('BULLETINBOARD_CMS', '2');
        Configuration::updateValue('BULLETINBOARD_TAXES', true);
        Configuration::updateValue('BULLETINBOARD_COMMIS', '15');
        Configuration::updateValue('BULLETINBOARD_SHOW_INFO', true);
        Configuration::updateValue('BULLETINBOARD_ORDERS_ON', true);
        
        Configuration::updateValue('BULLETINBOARD_EMAIL_S_ACTIVE', true);
        Configuration::updateValue('BULLETINBOARD_EMAIL_P_ACTIVE', true);
        
        Configuration::updateValue('BULLETINBOARD_F_DESCRIPTION', true);
        Configuration::updateValue('BULLETINBOARD_F_CATEGORY', true);
        Configuration::updateValue('BULLETINBOARD_F_CONDITION', true);
        Configuration::updateValue('BULLETINBOARD_F_OPTIONS', true);
        Configuration::updateValue('BULLETINBOARD_F_QUANTITY', true);
        Configuration::updateValue('BULLETINBOARD_F_REFERENCE', true);
        Configuration::updateValue('BULLETINBOARD_F_MANUFACTURER', true);
        

        include(dirname(__FILE__).'/sql/install.php');
        $this->createAdminTabs();

        return parent::install() &&
            $this->registerHook('displayCustomerAccount') &&
            $this->registerHook('header') &&
            $this->registerHook('NewOrder') &&
            $this->registerHook('UpdateOrderStatus') &&
            $this->registerHook('displayRightColumnProduct') &&
            $this->registerHook('productActions') &&
            $this->registerHook('displayProductListFunctionalButtons') &&
            $this->registerHook('actionProductDelete') &&
            $this->registerHook('actionProductSave') &&
            $this->registerHook('actionObjectDeleteBefore') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('displayAdminOrder') &&
            $this->registerHook('displayMyAccountBlock')  &&
            $this->registerHook('displayMyAccountBlockfooter')  &&
            $this->registerHook('registerGDPRConsent') &&
            $this->registerHook('actionDeleteGDPRCustomer') &&
            $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('BULLETINBOARD_ORDER_MODE');
        Configuration::deleteByName('BULLETINBOARD_CATEGORY');

        include(dirname(__FILE__).'/sql/uninstall.php');
        $this->uninstallTab();

        return parent::uninstall();
    }
    
    public function createAdminTabs()
    {
        $langs = Language::getLanguages();
    
        $tab0 = new Tab();
        $tab0->class_name = "AdminWsMarketplace";
        $tab0->module = $this->name;
        $tab0->id_parent = 0;
        foreach ($langs as $l) {
            $tab0->name[$l['id_lang']] = $this->l('Marketplace');
        }
        $tab0->save();
        $main_tab_id = $tab0->id;
    
        unset($tab0);
    
        $tab1 = new Tab();
        $tab1->class_name = "AdminWsSellers";
        $tab1->module = $this->name;
        $tab1->id_parent = $main_tab_id;
        foreach ($langs as $l) {
            $tab1->name[$l['id_lang']] = $this->l('Seller Statistic');
        }
        $tab1->save();
    
        unset($tab1);
        
        $tab1 = new Tab();
        $tab1->class_name = "AdminWsProducts";
        $tab1->module = $this->name;
        $tab1->id_parent = $main_tab_id;
        foreach ($langs as $l) {
            $tab1->name[$l['id_lang']] = $this->l('Seller Products');
        }
        $tab1->save();
        
        unset($tab1);
    }
    
    public function uninstallTab()
    {
        $prefix = '';
    
        $tab_id = Tab::getIdFromClassName("AdminWsMarketplace".$prefix);
        if ($tab_id) {
            $tab = new Tab($tab_id);
            $tab->delete();
        }
    
        $tab_id = Tab::getIdFromClassName("AdminWsSellers".$prefix);
        if ($tab_id) {
            $tab = new Tab($tab_id);
            $tab->delete();
        }
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $this->_html = '';
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('updatews_seller')) == true) {
            $this->_html .= $this->renderSellerEditForm((int)Tools::getValue('id_ws_seller'));
        } elseif (((bool)Tools::isSubmit('updatews_seller_payment')) == true) {
            $this->_html .= $this->renderPaymentEditForm((int)Tools::getValue('id_payment'));
        } else {
            $this->postProcess();
            $this->_html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
            $this->_html .= '<div class="row">';
            $this->_html .= '<div class="tab-content col-lg-12 col-md-9">';
            $this->_html .= '<div class="tab-pane active" id="configSellerForm">';
            $this->_html .=  $this->renderSellerForm();
            $this->_html .= '</div>';
            $this->_html .= '<div class="tab-pane" id="configProductForm">';
            $this->_html .= $this->renderProductForm();
            $this->_html .= '</div>';
            $this->_html .= '<div class="tab-pane" id="SellerStatistic">';
            $this->_html .= $this->renderSellerList();
            $this->_html .= '</div>';
            $this->_html .= '<div class="tab-pane" id="SellerPayments">';
            $this->_html .= $this->renderPaymentsList();
            $this->_html .= '</div>';
            $this->_html .= '</div>';
            $this->_html .= '</div>';
        }
        return $this->_html;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderSellerForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitBulletinboardModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormSellerValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($this->getConfigSellerForm());
    }
    
    protected function renderProductForm()
    {
        $helper = new HelperForm();
    
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
    
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitBulletinboardModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
    
        $helper->tpl_vars = array(
                'fields_value' => $this->getConfigFormProductValues(), /* Add values for your inputs */
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id,
        );
    
        return $helper->generateForm($this->getConfigProductForm());
    }
    

    private function renderSellerList()
    {
        $sellers = WsSeller::getSellersList();

        $fields_list = array(
                'id_ws_seller' => array(
                        'title' => $this->l('Id Seller'),
                        'type' => 'text',
                ),
                'img_seller' => array(
                    'title' => $this->l('Logo'),
                    'type' => 'img_seller',
                ),
                'name' => array(
                        'title' => $this->l('Name Seller'),
                        'width' => 140,
                        'type' => 'text',
                ),
                'customer' => array(
                        'title' => $this->l('Customer Info'),
                        'width' => 140,
                        'type' => 'text',
                ),
                'payment_acc' => array(
                        'title' => $this->l('Payment Info'),
                        'width' => 140,
                        'type' => 'text',
                ),
                'email' => array(
                        'title' => $this->l('Customer email'),
                        'width' => 140,
                        'type' => 'text',
                ),
                'product_count' => array(
                        'title' => $this->l('Products'),
                        'type' => 'text',
                ),
                'balance' => array(
                        'title' => $this->l('Balance'),
                        'type' => 'price',
                ),
                'active' => array(
                        'title' => $this->l('Status'),
                        'active' => 'status',
                        'type' => 'bool',
                ),
        );
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->actions = array('edit', 'view', 'delete');
        
        $helper->module = $this;
        $helper->identifier = 'id_ws_seller';
        $helper->title = $this->l('Seller List');
        $helper->table = 'ws_seller';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        return $helper->generateList($sellers, $fields_list);
    }
    
    public function renderSellerEditForm($id_product)
    {
        $fields_form_1 = array(
                'form' => array(
                        'legend' => array(
                                'title' => $this->l('Edit Seller'),
                                'icon' => 'icon-cogs'
                        ),
                        'input' => array(
                                array(
                                        'type' => 'hidden',
                                        'name' => 'id_ws_seller',
                                ),
                                array(
                                        'type' => 'hidden',
                                        'name' => 'id_customer',
                                ),
                                array(
                                        'type' => 'text',
                                        'label' => $this->l('Seller name'),
                                        'name' => 'name',
                                ),
                                array(
                                        'type' => 'text',
                                        'label' => $this->l('Payment account'),
                                        'name' => 'payment_acc',
                                ),
                                array(
                                        'type' => 'textarea',
                                        'label' => $this->l('Shop Description'),
                                        'name' => 'description',
                                        'lang' => true,
                                ),
                                array(
                                        'type' => 'switch',
                                        'is_bool' => true, //retro compat 1.5
                                        'label' => $this->l('Active'),
                                        'name' => 'active',
                                        'values' => array(
                                                array(
                                                        'id' => 'validate_on',
                                                        'value' => 1,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'validate_off',
                                                        'value' => 0,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                        ),
                        'submit' => array(
                                'title' => $this->l('Save'),
                                'class' => 'btn btn-default pull-right',
                                'name' => 'submitEditSeller',
                        )
                ),
        );
        
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->name;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEditSeller';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
                'fields_value' => $this->getSellerFieldsValues($id_product),
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id
        );
        
        return $helper->generateForm(array($fields_form_1));
    }
    
    public function renderPaymentEditForm($id_product)
    {
        $status = array();
        $status[] = array('id_status' => '0', 'title_status' => 'Sale');
        $status[] = array('id_status' => '1', 'title_status' => 'Query');
        $status[] = array('id_status' => '2', 'title_status' => 'Payment');
        $status[] = array('id_status' => '5', 'title_status' => 'Refund');
        $fields_form_1 = array(
                'form' => array(
                        'legend' => array(
                                'title' => $this->l('Edit Payment'),
                                'icon' => 'icon-cogs'
                        ),
                        'input' => array(
                                array(
                                        'type' => 'hidden',
                                        'name' => 'id_payment',
                                ),
                                /*
                                array(
                                        'type' => 'text',
                                        'label' => $this->l('Seller name'),
                                        'name' => 'name',
                                ),
                                */
                                array(
                                        'type' => 'text',
                                        'label' => $this->l('Amount'),
                                        'name' => 'summ',
                                        'width' => 140,
                                        //'type' => 'price',
                                        //'currency' => true,
                                ),
                                array(
                                        'type' => 'select',
                                        'label' => $this->l('Status'),
                                        'name' => 'status',
                                        'options' => array(
                                                'query' => $status,
                                                'id' => 'id_status',
                                                'name' => 'title_status'
                                )),

                        ),
                        'submit' => array(
                                'title' => $this->l('Save'),
                                'class' => 'btn btn-default pull-right',
                                'name' => 'submitEditPayment',
                        )
                ),
        );
    
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->name;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEditPayment';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
                'fields_value' => $this->getPaymentFieldsValues($id_product),
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id
        );
    
        return $helper->generateForm(array($fields_form_1));
    }
    
    
    private function renderPaymentsList()
    {
        $payments = PaymentBoard::getAllPayments();
        
        $fields_list = array(
                'id_payment' => array(
                        'title' => $this->l('Id Payment'),
                        'width' => 140,
                        'type' => 'text',
                        'remove_onclick' => true,
                ),
                'id_ws_seller' => array(
                        'title' => $this->l('Id Seller'),
                        'width' => 140,
                        'type' => 'text',
                        'remove_onclick' => true,
                ),
                'summ' => array(
                        'title' => $this->l('Amount'),
                        'width' => 140,
                        'type' => 'price',
                        'currency' => true,
                        'remove_onclick' => true,
                ),
                'description' => array(
                        'title' => $this->l('Description'),
                        'width' => 140,
                        'type' => 'text',
                        'remove_onclick' => true,
                ),
                'status' => array(
                        'title' => $this->l('Operation'),
                        'width' => 140,
                        'type' => 'text',
                        'remove_onclick' => true,
                ),
                'date_add' => array(
                        'title' => $this->l('Date'),
                        'width' => 140,
                        'type' => 'datetime',
                        'remove_onclick' => true,
                ),
        );
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
         
        // Actions to be displayed in the "Actions" column
        //$helper->actions = array('edit', 'delete', 'view');
        $helper->actions = array('edit');
        $helper->identifier = 'id_payment';
        $helper->show_toolbar = true;

        $helper->title = 'Payment List';
        $helper->table = 'ws_seller_payment';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        
        return $helper->generateList($payments, $fields_list);
    }
    
    public function getSellerFieldsValues($id = 0)
    {
        $seller = new WsSeller($id);
    
        return array(
                'id_ws_seller' => $seller->id_ws_seller,
                'id_customer' => $seller->id_customer,
                'name' => $seller->name,
                'description' => $seller->description,
                'payment_acc' => $seller->payment_acc,
                'active' => $seller->active,
        );
    }
    
    public function getPaymentFieldsValues($id = 0)
    {
        $payment = new PaymentBoard($id);
    
        return array(
                'id_ws_seller' => $payment->id_ws_seller,
                'id_payment' => $payment->id_payment,
                'summ' => $payment->summ,
                //'name' => $payment->name,
                'status' => $payment->status,
        );
    }
    
    /**
     * Create the structure of your form.
     */
    protected function getConfigSellerForm()
    {
        
        $cms_pages = CMS::getCMSPages($this->context->language->id);
        $ws_seller_group =  Group::getGroups($this->context->language->id);
        
        $fields_form_0 = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings Seller Page'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                        array(
                                'type' => 'switch',
                                'label' => $this->l('Active after adding (no sellers moderation)'),
                                'name' => 'BULLETINBOARD_NO_SELLER_MODERAT',
                                'is_bool' => true,
                                'values' => array(
                                        array(
                                                'id' => 'moderat_s_on',
                                                'value' => true,
                                                'label' => $this->l('Enabled')
                                        ),
                                        array(
                                                'id' => 'moderat_s_off',
                                                'value' => false,
                                                'label' => $this->l('Disabled')
                                        )
                                ),
                        ),
                        array(
                                'type' => 'switch',
                                'label' => $this->l('Display order history for the seller'),
                                'name' => 'BULLETINBOARD_ORDERS_ON',
                                'is_bool' => true,
                                'values' => array(
                                        array(
                                                'id' => 'order_on',
                                                'value' => true,
                                                'label' => $this->l('Enabled')
                                        ),
                                        array(
                                                'id' => 'order_off',
                                                'value' => false,
                                                'label' => $this->l('Disabled')
                                        )
                                ),
                        ),
                        array(
                                'type' => (version_compare(_PS_VERSION_, '1.6')<0) ?'radio' :'switch',
                                'label' => $this->l('Display information about the seller on the product page.'),
                                'name' => 'BULLETINBOARD_SHOW_INFO',
                                'is_bool' => true,
                                'values' => array(
                                        array(
                                                'id' => 'info_on',
                                                'value' => true,
                                                'label' => $this->l('Enabled')
                                        ),
                                        array(
                                                'id' => 'info_off',
                                                'value' => false,
                                                'label' => $this->l('Disabled')
                                        )
                                ),
                        ),
                        array(
                                'type' => 'select',
                                'label' => $this->l('CMS page with Terms of Service'),
                                'name' => 'BULLETINBOARD_CMS',
                                'options' => array(
                                        'query' => $cms_pages,
                                        'id' => 'id_cms',
                                        'name' => 'meta_title'
                                )),
                        array(
                                'type' => 'select',
                                'label' => $this->l('Customer groups allowed to add products.'),
                                'name' => 'group[]',
                                'multiple' => true,
                                'options' => array(
                                        'query' => $ws_seller_group,
                                        'id' => 'id_group',
                                        'name' => 'name'
                                )
                        ),
                ),
            ),
        );
        
        $fields_form_1 = array(
                'form' => array(
                        'legend' => array(
                                'title' => $this->l('Settings Email'),
                                'icon' => 'icon-email',
                        ),
                        'input' => array(
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Enable email notifications to the seller about activation'),
                                        'name' => 'BULLETINBOARD_EMAIL_S_ACTIVE',
                                        'is_bool' => true,
                                        'values' => array(
                                                array(
                                                        'id' => 'email_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'email_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Enable email notifications to the seller about product activation'),
                                        'name' => 'BULLETINBOARD_EMAIL_P_ACTIVE',
                                        'is_bool' => true,
                                        'values' => array(
                                                array(
                                                        'id' => 'email_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'email_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                        ),
                ),
        );
                                

        $order_states = OrderState::getOrderStates((int)$this->context->language->id);
        
        $fields_form_2 = array(
                'form' => array(
                        'legend' => array(
                                'title' => $this->l('Seller bonuses'),
                                'icon' => 'icon-child',
                        ),
                        'input' => array(
                                array(
                                        'type' => (version_compare(_PS_VERSION_, '1.6')<0) ?'radio' :'switch',
                                        'label' => $this->l('Enable Seller bonuses'),
                                        'name' => 'BULLETINBOARD_BONUS_ON',
                                        'is_bool' => true,
                                        'values' => array(
                                                array(
                                                        'id' => 'bonus_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'bonus_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'text',
                                        'label' => $this->l('Shop commission %'),
                                        'name' => 'BULLETINBOARD_COMMIS',
                                ),
                                array(
                                        'type' => 'select',
                                        'label' => $this->l('Voucher is awarded when the order is'),
                                        'name' => 'id_order_state_activ[]',
                                        'multiple' => true,
                                        'options' => array(
                                                'query' => $order_states,
                                                'id' => 'id_order_state',
                                                'name' => 'name'
                                )),
                        ),
                        'submit' => array(
                                'title' => $this->l('Save'),
                                'name' => 'submitSeller',
                        ),
                ),
        );
        return array($fields_form_0, $fields_form_1, $fields_form_2);
    }

    protected function getConfigProductForm()
    {
        $selected_cat = explode(',', Configuration::get('BULLETINBOARD_CATEGORY'));
    
        $fields_form_1 = array(
                'form' => array(
                        'legend' => array(
                                'title' => $this->l('Settings'),
                                'icon' => 'icon-cogs',
                        ),
                        'input' => array(
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Allow ordering of customer\'s products'),
                                        'name' => 'BULLETINBOARD_ORDER_MODE',
                                        'is_bool' => true,
                                        'values' => array(
                                                array(
                                                        'id' => 'active_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'active_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Active after adding (no products moderation)'),
                                        'name' => 'BULLETINBOARD_NO_MODERAT',
                                        'is_bool' => true,
                                        'values' => array(
                                                array(
                                                        'id' => 'no_moderat',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'moderat',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'categories',
                                        'label' => $this->l('Categories'),
                                        'name' => 'BULLETINBOARD_CATEGORY',
                                        'tree' => array(
                                                'use_search' => false,
                                                'id' => 'id_category',
                                                'use_checkbox' => true,
                                                'selected_categories' =>  $selected_cat,
                                        )),
                        ),
                ),
        );
    
    
        $fields_form_2 = array(
                'form' => array(
                        'legend' => array(
                                'title' => $this->l('Product Fields'),
                                'icon' => 'icon-list-ul',
                        ),
                        'input' => array(
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Enable virtual (digital) products'),
                                        'name' => 'BULLETINBOARD_F_TYPE',
                                        'is_bool' => true,
                                        'desc' => $this->l('If disabled, then all products will be added as "Standard"'),
                                        'values' => array(
                                                array(
                                                        'id' => 'type_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'type_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Reference code'),
                                        'name' => 'BULLETINBOARD_F_REFERENCE',
                                        'is_bool' => true,
                                        'values' => array(
                                                array(
                                                        'id' => 'active_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'active_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Description'),
                                        'name' => 'BULLETINBOARD_F_DESCRIPTION',
                                        'is_bool' => true,
                                        'values' => array(
                                                array(
                                                        'id' => 'active_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'active_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Category'),
                                        'name' => 'BULLETINBOARD_F_CATEGORY',
                                        'is_bool' => true,
                                        'values' => array(
                                                array(
                                                        'id' => 'active_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'active_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Manufacturer'),
                                        'name' => 'BULLETINBOARD_F_MANUFACTURER',
                                        'is_bool' => true,
                                        'values' => array(
                                                array(
                                                        'id' => 'active_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'active_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Condition'),
                                        'name' => 'BULLETINBOARD_F_CONDITION',
                                        'is_bool' => true,
                                        'values' => array(
                                                array(
                                                        'id' => 'active_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'active_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Options'),
                                        'name' => 'BULLETINBOARD_F_OPTIONS',
                                        'is_bool' => true,
                                        'values' => array(
                                                array(
                                                        'id' => 'active_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'active_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Quantity'),
                                        'name' => 'BULLETINBOARD_F_QUANTITY',
                                        'is_bool' => true,
                                        'values' => array(
                                                array(
                                                        'id' => 'active_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'active_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),
                                array(
                                        'type' => 'switch',
                                        'label' => $this->l('Include taxes'),
                                        'name' => 'BULLETINBOARD_TAXES',
                                        'is_bool' => true,
                                        'values' => array(
                                                array(
                                                        'id' => 'taxes_on',
                                                        'value' => true,
                                                        'label' => $this->l('Enabled')
                                                ),
                                                array(
                                                        'id' => 'taxes_off',
                                                        'value' => false,
                                                        'label' => $this->l('Disabled')
                                                )
                                        ),
                                ),

                        ),
                        'submit' => array(
                                'title' => $this->l('Save'),
                                'name' => 'submitGeneral',
                        ),
                ),
        );
        return array($fields_form_1, $fields_form_2);
    }
    
    /**
     * Set values for the inputs.
     */
    protected function getConfigFormSellerValues()
    {
        return array(
                'BULLETINBOARD_NO_SELLER_MODERAT' => Configuration::get('BULLETINBOARD_NO_SELLER_MODERAT'),
                'BULLETINBOARD_CMS' => Configuration::get('BULLETINBOARD_CMS'),
                'group[]' => unserialize(Configuration::get('BULLETINBOARD_GROUP')),
                'BULLETINBOARD_SHOW_INFO' => Configuration::get('BULLETINBOARD_SHOW_INFO'),
                'BULLETINBOARD_ORDERS_ON' => Configuration::get('BULLETINBOARD_ORDERS_ON'),
                'BULLETINBOARD_EMAIL_S_ACTIVE' =>  Configuration::get('BULLETINBOARD_EMAIL_S_ACTIVE'),
                'BULLETINBOARD_EMAIL_P_ACTIVE' =>  Configuration::get('BULLETINBOARD_EMAIL_P_ACTIVE'),
                'BULLETINBOARD_BONUS_ON' => Configuration::get('BULLETINBOARD_BONUS_ON'),
                'BULLETINBOARD_COMMIS' => Configuration::get('BULLETINBOARD_COMMIS'),
                'id_order_state_activ[]' => unserialize(Configuration::get('BULLETINBOARD_STATE_ACTIV')),
        );
    }
    
    protected function getConfigFormProductValues()
    {
        return array(
                'BULLETINBOARD_ORDER_MODE' => Configuration::get('BULLETINBOARD_ORDER_MODE'),
                'BULLETINBOARD_NO_MODERAT' => Configuration::get('BULLETINBOARD_NO_MODERAT'),
                'BULLETINBOARD_CATEGORY' => Configuration::get('BULLETINBOARD_CATEGORY'),
                
                'BULLETINBOARD_F_TYPE' => Configuration::get('BULLETINBOARD_F_TYPE'),
                'BULLETINBOARD_F_DESCRIPTION' => Configuration::get('BULLETINBOARD_F_DESCRIPTION'),
                'BULLETINBOARD_F_CATEGORY' => Configuration::get('BULLETINBOARD_F_CATEGORY'),
                'BULLETINBOARD_F_CONDITION' => Configuration::get('BULLETINBOARD_F_CONDITION'),
                'BULLETINBOARD_F_OPTIONS' => Configuration::get('BULLETINBOARD_F_OPTIONS'),
                'BULLETINBOARD_F_QUANTITY' => Configuration::get('BULLETINBOARD_F_QUANTITY'),
                'BULLETINBOARD_F_REFERENCE' => Configuration::get('BULLETINBOARD_F_REFERENCE'),
                'BULLETINBOARD_F_MANUFACTURER' => Configuration::get('BULLETINBOARD_F_MANUFACTURER'),
                'BULLETINBOARD_TAXES' => Configuration::get('BULLETINBOARD_TAXES'),
        );
    }
    
    /**
     * Save form data.
     */
    protected function postProcess()
    {

        if (Tools::isSubmit('submitGeneral')) {
            Configuration::updateValue('BULLETINBOARD_ORDER_MODE', Tools::getValue('BULLETINBOARD_ORDER_MODE'));
            Configuration::updateValue('BULLETINBOARD_NO_MODERAT', Tools::getValue('BULLETINBOARD_NO_MODERAT'));
            Configuration::updateValue('BULLETINBOARD_CATEGORY', implode(',', Tools::getValue('BULLETINBOARD_CATEGORY')));
            
            Configuration::updateValue('BULLETINBOARD_F_TYPE', Tools::getValue('BULLETINBOARD_F_TYPE'));
            Configuration::updateValue('BULLETINBOARD_F_DESCRIPTION', Tools::getValue('BULLETINBOARD_F_DESCRIPTION'));
            Configuration::updateValue('BULLETINBOARD_F_CATEGORY', Tools::getValue('BULLETINBOARD_F_CATEGORY'));
            Configuration::updateValue('BULLETINBOARD_F_CONDITION', Tools::getValue('BULLETINBOARD_F_CONDITION'));
            Configuration::updateValue('BULLETINBOARD_F_OPTIONS', Tools::getValue('BULLETINBOARD_F_OPTIONS'));
            Configuration::updateValue('BULLETINBOARD_F_QUANTITY', Tools::getValue('BULLETINBOARD_F_QUANTITY'));
            Configuration::updateValue('BULLETINBOARD_F_REFERENCE', Tools::getValue('BULLETINBOARD_F_REFERENCE'));
            Configuration::updateValue('BULLETINBOARD_F_MANUFACTURER', Tools::getValue('BULLETINBOARD_F_MANUFACTURER'));
            Configuration::updateValue('BULLETINBOARD_TAXES', Tools::getValue('BULLETINBOARD_TAXES'));

            $this->_html .= $this->displayConfirmation($this->l('General configuration updated.'));
        } elseif (Tools::isSubmit('submitSeller')) {
            Configuration::updateValue('BULLETINBOARD_NO_SELLER_MODERAT', Tools::getValue('BULLETINBOARD_NO_SELLER_MODERAT'));
            Configuration::updateValue('BULLETINBOARD_CMS', Tools::getValue('BULLETINBOARD_CMS'));
            Configuration::updateValue('BULLETINBOARD_GROUP', serialize(Tools::getValue('group')));
            Configuration::updateValue('BULLETINBOARD_SHOW_INFO', Tools::getValue('BULLETINBOARD_SHOW_INFO'));
            Configuration::updateValue('BULLETINBOARD_ORDERS_ON', Tools::getValue('BULLETINBOARD_ORDERS_ON'));
            Configuration::updateValue('BULLETINBOARD_EMAIL_S_ACTIVE', Tools::getValue('BULLETINBOARD_EMAIL_S_ACTIVE'));
            Configuration::updateValue('BULLETINBOARD_EMAIL_P_ACTIVE', Tools::getValue('BULLETINBOARD_EMAIL_P_ACTIVE'));
            Configuration::updateValue('BULLETINBOARD_BONUS_ON', Tools::getValue('BULLETINBOARD_BONUS_ON'));
            Configuration::updateValue('BULLETINBOARD_COMMIS', Tools::getValue('BULLETINBOARD_COMMIS'));
            Configuration::updateValue('BULLETINBOARD_STATE_ACTIV', serialize(Tools::getValue('id_order_state_activ')));

            $this->_html .= $this->displayConfirmation($this->l('Sellers configuration updated.'));
        } elseif (Tools::isSubmit('submitEditSeller')) {
            $id = (int)Tools::getValue('id_ws_seller');
            $seller = new WsSeller($id);
            
            $seller->name = Tools::getValue('name');
            $seller->payment_acc = Tools::getValue('payment_acc');

            foreach (Language::getLanguages(false) as $lang) {
                $seller->description[$lang['id_lang']] = Tools::getValue('description_'.(int)$lang['id_lang']);
            }
            
            $seller->active = Tools::getValue('active');
            $seller->save();
            
            if ($seller->active) {
                $customer_id = WsSeller::getCustomerId($id);
                if (!$customer_id) {
                    return ;
                }
                $customer = new Customer($customer_id);
                
                $data = array();
                $data['{seller_name}'] = $customer->firstname.' '.$customer->lastname;

                if (Configuration::get('BULLETINBOARD_EMAIL_S_ACTIVE')) {
                    $iso_lng = Language::getIsoById((int)($customer->id_lang));
                    
                    if (is_dir($this->dir_mails . $iso_lng . '/')) {
                        $id_lang_current = $customer->id_lang;
                    } else {
                        $id_lang_current = Language::getIdByIso('en');
                    }
                    
                    Mail::Send(
                        (int)$id_lang_current,
                        'seller_active',
                        $this->l('Your account is activated'),
                        $data,
                        $customer->email,
                        $customer->firstname.' '.$customer->lastname,
                        (string)Configuration::get('PS_SHOP_EMAIL'),
                        (string)Configuration::get('PS_SHOP_NAME'),
                        null,
                        null,
                        dirname(__FILE__).'/mails/'
                    );
                }
            }
            $this->_html .= $this->displayConfirmation($this->l('Seller Profile updated.'));
        } elseif (Tools::isSubmit('submitEditPayment') || Tools::getValue('statuspaymet')) {
            $id = (int)Tools::getValue('id_payment');
            $payment = new PaymentBoard($id);
            $payment->summ = Tools::getValue('summ');
            $payment->status = Tools::getValue('status');
            $payment->save();
        } elseif (Tools::isSubmit('statusws_seller')) {
            $id = (int)Tools::getValue('id_ws_seller');
            $seller = new WsSeller($id);
            
            if ($seller->id) {
                $seller->active = (int)(!$seller->active);
                $seller->save();
                
                if ($seller->active) {
                    $customer_id = WsSeller::getCustomerId($id);
                    if (!$customer_id) {
                        return ;
                    }
                    $customer = new Customer($customer_id);
                
                    $data = array();
                    $data['{seller_name}'] = $customer->firstname.' '.$customer->lastname;
                
                    if (Configuration::get('BULLETINBOARD_EMAIL_S_ACTIVE')) {
                        $iso_lng = Language::getIsoById((int)($customer->id_lang));
                         
                        if (is_dir($this->dir_mails . $iso_lng . '/')) {
                            $id_lang_current = $customer->id_lang;
                        } else {
                            $id_lang_current = Language::getIdByIso('en');
                        }
                         
                        Mail::Send(
                            (int)$id_lang_current,
                            'seller_active',
                            $this->l('Your account is activated'),
                            $data,
                            $customer->email,
                            $customer->firstname.' '.$customer->lastname,
                            (string)Configuration::get('PS_SHOP_EMAIL'),
                            (string)Configuration::get('PS_SHOP_NAME'),
                            null,
                            null,
                            dirname(__FILE__).'/mails/'
                        );
                    }
                }
                
                $this->_html .= $this->displayConfirmation($this->l('Seller Profile updated.'));
            }
        } elseif (Tools::isSubmit('viewws_seller')) {
            $id = WsSeller::getCustomerId((int)Tools::getValue('id_ws_seller'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminCustomers').'&id_customer='.$id.'&viewcustomer');
        } elseif (Tools::isSubmit('deletews_seller')) {
            $id_seller = (int)Tools::getValue('id_ws_seller');
            WsSeller::deleteByIdSeller($id_seller);
        }
    }


    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
        
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addJS($this->_path.'/views/js/jquery.fileupload.js');
    }
    
    public function hookBackOfficeHeader()
    {
        $css = '<style type="text/css">
                    .icon-AdminWsMarketplace:before{
                    content: "\f07a";
                color:#f0f048;
                    }
                    </style>
                    ';
        return $css;
    }
    
    public function hookDisplayCustomerAccount($params)
    {
        if ($this->groupSellerEnabled($params['cookie']->id_customer)) {
            $this->smarty->assign(
                array(
                            'module_link' => $this->context->link->getModuleLink('bulletinboard', 'boardcustomer'),
                )
            );
            return $this->display(__FILE__, 'customer_account.tpl');
        }
    }
    
    public function hookMyAccountBlock($params)
    {
        if ($this->groupSellerEnabled($params['cookie']->id_customer)) {
            $this->smarty->assign(
                array(
                            'module_link' => $this->context->link->getModuleLink('bulletinboard', 'boardcustomer'),
                )
            );
            return $this->display(__FILE__, 'my-account.tpl');
        }
    }
    
    public function hookDisplayMyAccountBlockfooter($params)
    {
        if ($this->groupSellerEnabled($params['cookie']->id_customer)) {
            $this->smarty->assign(
                array(
                            'module_link' => $this->context->link->getModuleLink('bulletinboard', 'boardcustomer'),
                )
            );
            return $this->display(__FILE__, 'my-account.tpl');
        }
    }
    
   
    public function hookDisplayProductListFunctionalButtons()
    {
        $customer_cat_id = Configuration::get('BULLETINBOARD_CATEGORY');
        $id_category = Tools::getValue('id_category');
        if ($customer_cat_id == $id_category) {
            $this->context->smarty->assign('order_mode', Configuration::get('BULLETINBOARD_ORDER_MODE'));
               return $this->display(__FILE__, 'productListFunctionalButtons.tpl');
        }
    }
    
    public function hookDisplayRightColumnProduct()
    {
        $id_product = Tools::getValue('id_product');
            
        if (Board::issetProduct($id_product) and Configuration::get('BULLETINBOARD_SHOW_INFO')) {
            $id_seller = Board::getSellerByProduct($id_product);
            $seller = new WsSeller((int)$id_seller, $this->context->cookie->id_lang);
            
            $this->context->smarty->assign(array(
                    'seller' => $seller,
                    'seller_link' => $this->context->link->getModuleLink('bulletinboard', 'default', ['id_seller'=>$id_seller], true),
            ));
            
            return $this->display(__FILE__, 'right_column.tpl');
        }
    }
    
    public function hookproductActions($params)
    {
        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            $id_product = Tools::getValue('id_product');
            
            if (Board::issetProduct($id_product) and Configuration::get('BULLETINBOARD_SHOW_INFO')) {
                $id_seller = Board::getSellerByProduct($id_product);
                $seller = new WsSeller((int)$id_seller, $this->context->cookie->id_lang);
            
                $this->context->smarty->assign(array(
                        'seller' => $seller,
                        'seller_link' => $this->context->link->getModuleLink('bulletinboard', 'default', ['id_seller'=>$id_seller], true),
                ));
            
                return $this->display(__FILE__, 'right_column.tpl');
            }
        }
    }

    public function hookActionProductDelete($param)
    {
        $id_product = Tools::getValue('id_product');
        if (Board::issetProduct($id_product)) {
            Board::deleteProduct($id_product);
        }
    }
    
    public function hookActionProductUpdate($params)
    {
        $idProduct = false;

        if (isset($params['product']) && Validate::isLoadedObject($params['product'])) {
            $idProduct = (int)$params['product']->id;
        } else {
            $idProduct = (int)Tools::getValue('id_product');
        }
        
        // Update from BO
        if ($seller_id = Tools::getValue('form_ws_seller')) {
            if (!Board::issetProduct($idProduct, $seller_id)) {
                $board = new Board();
                $board->id_product = $idProduct;
                $board->id_ws_seller = $seller_id;
                $board->save();
            }
        }
        
        // Update from FO
        if ($idProduct !== false and Board::issetProduct($idProduct) and $params['product']->active) {
            $id_seller = Board::getSellerByProduct($idProduct);
            $customer_id = WsSeller::getCustomerId($id_seller);
            if (!$customer_id) {
                return ;
            }
            $customer = new Customer($customer_id);
            $product = new Product($idProduct, $customer->id_lang);
            if (Configuration::get('BULLETINBOARD_EMAIL_P_ACTIVE')) {
                $data = array();
                $data['{seller_name}'] = $customer->firstname.' '.$customer->lastname;
                $data['{product_name}'] = $product->name;
                $data['{product_link}'] = $this->context->link->getProductLink($idProduct);
                
                $iso_lng = Language::getIsoById((int)($customer->id_lang));
                
                if (is_dir($this->dir_mails . $iso_lng . '/')) {
                    $id_lang_current = $customer->id_lang;
                } else {
                    $id_lang_current = Language::getIdByIso('en');
                }
                 
                Mail::Send(
                    (int)$id_lang_current,
                    'seller_product_active',
                    $this->l('Your product is activated'),
                    $data,
                    $customer->email,
                    $customer->firstname.' '.$customer->lastname,
                    (string)Configuration::get('PS_SHOP_EMAIL'),
                    (string)Configuration::get('PS_SHOP_NAME'),
                    null,
                    null,
                    dirname(__FILE__).'/mails/'
                );
            }
        }
    }
    
    public function hookActionProductSave($params)
    {
        $idProduct = false;
    
        if (isset($params['product']) && Validate::isLoadedObject($params['product'])) {
            $idProduct = (int)$params['product']->id;
        } else {
            $idProduct = (int)Tools::getValue('id_product');
        }
    
        // Update from BO
        if ($seller_id = Tools::getValue('form_ws_seller')) {
            if (!Board::issetProduct($idProduct, $seller_id)) {
                $board = new Board();
                $board->id_product = $idProduct;
                $board->id_ws_seller = $seller_id;
                $board->save();
            }
        }
    
        // Update from FO
        if ($idProduct !== false and Board::issetProduct($idProduct) and $params['product']->active) {
            $id_seller = Board::getSellerByProduct($idProduct);
            $customer_id = WsSeller::getCustomerId($id_seller);
            if (!$customer_id) {
                return ;
            }
            $customer = new Customer($customer_id);
            $product = new Product($idProduct, $customer->id_lang);
            if (Configuration::get('BULLETINBOARD_EMAIL_P_ACTIVE')) {
                $data = array();
                $data['{seller_name}'] = $customer->firstname.' '.$customer->lastname;
                $data['{product_name}'] = $product->name;
                $data['{product_link}'] = $this->context->link->getProductLink($idProduct);
                 
                $iso_lng = Language::getIsoById((int)($customer->id_lang));
                 
                if (is_dir($this->dir_mails . $iso_lng . '/')) {
                    $id_lang_current = $customer->id_lang;
                } else {
                    $id_lang_current = Language::getIdByIso('en');
                }
    
                Mail::Send(
                    (int)$id_lang_current,
                    'seller_product_active',
                    $this->l('Your product is activated'),
                    $data,
                    $customer->email,
                    $customer->firstname.' '.$customer->lastname,
                    (string)Configuration::get('PS_SHOP_EMAIL'),
                    (string)Configuration::get('PS_SHOP_NAME'),
                    null,
                    null,
                    dirname(__FILE__).'/mails/'
                );
            }
        }
    }
    
    public function hookDisplayAdminProductsExtra($params)
    {
        if (isset($params['id_product'])) {
            // Presta 1.7
            $id_product = (int)$params['id_product'];
        } else {
            // Presta 1.6
            $id_product = (int)Tools::getValue('id_product');
        }
        
        if (!$id_product) {
            return false;
        }
    
        if (Validate::isLoadedObject(new Product($id_product))) {
            $seller_list = WsSeller::getSellersList();
            $id_seller = false;
            $current_seller = false;
            $current_customer = false;
            
            if (Board::issetProduct($id_product)) {
                $id_seller = Board::getSellerByProduct($id_product);
                $current_seller = new WsSeller($id_seller);
                $current_customer = new Customer($current_seller->id_customer);
                $current_seller->customer_url = $this->context->link->getAdminLink('AdminCustomers').'&id_customer='.$current_seller->id_customer.'&viewcustomer';
            }

            $this->context->smarty->assign(array(
                    'seller_list' => $seller_list,
                    'id_seller' => $id_seller,
                    'current_seller' => $current_seller,
                    'current_customer' => $current_customer,
            ));
            
               return $this->display(__FILE__, '/views/templates/admin/bo-extra-tab.tpl');
        }
    }
    
    public function hookDisplayAdminOrder()
    {
        require_once(_PS_MODULE_DIR_.'bulletinboard/classes/WsSellerOrders.php');
        
        $id_order = Tools::getValue('id_order');
        $order = new Order($id_order);
        $state = new OrderState($order->getCurrentState(), Configuration::get('PS_LANG_DEFAULT'));
        
        if ($seller_order_details = WsSellerOrders::getProductsByOrderId($id_order)) {
            $this->context->smarty->assign(array(
                    'seller_order_details' => $seller_order_details,
                    'customer_link' => $this->context->link->getAdminLink('AdminCustomers').'&id_customer=',
                    //'currentState' => $state,
            ));
            return $this->display(__FILE__, '/views/templates/admin/bo-seller-order.tpl');
        }
    }

    public function hookActionObjectDeleteBefore($params)
    {
        $object = $params['object'];
        
        if ($object instanceof Customer) {
            if ($id_seller = WsSeller::getSellerId($object->id)) {
                WsSeller::deleteByIdSeller($id_seller);
            }
        }
    }
    
    /* Hook called when a new order is created */
    public function hookNewOrder($params)
    {
        require_once(_PS_MODULE_DIR_.'bulletinboard/classes/WsSellerOrders.php');
    
        $order = new Order((int)$params['order']->id);
        
        if ($order && !Validate::isLoadedObject($order)) {
            die(Tools::displayError('Incorrect object Order.'));
        }
        
        $products = $order->getProducts();
        
        foreach ($products as $product) {
            if ($id_seller = Board::getSellerByProduct((int)$product['product_id'])) {
                $s_order = new WsSellerOrders();
                $s_order->id_order = (int)$params['order']->id;
                $s_order->id_ws_seller = $id_seller;
                $s_order->id_product = (int)$product['product_id'];
                $s_order->id_product_attribute = (int)$product['product_attribute_id'];
                $s_order->save();
            }
        }
    }
    
    public function hookUpdateOrderStatus($params)
    {
        if (Configuration::get('BULLETINBOARD_BONUS_ON')) {
            if (!Validate::isLoadedObject($params['newOrderStatus'])) {
                die(Tools::displayError('Some parameters are missing.'));
            }
            $order = new Order((int)$params['id_order']);
            $orderState = $params['newOrderStatus'];
            
            if ($order && !Validate::isLoadedObject($order)) {
                die(Tools::displayError('Incorrect object Order.'));
            }

            $activ_order_status = unserialize(Configuration::get('BULLETINBOARD_STATE_ACTIV'));
            //TODO Remove pay
            //$remove_order_status = unserialize(Configuration::get('BULLETINBOARD_STATE_REMOVE'));
            $include_taxes = Configuration::get('BULLETINBOARD_TAXES');

            if (in_array($orderState->id, $activ_order_status)) {
                if (!class_exists('PaymentBoard')) {
                    require_once(_PS_MODULE_DIR_.'bulletinboard/classes/PaymentBoard.php');
                }
                $products = $order->getProducts();
              
                foreach ($products as $product) {
                    $t_product = new Product((int)$product['product_id']);
                    if (Validate::isLoadedObject($t_product)) {
                        if ($id_seller = Board::getSellerByProduct((int)$product['product_id'])) {
                            $customer_id = WsSeller::getCustomerId($id_seller);
                            if (!$customer_id) {
                                return;
                            }
                            PaymentBoard::deletePaymentByOrderIdAndProductId($order->id, $product['product_id']);

                            if (!PaymentBoard::paymentExists($order->id, $product['product_id'])) {
                                $payment = new PaymentBoard();

                                $percent = 1 - Configuration::get('BULLETINBOARD_COMMIS') / 100;
                                $product_price = ($include_taxes) ? $product['total_price_tax_incl'] : $product['total_price_tax_excl'];
                                $sum = $product_price / $order->conversion_rate * $percent;
                                $currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
                                if ($sum > 0) {
                                    $payment->id_order = $order->id;
                                    $payment->id_ws_seller = $id_seller;
                                    $payment->summ = $sum;
                                    $payment->id_product = $product['product_id'];
                                    $payment->status = 0;
                                    $payment->description = $product['product_name'];
                                    if ($payment->add()) {
                                        $product_total_price = $product['product_price']*$product['product_quantity'];
                                        $data = array();
                                        $data['{product_name}'] = $product['product_name'];
                                        $data['{product_quantity}'] = $product['product_quantity'];
                                        $data['{product_price}'] = Product::convertPriceWithCurrency(array('price' => $product['product_price'], 'currency' => $currency), $this->context->smarty);
                                        $data['{product_total_price}'] = Product::convertPriceWithCurrency(array('price' => $product_total_price, 'currency' => $currency), $this->context->smarty);
                                        $data['{service_charge}'] = '-'.Configuration::get('BULLETINBOARD_COMMIS').'%';
                                        $data['{product_sum}'] = Product::convertPriceWithCurrency(array('price' => $payment->summ, 'currency' => $currency), $this->context->smarty);
                                         
                                        $customer = new Customer($customer_id);
                                        if (Validate::isEmail($customer->email)) {
                                            Mail::Send(
                                                (int)$customer->id_lang,
                                                'purchase',
                                                Mail::l('Congratulations you have sold your product', (int)$customer->id_lang),
                                                $data,
                                                $customer->email,
                                                $customer->firstname.' '.$customer->lastname,
                                                (string)Configuration::get('PS_SHOP_EMAIL'),
                                                (string)Configuration::get('PS_SHOP_NAME'),
                                                null,
                                                null,
                                                dirname(__FILE__).'/mails/'
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    public function groupSellerEnabled($id_customer)
    {
        $customer = new Customer($id_customer);
        $groups_enabled = unserialize(Configuration::get('BULLETINBOARD_GROUP'));
        if (!is_array($groups_enabled)) {
            $groups_enabled = array();
        }
        return in_array($customer->id_default_group, $groups_enabled);
    }
    
    public function translateCustom()
    {
        return array(
                'seller_request'=> $this->l('Request to activate the seller'),
                'seller_add_product' => $this->l('The seller added a new product'),
        );
    }
    
    public function hookActionDeleteGDPRCustomer($customer)
    {
    	if (!empty($customer['id'])) {
    		$sql = "SElECT id_ws_seller FROM "._DB_PREFIX_."ws_seller WHERE id_customer = '".(int)$customer['id']."'";
    		if ($id_seller = Db::getInstance()->getValue($sql)) {
    			$seller = new WsSeller($id_seller);
    			$seller->delete();
    
    			return json_encode(true);
    		}
    		return json_encode($this->l('Seller : Unable to delete customer using id.'));
    	}
    }
}
