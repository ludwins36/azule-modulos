<?php 
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $oList ML_Productlist_Model_ProductList_Abstract */
    /* @var $oProduct ML_Shop_Model_Product_Abstract */
    class_exists('ML',false) or die();
?>
<?php 
if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) { 
    $sQuantity = '';
    if ($oProduct->isSingle() || $oProduct->get('parentid') > 0) {
        $sQuantity = $this->getStock($oProduct);
    } else {
         $sQuantity = '&mdash;';
    }
    echo $sQuantity;
}
