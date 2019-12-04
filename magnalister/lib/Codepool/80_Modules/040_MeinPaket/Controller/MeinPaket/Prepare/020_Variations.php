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
MLFilesystem::gi()->loadClass('Form_Controller_Widget_Form_VariationsAbstract');

class ML_MeinPaket_Controller_MeinPaket_Prepare_Variations extends ML_Form_Controller_Widget_Form_VariationsAbstract
{
    protected $numberOfMaxAdditionalAttributes = self::UNLIMITED_ADDITIONAL_ATTRIBUTES;

    protected function checkAttributesFromDB($sIdentifier, $sIdentifierName)
    {
        // similar validation exists in ML_Productlist_Model_ProductList_Abstract::isPreparedDifferently
        $aValue = MLDatabase::getVariantMatchingTableInstance()->getMatchedVariations($sIdentifier);

        $oPrepareTable = MLDatabase::getPrepareTableInstance();
        $sShopVariationField = $oPrepareTable->getShopVariationFieldName();

        $aValuesFromPrepare = $oPrepareTable->set($oPrepareTable->getPrimaryCategoryFieldName(), $sIdentifier)
            ->getList();
        $aValuesFromPrepare = $aValuesFromPrepare->getQueryObject()->getCount(true) > 0 ? $aValuesFromPrepare->get($sShopVariationField) : array();

        if (!empty($aValuesFromPrepare)) {
            foreach ($aValuesFromPrepare as $prepareValue) {
                $prepareValue = $this->convertOldShopVariationValue($prepareValue, $sIdentifier);
                // comparing arrays! do not use '!=='
                if ($prepareValue != $aValue) {
                    MLMessage::gi()->addNotice(self::getMessage('_prepare_variations_notice',
                        array('category_name' => $sIdentifierName)));
                    return;
                }
            }
        }
    }

    protected function getAttributesFromDB($sIdentifier, $sCustomIdentifier = '') {
        if ($sCustomIdentifier === null) {
            $sCustomIdentifier = '';
        }
        $aValue = $this->getVariationDb()
            ->set('Identifier', $sIdentifier)
            ->set('CustomIdentifier', $sCustomIdentifier)
            ->get('ShopVariation');

        if (empty($aValue)) {
            return array();
        }

        return $this->convertOldShopVariationValue($aValue, $sIdentifier);
    }

    protected function convertOldShopVariationValue($oldShopVariation, $sIdentifier)
    {
        /* @var $oHelper ML_MeinPaket_Helper_Model_Service_Product */
        $oHelper = MLHelper::gi('Model_Service_Product');
        return $oHelper->convertOldShopVariationValue($oldShopVariation, $sIdentifier);
    }

    protected function variationGroups_ValueField(&$aField) {
        $variationGroups = $this->getAllMPVariationGroups();
        $aField['values'] = array_merge(array(
            'none' => MLI18n::gi()->get('ML_AMAZON_LABEL_APPLY_PLEASE_SELECT'),
            'splitAll' => MLI18n::gi()->get('ML_LABEL_DONT_USE'),
        ), $variationGroups);
    }

    protected function getAllMPVariationGroups()
    {
        $variationGroups = $this->getFromApi('GetAvailableVariantConfigurations');
        return array_combine(array_keys($variationGroups), array_map(function($variationGroup) {
            return $variationGroup['Name'];
        }, $variationGroups));
    }
}
