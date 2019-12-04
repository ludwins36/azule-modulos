<?php

/**
 * select all products 
 * amazon-config: 
 *  - amazon.lang isset
 * magnalister.selectionname=='match
 */
class ML_Amazon_Model_List_Amazon_Order_Summary {

    protected $aList = null;
    protected $aMixedData = array();
    protected $sSelectionName;
    
    public function getList() {
        if ($this->aList === null) {
           $oSelection = MLDatabase::factory('globalselection')->set('selectionname', $this->getSelectionName());
           foreach ($oSelection->getList()->getList() as $oOrder){
               $aData = $oOrder->get('data');
               if(isset($aData['ShippingServiceId'])){
                    $fPrice =  MLPrice::factory()->format($aData['globalinfo']['shippingservice']['Rate']['Amount'], $aData['globalinfo']['shippingservice']['Rate']['CurrencyCode']);
                    $this->aList[]=array(
                        'BuyerName' => $aData['globalinfo']['AddressSets']['Shipping']['Firstname']. ' '. $aData['globalinfo']['AddressSets']['Shipping']['Lastname'],
                        'ShippingDate' => $aData['ShippingDate'],
                        'Weight' => $aData['Weight']['Value']. ' ' . $aData['Weight']['Unit'],
                        'ShippingServiceName' => $aData['globalinfo']['shippingservice']['ShippingServiceName'],
                        'CarrierName' => $aData['globalinfo']['shippingservice']['CarrierName'],
                        'UnitPrice' => $fPrice,
                        'TotalPrice' => $fPrice,
                    );
               }else{
                   $oOrder->delete();
               }
           }
        }
        return $this->aList;
    }

    public function getFilters(){
        return array();
    }
    
    public function setSelectionName($sSelectionName){
        $this->sSelectionName = $sSelectionName;
    }
    public function getSelectionName(){
        return $this->sSelectionName;
    }

    public function getHead() {
        $aHead = array();
        $aHead['BuyerName'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Receiver'),
        );
         $aHead['ShippingDate'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_ShippingDate'),
        );
        $aHead['Weight'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_Weight'),
        );
        $aHead['CarrierName'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_CarrierName'),
        );
        $aHead['ShippingServiceName'] = array(
            'title' => MLI18n::gi()->get('ML_LABEL_MARKETPLACE_SHIPPING_METHOD'),
        );
        $aHead['UnitPrice'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_UnitPrice'),
        );
        
        $aHead['TotalPrice'] = array(
            'title' => MLI18n::gi()->get('ML_Amazon_Shippinglabel_TotalPrice'),
        );
        return $aHead;
    }


}
