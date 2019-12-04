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
 * (c) 2010 - 2018 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
MLFilesystem::gi()->loadClass('Ebay_Controller_Ebay_Prepare_Apply_Form');

class ML_Ebay_Controller_Ebay_Prepare_Match_Form extends ML_Ebay_Controller_Ebay_Prepare_Apply_Form {

    public function __construct() {
        MLSetting::gi()->ebay_prepare_match_form = MLSetting::gi()->ebay_prepare_apply_form;
        MLI18n::gi()->ebay_prepare_match_form = MLI18n::gi()->ebay_prepare_apply_form;
        parent::__construct();
    }

    protected function getSelectionNameValue() {
        return 'match_apply';
    }

}
