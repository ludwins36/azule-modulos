<?php

class ML_Prestashop_Model_Price extends ML_Shop_Model_Price_Abstract implements ML_Shop_Model_Price_Interface {

    public function format($fPrice, $sCode, $blConvert = true) {
        if (!$blConvert) {
            return Tools::displayPrice($fPrice, Currency::getIdByIsoCode($sCode));
        }

        return Tools::displayPrice(Tools::convertPrice($fPrice, Currency::getIdByIsoCode($sCode)), Currency::getIdByIsoCode($sCode));
    }

}