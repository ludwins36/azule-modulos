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
 *
 * (c) 2010 - 2016 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
MLFilesystem::gi()->loadClass('Database_Model_Table_Abstract');

class ML_Database_Model_Table_VariantMatching_Abstract extends ML_Database_Model_Table_Abstract {

    protected $aFields = array(
        'mpID' => array(
            'isKey' => true,
            'Type' => 'int(11) unsigned', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'Identifier' => array(
            'isKey' => true,
            'Type' => 'varchar(50)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'CustomIdentifier' => array(
            'isKey' => true,
            'Type' => 'varchar(50)', 'Null' => 'NO', 'Default' => '', 'Extra' => '', 'Comment' => ''
        ),
        'ShopVariation' => array(
            'Type' => 'mediumtext', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''
        ),
        'ModificationDate' => array(
            'Type' => 'datetime', 'Null' => 'NO', 'Default' => '2000-01-01 00:00:00', 'Extra' => '', 'Comment' => ''
        ),
    );

    protected $aTableKeys = array(
        'UniqueEntry' => array('Non_unique' => '0', 'Column_name' => 'mpID, Identifier, CustomIdentifier'),
    );

    /**
     * Gets the list of all saved custom variations.
     * 
     * @return array Array of all custom variations
     */
    public function getCustomVariations() {
        $sQuery = 'SELECT * '
                . '  FROM ' . $this->getTableName()
                . " WHERE NULLIF(CustomIdentifier, '') IS NOT NULL "
                . '   AND mpID = ' . ((int)$this->get('mpID'));
        $aResult = array();
        foreach (MLDatabase::getDbInstance()->fetchArray($sQuery, true) as $aRecord) {
            $sKey = $aRecord['Identifier'] . ':' . $aRecord['CustomIdentifier'];
            $aResult[$sKey] = $aRecord['CustomIdentifier'];
        }
        
        return $aResult;
    }

    /**
     * Gets matched values for selected identifier
     *
     * @param string $sIdentifier Matching identifier (usually category name or ID).
     * @param string  Custom identifier (Additional id if there are subcategories or similar)
     * @return array|bool
     */
    public function getMatchedVariations($sIdentifier, $sCustomIdentifier = '')
    {
        return $this->set('Identifier', $sIdentifier)->set('CustomIdentifier', $sCustomIdentifier)->get('ShopVariation');
    }

    public function getModificationDate($sIdentifier, $sCustomIdentifier = '')
    {
        return $this->set('Identifier', $sIdentifier)->set('CustomIdentifier', $sCustomIdentifier)->get('ModificationDate');
    }

    /**
     * Gets all matchings for current marketplace.
     *
     * @return array
     */
    public function getAllItems() {
        $sQuery = 'SELECT * '
                . '  FROM ' . $this->getTableName()
                . ' WHERE mpID = ' . (int)$this->get('mpID');
        $aResult = array();
        foreach (MLDatabase::getDbInstance()->fetchArray($sQuery, true) as $aRecord) {
            $sKey = $aRecord['Identifier'] . ':' . $aRecord['CustomIdentifier'];
            $aResult[$sKey] = $aRecord['CustomIdentifier'] ?: $aRecord['Identifier'];
        }
        
        return $aResult;
    }

    /**
     * Deletes matched variations.
     *
     * @param string $sIdentifier Saved variation matching identifier
     * @param string $sCustomIdentifier Saved variation matching custom identifier
     */
    public function deleteVariation($sIdentifier, $sCustomIdentifier = '') {
        MLDatabase::getDbInstance()->delete($this->getTableName(), array(
            'mpID' => $this->get('mpID'),
            'Identifier' => $sIdentifier,
            'CustomIdentifier' => $sCustomIdentifier,
        ));
    }

    /**
     * Deletes custom variations.
     *
     * @param string $sIdentifier Identifier for custom variation matching
     */
    public function deleteCustomVariation($sIdentifier) {
        MLDatabase::getDbInstance()->delete($this->getTableName(), array(
            'mpID' => $this->get('mpID'),
            'CustomIdentifier' => $sIdentifier,
        ));
    }

    /**
     * returns value of this->aData
     * @param string $sName
     * @param bool $reload
     * @return mixed
     */
    public function get($sName, $reload = false) {
        $sName = strtolower($sName);
        if ($reload || !isset($this->aData[$sName])) {
            $this->blLoaded = null;
            $this->load();
        }
        return array_key_exists($sName, $this->aData)? MLHelper::getEncoderInstance()->decode($this->aData[$sName]) : null;
    }

    protected function setDefaultValues() {
        try {
            $sId = MLRequest::gi()->get('mp');
            if (is_numeric($sId)) {
                $this->set('mpid', $sId);
            }

            $this->set('CustomIdentifier', '');
        } catch (Exception $oEx) {
        }

        return $this;
    }
    
    public function set($sName, $mValue) {
        $sKey = strtolower($sName);
        if($this->allKeysExists() && $this->blLoaded && in_array($sKey, $this->aKeys) && isset($this->aData[$sKey]) && $this->aData[$sKey] !== $mValue){
            //if object is already load from database, but here we want to set one key with new value, it is needed to reload object with new key value
            $this->init();
        }
        return parent::set($sName, $mValue);
    }
}
