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
class ML_Dawanda_Controller_Dawanda_Prepare_Apply_Form extends ML_Form_Controller_Widget_Form_PrepareWithVariationMatchingAbstract {
    
    protected function productTypeField (&$aField) {
        $aField['values'] = ML::gi()->instance('controller_dawanda_config_prepare')->getField('producttype', 'values');
    }
    
    protected function listingDurationField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('prepareform_listingduration_values');
    }
    
    protected function returnPolicyField (&$aField) {
        $aField['values'] = ML::gi()->instance('controller_dawanda_config_prepare')->getField('returnpolicy', 'values');
    }
    
    protected function shippingServiceField (&$aField) {
        $aField['values'] = ML::gi()->instance('controller_dawanda_config_prepare')->getField('shippingservice', 'values');
    }
    
    protected function variationgroupsField(&$aField) {
        $aField['subfields']['variationgroups.value']['values'] =
            array(-1 => '..')
            + ML::gi()->instance('controller_dawanda_config_prepare')->getField('variationGroups_Value', 'values')
        ;
        $aField['subfields']['secondary']['values'] = 
            array(-1 => '..') 
            + ML::gi()->instance('controller_dawanda_config_prepare')->getField('secondarycategory', 'values')
        ;
        unset($aField['subfields']['secondary']); // dawanda currently only supports one marketplace category.
        $aField['subfields']['store']['values'] = 
            array('' => '..') 
            + ML::gi()->instance('controller_dawanda_config_prepare')->getField('shopcategory', 'values')
        ;
        
        foreach ($aField['subfields'] as &$aSubField) { //adding current cat, if not in top cat
            if (!empty($aSubField['value']) && !array_key_exists($aSubField['value'], $aSubField['values'])) {
                $oCat = MLDatabase::factory('dawanda_categories'.$aSubField['cattype']);
                $oCat->init(true)->set('categoryid', $aSubField['value']);
                $sCat = '';
                foreach($oCat->getCategoryPath() as $oParentCat) {
                    $sCat = $oParentCat->get('categoryname').' &gt; '.$sCat;
                }
                $aSubField['values'][$aSubField['value']] = substr($sCat,0 ,-6);
            }
        }
    }

    /**
     * @param $sIdentifier
     * @param $sCustomIdentifier
     * @return mixed
     */
    protected function getPreparedData($sIdentifier, $sCustomIdentifier)
    {
        $sProductId = $this->getProductId();

        $oPrepareTable = MLDatabase::getPrepareTableInstance();
        $sShopVariationField = $oPrepareTable->getShopVariationFieldName();
        $sPrimaryCategory = $this->oPrepareList->get($oPrepareTable->getPrimaryCategoryFieldName());

        $sPrimaryCategoryValue = isset($sPrimaryCategory['[' . $sProductId . ']'])
            ? $sPrimaryCategory['[' . $sProductId . ']'] : reset($sPrimaryCategory);

        if (!empty($sPrimaryCategory)) {
            if ($sPrimaryCategoryValue === $sIdentifier) {
                $aShopVariation = $this->oPrepareList->get($sShopVariationField);
                if (!empty($aShopVariation)) {
                    $aValue = isset($aShopVariation['[' . $sProductId . ']'])
                        ? $aShopVariation['[' . $sProductId . ']'] : reset($aShopVariation);
                }

                if (empty($aValue)) {
                    // Backward compatibility
                    $aValue = $this->fixOldAttributes($sPrimaryCategoryValue, $sProductId);
                }
            }
        }

        if (!isset($aValue)) {
            $aValue = $this->getVariationDb()
                ->set('Identifier', $sIdentifier)
                ->set('CustomIdentifier', $sCustomIdentifier)
                ->get('ShopVariation');
        }

        return $aValue;
    }

    public function triggerAfterField(&$aField, $parentCall = false)
    {
        //TODO Check this parent call
        parent::triggerAfterField($aField);

        if ($parentCall) {
            return;
        }

        $sName = $aField['realname'];

        // when top variation groups drop down is changed, its value is updated in getRequestValue
        // otherwise, it should remain empty.
        // without second condition this function will be executed recursevly because of the second line below.
        if (!isset($aField['value'])) {
            $sProductId = $this->getProductId();

            $oPrepareTable = MLDatabase::getPrepareTableInstance();
            $sShopVariationField = $oPrepareTable->getShopVariationFieldName();

            $aPrimaryCategories = $this->oPrepareList->get($oPrepareTable->getPrimaryCategoryFieldName());
            $sPrimaryCategoriesValue = isset($aPrimaryCategories['[' . $sProductId . ']'])
                ? $aPrimaryCategories['[' . $sProductId . ']'] : reset($aPrimaryCategories);

            if ($sName === 'variationgroups.value') {
                $aField['value'] = $sPrimaryCategoriesValue;
            } else {
                // check whether we're getting value for standard group or for custom variation mathing group
                $sCustomGroupName = $this->getField('variationgroups.value', 'value');
                $aCustomIdentifier = explode(':', $sCustomGroupName);

                if (count($aCustomIdentifier) == 2 && ($sName === 'attributename' || $sName === 'customidentifier')) {
                    $aField['value'] = $aCustomIdentifier[$sName === 'attributename' ? 0 : 1];
                    return;
                }

                $aNames = explode('.', $sName);
                if (count($aNames) == 4 && strtolower($aNames[3]) === 'code') {
                    $aValue = $this->oPrepareList->get($sShopVariationField);
                    $aValueFix = isset($aValue['[' . $sProductId . ']']) ? $aValue['[' . $sProductId . ']'] : reset($aValue);

                    if (empty($aValueFix) && $sPrimaryCategoriesValue) {
                        $aValueFix = $this->fixOldAttributes($sPrimaryCategoriesValue, $sProductId);
                    }
                    
                    if (empty($aValueFix) || strtolower($sPrimaryCategoriesValue) !== strtolower($aNames[1])) {
                        // real name is in format "variationgroups.qnvjagzvcm1hda____.rm9ybwf0.code"
                        $sCustomIdentifier = count($aCustomIdentifier) == 2 ? $aCustomIdentifier[1] : '';
                        $aValue = $this->getVariationDb()
                            ->set('Identifier', $aNames[1])
                            ->set('CustomIdentifier', $sCustomIdentifier)
                            ->get('ShopVariation');
                    } else {
                        $aValue = $aValueFix;
                    }

                    if ($aValue) {
                        foreach ($aValue as $sKey => &$aMatch) {
                            if (strtolower($sKey) === $aNames[2]) {
                                if (!isset($aMatch['Code'])) {
                                    // this will happen only if attribute was matched and then deleted from the shop
                                    $aMatch['Code'] = '';
                                }
                                $aField['value'] = $aMatch['Code'];
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Covering situation if client prepared item before new variation matching concept
     *
     * @param $sCategoryId
     * @param $sProductId
     * @return array
     */
    private function fixOldAttributes($sCategoryId, $sProductId) {
        $attributesFixed = array();
        $response = $this->callGetCategoryDetails($sCategoryId);
        $mpAttributes = $response['DATA']['attributes'];

        $aMpColors = $this->oPrepareList->get('MpColors');
        if (!empty($aMpColors['[' . $sProductId . ']'])) {
            for ($i = 0; $i < 2; $i++) {
                if (!empty($aMpColors['[' . $sProductId . ']'][$i])) {
                    $attributesFixed['Color' . ($i + 1)] = array(
                        'Kind' => 'Matching',
                        'AttributeName' => $mpAttributes['Color' . ($i + 1)]['title'],
                        'Values' => $aMpColors['[' . $sProductId . ']'][$i],
                        'Required' => false,
                        'Code' => 'attribute_value',
                        'DataType' => 'select',
                        'Error' => false
                    );
                }
            }
        }

        $aAttributes = $this->oPrepareList->get('Attributes');
        if (!empty($aAttributes['[' . $sProductId . ']']) && !empty($aAttributes['[' . $sProductId . ']'][$sCategoryId]['specifics'])) {

            foreach ($aAttributes['[' . $sProductId . ']'][$sCategoryId]['specifics'] as $attributeValues) {
                foreach ($mpAttributes as $mpKeyAttribute => $mpAttribute) {
                    // If at least of of the value id is in marketplace values then this is our attribute
                    // We need this for attribute key, because we don't have that information in prepare table
                    $mpAttribute['values'] = array_flip($mpAttribute['values']);
                    if (is_array($attributeValues)) {
                        $sType = 'multiSelect';
                        $containsSearch = count(array_intersect($attributeValues, $mpAttribute['values'])) > 0;
                    } else {
                        $sType = 'select';
                        $containsSearch = in_array($attributeValues, $mpAttribute['values']);
                    }

                    if ($containsSearch) {
                        $attributesFixed[$mpKeyAttribute] = array(
                            'Kind' => 'Matching',
                            'AttributeName' => $mpAttribute['title'],
                            'Values' => $attributeValues,
                            'Required' => false,
                            'Code' => 'attribute_value',
                            'DataType' => $sType,
                            'Error' => false
                        );

                        break;
                    }
                }
            }
        }

        return $attributesFixed;
    }
    
    
    public function shippingTimeField (&$aField) {
        $aField['values'] = MLModul::gi()->getShippingTimes();
    }
    
}