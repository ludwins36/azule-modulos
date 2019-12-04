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
MLFilesystem::gi()->loadClass('Shop_Model_ConfigForm_Shop_Abstract');

class ML_Prestashop_Model_ConfigForm_Shop extends ML_Shop_Model_ConfigForm_Shop_Abstract {

    public function getDescriptionValues() {
        $aLangs = array();
        foreach (Language::getLanguages(true) as $aRow) {
            $aLangs[$aRow['id_lang']] = $aRow['name'];
        }

        return $aLangs;
    }
    
    public function getShopValues() {
        $aShops = array();
        foreach (Shop::getShops(true) as $aRow) {
            $aShops[$aRow['id_shop']] = $aRow['name'];
        }

        return $aShops;
    }

    public function getCustomerGroupValues($blNotLoggedIn = false) {
        $aGroupsName = array();
        foreach (Group::getGroups(_LANG_ID_) as $aRow) {
            $aGroupsName[$aRow['id_group']] = $aRow['name'];
        }

        return $aGroupsName;
    }

    public function getOrderStatusValues() {
        $aOrderStatesName = array();
        foreach (OrderState::getOrderStates(_LANG_ID_) as $aRow) {
            $aOrderStatesName[$aRow['id_order_state']] = $aRow['name'];
        }

        return $aOrderStatesName;
    }

    public function getEan() {
        return array(
            '' => MLI18n::gi()->get('ConfigFormEmptySelect'),
            'ean13' => 'EAN', 
            'upc' => 'UPC'
        );
    }

    public function getUpc() {
        return $this->getProductFields();
    }

    public function getMarketingDescription() {
        return $this->getProductFields();
    }

    public function getManufacturer() {
        return array(
            '' => MLI18n::gi()->get('ConfigFormEmptySelect'),
            'manufacturer_name' => 'Manufacturer Name',
            'id_manufacturer' => 'Manufacturer Id'
        );
        /*
        $aOut = array();
        foreach (Manufacturer::getManufacturers() as $aManufacturer ) {
            if ($aManufacturer['id_manufacturer'] != '') {
                $aOut[$aManufacturer['id_manufacturer']] = array (
                    'value' => $aManufacturer['id_manufacturer'] ,
                    'label' => $aManufacturer['name'] ,
                ) ;
            }
        }
        return $aOut;
        */
    }

    public function getManufacturerPartNumber() {
        return $this->getProductFields();
    }
    
    public function getBrand() {
        return $this->getProductFields();
    }
    
    public function getShippingTime() {
        return $this->getProductFields();
    }


    /**
     * get all the available fields for products and combination 
     */
    protected function getProductFields() {
        $oProduct = new Product();
        $oAttribute = new Combination();
        $aFields = array_merge(
                get_object_vars($oProduct), get_object_vars($oAttribute)
        );
        ksort($aFields);
        foreach ($aFields as $sField => &$sValue) {
            $sValue = ucfirst(str_replace('_', ' ', $sField));
        }
        $aFields = array('' => MLI18n::gi()->get('ConfigFormEmptySelect')) + $aFields;
        $aFeatures = Feature::getFeatures(Context::getContext()->language->id);
        $sFeaturesTranslation = Translate::getAdminTranslation('Features', 'AdminProducts');
        foreach ($aFeatures as $aFeature) {
            $aFields['product_feature_' . $aFeature['id_feature']] = $aFeature['name'] . ' (' . $sFeaturesTranslation . ')';
        }
        return $aFields;
    }

    /**
     * Gets list of custom attributes from shop.
     *
     * @return array Collection of all attributes
     */
    public function getAttributeList() {
        // TODO: implement this for Prestashop if applicable.
        return array();
    }

    /**
     * Gets the list of product attributes prefixed with attribute type.
     *
     * @param bool $getProperties Indicates whether to get properties with attributes
     * @TODO Check all usage of this method and if properties should always be present, remove this parameter,
     *      since it is used only in Shopware.
     *
     * @return array Collection of prefixed attributes
     */
    public function getPrefixedAttributeList($getProperties = false) {
        $aAttributes = array('' => MLI18n::gi()->get('ConfigFormEmptySelect'));

        $oProduct = new Product();
        $oAttribute = new Combination();
        $aFields = array_merge(
            get_object_vars($oProduct), get_object_vars($oAttribute)
        );
        ksort($aFields);
        foreach ($aFields as $sField => &$sValue) {
            $aAttributes[$sField] = ucfirst(str_replace('_', ' ', $sField));
        }

        foreach (AttributeGroup::getAttributesGroups(_LANG_ID_) as $aRow) {
            $aAttributes['a_'.$aRow['id_attribute_group']] = $aRow['name'];
        }

        foreach (Feature::getFeatures(_LANG_ID_) as $aRow) {
            $aAttributes['f_'.$aRow['id_feature']] = $aRow['name'];
        }

        return $aAttributes;
    }

    /**
     * Gets the list of product attributes that have options (displayed as dropdown or multiselect fields).
     *
     * @return array Collection of attributes with options
     */
    public function getAttributeListWithOptions() {
        $aAttributes = array('' => MLI18n::gi()->get('ConfigFormEmptySelect'));
        foreach (AttributeGroup::getAttributesGroups(_LANG_ID_) as $aRow) {
            $aAttributes['a_' . $aRow['id_attribute_group']] = $aRow['name'];
        }
        
        return $aAttributes;
    }

    /**
     * Gets the list of product attribute values.
     * If $iLangId is set, use translation for attribute options' labels.
     *
     * @return array Collection of attribute values
     */
    public function getAttributeOptions($sAttributeCode, $iLangId = _LANG_ID_) {
        $aAttributeCode = explode('_', $sAttributeCode, 2);
        $aAttributes = array();

        // Getting values for manufacturer, supplier, tag and condition
        if ($sAttributeCode === 'manufacturer_name') {
            foreach (Manufacturer::getManufacturers(false, $iLangId) as $aManufacturer) {
                if ($aManufacturer['id_manufacturer'] != '') {
                    $aAttributes[$aManufacturer['id_manufacturer']] = $aManufacturer['name'];
                }
            }
        } elseif ($sAttributeCode === 'supplier_name') {
            foreach (Supplier::getSuppliers(false, $iLangId) as $aSupplier) {
                if ($aSupplier['id_supplier'] != '') {
                    $aAttributes[$aSupplier['id_supplier']] = $aSupplier['name'];
                }
            }
        } elseif ($sAttributeCode === 'tags') {
            $aResultTags = MLDatabase::factorySelectClass()
                ->select('*')
                ->from(_DB_PREFIX_ . 'tag')
                ->where("id_lang = $iLangId")
                ->getResult();

            foreach ($aResultTags as $aTag) {
                if ($aTag['id_tag'] != '') {
                    $aAttributes[$aTag['id_tag']] = $aTag['name'];
                }
            }
        } elseif ($sAttributeCode === 'condition') {
            // This is also hardcoded in Prestashop
            $aAttributes = array(
                'new' => MLI18n::gi()->get('ConditionNew'),
                'used' => MLI18n::gi()->get('ConditionUsed'),
                'refurbished' => MLI18n::gi()->get('ConditionRefurbished')
            );
        } elseif ($sAttributeCode === 'available_for_order') {
            $aAttributes = array(1 => MLI18n::gi()->get('ML_BUTTON_LABEL_YES'), 0 => MLI18n::gi()->get('ML_BUTTON_LABEL_NO'));
        }

        // Getting values for variation attributes
        if ($aAttributeCode[0] === 'a') {
            foreach (AttributeGroup::getAttributes($iLangId, $aAttributeCode[1]) as $aRow) {
                $aAttributes[$aRow['id_attribute']] = $aRow['name'];
            }
        }

        // Getting values for product feature
        if ($aAttributeCode[0] === 'f' && $this->isFeatureCustom($aAttributeCode[1]) === false) {
            foreach ($this->getFeatureOptions($aAttributeCode[1], $iLangId) as $aRow) {
                $aAttributes[$aRow['id_feature_value']] = $aRow['value'];
            }
        }

        return $aAttributes;
    }

    /**
     * Gets the list of product attribute values.
     * If $iLangId is set, use translation for attribute options' labels.
     *
     * @return array Collection of attribute values
     */
    public function getPrefixedAttributeOptions($sAttributeCode, $iLangId = _LANG_ID_) {
        $aAttributes = array();
        $aAttributeCode = explode('_', $sAttributeCode);

        if ($aAttributeCode[0] === 'a') {
            foreach (AttributeGroup::getAttributes($iLangId, $aAttributeCode[1]) as $aRow) {
                $aAttributes[$aRow['id_attribute']] = $aRow['name'];
            }
        } else if ($aAttributeCode[0] === 'f' && $this->isFeatureCustom($aAttributeCode[1]) === false) {
            foreach ($this->getFeatureOptions($aAttributeCode[1], $iLangId) as $aRow) {
                $aAttributes[$aRow['id_feature_value']] = $aRow['value'];
            }
        }
        
        return $aAttributes;
    }

    public function getCurrency() {
        MLShop::gi()->getCurrency()->getList();
    }

    /**
     * Gets tax classes for product.
     *
     * @return array Tax classes
     * array(
     *  array(
     *      'value' => string
     *      'label' => string
     *  )
     * )
     */
    public function getTaxClasses() {
        $aTaxGroup = array();
        foreach (TaxRulesGroup::getTaxRulesGroupsForOptions() as $iId => $sName) {
            $aTaxGroup[] = array(
                'value'=> $sName['id_tax_rules_group'] ,
                'label'=> $sName['name']
            );
        }
        return $aTaxGroup;
    }

    private function getFeatureOptions($iFeatureId, $iLangId = _LANG_ID_) {
        return MLDatabase::factorySelectClass()
            ->select(array('v.id_feature_value', 'l.value'))
            ->from(_DB_PREFIX_ . 'feature_value', 'v')
            ->join(array(_DB_PREFIX_ . 'feature_value_lang', 'l', 'v.id_feature_value = l.id_feature_value', ML_Database_Model_Query_Select::JOIN_TYPE_LEFT))
            ->where("l.id_lang = $iLangId and v.id_feature = $iFeatureId")
            ->getResult();
    }

    private function isFeatureCustom($iFeatureId) {
        $result = MLDatabase::factorySelectClass()
            ->select('custom')
            ->from(_DB_PREFIX_ . 'feature_value')
            ->where("id_feature = $iFeatureId")
            ->getResult();

        if (!empty($result)) {
            foreach ($result as $value) {
                if ((int)$value['custom'] === 0) {
                    return false;
                }
            }
        }

        return true;
	}
        
    public function manipulateForm(&$aForm) {
        try{
            parent::manipulateForm($aForm);
            MLModul::gi();//throw excepton if we are not in marketplace configuration

//                if(isset($aForm['account'])){
//                    $aForm['account']['fields']['orderimport.shop'] =  array
//                        (
//                            'name' => 'orderimport.shop',
//                            'type' => 'select',
//                            'i18n'=>array(
//                                'label' => 'Shop'
//                            )
//                        );
//                }
            if(isset($aForm['importactive'])){
                foreach ($aForm['importactive']['fields'] as $sKey => $aField){
                    if($aField['name'] == 'orderimport.shop'){
                        unset($aForm['importactive']['fields'][$sKey]);
                    }
                }
            }
        } catch (Exception $ex) {

        }
    }

    public function getPossibleVariationGroupNames () {
        $aAttributes = array('' => MLI18n::gi()->get('ConfigFormEmptySelect'));
        foreach (AttributeGroup::getAttributesGroups(_LANG_ID_) as $aRow) {
            $aAttributes[$aRow['id_attribute_group']] = $aRow['name'];
        }
        return $aAttributes;
    }
    
    public function getShippingMethodValues(){
        $aCarriers = array();
        foreach(Carrier::getCarriers(Context::getContext()->language->id) as $aCariers){
            $aCarriers[$aCariers['id_carrier']] = $aCariers['name'];
        }
        return $aCarriers;
    }

    /**
     * Returns grouped attributes for attribute matching
     *
     * @return array
     */
    public function getGroupedAttributesForMatching() {
        $aShopAttributes = array();

        // First element is pure text that explains that nothing is selected so it should not be added
        // nor in Properties or Variations, it is spliced and used just for forming the final array.
        $aFirstElement = array('' => MLI18n::gi()->get('ConfigFormEmptySelect'));

        // Variation attributes
        $aShopVariationAttributes = $this->getVariationAttributes();
        if (!empty($aShopVariationAttributes)) {
            $aShopVariationAttributes['optGroupClass'] = 'variation';
            $aShopAttributes += array(MLI18n::gi()->get('VariationsOptGroup') => $aShopVariationAttributes);
        }

        // Product default fields
        $aShopDefaultFieldsAttributes = $this->getDefaultFieldsAttributes();
        if (!empty($aShopDefaultFieldsAttributes)) {
            $aShopDefaultFieldsAttributes['optGroupClass'] = 'default';
            $aShopAttributes += array(MLI18n::gi()->get('ProductDefaultFieldsOptGroup') => $aShopDefaultFieldsAttributes);
        }

        // Product features
        $aShopProductFeatures = $this->getProductFeatures();
        if (!empty($aShopProductFeatures)) {
            $aShopProductFeatures['optGroupClass'] = 'property';
            $aShopAttributes += array(MLI18n::gi()->get('PropertiesOptGroup') => $aShopProductFeatures);
        }

        return $aFirstElement + $aShopAttributes;
    }

    /**
     * Returns flat attribute if attribute code is sent and if not it returns all shop attributes for attribute matching
     * @param string $attributeCode
     * @param ML_Shop_Model_Product_Abstract|null $product If present attribute value will be set from given product
     * @return array|mixed
     */
    public function getFlatShopAttributesForMatching($attributeCode = null, $product = null)
    {
        $result = $this->getVariationAttributes() + $this->getDefaultFieldsAttributes() + $this->getProductFeatures();
        if (!empty($attributeCode) && !empty($result[$attributeCode])) {
            $result = $result[$attributeCode];
            if (!empty($product)) {
                $result['value'] = $product->getAttributeValue($attributeCode);
                // Because of special handling of condition on prepare form we need to add this special case, otherwise
                // value returned from shop is key, not value. See ML_Prestashop_Model_ConfigForm_Shop::getAttributeOptions for
                // more details
                if ($attributeCode === 'condition') {
                    $allConditionValues = array(
                        'new' => MLI18n::gi()->get('ConditionNew'),
                        'used' => MLI18n::gi()->get('ConditionUsed'),
                        'refurbished' => MLI18n::gi()->get('ConditionRefurbished')
                    );
                    if (!empty($allConditionValues[$result['value']])) {
                        $result['value'] = $allConditionValues[$result['value']];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Returns variation attributes for attribute matching
     *
     * @return array
     */
    private function getVariationAttributes() {
        $aShopVariationAttributes = array();

        foreach (AttributeGroup::getAttributesGroups(_LANG_ID_) as $aRow) {
            $aShopVariationAttributes['a_' . $aRow['id_attribute_group']] = array(
                'name' => $aRow['name'],
                'type' => 'select',
            );
        }

        return $aShopVariationAttributes;
    }

    /**
     * Returns default product fields for attribute matching
     *
     * @return array
     */
    private function getDefaultFieldsAttributes() {
        $aShopDefaultFieldsAttributes = array();

        $oProduct = new Product();
        $oAttribute = new Combination();
        $aFields = array_merge(
            get_object_vars($oProduct), get_object_vars($oAttribute)
        );
        ksort($aFields);

        foreach ($aFields as $sField => &$sValue) {
            // If attribute is in ignore list, skip that attribute
            if ($this->attributeIsInIgnoreList($sField)) {
                continue;
            }

            // Manufacturer, supplier and tags are only attributes that are different by type from other attributes
            if (in_array($sField, array('manufacturer_name', 'supplier_name', 'condition', 'available_for_order'))) {
                $sType = 'select';
            } elseif ($sField === 'tags') {
                $sType = 'multiSelect';
            } else {
                $sType = 'text';
            }

            $aShopDefaultFieldsAttributes[$sField] = array(
                'name' => ucfirst(str_replace('_', ' ', $sField)),
                'type' => $sType
            );
        }

        return $aShopDefaultFieldsAttributes;
    }

    /**
     * Returns product features for attribute matching
     *
     * @return array
     */
    private function getProductFeatures() {
        $aShopProductFeatures = array();

        foreach (Feature::getFeatures(_LANG_ID_) as $aRow) {
            if ($this->isFeatureCustom($aRow['id_feature']) === false) {
                $sType = 'selectAndText';
            } else {
                $sType = 'text';
            }

            $aShopProductFeatures["f_{$aRow['id_feature']}"] = array(
                'name' => $aRow['name'],
                'type' => $sType,
            );
        }

        return $aShopProductFeatures;
    }

    /**
     * Checks if attribute is in ignore list
     *
     * @param $attribute
     * @return bool
     */
    private function attributeIsInIgnoreList($attribute)
    {
        $ignoreList = array(
            'active',
            'advanced_stock_management',
            'available_later',
            'available_now',
            'category',
            'cache_default_attribute',
            'cache_has_attachments',
            'cache_is_pack',
            'customizable',
            'date_add',
            'date_upd',
            'default_on',
            'depends_on_stock',
            'id',
            'id_category_default',
            'id_color_default',
            'id_manufacturer',
            'id_pack_product_attribute',
            'id_product_redirected',
            'id_shop_default',
            'id_shop_list',
            'id_supplier',
            'id_tax_rules_group',
            'indexed',
            'isFullyLoaded',
            'is_virtual',
            'force_id',
            'link_rewrite',
            'new',
            'on_sale',
            'online_only',
            'out_of_stock',
            'pack_stock_type',
            'redirect_type',
            'show_price',
            'text_fields',
            'uploadable_files',
            'visibility',
        );

        return in_array($attribute, $ignoreList);
    }

    public function shouldBeDisplayedAsVariationAttribute($sAttributeKey) {
        return substr($sAttributeKey, 0, 2) === 'a_';
    }
}
