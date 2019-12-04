<?php

MLFilesystem::gi()->loadClass('Modul_Helper_Model_Service_OrderData_Normalize');

class ML_Meinpaket_Helper_Model_Service_OrderData_Normalize extends ML_Modul_Helper_Model_Service_OrderData_Normalize {

    protected function normalizeTotals() {
        $this->addMissingTotal();
        parent::normalizeTotals();
        return $this;
    }

}
