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
 * (c) 2010 - 2017 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
class_exists('ML', false) or die();

$enabled = isset($aField['value']) ? $aField['value'] : 'false';
if (!isset($aField['values']) && isset($aField['i18n']['values'])) {
    $aField['values'] = $aField['i18n']['values'];
}
$aField['type'] = 'radio';
$this->includeType($aField);
?>
<script>
    (function ($) {
        function enableB2b(enable, cls) {
            $(cls).parent().find('input, select').prop('disabled', !enable);
        }

        function showMessage(message) {
            $('<div class="ml-modal dialog2" title="<?php echo addslashes($aField['i18n']['label']) ?>"></div>').html(message)
                .jDialog({
                    width: '500px'
                });
        }

        $('#<?php echo $aField['id'].'_true'; ?>').click(function() {
            <?php if (isset($aField['disable'])) { ?>
                showMessage('<?php echo str_replace("\n", ' ', addslashes($aField['i18n']['disabledNotification'])) ?>');
                $('#<?php echo $aField['id'].'_false'; ?>').click();
            <?php } else { ?>
                showMessage('<?php echo str_replace("\n", ' ', addslashes($aField['i18n']['notification'])) ?>');
                enableB2b(true, '.js-b2b');
                $('#<?php echo str_replace('active', 'discounttype', $aField['id']); ?>').change();
            <?php } ?>
        });

        $('#<?php echo $aField['id'].'_false'; ?>').click(function() {
            enableB2b(false, '.js-b2b');
        });

        <?php if ($enabled === 'false') { ?>
            $(document).ready(function() {
                enableB2b(false, '.js-b2b');
            });
        <?php } ?>
    })(jqml);
</script>
