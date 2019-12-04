<?php class_exists('ML', false) or die() ?>
<span style="display:table-cell;">
    <?php echo (isset($aField['value']) ? htmlspecialchars($aField['value'], ENT_COMPAT) : '') ?>
    <?php echo $aField['currency'] ?>
</span>