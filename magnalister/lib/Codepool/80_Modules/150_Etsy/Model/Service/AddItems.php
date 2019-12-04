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
* (c) 2010 - 2018 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

class ML_Etsy_Model_Service_AddItems extends ML_Modul_Model_Service_AddItems_Abstract {
    protected function getProductArray() {
        /* @var $oHelper ML_Etsy_Helper_Model_Service_Product */
        $oHelper = MLHelper::gi('Model_Service_Product');
        $aMasterProducts = array();
        foreach ($this->oList->getList() as $oProduct) {
            /* @var $oProduct ML_Shop_Model_Product_Abstract */
            $oHelper->setProduct($oProduct);
            foreach ($this->oList->getVariants($oProduct) as $oVariant) {
                /* @var $oVariant ML_Shop_Model_Product_Abstract */
                if ($this->oList->isSelected($oVariant)) {
                    $oHelper->resetData();
                    $aData = $oHelper->setVariant($oVariant)->getData();
                    if (empty($aData['CategoryAttributes']) || !$this->variationShouldBeExcluded(json_decode($aData['CategoryAttributes'], true))) {
                        $aMasterProducts[$oVariant->get('id')] = $aData;
                    }

                }
            }
        }

        return $aMasterProducts;
    }


    /**
     * check if there is any notmatch value in matched value
     * @param array $aVariation
     * @return bool
     */
    protected function variationShouldBeExcluded(array $aVariation) {
        $blReturn = false;
        foreach ($aVariation['property_values'] as $aValue) {
            if ($aValue['property_id'] === 'notmatch') {
                $blReturn = true;
                break;
            }
        }
        return $blReturn;
    }

}