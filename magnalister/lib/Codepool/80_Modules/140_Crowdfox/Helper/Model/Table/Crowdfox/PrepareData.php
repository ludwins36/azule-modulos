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

MLFilesystem::gi()->loadClass('Form_Helper_Model_Table_PrepareData_Abstract');

class ML_Crowdfox_Helper_Model_Table_Crowdfox_PrepareData extends ML_Form_Helper_Model_Table_PrepareData_Abstract {
    const TITLE_MAX_LENGTH = 255;
    const DESC_MAX_LENGTH = 5000;
    const MAX_NUMBER_OF_IMAGES = 4;

    public $aErrorFields = array();
    public $bIsSinglePrepare;

    public $itemsPerPage;
    public $productChunks;
    public $totalPages;
    public $currentPage;
    public $currentChunk;

    public function getPrepareTableProductsIdField() {
        return 'products_id';
    }

    protected function products_idField(&$aField) {
        $aField['value'] = $this->oProduct->get('id');
    }

    protected function itemTitleField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
        if (isset($this->oProduct)) {
            if ((!$this->bIsSinglePrepare) || isset($aField['value']) === false || empty($aField['value'])) {
                try {
                    if (!empty($aField['value'])) {
                        $parent = $this->oProduct->getParent();
                        $parentName = $parent->getName();
                        $aField['value'] = str_replace($parentName, $aField['value'], $this->oProduct->getName());
                    } else {
                        $aField['value'] = $this->oProduct->getName();
                    }
                } catch (Exception $ex) {
                    $aField['value'] = $this->oProduct->getName();
                }
            }

            $aField['value'] = $this->crowdfoxSanitizeTitle($aField['value']);
        }

        if (isset($aField['value']) && mb_strlen($aField['value'], 'UTF-8') > self::TITLE_MAX_LENGTH) {
            $aField['value'] = mb_substr($aField['value'], 0, self::TITLE_MAX_LENGTH - 3, 'UTF-8') . '...';
        }

        $aField['maxlength'] = self::TITLE_MAX_LENGTH;
        if ($this->bIsSinglePrepare && (!isset($aField['value']) || empty($aField['value']))) {
            $this->aErrors[] = 'crowdfox_prepareform_itemtitle';
        }
    }

    protected function descriptionField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
        if ($this->oProduct) {
            if (empty($aField['value'])) {
                $aField['value'] = $this->crowdfoxSanitizeDescription($this->oProduct->getDescription());
            }
        }

        $aField['maxlength'] = self::DESC_MAX_LENGTH;

        if (isset($aField['value'])) {
            $aField['value'] = str_replace("\r", ' ', $aField['value']);
            $aField['value'] = str_replace("\n", ' ', $aField['value']);
            if (mb_strlen($aField['value'], 'UTF-8') > self::DESC_MAX_LENGTH) {
                $aField['value'] = mb_substr($aField['value'], 0, self::DESC_MAX_LENGTH - 3, 'UTF-8') . '...';
            }
        }

        if ($this->bIsSinglePrepare && !isset($aField['value']) || $aField['value'] === '') {
            $this->aErrors[] = 'crowdfox_prepareform_description';
        }
    }

    protected function priceField(&$aField) {
        $aField['issingleview'] = isset($this->oProduct);

        if (isset($this->oProduct)) {
            $price = $aField['value'] = $this->oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject());
            $aField['value'] = round($price, 2);
        }
    }

    protected function imagesField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
        $aField['values'] = array();
        $aIds = array();
        if (isset($this->oProduct)) {
            $aImages = $this->oProduct->getImages();
            foreach ($aImages as $sImagePath) {
                $sId = self::substringAferLast('\\', $sImagePath);
                if (isset($sId) === false || strpos($sId, '/') !== false) {
                    $sId = self::substringAferLast('/', $sImagePath);
                }

                try {
                    $aUrl = MLImage::gi()->resizeImage($sImagePath, 'products', 60, 60);
                    $aField['values'][$sId] = array(
                        'height' => '60',
                        'width' => '60',
                        'alt' => $sId,
                        'url' => $aUrl['url'],
                    );
                    $aIds[] = $sId;
                } catch (Exception $ex) {
                    // Happens if image doesn't exist.
                }
            }
        }

        if (isset($aField['value'])) {
            if (in_array('false', $aField['value']) === true) {
                array_shift($aField['value']);
            }
        } else {
            $aField['value'] = $aIds;
        }

        if (empty($aField['value']) && !$this->bIsSinglePrepare) {
            $aField['value'] = $aIds;
        }

        if ($this->bIsSinglePrepare && empty($aField['value'])) {
            $this->aErrors[] = 'crowdfox_prepareform_images_required';
        }

        if ($this->bIsSinglePrepare && count($aField['value']) > self::MAX_NUMBER_OF_IMAGES) {
            $this->aErrors[] = 'crowdfox_prepareform_images_max';
        }
    }

    public function deliverycostField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
        if ((!empty($aField['value'])) && !is_numeric($aField['value'])) {
            $this->aErrors[] = 'crowdfox_prepareform_delivery_costs_not_number';
        }
    }

    public function deliverytimeField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
        if (empty($aField['value'])) {
            $this->aErrors[] = 'crowdfox_prepareform_delivery_time_invalid_value';
        }
    }

    public function gtinField(&$aField) {
        $sValue = $this->getFirstValue($aField);
        $sShopValue = '';

        if ($this->oProduct) {
            $sCode = MLModul::gi()->getConfig('gtincolumn');
            $sShopValue = $this->oProduct->__get($sCode);
        }

        if (is_null($sValue) && $this->bIsSinglePrepare) {
            $sValue = $sShopValue;
        }

        $aField['value'] = $this->bIsSinglePrepare ? $sValue : $sShopValue;
    }

    public function brandField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);

        if (empty($aField['value']) && $this->oProduct) {
            $aField['value'] = $this->oProduct->getModulField('general.manufacturer', true);
        }
    }

    public function mpnField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);

        if (empty($aField['value']) && $this->oProduct) {
            $aField['value'] = $this->oProduct->getModulField('general.manufacturerpartnumber', true);
        }
    }

    public function shippingMethodField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }

    protected function obligationinfoField(&$aField) {
        if ($this->oProduct) {
            $basePrice = $this->oProduct->getBasePrice();
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

            $aField['value'] = sprintf(MLI18n::gi()->crowdfox_obligation_info,
                isset($formattedBasePrice['Unit']) ? $formattedBasePrice['Unit'] : '',
                isset($formattedBasePrice['Value']) ? $formattedBasePrice['Value'] : '');
        }
    }

    public static function substringAferLast($sNeedle, $sString) {
        if (!is_bool(self::strrevpos($sString, $sNeedle))) {
            return substr($sString, self::strrevpos($sString, $sNeedle) + strlen($sNeedle));
        }
    }

    private static function strrevpos($instr, $needle) {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if ($rev_pos === false) {
            return false;
        } else {
            return strlen($instr) - $rev_pos - strlen($needle);
        }
    }

    protected function getImageSize() {
        $sSize = MLModul::gi()->getConfig('imagesize');
        $iSize = $sSize == null ? 500 : (int)$sSize;
        $iSize = $iSize < 340 ? 340 : $iSize;

        return $iSize;
    }

    /**
     * Sanitazes description and preparing it for Crowdfox because Crowdfox doesn't allow html tags.
     *
     * @param string $sDescription
     *
     * @return string $sDescription
     *
     */
    public function crowdfoxSanitizeDescription($sDescription) {
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
    public function crowdfoxSanitizeTitle($sSubtitle) {
        $sSubtitle = preg_replace(array(
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
        ), " ", $sSubtitle);

        return $sSubtitle;
    }
}
