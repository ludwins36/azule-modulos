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
MLFilesystem::gi()->loadClass('Form_Controller_Widget_Form_PrepareWithVariationMatchingAbstract');

class ML_MeinPaket_Controller_MeinPaket_Prepare_Prepare_Form extends ML_Form_Controller_Widget_Form_PrepareWithVariationMatchingAbstract
{
    protected $numberOfMaxAdditionalAttributes = self::UNLIMITED_ADDITIONAL_ATTRIBUTES;

    protected function getSelectionNameValue()
    {
        return 'match';
    }

    protected function shippingtypeField(&$aField)
    {
        $aField['values'] = ML::gi()->instance('controller_meinpaket_config_prepare')->getField('shippingtype', 'values');
    }

    protected function categoriesField(&$aField)
    {
        $aField['subfields']['primary']['values'] = array('' => '..') 
            + ML::gi()->instance('controller_meinpaket_config_prepare')->getField('primarycategory', 'values');
        
        foreach ($aField['subfields'] as &$aSubField) { //adding current cat, if not in top cat
            if (!array_key_exists($aSubField['value'], $aSubField['values'])) {
                $oCat = MLDatabase::factory('meinpaket_categories' . $aSubField['cattype']);
                $oCat->init(true)->set('categoryid', $aSubField['value']);
                $sCat = '';
                foreach ($oCat->getCategoryPath() as $oParentCat) {
                    $sCat = $oParentCat->get('categoryname') . ' &gt; ' . $sCat;
                }
                $aSubField['values'][$aSubField['value']] = substr($sCat, 0, -6);
            }
        }
    }

    protected function variationGroups_ValueField(&$aField)
    {
        $aField['type'] = 'select';
        $variationGroups = $this->getAllMPVariationGroups();
        $aField['values'] = array_merge(array(
            '' => MLI18n::gi()->get('ML_AMAZON_LABEL_APPLY_PLEASE_SELECT'),
            'splitAll' => MLI18n::gi()->get('ML_LABEL_DONT_USE'),
        ), $variationGroups);
    }

    protected function variationThemeCodeField(&$aField)
    {
        $variationThemes = json_decode(htmlspecialchars_decode($this->getField('variationthemealldata', 'value')), true);
        $aField['type'] = 'ajax';
        $aField['ajax'] = array(
            'selector' => '#' . $this->getField('variationgroups.value', 'id'),
            'trigger' => 'change',
        );

        $mParentValue = $this->getField('variationgroups.value', 'value');

        if (is_array($variationThemes) && count($variationThemes) > 0 && $mParentValue != '') {
            $aField['value'] = array((string)$mParentValue => $mParentValue);
            $aField['ajax']['field']['type'] = 'dependonfield';
            $aField['dependonfield']['depend'] = 'variationgroups.value';
            $aField['dependonfield']['field']['type'] = 'hidden';
            $aField['dependonfield']['field']['value'] = $mParentValue;
        }
    }

    protected function callGetCategoryDetails($sCategoryId)
    {
        if (empty($sCategoryId)) {
            $sCategoryId = 'splitAll';
        }

        return parent::callGetCategoryDetails($sCategoryId);
    }

    protected function getPreparedShopVariationForList($oPrepareList, $setDefaultValue = true)
    {
        $shopVariation = parent::getPreparedShopVariationForList($oPrepareList, $setDefaultValue);

        $sProductId = $this->getProductId();
        $aPrimaryCategories = $oPrepareList->get(MLDatabase::getPrepareTableInstance()->getPrimaryCategoryFieldName());
        $sPrimaryCategoriesValue = isset($aPrimaryCategories['[' . $sProductId . ']'])
            ? $aPrimaryCategories['[' . $sProductId . ']'] : reset($aPrimaryCategories);

        return $this->convertOldShopVariationValue($shopVariation, $sPrimaryCategoriesValue);
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

    private function getAllMPVariationGroups()
    {
        $variationGroups = array();
        try {
            $aResponse = MagnaConnector::gi()->submitRequestCached(array('ACTION' => 'GetAvailableVariantConfigurations'));
            if ($aResponse['STATUS'] == 'SUCCESS' && isset($aResponse['DATA']) && is_array($aResponse['DATA'])) {
                $variationGroups = $aResponse['DATA'];
            }
        } catch (MagnaException $e) {

        }

        return array_combine(array_keys($variationGroups), array_map(function($variationGroup) {
            return $variationGroup['Name'];
        }, $variationGroups));
    }
}
