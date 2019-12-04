<?php
MLFilesystem::gi()->loadClass('Modul_Helper_Model_Service_OrderData_Normalize');

class ML_Cdiscount_Helper_Model_Service_OrderData_Normalize extends ML_Modul_Helper_Model_Service_OrderData_Normalize {

    protected $oModul = null;

    protected function getModul() {
        if($this->oModul === null ){
            $this->oModul = MLModul::gi();
        }

        return $this->oModul;
    }
    
    protected function getShippingCode($aTotal) {
        $sShippingMethod = $this->getModul()->getConfig('orderimport.shippingmethod');
        if (!empty($sShippingMethod)) {
            if ('textfield' == $sShippingMethod) {
               $sShipping = $this->getModul()->getConfig('orderimport.shippingmethod.name');
               return $sShipping == '' ? MLModul::gi()->getMarketPlaceName() : $sShipping;
            }

            return $sShippingMethod;
        }

        return MLModul::gi()->getMarketPlaceName();
    }

}
