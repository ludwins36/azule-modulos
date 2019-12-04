<?php

class ML_MeinPaket_Model_Service_SyncOrderStatus extends ML_Modul_Model_Service_SyncOrderStatus_Abstract {

    protected $sOrderIdConfirmations = 'MOrderID';
    protected function submitRequestAndProcessResult($sAction, $aRequest, $aModels) {
        $oModule = MLModul::gi();
        foreach ($aModels as $sModel => $oModel) {
            if (array_key_exists($sModel, $aRequest) && !array_key_exists('ConsignmentID', $aRequest[$sModel])) {
                $aRequest[$sModel]['ConsignmentID'] = $oModel->get('orders_id');
                $sShopStatus = $oModel->getShopOrderStatus();
                $sReason = 'DealerRequest';
		if($sShopStatus == $oModule->getConfig('orderstatus.canceled.customerrequest')){
                        $sReason = 'CustomerRequest';
                } elseif ($sShopStatus == $oModule->getConfig('orderstatus.canceled.outofstock')) {
                        $sReason = 'OutOfStock';
                } elseif ($sShopStatus == $oModule->getConfig('orderstatus.canceled.damagedgoods')) {
                        $sReason = 'DamagedGoods';
                }
                $aRequest[$sModel]['Reason'] = $sReason;
            }
        }
        return parent::submitRequestAndProcessResult($sAction, $aRequest, $aModels);
    }

    
    protected function isCancelled($sShopStatus){
        $oModule = MLModul::gi();
        $aCancelledStatuses = array(
            $oModule->getConfig('orderstatus.canceled.customerrequest'),
            $oModule->getConfig('orderstatus.canceled.outofstock'),
            $oModule->getConfig('orderstatus.canceled.damagedgoods'),
            $oModule->getConfig('orderstatus.canceled.dealerrequest')
        );
        return in_array($sShopStatus, $aCancelledStatuses);
    }
    
    protected function postProcessError($aError, &$aModels) {
        $sMarketplaceOrderId = null;
        if (isset($aError['DETAILS']) && isset($aError['DETAILS'][$this->sOrderIdentifier])) {
            $sMarketplaceOrderId = $aError['DETAILS'][$this->sOrderIdentifier];
        }
        if (empty($sMarketplaceOrderId) || !array_key_exists($sMarketplaceOrderId, $aModels)) {
            return;
        }
        $sErrorCode = null;
        if(isset($aError['DETAILS']['ErrorCode'])) {
            $sErrorCode = $aError['DETAILS']['ErrorCode'];
        }
        if (in_array($sErrorCode, array(
                '6003', // 6003: order is already shipped or cancelled, it couldn't be changed anymore
            ))
        ) {
            $this->saveOrderData($aModels[$sMarketplaceOrderId]);
            unset($aModels[$sMarketplaceOrderId]);
        }
    }
}
