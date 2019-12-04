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
MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_Abstract');

class ML_Ebay_Controller_Ebay_Prepare_Match_Manual extends ML_Productlist_Controller_Widget_ProductList_Abstract {

    protected $blRenderVariants = true;
    protected $aParameters = array('controller');

    public function __construct() {
        parent::__construct();
        $aStatistic = $this->getProductList()->getStatistic();
        $iFormTotal = $this->getSelection()->getList()
                        ->set('selectionname', 'match_apply')->getCountTotal();
        if ($aStatistic['iCountTotal'] == 0) {
            if ($iFormTotal == 0) {
                MLHttp::gi()->redirect($this->getParentUrl());
            } else {
                MLHttp::gi()->redirect(str_replace('_manual', '_form', $this->getCurrentUrl()));
            }
        }
    }

    /**
     * 
     * @return ML_Ebay_Model_Modul
     */
    protected function getModule() {
        return MLModul::gi();
    }

    /**
     * 
     * @return ML_Ebay_Model_Table_Ebay_Prepare
     */
    protected function getPrepare() {
        return MLDatabase::factory('ebay_prepare');
    }

    protected function callAjaxEbayItemsearch() {
        $oRequest = MLRequest::gi();
        $oProduct = MLProduct::factory()->set('id', $oRequest->data('id'));
        //print_r($oProduct->data());
        $sKeywords = '';
        $sEpid = '';
        $sName = '';
        $sEan = '';
        $sMpn = '';
        if (!in_array($oRequest->data('searchfree'), array(null, ''))) {
            $sKeywords = $oRequest->data('searchfree');
        } else if (!in_array($oRequest->data('searchepid'), array(null, ''))) {
            $sEpid = $oRequest->data('searchepid');
        } else {
            $sEpid = $this->getPrepare()->set($this->getPrepare()->getProductIdFieldName(), $oProduct->get('id'))->get('epid');
            if (in_array($sEpid, array(null, ''))) {
                $sName = $oProduct->getName();
                $sEan = $oProduct->getEAN();
                $sMpn = $oProduct->getEAN();
            }
        }
        $this->includeViewBuffered('widget_productlist_list_variantarticleadditional_ebay_itemsearch', array(
            'oProduct' => $oProduct,
            'aAdditional' => array('aSearchResult' => $this->getModule()->performItemSearch($sEpid, $sEan, $sMpn, $sName, $sKeywords)),
                )
        );
    }

    protected function callAjaxSaveMatching() {
        $aRequest = $this->getRequest();
        $sEPID = $aRequest['data'];

 /*       if ($sEPID == 'newproduct') {
            $oProduct = MLProduct::factory()->set('id', $aRequest['id']);
            $oProductList = MLProductList::gi('generic')->addVariant($oProduct);
            $oService = $this->getAdditemService()->setProductList($oProductList);
            try {
                $oService->execute();
            } catch (Exception $oEx) {
                MLMessage::gi()->addDebug($oEx);
            }

            if ($oService->haveError()) {
                MLMessage::gi()->addDebug($oService->getErrors());
                //$this->oPrepareList->set('verified', 'ERROR');
            } else {
                //$this->oPrepareList->set('verified', 'OK');
            }
        } else if (strpos($sEPID, 'newproductlike') === 0) {
//            require_once(DIR_MAGNALISTER_MODULES . 'ebay/classes/eBayAddProductToCatalog.php');
//            $addProd = new eBayAddProductToCatalog(array('selectionName' => 'newproductlike'));
//            $addProd->setEPidList(array($data['products_id'] => rtrim(substr($sEPID, strpos($sEPID, '(') + 1), ')')));
//            $aRes = $addProd->submit();
        } else*/
        if ($sEPID === 'false') {
            $sEPID = '';
        }

        $oPrepare = $this->getPrepareModel();
        $oPrepare->set($oPrepare->getPreparedTimestampFieldName(), $this->getLastPreparedTS());
        $oPrepare->set($oPrepare->getProductIdFieldName(), $aRequest['id']);
        $oPrepare->set('epid', $sEPID);
        MLMessage::gi()->addDebug($sEPID);

        if ($oPrepare->exists()) {
            // if the error was bc of lacking ePID, and ePID is now there, set Verified = OK
            if ($oPrepare->get('Verified') == 'ERROR' && (in_array($oPrepare->get('ErrorCode'), array('21920000','21920064','21920071'))) && !empty($sEPID)) {
                $oPrepare->set('Verified', 'OK');
            }
        } else {
            MLMessage::gi()->addDebug("product is not prepared, epid is saving in prepare table.");
        }
        $oPrepare->save();
        $this->getSelection()->set('selectionname', 'match')->set('pID', $aRequest['id'])->delete();
        $this->getSelection()->set('selectionname', 'match_apply')->set('pID', $aRequest['id'])->save();
    }

    public function getPriceObject(ML_Shop_Model_Product_Abstract $oProduct) {
        return MLModul::gi()->getPriceObject();
    }

    public function getCurrentProduct() {
        $oTable = MLDatabase::factory('selection')->set('selectionname', 'match');
        $oSelectList = $oTable->getList();
        /* @var $oSelectList ML_Database_Model_List */
        $aResult = $oSelectList->getQueryObject()->init('aSelect')->select('parentid')->join(array('magnalister_products', 'p', 'pid = p.id'))->getResult();
        $sParent = null;
        foreach ($aResult as $aRow) {//show shippingtime , condition and ... if it is single preparation
            if ($sParent !== null && $sParent != $aRow['parentid']) {
                return null;
            }
            $sParent = $aRow['parentid'];
        }
        $aList = $oTable->getList()->getList();
        $oProduct = MLProduct::factory()->set('id', current($aList)->get('pid'));
        $aPreparedData = MLHelper::gi('Model_Service_Product')->addVariant($oProduct)->getData();
        return current($aPreparedData);
    }

    public function getMarketplacePrice($oProduct) {
        if ($oProduct->get('parentid') == 0) {
            if ($oProduct->isSingle()) {
                $oProduct = $this->getFirstVariant($oProduct);
            } else {
                return array(
                    array(
                        'price' => '&dash;'
                    )
                );
            }
        }
        return array(
            array(
                'price' => $oProduct->getSuggestedMarketplacePrice($this->getPriceObject($oProduct), true, true)
            )
        );
    }

    protected static $sLastPreparedTS = null;

    protected function getLastPreparedTS() {
        if (self::$sLastPreparedTS == null) {
            self::$sLastPreparedTS = date('Y-m-d H:i:s');
        }
        return self::$sLastPreparedTS;
    }

    protected function getSelectionNameValue() {
        return 'match';
    }

    /**
     * 
     * @return ML_Ebay_Model_Table_Ebay_Prepare
     */
    protected function getPrepareModel() {
        return MLDatabase::factory('ebay_prepare');
    }

    /**
     * 
     * @return ML_Database_Model_Table_Selection
     */
    protected function getSelection() {
        return MLDatabase::factory('selection');
    }

    /**
     * 
     * @return ML_Ebay_Model_Service_AddToCatalog
     */
    protected function getAdditemService() {
        return null;
    }

}
