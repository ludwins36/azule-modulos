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
MLFilesystem::gi()->loadClass('Productlist_Helper_Model_ProductList_List_Abstract') ;
 class ML_Prestashop_Helper_Model_ProductList_List extends ML_Productlist_Helper_Model_ProductList_List_Abstract{

     protected $oI18n = null ;
     protected $aProducts = null ;

     /** @var ML_Database_Model_Query_Select $oSelect   */
     protected $oSelect = null ;
     protected $aFields = array () ;
     protected $aList = array () ;

     /**
      * @var ML_Prestashop_Model_Product 
      */
     protected $oLoadedProduct = null ;

     /** @var Product $oProduct */
//     protected $oProduct = null ;
     protected $aProduct = null ;

     //protected $aAttributes = null ;

     public function __construct() {
         $this->oI18n = MLI18n::gi() ;
         $this->aAttributes = MLFormHelper::getShopInstance()->getManufacturerPartNumber() ;
     }

     /**
      * clear all property value of this class and refresh the class
      * @return \ML_Prestashop_Helper_Shop_ProductList_List
      */
     public function clear() {
         $oRef = new ReflectionClass($this) ;
         foreach ( $oRef->getDefaultProperties() as $sKey => $mValue ) {
             $this->$sKey = $mValue ;
         }
         $this->__construct() ;
         return $this ;
     }

     public function setCollection(ML_Database_Model_Query_Select $oSelect) {
         $this->oSelect = $oSelect ;
         return $this ;
     }

     public function getLoadedList() {
         if ( count($this->aList) <= 0 ) {
             $aIDs = array () ;
             $aPrducts = $this->oSelect->getResult() ;
             foreach ( $aPrducts as $aProduct ) {
                 $aIDs[] = $aProduct['id_product'] ;
             }
             return $aIDs ;
         } else {
             return array_keys($this->aList) ;
         }
     }

     public function getList() {
         $aProducts = $this->oSelect->getResult() ;// echo "<br>$this->oSelect<br>";
         foreach ( $aProducts as $aProduct ) {
             $oMLProduct = MLProduct::factory();
             $oProduct = $oMLProduct->loadPrestashopProduct($aProduct['id_product'], true);
             /* @var $oMLProduct ML_Shop_Model_Product_Abstract */
             $oMLProduct->loadByShopProduct($oProduct) ;
             $this->oLoadedProduct = $oMLProduct ;
             foreach ( $this->aFields as $sField ) {

                 $mReturn = method_exists($this , $sField)?$this->{$sField}():(isset($oProduct->$sField)?$oProduct->$sField:$this->shopSystemAttribute($sField)) ;
                 if ( $mReturn !== null ) {
                     $this->aMixedData[$oMLProduct->get("id")][$sField] = $mReturn ;
                 }
             }
             $this->aList[$oProduct->id] = $oMLProduct ;
         }
         return $this->aList ;
     }

    public function getMixedData($oProduct, $sKey){
        if(isset($this->aMixedData[$oProduct->get("id")]) && isset($this->aMixedData[$oProduct->get("id")][$sKey])) {
            return $this->aMixedData[$oProduct->get("id")][$sKey];
        } else {
            $this->oLoadedProduct=$oProduct;
            return method_exists($this, $sKey) ? $this->{$sKey}() : $this->shopSystemAttribute($sKey);
        }
    }


    public function shopSystemAttribute($sCode , $blUse = true , $sTitle = null, $sTypeVariant = null) {
        if ( $this->oLoadedProduct === null ) {
            if ( !in_array($sCode , $this->aFields) ) {
                if ( $blUse && array_key_exists($sCode , $this->aAttributes) ) {
                    $this->aFields[] = '_' . $sCode ;
                    $this->aHeader['_' . $sCode] = array (
                        'title' => $sTitle === null?$this->aAttributes[$sCode]:$sTitle ,
                        'order' => $sCode ,
                        'type' => 'simpleText'
                    ) ;
                }
            }
            return $this ;
        } else {
            $sCode = substr($sCode , 1) ;
            $mValue = $this->oLoadedProduct->getAttributeValue($sCode);
            return in_array($mValue, array('', null)) ? '-' : $mValue;
        }
    }

     public function quantityShop() {
        if ($this->oLoadedProduct === null) {
            $this->aFields[] = 'quantityShop';
            $this->aHeader['quantityShop'] = array(
                'title' => $this->oI18n->get('Productlist_Header_sStockShop'),
                'order' => 'quantity',
                'type' => 'simpleText'
                    );
            return $this;
        } else {
            return $this->oLoadedProduct->getStock();
        }
    }
     
    public function priceShop($sTypeVariant=null){
        if($this->oLoadedProduct===null){
            if(!in_array(__function__, $this->aFields)){
                $this->aFields[]=__function__;
                $this->aHeader[__function__]=array(
                    'title'=>$this->oI18n->get('Productlist_Header_sPriceShop'),
                    'order'=>'price',
                    'type'=>'priceShop',
                    'type_variant'=>$sTypeVariant===null?'priceShop':$sTypeVariant
                );
            }
            return $this;
        }
    }
    
 }