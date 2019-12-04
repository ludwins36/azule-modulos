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

if (!defined('_PS_VERSION_'))
    exit;

class hardevelmulticurrency extends Module
{
    public $id_shop;
    public $shop; //version bigger than 1.4


    public function __construct()
    {
        $this->name = 'hardevelmulticurrency';
        $this->tab = 'content_management';
        $this->version = '1.0.21';
        $this->author = 'HarDevel';
        $this->module_key = '92826760fb1d3bd6d556ca16eb497967';
        $this->ps_versions_compliancy = array('min' => '1.4', 'max' => '1.7.5.99');

        parent::__construct();

        $this->displayName = $this->l('Multi currency for products in backoffice');
        $this->description = $this->l('Different currencies');

        if (_PS_VERSION_ < '1.5'){
            require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
            $this->shop = false;
            $this->id_shop = 1;

        }else{
            $this->shop = true;
            $this->id_shop = $this->context->shop->id;
            $this->id_hc_shop = Shop::getContext() == Shop::CONTEXT_ALL ? 0 : $this->context->shop->id;
        }

    }

    public function install()
    {
        
        return $this->installOverrides14()
        && $this->installModuleTab('AdminProductCurrencies', array(Configuration::get('PS_LANG_DEFAULT') => 'Multi Currency'), Tab::getIdFromClassName('AdminCatalog'))
        && $this->installDb()
        && parent::install();

    }

    public function uninstall()
    {
        return self::uninstallDb() && self::uninstallModuleTab() && self::uninstallOverride() && parent::uninstall();
    }

    public function uninstallDb()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS `'._DB_PREFIX_.'hardevel_multicurrency`
        ');
    }
    
    public function uninstallOverride()
    {
        return true;
    }
    
    public function uninstallModuleTab()
    {
        $id_tab = Db::getInstance()->getValue('SELECT id_tab FROM `'._DB_PREFIX_.'tab` WHERE module="hardevelmulticurrency"');
        $tab = new Tab($id_tab);
        return $tab->delete();

    }

    private function installDb()
    {
        return Db::getInstance()->execute('
           
        
        CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'hardevel_multicurrency` (
          `id_shop` int(10) unsigned NOT NULL,
          `id_product` int(10) unsigned NOT NULL,
          `id_product_attribute` int(11) NOT NULL DEFAULT \'0\',
          `id_currency` int(10) unsigned NOT NULL,
          `price` float unsigned NOT NULL,
          `wholesale_price` float unsigned NOT NULL,
          `id_wholesale_currency` int(10) unsigned NOT NULL,
          `reduction` float NOT NULL,
          `reduction_type` varchar(10) NOT NULL,
          `id_reduction_currency` int(11) NOT NULL
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        
        
        ALTER TABLE `'._DB_PREFIX_.'hardevel_multicurrency`
         ADD PRIMARY KEY (`id_shop`,`id_product`,`id_product_attribute`);

        ');
    }

    private function installModuleTab($tabClass, $tabName, $idTabParent)
    {
        if(!$this->shop){
            if (!is_dir(dirname(__FILE__).'/tabs'))
                return true;

            $path = dirname(__FILE__).'/tabs/';

            foreach(scandir($path) as $file){
                if ($file != '.' && $file != '..' && $file!='index.php'){
                    if(is_file(_PS_ADMIN_DIR_.'/tabs/'.$file)){

                        return false;
                    }else{
                        if(!copy($path.$file,_PS_ADMIN_DIR_.'/tabs/'.$file))
                            return false;
                    }
                }
            }
        }

        $tab = new Tab();
        $tab->name = $tabName;
        $tab->class_name = $tabClass;
        $tab->module = $this->name;
        $tab->id_parent = $idTabParent;

        if(!$tab->save()){
            return false;
        }

        return true;
    }


    public function getContent()
    {
        $id_employee = (int)$this->context->employee->id;
        $time_generated = time();
        $crypt_token = md5($id_employee . _COOKIE_KEY_ . $time_generated);
        $secure =  'id_shop=' . (Shop::getContext() == 4 ? 0 : $this->context->shop->id).'&crypt_token='.$crypt_token . '&id_employee='.$id_employee.'&time_generated='.$time_generated;

        $categories = Category::getCategories($this->context->language->id);
        
        $categories_select = $this->recurseCategory($categories, $categories, 1);
        
        $manufacturers = Manufacturer::getManufacturers();
        $suppliers = Supplier::getSuppliers();
        $this->smarty->assign(array(
            'selected' => 1,
            'secure' => $secure,
            'categories' => $categories_select,
            'manufacturers' => $manufacturers,
            'suppliers' => $suppliers,
            'path' => $this->_path,

        ));

        return $this->display(__FILE__,'views/templates/hook/hardevelmulticurrency.tpl');
    }

    public function recurseCategory($categories, $current, $id_category = 1)
    {
        $new_categories = array();
        if(isset($current['infos']['name'])){
            $current['infos']['name'] = str_repeat(' ', $current['infos']['level_depth'] * 5).Tools::stripslashes($current['infos']['name']);
            $new_categories[] =  $current['infos'];
            
        }
        if(isset($categories[$id_category]))
            foreach (array_keys($categories[$id_category]) as $key)
                    $new_categories = array_merge($new_categories,$this->recurseCategory($categories, $categories[$id_category][$key], $key));
        return $new_categories;
    }

    public function ajax()
    {
        $id_employee = (int)Tools::getValue('id_employee');
        $time_generated = (int)Tools::getValue('time_generated');
        $crypt_token = md5($id_employee . _COOKIE_KEY_ . $time_generated);

        if(Tools::getValue('crypt_token') != $crypt_token) {
            return array('error'=>$this->l('error'));
        }else{
            $this->employee = new Employee((int)Tools::getValue('id_employee'));
            $this->id_lang = $this->employee->id_lang;

        }
        if(Tools::getIsset('filter'))
            return $this->getProducts();
        elseif(Tools::getIsset('save'))
            return $this->saveProducts();
        else
            return array('error'=>$this->l('error'));
    }

    private function getProducts()
    {

        if(empty($_POST) || (Tools::getValue('id_manufacturer')=="0" && Tools::getValue('id_supplier')=="0" && (int)Tools::getValue('id_category')<2))
            return array('error' => $this->l('Empty filter'));
        
        
        $id_manufacturer = (int)Tools::getValue('id_manufacturer');
        $id_supplier = (int)Tools::getValue('id_supplier');
        $id_category = ((int)Tools::getValue('id_category')<2 ? 0 : (int)Tools::getValue('id_category'));
        $active = Tools::getIsset('active');
        $combinations = Tools::getIsset('combinations');
        
        $final_sql = '(SELECT p.`id_product`,"0" as `id_product_attribute`, pl.`name` COLLATE utf8_general_ci as `name` ,i.`id_image`,p.`reference`, pl.`link_rewrite`, hc.`price` as hh_price, hc.`wholesale_price` as hh_wholesale_price, hc.`id_currency` as hh_id_currency, hc.`id_wholesale_currency` as hh_id_wholesale_currency, hc.`reduction` as `hh_reduction`, hc.`reduction_type` as `hh_reduction_type`, hc.`id_reduction_currency` as `id_reduction_currency`, sp.`reduction`,  sp.`reduction_type`,'
            .($this->shop ? ' ps.`price` as p_price' : ' p.`price` as `p_price`')
            .($this->shop ? ', ps.`wholesale_price` as p_wholesale_price' : ', p.`wholesale_price` as `p_wholesale_price`').
            '        
			FROM `'._DB_PREFIX_.'product` p'
            .($this->shop ? ' LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.`id_product`=p.`id_product`) ' : '').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang`='.pSQL($this->context->language->id).')
                LEFT JOIN `'._DB_PREFIX_.'image` i	ON (i.`id_product` = p.`id_product` AND i.`cover`=1)'
            .($id_category ? ' LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product`=p.`id_product`)' : '').
            
            'LEFT JOIN `'._DB_PREFIX_.'hardevel_multicurrency` hc ON (hc.`id_shop` = '.pSQL($this->id_hc_shop).' AND hc.`id_product` = p.`id_product` AND  hc.`id_product_attribute`=0) 
            LEFT JOIN `'._DB_PREFIX_.'specific_price` sp ON ('.($this->shop? ' (sp.`id_shop` = '.pSQL($this->id_shop).' OR sp.`id_shop` = 0)  AND ' : ' ' ). ' sp.`id_product` = p.`id_product` AND sp.`id_product_attribute`=0)'.
            
            'WHERE 1 '
            .($this->shop ? ' AND ps.`id_shop` = '.(int)$this->id_shop : '')
            .($id_manufacturer ? ' AND p.`id_manufacturer`='.(int)$id_manufacturer : '')
            .($id_supplier ? ' AND p.`id_supplier` = '.(int)$id_supplier : '')
            .($id_category ? ' AND cp.`id_category` = '.(int)$id_category : '')
            .($active ? ' AND p.`active` = 1' : '')
            .' ) ';

        
        
            
        $sql_combinations = '(SELECT p.`id_product`, pa.`id_product_attribute`, GROUP_CONCAT(al.`name` SEPARATOR " ") COLLATE utf8_general_ci as `name`,i.`id_image`, pa.`reference`, pl.`link_rewrite`, hc.`price` as hh_price, hc.`wholesale_price` as hh_wholesale_price, hc.`id_currency` as hh_id_currency, hc.`id_wholesale_currency` as hh_id_wholesale_currency, hc.`reduction` as `hh_reduction`, hc.`reduction_type` as `hh_reduction_type`, hc.`id_reduction_currency` as `id_reduction_currency`, sp.`reduction`, sp.`reduction_type`,'
            .($this->shop ? 'pas.`price` as `p_price`' : 'pa.`price` as `p_price`') 
            .($this->shop ? ', pas.`wholesale_price` as p_wholesale_price' : ', pa.`wholesale_price` as `p_wholesale_price`').
             ' 
                    
			     
			FROM `'._DB_PREFIX_.'product` p'
            
            .($this->shop ? ' LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.`id_product`=p.`id_product`) ' : '').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang`='.(int)$this->context->language->id.' AND pl.`id_shop`='.(int)$this->id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'image` i	ON (i.`id_product` = p.`id_product` AND i.`cover`=1)'
            .($id_category ? ' LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product`=p.`id_product`)' : '').
            
            ' LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.`id_product` = p.`id_product`)'
                .($this->shop ? '
                        LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                        LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pas.`id_product_attribute` = pa.`id_product_attribute` AND pas.`id_shop`='.(int)$this->id_shop.')
                    ' : '
                        LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                    ').
            ' LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (pac.`id_attribute` = al.`id_attribute` AND al.`id_lang`='.(int)$this->context->language->id.')        
              LEFT JOIN `'._DB_PREFIX_.'hardevel_multicurrency` hc ON (hc.`id_shop` = '.(int)$this->id_hc_shop.' AND hc.`id_product` = p.`id_product` AND  hc.`id_product_attribute`=pa.`id_product_attribute`) 
              LEFT JOIN `'._DB_PREFIX_.'specific_price` sp ON ('.($this->shop ? ' (sp.`id_shop` = '.(int)$this->id_shop.' OR sp.`id_shop` = 0) AND ' : ' ' ). 'sp.`id_product` = p.`id_product` AND sp.`id_product_attribute`=pa.`id_product_attribute`)'.
            
            ' WHERE 1 AND pa.`id_product_attribute` IS NOT NULL '
            .($this->shop ? ' AND ps.`id_shop` = '.(int)$this->id_shop : '')
            .($id_manufacturer ? ' AND p.`id_manufacturer`='.(int)$id_manufacturer : '')
            .($id_supplier ? ' AND p.`id_supplier` = '.(int)$id_supplier : '')
            .($id_category ? ' AND cp.`id_category` = '.(int)$id_category : '')
            .($active ? ' AND p.`active` = 1' : '')
            .' GROUP BY p.`id_product`, pa.`id_product_attribute` ) ';    
        
        if($combinations)
            $final_sql = ''.$final_sql . ' UNION '.$sql_combinations.' ORDER BY id_product, `id_product_attribute`';

        $products = Db::getInstance()->ExecuteS($final_sql);
        if(!$products || empty($products))
            return array('error'=>$this->l('There is no products'));

        $image_type = Db::getInstance()->getValue('SELECT name FROM `'._DB_PREFIX_.'image_type` WHERE products=1 ORDER BY width ASC');
        $result = array();
        
        foreach($products as $product){
            $elem=array(
                    'id_product'=>$product['id_product'],
                    'id_product_attribute'=> $product['id_product_attribute'],
                    'name' => $product['name'],
                    'reference'=>$product['reference'],
                    'image'=>$this->context->link->getImageLink($product['link_rewrite'],$product['id_product'].'-'.$product['id_image'],$image_type),
                    
                    'wholesale_price'=>($product['hh_wholesale_price'] ? $product['hh_wholesale_price'] : $product['p_wholesale_price']),
                    'id_wholesale_currency'=>($product['hh_id_wholesale_currency'] ? $product['hh_id_wholesale_currency'] : 0),
                    'price'=>($product['hh_price']!='' ? $product['hh_price'] : $product['p_price']),
                    'id_currency'=>($product['hh_id_currency'] ? $product['hh_id_currency'] : 0),
                    'reduction_type'=>($product['hh_reduction_type'] ? $product['hh_reduction_type'] : $product['reduction_type']),
                    'reduction'=>($product['hh_reduction'] ? $product['hh_reduction'] : $product['reduction']),
                    'id_reduction_currency' => $product['id_reduction_currency'],
                    
                );
                
                if(is_null($elem['reduction']))
                    $elem['reduction'] = 0;
                if($elem['reduction_type'] == 'percentage')
                    $elem['reduction'] *= 100;
                $result[] = $elem;
            }

        return array(
            'currencies'=>Currency::getCurrencies(false,false),
            'products'=>$result,
            'default_currency'=>(int)Configuration::get('PS_CURRENCY_DEFAULT')
        );


    }

    private function saveProducts()
    {
        $sql = '';
        $products = array();
        foreach(Tools::getValue('products') as $id_product=>$combinations){
            $sql .= 'DELETE FROM `'._DB_PREFIX_.'hardevel_multicurrency` WHERE id_product='.(int)$id_product.' AND id_shop='.(int)$this->id_hc_shop.';'.PHP_EOL;
            $id_currency = 0;
            $id_wholesale_currency = 0;
            foreach($combinations as $id_product_attribute=>$array){
                if(isset($array['id_currency']))
                    $id_currency = (int)$array['id_currency'];
                if(isset($array['id_wholesale_currency']))
                    $id_wholesale_currency = (int)$array['id_wholesale_currency'];
    
                $sql .= 'INSERT INTO `'._DB_PREFIX_.'hardevel_multicurrency` (`id_shop`,`id_product`,`id_product_attribute`,`id_currency`,`price`,`wholesale_price`,`id_wholesale_currency`,`reduction`,`reduction_type`,`id_reduction_currency`)
                VALUES ('.(int)$this->id_hc_shop.',
                 '.(int)$id_product.' ,
                 '.(int)$id_product_attribute.',
                 '.(int)$id_currency.',
                 "'.(float)($array['price']).'",
                 "'.(float)($array['wholesale_price']).'",
                 '.(int)$id_wholesale_currency.',
                 '.(isset($array['reduction']) ? ($array['reduction_type']=='percentage' ? (float)$array['reduction']/100 : (float)$array['reduction']) : 0 ).',
                 "'.(isset($array['reduction']) && $array['reduction']!=0 ? $array['reduction_type'] : 'non').'",
                 '.(isset($array['id_reduction_currency']) ? (int)$array['id_reduction_currency'] : (int)$id_currency).');'.PHP_EOL;

            }

            $products[]=$id_product;
        }

        if($sql ==  '')
            return;
        
        $this->sendMultipleQueries($sql);

        
        if(!empty($products))
            $this->updatePrices($products);

        return array('ok'=>$this->l('Saved'));

    }

    public function updatePrices($products=array(), $id_currency=0)
    {
        if(!empty($products)){
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'hardevel_multicurrency` WHERE id_product IN ('.implode(",",$products).') ORDER BY id_shop';
        
        }elseif($id_currency != 0){
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'hardevel_multicurrency` WHERE id_currency='.(int)$id_currency . ' ORDER BY id_shop';
        }else{
            return true;
        }
        
        $result = Db::getInstance()->ExecuteS($sql);
        $sql = '';
        $sql_specific = '';
        if($result && count($result)>0){
            $currencies_temp = Currency::getCurrencies(false,false);
            $currencies=array();

            foreach($currencies_temp as $currency){
                $currencies[$currency['id_currency']]=$currency;
            }
            foreach($result as $product){
                
                if($product['id_shop'] == 0){
                    $shops = Db::getInstance()->executeS('SELECT id_shop FROM '._DB_PREFIX_.'shop');    
                }else{
                    $shops = array(array('id_shop' => $product['id_shop']));
                }
                foreach($shops as $shop){
                    $id_shop = $shop['id_shop'];
                    
                    $price = $product['price']/$currencies[$product['id_currency']]['conversion_rate'];
                    $wholesale_price = $product['wholesale_price']/$currencies[$product['id_wholesale_currency']]['conversion_rate'];
                    if($product['id_product_attribute'] == 0){
                        if ($product['reduction_type'] != ''){
                           $sql_specific .= ' DELETE FROM `'._DB_PREFIX_.'specific_price` WHERE `id_product` = '. (int)$product['id_product'] . ' AND `id_product_attribute` = '. (int)$product['id_product_attribute'] . ($this->shop ? ' AND (`id_shop` = 0 OR id_shop = '.(int)$id_shop .' ) ' : '').';';
                           
                           if($product['reduction_type'] != 'non'){
                                if($product['reduction_type'] == 'amount')
                                    $reduction = $product['reduction']/$currencies[$product['id_reduction_currency']]['conversion_rate'];
                                else
                                    $reduction = $product['reduction'];
                                if($this->shop)
                                    $sql_specific .= ' INSERT INTO `'._DB_PREFIX_.'specific_price` (`id_shop`,`id_product`,`id_product_attribute`,`reduction`,`reduction_type`,`price`,`from_quantity`) 
                                        VALUES(
                                        '.(int)$id_shop.',
                                        '.(int)$product['id_product'].',
                                        '.(int)$product['id_product_attribute'].',
                                        '.(float)($reduction).',
                                        "'.pSQL($product['reduction_type']).'",
                                        -1.0,
                                        1
                                        );';
                                else
                                    $sql_specific .= ' INSERT INTO `'._DB_PREFIX_.'specific_price` (`id_product`,`id_product_attribute`,`reduction`,`reduction_type`,`price`,`from_quantity`) 
                                        VALUES(
                                        '.(int)$product['id_product'].',
                                        '.(int)$product['id_product_attribute'].',
                                        '.(float)($reduction).',
                                        "'.pSQL($product['reduction_type']).'",
                                        -1.0,
                                        1
                                        );';
                           }                      
                        }
                        
                    }
                    $sql_part = 'price='.pSQL($price).', wholesale_price='.pSQL($wholesale_price).'';     
                    if($this->shop){
                        if($product['id_product_attribute']=="0"){
                            $sql .= 'UPDATE `'._DB_PREFIX_.'product_shop` SET '.pSQL($sql_part).' WHERE id_product='.(int)$product['id_product'].' AND id_shop='.(int)$id_shop.';'.PHP_EOL;
                        }else{
                            $sql .= 'UPDATE `'._DB_PREFIX_.'product_attribute_shop` SET '.pSQL($sql_part).' WHERE id_product_attribute='.(int)$product['id_product_attribute'].' AND id_shop='.(int)$id_shop.';'.PHP_EOL;
                        }
                    }else{
                        if($product['id_product_attribute']=="0"){
                            $sql .= 'UPDATE `'._DB_PREFIX_.'product` SET '.pSQL($sql_part).' WHERE id_product='.(int)$product['id_product'].';'.PHP_EOL;
                        }else{
                            $sql .= 'UPDATE `'._DB_PREFIX_.'product_attribute` SET '.pSQL($sql_part).' WHERE id_product_attribute='.(int)$product['id_product_attribute'].';'.PHP_EOL;
                        }
                    }
                }
                
                    
             
                

            }
        }   
        $sql = $sql . $sql_specific;
        
        if($sql != ''){
            
            $this->sendMultipleQueries($sql);
            Tools::clearSmartyCache();
            Tools::clearXMLCache();
            Media::clearCache();
            Tools::generateIndex();
        }
            

        return true;

    }

    public function sendMultipleQueries($sql)
    {

        $sqls = explode(";",$sql);
            $result = true;
            
            foreach($sqls as $sql){
                if(trim($sql)){
                    $result &= Db::getInstance()->execute($sql);
                }
                    
            }
            return $result;
    }

    public function installOverrides14()
    {
        if($this->shop)
            return true;

        $path = dirname(__FILE__).'/override/classes/';

        foreach(scandir($path) as $file){
            if ($file != '.' && $file != '..'){
                if(is_file(_PS_ROOT_DIR_.'/override/classes/'.$file)){

                    return false;
                }else{
                    return copy($path.$file,_PS_ROOT_DIR_.'/override/classes/'.$file);
                }
            }
        }
        return true;
    }

}