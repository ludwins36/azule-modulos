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

if (!class_exists('Board')) {
    require_once(_PS_MODULE_DIR_.'bulletinboard/classes/Board.php');
}

class PaymentBoard extends ObjectModel
{
    public $id_payment;
    
    public $id_product;
    
    public $id_order;

    public $id_ws_seller;

    public $summ;

    public $description;

    public $status = 0;
    public $date_add;

    public $date_upd;

    public static $definition = array(
            'table' => 'ws_seller_payment',
            'primary' => 'id_payment',
            'fields' => array(
                    'id_product' =>         array('type' => 1, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
                    'id_order' =>                   array('type' => 1, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
                    'summ' =>                       array('type' => 1, 'required' => true, 'copy_post' => false),
                    'description' =>         array('type' => 3, 'copy_post' => true),
                    'status' =>             array('type' => 1, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
                    'id_ws_seller' =>             array('type' => 1, 'validate' => 'isUnsignedId', 'required' => true, 'size' => 32),
                    'date_add' =>             array('type' => 5, 'validate' => 'isDateFormat', 'copy_post' => false),
                    'date_upd' =>             array('type' => 5, 'validate' => 'isDateFormat', 'copy_post' => false)),
    );
    protected $fieldsRequired = array('id_ws_seller','summ');
    protected $fieldsSize = array('description' => 255);
    protected $fieldsValidate = array('id_product' => 'isUnsignedId',
            'id_order' => 'isUnsignedId','id_ws_seller' => 'isUnsignedId','summ' => 'isFloat','description' => 'isGenericName');


    protected $table = 'ws_seller_payment';
    protected $identifier = 'id_payment';
    public $def_currency_sign;

    public function getFields()
    {
        $fields = array();
        parent::validateFields();
        if (isset($this->id)) {
            $fields['id_payment'] = (int)$this->id;
        }
        $fields['id_product'] = (int)$this->id_product;
        $fields['id_order'] = (int)$this->id_order;
        $fields['id_ws_seller'] = (int)$this->id_ws_seller;
        $fields['summ'] = (float)$this->summ;
        $fields['description'] = pSQL($this->description);
        $fields['status'] = (int)$this->status;
        $fields['date_add'] = pSQL($this->date_add);
        $fields['date_upd'] = pSQL($this->date_upd);
        return $fields;
    }
    
    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }
    
    public static function deletePaymentByOrderIdAndProductId($i_order_id = null, $i_product_id = null)
    {
        if (!$i_order_id || !$i_product_id) {
            return false;
        }
        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ws_seller_payment` WHERE `id_order` = '.(int)$i_order_id.' AND `id_product` = '.(int)$i_product_id);
    }
    
    public static function paymentExists($id_order, $id_product)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS(
            'SELECT *
        FROM `'._DB_PREFIX_.'ws_seller_payment`
        WHERE `id_order` = '.(int)$id_order.
            ' AND `id_product` = '.(int)$id_product.' AND `status` != 5 '
        );
    }
    
    public static function getBallance($id_ws_seller)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT SUM(summ)
        FROM `'._DB_PREFIX_.'ws_seller_payment`
        WHERE `id_ws_seller` = "'.(int)$id_ws_seller.'"');
    }
    
    public static function getPayments($id_ws_seller)
    {
        if ($id_ws_seller) {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT sp.*
            FROM `'._DB_PREFIX_.'ws_seller_payment` sp
            LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = sp.`id_product`
            LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = sp.`id_order`
            WHERE sp.`id_ws_seller` = '.$id_ws_seller.' AND (o.`valid` = 1 OR sp.`id_product` = 0 OR o.`module` = \'internalpay\')
            ORDER BY sp.`date_upd` DESC');
        }
        
        return array();
    }
    
    public static function getAllPayments()
    {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT sp.id_payment, sp.id_ws_seller,
                    sp.summ, 
                    sp.description,  
                    sp.date_add,
            CASE sp.status WHEN 0 THEN "Sale" WHEN 1 THEN "Query" WHEN 2 THEN "Payment" WHEN 5 THEN "Refund" END as status
            FROM `'._DB_PREFIX_.'ws_seller_payment` sp
            WHERE 1
            ORDER BY sp.`date_upd` DESC');
    }
}
