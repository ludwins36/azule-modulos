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
if (!isset($aField['values']) && isset($aField['i18n']['values'])) {
    $aField['values'] = $aField['i18n']['values'];
}
$aField['type'] = 'select';
$this->includeType($aField);
?>
<script>
    (function ($) {
        function enableB2b(enable, cls) {
            $(cls).parent().find('input, select').prop('disabled', !enable);
        }

        $(document).ready(function() {
            if ($('#<?php echo str_replace('discounttype', 'active_true', $aField['id']); ?>').prop('checked')) {
                enableB2b($('#<?php echo $aField['id']; ?>').val() !== '', '.js-b2b-tier');
            }
        });

        $('#<?php echo $aField['id']; ?>').change(function() {
            enableB2b($(this).val() !== '', '.js-b2b-tier');
        }).change();
    })(jqml);
</script>
