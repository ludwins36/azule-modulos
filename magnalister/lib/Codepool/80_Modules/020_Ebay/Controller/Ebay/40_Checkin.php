<?php

MLFilesystem::gi()->loadClass('Ebay_Controller_Widget_ProductList_Ebay_Abstract');
class ML_Ebay_Controller_Ebay_Checkin extends ML_Ebay_Controller_Widget_ProductList_Ebay_Abstract {

    protected $aParameters = array('controller');
    
    
    public static function getTabTitle() {
        return MLI18n::gi()->get('ML_GENERIC_CHECKIN');
    }

    public static function getTabActive() {
        return MLModul::gi()->isConfigured();
    }
    
    public function getProductListWidget() {
        if ($this->isCurrentController()) {
            if (count($this->getProductList()->getMasterIds(true))==0) {//only check current page
                MLMessage::gi()->addInfo($this->__('ML_EBAY_TEXT_NO_MATCHED_PRODUCTS'));
            }
            return parent::getProductListWidget();
        } else {
            return $this->getChildController('summary')->render();
        }
    } 
    
    protected function callAjaxDontShowWarning() {
        MLModul::gi()->setConfig('checkin.dontshowwarning', 1, true);
        MLSetting::gi()->add(
            'aAjax', array(
                'success' => true,
                'error' => '',
            )
        );
        return true;
        
    }
    
    /**
     * only prepared can be selected
     * @param ML_Database_Model_Table_Abstract $mProduct
     * @return type
     */
    public function getVariantCount($mProduct) {
        return MLDatabase::factory('ebay_prepare')->getVariantCount($mProduct);
    }
    
        protected $aPrepare = array();
    protected $aSelected = array();

    public function getPriceObject(ML_Shop_Model_Product_Abstract $oProduct) {
        $oPrepare = $this->getPrepareData($oProduct);
        return MLModul::gi()->getPriceObject($oPrepare->get('ListingType'));
    }

    public function getPrepareData(ML_Shop_Model_Product_Abstract $oProduct) {
        if (!isset($this->aPrepare[$oProduct->get('id')])) {
            $this->aPrepare[$oProduct->get('id')] = MLDatabase::factory('ebay_prepare')->set('products_id', $oProduct->get('id'));
        }
        return $this->aPrepare[$oProduct->get('id')];
    }

    public function getPrice(ML_Shop_Model_Product_Abstract $oProduct) {
        $oPrepare = $this->getPrepareData($oProduct);
        if ($oPrepare->get('price') !== null) {
            return $oPrepare->get('price');
        } else {
            return $oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject($oPrepare->get('ListingType')), true, false);
        }
    }

    public function getStock(ML_Shop_Model_Product_Abstract $oProduct) {
        $oPreparedProduct = $oProduct;
        if ($oPreparedProduct->get('parentid') == 0) {
            $oPreparedProduct = $this->getFirstVariant($oPreparedProduct);
        }
        $oPrepare = $this->getPrepareData($oPreparedProduct);
        $aStockConf = MLModul::gi()->getStockConfig($oPrepare->get('ListingType'));
        return $oProduct->getSuggestedMarketplaceStock($aStockConf['type'], $aStockConf['value'], $aStockConf['max']);
    }
}