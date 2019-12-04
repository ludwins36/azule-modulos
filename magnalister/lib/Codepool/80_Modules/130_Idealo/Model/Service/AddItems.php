<?php

/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
class ML_Idealo_Model_Service_AddItems extends ML_Modul_Model_Service_AddItems_Abstract {

    protected function getSubSystem(){
        return 'ComparisonShopping';
    }
    protected function getProductArray() {
        /* @var $oPrepareHelper ML_Idealo_Helper_Model_Table_Idealo_PrepareData */
        $oPrepareHelper = MLHelper::gi('Model_Table_idealo_PrepareData');
        $aMasterProducts = array();
        foreach ($this->oList->getList() as $oProduct) {
            /* @var $oProduct ML_Shop_Model_Product_Abstract */
            foreach ($this->oList->getVariants($oProduct) as $oVariant) {
                /* @var $oVariant ML_Shop_Model_Product_Abstract */
                if ($this->oList->isSelected($oVariant)) {
                    $aPrepareFields =  array(
                        'ItemTitle' => array('optional' => array('active' => true)),
                        'SKU' => array('optional' => array('active' => true)),
                        'Description' => array('optional' => array('active' => true)),
                        'ShippingCostMethod' => array('optional' => array('active' => true)),
                        'ShippingCost' => array('optional' => array('active' => true)),
                        'ShippingTime' => array('optional' => array('active' => true)),
                        'ShippingMethod' => array('optional' => array('active' => true)),
                        'PaymentMethod' => array('optional' => array('active' => true)),
                        'Image' => array('optional' => array('active' => true)),
                        'Price' => array('optional' => array('active' => true)),
                        'Quantity' => array('optional' => array('active' => true)),
                        'BasePrice' => array('optional' => array('active' => true)),
                        'BasePriceString' => array('optional' => array('active' => true)),
                        'ItemUrl' => array('optional' => array('active' => true)),
                        'Checkout' => array('optional' => array('active' => true)),
                        'Manufacturer' => array('optional' => array('active' => true)),
                        'ItemWeight' => array('optional' => array('active' => true)),
                        'ManufacturerPartNumber' => array('optional' => array('active' => true)),
                        'EAN' => array('optional' => array('active' => true)),
                        'MerchantCategory' => array('optional' => array('active' => true)),
                        'Quantity' => array('optional' => array('active' => true)),
                    );
                    $aMasterProducts[$oVariant->get('id')] = $oPrepareHelper
                        ->setPrepareList(null)
                        ->setProduct($oVariant)
                        ->getPrepareData($aPrepareFields, 'value')
                    ;
                    $aMasterProducts[$oVariant->get('id')]['Checkout'] = $aMasterProducts[$oVariant->get('id')]['checkout'];
                    unset($aMasterProducts[$oVariant->get('id')]['checkout']);
                    // shipping-cost = itemWeight
                    if ($aMasterProducts[$oVariant->get('id')]['ShippingCostMethod'] === '__ml_weight') {
                        $aMasterProducts[$oVariant->get('id')]['ShippingCost'] = $aMasterProducts[$oVariant->get('id')]['ItemWeight'];
                    }
                    unset($aMasterProducts[$oVariant->get('id')]['ShippingCostMethod']);
                    if ($aMasterProducts[$oVariant->get('id')]['ShippingTime']['type'] === '__ml_lump') {
                        $aMasterProducts[$oVariant->get('id')]['ShippingTime'] = $aMasterProducts[$oVariant->get('id')]['ShippingTime']['value'];
                    } else {
                        $aMasterProducts[$oVariant->get('id')]['ShippingTime'] = $aMasterProducts[$oVariant->get('id')]['ShippingTime']['type'];
                    }
                    // unset checkoutstatus fields if deactivated
                    if (!$aMasterProducts[$oVariant->get('id')]['Checkout']) {
                        unset($aMasterProducts[$oVariant->get('id')]['ShippingMethod']);
                    }
                    foreach($aMasterProducts[$oVariant->get('id')] as $sKey => $mValue){
                        if($mValue === null){
                            unset($aMasterProducts[$oVariant->get('id')][$sKey]);
                        }
                        if ($sKey === 'Image' && is_array($mValue)) {
                            $aImages = array();
                            foreach ($mValue as $sImage) {
                                try {
                                    $aImage = MLImage::gi()->resizeImage($sImage, 'products', 500, 500);
                                    $aImages[] = array('URL' => $aImage['url']);
                                } catch(Exception $ex) {
                                    // Happens if image doesn't exist.
                                }
                            }
                            $aMasterProducts[$oVariant->get('id')][$sKey] = $aImages;
                        }
                    }
                }
            }
        }
        return $aMasterProducts;
    }

    public function uploadItems() {
        return true;
    }
}
