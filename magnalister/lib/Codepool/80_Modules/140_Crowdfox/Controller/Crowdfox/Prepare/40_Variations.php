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

class ML_Crowdfox_Controller_Crowdfox_Prepare_Variations extends ML_Form_Controller_Widget_Form_VariationsAbstract
{
    protected $numberOfMaxAdditionalAttributes = self::UNLIMITED_ADDITIONAL_ATTRIBUTES;

    public function saveAction($blExecute = true)
    {
        parent::saveAction($blExecute);
        MLRequest::gi()->set('resetForm', false, true);
    }

    public function resetAction($blExecute = true)
    {
        if ($blExecute) {
            $aActions = $this->getRequest($this->sActionPrefix);
            $reset = $aActions['resetaction'] === '1';
            if ($reset) {
                $aMatching = $this->getRequestField();
                $sIdentifier = $aMatching['variationgroups.value'];
                if ($sIdentifier === 'none') {
                    MLMessage::gi()->addSuccess(self::getMessage('_prepare_variations_saved'));
                    return;
                }

                $oVariantMatching = $this->getVariationDb();
                $oVariantMatching->deleteVariation($sIdentifier);
                MLRequest::gi()->set('resetForm', true);
            }
        }
    }

    protected function variationGroups_ValueField(&$aField)
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

    protected function checkAttributesFromDB($sIdentifier, $sIdentifierName)
    {
        // need to stay empty, because Crowdfox does not have categories
    }
}
