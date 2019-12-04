<?php

MLFilesystem::gi()->loadClass('Cdiscount_Helper_Model_Service_OrderData_Normalize');

class ML_PrestashopCdiscount_Helper_Model_Service_OrderData_Normalize extends ML_Cdiscount_Helper_Model_Service_OrderData_Normalize {
    
    protected function getShippingCode($aTotal) {
        $sShippingMethod = MLModul::gi()->getConfig('orderimport.shippingmethod');
        return $sShippingMethod == 'textfield'/*check for old configuration*/ ? null: $sShippingMethod;
    }

}
