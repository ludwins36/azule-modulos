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
class ML_Amazon_Model_Modul extends ML_Modul_Model_Modul_Abstract {
    
    public function __construct() {
        parent::__construct();
    }
    
    
    public function isConfigured() {
        $bReturn = parent::isConfigured();
        $aFields = MLRequest::gi()->data('field');
        $sCurrency = MLDatabase::factory('config')->set('mpid',$this->getMarketPlaceId())->set('mkey', 'currency')->get('value');  
        if(!MLHttp::gi()->isAjax() && $aFields !== null && isset($aFields['site']) ){ // saving new site in configuration            
            $aCurrencies = $this->getCurrencies();
            $sCurrency = $aCurrencies[$aFields['site']];
        }
        if (!empty($sCurrency) && !in_array($sCurrency, array_keys(MLCurrency::gi()->getList()))) {
            MLMessage::gi()->addWarn(sprintf(MLI18n::gi()->ML_AMAZON_ERROR_CURRENCY_NOT_IN_SHOP , $sCurrency));
            return false;
        }
        if(MLModul::gi()->getConfig('shipping.template.active') == '1'){
            $aTemplateName = MLModul::gi()->getConfig('shipping.template.name');
            $blEmptyTemplate = true;
            foreach ($aTemplateName as $iKey => $sValue) {
                if($sValue !== ''){
                    $blEmptyTemplate = false;
                    break;
                }
            }
            if($blEmptyTemplate){
                MLMessage::gi()->addWarn(MLI18n::gi()->ML_AMAZON_ERROR_TEMPLATENAME_EMPTY);
                return false;
            }
        }
        return $bReturn;
    }
    
    /**
     *
     * @var ML_Shop_Model_Price_Interface $oPrice 
     */
    protected $oPrice=null;
    public function getMarketPlaceName($blIntern = true){
        return $blIntern ? 'amazon' : MLI18n::gi()->get('sModuleNameAmazon');
    }
    
    public function getStockConfig($sType = null){
        return array(
            'type'=>$this->getConfig('quantity.type'),
            'value'=>$this->getConfig('quantity.value')
        );
    }
    public function getPublicDirLink(){
        $aResponse=MagnaConnector::gi()->submitRequestCached(array(
            'ACTION'=>'GetPublicDir',
        ), 0);
        if(isset($aResponse['DATA']) && $aResponse['STATUS']=='SUCCESS'){
            return $aResponse['DATA'];
        }else{
            throw new Exception('GetPublicDir');
        }
    }
    public function getMainCategories(){
        $aCategories=array();
        try {
            $aResponse = MagnaConnector::gi()->submitRequestCached(array(
                'ACTION' => 'GetMainCategories',
            ));
            if(isset($aResponse['DATA'])){
                $aCategories=$aResponse['DATA'];
            }
        } catch (MagnaException $e) {
            //echo print_m($e->getErrorArray(), 'Error: '.$e->getMessage(), true);
        }
        return $aCategories;
    }
    public function getProductTypesAndAttributes($sCategory) {
        $aOut=array();
        try {
            $aRequest = MagnaConnector::gi()->submitRequestCached(array(
                'ACTION' => 'GetProductTypesAndAttributes',
                'CATEGORY' => $sCategory
            ));
        } catch (MagnaException $e) {
        }
        if(isset($aRequest['DATA'])){
            $aOut=$aRequest['DATA'];
        }else{
            $aOut= array(
                'ProductTypes' => array('null' => ML_AMAZON_ERROR_APPLY_CANNOT_FETCH_SUBCATS),
                'Attributes' => false
            );
        }
        return $aOut;
    }

    /**
     * @param $sCategory
     * @param bool|string $mNewResponse (string for now could be "ALL")
     * @return array
     */
    public function getBrowseNodes($sCategory, $mNewResponse = 'ALL') {
        $aRequest = array();

        try {
            $aRequest = MagnaConnector::gi()->submitRequestCached(array(
                'ACTION' => 'GetBrowseNodes',
                'CATEGORY' => $sCategory,
                'NewResponse' => $mNewResponse,
            ));
        } catch (MagnaException $e) {
        }

        return isset($aRequest['DATA']) ? $aRequest['DATA'] : array();
    }
    
    /**
     * @return array('configKeyName'=>array('api'=>'apiKeyName', 'value'=>'currentSantizedValue'))
     */
    protected function getConfigApiKeysTranslation() {
        $sDate = $this->getConfig('preimport.start');
        //magento tip to find empty date
        $sDate = (preg_replace('#[ 0:-]#', '', $sDate) ==='') ? date('Y-m-d') : $sDate;
        $sDate = date('Y-m-d', strtotime($sDate));
        $sSync = $this->getConfig('stocksync.tomarketplace');
        return array(
            'import' => array('api' => 'Orders.Import', 'value' => ($this->getConfig('import'))),            
            'preimport.start' => array('api' => 'Orders.Import.TS', 'value' => $sDate),
            'stocksync.tomarketplace' => array('api' => 'Callback.SyncInventory', 'value' => isset($sSync) ? $sSync : 'no'),
        );
    }
    
    public function getCurrencies(){
        $aCurrencies = array();
        foreach ($this->getMarketPlaces() as $aMarketplace) {
            $aCurrencies[$aMarketplace['Key']] = fixHTMLUTF8Entities($aMarketplace['Currency']);
        }
        return $aCurrencies;
    }
    
    public function getMarketPlaces() {
        try {
            $aRequest = MagnaConnector::gi()->submitRequest(array(
                'ACTION' => 'GetMarketplaces',
                'SUBSYSTEM' => 'Amazon',
            ));
            
        } catch (MagnaException $e) {            
        }        
        return isset($aRequest['DATA'])?$aRequest['DATA']:array();
    }

    public function getCarrierCodes() {
        try {
            $aRequest = MagnaConnector::gi()->submitRequest(array(
                'ACTION' => 'GetCarrierCodes',
                'SUBSYSTEM' => 'Amazon',
                'MARKETPLACEID' => $this->getMarketPlaceId(),
            ));
        } catch (MagnaException $e) {
            
        }
        return isset($aRequest['DATA']) ? $aRequest['DATA'] : array();
    }
    
    public function amazonLookUp($sSearch) {
        $searchResults = array();
        try {
            $result = MagnaConnector::gi()->submitRequest(array(
                'ACTION' => 'ItemLookup',
                'ASIN' => $sSearch
            ));
            if (!empty($result['DATA'])) {
                $searchResults = array_merge($searchResults, $result['DATA']);
            }
        } catch (MagnaException $e) {
            $e->setCriticalStatus(false);
        }
        return $searchResults ;
    }

    
    public function amazonSearch($sSearch) {
        $searchResults = array();
        try {
            $result = MagnaConnector::gi()->submitRequest(array(
                        'ACTION' => 'ItemSearch',
                        'NAME' => $sSearch
            ));
            if (!empty($result['DATA'])) {
                $searchResults = array_merge($searchResults, $result['DATA']);
            }
        } catch (MagnaException $e) {
            $e->setCriticalStatus(false);
        }
        return $searchResults ;
    }

    public function performItemSearch($asin, $ean, $productsName) {
        $sCacheId = __FUNCTION__ . '_' . md5(json_encode(array($asin, $ean, $productsName)));
        try {
            $searchResults = MLCache::gi()->get($sCacheId);
        } catch (ML_Filesystem_Exception $oEx) {
            $searchResults = array();
            
            if (!empty($asin)) {
                $searchResults = $this->amazonLookUp($asin);
            }
            
            $ean = str_replace(array(' ', '-'), '', $ean);
            if (!empty($ean)) {
                $searchResults = array_merge($searchResults, $this->amazonLookUp($ean));
                $searchResults = array_merge($searchResults, $this->amazonSearch($ean));
            }

            if (!empty($productsName)) {
                $searchResults = array_merge($searchResults, $this->amazonSearch($productsName));
            }
            if (!empty($searchResults)) {
                $searchResults = array_map('unserialize', array_unique(array_map('serialize', $searchResults)));
                foreach ($searchResults as &$data) {
                    if (!empty($data['Author'])) {
                        $data['Title'] .= ' (' . $data['Author'] . ')';
                    }
                    if (!empty($data['LowestPrice']['Price']) && !empty($data['LowestPrice']['CurrencyCode'])) {
                        $data['LowestPriceFormated'] = MLPrice::factory()->format($data['LowestPrice']['Price'], $data['LowestPrice']['CurrencyCode']); //$price->format();
                        $data['LowestPrice'] = $data['LowestPrice']['Price'];
                    } {
                        $data['LowestPriceFormated'] = '&mdash;';
                        $data['LowestPrice'] = '-';
                    }
                }
            }
            MLCache::gi()->set($sCacheId, $searchResults, 60 * 60 * 2);
        }
        return $searchResults;
    }
    
    public function MfsGetConfigurationValues($sType = null) {
        try {
            $aResponse = MagnaConnector::gi()->submitRequestCached(array(
                'ACTION' => 'MFS_GetConfigurationValues',
            ), 6 * 60 * 60);
            if (array_key_exists('DATA', $aResponse)) {
                if ($sType === null) {
                    return $aResponse['DATA'];
                } elseif (array_key_exists($sType, $aResponse['DATA'])) {
                    return $aResponse['DATA'][$sType];
                } else {
                    return $sType;
                }
            } else {
                return array();
            }
        } catch (Exception $oEx) {
            return array();
        }
    }
    
    public function isMultiPrepareType(){
        return true;
    }

//    public function isAttributeMatchingNotMatchOptionImplemented() {
//        return true;
//    }
}