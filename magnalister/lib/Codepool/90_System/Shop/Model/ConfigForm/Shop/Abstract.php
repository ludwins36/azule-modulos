<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author mba
 */
abstract class ML_Shop_Model_ConfigForm_Shop_Abstract {
    
    /**
     * you can override this function and edit form mask in each shop specific class
     * @param type $aForm
     */
    public function manipulateForm(&$aForm) {
    }

    /**
     * Returns grouped attributes for attribute matching
     */
    public abstract function getGroupedAttributesForMatching();

    /**
     * return true if variation could be a variation
     * @param $sAttributeKey key of selected attribute
     * @return bool
     */
    public function shouldBeDisplayedAsVariationAttribute($sAttributeKey) {
        return true;
    }
}