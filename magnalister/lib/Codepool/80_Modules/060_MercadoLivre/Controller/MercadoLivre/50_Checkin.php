<?php

MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_UploadAbstract');

class ML_MercadoLivre_Controller_MercadoLivre_Checkin extends ML_Productlist_Controller_Widget_ProductList_UploadAbstract {

    public function getStock(ML_Shop_Model_Product_Abstract $oProduct) {
        $aStockConf = MLModul::gi()->getStockConfig();

        $checkinListingType = MLModul::gi()->getConfig('checkin.listingtype');

        if ($checkinListingType === 'free') {
            return 1;
        } else {
            return $oProduct->getSuggestedMarketplaceStock($aStockConf['type'], $aStockConf['value']);
        }
    }

    public function getConditions(ML_Shop_Model_Product_Abstract $oProduct) {
        $catSettings = $this->getGategorySettings($oProduct);
        if ($catSettings !== null) {
            $checkinItemCondition = MLModul::gi()->getConfig('checkin.itemcondition');
            $itemConditions = array();

            foreach ($catSettings['ItemConditions'] as $itemCondition) {
                $itemConditions[$itemCondition] = $itemCondition === $checkinItemCondition;
            }

            return $itemConditions;
        }

        return array();
    }

    public function isConditionAllowed(ML_Shop_Model_Product_Abstract $oProduct) {
        $catSettings = $this->getGategorySettings($oProduct);
        return !($catSettings !== null && count($catSettings['ItemConditions']) === 1 && current($catSettings['ItemConditions']) === 'not_allowed');
    }

    public function getListingTypes() {
        $listingTypes = MLModul::gi()->getConfig('site.listing_types');

        $checkinListingType = MLModul::gi()->getConfig('checkin.listingtype');
        $availableListingTypes = array();

        foreach ($listingTypes as $value => $name) {
            $availableListingTypes[$value] = array(
                'Name' => $name,
                'IsDefault' => $value === $checkinListingType
            );
        }

        return $availableListingTypes;
    }

    public function getBuyingModes(ML_Shop_Model_Product_Abstract $oProduct) {
        $catSettings = $this->getGategorySettings($oProduct);
        if ($catSettings !== null) {
            $checkinBuyingMode = MLModul::gi()->getConfig('checkin.buyingmode');
            $buyingModes = array();

            foreach ($catSettings['BuyingModes'] as $buyingMode) {
                $buyingModes[$buyingMode] = $buyingMode === $checkinBuyingMode;
            }

            return $buyingModes;
        }

        return array();
    }

    public function getShippingModes() {
        $checkinShippingMode = MLModul::gi()->getConfig('checkin.shippingmode');

        return array(
            'custom' => $checkinShippingMode === 'custom',
            'not_specified' => $checkinShippingMode === 'not_specified'
        );
    }

    private function getGategorySettings(ML_Shop_Model_Product_Abstract $oProduct) {
        $oModel = MLDatabase::factory('mercadolivre_prepare')->set('products_id', $oProduct->get('id'));
        if ($oModel->exists()) {
            $cat = $oModel->get('PrimaryCategory');
            $catSettings = $this->callApi(array('ACTION' => 'GetCategory', 'DATA' => array('CategoryID' => $cat)), 1 * 60 * 60);
            return $catSettings;
        } else {
            return null;
        }
    }

    protected function callApi($aRequest, $iLifeTime) {
        try {
            $aResponse = MagnaConnector::gi()->submitRequestCached($aRequest, $iLifeTime);
            if ($aResponse['STATUS'] == 'SUCCESS' && isset($aResponse['DATA']) && is_array($aResponse['DATA'])) {
                return $aResponse['DATA'];
            } else {
                return array();
            }
        } catch (MagnaException $e) {
            return array();
        }
    }

}
