<?php
$sFileName = dirname(dirname(__FILE__)) . '/I18n/' . ucfirst(MLLanguage::gi()->getCurrentIsoCode()) . '/idealo.php';
if (file_exists($sFileName)) {
    require_once $sFileName;
} else {
    require_once dirname(dirname(__FILE__)) . '/I18n/De/idealo.php';
}