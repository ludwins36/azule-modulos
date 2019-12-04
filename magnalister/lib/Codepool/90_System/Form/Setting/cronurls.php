<?php
foreach (array('SyncOrderStatus', 'SyncInventory', 'ImportOrders') as $sSync) {
    $aCronUrlParam['do'] = $sSync;
    MLSetting::gi()->set('s'.$sSync.'Url', MLHttp::gi()->getFrontendDoUrl($aCronUrlParam), true);
}