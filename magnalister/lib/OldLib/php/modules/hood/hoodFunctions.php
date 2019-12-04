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
 * $Id: hoodFunctions.php 645 2010-12-21 20:09:08Z MaW $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the GNU General Public License v2 or later
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

function gethoodShippingDetails() {
	global $_MagnaSession;

	$mpID = $_MagnaSession['mpID'];
	$site = MLModul::gi()->getConfig('site');
	
	initArrayIfNecessary($_MagnaSession, array($mpID, $site, 'hoodShippingDetails'));
	
	if (!empty($_MagnaSession[$mpID][$site]['hoodShippingDetails'])) {
		return $_MagnaSession[$mpID][$site]['hoodShippingDetails'];
	}
	try {
		$shippingDetails = MagnaConnector::gi()->submitRequest(array(
			'ACTION' => 'GetShippingServiceDetails',
			'DATA' => array('Site' => $site),
		));
		$shippingDetails = $shippingDetails['DATA'];
	} catch (MagnaException $e) {
		return false;
	}
	unset($shippingDetails['Version']);
	unset($shippingDetails['Timestamp']);
	unset($shippingDetails['Site']);
	foreach ($shippingDetails['ShippingServices'] as &$service) {
		$service['Description'] = fixHTMLUTF8Entities($service['Description']);
	}
	foreach ($shippingDetails['ShippingLocations'] as &$location) {
		$location = fixHTMLUTF8Entities($location);
	}
	$_MagnaSession[$mpID][$site]['hoodShippingDetails'] = $shippingDetails;
	return $_MagnaSession[$mpID][$site]['hoodShippingDetails'];
}


function gethoodLocalShippingServicesList() {
	$shippingDetails = gethoodShippingDetails();
	$servicesList = array();
	foreach($shippingDetails['ShippingServices'] as $service=>$serviceData) {
		if ('1' == $serviceData['InternationalService']) continue;
	#	$servicesList["$service"] = utf8_decode($serviceData['Description']);
		$servicesList["$service"] = $serviceData['Description'];
	}
	return $servicesList;
}

function gethoodInternationalShippingServicesList() {
	$shippingDetails = gethoodShippingDetails();
	$servicesList = array('' => ML_HOOD_LABEL_NO_INTL_SHIPPING);
	foreach($shippingDetails['ShippingServices'] as $service=>$serviceData) {
		if ('0' == $serviceData['InternationalService']) continue;
	#	$servicesList["$service"] = utf8_decode($serviceData['Description']);
		$servicesList["$service"] = $serviceData['Description'];
	}
	return $servicesList;
}

function gethoodShippingLocationsList() {
	$shippingDetails = gethoodShippingDetails();
	return $shippingDetails['ShippingLocations'];
}


function getHoodAttributes($cID,  $preselectedValues = array() ,$sName , $oProduct = null) {
        $oProduct = $oProduct instanceof ML_Shop_Model_Product_Abstract ? $oProduct : false;
        $attrOptions=MLDatabase::factory('hood_categories')->set('categoryid',$cID)->getAttributes();
        if(count(current($attrOptions))>0){
            // normalize preselected values
            if (!is_array($preselectedValues)) {
                $preselectedValues = json_decode($preselectedValues, true);
            }
            if (empty($preselectedValues)) {
                $preselectedValues = array();
            } elseif (!isset($preselectedValues[0])) {
                if (isset($preselectedValues[1])) {
                    $preselectedValues = $preselectedValues[1];
                } else if (isset($preselectedValues[2])) {
                    $preselectedValues = $preselectedValues[2];
                }
            }
            $aPreselectedValues = array();
            //change key for form-fields an fill preselected values with new key
            foreach ($attrOptions as $sAttrOptionsKey => $aAttrOptionsValue) {
                foreach ($aAttrOptionsValue['fields'] as $sAttrOptionsFieldKey => $aAttrOptionsFieldValue ) {
                    $sLabel = $aAttrOptionsFieldValue['label'];
                    $blMulti = count($aAttrOptionsFieldValue['inputs']) > 1;
                    foreach ($aAttrOptionsFieldValue['inputs'] as $sAttrOptionsInputKey => $aAttrOptionsInputValue) {
                        foreach ($aAttrOptionsInputValue['cols'] as $sAttrOptionsColsKey => $aAttrOptionsColsValue) {
                            $sKey = $sLabel.($blMulti ? '_'.$aAttrOptionsColsValue['key'] : '');
                            $aPack = unpack('H*', $sKey);
                            $attrOptions[$sAttrOptionsKey]['fields']
                                [$sAttrOptionsFieldKey]['inputs']
                                [$sAttrOptionsInputKey]['cols']
                                [$sAttrOptionsColsKey]['key'] = $aPack[1]
                            ;
                            if (array_key_exists($sKey, $preselectedValues)) {
                                $aPreselectedValues[$aPack[1]] = $preselectedValues[$sKey];
                            } elseif(array_key_exists($aPack[1], $preselectedValues)) {
                                $aPreselectedValues[$aPack[1]] = $preselectedValues[$aPack[1]];
                            } elseif (array_key_exists($aAttrOptionsColsValue['key'], $preselectedValues)) {
                                $aPreselectedValues[$aPack[1]] = $preselectedValues[$aAttrOptionsColsValue['key']];
                            }
                            if (array_key_exists('values', $aAttrOptionsColsValue)) {
                                $aRealValues = array();
                                foreach ($aAttrOptionsColsValue['values'] as $iSelectKey => $sSelectValue) {
                                    if ($iSelectKey < 0) {
                                        $aRealValues[$iSelectKey] = $sSelectValue;
                                    } else {
                                        $aRealValues[$sSelectValue] = $sSelectValue;
                                        if (
                                            array_key_exists($aPack[1], $aPreselectedValues) 
                                            && array_key_exists('select', $aPreselectedValues[$aPack[1]])
                                            && (
                                                (
                                                    !is_numeric($aPreselectedValues[$aPack[1]]['select'])
                                                    && $aPreselectedValues[$aPack[1]]['select'] == $sSelectValue
                                                ) || (
                                                    is_numeric($aPreselectedValues[$aPack[1]]['select']) 
                                                    && $aPreselectedValues[$aPack[1]]['select'] == $iSelectKey
                                                    && !in_array($aPreselectedValues[$aPack[1]]['select'], $aAttrOptionsColsValue['values'])
                                                )
                                            )
                                        ) {
                                            $aPreselectedValues[$aPack[1]]['select'] = $sSelectValue;
                                        } elseif (
                                            array_key_exists($aPack[1], $aPreselectedValues) 
                                            && array_key_exists(0, $aPreselectedValues[$aPack[1]])
                                        ) {//multiple
                                            foreach ($aPreselectedValues[$aPack[1]] as $sMultipleValue) {
                                                if (
                                                    (!is_numeric($sMultipleValue) && $sSelectValue == $sMultipleValue)
                                                    ||
                                                    (is_numeric($sMultipleValue) && $iSelectKey == $sMultipleValue)
                                                ) {
                                                    $aPreselectedValues[$aPack[1]][$sSelectValue] = $sSelectValue;
                                                }
                                            }
                                        }
                                    }
                                }
                                $attrOptions[$sAttrOptionsKey]['fields']
                                    [$sAttrOptionsFieldKey]['inputs']
                                    [$sAttrOptionsInputKey]['cols']
                                    [$sAttrOptionsColsKey]['values'] = $aRealValues
                                ;
                            }
                        }
                    }
                }
            }
            require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/GenerateProductsDetailInput.php');
            $gPDI = new GenerateProductsDetailInput($attrOptions, $aPreselectedValues, $cID,$sName,$oProduct);
            return $gPDI->render();
        }else{
            return '';
        }
}

function getHoodItemSpecifics($cID,  $preselectedValues='') {
    return getHoodAttributes($cID,  $preselectedValues);
}

function VariationsEnabled($cID) {
    return MLDatabase::factory('hood_categories')->set('categoryid',$cID)->variationsEnabled();

}


function gethoodCategoryPath($CategoryID, $StoreCategory = false, $justImported = false) {
    return MLDatabase::factory('hood_categories')
            ->set('categoryid',$CategoryID)
            ->set('storecategory',(int)$StoreCategory)
            ->getCategoryPath()
    ;
}

# Die Funktion wird verwendet beim Aufruf der Kategorie-Zuordnung, nicht vorher.
# Beim Aufruf werden die Hauptkategorien gezogen,
# und beim Anklicken der einzelnen Kategorie die Kind-Kategorien, falls noch nicht vorhanden.
function importhoodCategoryPath($CategoryID) {
    MLDatabase::factory('hood_categories')
            ->set('storecategory', 0)
            ->set('categoryid', $CategoryID)
            ->save()
    ;
    return true;
}

function hoodInsertPrepareData($data) {		
	foreach (array('ItemSpecifics', 'Attributes') as $sAttributeOrSpecific) {
		if (array_key_exists($sAttributeOrSpecific, $data)) {
			$aAttribute = json_decode($data[$sAttributeOrSpecific], true);
			if (!empty($aAttribute) && is_array($aAttribute)) {
				$aMyAttribute = array();
				foreach ($aAttribute as $sKey => $aValue) {
					foreach ($aValue as $sAttributeKey => $aAttributeValue) {
						$aMyAttribute[$sKey][pack('H*', $sAttributeKey)] = $aAttributeValue;
					}
				}
				$data[$sAttributeOrSpecific] = json_encode($aMyAttribute);
			}
		}
	}
	$data['topPrimaryCategory']	  = $data['PrimaryCategory']      == NULL ? '': $data['PrimaryCategory'];
	$data['topSecondaryCategory'] = $data['topSecondaryCategory'] == NULL ? '': $data['SecondaryCategory'];
	$data['topStoreCategory1']    = $data['topStoreCategory1']    == NULL ? '': $data['StoreCategory'];
	$data['topStoreCategory2']    = $data['topStoreCategory2']    == NULL ? '': $data['StoreCategory2'];
	/* {Hook} "hoodInsertPrepareData": Enables you to modify the prepared product data before it will be saved.<br>
	   Variables that can be used:
	   <ul>
		<li><code>$data</code>: The data of a product.</li>
		<li><code>$data['mpID']</code>: The ID of the marketplace.</li>
	   </ul>
	 */
	if (($hp = magnaContribVerify('hoodInsertPrepareData', 1)) !== false) {
		require($hp);
	}
	MagnaDB::gi()->insert(TABLE_MAGNA_HOOD_PROPERTIES, $data, true);
}

function hoodSubstituteTemplate($mpID, $pID, $template, $substitution) {
	/* {Hook} "hoodSubstituteTemplate": Enables you to extend the hood Template substitution (e.g. use your own placeholders).<br>
	   Variables that can be used:
	   <ul><li><code>$mpID</code>: The ID of the marketplace.</li>
	       <li><code>$pID</code>: The ID of the product (Table <code>products.products_id</code>).</li>
	       <li><code>$template</code>: The hood product template.</li>
	       <li><code>$substitution</code>: Associative array. Keys are placeholders, Values are their content.</li>
	   </ul>
	 */
	if (($hp = magnaContribVerify('hoodSubstituteTemplate', 1)) !== false) {
		require($hp);
	}

	return substituteTemplate($template, $substitution);
}

