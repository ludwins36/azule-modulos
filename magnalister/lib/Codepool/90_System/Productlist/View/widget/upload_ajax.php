<?php
/* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
/* @var $sProcess  string */
/* @var $sError  string */
/* @var $sSuccess  string */
class_exists('ML', false) or die();
?>
<div style="display:none;" id="js-ml-modal-uploadConfirmPurge" title="<?php echo MLI18n::gi()->get('ML_HINT_HEADLINE_CONFIRM_PURGE'); ?>">
    <?php echo MLI18n::gi()->get('ML_TEXT_CONFIRM_PURGE'); ?>
</div>
<script type="text/javascript">/*<![CDATA[*/
    (function ($) {
        $(document).ready(function () {
            $('.js-marketplace-upload').on("click forceClick", function (event) {
                var form = $(this.form);
                if(form.find('[value="checkinPurge"]').length > 0 && event.type === 'click') {
                    var eModal = $("#js-ml-modal-uploadConfirmPurge");
                    eModal.dialog({
                        modal: true,
                        width: '600px',
                        buttons: [
                            {
                                text: "<?php echo $this->__('ML_BUTTON_LABEL_ABORT'); ?>",
                                click: function () {
                                    $(this).dialog("close");
                                    return false;
                                }
                            },
                            {
                                text: "<?php echo $this->__('ML_BUTTON_LABEL_OK'); ?>",
                                click: function () {
                                    $(this).dialog("close");
                                    form.find('[type="submit"]').trigger('forceClick');
                                    return false;
                                }
                            }
                        ]
                    });
                    return false;
                }
                $(this).magnalisterRecursiveAjax({
                    sOffset: '<?php echo MLHttp::gi()->parseFormFieldName('offset') ?>',
                    sAddParam: '<?php echo MLHttp::gi()->parseFormFieldName('ajax') ?>=true',
                    oFinalButtons: {
                        oError: [
                            {text: 'Ok', click: function () {
                                    var eDialog=$('#recursiveAjaxDialog');
                                    if (eDialog.find(".requestErrorBox").is(':hidden')) {
                                        window.location.href = '<?php
                                                $sMpId = MLModul::gi()->getMarketPlaceId();
                                                $sMpName = MLModul::gi()->getMarketPlaceName();
                                                echo $this->getUrl(array('controller' => "{$sMpName}:{$sMpId}_errorlog"));
                                                ?>';
                                    } else {
                                        window.location.href = '<?php echo $this->getCurrentUrl() ?>';
                                    }
                                }}
                        ],
                        oSuccess: [
                            {text: 'Ok', click: function () {
                                    window.location.href = '<?php echo $this->getUrl(array('controller' => "{$sMpName}:{$sMpId}_listings")); ?>';
                                }}
                        ]
                    },
                    oI18n: {
                        sProcess: <?php echo json_encode($sProcess) ?>,
                        sError: <?php echo json_encode($sError) ?>,
                        sErrorLabel: <?php echo json_encode($this->__('ML_ERROR_LABEL'))?>,
                        sSuccess: <?php echo json_encode($this->__('ML_STATUS_SUBMIT_PRODUCTS_SUMMARY'))?>,
                        sSuccessLabel: <?php echo json_encode($sSuccess) ?>,
                        <?php if(array_key_exists('sInfo', get_defined_vars())) { ?>
                        sInfo: <?php echo json_encode($sInfo) ?>,
                        <?php } ?>
                    },
                    onProgessBarClick: function (data) {
                        console.dir({data: data});

                    },
                    onFinalize: function (blError) {

                    },
                    blDebug: <?php echo MLSetting::gi()->get('blDebug') ? 'true' : 'false' ?>,
                    sDebugLoopParam: "<?php echo MLHttp::gi()->parseFormFieldName('saveSelection') ?>=true"
                });
                return false;
            });
        });
    })(jqml);
    /*]]>*/</script>