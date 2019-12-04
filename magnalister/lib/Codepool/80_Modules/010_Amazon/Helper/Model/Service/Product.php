<?php

class ML_Amazon_Helper_Model_Service_Product {
    protected $aModul = null;
    
    /**
     *
     * @var ML_Amazon_Model_Table_Amazon_Prepare $oPrepare
     */
    protected $oPrepare = null;
    
    /**
     *
     * @var ML_Amazon_Helper_Model_Table_Amazon_PrepareData 
     */
    protected $oPrepareDataHelper = null;
    
    /**
     *
     * @var ML_Shop_Model_Product_Abstract $oProduct
     */
    protected $oProduct = null;
    /**
     *
     * @var array $aVariants of ML_Shop_Model_Product_Abstract
     */
    protected $aVariants = array();
    /**
     *
     * @var ML_Shop_Model_Product_Abstract $oCurrentProduct
     */
    protected $oCurrentProduct = null;

    protected $sPrepareType = '';
    protected $aData = null;

    /**
     *
     * @var ML_Modul_Model_Modul_Abstract $oMarketplace
     */
    protected $oMarketplace = null;

    protected $aAttributes = array();

    public function __call($sName, $mValue) {
        return $sName.'()';
    }

    public function __construct() {
        $this->aModul = MLModul::gi()->getConfig();
        $this->oPrepare = MLDatabase::factory('amazon_prepare');
        $this->oMarketplace = MLModul::gi();
        $this->oPrepareDataHelper = MLHelper::gi('model_table_amazon_preparedata');
    }

    public function setProduct(ML_Shop_Model_Product_Abstract $oProduct) {
        $this->oProduct = $oProduct;
        $this->aVariants = array();
        $this->sPrepareType = '';
        $this->aData = null;
        return $this;
    }

    public function addVariant(ML_Shop_Model_Product_Abstract $oProduct) {
        $this->aVariants[] = $oProduct;
        return $this;
    }

    public function getData() {
        if ($this->aData === null) {
            $aData = $aApplyVariantsData = array();
            foreach ($this->aVariants as $oVariant) {
                /* @var $oVariant ML_Shop_Model_Product_Abstract */
                $this->oPrepare->init()->set('productsid', $oVariant->get('id'));
                $this->setPrepareType($this->oPrepare->get('preparetype'));
                $this->oCurrentProduct = $oVariant;
                if ($this->sPrepareType == 'apply') {
                    $aVariantData = array();
                    $aVariantData['Variation'] = $this->getVariation();
                    if ($this->variationShouldBeExcluded($aVariantData['Variation'])) {
                        continue;
                    }
                    foreach (array(
                                 'SKU',
                                 'Price',
                                 'BusinessFeature',
                                 'BusinessPrice',
                                 'ProductTaxCode',
                                 'QuantityPriceType',
                                 'QuantityLowerBound1',
                                 'QuantityPrice1',
                                 'QuantityLowerBound2',
                                 'QuantityPrice2',
                                 'QuantityLowerBound3',
                                 'QuantityPrice3',
                                 'QuantityLowerBound4',
                                 'QuantityPrice4',
                                 'QuantityLowerBound5',
                                 'QuantityPrice5',
                                 'Currency',
                                 'Quantity',
                                 'EAN',
                                 'Variation',
                                 'Attributes',
                                 'ShippingTime',
                                 'ManufacturerPartNumber',
                                 'Images',
                                 'BasePrice',
                                 'Weight',
                                 'variation_theme',
                             ) as $sField) {
                        $aVariantData[$sField] = $this->{'get'.$sField}();
                    }
                    foreach (array('BasePrice', 'Weight') as $sKey) {
                        if (empty($aVariantData[$sKey])) {
                            unset($aVariantData[$sKey]);
                        }
                    }

                    $this->checkBusinessFeature($aVariantData);

                    //Condition and product type goes as standard fields
                    if (isset($aVariantData['Attributes']['ConditionType'])) {
                        unset($aVariantData['Attributes']['ConditionType']);
                    }

                    if (isset($aVariantData['Attributes']['ConditionNote'])) {
                        unset($aVariantData['Attributes']['ConditionNote']);
                    }

                    if (isset($aVariantData['Attributes']['ProductType'])) {
                        unset($aVariantData['Attributes']['ProductType']);
                    }

                    $aApplyVariantsData[] = $aVariantData;
                } else {//match
                    $aVariant = array();
                    foreach (array(
                                 'Id',/*use as index in additem */
                                 'SKU',
                                 'ASIN',
                                 'ConditionType',
                                 'ConditionNote',
                                 'Price',
                                 'BusinessFeature',
                                 'BusinessPrice',
                                 'ProductTaxCode',
                                 'QuantityPriceType',
                                 'QuantityLowerBound1',
                                 'QuantityPrice1',
                                 'QuantityLowerBound2',
                                 'QuantityPrice2',
                                 'QuantityLowerBound3',
                                 'QuantityPrice3',
                                 'QuantityLowerBound4',
                                 'QuantityPrice4',
                                 'QuantityLowerBound5',
                                 'QuantityPrice5',
                                 'Quantity',
                                 'WillShipInternationally',
                                 'ShippingTime',
                                 'variation_theme',
                             ) as $sField) {
                        $aVariant[$sField] = $this->{'get'.$sField}();
                    }

                    $this->checkBusinessFeature($aVariant);

                    $aData[] = $aVariant;
                }
            }
            if ($this->sPrepareType == 'apply') {//add master
                $this->oCurrentProduct = $this->oProduct;
                //                $this->oPrepare->init()->set('productsid',$this->oProduct->get('id'));
                foreach (array(
                             'SKU',
                             'Price',
                             'BusinessFeature',
                             'BusinessPrice',
                             'ProductTaxCode',
                             'QuantityPriceType',
                             'QuantityLowerBound1',
                             'QuantityPrice1',
                             'QuantityLowerBound2',
                             'QuantityPrice2',
                             'QuantityLowerBound3',
                             'QuantityPrice3',
                             'QuantityLowerBound4',
                             'QuantityPrice4',
                             'QuantityLowerBound5',
                             'QuantityPrice5',
                             'Quantity',
                             'ConditionType',
                             'ConditionNote',
                             'MainCategory',
                             'ProductType',
                             'BrowseNodes',
                             'ItemTitle',
                             'Manufacturer',
                             'Brand',
                             'ManufacturerPartNumber',
                             'EAN',
                             'Images',
                             'BulletPoints',
                             'Description',
                             'Keywords',
                             'Attributes',
                             'BasePrice',
                             'Weight',
                             'ShippingTime',
                             'variation_theme',
                         ) as $sField) {
                    if (method_exists($this, 'getmaster'.$sField)) {
                        $aData[$sField] = $this->{'getmaster'.$sField}($aApplyVariantsData);
                    } else {
                        $aData[$sField] = $this->{'get'.$sField}();
                    }
                }
                foreach (array('BasePrice', 'Weight') as $sKey) {
                    if (empty($aData[$sKey])) {
                        unset($aData[$sKey]);
                    }
                }

                $this->checkBusinessFeature($aData);

                $aData['Variations'] = $aApplyVariantsData;
                if (count($aData['Variations']) == 1 and count($aData['Variations'][0]['Variation']) == 0) {//only master
                    unset($aData['Variations']);
                }
            }

            foreach (array('BrowseNodes' => 1, 'BulletPoints' => 5, 'Keywords' => 5) as $sKey => $iCount) {
                if (isset($aData[$sKey])) {
                    $iCurrentCount = count($aData[$sKey]);
                    if ($iCurrentCount > $iCount) {
                        for($i = $iCurrentCount; $i > $iCount; $i--){
                            unset($aData[$sKey][$i-1]);
                        }
                    } elseif (count($aData[$sKey]) < $iCount) {
                        for ($i = $iCurrentCount; $i < $iCount; $i++) {
                            $aData[$sKey][] = '';
                        }
                    }
                }
            }
            
            //Condition and product type goes as standard fields
            if (isset($aData['Attributes']['ConditionType'])) {
                unset($aData['Attributes']['ConditionType']);
            }

            //Condition and product type goes as standard fields
            if (isset($aData['Attributes']['ConditionNote'])) {
                unset($aData['Attributes']['ConditionNote']);
            }

            if (isset($aData['Attributes']['ProductType'])) {
                unset($aData['Attributes']['ProductType']);
            }

            $this->aData = $aData;
        }
        return $this->aData;
    }

    protected function getMasterEan($aVariants) {
        $sType = $this->getInternationalIdentifier();
        $aData = $this->oPrepare->get('applydata');
        $sEAN= isset($aData[$sType]) ? $aData[$sType] : $this->oPrepare->get('EAN');
        return (
            isset($sEAN) &&
            count($this->aVariants) == 1
        ) ? $sEAN : $this->oProduct->getModulField('general.' . strtolower($sType), true);
    }

    protected function getMasterSku($aVariants) {
        return $this->oProduct->getMarketPlaceSku();
    }

    protected function getMasterItemTitle($aVariants) {
        $aData = $this->oPrepare->get('applydata');
        $sItemTitle= isset($aData['ItemTitle']) ? $aData['ItemTitle'] : $this->oPrepare->get('ItemTitle');
        return isset($sItemTitle) ? $sItemTitle : $this->oProduct->getName();
    }

    protected function getMasterDescription($aVariants) {
        $aData = $this->oPrepare->get('applydata');
        $sDescription= isset($aData['Description']) ? $aData['Description'] : $this->oPrepare->get('Description');
        return isset($sDescription) ? $sDescription : $this->getSanitizedProductDescription($this->oProduct->getDescription());
    }

    private function getSanitizedProductDescription($sDescription)
    {
        $sDescription = str_replace(array('&nbsp;', html_entity_decode('&nbsp;')), ' ', $sDescription);
        $sDescription = sanitizeProductDescription(
            $sDescription,
            '<p><br><ul><ol><li><strong><b><em><i>',
            '_keep_all_'
        );

        $sDescription = str_replace(array('<br />', '<br/>'), '<br>', $sDescription);
        // $sDescription = preg_replace('/(\s*<br[^>]*>\s*)*$/', ' ', $sDescription);
        $sDescription = preg_replace('/\s\s+/', ' ', $sDescription);
        $sDescription = $this->oPrepareDataHelper->truncateStringHtmlSafe($sDescription, 2000);

        return $sDescription;
    }

    protected function getMasterQuantity($aVariants) {
        $iQty = 0;
        foreach ($aVariants as $aVariant) {
            $iQty += $aVariant['Quantity'];
        }
        return $iQty;
    }

    protected function getMainCategory() {
        return $this->oPrepare->get('maincategory');
    }

    protected function getProductType() {
        $sMainCategory = $this->getMainCategory();
        $aData = $this->oPrepare->get('applydata');
        $aProductType = isset($aData['ProductType']) ? $aData['ProductType'] : $this->oPrepare->get('ProductType');
        if (!empty($aProductType[$sMainCategory])) {
            return $aProductType[$sMainCategory];
        }

        $aAttributes = $this->getAttributes();
        if (!empty($aAttributes['ProductType'])) {
            return $aAttributes['ProductType'];
        }

        if (isset($aProductType) && !is_array($aProductType)) {
            return $aProductType;
        }

        return '';
    }

    protected function getBrowseNodes() {
        $sMainCategory = $this->getMainCategory();
        $aData = $this->oPrepare->get('applydata');
        $aBrowseNodes = isset($aData['BrowseNodes']) ? $aData['BrowseNodes'] : $this->oPrepare->get('BrowseNodes');
        $aBrowseNodesNew = isset($aBrowseNodes[$sMainCategory]) ? $aBrowseNodes[$sMainCategory] : null;
        $aBrowseNodesNew = isset($aBrowseNodes) && !isset($aBrowseNodesNew) ? $aBrowseNodes : $aBrowseNodesNew;

        if (!isset($aBrowseNodesNew)) {
            $aBrowseNodesNew = array('null', 'null');
        }

        foreach ($aBrowseNodesNew as &$sBrowseNode) {
            $sBrowseNode = (strpos($sBrowseNode, '__') === false) ? $sBrowseNode : substr($sBrowseNode, 0, strpos($sBrowseNode, '__'));
            unset($sBrowseNode);
        }

        return $aBrowseNodesNew;
    }

    protected function getItemTitle() {
        $aData = $this->oPrepare->get('applydata');
        $sItemTitle = isset($aData['ItemTitle']) ? $aData['ItemTitle'] : $this->oPrepare->get('ItemTitle');
        return isset($sItemTitle) ? $sItemTitle : $this->oCurrentProduct->getName();
    }

    protected function getBasePrice() {
        return $this->oCurrentProduct->getBasePrice();
    }

    protected function getWeight() {
        return $this->oCurrentProduct->getWeight();
    }

    protected function getMasterBasePrice() {
        return $this->oProduct->getBasePrice();
    }

    protected function getMasterWeight() {
        return $this->oProduct->getWeight();
    }

    protected function getImageSize() {
        $sSize = MLModul::gi()->getConfig('imagesize');
        $iSize = $sSize == null ? 500 : (int)$sSize;
        return $iSize;
    }

    public function getBulletPoints() {
        $aData = $this->oPrepare->get('applydata');
        $aBulletPoints = isset($aData['BulletPoints']) ? $aData['BulletPoints'] : $this->oPrepare->get('BulletPoints');
        $aBulletPointsFromDB = $this->oPrepareDataHelper->stringToArray(
            $this->oCurrentProduct->getMetaDescription(),
            5,
            500
        );
        
        $aBulletPoints = isset($aBulletPoints) ? $aBulletPoints : $aBulletPointsFromDB;
        return isset($aBulletPoints) ? $aBulletPoints : array('', '', '', '', '');
    }

    public function getDescription() {
        $aData = $this->oPrepare->get('applydata');
        $sDescription = isset($aData['Description']) ? $aData['Description'] : $this->oPrepare->get('Description');
        return isset($sDescription) ? $sDescription : $this->getSanitizedProductDescription($this->oCurrentProduct->getDescription());
    }

    public function getKeywords() {
        $aData = $this->oPrepare->get('applydata');
        $aKeywords = isset($aData['Keywords']) ? $aData['Keywords'] : $this->oPrepare->get('Keywords');
        $aKeywordsFromDB = $this->oPrepareDataHelper->stringToArray(
            $this->oCurrentProduct->getMetaKeywords(),
            5,
            1000
        );
        $aKeywords = isset($aKeywords) ? $aKeywords : $aKeywordsFromDB;
        return isset($aKeywords) ? $aKeywords : array('', '', '', '', '');
    }

    protected function getAttributes() {
        $aData = $this->oPrepare->get('applydata');

        if (!empty($aData['Attributes'])) {
            $aCatAttributes = $aData['Attributes'];
        } else {
            /* @var $attributesMatchingService ML_Modul_Helper_Model_Service_AttributesMatching */
            $attributesMatchingService = MLHelper::gi('Model_Service_AttributesMatching');

            $aCatAttributes = $attributesMatchingService->mergeConvertedMatchingToNameValue(
                $this->oPrepare->get('ShopVariation'),
                $this->oCurrentProduct,
                $this->oProduct
            );
        }

        if(MLModul::gi()->getConfig('shipping.template.active') == '1'){
            $sPreparedTemplate = $this->oPrepare->get('shippingtemplate');
            $aTemplateName = MLModul::gi()->getConfig('shipping.template.name');

            $sTemplateName = null;
            if($sPreparedTemplate !== null && is_array($aTemplateName) && isset($aTemplateName[$sPreparedTemplate])){
                $sTemplateName = $aTemplateName[$sPreparedTemplate];
            }else if(is_array($aTemplateName)){
                $aDefaultTemplateName = MLModul::gi()->getConfig('shipping.template');
                foreach ($aDefaultTemplateName as $sKey => $sTemplate){
                    if($sTemplate['default'] == '1'){
                        $sTemplateName = $aTemplateName[$sKey];
                    }
                }
            }
            if($sTemplateName !== null ){
                if(!is_array($aCatAttributes)){
                   $aCatAttributes = array();
                }
                $aCatAttributes['MerchantShippingGroupName'] = $sTemplateName;
            }
        }

        return $aCatAttributes;
    }

    protected function getvariation_theme()
    {
        $variationTheme = $this->oPrepare->get('variation_theme');
        if (!is_array($variationTheme)) {
            $variationTheme = array();
        }

        return $variationTheme;
    }

    protected function getManufacturer() {
        $sManufacturer = $this->oPrepare->get('Manufacturer');

        if (empty($sManufacturer)) {
            $sManufacturer = $this->oCurrentProduct->getModulField('manufacturer');
            if (empty($sManufacturer)) {
                $sManufacturer = $this->oMarketplace->getConfig('prepare.manufacturerfallback');
            }
        }

        return $sManufacturer;
    }

    protected function getBrand() {
        $aData = $this->oPrepare->get('applydata');
        $sBrand = isset($aData['Brand']) ? $aData['Brand'] : $this->oPrepare->get('Brand');
        return isset($sBrand) ? $sBrand : $this->oCurrentProduct->getModulField('manufacturer');
    }

    protected function getManufacturerPartNumber() {
        $blSkuasmfrpartnoConfig = $this->oMarketplace->getConfig('checkin.skuasmfrpartno');
        if ($blSkuasmfrpartnoConfig) {
            return $this->oCurrentProduct->getSku();
        } else {
            return $this->oCurrentProduct->getManufacturerPartNumber();
        }
    }

    protected function getMasterManufacturerPartNumber() {
        $aData = $this->oPrepare->get('applydata');
        $sManufacturerPartNumber = isset($aData['ManufacturerPartNumber']) ? $aData['ManufacturerPartNumber'] : $this->oPrepare->get('ManufacturerPartNumber');
        return (
               (isset($sManufacturerPartNumber))
            // && count($this->aVariants) == 1
        )
            ? $sManufacturerPartNumber
            : $this->getManufacturerPartNumber();
    }

    protected function setPrepareType($sPrepareType) {
        if ($this->sPrepareType == '') {
            $this->sPrepareType = $sPrepareType;
        } elseif (
            (in_array($this->sPrepareType, array('auto', 'manual')) && !in_array($sPrepareType, array('auto', 'manual')))
            ||
            ($this->sPrepareType == 'apply' && $sPrepareType != 'apply')
        ) {
            throw new Exception ('mixed preparetypes: '.$sPrepareType.'!='.$this->sPrepareType);
        }
        return $this;
    }

    public function getPrepareType() {
        $this->getData();
        return $this->sPrepareType;
    }

    protected function getSku() {
        return $this->oCurrentProduct->getMarketPlaceSku();
    }

    protected function getPrice() {
        if ($this->oPrepare->get('price') !== null) {// @deprecated price comes only from mp-config
            return $this->oPrepare->get('price');
        } else {
            return $this->oCurrentProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject());
        }
    }

    protected function getB2BActive() {
        $value = MLDatabase::factory('preparedefaults')->getValue('b2bactive');
        // if it is globally disabled, ignore prepared value
        if (empty($value) || $value === 'false') {
            return 'false';
        }

        return $this->getB2BSetting('b2bactive', 'false');
    }

    protected function getB2BSellTo() {
        return $this->getB2BSetting('b2bsellto', 'b2b_b2c');
    }

    protected function getBusinessFeature() {
        $bB2B = $this->getB2BActive() === 'true';
        $bB2C = $this->getB2BSellTo() === 'b2b_b2c';
        if ($bB2B && $bB2C) {
            $feature = 'AMAZON_BUSINESS_B2B_B2C';
        } elseif ($bB2B) {
            $feature = 'AMAZON_BUSINESS_B2B';
        } else {
            $feature = 'AMAZON_BUSINESS_STANDARD';
        }

        return $feature;
    }

    protected function getBusinessPrice() {
        $dPrice = $this->oCurrentProduct->getSuggestedMarketplacePrice($this->getBusinessPriceObject());

        if (!isset($dPrice) || empty($dPrice) || $dPrice < 0) {
            $dPrice = $this->getPrice();
        }

        return $dPrice;
    }

    protected function getProductTaxCode() {
        // first check if there is a category specific settings
        $category = $this->getMainCategory();
        $categorySpecificCategory = MLModul::gi()->getConfig('b2b.tax_code_category');
        $aProductTaxCodeMatching = null;
        if (is_array($categorySpecificCategory)) {
            $key = array_search($category, $categorySpecificCategory);
            if ($key !== false) {
                $categorySpecificTaxMatching = MLModul::gi()->getConfig('b2b.tax_code_specific');
                $aProductTaxCodeMatching = $categorySpecificTaxMatching[$key];
            }
        }

        $aProductTaxCodeMatching = $aProductTaxCodeMatching ? : MLModul::gi()->getConfig('b2b.tax_code');
        $sProductTaxCode = $aProductTaxCodeMatching[$this->oCurrentProduct->getTaxClassId()];

        if (!isset($sProductTaxCode) || empty($sProductTaxCode)) {
            $sProductTaxCode = 'A_GEN_NOTAX';
        }

        return $sProductTaxCode;
    }

    protected function getQuantityPriceType() {
        return $this->getB2BSetting('b2bdiscounttype', '');
    }

    protected function getQuantityLowerBound1() {
        return (int)$this->getB2BQuantityTierSetting('b2bdiscounttier1quantity');
    }

    protected function getQuantityPrice1() {
        return $this->getB2BQuantityTierSetting('b2bdiscounttier1discount');
    }

    protected function getQuantityLowerBound2() {
        return (int)$this->getB2BQuantityTierSetting('b2bdiscounttier2quantity');
    }

    protected function getQuantityPrice2() {
        return $this->getB2BQuantityTierSetting('b2bdiscounttier2discount');
    }

    protected function getQuantityLowerBound3() {
        return (int)$this->getB2BQuantityTierSetting('b2bdiscounttier3quantity');
    }

    protected function getQuantityPrice3() {
        return $this->getB2BQuantityTierSetting('b2bdiscounttier3discount');
    }

    protected function getQuantityLowerBound4() {
        return (int)$this->getB2BQuantityTierSetting('b2bdiscounttier4quantity');
    }

    protected function getQuantityPrice4() {
        return $this->getB2BQuantityTierSetting('b2bdiscounttier4discount');
    }

    protected function getQuantityLowerBound5() {
        return (int)$this->getB2BQuantityTierSetting('b2bdiscounttier5quantity');
    }

    protected function getQuantityPrice5() {
        return $this->getB2BQuantityTierSetting('b2bdiscounttier5discount');
    }

    private function getB2BQuantityTierSetting($key)
    {
        return $this->getQuantityPriceType() !== '' ? $this->getB2BSetting($key) : 0;
    }

    private function getB2BSetting($key, $default = 0)
    {
        $value = $this->oPrepare->get($key);
        if (!isset($value) || $value === null) {
            $value = $this->oMarketplace->getConfig($key);
        }

        if (empty($value)) {
            $value = $default;
        }

        return $value;
    }

    protected function getCurrency() {
        return $this->oMarketplace->getConfig('currency');
    }

    protected function getQuantity() {
        if ($this->oPrepare->get('quantity') !== null) {
            return $this->oPrepare->get('quantity');
        } else {
            $aStockConf = MLModul::gi()->getStockConfig();
            return $this->oCurrentProduct->getSuggestedMarketplaceStock($aStockConf['type'], $aStockConf['value']);
        }
    }

    protected function getEan() {
        $sType = $this->getInternationalIdentifier();
        return $this->oCurrentProduct->getModulField('general.' . strtolower($sType), true);
    }

    protected function getVariation() {
        $shopAllAttributes = MLFormHelper::getShopInstance()->getPrefixedAttributeList();
        $aCatAttributes = $this->oPrepare->get('ShopVariation');

        $variationTheme = $this->getvariation_theme();
        $variationThemeCode = key($variationTheme);
        $variationThemeAttributes = current($variationTheme);
        $checkVariationTheme = !empty($variationThemeCode) && ('autodetect' !== $variationThemeCode) && !empty($variationThemeAttributes);

        $aVariants = array();
        $aAttributes =  $this->getAttributes();
        $aProductVariationData = $this->oCurrentProduct->getVariatonData();
        foreach ($aProductVariationData as $aVariant) {
            $bVariantSetFromAM = false;
            $shopVariationAttributeIsMatched = false;
            if (isset($aCatAttributes) && is_array($aCatAttributes)) {
                $sShopCode = array_search($aVariant['name'], $shopAllAttributes);
                foreach ($aCatAttributes as $sCode => $aAttribute) {
                    if (!$checkVariationTheme) {// if Theme is available and is not autodetect
                        if ($aAttribute['Code'] === $sShopCode && isset($aAttributes[$sCode])) {
                            $aVariants[] = array(
                                'Name' => $sCode,
                                'Value' => $aAttributes[$sCode]
                            );

                            $bVariantSetFromAM = true;
                        }
                    } else {// if Theme is not available or is autodetect
                        if ($aAttribute['Code'] === $sShopCode && in_array($sCode, $variationThemeAttributes)) {
                            $shopVariationAttributeIsMatched = true;
                            if (isset($aAttributes[$sCode])) {
                                $aVariants[] = array(
                                    'Name' => $sCode,
                                    'Value' => $aAttributes[$sCode]
                                );

                                $bVariantSetFromAM = true;
                            }
                        }
                    }
                }
            }

            if ($checkVariationTheme && !$shopVariationAttributeIsMatched) {
                $aVariants[] = array(
                    'Name' => $aVariant['name'],
                    'Value' => $aVariant['value']
                );
            } elseif (!$checkVariationTheme && !$bVariantSetFromAM) {
                $aVariants[] = array(
                    'Name' => $aVariant['name'],
                    'Value' => $aVariant['value']
                );
            }
        }

        return $aVariants;
    }


    protected function getMasterImages() {
        $aData = $this->oPrepare->get('applydata');
        if (isset($aData['Images'])) {
            $aImages = array();
            foreach ($aData['Images'] as $sImage => $blUpload) {
                if ($blUpload && $blUpload != 'false') {
                    $aImages[] = $sImage;
                }
            }
        } else {
            $aImages = $this->oPrepare->get('Images');
            $aImages = isset($aImages) ? $aImages : $this->oCurrentProduct->getImages();
        }
        $aOut = array();
        $iSize = $this->getImageSize();
        foreach ($aImages as $sImage) {
            try {
                $aImage = MLImage::gi()->resizeImage($sImage, 'products', $iSize, $iSize);
                $aOut[] = $aImage['url'];
            } catch (Exception $oExc) {
                try{
                    $sImage = $blUpload;
                    $aImage = MLImage::gi()->resizeImage($sImage, 'products', $iSize, $iSize);
                    $aOut[] = $aImage['url'];
                } catch (Exception $oExc) {
                    MLMessage::gi()->addDebug($oExc);
                }
            }
        }
        return $aOut;
    }

    protected function getImages() {
        $aOut = array();
        $iSize = $this->getImageSize();
        $aMasterImages = $this->getMasterImages();
        foreach ($this->oCurrentProduct->getImages() as $sImage) {
            try {
                $aImage = MLImage::gi()->resizeImage($sImage, 'products', $iSize, $iSize);
                if (in_array($aImage['url'], $aMasterImages)) {
                    $aOut[] = $aImage['url'];
                }
            } catch (Exception $oExc) {
                MLMessage::gi()->addDebug($oExc);
            }
        }
        return $aOut;
    }

    protected function getShippingTime() {
        if ($this->oPrepare->get('shippingtime') !== null) {
            $mShippingTime = $this->oPrepare->get('shippingtime');
        } else {
            $mShippingTime = $this->oMarketplace->getConfig('leadtimetoship');
        }

        if ($mShippingTime == 0) {
            $mShippingTime = null;
        }

        return $mShippingTime;
    }

    protected function getAsin() {
        return $this->oPrepare->get('aidentid');
    }

    protected function getConditionType() {
        $aAttributes = $this->getAttributes();
        if (!empty($aAttributes['ConditionType'])) {
            return $aAttributes['ConditionType'];
        }

        if ($this->oPrepare->get('conditiontype') != '') {
            return $this->oPrepare->get('conditiontype');
        }

        return $this->oMarketplace->getConfig('itemcondition');
    }

    protected function getConditionNote() {
        $aAttributes = $this->getAttributes();

        if (!empty($aAttributes['ConditionNote'])) {
            return $aAttributes['ConditionNote'];
        }

        if ($this->oPrepare->get('conditionnote') != '') {
            return $this->oPrepare->get('conditionnote');
        }

        return $this->oMarketplace->getConfig('itemnote');
    }

    protected function getWillShipInternationally() {
        if ($this->oPrepare->get('shipping') != '') {
            return $this->oPrepare->get('shipping');
        } else {
            return $this->oMarketplace->getConfig('internationalshipping');
        }
    }
    
    protected function getId() {
        return $this->oCurrentProduct->get('id');
    }

    private function getInternationalIdentifier() {
        $sSite = MLModul::gi()->getConfig('site');
        if ($sSite === 'US') {
            return 'UPC';
        }

        return 'EAN';
    }

    private function checkBusinessFeature(&$aData) {
        $sB2BActive = $this->getB2BActive();
        if (isset($sB2BActive) && $sB2BActive === 'true') {
            $sB2BSellTo = $this->oPrepare->get('b2bsellto');
            if (!isset($sB2BSellTo) || empty($sB2BSellTo)) {
                $sB2BSellTo = $this->oMarketplace->getConfig('b2bsellto');
            }

            if ($sB2BSellTo === 'b2b_only') {
                unset($aData['Price']);
            }
        } else {
            unset($aData['BusinessPrice']);
            unset($aData['ProductTaxCode']);
            unset($aData['QuantityPriceType']);
            unset($aData['QuantityLowerBound1']);
            unset($aData['QuantityPrice1']);
            unset($aData['QuantityLowerBound2']);
            unset($aData['QuantityPrice2']);
            unset($aData['QuantityLowerBound3']);
            unset($aData['QuantityPrice3']);
            unset($aData['QuantityLowerBound4']);
            unset($aData['QuantityPrice4']);
            unset($aData['QuantityLowerBound5']);
            unset($aData['QuantityPrice5']);
        }
    }

    /**
     * Configures price-object
     * @return ML_Shop_Model_Price_Interface
     */
    private function getBusinessPriceObject() {
        $sKind = $this->oMarketplace->getConfig('b2b.price.addkind');
        if (!isset($sKind)) {
            return MLModul::gi()->getPriceObject();
        }

        $fFactor = (float)$this->oMarketplace->getConfig('b2b.price.factor');
        $iSignal = $this->oMarketplace->getConfig('b2b.price.signal');
        $iSignal = $iSignal === '' ? null : (int)$iSignal;
        $blSpecial = (boolean)$this->oMarketplace->getConfig('b2b.price.usespecialoffer');
        $sGroup = $this->oMarketplace->getConfig('b2b.price.group');
        $oPrice = MLPrice::factory()->setPriceConfig($sKind, $fFactor, $iSignal, $sGroup, $blSpecial);

        return $oPrice;
    }

    protected function stringToArray($sString,$iCount,$iMaxChars){
        $aArray = explode(',', $sString);
        array_walk($aArray, array($this, 'trim'));
        $aOut = array_slice($aArray, 0, $iCount);
        foreach ($aOut as $sKey => $sBullet) {
            $aOut[$sKey] = trim($sBullet);
            if (empty($aOut[$sKey])){
                continue;
            }
            $aOut[$sKey] = substr($sBullet, 0, $iMaxChars);
        }
        return $aOut;
    }

    /**
     * check if there is any notmatch value in matched value
     * @param array $aVariation
     * @return bool
     */
    protected function variationShouldBeExcluded(array $aVariation) {
        $blReturn = false;
        foreach ($aVariation as $aValue) {
            if ($aValue['Value'] === 'notmatch') {
                $blReturn = true;
                break;
            }
        }
        return $blReturn;
    }

}
