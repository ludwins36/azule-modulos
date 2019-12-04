<?php

class ML_Idealo_Model_Service_SyncInventory extends ML_Modul_Model_Service_SyncInventory_Abstract {
    
   public function execute() {
        include_once MLFilesystem::getOldLibPath('php/modules/idealo/crons/IdealoSyncInventory.php');
        $oModul = new IdealoSyncInventory($this->oModul->getMarketplaceId(), $this->oModul->getMarketplaceName());
        $oModul->process();
        return $this;
    }
}
