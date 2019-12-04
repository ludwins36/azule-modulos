<?php
class_exists('ML', false) or die();
$aValues = isset($aValues) ? $aValues : (isset($aField['values']) ? $aField['values'] : array());
$sValue = isset($sValue) ? $sValue : (isset($aField['value']) ? $aField['value'] : '');


// performance: cache rendered-options in settings (only current request) for dont renderer them multiple times
$sRenderedOptions = 'rendered_form_select_options_'.md5(json_encode(array('value' => $sValue, 'values' => $aValues, )));
try {
    echo MLSetting::gi()->get($sRenderedOptions);
} catch (MLSetting_Exception $oEx) {
    ob_start();
    foreach ($aValues as $sOptionKey => $sOptionValue) {
        if (is_array($sOptionValue)) {
            $sOptGroupClass = '';
            if (!empty($sOptionValue['optGroupClass'])) {
                $sOptGroupClass = $sOptionValue['optGroupClass'];
                unset($sOptionValue['optGroupClass']);
            }
            ?>
            <optgroup label="<?php echo $sOptionKey; ?>" class="<?php echo $sOptGroupClass; ?>">
                <?php $this->includeType($aField, array('aValues' => $sOptionValue, 'sValue' => $sValue)); ?>
            </optgroup>
        <?php } else {
            if (array_key_exists('multiple', $aField) && $aField['multiple']) {
                $blSelected = in_array($sOptionKey, (array)$sValue);
            } else {
                $blSelected = is_array($sValue) === false && (string) $sValue === (string) $sOptionKey;
            }
            $this->includeType(array_merge($aField, array('type' => 'select_option')), array('aOption' => array(
                'selected' => $blSelected,
                'key' => $sOptionKey,
                'value' => $sOptionValue
            )));
        }
    }
    MLSetting::gi()->set($sRenderedOptions, ob_get_contents());
    ob_end_clean();
    echo MLSetting::gi()->get($sRenderedOptions);
}