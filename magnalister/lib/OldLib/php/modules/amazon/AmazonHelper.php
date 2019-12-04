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

require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/MagnaCompatibleHelper.php');

class AmazonHelper extends MagnaCompatibleHelper {
	const numberOfMaxAdditionalAttributes = 0;
	const marketplace = 'amazon';

	public static function processCheckinErrors($result, $mpID) {
		// Empty is ok, the API has a method to fetch the error log later.
	}

	public static function loadPriceSettings($mpId) {
		$mp = magnaGetMarketplaceByID($mpId);

		$config = array(
			'AddKind' => getDBConfigValue($mp.'.price.addkind', $mpId, 'percent'),
			'Factor'  => (float)getDBConfigValue($mp.'.price.factor', $mpId, 0),
			'Signal'  => getDBConfigValue($mp.'.price.signal', $mpId, ''),
			'Group'   => getDBConfigValue($mp.'.price.group', $mpId, ''),
			'UseSpecialOffer' => getDBConfigValue(array($mp.'.price.usespecialoffer', 'val'), $mpId, false),
			'Currency' => getCurrencyFromMarketplace($mpId),
			'ConvertCurrency' => getDBConfigValue(array($mp.'.exchangerate', 'update'), $mpId, false),
		);

		return $config;
	}

	public static function loadQuantitySettings($mpId) {
		$mp = magnaGetMarketplaceByID($mpId);

		$config = array(
			'Type'  => getDBConfigValue($mp.'.quantity.type', $mpId, 'lump'),
			'Value' => (int)getDBConfigValue($mp.'.quantity.value', $mpId, 0),
			'MaxQuantity' => (int)getDBConfigValue($mp.'.quantity.maxquantity', $mpId, 0),
		);

		return $config;
	}
	
	public static function loadShopVariations()
	{
		$shopAllAttributes = MLFormHelper::getShopInstance()->getPrefixedAttributeList();

		$shopVariationAttributes = MLFormHelper::getShopInstance()->getAttributeListWithOptions();
		$fixedVariationAttributes = array();
		foreach ($shopVariationAttributes as $key => $attribute) {
			$fixedVariationAttributes[$key] = array(
				'Code' => $key,
				'Name' => $attribute,
				'Values' => self::getShopAttributeValues($key),
			);
		}

		$shopArticleAttributes = array_diff($shopAllAttributes, $shopVariationAttributes);
		$fixedArticleAttributes = array();
		foreach ($shopArticleAttributes as $key => $attribute) {
			$fixedArticleAttributes[$key] = array(
				'Code' => $key,
				'Name' => $attribute,
				'Values' => self::getShopAttributeValues($key),
				'Custom' => true,
			);
		}

		$fixedVariationAttributes['separator_line_'] = array(
			'Code' => 'separator_line',
			'Name' => MLI18n::gi()->get('amazon_prepare_variations_separator_line_label'),
			'Values' => array(),
			'Disabled' => 'disabled',
		);

		$shopAttributes = $fixedVariationAttributes + $fixedArticleAttributes;

		$shopAttributes['separator_line2'] = array(
			'Code' => 'separator_line2',
			'Name' => MLI18n::gi()->get('amazon_prepare_variations_separator_line_label'),
			'Values' => array(),
			'Disabled' => 'disabled',
		);

		$shopAttributes['freetext'] = array(
			'Code' => 'freetext',
			'Name' => MLI18n::gi()->get('amazon_prepare_variations_free_text'),
			'Values' => array(),
		);

		$shopAttributes['attribute_value'] = array(
			'Code' => 'attribute_value',
			'Name' => MLI18n::gi()->get('amazon_prepare_variations_choose_mp_value'),
			'Values' => array(),
		);

		foreach ($shopAttributes as &$aAttribute) {
			if (!isset($aAttribute['Disabled'])) {
				$aAttribute['Disabled'] = '';
			}

			if (!isset($aAttribute['Custom'])) {
				$aAttribute['Custom'] = 'false';
			}
		}

		return $shopAttributes;
	}

	public static function getShopAttributeValues($sAttributeCode) {
		$shopValues = MLFormHelper::getShopInstance()->getPrefixedAttributeOptions($sAttributeCode);
		if (!isset($shopValues) || empty($shopValues)) {
			$shopValues = MLFormHelper::getShopInstance()->getAttributeOptions($sAttributeCode);
		}

		return $shopValues;
	}

    public static function loadMPVariations($category, $mpID, $productId = false)
    {
        // if we open from prepare form, first checked saved data
        if ($productId) {
            $prepareTable = MLDatabase::getPrepareTableInstance();

            $applyData = $prepareTable
                ->set($prepareTable->getProductIdFieldName(), $productId)
                ->set($prepareTable->getMarketplaceIdFieldName(), $mpID)
                ->get('ApplyData');

            $preparedCategory = $prepareTable->get($prepareTable->getPrimaryCategoryFieldName());
            if (empty($applyData['Attributes'])) {
                // This is for covering situation if client prepared item before new variation matching concept
                if ($preparedCategory === $category) {
                    $availableCustomConfigs = $prepareTable->get($prepareTable->getShopVariationFieldName());
                }
            } else {
                $availableCustomConfigs = array();
                foreach ($applyData['Attributes'] as $attributeKey => $attributeValue) {
                    $availableCustomConfigs[$attributeKey] = array(
                        'Kind' => 'Matching',
                        'Values' => $attributeValue,
                        'Error' => false
                    );
                }
            }
        }

        // load default values from Variation Matching tab
        $globalMatching = self::getVariationDb()->getMatchedVariations($category);
        $usedGlobal = false;
        if (!isset($availableCustomConfigs)) {
            // if we don't have saved prepare data, use data saved in Variation Matching tab
            $availableCustomConfigs = $globalMatching;
            $usedGlobal = true;
        }

        $aValues = MagnaConnector::gi()->submitRequestCached(array('ACTION' => 'GetCategoryDetails', 'CATEGORY' => $category));
        $result = array();
        if ($aValues) {
            foreach ($aValues['DATA']['attributes'] as $key => $value) {
                $result[$key] = array(
                    'AttributeCode' => $key,
                    'AttributeName' => $value['title'],
                    'AllowedValues' => isset($value['values']) ? $value['values'] : array(),
                    'AttributeDescription' => isset($value['desc']) ? $value['desc'] : '',
                    'CurrentValues' => array('Values' => array()),
                    'ChangeDate' => isset($value['changed']) ? $value['changed'] : false,
                    'Required' => isset($value['mandatory']) ? (bool)$value['mandatory'] : true,
                );

                if (isset($availableCustomConfigs[$key])) {
                    if (!isset($availableCustomConfigs[$key]['Required'])) {
                        $availableCustomConfigs[$key]['Required'] = isset($value['mandatory']) ? (bool)$value['mandatory'] : true;
                        $availableCustomConfigs[$key]['Code'] = !empty($value['values']) ? 'attribute_value' : 'freetext';
                    }

                    $result[$key]['CurrentValues'] = $availableCustomConfigs[$key];
                }
            }
        }

        if (!$usedGlobal && !empty($globalMatching)) {
            self::detectChanges($globalMatching, $result);
        }

		// if there are saved values but they were removed from Marketplace, display warning to user
		foreach ($availableCustomConfigs as $code => $value) {
			if (!isset($result[$code]) && strpos($code, 'additional_attribute_') === false) {
				$result[$code] = array(
					'Deleted' => true,
					'AttributeCode' => $code,
					'AttributeName' => $value['AttributeName'],
					'AllowedValues' => array(),
					'AttributeDescription' => '',
					'CurrentValues' => array('Values' => array()),
					'ChangeDate' => '',
					'Required' => isset($value['mandatory']) ? $value['mandatory'] : false,
				);
			}
		}

        return $result;
    }

    /**
     * Checks for each attribute whether it is prepared differently in Variation Matching tab, and if so, marks it Modified.
     * Arrays cannot be compared directly because values could be in different order (with different numeric keys).
     *
     * @param array $globalMatching
     * @param array $productMatching
     */
    protected static function detectChanges($globalMatching, &$productMatching)
    {
        foreach ($globalMatching as $attributeCode => $attributeSettings) {
            if (!empty($productMatching[$attributeCode])) {
                $productAttrs = $productMatching[$attributeCode]['CurrentValues'];
                if (!is_array($productAttrs['Values']) || !is_array($attributeSettings['Values'])) {
                    $productMatching[$attributeCode]['Modified'] = $productAttrs != $attributeSettings;
                    continue;
                }

                $productAttrsValues = $productAttrs['Values'];
                $attributeSettingsValues = $attributeSettings['Values'];
                unset($productAttrs['Values']);
                unset($attributeSettings['Values']);

                // first compare without values (optimization)
                if ($productAttrs['Code'] == $attributeSettings['Code'] && count($productAttrsValues) === count($attributeSettingsValues)) {
                    // compare values
                    // values could be in different order so we need to iterate through array and check one by one
                    $allValuesMatched = true;
                    foreach ($productAttrsValues as $attribute) {
                        unset($attribute['Marketplace']['Info']);
                        $found = false;
                        foreach ($attributeSettingsValues as $value) {
                            unset($value['Marketplace']['Info']);
                            if ($attribute == $value) {
                                $found = true;
                                break;
                            }
                        }

                        if (!$found) {
                            $allValuesMatched = false;
                            break;
                        }
                    }

                    if ($allValuesMatched) {
                        $productMatching[$attributeCode]['Modified'] = false;
                        continue;
                    }
                }

                $productMatching[$attributeCode]['Modified'] = true;
            }
        }
    }

	/**
	 * @return ML_Amazon_Model_Table_Amazon_VariantMatching
	 */
	public static function getVariationDb() {
		return MLDatabase::getVariantMatchingTableInstance();
	}

	public static function saveMatching($category, $matching, $mpId, $savePrepare = false)
	{
		$errors = array();
		foreach ($matching['ShopVariation'] as $key => &$value) {
			if (isset($value['Required'])) {
                $value['Required'] = (bool)$value['Required'];
            }

			$sAttributeName = $value['AttributeName'];
			$value['Error'] = false;

			if ($value['Code'] == 'null' || $value['Code'] == '' || empty($value['Values'])) {
				if (isset($value['Required']) && (bool)$value['Required'] && $savePrepare) {
					$errors[] = MLI18n::gi()->get('amazon_prepare_variations_error_text', array('attribute_name' => $sAttributeName));
					$value['Error'] = true;
				} else {
					unset($matching['ShopVariation'][$key]);
				}

				continue;
			}

			if (!is_array($value['Values']) || !isset($value['Values']['FreeText'])) {
				continue;
			}

			$sInfo = MLI18n::gi()->get('amazon_prepare_variations_manualy_matched');
			$sFreeText = $value['Values']['FreeText'];
			unset($value['Values']['FreeText']);

			if ($value['Values']['0']['Shop']['Key'] === 'null' || $value['Values']['0']['Marketplace']['Key'] === 'null') {
				unset($value['Values']['0']);
				if (empty($value['Values']) && (bool)$value['Required'] && $savePrepare) {
					$errors[] = MLI18n::gi()->get('amazon_prepare_variations_error_text', array('attribute_name' => $sAttributeName));
					$value['Error'] = true;
				}

				foreach ($value['Values'] as $k => &$v) {
					if (empty($v['Marketplace']['Info']) || $v['Marketplace']['Key'] === 'manual') {
						$v['Marketplace']['Info'] = $v['Marketplace']['Value'] . MLI18n::gi()->get('amazon_prepare_variations_free_text_add');
					}
				}

				continue;
			}

			if ($value['Values']['0']['Marketplace']['Key'] === 'reset') {
				unset($matching['ShopVariation'][$key]);
				continue;
			}

			if ($value['Values']['0']['Marketplace']['Key'] === 'manual') {
				$sInfo = MLI18n::gi()->get('amazon_prepare_variations_free_text_add');
				if (empty($sFreeText)) {
					if ($savePrepare) {
						$errors[] = $sAttributeName . MLI18n::gi()->get('amazon_prepare_variations_error_free_text');
						$value['Error'] = true;
					}

					unset($value['Values']['0']);
					continue;
				}

				$value['Values']['0']['Marketplace']['Key'] = $sFreeText;
				$value['Values']['0']['Marketplace']['Value'] = $sFreeText;
			}

			if ($value['Values']['0']['Marketplace']['Key'] === 'auto') {
				self::autoMatch($mpId, $category, $key, $value);
				continue;
			}

			self::checkNewMatchedCombination($value['Values']);
			if ($value['Values']['0']['Shop']['Key'] === 'all') {
				$newValue = array();
				$i = 0;
				$shopVariations = self::loadShopVariations();
				foreach ($shopVariations[$value['Code']]['Values'] as $keyAttribute => $valueAttribute) {
					$newValue[$i]['Shop']['Key'] = $keyAttribute;
					$newValue[$i]['Shop']['Value'] = $valueAttribute;
					$newValue[$i]['Marketplace']['Key'] = $value['Values']['0']['Marketplace']['Key'];
					$newValue[$i]['Marketplace']['Value'] = $value['Values']['0']['Marketplace']['Key'];
					$newValue[$i]['Marketplace']['Info'] = $value['Values']['0']['Marketplace']['Value'] . $sInfo;
					$i++;
				}

				$value['Values'] = $newValue;
			} else {
				foreach ($value['Values'] as $k => &$v) {
					if (empty($v['Marketplace']['Info'])) {
						$v['Marketplace']['Info'] = $v['Marketplace']['Value'] . $sInfo;
					}

					$v['Marketplace']['Value'] = $v['Marketplace']['Key'];
				}
			}
		}

		arrayEntitiesToUTF8($matching['ShopVariation']);

		/** @var ML_Amazon_Model_Table_Amazon_VariantMatching $oVariantMatching */
		$oVariantMatching = self::getVariationDb();
		$aShopVariation = $oVariantMatching->getMatchedVariations($category);

		if (!isset($aShopVariation) && $savePrepare) {
			$oVariantMatching->deleteVariation($category);
			$oVariantMatching
				->set('Identifier', $category)
				->set('ShopVariation', json_encode($matching['ShopVariation']))
				->save();
		}
		
		if ($savePrepare) {
			if (!empty($errors)) {
				MLRequest::gi()->set('Errors', $errors);
			} 
		}

		return json_encode($matching['ShopVariation']);
	}

	public static function autoMatch($mpID, $categoryId, $sMPAttributeCode, &$aAttributes) {
		$mpVariations = self::loadMPVariations($categoryId, $mpID);
		$aMPAttributeValues = $mpVariations[$sMPAttributeCode]['AllowedValues'];

		$sVariations = self::loadShopVariations();
		$sAttributeValues = $sVariations[$aAttributes['Code']]['Values'];

		if (empty($aMPAttributeValues)) {
			foreach ($sAttributeValues as $sShopValue) {
				$aMPAttributeValues[$sShopValue] = $sShopValue;
			}
		}

		$sInfo = MLI18n::gi()->get('amazon_prepare_variations_auto_matched');
		$blFound = false;
		if ($aAttributes['Values']['0']['Shop']['Key'] === 'all') {
			$newValue = array();
			$i = 0;
			foreach($sAttributeValues as $keyAttribute => $valueAttribute) {
				foreach ($aMPAttributeValues as $key => $value) {
					if (strcasecmp($valueAttribute, $value) == 0) {
						$newValue[$i]['Shop']['Key'] = $keyAttribute;
						$newValue[$i]['Shop']['Value'] = $valueAttribute;
						$newValue[$i]['Marketplace']['Key'] = $key;
						$newValue[$i]['Marketplace']['Value'] = $key;
						$newValue[$i]['Marketplace']['Info'] = $value . $sInfo;
						$blFound = true;
						$i++;
						break;
					}
				}
			}

			$aAttributes['Values'] = $newValue;
		} else {
			foreach ($aMPAttributeValues as $key => $value) {
				if (strcasecmp($aAttributes['Values']['0']['Shop']['Value'] , $value) == 0) {
					$aAttributes['Values']['0']['Marketplace']['Key'] = $key;
					$aAttributes['Values']['0']['Marketplace']['Value'] = $key;
					$aAttributes['Values']['0']['Marketplace']['Info'] = $value . $sInfo;
					$blFound = true;
					break;
				}
			}
		}

		if (!$blFound) {
			unset($aAttributes['Values']['0']);
		}

		self::checkNewMatchedCombination($aAttributes['Values']);
	}

	protected static function checkNewMatchedCombination(&$attributes)
	{
		foreach ($attributes as $key => $value) {
			if ($key === 0) {
				continue;
			}

			if (isset($attributes['0']) && $value['Shop']['Key'] === $attributes['0']['Shop']['Key']) {
				unset($attributes[$key]);
				break;
			}
		}
	}
}