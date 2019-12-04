<?php
class_exists('ML', false) or die();
if (!MLHttp::gi()->isAjax()) {
    MLSetting::gi()->add('aCss', 'magnalister.hoodprepareform.css', true);
}

$this->getFormWidget();
