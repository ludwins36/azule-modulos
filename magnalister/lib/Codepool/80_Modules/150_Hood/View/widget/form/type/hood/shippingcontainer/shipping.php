<?php class_exists('ML', false) or die(); ?>
<div class="hood-shipping">
    <div>
        <div class="type">
            <?php
            $aType = $aField;
            $aType['type'] = 'select';
            $aType['name'] .= '[ShippingService]';
            $aType['value'] = isset($aType['value']['ShippingService']) ? $aType['value']['ShippingService'] : '';
            $this->includeType($aType);
            ?>
        </div>
        <div class="text"><?php echo $aField['i18n']['cost'] ?>:</div>
        <div class="cost">
            <?php
            $aCost = $aField;
            $aCost['type'] = 'string';
            $aCost['name'] .= '[ShippingServiceCost]';
            $aCost['value'] = isset($aCost['value']['ShippingServiceCost']) ? $aCost['value']['ShippingServiceCost'] : '';
            unset($aCost['values']);
            $this->includeType($aCost);
            ?>
        </div>
    </div>
    <br/>
    <?php
    if (isset($aField['locations'])) {
        $aLocations = $aField;
        $aSelectedLocations = array();
        if (isset($aLocations['value']['ShipToLocation'])) {
            if (is_array($aLocations['value']['ShipToLocation'])) {
                $aSelectedLocations = $aLocations['value']['ShipToLocation'][0];
            } else {//deprecated
                $aSelectedLocations = $aLocations['value']['ShipToLocation'];
            }
        }

        $sShippingIndex = str_replace(array(']', '['), '_', strrchr($aLocations['name'], '['));
        ?>
        <div>
        <?php

        $aLocation['type'] = 'select';
        $aLocation['id'] = $aLocations['id'];
        $aLocation['name'] = $aLocations['name'].'[ShipToLocation][]';
        $aLocation['values'] = $aLocations['locations'];
        $aLocation['value'] = $aSelectedLocations;
        $this->includeType($aLocation);
        ?>
        </div>
        <?php
    }
    ?>
</div>
