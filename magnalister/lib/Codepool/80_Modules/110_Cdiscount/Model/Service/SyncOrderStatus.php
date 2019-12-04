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

class ML_Cdiscount_Model_Service_SyncOrderStatus extends ML_Modul_Model_Service_SyncOrderStatus_Abstract {
    
    protected function postProcessError($aError, &$aModels) {
        $sMarketplaceOrderId = null;
        if (isset($aError['DETAILS']) && isset($aError['DETAILS'][$this->sOrderIdentifier])) {
            $sMarketplaceOrderId = $aError['DETAILS'][$this->sOrderIdentifier];
        }
        if (empty($sMarketplaceOrderId)) {
            return;
        }

        if (   isset($aError['DETAILS']['ErrorType'])
            && in_array ($aError['DETAILS']['ErrorType'], array(
                'OrderStateIncoherent', // OrderStateIncoherent: Shipped state is not possible to set at this stage
            ))
        ) {
            $this->saveOrderData($aModels[$sMarketplaceOrderId]);
            unset($aModels[$sMarketplaceOrderId]);
        }
    }    
    
    protected function getCarrier($sCarrier) {
        $aCarriers = MLModul::gi()->getCarriers();
        if (in_array($sCarrier, $aCarriers) || MLModul::gi()->getConfig('orderstatus.carrier.default') == null || MLModul::gi()->getConfig('orderstatus.carrier.default') == '-1') {
            return $sCarrier;
        } else {
            return MLModul::gi()->getConfig('orderstatus.carrier.default');
        }
    }
    
}
