<?php
MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_Selection');
abstract class ML_Productlist_Controller_Widget_ProductList_UploadAbstract extends ML_Productlist_Controller_Widget_ProductList_Selection {

    protected $aParameters = array('controller');

    protected $aPrepare=array();
    public function getPrepareData(ML_Shop_Model_Product_Abstract $oProduct){
        if(!isset($this->aPrepare[$oProduct->get('id')])){
            $sMpName = MLModul::gi()->getMarketPlaceName();
            $this->aPrepare[$oProduct->get('id')]=MLDatabase::factory($sMpName.'_prepare')->set('productsid',$oProduct->get('id'));
        }
        return $this->aPrepare[$oProduct->get('id')];
    }
    
    public static function getTabTitle() {
        return MLI18n::gi()->get('ML_GENERIC_CHECKIN');
    }

    public static function getTabActive() {
        return MLModul::gi()->isConfigured();
    }
    protected function addItems($blPurge) {
        $oList = $this->getProductList()->setAdditemMode(true);
        $mOffset = $this->getRequest('offset');
        $iOffset = ($mOffset === null) ? 0 : $mOffset;
        $iLimit = 1;//min from list
        $oList->setLimit(0, $iLimit);//offset is 0, because uploaded products will be deleted from selections
        $aStatistic = $oList->getStatistic();
        $iTotal =  (int)$aStatistic['iCountTotal'];
        $oService =  MLService::getAddItemsInstance();
        try {
            $oService->setProductList($oList)->setPurge($blPurge)->execute();
            $blSuccess = true;
        } catch (Exception $oEx) {//more
            MLMessage::gi()->addDebug($oEx);
            $blSuccess = false;
        }
        
        // In case selection list is empty, send back success response.
        if ($oList->getList()->count() === 0) {
            MLSetting::gi()->add(
                'aAjax', array(
                    'success' => true,
                    'error' => '',
                    'offset' => 0,
                    'info' => array(
                        'total' => 0,
                        'current' => 0,
                        'purge' => false,
                    ),
                )
            );
            
            return $this;
        }
        
        if ($oService->haveError()) {
            $sMessage = '';
            foreach ($oService->getErrors() as $sServiceMessage) {
                $sMessage .= '<div>' . $sServiceMessage . '</div>';
            }

            MLSetting::gi()->add('aAjaxPlugin', array('dom' => array('#recursiveAjaxDialog .errorBox' => array('action' => 'append', 'content' => $sMessage))));
        }
        if ($this->getRequest('saveSelection') != 'true') {
            MLSetting::gi()->add(
                'aAjax',
                array(
                    'success' => $blSuccess,
                    'error' => $oService->haveError() ,
                    'offset' => $iOffset+count($oList->getMasterIds(true)),
                    'info' => array(
                        'total' => $iTotal+$iOffset,
                        'current' => $iOffset+count($oList->getMasterIds(true)),
                        'purge' => ($blPurge && $iOffset == 0),
                    ),
                )
            );
            $oSelection = MLDatabase::factory('selection');
            foreach ($oList->getList() as $oProduct) {
                foreach ($oList->getVariants($oProduct) as $oChild) {
                    $oSelection->init()->loadByProduct($oChild,'checkin')->delete();
                }
            }
        } else {
            MLSetting::gi()->add(
                'aAjax',
                array(
                    'success' => false,
                    'error' => $oService->haveError() ,
                    'offset' => $iOffset,
                    'info' => array(
                        'total' => $iTotal+$iOffset,
                        'current' => $iOffset,
                        'purge' => $blPurge,
                    ),
                )
            );
        }
        return $this;
    }

    public function getStock(ML_Shop_Model_Product_Abstract $oProduct) {
        $aStockConf = MLModul::gi()->getStockConfig();
        return $oProduct->getSuggestedMarketplaceStock($aStockConf['type'], $aStockConf['value']);
    }
    
    protected function callAjaxCheckinAdd() {
        return $this->addItems(false);
        
    }
    protected function callAjaxCheckinPurge() {
        return $this->addItems(true);
    }
    
    public function render(){
        $this->getProductListWidget();
        return $this;
    }
    
    
    public function getProductListWidget() {
        if (count($this->getProductList()->getMasterIds(true))==0) {//only check current page
            MLMessage::gi()->addInfo(MLI18n::gi()->get('Productlist_No_Prepared_Products', array('marketplace'=> MLModul::gi()->getMarketPlaceName(false))));
        }
        parent::getProductListWidget();
    } 
    
    public function getPriceObject(\ML_Shop_Model_Product_Abstract $oProduct) {
        return MLModul::gi()->getPriceObject();
    }
}