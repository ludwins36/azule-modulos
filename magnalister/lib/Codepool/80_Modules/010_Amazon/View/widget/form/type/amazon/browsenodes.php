<?php
class_exists('ML', false) or die();
$aMyField = $aField;
$aMyField['type'] = 'select';
$aMyField['name'] = $aField['name'].'[]';
$aMyField['value'] = isset($aField['value'][0]) ? $aField['value'][0] : '';
$this->includeType($aMyField);
//    $aMyField['value'] = isset($aField['value'][1]) ? $aField['value'][1] : '';
//    $this->includeType($aMyField);
?>