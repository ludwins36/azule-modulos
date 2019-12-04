<?php
class_exists('ML', false) or die();
$aValues = isset($aValues) ? $aValues : (isset($aField['values']) ? $aField['values'] : array());
$sValue = isset($sValue) ? $sValue : (isset($aField['value']) ? $aField['value'] : '');

foreach ($aValues as $sOptionKey => $sOptionValue) {
    if (is_array($sOptionValue) && !empty($sOptionValue['optGroupClass'])) {
        $sOptGroupClass = $sOptionValue['optGroupClass'];
        unset($sOptionValue['optGroupClass']);
        ?>
        <optgroup label="<?php echo $sOptionKey; ?>" class="<?php echo $sOptGroupClass; ?>">
            <?php $this->includeType($aField, array('aValues' => $sOptionValue, 'sValue' => $sValue)); ?>
        </optgroup>
    <?php } else {
        // array_merge is intentional because we want "clone" of original field
        // since we are in a loop and upper includeType call expects original field!
        $optoinField = array_merge($aField, array('type' => 'select_option'));
        $this->includeType($optoinField, array(
            'aOption' => array(
                'selected' => is_array($sValue) ? array_key_exists($sOptionKey, $sValue) : (string) $sValue === (string) $sOptionKey,
                'key' => $sOptionKey,
                'value' => is_array($sOptionValue) && array_key_exists('name', $sOptionValue) ? $sOptionValue['name'] : $sOptionValue,
                'dataType' => is_array($sOptionValue) && array_key_exists('type', $sOptionValue) ? $sOptionValue['type'] : '',
            ),
        ));
    }
}
