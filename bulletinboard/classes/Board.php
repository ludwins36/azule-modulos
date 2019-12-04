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

class Board extends ObjectModel
{
    public $id_ws_seller;
    public $id_product;
    
    public static $definition = array(
            'table'          => 'ws_seller_product',
            'primary'        => 'id_ws_seller',
            'fields'         => array(
                    'id_ws_seller'        => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                    'id_product'        => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            ),
    );
    

    public static function issetProduct($id_product, $id_ws_seller = false)
    {
        $result = Db::getInstance()->getRow('SELECT wsp.`id_product`
        FROM '._DB_PREFIX_.'ws_seller_product wsp
        WHERE wsp.`id_product` = '.(int)$id_product.($id_ws_seller ? ' AND wsp.`id_ws_seller` = '.(int)$id_ws_seller : ''));
        return (is_array($result) && count($result) ? true : false);
    }
    
    public static function getSellerByProduct($id_product)
    {
        $result = Db::getInstance()->getValue('SELECT wsp.`id_ws_seller`
        FROM '._DB_PREFIX_.'ws_seller_product wsp
        WHERE wsp.`id_product` = '.(int)$id_product);
        return $result;
    }
    
    public static function getAllProductsBySeller($id_seller, $start = 0, $step = 6)
    {
        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            $prod_arr = Db::getInstance()->executeS('SELECT
            p.*,
            pl.`name`,
            pl.`description_short`,
            pl.`link_rewrite`,
            pl.`available_now`, pl.`available_later`,
            cl.`name` AS category_default,
            IFNULL(p.quantity, 0) as quantity'
                    .(Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.`id_product_attribute`,0) id_product_attribute' : '').',
            (SELECT i.`id_image` FROM '._DB_PREFIX_.'image i WHERE i.`id_product` = p.`id_product` ORDER BY i.`cover` DESC LIMIT 0,1) as image
            FROM `'._DB_PREFIX_.'product` p '.
                    (Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
            ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)Context::getContext()->shop->id.')':'').'
        LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)Context::getContext()->language->id.') 
                    LEFT JOIN `'._DB_PREFIX_.'ws_seller_product` wsp ON p.`id_product` = wsp.`id_product`
                    LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
                    ON (p.`id_category_default` = cl.`id_category`
                    AND cl.`id_lang` = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('cl').')
                WHERE wsp.`id_ws_seller` = '.(int)$id_seller.' AND p.`active` = 1  GROUP BY p.`id_product` ORDER BY p.`date_upd` DESC LIMIT '.(int)($start).' ,'.(int)($step));
        } elseif (version_compare(_PS_VERSION_, '1.6.1.0', '>')) {
            $prod_arr = Db::getInstance()->executeS('SELECT
            p.*,
            pl.`name`,
            pl.`description_short`,
            pl.`link_rewrite`,
            pl.`available_now`, pl.`available_later`,
            cl.`name` AS category_default,
            IFNULL(p.quantity, 0) as quantity'
                    .(Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.`id_product_attribute`,0) id_product_attribute' : '').',
            (SELECT i.`id_image` FROM '._DB_PREFIX_.'image i WHERE i.`id_product` = p.`id_product` ORDER BY i.`cover` DESC LIMIT 0,1) as id_image
            FROM `'._DB_PREFIX_.'product` p '.
                    (Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
            ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)Context::getContext()->shop->id.')':'').'
        LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)Context::getContext()->language->id.')
                    LEFT JOIN `'._DB_PREFIX_.'ws_seller_product` wsp ON p.`id_product` = wsp.`id_product`
                    LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
                    ON (p.`id_category_default` = cl.`id_category`
                    AND cl.`id_lang` = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('cl').')
                WHERE wsp.`id_ws_seller` = '.(int)$id_seller.' AND p.`active` = 1  GROUP BY p.`id_product` ORDER BY p.`date_upd` DESC LIMIT '.(int)($start).' ,'.(int)($step));
        } else {
            $prod_arr = Db::getInstance()->executeS('SELECT
            p.*,
            pl.`name`,
            pl.`description_short`,
            pl.`link_rewrite`,
            pl.`available_now`, pl.`available_later`,
            cl.`name` AS category_default,
            IFNULL(p.quantity, 0) as quantity,
            (SELECT i.`id_image` FROM '._DB_PREFIX_.'image i WHERE i.`id_product` = p.`id_product` ORDER BY i.`cover`) as id_image
        FROM '._DB_PREFIX_.'ws_seller_product wsp
        LEFT JOIN '._DB_PREFIX_.'product p ON p.`id_product` = wsp.`id_product`
        LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)Context::getContext()->language->id.')
        LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
                    ON (p.`id_category_default` = cl.`id_category`
                    AND cl.`id_lang` = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('cl').')
                WHERE wsp.`id_ws_seller` = '.(int)$id_seller.' AND p.`active` = 1');
        }

        return Product::getProductsProperties(Context::getContext()->language->id, $prod_arr);
    }
    
    public static function getProductsForSeller($id_seller, $start = 1, $step = 15)
    {
        $sql = 'SELECT 
                p.*, 
                pl.`link_rewrite`, pl.`name`, pl.`available_now`, pl.`available_later`, 
                SUM(od.`product_quantity`) as sold_q,
                (SELECT i.`id_image` FROM '._DB_PREFIX_.'image i WHERE i.`id_product` = p.`id_product` ORDER BY i.`cover` DESC LIMIT 0,1) as image,
            SUM(od.`product_quantity`*od.`product_price`/od.`conversion_rate`) as sold_total
            FROM `'._DB_PREFIX_.'product` p
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)Context::getContext()->language->id.')
            LEFT JOIN `'._DB_PREFIX_.'ws_seller_product` wsp ON p.`id_product` = wsp.`id_product`
            LEFT JOIN (SELECT odt.`product_quantity`, odt.`product_price`, odt.`product_id`, o.`conversion_rate`
            FROM `'._DB_PREFIX_.'order_detail` odt LEFT JOIN `'._DB_PREFIX_.'orders` o ON odt.`id_order` = o.`id_order`
            WHERE o.`valid` = 1) od ON p.`id_product` = od.`product_id`
            WHERE wsp.`id_ws_seller` = '.(int)$id_seller.
                 ' GROUP BY p.`id_product` ORDER BY p.`date_upd` DESC 
                 LIMIT '.(int)($start).' ,'.(int)($step);
        
        $prod_arr = Db::getInstance()->executeS($sql);
    
        return Product::getProductsProperties(Context::getContext()->language->id, $prod_arr);
    }

    public static function getCountProducts($id_seller)
    {
        $sql = 'SELECT COUNT(`id_product`)
            FROM `'._DB_PREFIX_.'ws_seller_product` 
            WHERE `id_ws_seller` = '.(int)$id_seller;
    
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
    
    public static function deleteProduct($id_product)
    {
        Db::getInstance()->delete('ws_seller_product', 'id_product = '.(int)$id_product);
    }
    
    public static function pagingProducts($start, $step, $count, $link)
    {
        $res = '';
        $rp = '';
        $pages = array();
        $start1 = $start;
        for ($start1 = ($start - $step*4 >= 0 ? $start - $step*4 : 0); $start1 < ($start + $step*5 < $count ? $start + $step*5 : $count); $start1 += $step) {
            $par = (int)($start1 / $step) + 1;
            if ($start1 == $start) {
                $pages[$par]["page"] = $par;
                $pages[$par]["type"] = "current";
                //$res .= '<b>'. $par .'</b>';
            } else {
                $pages[$par]["page"] = $par;
                $pages[$par]["type"] = false;
                if ($start1) {
                    if (strripos($link, '?')) {
                        $rp = '&';
                    } else {
                        $rp = '?';
                    }
                    $rp .= 'p='.$par;
                }
                $pages[$par]["rp"] = $rp;
    
    
                $res .= '<a href="'.$link.$rp.'" title="'.$par.'">'.$par.'</a>';
            }
        }
    
        return $pages;
    }
}
