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


MLFilesystem::gi()->loadClass('Prestashop_Model_ProductList_Abstract');

class ML_PrestashopEbay_Model_ProductList_Ebay_Prepare_Apply extends ML_Prestashop_Model_ProductList_Abstract {

    protected function executeFilter() {
        $this->oFilter
            ->registerDependency('searchfilter')
            ->limit()
            ->registerDependency('categoryfilter')
            ->registerDependency('preparestatusfilter')
            ->registerDependency('marketplacesyncfilter')
            ->registerDependency('prestashopproducttypefilter')
            ->registerDependency('productstatusfilter')
            ->registerDependency('manufacturerfilter')
        ;
        return $this;
    }

    public function getSelectionName() {
        return 'apply';
    }

    /**
     * Checks whether product attributes are prepared differently than in variation matching tab.
     *
     * @param ML_Shop_Model_Product_Abstract $oProduct
     * @return array
     */
    public function isPreparedDifferently($oProduct)
    {
        $warningMessages = array();
        $oPrepareTable = MLDatabase::getPrepareTableInstance();
        $sTableName = $oPrepareTable->getTableName();
        if (!$oPrepareTable->isVariationMatchingSupported()) {
            return $warningMessages;
        }

        $sShopVariationField = $oPrepareTable->getShopVariationFieldName();
        $sCategoryField = $oPrepareTable->getPrimaryCategoryFieldName();
        $mpId = MLModul::gi()->getMarketPlaceId();
        $productId = (int)$oProduct->get('id');

        if ($oProduct->get('parentid') == 0) {
            $aPreparedData = MLDatabase::getDbInstance()->fetchRow("
                SELECT $sTableName.$sShopVariationField, $sTableName.$sCategoryField, $sTableName.SecondaryCategory
                  FROM magnalister_products
                      INNER JOIN $sTableName ON magnalister_products.id = $sTableName.{$oPrepareTable->getProductIdFieldName()}
                  WHERE {$oPrepareTable->getMarketplaceIdFieldName()} = '$mpId'
                      AND magnalister_products.parentid='$productId'
              -- GROUP BY 
            ");
        } else {
            $aPreparedData = MLDatabase::getDbInstance()->fetchRow("
                SELECT $sShopVariationField, $sCategoryField, SecondaryCategory
                  FROM $sTableName
                 WHERE {$oPrepareTable->getMarketplaceIdFieldName()} = '$mpId'
                   AND {$oPrepareTable->getProductIdFieldName()} = '$productId'
            ");
        }

        if (isset($aPreparedData[$sCategoryField])) {
            $sGlobalMatching = $this->getMatchedAttributes($aPreparedData[$sCategoryField]);
            $shopAttributes = MLFormHelper::getShopInstance()->getFlatShopAttributesForMatching();
            $preparedData = json_decode($aPreparedData[$sShopVariationField], true);

            if (!empty($aPreparedData['SecondaryCategory'])) {
                $secondaryCategoryGlobalMatching = $this->getMatchedAttributes($aPreparedData['SecondaryCategory']);
                if (!empty($secondaryCategoryGlobalMatching)) {
                    $sGlobalMatching = !empty($sGlobalMatching) ? $sGlobalMatching : array();
                    foreach ($secondaryCategoryGlobalMatching as $attributeCode => $attributeSettings) {
                        if (isset($sGlobalMatching[$attributeCode]) || !isset($preparedAttributes[$attributeCode])) {
                            continue;
                        }

                        $sGlobalMatching[$attributeCode] = $attributeSettings;
                    }
                }
            }

            if ($sGlobalMatching && $sGlobalMatching != $preparedData) {
                $warningMessages[] = 'Productlist_ProductMessage_sPreparedDifferently';
            }

            if (is_array($preparedData)) {
                foreach ($preparedData as $attribute) {
                    if ($attribute['Code'] === '' || $attribute['Code'] === 'freetext' || $attribute['Code'] === 'attribute_value') {
                        continue;
                    }

                    if (!isset($shopAttributes[$attribute['Code']])) {
                        $warningMessages[] = 'Productlist_ProductMessage_sAttributeDeletedFromTheShop';
                        return $warningMessages;
                    }

                    $shopAttributeValues = MLFormHelper::getShopInstance()->getAttributeOptions($attribute['Code']);

                    if (isset($attribute['Values']) && is_array($attribute['Values'])) {
                        foreach ($attribute['Values'] as $attributeValue) {
                            if (!is_array($attributeValue['Shop']['Key'])) {
                                $attributeValue['Shop']['Key'] = array($attributeValue['Shop']['Key']);
                            }

                            $missingShopValueKeys = array_diff_key(array_flip($attributeValue['Shop']['Key']), $shopAttributeValues);
                            if (count($missingShopValueKeys) > 0) {
                                $warningMessages[] = 'Productlist_ProductMessage_sAttributeValuesDeletedFromTheShop';
                                return $warningMessages;
                            }
                        }
                    }
                }
            }
        }

        return $warningMessages;
    }
}