<?php class_exists('ML', false) or die() ?>
<?php $sValue = isset($aField['value']) ? $aField['value'] : ''?>
<input type="hidden" id="<?php echo $aField['id']; ?>" name="<?php echo MLHttp::gi()->parseFormFieldName($aField['name']) ?>" value="<?php echo htmlentities($sValue, ENT_COMPAT | ENT_HTML401, 'UTF-8') ?>" />