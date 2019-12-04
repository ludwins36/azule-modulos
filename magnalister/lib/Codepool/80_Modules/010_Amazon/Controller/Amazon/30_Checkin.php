<?php
MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_UploadAbstract');
class ML_Amazon_Controller_Amazon_Checkin extends ML_Productlist_Controller_Widget_ProductList_UploadAbstract {

    public function getPrice(ML_Shop_Model_Product_Abstract $oProduct){
        return $oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject(),true,false);
    }
    
    public function getStock(ML_Shop_Model_Product_Abstract $oProduct){
        $aStockConf=  MLModul::gi()->getStockConfig();
        return $oProduct->getSuggestedMarketplaceStock($aStockConf['type'],$aStockConf['value']);
    }
    
    /**
     * only prepared can be selected
     * @param ML_Database_Model_Table_Abstract $mProduct
     * @return type
     */
    public function getVariantCount($mProduct) {
        $sMpName = MLModul::gi()->getMarketPlaceName();
        return MLDatabase::factory($sMpName.'_prepare')->getVariantCount($mProduct);
    }
}