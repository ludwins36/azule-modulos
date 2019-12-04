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
 *     Released under the GNU General Public License v2 or later
 * -----------------------------------------------------------------------------
 */
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_MAGNALISTER_MODULES . 'magnacompatible/crons/MagnaCompatibleSyncInventory.php');
require_once(DIR_MAGNALISTER_MODULES . 'hood/hoodFunctions.php');

class HoodSyncInventory extends MagnaCompatibleSyncInventory {

    protected $syncFixedStock = false;
    protected $syncChineseStock = false;
    protected $syncFixedPrice = false;
    protected $syncChinesePrice = false;

    # Bei Varianten kommt dieselbe ItemID mehrmals zurueck,
    # sollte aber nur einmal upgedatet werden
    protected $itemsProcessed = array();
    protected $variationsForItemCalculated = array();
    protected $totalQuantityForItemCalculated = array();

    public function __construct($mpID, $marketplace, $limit = 100) {
        global $_MagnaSession;

        # Ensure that $_MagnaSession contains needed data
        if (!isset($_MagnaSession) || !is_array($_MagnaSession)) {
            $_MagnaSession = array('mpID' => $mpID,
                'currentPlatform' => $marketplace);
        } else {
            $_MagnaSession['mpID'] = $mpID;
            $_MagnaSession['currentPlatform'] = $marketplace;
        }

        parent::__construct($mpID, $marketplace, $limit);
        $iConfigTimeout = MLModul::gi()->getConfig('updateitems.timeout');
        $this->timeouts['UpdateItems'] = $iConfigTimeout == null ? 1 : (int) $iConfigTimeout;
        $this->timeouts['GetInventory'] = 1200;

        $this->startedAtTimestamp = time();
    }

    protected function getConfigKeys() {
        return array(
            'FixedStockSync' => array(
                'key' => 'stocksync.tomarketplace',
                'default' => '',
            ),
            'ChineseStockSync' => array(
                'key' => 'chinese.stocksync.tomarketplace',
                'default' => '',
            ),
            'FixedPriceSync' => array(
                'key' => 'inventorysync.price',
                'default' => '',
            ),
            'ChinesePriceSync' => array(
                'key' => 'chinese.inventorysync.price',
                'default' => '',
            ),
            'FixedQuantityType' => array(
                'key' => 'fixed.quantity.type',
                'default' => '',
            ),
            'FixedQuantityValue' => array(
                'key' => 'fixed.quantity.value',
                'default' => 0,
            ),
            'Lang' => array(
                'key' => 'lang',
                'default' => false,
            ),
            'StatusMode' => array(
                'key' => 'general.inventar.productstatus',
                'default' => 'false',
            ),
            'SKUType' => array(
                'key' => 'general.keytype',
            ),
        );
    }

    protected function initQuantitySub() {
        $this->config['FixedQuantitySub'] = 0;
        if ($this->syncStock) {
            if ($this->config['FixedQuantityType'] == 'stocksub') {
                $this->config['FixedQuantitySub'] = $this->config['FixedQuantityValue'];
            }
        }
        $this->config['ChineseQuantitySub'] = 0;
        $this->config['ChineseQuantityType'] = 'lump';
        $this->config['ChineseQuantityValue'] = 1;
    }

    protected function uploadItems() {
        
    }

    protected function extendGetInventoryRequest(&$request) {
        $request['ORDERBY'] = 'DateAdded';
        $request['SORTORDER'] = 'DESC';
        $aGet = MLRequest::gi()->data();
        if (isset($aGet['fixHoodPrices']) && ($aGet['fixHoodPrices'] == 'true')) {
            $request['EXTRA'] = 'ROUNDPRICES';
        }
    }

    protected function postProcessRequest(&$request) {
        $request['ACTION'] = 'UpdateQuantity';
    }

    protected function isAutoSyncEnabled() {
        $this->syncFixedStock = $this->config['FixedStockSync'] == 'auto' || $this->config['FixedStockSync'] === 'auto_fast';
        $this->syncChineseStock = $this->config['ChineseStockSync'] == 'auto';
        $this->syncFixedPrice = $this->config['FixedPriceSync'] == 'auto';
        $this->syncChinesePrice = $this->config['ChinesePriceSync'] == 'auto';
        $aGet = MLRequest::gi()->data();
        if (isset($aGet['fixHoodPrices']) && ($aGet['fixHoodPrices'] == 'true')) {
            $this->syncFixedPrice = true;
            $this->syncChinesePrice = true;
        }
        /*
          if ($this->_debugDryRun) {
          $this->syncFixedStock = $this->syncChineseStock = $this->syncFixedPrice = $this->syncChinesePrice = true;
          }
          // */

        if (!($this->syncFixedStock || $this->syncChineseStock || $this->syncFixedPrice || $this->syncChinesePrice)) {
            $this->log('== ' . $this->marketplace . ' (' . $this->mpID . '): no autosync ==' . "\n");
            return false;
        }
        $this->log(
                '== ' . $this->marketplace . ' (' . $this->mpID . '): ' .
                'Sync fixed stock: ' . ($this->syncFixedStock ? 'true' : 'false') . '; ' .
                'Sync chinese stock: ' . ($this->syncChineseStock ? 'true' : 'false') . '; ' .
                'Sync fixed price: ' . ($this->syncFixedPrice ? 'true' : 'false') . '; ' .
                'Sync chinese price: ' . ($this->syncChinesePrice ? 'true' : 'false') . " ==\n"
        );
        return true;
    }

    protected function identifySKU() {
        $this->oProduct = null;
        // if MasterSKU is set load master Product
        if (!empty($this->cItem['MasterSKU'])) {
            $this->oProduct = MLProduct::factory()->getByMarketplaceSKU($this->cItem['MasterSKU'], true);
        }
         
        // if MasterSKU is not set or master product not exists load default product or variation
        if ($this->oProduct === null || !$this->oProduct->exists()) {
            $this->oProduct = MLProduct::factory()->getByMarketplaceSKU($this->cItem['SKU']);

            if ($this->oProduct->exists() && $this->oProduct->get('parentid') != 0) {
                $oMaster = $this->oProduct->getParent();
                if ($oMaster->exists()) {
                    $this->oProduct = $oMaster;
                }
            }
        }

        $this->cItem['pID'] = (($this->oProduct->exists()) ? (int)$this->oProduct->get('id') : 0);
    }
    
    protected $aMasterData = array();
    
    protected function getMasterData () {
        /* @var $oPrepareHelper ML_Hood_Helper_Model_Table_Hood_PrepareData */
        $oPrepareHelper = MLHelper::gi('Model_Table_Hood_PrepareData');
        if (!array_key_exists($this->cItem['ItemID'], $this->aMasterData)) {
            /* getProduct, get master and walk childs which are prepared
             * @see additems
             */
            //we start with master article
            $oMaster = $this->oProduct->get('parentid') == 0 ? $this->oProduct : $this->oProduct->getParent();
            /* @var $oMaster ML_Shop_Model_Product_Abstract */
            if ($oMaster->getVariantCount() > MLSetting::gi()->get('iMaxVariantCount')) {
                $sMessage = MLI18n::gi()->get('Productlist_ProductMessage_sErrorToManyVariants', array('variantCount' => $oMaster->getVariantCount(), 'maxVariantCount' => MLSetting::gi()->get('iMaxVariantCount')));
                MLErrorLog::gi()->addError(
                    $oMaster->get('MarketplaceIdentId'),
                    $oMaster->get('MarketplaceIdentSku'),
                    $sMessage,
                    array('SKU' => $oMaster->get('productssku'))
                );
                throw new Exception($sMessage, 1492422765);
            }
            
            // getting all variants
            $aVariants = $oMaster->getVariants();
            if (count($aVariants) == 0) {// eg. variants have no distinct sku (eg. magento attributes)
                throw new Exception('No Variants found.', 1492422768);
            }
            
            $aDefine = array(
                'StartPrice' => array('optional' => array('active' => true)),
                'SKU' => array('optional' => array('active' => true)),
                'Quantity' => array('optional' => array('active' => true)),
                'Variation' => array('optional' => array('active' => true)),
                'ShortBasePriceString'  => array('optional' => array('active' => true)),
                'EAN'       => array('optional' => array('active' => true)),
            );
            $aMasterData = array(
                'ItemID' => $this->cItem['ItemID'],
                'EAN' => $this->oProduct->getEAN(),
                'StartPrice' => 0,
                'NewQuantity' => 0,
                'Variations' => array(),
                'fixed.stocksync' => $this->config['FixedStockSync'],
                'fixed.pricesync' => $this->config['FixedPriceSync'],
                'chinese.stocksync' => $this->config['ChineseStockSync'],
                'chinese.pricesync' => $this->config['ChinesePriceSync'],
            );
            foreach ($aVariants as $oVariant) {
                $aVariation = $oPrepareHelper
                    ->setPrepareList(null)->setProduct($oVariant)
                    ->getPrepareData($aDefine, 'value')
                ;
                
                if (!$oVariant->exists() || ($this->config['StatusMode'] === 'true') && !$oVariant->isActive()) {
                    $aVariation['Quantity'] = 0;
                }
                $aMasterData['Variations'][] = $aVariation;
            }
            
            $aMasterData['DEBUG'] = array(
                'product' => array(
                    'products_id' => $oMaster->get('marketplaceidentid'),
                    'products_model' => $oMaster->getSku(),
                    'products_status' => '1'
                ),
                'syncConf' => $this->config,
                'contrib' => false,
                'calledBy' => 'SyncInventory'
            );
            $this->aMasterData[$this->cItem['ItemID']] = $aMasterData;
        }
        
        $aReturn = $this->aMasterData[$this->cItem['ItemID']];
        
        $iVariantIndex = 0;//@todo: Variante in index => nicht mehr vorhandenimmmer abgleich
        foreach (array_keys($aReturn['Variations']) as $i) {
            if($aReturn['Variations'][$i]['SKU'] == $this->cItem['SKU']) {
                $iVariantIndex = $i;
                break;
            }
        }
        # ist es eine Variante?
        $fCurrenVariantPrice = isset($aReturn['Variations'][$iVariantIndex]) ? $aReturn['Variations'][$iVariantIndex]['StartPrice'] : false;
        if (count($aReturn['Variations']) == 1 && $aReturn['Variations'][0]['Variation'] == array()) {//is master
            $aReturn = array_merge($aReturn, $aReturn['Variations'][0]);
            $aReturn['StartPrice'] = $aReturn['Variations'][0]['StartPrice'];
            $aReturn['NewQuantity'] = $aReturn['Variations'][0]['Quantity'];
            unset($aReturn['Variation'], $aReturn['Variations'], $aReturn['Quantity'], $aReturn['EAN']);
        } else {
            if ($this->checkVariation()) {
                unset($aReturn['StartPrice']);
                $blVariationBasePrice = $oPrepareHelper->haveVariationBasePrice($aReturn['Variations']);
                foreach ($aReturn['Variations'] as &$aVariant) {
                    $aReturn['NewQuantity'] += $aVariant['Quantity'];
                    $oPrepareHelper->manageVariationBasePrice($aVariant, !$blVariationBasePrice);
                }
            } else {
                $aReturn = array_merge($aReturn, $aReturn['Variations'][$iVariantIndex]);
                $aReturn['StartPrice'] = $aReturn['Variations'][$iVariantIndex]['StartPrice'];
                $aReturn['NewQuantity'] = $aReturn['Variations'][$iVariantIndex]['Quantity'];
                unset($aReturn['Variation'], $aReturn['Variations'], $aReturn['Quantity']);
            }
        }
        # Bei 'Chinese' moegliche Option: hood-Bestand nur reduzieren
        # d.h. wenn gewachsen, nichts tun
        if (
            ('Chinese' == $this->cItem['ListingType']) 
            && ($this->cItem['Quantity'] < $aReturn['NewQuantity']) 
            && ('onlydecr' == $this->config['ChineseStockSync'])
        ) {
            throw new Exception('No stock sync for variations.', 1492424185);
        }
        
        # ist es eine Variante?
        $fCurrenVariantQuantity = isset($aReturn['Variations'][$iVariantIndex]) ? $aReturn['Variations'][$iVariantIndex]['Quantity'] : false;
        $this->log(
            "\n\thood Quantity: " . $this->cItem['Quantity'] .
            "\n\tCurrent Variant Quantity: " .$fCurrenVariantQuantity .
            "\n\tShop Mater Product Quantity: " . (($aReturn['NewQuantity'] === false) ? 'false' : $aReturn['NewQuantity']) .
            "\n\thood Price: " . $this->cItem['Price'] .
            "\n\tShop Price: " . (($fCurrenVariantPrice === false) ? ((($this->syncFixedPrice && 'Chinese' != $this->cItem['ListingType'] ) || ($this->syncChinesePrice && ('Chinese' == $this->cItem['ListingType']))) ? 'frozen' : 'false') : $fCurrenVariantPrice)
        );
        
        $aReturn['DEBUG']['product']['products_quantity'] = $fCurrenVariantQuantity;
        $aReturn['DEBUG']['product']['products_price'] = $fCurrenVariantPrice;
        return $aReturn;
    }

    protected function updateItem() {
        if (in_array($this->cItem['ItemID'], $this->itemsProcessed)) {
            $this->log("\nItemID " . $this->cItem['ItemID'] . ' already processed.');
            return;
        }
        $this->cItem['SKU'] = trim($this->cItem['SKU']);
        if (empty($this->cItem['SKU'])) {
            $this->log("\nItemID " . $this->cItem['ItemID'] . ' has an emtpy SKU.');
            return;
        }

        @set_time_limit(180);
        $this->identifySKU();

        $articleIdent = 'SKU: ' . $this->cItem['SKU'] . ' (' . $this->cItem['ItemTitle'] . '); hood-ItemID: ' . $this->cItem['ItemID'] . '; ListingType: ' . $this->cItem['ListingType'] . ' ';
        if ((int) $this->cItem['pID'] <= 0) {
            $this->log("\n" . $articleIdent . ' not found');
            return;
        } else {
            $this->log("\n" . $articleIdent . ' found (pID: ' . $this->cItem['pID'] . ')');
        }
        
        try {
            $aMasterData = $this->getMasterData();
        } catch(Exception $oEx) {
            $this->log("\n" . $oEx->getMessage() . "\n");
            return;
        }
        
        $iMasterQty = $aMasterData['NewQuantity'];
        $fCurrenVariantQuantity = $aMasterData['DEBUG']['product']['products_quantity'];
        $fCurrenVariantPrice = $aMasterData['DEBUG']['product']['products_price'];
        
        if ( /* FixedPrice Article */
                (
                    ($this->syncFixedStock && ('Chinese' != $this->cItem['ListingType'])) 
                    && (/* Quantity changed (Article Variation) */                        
                        ((false !== $fCurrenVariantQuantity) && ($this->cItem['Quantity'] != $fCurrenVariantQuantity))
                        || 
                        ((false === $fCurrenVariantQuantity) && ($this->cItem['Quantity'] != $iMasterQty))/* Quantity changed (Article w/o Variation) */
                    )
                )
                || 
                ( /* Chinese Article */ 
                    ($this->syncChineseStock && ('Chinese' == $this->cItem['ListingType'])) && ($this->cItem['Quantity'] != $iMasterQty)
                )
                || 
                ( /* Sync FixedPrice price */
                    ($this->syncFixedPrice && ($fCurrenVariantPrice !== false) && ('Chinese' != $this->cItem['ListingType'])) && ($this->cItem['Price'] != $fCurrenVariantPrice)
                )
                || 
                (/* Sync Chinese price */ 
                    ($this->syncChinesePrice && ($fCurrenVariantPrice !== false) && ('Chinese' == $this->cItem['ListingType'])) && ($this->cItem['Price'] != $fCurrenVariantPrice)
                )
        ) {
            if (!$this->updateItems($aMasterData) && $this->iErrorCode == MagnaException::TIMEOUT) {
                $this->resetTimeOut();
            }
            $this->itemsProcessed[] = $this->cItem['ItemID'];
        }
    }
    
    /**
     * check if should send variation as variation or as a single product
     * @return boolean
     */
    protected function checkVariation() {
        if (!empty($this->cItem['MasterSKU']) && !empty($this->cItem['SKU'])) {
            $blVariationEnabled = $this->cItem['MasterSKU'] !== $this->cItem['SKU'];
        } elseif (isset($this->cItem['VariationAttributesText'])) {
            $blVariationEnabled = !empty($this->cItem['VariationAttributesText']);
        } else {
            $blVariationEnabled = false;
        }
        return $blVariationEnabled;
    }
    
    protected function resetTimeOut() {
        $this->timeouts['UpdateItems'] = min(10, $this->timeouts['UpdateItems'] + 1);
        try {
            MLDatabase::factory('config')
                    ->set('mpid', MLRequest::gi()->get('mp'))
                    ->set('mkey', 'updateitems.timeout')
                    ->set('value', $this->timeouts['UpdateItems'])
                    ->save();
        } catch (Exception $oExc) {
            $this->logException($oExc);
        }
    }

    protected function submitStockBatch() {
        // Do nothing, as items are already updated one by one in updateItem().
    }

    protected function getPriceObject() {
        //$oProduct=$this->oProduct;// amazon dont need it
        return MLModul::gi()->getPriceObject();
    }

    protected function getStockConfig() {
        return MLModul::gi()->getStockConfig();
    }

}
