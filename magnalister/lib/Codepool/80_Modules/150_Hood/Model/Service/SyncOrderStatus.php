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
 * (c) 2010 - 2018 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

class ML_Hood_Model_Service_SyncOrderStatus extends ML_Modul_Model_Service_SyncOrderStatus_Abstract {

    protected $sOrderIdConfirmations = 'MOrderID';

    protected function submitRequestAndProcessResult($sAction, $aRequest, $aModels) {
        $oModule = MLModul::gi();

        foreach ($aRequest as &$aData) {
            $aData['SendMail'] = ($oModule->getConfig('orderstatus.sendmail') == 1);
        }
        return parent::submitRequestAndProcessResult($sAction, $aRequest, $aModels);
    }

    protected function postProcessError($aError, &$aModels) {
        $sMarketplaceOrderId = null;
        if (isset($aError['DETAILS']) && isset($aError['DETAILS'][$this->sOrderIdConfirmations])) {
            $sMarketplaceOrderId = $aError['DETAILS'][$this->sOrderIdConfirmations];
        }
        if (empty($sMarketplaceOrderId)) {
            return;
        }

        if (
            isset($aError['ERRORCODE'])
            && in_array($aError['ERRORCODE'], array(
                1382912841, //order id is false
            ))
        ) {
            $this->saveOrderData($aModels[$sMarketplaceOrderId]);
            unset($aModels[$sMarketplaceOrderId]);
        }
    }

}
