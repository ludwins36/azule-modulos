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

class ML_Ebay_Helper_Model_Table_Ebay_ConfigData extends ML_Form_Helper_Model_Table_ConfigData_Abstract {
    
    public function hitcounterField(&$aField){
        $aField['values'] = MLI18n::gi()->ebay_configform_prepare_hitcounter_values;
    }
    
    public function dispatchtimemaxField(&$aField){
        $aField['values'] = MLI18n::gi()->ebay_configform_prepare_dispatchtimemax_values;
    }
    
    public function siteField(&$aField){        
        $aField['values'] = array_merge(array(''=>'__') ,MLI18n::gi()->config_ebay_sites);
    }
    public function countryField(&$aField){
        $aCountryList = MLI18n::gi()->config_ebay_countries;
        asort($aCountryList);
        $aField['values'] = $aCountryList;
    }

    public function primaryCategoryField(&$aField) {
        $aField['values'] = MLDatabase::factory('ebay_categories')->getTopTenCategories('top'.$aField['name']);
    }

    public function currencyField (&$aField) {
        $aField['ajax']=array(
            'selector' => '#' . $this->getFieldId('site'),
            'trigger' => 'change',
            'field' => array(
                'type' => 'select',
            ),
        );
        $aField['values'] = MLFormHelper::getModulInstance()->getCurrencyValues();
    }
    
    public function fixed_durationField (&$aField) {
        $aField['values'] = MLFormHelper::getModulInstance()->getListingFixedDurations();
    }
     
    public function chinese_durationField (&$aField) {
        $aField['values'] = MLFormHelper::getModulInstance()->getListingChineseDurations();
    }
    
    public function orderstatus_closedField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getOrderStatusValues();
    }
    
    public function updateable_orderstatusField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getOrderStatusValues();
    }
        
    
    public function orderstatus_paidField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getOrderStatusValues();
    }
    
    public function orderstatus_carrier_defaultField (&$aField) {
        $aField['values'] = MLFormHelper::getModulInstance()->getCarrier();
    }
    
    public function inventorysync_priceField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('ebay_configform_pricesync_values');
    }
    
   public function stocksync_toMarketplaceField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('ebay_configform_sync_values');
    }
    
    public function stocksync_fromMarketplaceField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('ebay_configform_stocksync_values');
    }    
    
    public function chinese_stocksync_toMarketplaceField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('ebay_configform_sync_chinese_values');
    }
    
    public function chinese_stocksync_fromMarketplaceField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('ebay_configform_stocksync_values');
    }    
    
    public function chinese_inventorysync_priceField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('ebay_configform_pricesync_values');
    }
    
    /**
     * Gets Laguage list of amazon for config form.
     */
    public function langField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getDescriptionValues();
    }
    
    public function conditionIdField(&$aField) {
        $aField['values'] = MLModul::gi()->getConditionValues();
    }
    
    public function fixed_price_addKindField (&$aField) {
        $this->price_addKindField($aField);
    }
    
    public function fixed_price_factorField (&$aField) {
        $this->price_factorField($aField);
    }
    
    public function fixed_price_signalField (&$aField) {
        $this->price_signalField($aField);
    }
    
    public function fixed_price_groupField (&$aField) {
        $this->price_groupField($aField);
    }
    
    public function fixed_quantity_typeField (&$aField) {
        $this->quantity_typeField($aField);
    }
    
    public function fixed_quantity_valueField (&$aField) {        
        $aField['value'] = isset($aField['value']) ? trim($aField['value']) : 0;
        if (MLModul::gi()->getConfig('fixed.quantity.type') != 'stock' && (string)((int)$aField['value']) != $aField['value']) {
            $this->addError($aField, MLI18n::gi()->get('configform_quantity_value_error'));
        }
    }
    
        
    public function chinese_price_addKindField (&$aField) {
        $this->price_addKindField($aField);
    }
    
    public function chinese_price_factorField (&$aField) {
        $this->price_factorField($aField);
    }
    
    public function chinese_price_signalField (&$aField) {
        $this->price_signalField($aField);
    }
    
    public function chinese_price_groupField (&$aField) {
        $this->price_groupField($aField);
    }   
    public function chinese_buyitnow_price_addKindField (&$aField) {
        $this->price_addKindField($aField);
    }
    
    public function chinese_buyitnow_price_factorField (&$aField) {
        $this->price_factorField($aField);
    }
    
    public function chinese_buyitnow_price_signalField (&$aField) {
        $this->price_signalField($aField);
    }
    
    public function chinese_buyitnow_price_groupField (&$aField) {
        $this->price_groupField($aField);
    }   
        
    public function chinese_quantityField (&$aField) {
        $aField['value'] = MLI18n::gi()->ebay_configform_price_chinese_quantityinfo;
    }    
        
    public function orderstatus_cancelledField(&$aField) {
        $this->orderstatus_canceledField($aField);
    }
        
    public function orderimport_shippingmethodField (&$aField) {
        if(method_exists(MLFormHelper::getShopInstance(), 'getShippingMethodValues')){
            $aField['values'] = MLFormHelper::getShopInstance()->getShippingMethodValues();
        }else{
            $aField['values'] = MLI18n::gi()->get('ebay_configform_orderimport_shipping_values');
        }
    }
    
    public function orderimport_paymentmethodField (&$aField) {
        $aField['values'] = MLI18n::gi()->get('ebay_configform_orderimport_payment_values');
    }
    
    public function inventory_importField (&$aField) {
        $aField['values'] = MLI18n::gi()->ebay_config_sync_inventory_import;
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
    
    public function template_mobile_activeField(&$aField) {
        $aField['values'] = array(
            "true" => MLI18n::gi()->get('ML_BUTTON_LABEL_YES'),
            "false" => MLI18n::gi()->get('ML_BUTTON_LABEL_NO')
        );
    }
        
    public function importField (&$aField) {        
        $aField['value'] = isset($aField['value']) && in_array($aField['value'], array('true','false') )? $aField['value'] : 'true';
        $aField['values'] = array('true' => MLI18n::gi()->get('ML_BUTTON_LABEL_YES'),'false' => MLI18n::gi()->get('ML_BUTTON_LABEL_NO'));
    }
        
    public function productfield_brandField(&$aField) {
        $aField['values'] = MLFormHelper::getShopInstance()->getBrand();
    }
    
    public function ebayplusField (&$aField) {        
        $aField['disabled'] = true;
        $aSetting = MLModul::gi()->getEBayAccountSettings();
        if(isset($aSetting['eBayPlus']) && $aSetting['eBayPlus'] == "true"){
            $aField['disabled'] = false;
        }
    }
    
    public function variationDimensionForPicturesField (&$aField) {
        if (MLShop::gi()->addonBooked('EbayPicturePack')) {
            $aField['type'] = 'select';
            $aField['values'] = array();
            foreach(MLFormHelper::getShopInstance()->getPossibleVariationGroupNames() as $iKey => $sValue) {
                $aField['values'][$iKey] = $sValue;
            }
        }
    } 
    
    public function galleryTypeField (&$aField) {
        $aField['values'] = MLI18n::gi()->ebay_configform_prepare_gallerytype_values;
    } 
   
}
