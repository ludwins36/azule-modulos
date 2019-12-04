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

class ML_Tradoria_Helper_Model_Service_Product
{

    protected $aModul = null;

    /** @var ML_Database_Model_Table_Selection $oSelection */
    protected $oSelection = null;
    protected $aSelectionData = array();

    /** @var ML_Tradoria_Model_Table_Tradoria_Prepare $oPrepare */
    protected $oPrepare = null;

    /** @var ML_Shop_Model_Product_Abstract $oProduct */
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
     * @var ML_Magnalister_Model_Modul $oMarketplace
     */
    protected $oMarketplace = null;

    protected $aAttributes = array();

    public function __call($sName, $mValue)
    {
        return $sName . '()';
    }

    public function __construct()
    {
        $this->aModul = MLModul::gi()->getConfig();
        $this->oPrepare = MLDatabase::factory('tradoria_prepare');
        $this->oSelection = MLDatabase::factory('selection');
        $this->oMarketplace = MLModul::gi();
    }

    public function setProduct(ML_Shop_Model_Product_Abstract $oProduct)
    {
        $this->oProduct = $oProduct;
        $this->aVariants = array();
        $this->sPrepareType = '';
        $this->aData = null;
        return $this;
    }

    public function addVariant(ML_Shop_Model_Product_Abstract $oProduct)
    {
        $this->aVariants[] = $oProduct;
        return $this;
    }

    public function getData()
    {
        if ($this->aData === null) {
            $aData = $aApplyVariantsData = array();
            foreach ($this->aVariants as $oVariant) {
                /* @var $oVariant ML_Shop_Model_Product_Abstract */
                $this->oPrepare->init()->set('products_id', $oVariant->get('id'));
                $this->oSelection->init()->set('pid', $oVariant->get('id'))->set('selectionname', 'checkin');
                $aSelectionData = $this->oSelection->data();
                $this->aSelectionData = $aSelectionData['data'];
                $this->oCurrentProduct = $oVariant;
                $aVariantData = array(
                    'ShopProductInstance' => $this->oCurrentProduct,
                );
                foreach (array(
                             'SKU',
                             'Price',
                             'Currency',
                             'Quantity',
                             'EAN',
                             'ItemTitle',
                             'Variation',
                             'RawShopVariation',
                         ) as $sField) {
                    $aVariantData[$sField] = $this->{'get' . $sField}();
                }

                $aApplyVariantsData[] = $aVariantData;
            }

            $this->oCurrentProduct = $this->oProduct;
            foreach (array(
                         'SKU',
                         'ItemTitle',
                         'Price',
                         'Currency',
                         'Quantity',
                         'Description',
                         'ItemTax',
                         'ShippingTime',
                         'Images',
                         'EAN',
                         'Manufacturer',
                         'ManufacturerPartNumber',
                         'BasePrice',
                         'ShippingGroup',
                         'MarketplaceCategory',
                         'RawAttributesMatching',
                         'ShopCategory',
                         'ShopCategoryStructure',
                     ) as $sField) {

                if (method_exists($this, 'getmaster' . $sField)) {
                    $aData[$sField] = $this->{'getmaster' . $sField}($aApplyVariantsData);
                } else {
                    $aData[$sField] = $this->{'get' . $sField}();
                }
            }
            if (empty($aData['BasePrice'])) {
                unset($aData['BasePrice']);
            }

            $aData['Variations'] = $aApplyVariantsData;
            if (count($aData['Variations']) == 1 and count($aData['Variations'][0]['Variation']) == 0) {//only master
                unset($aData['Variations']);
            }

            $additionalAttributes = $this->getCategoryAttributes();

            if (is_array($additionalAttributes) && count($additionalAttributes) > 0) {
                $aData['Attributes'] = $additionalAttributes;
            }

            $aData['ShopProductInstance'] = $this->oCurrentProduct;
            $this->aData = $aData;
        }

        return $this->aData;
    }

    protected function getMasterEan($aVariants)
    {
        return $this->oProduct->getModulField('general.ean', true);
    }

    protected function getMasterSku($aVariants)
    {
        return $this->oProduct->getMarketPlaceSku();
    }

    protected function getMasterItemTitle($aVariants)
    {
        return $this->oProduct->getName();
    }

    protected function getMasterDescription($aVariants)
    {
        return $this->oProduct->getDescription();
    }

    protected function getMasterQuantity($aVariants)
    {
        $iQty = 0;
        foreach ($aVariants as $aVariant) {
            $iQty += $aVariant['Quantity'];
        }
        return $iQty;
    }

    protected function getSku()
    {
        return $this->oCurrentProduct->getMarketPlaceSku();
    }

    protected function getItemTitle()
    {
        return $this->oCurrentProduct->getName();
    }

    protected function getDescription()
    {
        return $this->oCurrentProduct->getDescription();
    }

    protected function getMasterItemTax()
    {
        $taxClassId = $this->oCurrentProduct->getTaxClassId();
        $taxMatchConfig = MLModul::gi()->getConfig('checkin.taxmatching');
        return $taxMatchConfig[$taxClassId];
    }

    
    protected function getImageSize()
    {
        $sSize = MLModul::gi()->getConfig('imagesize');
        $iSize = $sSize == null ? 500 : (int)$sSize;
        return $iSize;
    }
    
    protected function getVariation()
    {
        $aVariants = array();
        foreach ($this->oCurrentProduct->getVariatonData() as $aVariant) {
            $aVariants[$aVariant['name']] = $aVariant['value'];
        }
        return $aVariants;
    }

    // Just used to get information that is needed for splitting and skipping of variations.
    protected function getRawShopVariation()
    {
        return $this->oCurrentProduct->getPrefixedVariationData();
    }

    protected function getMasterImages()
    {
        $aOut = array();
        $iSize = $this->getImageSize();
        foreach ($this->oCurrentProduct->getImages() as $sImage) {
            try {
                $aImage = MLImage::gi()->resizeImage($sImage, 'products', $iSize, $iSize);
                $aOut[] = $aImage['url'];
            } catch (Exception $ex) {
                // Happens if image doesn't exist.
            }
        }
        return $aOut;
    }

    protected function getQuantity()
    {
        if (isset($this->aSelectionData['stock'])) {
            return $this->aSelectionData['stock'];
        } else {
            $aStockConf = MLModul::gi()->getStockConfig();
            return $this->oCurrentProduct->getSuggestedMarketplaceStock($aStockConf['type'], $aStockConf['value']);
        }
    }

    protected function getPrice()
    {
        if (isset($this->aSelectionData['price'])) {
            return $this->aSelectionData['price'];
        } else {
            return $this->oCurrentProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject());
        }
    }

    protected function getMasterCurrency()
    {
        return $this->getCurrency();
    }

    protected function getCurrency()
    {
        return $this->oMarketplace->getConfig('currency');
    }

    protected function getBasePrice()
    {
        return $this->oCurrentProduct->getBasePrice();
    }

    protected function getMasterMarketplaceCategory()
    {
        return $this->oPrepare->get('PrimaryCategory');
    }

    protected function getMasterShopCategory()
    {
        return $this->oCurrentProduct->getCategoryIds(false);
    }

    protected function getMasterShopCategoryStructure()
    {
        return $this->oCurrentProduct->getCategoryStructure(false);
    }

    /**
     * @todo use new custom field to each shop(magento , shopware, prestashop) to return each product leadtimetoship
     */
    protected function getShippingTime()
    {
        return $this->oPrepare->get('shippingtime') === null ? MLModul::gi()->getConfig('checkin.leadtimetoship') : $this->oPrepare->get('shippingtime');
    }

    protected function getShippingGroup()
    {
        return MLModul::gi()->getConfig('checkin.shippinggroup');
    }

    protected function getListingDuration()
    {
        return $this->oPrepare->get('ListingDuration');
    }

    protected function getProductType()
    {
        return $this->oPrepare->get('ProductType');
    }

    protected function getReturnPolicy()
    {
        return $this->oPrepare->get('ReturnPolicy');
    }

    protected function getManufacturer()
    {
        return $this->oCurrentProduct->getManufacturer();
    }

    public function getManufacturerPartNumber()
    {
        return $this->oCurrentProduct->getManufacturerPartNumber();
    }

    protected function getEan()
    {
        return $this->oCurrentProduct->getEan();
    }

    protected function getCategoryAttributes()
    {
        /* @var $attributesMatchingService ML_Modul_Helper_Model_Service_AttributesMatching */
        $attributesMatchingService = MLHelper::gi('Model_Service_AttributesMatching');
        return $attributesMatchingService->mergeConvertedMatchingToNameValue(
            $this->oPrepare->get('ShopVariation'),
            $this->oCurrentProduct,
            $this->oProduct
        );
    }

    // Just used to get information that is needed for splitting and skipping of variations.
    protected function getMasterRawAttributesMatching()
    {
        return  $this->oPrepare->get('ShopVariation');
    }

}