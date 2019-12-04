<?php
class ML_Prestashop_Helper_Model_Carrier {
    public function createCarrier($sName, Shop $shop) {
        $oCarrier = new Carrier();
        $oCarrier->name = $sName;
        $oCarrier->id_tax_rules_group = 0;
        $oCarrier->active = false;
        $oCarrier->deleted = false;
        $oCarrier->shipping_handling = false;
        $oCarrier->range_behavior = 0;
        $oCarrier->is_module = true;
        $oCarrier->shipping_external = true;
        $oCarrier->external_module_name = '';
        $oCarrier->need_range = true;
        $oCarrier->max_width = 150;
        $oCarrier->max_height = 150;
        $oCarrier->max_depth = 150;
        $oCarrier->max_weight = 50; // Will be overriden by max($defaultWeightRange)
        $oCarrier->grade = 5;
        $oCarrier->position = Carrier::getHigherPosition() + 1;
        $oCarrier->url = '';
        $oCarrier->is_module = false;
        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            $oCarrier->delay[$language['id_lang']] = '-';
        }
        $iCarrier = Configuration::get('PS_CARRIER_DEFAULT');
        if ($oCarrier->add()) {
            $this->changeZones($oCarrier->id);
            $this->addCarrierGroups($oCarrier);
            $this->updateAssoCarrier($oCarrier, $shop);
            $iCarrier = $oCarrier->id;
        }

        return $iCarrier;
    }

    public function changeZones($id)
    {
        /** @var Carrier $carrier */
        $carrier = new Carrier($id);
        if (!Validate::isLoadedObject($carrier)) {
            throw new Exception(Tools::displayError('The carrier object cannot be loaded.'));
        }
        $zones = Zone::getZones(false);
        foreach ($zones as $zone) {
            $carrier->addZone($zone['id_zone']);
        }
    }
    
    /**
     * Add relation between `Group` and carrier.
     *
     * @param Carrier $carrier
     */
    private function addCarrierGroups(Carrier $carrier) {
        $db = Db::getInstance();
        $groups = Group::getGroups(true);

        $tbl_carrier_group = _DB_PREFIX_ . 'carrier_group';
        $insert_sql = 'INSERT INTO `%1$s` VALUES (%2$d, %3$d)';
        $exists_sql = 'SELECT count(1) ' .
                'FROM `%1$s` WHERE id_group = %2$d and id_carrier = %3$d';
        foreach ($groups as $group) {
            $exists_query = sprintf(
                    $exists_sql, pSQL($tbl_carrier_group), (int) $group['id_group'], (int) $carrier->id
            );
            if ((int) $db->getValue($exists_query) > 0) {
                // Skip if group already exists for carrier.
                continue;
            }
            $db->execute(sprintf(
                            $insert_sql, pSQL($tbl_carrier_group), (int) $carrier->id, (int) $group['id_group']
            ));
        }
    }

    /**
     * Update carrier relation to the shop, if applicable.
     *
     * @param Carrier $carrier
     * @param Shop $shop
     * @return bool `false` in case of failure to insert the new data.
     */
    private function updateAssoCarrier(Carrier $carrier, Shop $shop) {
        $shop_exists = (int) Db::getInstance(false)->getValue(sprintf(
                                'SELECT COUNT(1) FROM `' . _DB_PREFIX_ . 'carrier_shop`' .
                                'WHERE id_carrier=%d and id_shop=%d', (int) $carrier->id, (int) $shop->id
        ));
        $relation_sql = sprintf(
                'INSERT INTO `' . _DB_PREFIX_ . 'carrier_shop` (id_carrier, id_shop) VALUES (%d, %d)', (int) $carrier->id, (int) $shop->id
        );

        if (!$shop_exists) {
            Db::getInstance()->execute($relation_sql);
        }
    }

}
