<?php 
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $oList ML_Productlist_Model_ProductList_Abstract */
    /* @var $oProduct ML_Shop_Model_Product_Abstract */
    class_exists('ML',false) or die();
?>
<?php if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) {
    $listOfWarningsMessages = $oList->isPreparedDifferently($oProduct);?>
    <div class="hideChild">
        <div class="name"><?php echo $oProduct->setProductlistMode(true)->getName()?></div>
        <div class="artNr"><?php echo $this->__('Productlist_Header_sSku').': '.$oProduct->getSku()?></div>
        <?php if (count($listOfWarningsMessages) > 0) { ?>
            <div class="warning-wrapper">
                <?php foreach ($listOfWarningsMessages as $warningKey => $warningMessage) { ?>
                    <div class="ml-warning <?php echo $warningKey ?>" title="<?php echo $this->__($warningMessage)?>"></div>
                <?php } ?>
            </div>
        <?php } ?>
        <div class="product-link childToHide">
            <a class="ml-js-noBlockUi" href="<?php echo $oProduct->getEditLink() ?>" target="_blank"><span><?php echo $this->__('Productlist_Cell_sEditProduct')?></span></a>
        </div>
    </div>
<?php } ?>