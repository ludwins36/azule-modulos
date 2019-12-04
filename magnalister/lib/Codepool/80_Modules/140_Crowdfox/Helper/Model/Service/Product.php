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
class ML_Crowdfox_Helper_Model_Service_Product {
    const TITLE_MAX_LENGTH = 255;
    const DESC_MAX_LENGTH = 5000;

    /** @var ML_Database_Model_Table_Selection $oSelection */
    protected $oSelection = null;
    protected $aSelectionData = array();

    /** @var ML_Crowdfox_Model_Table_Crowdfox_Prepare $oPrepare */
    protected $oPrepare = null;

    /** @var ML_Shop_Model_Product_Abstract $oProduct */
    protected $oProduct = null;
    protected $oVariant = null;
    protected $aData = null;

    public function __call($sName, $mValue) {
        return $sName . '()';
    }

    public function __construct() {
        $this->oPrepare = MLDatabase::factory('crowdfox_prepare');
        $this->oSelection = MLDatabase::factory('selection');
    }

    public function setProduct(ML_Shop_Model_Product_Abstract $oProduct) {
        $this->oProduct = $oProduct;
        $this->sPrepareType = '';
        $this->aData = null;

        return $this;
    }

    public function setVariant(ML_Shop_Model_Product_Abstract $oProduct) {
        $this->oVariant = $oProduct;

        return $this;
    }

    public function resetData() {
        $this->aData = null;

        return $this;
    }

    public function getData() {
        if ($this->aData === null) {
            $this->oPrepare->init()->set('products_id', $this->oVariant->get('id'));
            $aData = array();
            foreach (array(
                'SKU',
                'GTIN',
                'ItemTitle',
                'Description',
                'CategoryPath',
                'Images',
                'Price',
                'Quantity',
                'Link',
                'ObligationInfo',
                'Brand',
                'ManufacturerNumber',
                'DeliveryTime',
                'DeliveryCosts',
                'ShippingMethod',
                'AdditionalAttributes',
            ) as $sField) {
                if (method_exists($this, 'get' . $sField)) {
                    $mValue = $this->{'get' . $sField}();
                    if (is_array($mValue)) {
                        foreach ($mValue as $sKey => $mCurrentValue) {
                            if (empty($mCurrentValue)) {
                                unset ($mValue[$sKey]);
                            }
                        }
                        $mValue = empty($mValue) ? null : $mValue;
                    }
                    if ($mValue !== null) {
                        $aData[$sField] = $mValue;
                    }
                } else {
                    MLMessage::gi()->addWarn("function  ML_Crowdfox_Helper_Model_Service_Product::get" . $sField .
                        "() doesn't exist");
                }
            }

            $this->aData = $aData;
        }

        return $this->aData;
    }

    protected function getSKU() {
        return $this->oVariant->getMarketPlaceSku();
    }

    protected function getGTIN() {
        return $this->oPrepare->get('Gtin');
    }

    protected function getItemTitle() {
        $sTitle = $this->oPrepare->get('ItemTitle');
        if (empty($sTitle)) {
            $this->oVariant->setLang(MLModul::gi()->getConfig('lang'));
            $sTitle = $this->oVariant->getName();
        }

        $sTitle = html_entity_decode(fixHTMLUTF8Entities($sTitle), ENT_COMPAT, 'UTF-8');
        $sTitle = $this->crowdfoxSanitizeTitle($sTitle);

        if (mb_strlen($sTitle, 'UTF-8') > self::TITLE_MAX_LENGTH) {
            $sTitle = mb_substr($sTitle, 0, self::TITLE_MAX_LENGTH - 3, 'UTF-8') . '...';
        }

        return $sTitle;
    }

    protected function getDescription() {
        $sDescription = $this->oPrepare->get('Description');
        if (empty($sDescription)) {
            $iLang = MLModul::gi()->getConfig('lang');
            $this->oVariant->setLang($iLang);
            if (empty($sDescription)) {
                $sDescription = $this->oVariant->getDescription();
            }
        }

        $sDescription = html_entity_decode(fixHTMLUTF8Entities($sDescription), ENT_COMPAT, 'UTF-8');
        if (mb_strlen($sDescription, 'UTF-8') > self::DESC_MAX_LENGTH) {
            $sDescription = mb_substr($sDescription, 0, self::DESC_MAX_LENGTH - 3, 'UTF-8') . '...';
        }

        return $sDescription;
    }

    protected function getCategoryPath() {
        $sValue = '';
        if ($this->oVariant) {
            $sValue = $this->oVariant->getCategoryPath();
            if (!empty($sValue)) {
                $aValues = explode('<br>', $sValue);
                if ((!empty($aValues)) && is_array($aValues)) {
                    if (count($aValues) == 1) {
                        $sValue = $aValues[0];
                    } else {
                        $sValue = strip_tags($aValues[count($aValues) - 2]);
                    }
                }
            }
        }

        return html_entity_decode($sValue);
    }

    protected function getBrand() {
        return $this->oPrepare->get('Brand');
    }

    protected function getManufacturerNumber() {
        return $this->oPrepare->get('MPN');
    }

    protected function getDeliveryTime() {
        return $this->oPrepare->get('DeliveryTime');
    }

    protected function getDeliveryCosts() {
        return $this->oPrepare->get('DeliveryCost');
    }

    protected function getShippingMethod() {
        return $this->oPrepare->get('ShippingMethod');
    }

    protected function getLink() {
        return $this->oVariant->getFrontendLink();
    }

    protected function getObligationInfo() {
        $basePrice = $this->oVariant->getBasePrice();
        $formattedBasePrice = array();
        // Shopware shop
        if (array_key_exists('ShopwareDefaults', $basePrice)) {
            $formattedBasePrice['Unit'] = $basePrice['ShopwareDefaults']['$sUnit'];
            $formattedBasePrice['Value'] = number_format((float)$basePrice['Value'], 2, '.', '');
            //prestashop
        } elseif (array_key_exists('Unit', $basePrice)) {
            $formattedBasePrice['Unit'] = $basePrice['Unit'];
            $formattedBasePrice['Value'] = number_format((float)$basePrice['Value'], 2, '.', '');
        }

        return sprintf(MLI18n::gi()->crowdfox_obligation_info,
            isset($formattedBasePrice['Unit']) ? $formattedBasePrice['Unit'] : '',
            isset($formattedBasePrice['Value']) ? $formattedBasePrice['Value'] : '');
    }

    protected function getAdditionalAttributes() {
        $result = array();

        /* @var $attributesMatchingService ML_Modul_Helper_Model_Service_AttributesMatching */
        $attributesMatchingService = MLHelper::gi('Model_Service_AttributesMatching');
        $nameValueList = $attributesMatchingService->mergeConvertedMatchingToNameValue(
            $this->oPrepare->get('ShopVariation'),
            $this->oVariant,
            $this->oProduct
        );

        foreach ($nameValueList as $key => $value) {
            $result[] = array('key' => $key, 'value' => $value);
        }

        return $result;
    }

    protected function getImages() {
        $aImagesPrepare = $this->oPrepare->get('Images');
        $sImagePathFromConfig = MLModul::gi()->getConfig('imagepath');
        $aOut = array();
        if (empty($aImagesPrepare) === false) {
            $aImages = $this->oVariant->getImages();

            foreach ($aImages as $sImage) {
                $sImageName = $this->substringAferLast('\\', $sImage);
                if (isset($sImageName) === false || strpos($sImageName, '/') !== false) {
                    $sImageName = $this->substringAferLast('/', $sImage);
                }

                if (in_array($sImageName, $aImagesPrepare) === false) {
                    continue;
                }

                try {
                    if (isset($sImagePathFromConfig) && $sImagePathFromConfig != '') {
                        $sImagePath = $sImagePathFromConfig . $sImageName;
                    } else {
                        $aImage = MLImage::gi()->resizeImage($sImage, 'products', 500, 500);
                        $sImagePath = $aImage['url'];
                    }

                    $aOut[] = array('URL' => $sImagePath);
                } catch (Exception $ex) {
                    echo '';
                    // Happens if image doesn't exist.
                }
            }
        }

        return $aOut;
    }

    protected function getQuantity() {
        $iQty = $this->oVariant->getSuggestedMarketplaceStock(MLModul::gi()->getConfig('quantity.type'),
            MLModul::gi()->getConfig('quantity.value'));

        return $iQty < 0 ? 0 : $iQty;
    }

    protected function getPrice() {
        if (isset($this->aSelectionData['price'])) {
            $fPrice = $this->aSelectionData['price'];
        } else {
            $fPrice = $this->oVariant->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject());
        }

        return $fPrice;
    }

    private function substringAferLast($sNeedle, $sString) {
        if (!is_bool($this->strrevpos($sString, $sNeedle))) {
            return substr($sString, $this->strrevpos($sString, $sNeedle) + strlen($sNeedle));
        }
    }

    private function strrevpos($instr, $needle) {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if ($rev_pos === false) {
            return false;
        } else {
            return strlen($instr) - $rev_pos - strlen($needle);
        }
    }

    /**
     * Sanitazes description and preparing it for Crowdfox because Crowdfox doesn't allow html tags.
     *
     * @param string $sDescription
     *
     * @return string $sDescription
     *
     */
    private function crowdfoxSanitizeDescription($sDescription) {
        $sDescription = preg_replace("#(<\\?div>|<\\?li>|<\\?p>|<\\?h1>|<\\?h2>|<\\?h3>|<\\?h4>|<\\?h5>|<\\?blockquote>)([^\n])#i",
            "$1\n$2", $sDescription);
        // Replace <br> tags with new lines
        $sDescription = preg_replace('/<[h|b]r[^>]*>/i', "\n", $sDescription);
        $sDescription = trim(strip_tags($sDescription));
        // Normalize space
        $sDescription = str_replace("\r", "\n", $sDescription);
        $sDescription = preg_replace("/\n{3,}/", "\n\n", $sDescription);

        if (strlen($sDescription) > self::DESC_MAX_LENGTH) {
            $sDescription = mb_substr($sDescription, 0, self::DESC_MAX_LENGTH - 3, 'UTF-8') . '...';
        } else {
            $sDescription = mb_substr($sDescription, 0, self::DESC_MAX_LENGTH, 'UTF-8');
        }

        return $sDescription;
    }

    /**
     * Sanitizes subtitle and preparing it for Crowdfox because Crowdfox doesn't allow html tags.
     *
     * @param $sSubtitle
     *
     * @return mixed
     */
    private function crowdfoxSanitizeTitle($sTitle) {
        $sTitle = preg_replace(array(
            '/<\/?div>/',
            '/<\/?li>/',
            '/<\/?p>/',
            '/<\/?h1>/',
            '/<\/?h2>/',
            '/<\/?h3>/',
            '/<\/?h4>/',
            '/<\/?h5>/',
            '/<\/?blockquote>/',
            '/<\/?br>/',
        ), " ", $sTitle);

        return $sTitle;
    }

}
