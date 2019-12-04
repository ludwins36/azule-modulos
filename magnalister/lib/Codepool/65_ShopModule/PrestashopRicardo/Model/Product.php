<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
 
MLFilesystem::gi()->loadClass('Prestashop_Model_Product');
class ML_PrestashopRicardo_Model_Product extends ML_Prestashop_Model_Product {

    protected function getPrice($blGros, $blFormated, $sPriceKind = '', $fPriceFactor = 0.0, $iPriceSignal = null, $fTax = null) {
        $context = Context::getContext();
        $fPercent = (float)$this->getLoaddedProduct()->getTaxesRate();
        $iGroupId = $this->blSpecialPrice && is_object($context->customer)  ? $context->customer->id_default_group : 0;
        $oCurrency = Currency::getCurrencyInstance($context->currency->id);
        if($oCurrency->conversion_rate == 0){
            $oDBCurrency = new Currency($context->currency->id);
            $oCurrency->conversion_rate = $oDBCurrency->conversion_rate;
        }
        $fPrice = Product::priceCalculation(
                $context->shop->id , (int) $this->oProduct->id, $this->getAttributeId(), 
                Configuration::get('PS_COUNTRY_DEFAULT',null ,null ,$context->shop->id), 
                0/*$id_state*/ ,  0/*$zipcode*/ , $context->currency->id /*$id_currency*/ ,
                $iGroupId/*$id_group*/  ,  1/*$quantity*/  , ($fTax === null) /*use_tax*/  , 2 /*$decimals*/  , 
                false/*$only_reduc*/ , $this->blSpecialPrice/*$use_reduc*/ , true/*$with_ecotax*/ , 
                $specific_price  ,  true  ,null , null ,null,1
            )   ;  
        if($fPrice === null){
            $fPrice = 0;
        }
        $oPrice = MLPrice::factory();
        if($fTax !== null) {
            $fPrice = $oPrice->calcPercentages(null, $fPrice, $fTax);
        }
        if ($sPriceKind == 'percent') {
            $fPrice = $oPrice->calcPercentages(null, $fPrice, $fPriceFactor);
        } elseif ($sPriceKind == 'addition') {
            $fPrice = $fPrice + $fPriceFactor;
        }
        if ($iPriceSignal !== null) {
            //If price signal is single digit then just add price signal as last digit
            if (strlen((string)$iPriceSignal) == 1) {
                $fPrice = (0.1 * (int)($fPrice * 10)) + ($iPriceSignal / 100);
            } else {
                $fPrice = ((int)$fPrice) + ($iPriceSignal / 100);
            }
        }
        // 3. calc netprice from modulprice
        $fNetPrice = $oPrice->calcPercentages($fPrice, null, $fPercent);
        // 4. define out price (unformated price of current shop)
        $fUsePrice = $blGros ? $fPrice : $fNetPrice;
        $fUsePrice = round($fUsePrice, 2);
        //Check if last digit (second decimal) is 0 or 5. If not set 5 as default last digit
        $fUsePrice = 
            (((int)(string)($fUsePrice * 100)) % 5) == 0 // cast to string because it seems php have float precision in background
            ? $fUsePrice 
            : (((int)(string)($fUsePrice * 10)) / 10) + 0.05
        ;
        // round again, to be sure that precision is 2
        $fUsePrice = round($fUsePrice, 2);
        if ($blFormated) {
            return "<span class='price'>".Tools::displayPrice($fUsePrice, Context::getContext()->currency->id)."</span>";
        }
        return $fUsePrice;
    }
    
}