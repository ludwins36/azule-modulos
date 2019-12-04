
<form method="post" action="<?php echo $this->getCurrentUrl(); ?>">
    <div style="display:none">
        <?php foreach (MLHttp::gi()->getNeededFormFields() as $sKey => $sValue) { ?>
            <input type="hidden" name="<?php echo $sKey ?>" value="<?php echo $sValue ?>" />
        <?php } ?>
    </div>
    <table>
        <tr>
            <td>
                <label for="ml-sku">OrderSpecial :</label>
            </td>
            <td>
                <input type="text" name="<?php echo MLHttp::gi()->parseFormFieldName('orderspecial') ?>" value="<?php echo $this->getRequestedOrderSpecial() ?>">
            </td>
        </tr>
        <tr>
            <td><button type="sumit" class="mlbtn">Search Order</button></td>
        </tr>
        <?php if (MLSetting::gi()->blDev) { ?>
            <tr>
                <td>
                    <select  name="<?php echo MLHttp::gi()->parseFormFieldName('apirequest') ?>">
                        <option value="yes">send request on API</option>
                        <option value="no">don't send request on API</option>
                    </select>
                </td>
                <td><button type="submit" class="mlbtn" name="<?php echo MLHttp::gi()->parseFormFieldName('action') ?>" value="unacknowledge">UnAcknowledge Imported Order</button></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="sumit" class="mlbtn" name="<?php echo MLHttp::gi()->parseFormFieldName('action') ?>" value="recreateProducts">Delete and recreate order products </button></td>
            </tr>
        <?php } ?>
    </table>
</form>
<?php
if (is_array($this->getOrderData())) {
    new dBug($this->getOrderData(), '', true);
} else {
    ?>Order not found.<?php
}
?>
<?php
/**
 * @deprecated this is only for ebay-order bug from 17.07.2017
 */
//if ($this->getRequestedOrderSpecial() === 'GetWrongOrdersWithDuplicatedItems') {
//    
?>
<!--        <table border="1">
        <tr><th>Marketplace</th><th>Marketplace-ID</th><th>order</th><th>response</th></tr>-->
<?php
//        foreach (MLShop::gi()->getMarketplaces() as $iMpId => $sMpName) {
//            if ($sMpName === 'ebay') {
//                $aOrders = MagnaConnector::gi()->submitRequest(array(
//                    'ACTION' =>'GetWrongOrdersWithDuplicatedItems',
//                    'SUBSYSTEM' => 'eBay',
//                    'MARKETPLACEID' => $iMpId,
//                ));
//                foreach(array_key_exists('DATA', $aOrders) ? $aOrders['DATA'] : array() as $aOrder) {
//                    
?>
<!--        <tr><td><?php // echo $sMpName      ?></td><td><?php // echo $iMpId;      ?></td><td><?php // new dBug($aOrder);      ?></td><td>-->
<?php
//                        try {
//                            if (MLOrder::factory()->unAcknowledgeImportedOrder($sMpName, $iMpId, $aOrder['MOrderID'], $aOrder['ShopOrderID'])) {
//                                echo 'success';
//                            } else {
//                                echo 'fail';
//                            }
//                        } catch (Exception $oEx) {
//                            new dBug('Error: '. $oEx->getMessage());
//                        }
//                    
?>
<!--        </td></tr>-->
<?php
//                }
//            }
//        }
//    
?>
<!--</table>-->
            <?php
//}
