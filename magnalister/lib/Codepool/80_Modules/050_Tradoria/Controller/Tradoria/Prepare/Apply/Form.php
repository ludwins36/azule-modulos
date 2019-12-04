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

class ML_Tradoria_Controller_Tradoria_Prepare_Apply_Form extends ML_Form_Controller_Widget_Form_PrepareWithVariationMatchingAbstract {

    protected $numberOfMaxAdditionalAttributes = self::UNLIMITED_ADDITIONAL_ATTRIBUTES;

    protected function getSelectionNameValue() {
        return 'match';
    }

    protected function shippingServiceField(&$aField) {
        $aField['values'] = ML::gi()->instance('controller_tradoria_config_prepare')->getField('shippingservice', 'values');
    }

    protected function categoriesField(&$aField) {
        $aField['subfields']['primary']['values'] = array('' => '..')
            + ML::gi()->instance('controller_tradoria_config_prepare')->getField('primarycategory', 'values');
        foreach ($aField['subfields'] as &$aSubField) {
            //adding current cat, if not in top cat
            if (!array_key_exists($aSubField['value'], $aSubField['values'])) {
                $oCat = MLDatabase::factory('tradoria_categories' . $aSubField['cattype']);
                $oCat->init(true)->set('categoryid', $aSubField['value']);
                $sCat = '';
                foreach ($oCat->getCategoryPath() as $oParentCat) {
                    $sCat = $oParentCat->get('categoryname') . ' &gt; ' . $sCat;
                }

                $aSubField['values'][$aSubField['value']] = substr($sCat, 0, -6);
            }
        }
    }

    protected function callGetCategoryDetails($sCategoryId) {
        return array();
    }

    protected function getMPAttributeValues($sCategoryId, $sMpAttributeCode, $sAttributeCode = false) {
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
            'from_mp' => false,
        );
    }
}
