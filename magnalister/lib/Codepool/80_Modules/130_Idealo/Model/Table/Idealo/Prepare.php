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
MLFilesystem::gi()->loadClass('Database_Model_Table_Prepare_Abstract');
class ML_Idealo_Model_Table_Idealo_Prepare extends ML_Database_Model_Table_Prepare_Abstract {

    protected $sTableName = 'magnalister_idealo_prepare';
    
    protected $aFields = array ( 
        'mpID' => array (
            'isKey' => true,
            'Type' => 'int(11) unsigned', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ),
        'products_id' => array (
            'isKey' => true,
            'Type' => 'int(11)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>''
        ),
        'PreparedTS'   => array (
            'isInsertCurrentTime' => true,
            'Type' => 'datetime', 'Null' => 'NO', 'Default' => '0000-00-00 00:00:00', 'Extra' => '', 'Comment'=>''
        ),
        'Verified' => array (//todo
            'Type' => "enum('OK','ERROR','OPEN','EMPTY')", 'Null' => 'NO', 'Default' => 'OPEN', 'Extra' => '', 'Comment'=>''
        ),
        'Title' => array(
            'Type' => 'varchar(255)', 'Null' => 'NO', 'Default' => '', 'Extra' => '', 'Comment'=>''
        ),
        'Description' => array(
            'Type' => 'text', 'Null' => 'NO', 'Default' => null,'Extra' => '', 'Comment'=>''
        ),
        'Image' => array(
            'Type' => 'text', 'Null' => 'NO', 'Default' => null,'Extra' => '', 'Comment'=>''
        ),
        'Checkout' => array(
             'Type' => "tinyint(1)", 'Null' => 'YES', 'Default' => null, 'Extra' => '', 'Comment'=>''
        ),
        'PaymentMethod' => array(
            'Type' => 'varchar(124)', 'Null' => 'NO', 'Default' => '', 'Extra' => '', 'Comment'=>''
        ),
        'ShippingMethod' => array(
            'Type' => 'varchar(124)', 'Null' => 'NO', 'Default' => '', 'Extra' => '', 'Comment'=>''
        ),
        'ShippingCountry' => array(
            'Type' => 'varchar(124)', 'Null' => 'NO', 'Default' => '', 'Extra' => '', 'Comment'=>''
        ),
        'ShippingCost' => array(
            'Type' => 'decimal(15,4)', 'Null' => 'NO', 'Default' => 1, 'Extra' => '', 'Comment'=>''
        ),
        'ShippingCostMethod' => array(
            'Type' => 'varchar(20)', 'Null' => 'NO','Default' => null, 'Extra' => '', 'Comment'=>''
        ),
        'ShippingTime' => array(
            'Type' => 'text', 'Null' => 'YES','Default' => null, 'Extra' => '', 'Comment'=>''
        ),
    );
    protected $aTableKeys=array(
        'UniqueEntry' => array('Non_unique' => '0', 'Column_name' => 'mpID, products_id'),
    );
    
    protected function setDefaultValues() {
        try {
            $sId = MLRequest::gi()->get('mp');
            if (is_numeric($sId)) {
                $this->set('mpid', $sId);
            }
        } catch (Exception $oEx) {
            
        }
        return $this;
    }
    
    
    
}