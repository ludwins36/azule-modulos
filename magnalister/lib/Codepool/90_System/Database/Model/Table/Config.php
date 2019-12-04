<?php

class ML_Database_Model_Table_Config extends ML_Database_Model_Table_Abstract {

    protected static $aCache = array();

    protected $sTableName = 'magnalister_config';

    protected $aFields = array(
        'mpID' => array(
            'isKey' => true,
            'Type' => 'int(8) unsigned', 'Null' => 'NO', 'Default' => 0, 'Extra' => '', 'Comment' => ''),
        'mkey' => array(
            'isKey' => true,
            'Type' => 'varchar(100)', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''),
        'value' => array(
            'Type' => 'longtext', 'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment' => ''),
    );
    protected $aTableKeys = array(
        'UniqueKey' => array('Non_unique' => '0', 'Column_name' => 'mpID, mkey'),
    );

    protected function setDefaultValues() {
        return $this;
    }

    public function load() {
        if (
            isset($this->aData['mpid']) && isset($this->aData['mkey'])
            && isset(self::$aCache[$this->aData['mpid']][$this->aData['mkey']])
            && $this->blLoaded === true
        ) {
            $this->aData['value'] = self::$aCache[$this->aData['mpid']][$this->aData['mkey']];
        } else {
            parent::load();
            if (isset($this->aData['value'])) {
                self::$aCache[$this->aData['mpid']][$this->aData['mkey']] = $this->aData['value'];
            }
        }
        return $this;
    }

    public function save() {
        if (
            isset($this->aData['mpid']) && isset($this->aData['mkey']) && isset($this->aData['value'])
        ) {
            self::$aCache[$this->aData['mpid']][$this->aData['mkey']] = $this->aData['value'];
        }
        return parent::save();
    }

    /**
     * return true if all necessary global configuration is already configured, otherwise return false
     * @var $sKey string
     * @return bool
     */
    public function isGCConfigured($sKey = null) {
        $aRequest = MLRequest::gi()->data();
        if ($sKey === null) {
            $aGCKeys = array(
                'general.passphrase',
                'general.keytype'
            );
        } else {
            $aGCKeys = array($sKey);
        }
        $blReturn = true;
        if (isset($aRequest['controller'], $aRequest['field']) && $aRequest['controller'] === 'configuration') { // mangnalister is saving configuration don't need to get value from database
            foreach ($aGCKeys as $sGCKey) {
                $blReturn = $blReturn && !empty($aRequest['field'][$sGCKey]);
                if (!$blReturn) {
                    break;
                }
            }

        } else {
            foreach ($aGCKeys as $sGCKey) {
                $sDBValue = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', $sGCKey)->get('value');
                $blReturn = $blReturn && !empty($sDBValue);
                if (!$blReturn) {
                    break;
                }
            }
        }
        return $blReturn;

    }

}