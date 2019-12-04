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
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

/**
 * all tabs using form-groups
 */
MLSetting::gi()->add('idealo_config_account', array(
    'checkoutenabled' => '{#setting:formgroups_idealo__checkoutenabled#}',
    'tabident' => '{#setting:formgroups__tabident#}',
    'account' => '{#setting:formgroups_idealo__account#}',
), false);

MLSetting::gi()->add('idealo_config_prepare', array(
    'checkoutenabled' => '{#setting:formgroups_idealo__checkoutenabled#}',
    'prepare' => '{#setting:formgroups_idealo__prepare#}',
    'shipping' => '{#setting:formgroups_idealo__shipping#}',
    'upload' => '{#setting:formgroups_idealo__upload#}',
), false);

MLSetting::gi()->add('idealo_config_price', array(
    'checkoutenabled' => '{#setting:formgroups_idealo__checkoutenabled#}',
    'price' => '{#setting:formgroups__comparisonprice#}'
), false);

MLSetting::gi()->add('idealo_config_orderimport', array(
    'checkoutenabled' => '{#setting:formgroups_idealo__checkoutenabled#}',
    'orderimport' => '{#setting:formgroups_idealo__orderimport#}',
    'mwst' => '{#setting:formgroups__mwst#}',
    'orderstatus' => '{#setting:formgroups_idealo__orderstatus#}',
), false);

MLSetting::gi()->add('idealo_config_sync', array(
    'checkoutenabled' => '{#setting:formgroups_idealo__checkoutenabled#}',
    'sync' => '{#setting:formgroups__sync#}',
), false);

MLSetting::gi()->add('idealo_config_emailtemplate', array(
    'checkoutenabled' => '{#setting:formgroups_idealo__checkoutenabled#}',
    'mail' => '{#setting:formgroups__mail#}',
), false);
