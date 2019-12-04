<?php
class_exists('ML', false) or die();
if (MLHttp::gi()->isAjax()) {
    MLSetting::gi()->add('aAjaxPlugin', array('content' => $aField['hood_categories']['oCategory']->renderAjax()));
} else {
    ?>
    <div class="hoodCatVisual" id="<?php echo $aField['id'] ?>_visual">
        <?php $this->includeType($this->getSubField($aField)) ?>
    </div>
    <?php
    if (isset($aField['hood_categories']['oCategory'])) {
        echo $aField['hood_categories']['oCategory']->renderView();
    }
}
