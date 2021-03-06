<?php
class_exists('ML', false) or die();
?>
<?php
/* @var $this  ML_Amazon_Controller_Amazon_ShippingLabel_Orderlist */
/* @var $oList ML_Amazon_Model_List_Amazon_Order */
/* @var $aStatistic array */

$sMpId = MLModul::gi()->getMarketPlaceId();
$sMpName = MLModul::gi()->getMarketPlaceName();

$sUrlPrefix = "{$sMpName}:{$sMpId}_";
$sI18nPrefix = 'ML_'.ucfirst($sMpName).'_';
?>
<table class="actions">
    <tbody class="firstChild">
        <tr>
            <td>
                <div class="actionBottom">
                    <button class="mlbtn action right" type="submit" ><?php echo sprintf($this->__('form_action_wizard_save'), $this->__("{$sI18nPrefix}Shippinglabel_Upload_Summary")); ?></button>
                    <a class="mlbtn backbtn right" href="<?php echo $this->getUrl(array('controller' => "{$sUrlPrefix}shippinglabel_upload_form")); ?>">
                        <?php echo $this->__('ML_BUTTON_LABEL_BACK') ?>
                    </a>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
    </tbody>
</table>
