<?php
/**
 * 2007-2016 PrestaShop
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

if (!class_exists('WsSeller')) {
    require_once(_PS_MODULE_DIR_.'bulletinboard/classes/WsSeller.php');
}

if (!class_exists('WsSellerOrders')) {
    require_once(_PS_MODULE_DIR_.'bulletinboard/classes/WsSellerOrders.php');
}

if (!class_exists('Board')) {
    require_once(_PS_MODULE_DIR_.'bulletinboard/classes/Board.php');
}

if (!class_exists('PaymentBoard')) {
    require_once(_PS_MODULE_DIR_.'bulletinboard/classes/PaymentBoard.php');
}

class BulletinboardBoardcustomerModuleFrontController extends ModuleFrontController
{
    
    public function __construct()
    {
        parent::__construct();
    
        $this->context = Context::getContext();
        $this->dir_mails = dirname(__FILE__).'/../../mails/';
    }
    
    public function initContent()
    {
        if (!$this->context->cookie->isLogged()) {
            Tools::redirect('authentication.php?back=modules/'.$this->module->name.'/boardcustomer.php');
        }
        
        parent::initContent();
        
        
        $customer = new Customer((int)$this->context->cookie->id_customer);
        
        if (Tools::isSubmit('submitAgree')) {
            $seller = new WsSeller();
            $seller->name = ($customer->firstname.' '.$customer->lastname);
            $seller->id_customer = $this->context->cookie->id_customer;
            $seller->active = Configuration::get('BULLETINBOARD_NO_SELLER_MODERAT');
            $seller->add();
            
            $data = array();
            $data['{seller_name}'] = $customer->firstname.' '.$customer->lastname;
            
            $iso_lng = Language::getIsoById((int)($this->context->language->id));
         
            if (is_dir($this->dir_mails . $iso_lng . '/')) {
                $id_lang_current = $this->context->language->id;
            } else {
                $id_lang_current = Language::getIdByIso('en');
            }
            
            $obj_module = new bulletinboard();
            $data_translate = $obj_module->translateCustom();
             
            Mail::Send(
                (int)$id_lang_current,
                'seller_request',
                (version_compare(_PS_VERSION_, '1.7', '>') ? $data_translate['seller_request'] :  Mail::l('Request to activate the seller', (int)$this->context->language->id)),
                $data,
                (string)Configuration::get('PS_SHOP_EMAIL'),
                $customer->firstname.' '.$customer->lastname,
                (string)Configuration::get('PS_SHOP_EMAIL'),
                (string)Configuration::get('PS_SHOP_NAME'),
                null,
                null,
                dirname(__FILE__).'/../../mails/'
            );
            
            Tools::redirect($this->context->link->getModuleLink('bulletinboard', 'boardcustomer'));
        }
        
        $seller_id = WsSeller::getSellerId($this->context->cookie->id_customer);
        $seller = new WsSeller($seller_id);
        
        if (Tools::getValue('add') || Tools::getValue('edit')) {
            $this->renderProductForm($seller);
        } elseif (Tools::getValue('order_detail')) {
            $this->renderOrderForm($seller);
        } else {
            $this->renderBoard($seller);
        }
    }
    
    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->addjqueryPlugin('fancybox');
        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->context->controller->registerJavascript('modules-boardcustomer', 'modules/'.$this->module->name.'/views/js/boardcustomer.js', ['position' => 'bottom', 'priority' => 150]);
        } else {
            $this->context->controller->addJS(_MODULE_DIR_.$this->module->name.'/views/js/boardcustomer.js');
        }
    }

   
    public function renderBoard($seller)
    {
        $cms = new CMS(Configuration::get('BULLETINBOARD_CMS'), $this->context->cookie->id_lang);
        $seller_id = (int)$seller->id;
        if (!Validate::isLoadedObject($cms)) {
            $cms = false;
        }
         
        $currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        $bonus_on = Configuration::get('BULLETINBOARD_BONUS_ON');
        $orders_on = Configuration::get('BULLETINBOARD_ORDERS_ON');
        $orders_history = false;
        
        $this_link = $this->context->link->getModuleLink('bulletinboard', 'boardcustomer');
         
        if ($bonus_on) {
            $ballance = (float)PaymentBoard::getBallance((int)$seller_id);
            $rewards = 100 - (int)Configuration::get('BULLETINBOARD_COMMIS');
        } else {
            $ballance = false;
            $rewards = false;
        }
         
        if ($orders_on) {
            $orders_history = WsSellerOrders::getOrders((int)$seller_id);
        }
        
        if (Tools::getValue('delete')) {
            $id_product = (int)Tools::getValue('id_product');
            if (Board::issetProduct($id_product, $seller_id)) {
                $product = new Product($id_product);
                $product->delete();
                Board::deleteProduct($id_product);
                 
                $this->context->smarty->assign(array(
                        'action_delete' => true,
                ));
            } else {
                $this->context->smarty->assign(array(
                        'action_delete' => false,
                ));
            }
        }
         
        
        $p = (int)Tools::getValue('p');
        $pagin_on = false;
        $pages = false;
        $step = 6;
         
        $start = (int)(($p - 1)*$step);
        if ($start < 0) {
            $start = 0;
        }
         
        $products = Board::getProductsForSeller($seller_id, $start, $step);
        $count_products = Board::getCountProducts($seller_id);
         
        if ($count_products > $step) {
            $pagin_on = true;
            $pages = Board::pagingProducts($start, $step, $count_products, $this_link);
        }
         
        $payments = PaymentBoard::getPayments($seller_id);
        $count_payments = count($payments);
         
        for ($i = 0; $i < $count_payments; $i++) {
            $payments[$i]['summ'] = Product::convertPriceWithCurrency(array('price' => $payments[$i]['summ'], 'currency' => $currency), $this->context->smarty);
        }
         
        $total_q = 0;
        $total_summ = 0;
        if (is_array($products)) {
            foreach ($products as $product) {
                $total_q += (int)$product['sold_q'];
                $total_summ += (float)$product['sold_total'];
            }
        }
        for ($i = 0; $i < count($products); $i++) {
            $products[$i]['price'] = Product::convertPriceWithCurrency(array('price' => $products[$i]['price'], 'currency' => $currency), $this->context->smarty);
            $products[$i]['sold_total'] = Product::convertPriceWithCurrency(array('price' => $products[$i]['sold_total'], 'currency' => $currency), $this->context->smarty);
        }
         
         
        $this->context->smarty->assign(array(
                'action_delete' => false,
                'moderat' => Configuration::get('BULLETINBOARD_NO_MODERAT'),
                'cms' => $cms,
                'seller' => $seller,
                'bonus_on' => $bonus_on,
                'orders_on' => $orders_on,
                'rewards' => $rewards,
                'currency' => $currency,
                'int_ballance' => $ballance,
                'ballance' => Product::convertPriceWithCurrency(array('price' => $ballance, 'currency' => $currency), $this->context->smarty),
                'added' => Tools::getValue('added'),
                'upload_file_dir' => _MODULE_DIR_.$this->module->name.'/upload/file_upload.php',
                'upload_image_dir' => _MODULE_DIR_.$this->module->name.'/upload/image_upload.php',
                'orders_history' => $orders_history,
                'languages' => Language::getLanguages(false),
                'defaultFormLanguage' => $this->context->language->id,
                'products' => $products,
                'count_products' => $count_products,
                'pagin_on' => $pagin_on,
                'pages'    => $pages,
                'this_link' => $this_link,
                'payments' => $payments,
                'total_q' => $total_q,
                'total_summ' => Product::convertPriceWithCurrency(array('price' => $total_summ, 'currency' => $currency), $this->context->smarty),
        ));
         
        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/display17.tpl');
        } else {
            $this->setTemplate('display.tpl');
        }
    }
    
    public function renderProductForm($seller)
    {
        $id_product = (Tools::getValue('id_product') ? Tools::getValue('id_product') : null);
        
        if (Tools::getValue('edit') and $id_product) {
            if (!Board::issetProduct($id_product, $seller->id)) {
                Tools::redirect($this->context->link->getModuleLink('bulletinboard', 'boardcustomer'));
            }
        }
        
        if (Tools::getValue('img_del')) {
            $id_img = (int)Tools::getValue('img_del');
            //$id_product = (int)Tools::getValue('id_product');
            $image = new Image($id_img);
            if (Validate::isLoadedObject($image)) {
                if ($image->cover) {
                    Image::deleteCover($id_product);
                    $image->delete();
                    $other_images = Image::getImages((int)$this->context->language->id, $id_product);
                    //die(var_dump($other_images));
                    if ($other_images) {
                        Db::getInstance()->execute(
                            'UPDATE `'._DB_PREFIX_.'image`
                                SET `cover` = 1
                                WHERE `id_image` = '.(int)$other_images[0]['id_image']
                        );
        
                        Db::getInstance()->execute(
                            'UPDATE `'._DB_PREFIX_.'image_shop`
                                SET `cover` = 1
                                WHERE `id_image` = '.(int)$other_images[0]['id_image']
                        );
                    }
                } else {
                    $image->delete();
                }
            }
        }
        
        $product = new Product($id_product);
        
        if (Validate::isLoadedObject($product)) {
            $product->quantity = StockAvailable::getQuantityAvailableByProduct($id_product);
            $product->categories_ids = Product::getProductCategories($product->id);
        
            $this->context->smarty->assign(array(
                    'id_product' => $product->id,
            ));
        } else {
            $this->context->smarty->assign(array(
                    'id_product' => false,
            ));
        }
        
       
        $this->context->smarty->assign(array(
                'show_type' =>  Configuration::get('BULLETINBOARD_F_TYPE'),
                'moderat' => Configuration::get('BULLETINBOARD_NO_MODERAT'),
                'edit_product' => $product,
                'seller' => $seller,
                'link_rewrite' => $product->link_rewrite[$this->context->language->id],
                'languages' => Language::getLanguages(false),
                'defaultFormLanguage' => $this->context->language->id,
                'categories' => $this->getBoardCategories(),
                'manufacturers' => Manufacturer::getManufacturers(),
                'cat_branch' => _PS_MODULE_DIR_.$this->module->name.'/views/templates/front/bulletinboard_branch.tpl',
                'lang_icon_dir' => _PS_IMG_.'l/',
                'use_taxes' =>  Configuration::get('BULLETINBOARD_TAXES'),
                'use_reference' =>  Configuration::get('BULLETINBOARD_F_REFERENCE'),
                'use_description' =>  Configuration::get('BULLETINBOARD_F_DESCRIPTION'),
                'use_category' =>  Configuration::get('BULLETINBOARD_F_CATEGORY'),
                'use_manufacturer' =>  Configuration::get('BULLETINBOARD_F_MANUFACTURER'),
                'use_condition' =>  Configuration::get('BULLETINBOARD_F_CONDITION'),
                'use_options' =>  Configuration::get('BULLETINBOARD_F_OPTIONS'),
                'use_quantity' =>  Configuration::get('BULLETINBOARD_F_QUANTITY'),
                'tax_rules' => TaxRulesGroup::getTaxRulesGroups(),
                'image_product' => $product->getImages((int)$this->context->language->id),
        ));
        
        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/product_form17.tpl');
        } else {
            $this->setTemplate('product_form.tpl');
        }
    }
    
    public function renderOrderForm($seller)
    {
        $id_order = (int)Tools::getValue('order_detail');
        if (WsSellerOrders::issetOrder($id_order, $seller->id_ws_seller)) {
            $ws_order = WsSellerOrders::getOrderDetail($id_order);
            $this->context->smarty->assign(array(
                    'ws_order' => $ws_order,
                    'seller' => $seller,
            ));
        } else {
            $this->context->smarty->assign(array(
                    'ws_order' => false,
                    'seller' => $seller,
            ));
        }
         
        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/order_form17.tpl');
        } else {
            $this->setTemplate('order_form.tpl');
        }
    }
    
    public function getBoardCategories()
    {
        // TODO _PS_DEFAULT_CUSTOMER_GROUP_ in 1.7
        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            $id_group = 1;
        } else {
            $id_group = _PS_DEFAULT_CUSTOMER_GROUP_;
        }
    
        if ($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
                    SELECT DISTINCT c.*, cl.*
                    FROM `'._DB_PREFIX_.'category` c
                    LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int)$this->context->language->id.')
                    LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`)
                    WHERE (c.`active` = 1 OR c.`id_category` = 1)
                    AND cg.`id_group` = '.$id_group.'
                    ORDER BY `level_depth` ASC, c.`position` ASC')) {
                        $result_parents = array();
                        $result_ids = array();
    
                        foreach ($result as &$row) {
                            $result_parents[$row['id_parent']][] = &$row;
                            $result_ids[$row['id_category']] = &$row;
                        }
                        $block_categ_tree = $this->getTree($result_parents, $result_ids, 10, null, 0);
        }
        
        
        return $block_categ_tree;
    }
    
    public function getTree($result_parents, $result_ids, $max_depth, $id_category = null, $current_depth = 0)
    {
        $selected_cat = explode(',', Configuration::get('BULLETINBOARD_CATEGORY'));
    
        $allow = false;
         
        if (is_null($id_category) && version_compare(_PS_VERSION_, '1.5', '>=')) {
            $id_category = Context::getContext()->shop->getCategory();
        }
         
        $children = array();
    
        if (isset($result_parents[$id_category]) && count($result_parents[$id_category]) && ($max_depth == 0 || $current_depth < $max_depth)) {
            foreach ($result_parents[$id_category] as $subcat) {
                $children[] = $this->getTree($result_parents, $result_ids, $max_depth, $subcat['id_category'], $current_depth + 1);
            }
        }
         
        if (!isset($result_ids[$id_category])) {
            return false;
        }
    
        if (in_array($id_category, $selected_cat)) {
            $allow = true;
        }
        
        if ($allow) {
            $result = array(
                    'id' => $id_category,
                    'name' => $result_ids[$id_category]['name'],
                    'allow' => $allow,
                    'children' => array_filter($children));
            return $result;
        }
    }
    
    public function postProcess()
    {
        $errors = array();
        
        if (Tools::isSubmit('submitAddProduct')) {
            $use_taxes = Configuration::get('BULLETINBOARD_TAXES');
            $product = new Product((Tools::getValue('id_product', null)));
            $id_product = Tools::getValue('id_product', null);
            $seller_id = WsSeller::getSellerId($this->context->customer->id);
            
            if (!Board::issetProduct($id_product, $seller_id) and $id_product) {
                Tools::redirect($this->context->link->getModuleLink('bulletinboard', 'boardcustomer'));
            }
            
            $fields = array(
                    'price' => Tools::getValue('price'),
                    'reference' => Tools::getValue('reference'),
                    'tax_rule' => Tools::getValue('tax_rule', null),
                    'id_category_default' => Tools::getValue('id_category_default'),
                    'condition' => Tools::getValue('condition'),
                    'quantity' => Tools::getValue('quantity'),
                    'id_manufacturer' => Tools::getValue('id_manufacturer'),
            );
            
            $languages = Language::getLanguages(false);
            
            $definition = Product::$definition['fields'];
            $definition = array_merge($definition, Board::$definition['fields']);
            $name_default = Tools::getValue('name_'.$this->context->language->id);
            
            if (empty($name_default)) {
                $errors[] = $this->module->l('Do not enter a title for the primary language');
            }
            
            foreach ($languages as $lang) {
                $fields['name'][$lang['id_lang']] = Tools::getValue('name_'.$lang['id_lang']);
                if ($fields['name'][$lang['id_lang']]) {
                    $fields['link_rewrite'][$lang['id_lang']] = Tools::link_rewrite($fields['name'][$lang['id_lang']]);
                } else {
                    if ($name_default) {
                        $fields['link_rewrite'][$lang['id_lang']] = Tools::link_rewrite($name_default);
                        $fields['name'][$lang['id_lang']] = $name_default;
                    } else {
                        $errors[] = $this->module->l('No name and URL in your Lang');
                    }
                }

                $fields['description_short'][$lang['id_lang']] = Tools::substr(strip_tags(Tools::getValue('description_short_'.$lang['id_lang'])), 0, 800);
                $fields['description'][$lang['id_lang']] = strip_tags(Tools::getValue('description_'.$lang['id_lang']));
            }

            if (!count($errors)) {
                if (Configuration::get('BULLETINBOARD_F_TYPE') and Tools::getValue('is_virtual')) {
                    $product->is_virtual = 1;
                } else {
                    $product->is_virtual = 0;
                }
                
                $product->name = $fields['name'];
                $product->reference = $fields['reference'];
                $product->link_rewrite = $fields['link_rewrite'];
                $product->id_category_default = $fields['id_category_default'];
                $product->price = $fields['price'];
                //$product->quantity = $fields['quantity'];
                $product->id_manufacturer = $fields['id_manufacturer'];
                $product->id_tax_rules_group = 0;
                if ($use_taxes) {
                    $product->id_tax_rules_group = $fields['tax_rule'];
                }
                $product->condition = $fields['condition'];
                $product->description_short = $fields['description_short'];
                $product->description = $fields['description'];
                
                if (Configuration::get('BULLETINBOARD_NO_MODERAT')) {
                    $product->active = Tools::getValue('active');
                } else {
                    $product->active = 0;
                }

                $product->available_for_order =  Configuration::get('BULLETINBOARD_ORDER_MODE');
                if ($product->save()) {
                    if (!$product->addToCategories(Tools::getValue('categories'))) {
                        $errors[] = $this->module->l('Error: This product is not added to categories.');
                    }
                    
                    $quantity_old = StockAvailable::getQuantityAvailableByProduct($product->id);
                    if ($quantity_old != $fields['quantity']) {
                        $quantity_new = $fields['quantity'] - $quantity_old;
                         StockAvailable::updateQuantity($product->id, 0, $quantity_new);
                    }

                    if (isset($_FILES['image_product']['tmp_name'])) {
                        foreach ($_FILES['image_product']['tmp_name'] as $image_product) {
                            if (file_exists($image_product)) {
                                $image = new Image();
                                $image->id_product = (int)$product->id;
                                $image->position = Image::getHighestPosition($product->id) + 1;
                                $image->cover = !count($product->getImages(Configuration::get('PS_LANG_DEFAULT')));
                                $image->save();
                                if (!file_exists(_PS_PROD_IMG_DIR_.Image::getImgFolderStatic($image->id))) {
                                    $image->createImgFolder();
                                }
                                ImageManager::resize(
                                    $image_product,
                                    _PS_PROD_IMG_DIR_.Image::getImgFolderStatic($image->id).$image->id.'.jpg'
                                );
                                $types = ImageType::getImagesTypes('products');
                                foreach ($types as $type) {
                                    ImageManager::resize(
                                        $image_product,
                                        _PS_PROD_IMG_DIR_.Image::getImgFolderStatic($image->id).$image->id.'-'.$type['name'].'.jpg',
                                        $type['width'],
                                        $type['height']
                                    );
                                }
                            }
                        }
                    }
                    
                    if ($_FILES['virtual_product_file_uploader']['name'] && (int)Tools::getValue('is_virtual')) {
                        if ($id_product_download = ProductDownload::getIdFromIdProduct((int)$product->id)) {
                            $download = new ProductDownload($id_product_download);
                            $download->delete();
                        }
                        $virtual_product_filename = ProductDownload::getNewFilename();
                        $helper = new HelperUploader('virtual_product_file_uploader');
                        $helper->setPostMaxSize(Tools::getOctets(ini_get('upload_max_filesize')))
                        ->setSavePath(_PS_DOWNLOAD_DIR_)->upload($_FILES['virtual_product_file_uploader'], $virtual_product_filename);
                         
                        $download = new ProductDownload();
                        $download->id_product = $product->id;
                        $download->display_filename = $_FILES['virtual_product_file_uploader']['name'];
                        $download->filename =  $virtual_product_filename;
                        $download->date_deposit = date('Y-m-d H:i:s');
                        $download->nb_days_accessible = '30';
                        $download->active = 1;
                        if (!$download->save()) {
                            $errors[] = Tools::displayError('The file has not been added to the product. Contact the administrator.');
                        }
                    }

                    if (!Board::issetProduct($product->id, $seller_id)) {
                        $board = new Board();
                        $board->id_product = $product->id;
                        $board->id_ws_seller = $seller_id;
                        $board->save();
                    }
                    
                    $customer_id = WsSeller::getCustomerId($seller_id);
                    if (!$customer_id) {
                        die('Not seller');
                    }
                    $customer = new Customer($customer_id);
                    
                    $data = array();
                    $data['{seller_name}'] = $customer->firstname.' '.$customer->lastname;
                    $data['{product_name}'] = $product->name;
                    $data['{product_reference}'] = $product->reference;
                    $data['{product_price}'] = $product->price;
                    
                    $iso_lng = Language::getIsoById((int)($this->context->language->id));
                     
                    if (is_dir($this->dir_mails . $iso_lng . '/')) {
                        $id_lang_current = $this->context->language->id;
                    } else {
                        $id_lang_current = Language::getIdByIso('en');
                    }
                    
                    $obj_module = new bulletinboard();
                    $data_translate = $obj_module->translateCustom();
                     
                    Mail::Send(
                        (int)$id_lang_current,
                        'seller_add_product',
                        (version_compare(_PS_VERSION_, '1.7', '>') ? $data_translate['seller_add_product'] :  Mail::l('The seller added a new product', (int)$this->context->language->id)),
                        $data,
                        (string)Configuration::get('PS_SHOP_EMAIL'),
                        $customer->firstname.' '.$customer->lastname,
                        (string)Configuration::get('PS_SHOP_EMAIL'),
                        (string)Configuration::get('PS_SHOP_NAME'),
                        null,
                        null,
                        dirname(__FILE__).'/../../mails/'
                    );
                    
                    Tools::redirect($this->context->link->getModuleLink('bulletinboard', 'boardcustomer', array('added'=>'1',)));
                } else {
                    $errors[] = 'This product is not added.';
                }
            }
            
            $this->context->smarty->assign(array(
                    'form_errors' => $errors
            ));
        }
        
        if (Tools::isSubmit('submitSellerProfile')) {
            $seller = new WsSeller((Tools::getValue('id_ws_seller', null)));
            $seller->name = Tools::getValue('seller_name');
            $seller->payment_acc = Tools::getValue('payment_acc');
            
            $fields = array();
            $languages = Language::getLanguages(false);
            
            foreach ($languages as $lang) {
                $fields['description'][$lang['id_lang']] = Tools::getValue('seller_description_'.$lang['id_lang']);
            }
            
            $seller->description = $fields['description'];
            
            $image_uploader = new HelperImageUploader('image_seller');
            $image_uploader->setAcceptTypes(array('jpeg', 'gif', 'png', 'jpg'))->setMaxSize('734003200');
            $file = $image_uploader->process();
            
            if ($file) {
                // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
                if (isset($file[0]['save_path']) && !ImageManager::checkImageMemoryLimit($file[0]['save_path'])) {
                    $errors[] = Tools::displayError('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ');
                }
                // Copy new image
            
                ImageManager::resize($file[0]['save_path'], _PS_COL_IMG_DIR_.'seller_'.$seller->id_ws_seller.'.jpg', 150, 150);
            }
            $seller->save();
            
            Tools::redirect($this->context->link->getModuleLink('bulletinboard', 'boardcustomer', array('edite_seller'=>'1',)));
        }
        
        if (Tools::isSubmit('submitPayment')) {
            $seller_id = WsSeller::getSellerId($this->context->customer->id);
            $ballance = PaymentBoard::getBallance((int)$seller_id);
            
            $seller = new WsSeller($seller_id);
            
            if (!($summ = Tools::getValue('summ'))) {
                $errors[] = Tools::displayError('Do not enter the amount!');
            } elseif ($summ > $ballance) {
                $errors[] = Tools::displayError('You can not withdraw more than is in the account!');
            }
            if (!count($errors)) {
                $payment = new PaymentBoard();
                $payment->id_ws_seller = $seller_id;
                $payment->summ = - abs((float)$summ);
                $payment->status = 1;
                $payment->description = pSQL(Tools::getValue('message'));
                
                // TODO Payment seller info
                
                $iso_lng = Language::getIsoById((int)($this->context->language->id));
                
                if (is_dir(_PS_MODULE_DIR_.'bulletinboard/mails/'. $iso_lng . '/')) {
                    $id_lang_current = $this->context->language->id;
                } else {
                    $id_lang_current = Language::getIdByIso('en');
                }
                
                $currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
                
                $data = array(
                        '{seller}' => $seller->name,
                        '{customer_id}' => $this->context->customer->id,
                        '{summ}' => Product::convertPriceWithCurrency(array('price' => $summ, 'currency' => $currency), $this->context->smarty),
                        '{description}' => $payment->description
                );
                Mail::Send(
                    (int)$id_lang_current,
                    'payment-request',
                    Mail::l('Withdrawal of rewards ordered', (int)$this->context->language->id),
                    $data,
                    (string)Configuration::get('PS_SHOP_EMAIL'),
                    (string)Configuration::get('PS_SHOP_NAME'),
                    (string)Configuration::get('PS_SHOP_EMAIL'),
                    (string)Configuration::get('PS_SHOP_NAME'),
                    null,
                    null,
                    dirname(__FILE__).'/../../mails/'
                );
                
                if (!$payment->add()) {
                    $errors[] = Tools::displayError('Error in withdrawal query');
                }
                
                Tools::redirect($this->context->link->getModuleLink('bulletinboard', 'boardcustomer', array('add_payment'=>'1',)));
            }
        }
        
        parent::postProcess();
    }
}
