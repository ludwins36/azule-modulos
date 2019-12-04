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
ini_set('xdebug.max_nesting_level', 200);
MLFilesystem::gi()->loadClass('Form_Controller_Widget_Form_PrepareWithVariationMatchingAbstract');

class ML_Crowdfox_Controller_Crowdfox_Prepare_Apply_Form extends ML_Form_Controller_Widget_Form_PrepareWithVariationMatchingAbstract
{
    protected $numberOfMaxAdditionalAttributes = self::UNLIMITED_ADDITIONAL_ATTRIBUTES;

    public function shippingmethodField(&$aField)
    {
        $aField['values'] = array_merge(array('' => MLI18n::gi()->ConfigFormEmptySelect),
            $this->callApi('GetShippingMethod', array(), 60));
    }

    protected function variationGroups_valueField(&$aField)
    {
        $aField['values'] = array('CrowdfoxPlaceholderCategory' => 'CrowdfoxPlaceholderCategory');
        $aField['value'] = 'CrowdfoxPlaceholderCategory';
    }

    protected function callGetCategoryDetails($sCategoryId)
    {
        return array();
    }

    protected function getMPAttributeValues($sCategoryId, $sMpAttributeCode, $sAttributeCode = false)
    {
        if ($sAttributeCode) {
            $shopValues = $this->getShopAttributeValues($sAttributeCode);
            foreach ($shopValues as $value) {
                $aValues[$value] = $value;
            }
        } else {
            $aValues = array();
        }

        return array(
            'values' => (isset($aValues) ? $aValues : array()),
            'from_mp' => false
        );
    }
}
