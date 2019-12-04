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
class ML_Tradoria_Model_Modul extends ML_Modul_Model_Modul_Abstract {

    public function getMarketPlaceName($blIntern = true) {
        return $blIntern ? 'tradoria' : MLI18n::gi()->get('sModuleNameTradoria');
    }

    public function getConfig($sName = null) {
        if ($sName == 'currency') {
            $mReturn = 'EUR';
        } else {
            $mReturn = parent::getConfig($sName);
        }
        
        if ($sName === null) {// merge
            $mReturn = MLHelper::getArrayInstance()->mergeDistinct($mReturn, array('lang' => $this->getConfig('lang'), 'currency' => 'EUR'));
        }
        
        return $mReturn;
    }

    public function getStockConfig($sType = null) {
        return array(
            'type' => $this->getConfig('quantity.type'),
            'value' => $this->getConfig('quantity.value')
        );
    }
    
    /**
     * @return array('configKeyName'=>array('api'=>'apiKeyName', 'value'=>'currentSantizedValue'))
     * @todo OrderImportOnlyPaid
     */
    protected function getConfigApiKeysTranslation() {
        $sDate = $this->getConfig('preimport.start');
        //magento tip to find empty date
        $sDate = (preg_replace('#[ 0:-]#', '', $sDate) ==='') ? date('Y-m-d') : $sDate;
        $sDate = date('Y-m-d', strtotime($sDate));
        $sSync = $this->getConfig('stocksync.tomarketplace');
        return array(
            'apikey' => array('api' => 'Access.Key', 'value' => $this->getConfig('apikey')),
            'mpusername' => array('api' => 'Access.MPUsername',  'value' => $this->getConfig('mpusername') ),
            'mppassword' => array('api' => 'Access.MPPassword',  'value' => $this->getConfig('mppassword') ),
            'import' => array('api' => 'Orders.Import', 'value' => ($this->getConfig('import') ? 'true' : 'false')),
            'preimport.start' => array('api' => 'Orders.Import.Start', 'value' => $sDate),
            'order.importonlypaid' => array('api' => 'Orders.ImportOnlyPaid', 'value' => ($this->getConfig('order.importonlypaid') ? 'true' : 'false')),
            'stocksync.tomarketplace' => array('api' => 'Callback.SyncInventory', 'value' => isset($sSync) ? $sSync : 'no'),
        );
    }
    
    public function getCarrier() {
        try {
            $aResponse = MagnaConnector::gi()->submitRequestCached(array(
                'ACTION' => 'GetCarriers'
            ), 30 * 60);
            return $aResponse['DATA'];             
        } catch (MagnaException $e) {
            return array();
        }
    }
}
