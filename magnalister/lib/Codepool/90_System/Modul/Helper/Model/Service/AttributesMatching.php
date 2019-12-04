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
 * (c) 2010 - 2017 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
class ML_Modul_Helper_Model_Service_AttributesMatching
{
    public function isMultiSelectType($type)
    {
        return in_array(strtolower($type), array('multiselect', 'multiselectandtext'), true);
    }

    /**
     * Converts attribute matching configuration and product data values into array of MP attribute name and value pairs using
     * $product as concrete shop values repository and $defaultValuesProduct for values that are not found in $product.
     * Can be useful for master/variant products where some of matched attributes values are defined on master product and variant
     * product holds only specifics that vary
     *
     * @param array $attributeMatching Matching attribute configurations from AM
     * @param ML_Shop_Model_Product_Abstract $product Product with concrete values for attributes
     * @param ML_Shop_Model_Product_Abstract $defaultValuesProduct Values from this product instance will be used when $product
     * instance value is empty
     * @param array $filterBy Array of shop codes to include in result set
     *
     * @return array Key value pairs of matched MP attribute name and its value
     */
    public function mergeConvertedMatchingToNameValue($attributeMatching, $product, $defaultValuesProduct, $filterBy = array())
    {
        return array_merge(
            $this->convertSingleProductMatchingToNameValue($attributeMatching, $defaultValuesProduct, $filterBy),
            $this->convertSingleProductMatchingToNameValue($attributeMatching, $product, $filterBy)
        );
    }

    /**
     * Converts attribute matching configuration and product data values into array of MP attribute name and value pairs
     *
     * @param array $attributeMatching Matching attribute configurations from AM
     * @param ML_Shop_Model_Product_Abstract $product Product with concrete values for attributes
     * @param array $filterBy Array of shop codes to include in result set
     *
     * @return array Key value pairs of matched MP attribute name and its value
     * @throws Exception
     */
    public function convertSingleProductMatchingToNameValue($attributeMatching, $product, $filterBy = array())
    {
        if (empty($attributeMatching) || !is_array($attributeMatching)) {
            return array();
        }

        $attributes = array();

        foreach ($attributeMatching as $mpCode => $attribute) {
            // Set mpCode to attribute artificially because some MP might want to override any of methods from this class and
            // attribute definition is not saved with mpCode in it, only way to get this info is with this key here in this loop
            $attribute['MPCode'] = $mpCode;

            if (!empty($filterBy) && !in_array($attribute['Code'], $filterBy)) {
                continue;
            }

            if (!isset($attribute['Values']) || $this->valueIsEmpty($attribute['Values'])) {
                continue;
            }

            $attributeValue = $attribute['Values'];
            if (!$this->attributeMatchedValueIsLiteral($attribute)) {
                $attributeValue = $this->findMatchingAttributeValue(
                    MLFormHelper::getShopInstance()->getFlatShopAttributesForMatching($attribute['Code'], $product),
                    $attribute
                );
            }

            if (!$this->valueIsEmpty($attributeValue)) {
                $attributeName = $this->stringStartsWith($mpCode, 'additional_attribute') ? $attribute['AttributeName'] : $mpCode;
                $attributes[$attributeName] = $attributeValue;
            }
        }

        return $attributes;
    }

    /**
     * Based on AM matching configuration for attribute and shop attribute value, locates matched MP value and returns it.
     * Empty string will be returned  when no matching is found
     *
     * @param array $shopAttribute Shop attribute configuration with value for attribute in shop
     * @param array $matchedAttribute AM matching configuration for attribute
     *
     * @return array|mixed|string Matched MP attribute value for specific shop attribute value.
     */
    protected function findMatchingAttributeValue($shopAttribute, $matchedAttribute)
    {
        if (!isset($shopAttribute['value'])) {
            return '';
        }

        // For shop text attributes take directly shop value, for other go trough matched values and find matched value
        if (!$this->valueIsEmpty($shopAttribute['value']) && ($shopAttribute['type'] === 'text')) {
            return $shopAttribute['value'];
        }

        $attributeValue = $this->findMatchingMPValue($shopAttribute, $matchedAttribute);

        return $this->convertMatchedAttributeValue($matchedAttribute, $attributeValue);
    }

    /**
     * Locates matched MP value for all select shop attribute types. Empty string will be returned  when no matching is found
     *
     * @param array $shopAttribute Shop attribute configuration with value for attribute in shop
     * @param array $matchedAttribute AM matching configuration for attribute
     *
     * @return array|mixed|string Matched MP attribute value for specific shop attribute value.
     */
    protected function findMatchingMPValue($shopAttribute, $matchedAttribute)
    {
        $attributeValue = $this->findMatchingMPValueForExactShopValue($shopAttribute['value'], $matchedAttribute['Values']);

        // Try to match shop values one by one if full combination is not matched and matching is MP text -> Shop multiSelect
        if ($this->valueIsEmpty($attributeValue) && $this->matchedMPAttributeIsText($matchedAttribute) &&
            in_array(strtolower($shopAttribute['type']), array('multiselect', 'multiselectandtext')) &&
            !empty($shopAttribute['value']) && is_array($shopAttribute['value'])
        ) {
            $matchedMpValues = array();
            foreach ($shopAttribute['value'] as $shopValue) {
                $mpValue = $this->findMatchingMPValueForExactShopValue($shopValue, $matchedAttribute['Values']);
                // If matching MP value is not found, break loop and consider whole value as unmatched
                if ($this->valueIsEmpty($mpValue)) {
                    break;
                }

                $matchedMpValues[] = $mpValue;
            }

            // If all individual shop values are matched, compose MP value out of matched MP values
            if (count($matchedMpValues) === count($shopAttribute['value'])) {
                $attributeValue = $matchedMpValues;
            }
        }

        return $attributeValue;
    }

    /**
     * Goes trough matched attribute values configuration and tries to locate matched MP value based on shop value(s)
     *
     * @param mixed|array|string $shopAttributeValue Attribute value from shop (multi select value is array)
     * @param array $matchedAttributeValues Shop to MP value mappings from AM
     *
     * @return mixed|string Matched MP attribute value for specific shop attribute value.
     */
    protected function findMatchingMPValueForExactShopValue($shopAttributeValue, $matchedAttributeValues)
    {
        if (empty($matchedAttributeValues) || !is_array($matchedAttributeValues)) {
            return '';
        }

        $attributeValue = '';
        foreach ($matchedAttributeValues as $value) {
            if (!is_array($value['Shop']['Value'])) {
                $aJson = json_decode($value['Shop']['Value'], true);
                if (is_array($aJson)) {
                    $value['Shop']['Value'] = $aJson;
                } else {
                    $value['Shop']['Value'] = array($value['Shop']['Value']);
                }
            }

            if (!is_array($shopAttributeValue)) {
                $shopAttributeValue = array($shopAttributeValue);
            }

            $missingShopValues = array_diff($value['Shop']['Value'], $shopAttributeValue);
            $missingMatchedValues = array_diff($shopAttributeValue, $value['Shop']['Value']);
            if (count($missingShopValues) === 0 && count($missingMatchedValues) === 0) {
                if ($value['Marketplace']['Key'] === 'manual') {
                    $wordsInInfo = explode('-', $value['Marketplace']['Info']);
                    array_pop($wordsInInfo);
                    $attributeValue = trim(implode('-', $wordsInInfo));
                    break;
                }

                $attributeValue = $value['Marketplace']['Key'];
                break;
            }
        }

        return $attributeValue;
    }

    protected function stringStartsWith($haystack, $needle)
    {
        return strpos($haystack, $needle) === 0;
    }

    /**
     * Checks if value is considered as empty value by AM
     *
     * @param mixed $attributeValue Value to check
     *
     * @return bool <b>TRUE</b> if attribute value is empty; otherwise, <b>FALSE</b>
     */
    protected function valueIsEmpty($attributeValue)
    {
        return ($attributeValue === null) || ($attributeValue === '');
    }

    public function attributeMatchedValueIsLiteral($attribute)
    {
        return in_array($attribute['Code'], array('freetext', 'attribute_value'));
    }

    protected function matchedMPAttributeIsText($matchedAttribute)
    {
        $mpAttributeIsText = $matchedAttribute['Kind'] === 'FreeText';
        if (!empty($matchedAttribute['DataType'])) {
            $mpAttributeIsText = $matchedAttribute['DataType'] === 'text';
        }

        return $mpAttributeIsText;
    }

    /**
     * Joins multiple values into single string value using ', ' as a separator if matched MP attribute is text type and
     * attribute value is multiple (array of values). Override this method if needed for specific MP.
     *
     * @param array $matchedAttribute AM matching configuration for attribute
     * @param mixed|array|string $attributeValue Matched shop attribute value
     *
     * @return mixed|array|string Converted and MP adjusted attribute value
     */
    protected function convertMatchedAttributeValue($matchedAttribute, $attributeValue)
    {
        if (!empty($attributeValue) && is_array($attributeValue) && $this->matchedMPAttributeIsText($matchedAttribute)) {
            $attributeValue = join(', ', $attributeValue);
        }

        return $attributeValue;
    }
}
