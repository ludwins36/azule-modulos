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
MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_UploadAbstract');

class ML_Crowdfox_Controller_Crowdfox_Checkin extends ML_Productlist_Controller_Widget_ProductList_UploadAbstract {

    protected $aParameters = array('controller');

    public static function getTabTitle() {
        return MLI18n::gi()->get('ML_GENERIC_CHECKIN');
    }

    public static function getTabActive() {
        return MLModul::gi()->isConfigured();
    }

    public function getProductListWidget() {
        if (count($this->getProductList()->getMasterIds(true)) > 0) { 
            $this->oPrepareList = MLDatabase::factory(MLModul::gi()->getMarketPlaceName() . '_prepare')->getList();
            $this->oPrepareList->getQueryObject()->where("Gtin = ''");
            if (count($this->oPrepareList->getList()) > 0) {
                MLMessage::gi()->addNotice($this->__('crowdfox_no_gtin'));
            }
        }
        return parent::getProductListWidget();
    }

    public function getStock(ML_Shop_Model_Product_Abstract $oProduct) {
        $aStockConf = MLModul::gi()->getStockConfig();
        $checkinListingType = MLModul::gi()->getConfig('checkin.listingtype');
        if ($checkinListingType === 'free') {
            return 1;
        } else {
            return $oProduct->getSuggestedMarketplaceStock($aStockConf['type'], $aStockConf['value']);
        }
    }


}
