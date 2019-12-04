<?php

MLFilesystem::gi()->loadClass('Amazon_Helper_Model_Service_OrderData_Normalize');

class ML_PrestashopAmazon_Helper_Model_Service_OrderData_Normalize extends ML_Amazon_Helper_Model_Service_OrderData_Normalize {
    
    protected function getShippingCode($aTotal) {
        if ($this->aOrder['MPSpecific']['FulfillmentChannel'] == 'AFN' && $this->getModul()->getConfig('orderimport.fbashippingmethod') !== null) { //amazon payed and shipped
            $sStatusKey = 'orderimport.fbashippingmethod';
        }else{
            $sStatusKey = 'orderimport.shippingmethod';
        }
        $sShippingMethod = MLModul::gi()->getConfig($sStatusKey);
        return $sShippingMethod == 'textfield'/*check for old configuration*/ ? null: $sShippingMethod;
    }


}
