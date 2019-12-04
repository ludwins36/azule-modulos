<?php
require_once MLFilesystem::getOldLibPath('php/modules/ebay/ebayFunctions.php');

MLFilesystem::gi()->loadClass('Modul_Model_Table_Categories_Abstract');

class ML_Ebay_Model_Table_Ebay_Categories extends ML_Modul_Model_Table_Categories_Abstract {
    const iEbayLiveTime=86400;
    const iStoreLiveTime=600;

    protected $sTableName = 'magnalister_ebay_categories';

    protected $aFields = array(
         'CategoryID'     =>array(
             'isKey' => true,
             'Type' => 'bigint(11)',  'Null' => 'NO', 'Default' => 0,    'Extra' => '', 'Comment'=>'' ),
         'SiteID'         =>array(
             'isKey' => true,
             'Type' => 'int(4)',      'Null' => 'NO', 'Default' => 77,   'Extra' => '', 'Comment'=>'' ),
         'CategoryName'   =>array(
             'Type' => 'varchar(128)','Null' => 'NO', 'Default' => '',   'Extra' => '', 'Comment'=>'' ),
         'CategoryLevel'  =>array(
             'Type' => 'int(3)',      'Null' => 'NO', 'Default' => 1,    'Extra' => '', 'Comment'=>'' ),
         'ParentID'       =>array(
             'Type' => 'bigint(11)',  'Null' => 'NO', 'Default' => 0,    'Extra' => '', 'Comment'=>'' ),
         'LeafCategory'   =>array(
             'Type' => 'tinyint(4)',  'Null' => 'NO', 'Default' => 1,    'Extra' => '', 'Comment'=>'' ),
         'StoreCategory'  =>array(
             'isKey' => true,
             'Type' => 'tinyint(4)',  'Null' => 'NO', 'Default' => 0,    'Extra' => '', 'Comment'=>'' ),
         'Attributes'     =>array(
             'Type' => 'int(11)',     'Null' => 'NO', 'Default' => 0, 'Extra' => '', 'Comment'=>'' ),
         'Expires'        =>array(
             'isExpirable' => true,
             'Type' => 'datetime',    'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>'' ),
     );

     protected $aTableKeys=array(
         'PRIMARY'    => array('Non_unique' => '0', 'Column_name' => 'CategoryID, SiteID, StoreCategory'),
     );

    /**
     *
     * @var bool $blEbayBatchmode if true, no check by loading
     */
    protected static $blLoadFromApi=true;


    public function __construct() {
        parent::__construct();
    }

    /**
     * In tree structure we couldn't delete only expired item, because of that for categories we truncate all item
     * and only get first level categories and whole path of prepared categories (TopTen categories)
     * @todo it should be consider for ML_Modul_Model_Table_Categories_Abstract too
     * @return ML_Modul_Model_Table_Categories_Abstract
     * @throws Exception
     */
    public function runOncePerRequest() {
        if (
            $this->sExpirableFieldName != '' &&
            MLDatabase::getDbInstance()->fetchOne('SELECT COUNT(*) FROM '.$this->sTableName.' WHERE '.$this->sExpirableFieldName."<'".date('Y-m-d H:i:s')."'") > 0
        ) {
            MLDatabase::getDbInstance()->query('TRUNCATE '.$this->sTableName);
            $this->set('categoryid', 0)->set('storecategory', 0)->getChildCategories(true);//populate ebay category
            $this->set('storecategory', 1)->getChildCategories(true);//populate ebay store category
        }
        return parent::runOncePerRequest();
    }



    protected function setDefaultValues() {
        $this->set('storecategory',0);
        return $this;
    }

    public function set($sName, $mValue) {
        if(strtolower($sName)=='storecategory'){
            if($mValue==0){
                parent::set('siteid', MLModul::gi()->getEbaySiteId());
            }else{
                parent::set('siteid', MLModul::gi()->getMarketPlaceId());
            }
        }elseif(strtolower($sName)=='siteid'){
            if($mValue== MLModul::gi()->getMarketPlaceId()){
                parent::set('storecategory',1);
            }else{
                parent::set('storecategory',0);
            }
        }
        return parent::set($sName, $mValue);
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function populateCategoryWholePath() {
        if ((int)$this->aData['categoryid'] !== 0 && isset($this->aData['categoryid'], $this->aData['siteid'], $this->aData['storecategory'])) {
            if ((int)$this->get('storecategory') === 0) {
                foreach (MLModul::gi()->getCategoryWithAncestors($this->get('categoryid')) as $aRow) {
                    $oCategory = new self;
                    foreach ($aRow as $sKey => $sValue) {
                        $oCategory->set($sKey, $sValue);
                    }
                    $oCategory->save($aRow);
                }
            } else {
                $this->getChildCategories();
            }
            //if category is newly inserted from api data, category object should be reload from database
            $this->blLoaded = null;
            $this->load();
        }
        return $this;
    }

    public function variationsEnabled(){
        if($this->get('categoryid')==0){
            $blOut=false;
        }else{
            try{
                $aResponse= MagnaConnector::gi()->submitRequestCached(array(
                    'ACTION' => 'VariationsEnabled',
                    'DATA' => array (
                        'CategoryID' => $this->get('categoryid'),
                        'Site' => MLModul::gi()->getConfig('site')
                    ),
                ),self::iEbayLiveTime);
                if (
                    isset($aResponse['DATA']['VariationsEnabled'])
                    && ('true' == (string)$aResponse['DATA']['VariationsEnabled'])
                ) {
                    $blOut=true;
                }else{
                    $blOut=false;
                }
            } catch (MagnaException $e) {
                echo $e->getMessage();
                $blOut=false;
            }
        }
        return $blOut;
    }


    public function productRequired() {

        if ($this->get('categoryid') == 0) {
            $blOut = false;
        } else {
            try {
                $aResponse = MagnaConnector::gi()->submitRequest(array(
                    'ACTION' => 'ProductRequired',
                    'DATA' => array(
                        'CategoryID' => $this->get('categoryid'),
                        'Site' => MLModul::gi()->getConfig('site')),
                ));
                if (
                        isset($aResponse['DATA']['ProductRequiredEnabled']) &&
                        (
                                ('Required' == (string) $aResponse['DATA']['ProductRequiredEnabled']) ||
                                ('Enabled' == (string) $aResponse['DATA']['ProductRequiredEnabled'])
                        )
                ) {
                    $blOut = true;
                }
            } catch (MagnaException $e) {
                $blOut = false;
            }
        }
        return $blOut;
    }

    public function getConditionValues(){
        if($this->get('categoryid')==0){
            $blOut=false;
        }else{
            try{
                $aResponse= MagnaConnector::gi()->submitRequestCached(array(
                    'ACTION' => 'GetConditionValues',
                    'DATA' => array (
                        'CategoryID' => $this->get('categoryid'),
                        'Site' => MLModul::gi()->getConfig('site')
                    ),
                ),self::iEbayLiveTime);
                if (
                    isset($aResponse['DATA']['ConditionValues'])
                    && (is_array($aResponse['DATA']['ConditionValues']))
                ) {
                    $blOut=$aResponse['DATA']['ConditionValues'];
                }else{
                    $blOut=false;
                }
            } catch (MagnaException $e) {
                echo $e->getMessage();
                $blOut=false;
            }
        }
        return $blOut;
    }

    /**
     * eBey use old stuff, so it is overridden
     * @param bool $blHtml
     * @return array|mixed|string
     * @throws Exception
     */
    public function getCategoryPath($blHtml = true) {
        $sSeparator = $blHtml ? '&nbsp;<span class="cp_next">&gt;</span>&nbsp;' : ' > ';
        $sPath = '';
        $sCatId = $this->get('categoryid');
        $sStoreId = $this->get('storecategory');
        $sSideId = $this->get('siteid');
        do {
            $oModel = new self;
            $oModel
                ->set('categoryid', $sCatId)
                ->set('storecategory', $sStoreId)
                ->set('siteid', $sSideId);
            if (!$oModel->exists()) {
                MLMessage::gi()->addDebug(__LINE__.':'.microtime(true));
                $oModel->populateCategoryWholePath()
                    ->getChildCategories(true);
            } elseif ((int)$oModel->get('parentid') === 0) { // first level categories are always loaded after expiration, but children of first level should be loaded anyway
                $oModel->getChildCategories(true);
            }
            $sPath = $oModel->get('categoryname').($sPath === '' ? '' : $sSeparator.$sPath);

            $sCatId = $oModel->get('parentid');
        } while ((int)$oModel->get('parentid') !== 0);
        if ($sPath === '') {
            $sPath = MLI18n::gi()->ml_ebay_prepare_form_category_notvalid;
        }
        return $sPath;
    }

    /**
     * Some eBay specification should be execute before main saving
     * @return ML_Modul_Model_Table_Categories_Abstract
     * @throws Exception
     */
    public function save(){
        if ($this->aData['categoryid'] == $this->aData['parentid']) {
            $this->aData['parentid'] = 0;
        }
        if($this->get('storecategory')==0){
            $iExpires=self::iEbayLiveTime;
        }else{
            $iExpires=self::iStoreLiveTime;
        }
        $this->set('expires',date('Y-m-d H:i:s', time() + $iExpires));
        return parent::save();
    }


    public function getAttributes(){
        if ($this->get('storecategory') == 1) {//store dont have attributes
            return array();
        }elseif($this->get('categoryid')==0){
            return array();
        }else{
            $aRequest=array(
                    "SUBSYSTEM" => "eBay",
                    'MARKETPLACEID' => MLModul::gi()->getMarketPlaceId(),
                    'DATA' => array(
                        'CategoryID' => $this->get('categoryid'),
                        'FormStructure' => true,
                        'Site' => MLModul::gi()->getConfig('site')
                    )
            );
            try {
                $aRequest['ACTION']='GetItemSpecifics';
                $aSpecifics = MagnaConnector::gi()->submitRequestCached($aRequest, self::iEbayLiveTime);
            } catch (MagnaException $e) {
                $aSpecifics['STATUS'] = 'ERROR';
                $aSpecifics['DATA'] = array();
            }
            if ($aSpecifics['STATUS'] == 'SUCCESS' && count($aSpecifics['DATA'])) {
                return $aSpecifics['DATA'];
            } else {
                try {
                    $aRequest['ACTION']='GetAttributes';
                    $aAttributes = MagnaConnector::gi()->submitRequestCached($aRequest, self::iEbayLiveTime);
                    if ($aAttributes['STATUS'] == 'SUCCESS' && count($aAttributes['DATA'])) {
                        return $aAttributes['DATA'];
                    } else {
                        return array();
                    }
                } catch (MagnaException $e) {
                    return array();
                }
            }
        }
    }

    /**
     *@return string root >kategorie > till > current
     * */
    public function getClickPath($separator = ' > ') {
        // Since previous version of eBay code had getCategoryPath totally differently defined
        // here we just need to call parent getCategoryPath method and leave rest of the method same as in base class
        foreach (parent::getCategoryPath() as $oParentCat) {
            $aTitles[] = $oParentCat->get('categoryname');
        }

        if (isset($aTitles)) {
            $aTitles = array_reverse($aTitles);
            return implode($separator, $aTitles).(count($aTitles) == 0 ? '' : $separator);
        }

        return '';
    }

    public function getCategoryPathArray()
    {
        return parent::getCategoryPath();
    }


    public function getChildCategories($blForce = false) {
        if ((int)$this->get('storecategory') !== 1) {
            return parent::getChildCategories($blForce);
        } else {
            $this
                ->set('storecategory', '1')
                ->set('siteid', MLModul::gi()->getMarketPlaceId())
                ->getList()
                ->delete();
            $oCategory = new $this;
            foreach (MLModul::gi()->getStoreCategories() as $aRow) {
                if (isset($aRow['mpID'])) {
                    $aRow['SiteID'] = $aRow['mpID'];
                    unset($aRow['mpID']);
                }
                $aRow['ParentID'] = ((float)$aRow['ParentID'] === (float)$aRow['CategoryID']) ? 0 : $aRow['ParentID'];
                foreach ($aRow as $sKey => $sValue) {
                    $oCategory->set($sKey, $sValue);
                }
                $oCategory->save($aRow)->init();
            }
            return MLDatabase::factory('ebay_categories')
                ->set('storecategory', '1')
                ->set('siteid', MLModul::gi()->getMarketPlaceId())
                ->set('parentid', $this->get('categoryid'))->getList();
        }

    }

    static protected $aTopTen = null;
    /**
     * @param $sType
     * @param array $aConfig
     * @return array
     * @throws Exception
     */
    public function getTopTenCategories($sType, $aConfig = array()) {
        if(self::$aTopTen === null) {
            $sType = strtolower($sType);
            $blStoreCat = strpos($sType, 'topstorecategory') === 0;
            if ($blStoreCat) {
                try {
                    $aStoreData = MagnaConnector::gi()->submitRequestCached(array('ACTION' => 'HasStore'));
                } catch (MagnaException $e) {
                    echo print_m($e->getErrorArray(), 'Error');
                }
                if (!$aStoreData['DATA']['Answer'] == 'True') {
                    throw new Exception('noStore');
                }
            }
            $sTopTenCatSql = $this->getTopTenQuery($sType);

            $aTopTenCatSql = MLDatabase::getDbInstance()->fetchArray($sTopTenCatSql, true);
            $aTopTenCatIds = array();
            asort($aTopTenCatSql);
            $iMax = 20; // If all categories are expired we couldn't load all topten categories from ebay, it takes a lot of time and limit it each request to load 10 category
            foreach ($aTopTenCatSql as $iCatId) {
                $oCategory = MLDatabase::factory('ebay_categories')->set('categoryid', $iCatId)->set('storecategory', (int)$blStoreCat);
                if (!$oCategory->exists()) {
                    $iMax--;
                }
                if ($iMax === 0) {
                    break;
                }
                $aTopTenCatIds[$iCatId] = $oCategory->getCategoryPath();
                if (empty($aTopTenCatIds[$iCatId])) {
                    unset($aTopTenCatIds[$iCatId]);
                    MLDatabase::getDbInstance()->query("UPDATE `magnalister_ebay_prepare` SET `{$sType}`=0 WHERE `{$sType}`='".$iCatId."' AND mpID='".MLModul::gi()->getMarketPlaceId()."'");//better siteid instead mpid
                }
            }
            asort($aTopTenCatIds);
            self::$aTopTen = $aTopTenCatIds;
        }
        return self::$aTopTen;
    }

    protected function getTopTenQuery($sType) {
        return "
            SELECT DISTINCT `{$sType}`
            FROM `magnalister_ebay_prepare` 
            WHERE `{$sType}` <> 0 and mpID = '".MLModul::gi()->getMarketPlaceId()."'
            GROUP BY `{$sType}` 
            ORDER BY count( `{$sType}` ) DESC
            ".(
            (int)MLModul::gi()->getConfig('topten') !== 0
                ? 'LIMIT '.(int)MLModul::gi()->getConfig('topten')
                : ''
            );
    }
}
