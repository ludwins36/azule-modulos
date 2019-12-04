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
* (c) 2010 - 2018 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
MLFilesystem::gi()->loadClass('Form_Helper_Model_Table_ConfigData_Abstract');

class ML_Etsy_Helper_Model_Table_Etsy_ConfigData extends ML_Form_Helper_Model_Table_ConfigData_Abstract {

    public function shippingtemplateField(&$aField) {
        $shippingTemplates = $this->callApi(array('ACTION' => 'GetShippingTemplates'), 12 * 12 * 60);

        if (isset($shippingTemplates['ShippingTemplates'])) {
            foreach ($shippingTemplates['ShippingTemplates'] as $shippingTemplate) {
                $aField['values'][$shippingTemplate['shippingTemplateId'].''] = $shippingTemplate['title'];
            }
        } else {
            $aField['values'][] = 'No shipping template created';
        }

    }

    public function shippingtemplatecountryField(&$aField) {
        $countries = $this->callApi(array('ACTION' => 'GetCountries'), 100);

        if (isset($countries['OriginCountries'])) {
            foreach ($countries['OriginCountries'] as $country) {
                $aField['values'][$country['countryId']] = $country['name'];
            }
        } else {
            $aField['values'][] = 'No origin country';
        }
    }

    public function fixed_price_addkindField(&$aField) {
        $this->price_addKindField($aField);
    }

    public function langField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getDescriptionValues();
    }

    public function primaryCategoryField(&$aField) {
        $aField['values'] = MLDatabase::factory('etsy_prepare')->getTopPrimaryCategories();
    }

    public function imagesizeField(&$aField) {
        $aField['values'] =  array(
            500 => '500px',
            600 => '600px',
            700 => '700px',
            800 => '800px',
            900 => '900px',
            1000 => '1000px',
            1200 => '1200px',
            1300 => '1300px',
            1400 => '1400px',
            1500 => '1500px',
            1600 => '1600px',
            1700 => '1700px',
            1800 => '1800px',
            1900 => '1900px',
            2000 => '2000px',
            2100 => '2100px',
            2200 => '2200px',
            2300 => '2300px',
            2400 => '2400px',
            2500 => '2500px',
            2600 => '2600px',
            2700 => '2700px',
            2800 => '2800px',
            2900 => '2900px',
            3000 => '3000px',
        );
    }
}
