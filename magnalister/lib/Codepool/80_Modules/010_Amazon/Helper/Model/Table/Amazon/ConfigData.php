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
MLFilesystem::gi()->loadClass('Form_Helper_Model_Table_ConfigData_Abstract');

class ML_Amazon_Helper_Model_Table_Amazon_ConfigData extends ML_Form_Helper_Model_Table_ConfigData_Abstract {
    
    /**
     * Gets Site list of amazon for config form.
     * 
     * @param array $aField
     */
    public function siteField(&$aField) {
        foreach (MLModul::gi()->getMarketPlaces() as $aMarketplace) {
            $aField['values'][$aMarketplace['Key']] = fixHTMLUTF8Entities($aMarketplace['Label']);
        }
    }   
    
    /**
     * Gets Currency list of amazon for config form.
     */
    public function currencyField(&$aField) {
        foreach (MLModul::gi()->getMarketPlaces() as $aMarketplace) {
            $aField['values'][$aMarketplace['Key']] = fixHTMLUTF8Entities($aMarketplace['Currency']);
        }
    }
    /**
     * Gets Laguage list of amazon for config form.
     */
    public function langField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getDescriptionValues();
    }
    
    public function internationalShippingField(&$aField) {
        $aField['values'] = MLFormHelper::getModulInstance()->getShippingLocationValues();
    }
    
    public function leadtimetoshipField(&$aField) {
        $aField['values'][0] = '&mdash;';
        for ($i = 1; $i < 31; $i++) {
             $aField['values'][$i] = $i;
        }
        
    }
    public function orderstatus_syncField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('amazon_configform_orderstatus_sync_values');
    }
    public function stocksync_toMarketplaceField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('amazon_configform_sync_values');
    }
    
    public function stocksync_fromMarketplaceField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('amazon_configform_stocksync_values');
    }    
    
    public function inventorysync_priceField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('amazon_configform_pricesync_values');
    }
    
    public function importField (&$aField) {
        $aField['value'] = isset($aField['value']) && in_array($aField['value'], array('true','false') )? $aField['value'] : 'true';
        $aField['values'] = array('true' => MLI18n::gi()->get('ML_BUTTON_LABEL_YES'),'false' => MLI18n::gi()->get('ML_BUTTON_LABEL_NO'));
    }
    
    public function orderstatus_carrier_defaultField(&$aField){
        $aField['ajax']=array(
            'selector' => '#' . $this->getFieldId('orderstatus.carrier.additional'),
            'trigger' => 'change',
            'field' => array(
                'type' => 'select',
            ),
        );
        $aField['values'] = MLFormHelper::getModulInstance()->getCarrierCodeValues();
    }
    
    public function mail_sendField(&$aField) {
        $aField['values'] = array(
            "true" => MLI18n::gi()->get('ML_BUTTON_LABEL_YES'),
            "false" => MLI18n::gi()->get('ML_BUTTON_LABEL_NO'));
    }    
    
    public function mail_copyField(&$aField) {
        $aField['values'] = array(
            "true" => MLI18n::gi()->get('ML_BUTTON_LABEL_YES'),
            "false" => MLI18n::gi()->get('ML_BUTTON_LABEL_NO'));
    }
    
    public function orderstatus_fbaField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getOrderStatusValues();
    }
    
    public function orderstatus_cancelledField(&$aField) {
        $this->orderstatus_canceledField($aField);
    }
    
    public function orderimport_shippingmethodField (&$aField) {
        if(method_exists(MLFormHelper::getShopInstance(), 'getShippingMethodValues')){
            $aField['values'] = MLFormHelper::getShopInstance()->getShippingMethodValues();
        }else{
            $aField['values'] = MLI18n::gi()->get('amazon_configform_orderimport_shipping_values');
        }        
    }
    
    public function orderimport_paymentmethodField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('amazon_configform_orderimport_payment_values');
    }
    
    public function orderimport_fbashippingmethodField (&$aField) {
        $this->orderimport_shippingmethodField($aField);
    }
    
    public function orderimport_fbapaymentmethodField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('amazon_configform_orderimport_payment_values');
    }
    
    public function shippingservice_carrierwillpickupField(&$aField) {
        $aService = MLModul::gi()->MfsGetConfigurationValues('ServiceOptions');
        $aField['values'] = array_key_exists('CarrierWillPickUp', $aService) ? $aService['CarrierWillPickUp'] : array();
    }
    
    public function shippingservice_deliveryexperienceField(&$aField) {
        $aService = MLModul::gi()->MfsGetConfigurationValues('ServiceOptions');
        $aField['values'] = array_key_exists('DeliveryExperience', $aService) ? $aService['DeliveryExperience'] : array();
    }
     
    public function shippinglabel_size_unitField(&$aField) {
        $aField['values'] = MLModul::gi()->MfsGetConfigurationValues('SizeUnits');
    }
     
    public function shippinglabel_weight_unitField(&$aField) {
        $aField['values'] = MLModul::gi()->MfsGetConfigurationValues('WeightUnits');
    }
    
    public function shippinglabel_address_countryField(&$aField) {
        $aField['values'] = MLModul::gi()->MfsGetConfigurationValues('Countries');
    }

    public function b2b_price_addKindField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('configform_price_addkind_values');
    }

    public function b2b_price_factorField (&$aField) {
        $aField['value'] = isset($aField['value']) ? str_replace(',', '.',trim($aField['value'])) : 0;
        if ((string)((float)$aField['value']) != $aField['value']) {
            $this->addError($aField, MLI18n::gi()->get('configform_price_factor_error'));
        } else {
            $aField['value'] = number_format($aField['value'], 2);
        }
    }

    public function b2b_price_signalField (&$aField) {
        $aField['value'] = isset($aField['value']) ? str_replace(',', '.',trim($aField['value'])) : '';
        if (!empty($aField['value']) && (string)((int)$aField['value']) != $aField['value']) {
            $this->addError($aField, MLI18n::gi()->get('configform_price_signal_error'));
        }
    }

    public function b2b_price_groupField (&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getCustomerGroupValues();
    }

    public function b2b_tax_code_categoryField(&$aField)
    {
        $aField['values'] = array('' => MLI18n::gi()->get('form_type_matching_select_optional'))
            + MLModul::gi()->getMainCategories();
    }

    public function b2bselltoField(&$aField) {
        $aField['values'] = $aField['i18n']['values'];
    }

    public function b2bdiscounttypeField(&$aField) {
        $aField['values'] = $aField['i18n']['values'];
    }

    public function b2b_tax_codeField(&$aField) {
        $this->setTaxMatchingField($aField);
    }
    
    public function b2b_tax_code_specificField(&$aField)
    {
        $catSelector = 'b2b.tax_code_category';

        $aField['ajax'] = array(
            'selector' => '#' . $this->getFieldId($catSelector),
            'trigger' => 'change',
            'duplicated' => true,
            'field' => array(
                'type' => 'matching',
            ),
        );

        $aField['cssclass'] = 'js-b2b';

        $selectedCat = $this->getRequestField($catSelector);
        if ($selectedCat) {
            $category = reset($selectedCat);
            if (isset($aField['postname'])) {
                // this means ajax call. field is inside "duplicate" so it has "[X]" suffix
                // and this is index of value for selected category
                $oldName = explode('][', $aField['postname']);
                $valueKey = rtrim(end($oldName), ']');
                if (isset($selectedCat[$valueKey])) {
                    $category = $selectedCat[$valueKey];
                }
            }
        } else {
            $category = MLModul::gi()->getConfig($catSelector);
            $category = $category ? reset($category) : '';
        }

        $this->setTaxMatchingField($aField, $category);
        if (empty($category)) {
            // hide field. do not add type => hidden because we need the field for ajax to work properly.
            $aField['cssclass'] = 'hide';
        }
    }

    private function setTaxMatchingField(&$aField, $category = '') {
        $shopTaxes = MLFormHelper::getShopInstance()->getTaxClasses();
        $aField['valuessrc'] = array();
        if ($shopTaxes) {
            foreach ($shopTaxes as $tax) {
                $aField['valuessrc'][$tax['value']] = array('i18n' => $tax['label'], 'required' => true);
            }
        }

        $aField['valuesdst'] = array('' => MLI18n::gi()->get('form_type_matching_select_optional'))
            + $this->callApi(array(
                'ACTION' => 'GetB2BProductTaxCode',
                'CATEGORY' => $category,
            ), 60);
    }
    
    public function itemConditionField(&$aField){
        $aField['values'] = MLFormHelper::getModulInstance()->getConditionValues();
    }
}
