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
MLSetting::gi()->add('formgroups', array(
    'tabident' => array(
        'legend' => array(
            'i18n' => '',
            'classes' => array('mlhidden'),
        ),
        'fields' => array(
            'tabident' => '{#setting:formfields__tabident#}'
        ),
    ),
    
    'sync' => array(
        'legend' => array('i18n' => '{#i18n:formgroups__sync#}'),
        'fields' => array(
            'stocksync.tomarketplace' => '{#setting:formfields__stocksync.tomarketplace#}',
            'stocksync.frommarketplace' => '{#setting:formfields__stocksync.frommarketplace#}',
            'inventorysync.price' => '{#setting:formfields__inventorysync.price#}',
        ),
    ),
    'mail' => array(
        'legend' => array('i18n' => '{#i18n:formgroups__mail#}'),
        'fields' => array(
            'mail.send' => '{#setting:formfields__mail.send#}',
            'mail.originator.name' => '{#setting:formfields__mail.originator.name#}',
            'mail.originator.adress' => '{#setting:formfields__mail.originator.adress#}',
            'mail.subject' => '{#setting:formfields__mail.subject#}',
            'mail.content' => '{#setting:formfields__mail.content#}',
            'mail.copy' => '{#setting:formfields__mail.copy#}',
        )
    ),
    'orderimport' => array(
        'legend' => array('i18n' => '{#i18n:formgroups__orderimport#}'),
        'fields' => array(
            'importactive' => '{#setting:formfields__importactive#}',
            'customergroup' => '{#setting:formfields__customergroup#}',
            'orderimport.shop' => '{#setting:formfields__orderimport.shop#}',
            'orderstatus.open' => '{#setting:formfields__orderstatus.open#}',
            'orderimport.shippingmethod' => '{#setting:formfields__orderimport.shippingmethod#}',
            'orderimport.paymentmethod' => '{#setting:formfields__orderimport.paymentmethod#}',
        ),
    ),
    'mwst' => array(
        'legend' => array('i18n' => '{#i18n:formgroups__mwst#}'),
        'fields' => array(
            'mwst.fallback' => '{#setting:formfields__mwst.fallback#}',
        )
    ),
    'comparisonprice' => array(
        'legend' => array('i18n' => '{#i18n:formgroups__comparisonprice#}'),
        'fields' => array(
            'priceoptions' => '{#setting:formfields__priceoptions#}',
            'exchangerate_update' => '{#setting:formfields__exchangerate_update#}',
        ),
    )
));