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
 * (c) 2010 - 2016 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
class_exists('ml', false) or die;
$iSelectedMpid = $this->getRequestedMpid();
if ($iSelectedMpid != null) {
    ML::gi()->init(array('mp' => $iSelectedMpid));
    if (!MLModul::gi()->isConfigured()) {
        throw new Exception('module is not configured');
    }
}
?>
<form method="post" action="<?php echo $this->getCurrentUrl(); ?>">
    <div style="display:none">
        <?php foreach (MLHttp::gi()->getNeededFormFields() as $sKey => $sValue) { ?>
            <input type="hidden" name="<?php echo $sKey ?>" value="<?php echo $sValue ?>" />
        <?php } ?>
    </div>
    <table>
        <tr>
            <td>
                <label for="ml-sku">SKU :</label></td><td>
                <input type="text" name="<?php echo MLHttp::gi()->parseFormFieldName('sku') ?>" value="<?php echo $this->getRequestedSku() ?>">
            </td>
        </tr>
        <tr>
            <td><label for="ml-marketplace">marketplace :</label></td><td>
                <select name="<?php echo MLHttp::gi()->parseFormFieldName('mpid') ?>">
                    <option value="">--</option>
                    <?php
                    $aTabIdents = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.tabident')->get('value');
                    foreach (magnaGetInvolvedMarketplaces() as $sMarketPlace) {
                        foreach (magnaGetInvolvedMPIDs($sMarketPlace) as $iMarketPlace) {
                            ?>
                            <option value="<?php echo $iMarketPlace ?>" <?php echo $iSelectedMpid == $iMarketPlace ? ' selected=selected ' : '' ?>>
                                <?php echo $sMarketPlace . ' (' . (isset($aTabIdents[$iMarketPlace]) && $aTabIdents[$iMarketPlace] != '' ? $aTabIdents[$iMarketPlace] . ' - ' : '') . $iMarketPlace . ')'; ?>
                            </option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <label for="ml-pricetype">price type</label></td><td><input type="text" id="ml-pricetype" name="<?php echo MLHttp::gi()->parseFormFieldName('pricetype') ?>" value="<?php echo $this->getRequest('pricetype') ?>">
            </td> 
        </tr>
        <tr><td><button type="sumit" class="mlbtn">Search SKU</button></td><td></td></tr>
    </table>
</form>
<hr />
<table style="table-layout: fixed;">
    <thead><tr><th>Master</th><th><sup>Variants</sup></th><th style="width: 3em;"></th><th>Variant</th><th><sup>Master</sup></th></tr></thead>
    <tbody>
        <tr>
            <td style="vertical-align: top">
                <?php
                if (($oProduct = $this->getProduct(true)) !== null) {
                    $aData = $oProduct->data();
                    if ($oProduct->exists()) {
                        if (count($aData) > 1) {
                            $aData['methods'][get_class($oProduct) . '::getTax()'] = $oProduct->getTax();
                        }
                        $aData['methods'][get_class($oProduct) . '::getBasePriceString(20)'] = $oProduct->getBasePriceString(20);
                        $aData['methods'][get_class($oProduct) . '::getImages()'] = $oProduct->getImages();
                    }
                    new dBug($aData, '', true);
                }
                ?>
            </td>
            <td style="vertical-align: top">
                <?php
                if ($oProduct instanceof ML_Shop_Model_Product_Abstract) {
                    try {
                        foreach ($oProduct->getVariants() as $oVariant) {
                            new dBug($oVariant->data(), '', true);
                        }
                    } catch (Exception $oEx) {
                        echo $oEx->getMessage();
                    }
                }
                ?>
            </td>
            <td></td>
            <td style="vertical-align: top">
                <?php
                if (($oProduct = $this->getProduct(false)) !== null) {
                    $aData = $oProduct->data();
                    if ($oProduct->exists()) {
                        if (count($aData) > 1) {
                            $aData['methods'][get_class($oProduct) . '::getTax()'] = $oProduct->getTax();
                        }
                        /* @var  $oProduct ML_Shop_Model_Product_Abstract
                         */
                        try {
                            $aStockConf = MLModul::gi()->getStockConfig($this->getRequest('pricetype'));
                            $aData['methods'][get_class($oProduct) . '::getSuggestedMarketplaceStock()'] = $oProduct->getSuggestedMarketplaceStock($aStockConf['type'], $aStockConf['value']);
                            $aData['methods'][get_class($oProduct) . '::getSuggestedMarketplacePrice()'] = $oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject($this->getRequest('pricetype')), true, true);
                            $fPrice = $oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject());
                            $aData['methods'][get_class($oProduct) . '::getBasePriceString()'] = $oProduct->getBasePriceString($fPrice);
                        } catch (Exception $oExc) {
                            $aData['methods'][get_class($oProduct) . '::getSuggestedMarketplacePrice(20)'] = 20;
                            $aData['methods'][get_class($oProduct) . '::getBasePriceString(20)'] = $oProduct->getBasePriceString(20);
                        }
                        $aData['methods'][get_class($oProduct) . '::isActive()'] = $oProduct->isActive();
                        $aData['methods'][get_class($oProduct) . '::getDescription()'] = htmlentities($oProduct->getDescription());
                        $aData['methods'][get_class($oProduct) . '::getVariatonData()'] = $oProduct->getVariatonDataOptinalField(array('code', 'valueid', 'name', 'value'));
                        $aData['methods'][get_class($oProduct) . '::getStock()'] = $oProduct->getStock();
                        $aData['methods'][get_class($oProduct) . '::getImages()'] = $oProduct->getImages();
                    }
                    new dBug($aData, '', true);
                }
                ?>
            </td>
            <td style="vertical-align: top">
                <?php
                if ($oProduct instanceof ML_Shop_Model_Product_Abstract) {
                    try {
                        new dBug($oProduct->getParent()->data(), '', true);
                    } catch (Exception $oEx) {
                        echo $oEx->getMessage();
                    }
                }
                ?>
            </td>
        </tr>
    </tbody>
</table>
<?php
ML::gi()->init(array());
