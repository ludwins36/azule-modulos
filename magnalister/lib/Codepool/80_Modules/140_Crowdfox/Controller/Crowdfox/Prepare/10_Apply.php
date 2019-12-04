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

MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_Selection');

class ML_Crowdfox_Controller_Crowdfox_Prepare_Apply extends ML_Productlist_Controller_Widget_ProductList_Selection {

    protected $aParameters = array('controller');

    public static function getTabTitle() {
        return MLI18n::gi()->get('crowdfox_prepare_apply');
    }

    public static function getTabActive() {
        return MLModul::gi()->isConfigured();
    }

    public static function getTabDefault() {
        $sValue = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.ean')->get('value');

        return (empty($sValue)) ? false : true;
    }

    protected function getListName() {
        $sValue = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.ean')->get('value');
        if (empty($sValue)) {
        }

        return parent::getListName();//'apply';
    }

    public function getProductListWidget() {
        try {
            if ($this->isCurrentController()) {

                $this->oPrepareList = MLDatabase::factory(MLModul::gi()->getMarketPlaceName() . '_prepare')->getList();
                $this->oPrepareList->getQueryObject()->where("Gtin = ''");
                if (count($this->oPrepareList->getList()) > 0) {
                    MLMessage::gi()->addNotice($this->__('crowdfox_no_gtin'));
                }

                return parent::getProductListWidget();
            }

            return $this->getChildController('form')->render();
        } catch (Exception $oExc) {
            MLHttp::gi()->redirect($this->getParentUrl());
        }
    }

    public function getPriceObject(ML_Shop_Model_Product_Abstract $oProduct) {
        return MLModul::gi()->getPriceObject();
    }

    public function __construct() {
        parent::__construct();
        try {
            $sExecute = $this->oRequest->get('execute');
            if (in_array($sExecute, array('unprepare', 'reset'))) {
                $oModel = MLDatabase::factory('crowdfox_prepare');
                $oList = MLDatabase::factory('selection')->set('selectionname', 'apply')->getList();
                foreach ($oList->get('pid') as $iPid) {
                    $oModel->init()->set('products_id', $iPid);
                    switch ($sExecute) {
                        case 'unprepare': {//delete from crowdfox_prepare
                            $oModel->delete();
                            break;
                        }

                        case 'reset': {//set products title, description and images of crowdfox_prepare to actual product values
                            if ($oModel->exists()) {
                                $iLang = MLModul::gi()->getConfig('lang');
                                MLProduct::factory()->set('id', $iPid)->setLang($iLang);
                                $this->reset($oModel, $iPid);
                            }

                            break;
                        }
                    }
                }
            }
        } catch (Exception $oEx) {
            //            echo $oEx->getMessage();
        }
    }

    public function productSelectable(ML_Shop_Model_Product_Abstract $oProduct, $blRender) {
        return !$blRender || $oProduct->get('parentid') == 0;
    }

    private function substringAferLast($sNeedle, $sString) {
        if (!is_bool($this->strrevpos($sString, $sNeedle))) {
            return substr($sString, $this->strrevpos($sString, $sNeedle) + strlen($sNeedle));
        }
    }

    private function strrevpos($instr, $needle) {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if ($rev_pos === false) {
            return false;
        } else {
            return strlen($instr) - $rev_pos - strlen($needle);
        }
    }

    private function reset($oModel, $iPid) {
        $sName = MLProduct::factory()->set('id', $iPid)->getName();
        $this->oPrepareHelper = MLHelper::gi('Model_Table_' . MLModul::gi()->getMarketPlaceName() . '_PrepareData');
        if (!empty($sName)) {
            $sName = $this->oPrepareHelper->crowdfoxSanitizeTitle($sName);
            if (isset($sName) &&
                mb_strlen($sName, 'UTF-8') > ML_Crowdfox_Helper_Model_Table_Crowdfox_PrepareData::TITLE_MAX_LENGTH
            ) {
                $sName = mb_substr($sName, 0, ML_Crowdfox_Helper_Model_Table_Crowdfox_PrepareData::TITLE_MAX_LENGTH - 3,
                        'UTF-8') . '...';
            }

            $oModel->set('ItemTitle', $sName)->save();
        } else {
            $oModel->set('ItemTitle', '')->save();
        }

        $sDescription = MLProduct::factory()->set('id', $iPid)->getDescription();
        if (!empty($sDescription)) {
            $sDescription = $this->oPrepareHelper->crowdfoxSanitizeDescription($sDescription);

            if (isset($sDescription) &&
                mb_strlen($sDescription, 'UTF-8') > ML_Crowdfox_Helper_Model_Table_Crowdfox_PrepareData::DESC_MAX_LENGTH
            ) {
                $aField['value'] = mb_substr($sDescription, 0,
                        ML_Crowdfox_Helper_Model_Table_Crowdfox_PrepareData::DESC_MAX_LENGTH - 3, 'UTF-8') . '...';
            }

            $oModel->set('Description', $sDescription)->save();
        } else {
            $oModel->set('Description', '')->save();
        }

        $aImages = MLProduct::factory()->set('id', $iPid)->getImages();
        if (!empty($aImages)) {
            $aIds = array();
            foreach ($aImages as $sImagePath) {
                $sId = $this->substringAferLast('\\', $sImagePath);
                if (isset($sId) === false || strpos($sId, '/') !== false) {
                    $sId = $this->substringAferLast('/', $sImagePath);
                }

                $aIds[] = $sId;
                if (count($aIds) == ML_Crowdfox_Helper_Model_Table_Crowdfox_PrepareData::MAX_NUMBER_OF_IMAGES) {
                    break;
                }
            }

            $oModel->set('Images', json_encode($aIds))->save();
        }
    }

}