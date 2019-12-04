<?php

MLFilesystem::gi()->loadClass('Core_Controller_Abstract');

class ML_Tools_Controller_Main_Tools_Orders extends ML_Core_Controller_Abstract {
    protected $aParameters=array('controller');
    protected $aData = null;
    
    protected function getRequestedOrderSpecial () {
        return $this->getRequest('orderspecial');
    }
    
    protected function getOrderData () {
        $oOrder = MLOrder::factory()->getByMagnaOrderId($this->getRequestedOrderSpecial());
        if ($this->getRequestedOrderSpecial() != '' && $oOrder->exists()) {
            if ($this->aData === null) {
                if ($this->getRequest('action') === 'unacknowledge') {
                   try {
                       $this->aData = array('unAcknowledgeImportedOrder' => $oOrder->unAcknowledgeImportedOrder($oOrder->get('platform'), $oOrder->get('mpid'), $oOrder->get('special'), $oOrder->get('orders_id'), $this->getRequest('apirequest') == 'yes'));
                   } catch (Exception $oEx) {
                       $this->aData = array('Exception' => $oEx->getMessage());
                   }
                } else if ($this->getRequest('action') === 'recreateProducts') {
                    ML::gi()->init(array('mp' => $oOrder->get('mpid')));$oOrderHelper = MLHelper::gi('model_shoporder');
                    /* @var $oOrderHelper ML_Shopware_Helper_Model_ShopOrder */
                    $sMpId = MLModul::gi()->getMarketPlaceId();
                    MLSetting::gi()->set('sCurrentOrderImportLogFileName', 'OrderImport_'.$sMpId, true);
                    MLSetting::gi()->sCurrentOrderImportMarketplaceOrderId = $oOrder->get('special');
                    $oOrderHelper
                            ->setOrder($oOrder)
                            ->recreateProducts();
                } else {
                    ML::gi()->init(array('mp' => $oOrder->get('mpid')));
                    $this->aData = array(
                        '$oOrder->data()' => $oOrder->data(),
                        'shop' => array(
                            '$oOrder->getShopOrderStatus()' => $oOrder->getShopOrderStatus(),
                            '$oOrder->getShopOrderLastChangedDate()' => $oOrder->getShopOrderLastChangedDate(),
                            '$oOrder->getShippingDateTime()' => $oOrder->getShippingDateTime(),
                            '$oOrder->getShippingCarrier()' => $oOrder->getShippingCarrier(),
                            '$oOrder->getShippingTrackingCode()' => $oOrder->getShippingTrackingCode(),
                        ),
                    );
                    ML::gi()->init(array());
                }
            }
            return $this->aData;
        }
    }
}
