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
MLSetting::gi()->add('formfields', array(
    'checkin.status' => array(
        'name' => 'checkin.status',
        'type' => 'bool',
        'i18n' => '{#i18n:formfields__checkin.status#}'
    ),
    'lang' => array(
        'name' => 'lang',
        'type' => 'select',
        'i18n' => '{#i18n:formfields__lang#}'
    ),
    'prepare.status' => array(
        'name' => 'prepare.status',
        'type' => 'bool',
        'i18n' => '{#i18n:formfields__prepare.status#}',
    ),
    'tabident' => array(
        'name' => 'tabident',
        'type' => 'string',
        'i18n' => '{#i18n:formfields__tabident#}',
    ),
    'stocksync.tomarketplace' => array(
        'i18n' => '{#i18n:formfields__stocksync.tomarketplace#}',
        'name' => 'stocksync.tomarketplace',
        'type' => 'select',
    ),
    'stocksync.frommarketplace' => array(
        'i18n' => '{#i18n:formfields__stocksync.frommarketplace#}',
        'name' => 'stocksync.frommarketplace',
        'type' => 'select',
    ),
    'inventorysync.price' => array(
        'i18n' => '{#i18n:formfields__inventorysync.price#}',
        'name' => 'inventorysync.price',
        'type' => 'select',
    ),
    'mail.send' => array(
        'i18n' => '{#i18n:formfields__mail.send#}',
        'name' => 'mail.send',
        'type' => 'radio',
        'default' => false,
    ),
    'mail.originator.name' => array(
        'i18n' => '{#i18n:formfields__mail.originator.name#}',
        'name' => 'mail.originator.name',
        'type' => 'string',
        'default' => '{#i18n:formfields__mail.originator.name__default#}',
    ),
    'mail.originator.adress' => array(
        'i18n' => '{#i18n:formfields__mail.originator.adress#}',
        'name' => 'mail.originator.adress',
        'type' => 'string',
        'default' => '{#i18n:formfields__mail.originator.adress__default#}',
    ),
    'mail.subject' => array(
        'i18n' => '{#i18n:formfields__mail.subject#}',
        'name' => 'mail.subject',
        'type' => 'string',
        'default' => '{#i18n:formfields__mail.subject__default#}',
    ),
    'mail.content' => array(
        'i18n' => '{#i18n:formfields__mail.content#}',
        'name' => 'mail.content',
        'type' => 'configMailContentContainer',
        'default' => '{#i18n:formfields__mail.content__default#}',
        'resetdefault' => '{#i18n:formfields__mail.content__default#}',
    ),
    'mail.copy' => array(
        'i18n' => '{#i18n:formfields__mail.copy#}',
        'name' => 'mail.copy',
        'type' => 'radio',
        'default' => true,
    ),
    'quantity' => array(
        'i18n' => '{#i18n:formfields__quantity#}',
        'name' => 'quantity',
        'type' => 'selectwithtextoption',
        'subfields' => array(
            'select' => '{#setting:formfields__quantity.type#}',
            'string' => '{#setting:formfields__quantity.value#}'
        ),
    ),
    'quantity.type' => array(
        'i18n' => array('label' => '', ),
        'name' => 'quantity.type',
        'type' => 'select'
     ),
    'quantity.value' => array(
        'i18n' => array('label' => '', ),
        'name' => 'quantity.value',
        'type' => 'string'
    ),
    'maxquantity' => array(
        'i18n' => '{#i18n:formfields__maxquantity#}',
        'name' => 'maxquantity',
        'type' => 'string',
    ),
    'priceoptions' => array(
        'i18n' => '{#i18n:formfields__priceoptions#}',
        'name' => 'priceoptions',
        'type' => 'subFieldsContainer',
        'subfields' => array(
            'group' => '{#setting:formfields__price.group#}',
            'usespecialoffer' => '{#setting:formfields__price.usespecialoffer#}',
        ),
    ),
    'price.group' => array(
        'i18n' => array('label' => '', ),
        'name' => 'price.group',
        'type' => 'select',
    ),
    'price.usespecialoffer' => array(
        'i18n' => '{#i18n:formfields__price.usespecialoffer#}',
        'name' => 'price.usespecialoffer',
        'type' => 'bool',
    ),
    'exchangerate_update' => array(
        'i18n' => '{#i18n:formfields__exchangerate_update#}',
        'name' => 'exchangerate_update',
        'type' => 'bool',
    ),
    
    
    
    'importactive' => array(
        'i18n' => '{#i18n:formfields__importactive#}',
        'name' => 'importactive',
        'type' => 'subFieldsContainer',
        'subfields' => array(
            'import' => '{#setting:formfields__import#}',
            'preimport.start' => '{#setting:formfields__preimport.start#}',
        ),
    ),
    'import' => array(
        'i18n' => array('label' => '', ),
        'name' => 'import', 
        'type' => 'radio', 
    ),
    'preimport.start' => array(
        'i18n' => '{#i18n:formfields__preimport.start#}',
        'name' => 'preimport.start', 
        'type' => 'datepicker',
    ),
    'customergroup' => array(
        'i18n' => '{#i18n:formfields__customergroup#}',
        'name' => 'customergroup',
        'type' => 'select',
    ),
    'orderimport.shop' => array(
        'i18n' => '{#i18n:formfields__orderimport.shop#}',
        'name' => 'orderimport.shop',
        'type' => 'select',
    ),
    'orderstatus.open' => array(
        'i18n' => '{#i18n:formfields__orderstatus.open#}',
        'name' => 'orderstatus.open',
        'type' => 'select',
    ),
    'orderimport.shippingmethod' => array(
        'i18n' => '{#i18n:formfields__orderimport.shippingmethod#}',
        'name' => 'orderimport.shippingmethod',
        'type' => 'string',
        'default' => '{#setting:currentMarketplaceName#}',
        'expert' => true,
    ),
    'orderimport.paymentmethod' => array(
        'i18n' => '{#i18n:formfields__orderimport.paymentmethod#}',
        'name' => 'orderimport.paymentmethod',
        'type' => 'string',
        'default' => '{#setting:currentMarketplaceName#}',
        'expert' => true,
    ),
    'mwst.fallback' => array(
        'i18n' => '{#i18n:formfields__mwst.fallback#}',
        'name' => 'mwst.fallback',
        'type' => 'string',
    ),
    'orderstatus.sync' => array(
        'i18n' => '{#i18n:formfields__orderstatus.sync#}',
        'name' => 'orderstatus.sync',
        'type' => 'select',
    ),
    'orderstatus.shipped' => array(
        'i18n' => '{#i18n:formfields__orderstatus.shipped#}',
        'name' => 'orderstatus.shipped',
        'type' => 'select',
    ),
    'orderstatus.carrier.default' => array(
        'i18n' => '{#i18n:formfields__orderstatus.carrier.default#}',
        'name' => 'orderstatus.carrier.default',
        'type' => 'string',
    ),
    'orderstatus.canceled' => array(
        'i18n' => '{#i18n:formfields__orderstatus.canceled#}',
        'name' => 'orderstatus.canceled',
        'type' => 'select',
    ),
));