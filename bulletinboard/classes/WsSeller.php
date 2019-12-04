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

if (!class_exists('PaymentBoard')) {
    require_once(_PS_MODULE_DIR_.'bulletinboard/classes/PaymentBoard.php');
}

class WsSeller extends ObjectModel
{
    public $id;
    
    /** @var int seller ID */
    public $id_ws_seller;
    
    public $id_customer;
    
    /** @var string Name */
    public $name;
    
    /** @var string Payment Method */
    public $payment_acc;
    
    /** @var string A description */
    public $description;
    
    /** @var string A short description */
    public $short_description;
    
    /** @var int Address */
    public $id_address;
    
    /** @var string Object creation date */
    public $date_add;
    
    /** @var string Object last modification date */
    public $date_upd;
    
    /** @var string Friendly URL */
    public $link_rewrite;
    
    /** @var string Meta title */
    public $meta_title;
    
    /** @var string Meta keywords */
    public $meta_keywords;
    
    /** @var string Meta description */
    public $meta_description;
    
    /** @var bool active */
    public $active;
    
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
            'table' => 'ws_seller',
            'primary' => 'id_ws_seller',
            'multilang' => true,
            'fields' => array(
                    'id_customer'        => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                    'name' =>                array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'required' => true, 'size' => 64),
                    'payment_acc'=>        array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'size' => 64),
                    'active' =>            array('type' => self::TYPE_BOOL),
                    'date_add' =>            array('type' => self::TYPE_DATE),
                    'date_upd' =>            array('type' => self::TYPE_DATE),
    
                    /* Lang fields */
                    'description' =>        array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
                    'short_description' =>    array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
                    'meta_title' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128),
                    'meta_description' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
                    'meta_keywords' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
            ),
    );
    
    protected $webserviceParameters = array(
            'fields' => array(
                    'active' => array(),
                    'link_rewrite' => array('getter' => 'getLink', 'setter' => false),
            ),
            'associations' => array(
                    'addresses' => array('resource' => 'address', 'setter' => false, 'fields' => array(
                            'id' => array('xlink_resource' => 'addresses'),
                    )),
            ),
    );
    
    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    
        $this->link_rewrite = Tools::link_rewrite($this->name);
        $this->image_dir = _PS_MANU_IMG_DIR_;
    }
    
    public function delete()
    {
        $address = new Address($this->id_address);
    
        if (Validate::isLoadedObject($address) && !$address->delete()) {
            return false;
        }
    
        if (parent::delete()) {
            CartRule::cleanProductRuleIntegrity('ws_seller', $this->id);
            return $this->deleteImage();
        }
    }
    
    public static function deleteByIdSeller($id_seller)
    {
        $sql = 'SELECT `id_product` FROM  `'._DB_PREFIX_.'ws_seller_product`
            WHERE `id_ws_seller` = '.(int)$id_seller;
        $prod_arr = Db::getInstance()->executeS($sql);
        
        if ($prod_arr) {
            foreach ($prod_arr as $row) {
                $p = new Product($row['id_product']);
                $p->delete();
            }
        }
        
        Db::getInstance()->delete('ws_seller', 'id_ws_seller = '.(int)$id_seller);
        Db::getInstance()->delete('ws_seller_lang', 'id_ws_seller = '.(int)$id_seller);
        Db::getInstance()->delete('ws_seller_product', 'id_ws_seller = '.(int)$id_seller);
        Db::getInstance()->delete('ws_seller_payment', 'id_ws_seller = '.(int)$id_seller);
    }
    
    public static function getSellerId($user_id)
    {
        $query = 'SELECT `id_ws_seller` FROM `'._DB_PREFIX_."ws_seller` WHERE `id_customer` = '{$user_id}'";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
    
    public static function getCustomerId($id_ws_seller = null)
    {
        $query = 'SELECT `id_customer` FROM `'._DB_PREFIX_."ws_seller` WHERE `id_ws_seller` = '{$id_ws_seller}'";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
    
    public static function getProductCount($id_ws_seller = null)
    {
        $query = 'SELECT COUNT(`id_product`) FROM `'._DB_PREFIX_."ws_seller_product` WHERE `id_ws_seller` = '{$id_ws_seller}'";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
    
    public static function getSellersList()
    {
        $query = 'SELECT ws.`id_ws_seller`, ws.`id_ws_seller` as `img_seller`, ws.`id_customer`, ws.`name`, ws.`payment_acc`, 
                CONCAT(c.`firstname`, \' \', c.`lastname`) AS customer, c.`email`, 
                ws.active   
            FROM `'._DB_PREFIX_.'ws_seller` AS ws
            LEFT JOIN `'._DB_PREFIX_.'customer` AS c ON (c.id_customer=ws.id_customer)
            ';
        $sellers =  Db::getInstance()->ExecuteS($query);
        
        foreach ($sellers as $key => $value) {
            $sellers[$key]['balance'] = PaymentBoard::getBallance($sellers[$key]['id_ws_seller']);
            $sellers[$key]['product_count'] = self::getProductCount($sellers[$key]['id_ws_seller']);
        }
        return $sellers;
    }
}
