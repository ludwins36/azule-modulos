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
 * (c) 2010 - 2011 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/crons/MagnaCompatibleSyncInventory.php');
require_once(DIR_MAGNALISTER_MODULES.'amazon/amazonFunctions.php');

class AmazonSyncInventory extends MagnaCompatibleSyncInventory {
    protected function getPriceObject(){
        //$oProduct=$this->oProduct;// amazon dont need it
        return MLModul::gi()->getPriceObject();
    }
	protected function updateCustomFields(&$data) {
		if (empty($data)) {
			return;
		}
		$timeToShip = (int)amazonGetLeadtimeToShip($this->mpID, $this->cItem['pID']);
		if ($timeToShip > 0) {
			$data['ShippingTime'] = $timeToShip;
		}

		if (isset($this->cItem['BusinessPrice'])) {
			$pU = $this->updateBusinessPrice();
			if ($pU !== false) {
				$data['BusinessPrice'] = $pU;
			}
		}
	}

    protected function getStockConfig() {
        return MLModul::gi()->getStockConfig();
    }

	protected function updateBusinessPrice() {
		if (!$this->oProduct->exists() || !$this->syncPrice) {
			return false;
		} else {
			$data = false;
			try{
				$price = $this->oProduct->getSuggestedMarketplacePrice($this->getBusinessPriceObject());
				if (($price > 0) && ((float) $this->cItem['BusinessPrice'] != $price)) {
					$this->log("\n\t" .
						'Business price changed (old: ' . $this->cItem['Price'] . '; new: ' . $price . ')'
					);
					$data = $price;
				} else {
					$this->log("\n\t" .
						'Business price not changed (' . $price . ')'
					);
				}
			}  catch (Exception $oExc){
				$this->log("\n\t" .$oExc->getMessage());
			}

			return $data;
		}
	}

	/**
	 * Configures price-object
	 * @return ML_Shop_Model_Price_Interface
	 */
	private function getBusinessPriceObject() {
		$sKind = MLModul::gi()->getConfig('b2b.price.addkind');
		if (isset($sKind)) {
			$fFactor = (float)MLModul::gi()->getConfig('b2b.price.factor');
			$iSignal = MLModul::gi()->getConfig('b2b.price.signal');
			$iSignal = $iSignal === '' ? null : (int)$iSignal;
			$blSpecial = (boolean)MLModul::gi()->getConfig('b2b.price.usespecialoffer');
			$sGroup = MLModul::gi()->getConfig('b2b.price.group');
			$oPrice = MLPrice::factory()->setPriceConfig($sKind, $fFactor, $iSignal, $sGroup, $blSpecial);
		} else {
			$oPrice = $this->getPriceObject();
		}

		return $oPrice;
	}
}
