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
 * all groups using form-fields and includes i18n for legend directly
 */
MLSetting::gi()->add('formgroups_idealo', array(
    'account' => array(
        'legend' => array('i18n' => '{#i18n:formgroups_idealo__account#}',),
        'fields' => array(
            'access.inventorypath' => '{#setting:formfields_idealo__access.inventorypath#}',
            'idealotoken' => '{#setting:formfields_idealo__idealotoken#}',
        ),
    ),
    'checkoutenabled' => array(
        'legend' => array(
            'i18n' => '', 
            'classes' => array('mlhidden'),
        ),
        'fields' => array(
            'checkoutenabled' => '{#setting:formfields_idealo__checkoutenabled#}',
        ),
    ),
    'prepare' => array(
        'legend' => array('i18n' => '{#i18n:formgroups_idealo__prepare#}'),
        'fields' => array(
            'prepare.status' => '{#setting:formfields__prepare.status#}',
            'checkout' => '{#setting:formfields_idealo__checkout#}',
            'paymentmethod' => '{#setting:formfields_idealo__paymentmethod#}',
        ),
    ),
    'shipping' => array(
        'legend' => array('i18n' => '{#i18n:formgroups_idealo__shipping#}'),
        'fields' => array(
            'subheader.pd' => '{#setting:formfields_idealo__subheader.pd#}',
            'shippingcountry' => '{#setting:formfields_idealo__shippingcountry#}',
            'shippingmethodandcost' => '{#setting:formfields_idealo__shippingmethodandcost#}',
            'shippingtime' => '{#setting:formfields_idealo__shippingtime#}',
            'shippingtimeproductfield' => '{#setting:formfields_idealo__shippingtimeproductfield#}',
            'subheader.d' => '{#setting:formfields_idealo__subheader.d#}',
            'shippingmethod' => '{#setting:formfields_idealo__shippingmethod#}',
        ),
    ),
    'upload' => array(
        'legend' => array('i18n' => '{#i18n:formgroups_idealo__upload#}'),
        'fields' => array(
            'subheader.pd' => '{#setting:formfields_idealo__subheader.pd#}',
            'checkin.status' => '{#setting:formfields__checkin.status#}',
            'lang' => '{#setting:formfields__lang#}',
            'quantity' => '{#setting:formfields__quantity#}',
            'subheader.d' => '{#setting:formfields_idealo__subheader.d#}',
            'maxquantity' => '{#setting:formfields__maxquantity#}',
        ),
    ),
    'orderstatus' => array(
        'legend' => array('i18n' => '{#i18n:formgroups_idealo__orderstatus#}'),
        'fields' => array(
            'orderstatus.sync' => '{#setting:formfields__orderstatus.sync#}',
            'orderstatus.shipped' => '{#setting:formfields__orderstatus.shipped#}',
            'orderstatus.carrier.default' => '{#setting:formfields__orderstatus.carrier.default#}',
            'orderstatus.canceled' => '{#setting:formfields__orderstatus.canceled#}',
            'orderstatus.cancelreason' => '{#setting:formfields_idealo__orderstatus.cancelreason#}',
            'orderstatus.cancelcomment' => '{#setting:formfields_idealo__orderstatus.cancelcomment#}',
            'orderstatus.carrier.default' => '{#setting:formfields__orderstatus.carrier.default#}',
        ),
    ),
    'orderimport' => array(
        'legend' => array('i18n' => '{#i18n:formgroups__orderimport#}'),
        'fields' => array(
            'importactive' => '{#setting:formfields__importactive#}',
            'customergroup' => '{#setting:formfields__customergroup#}',
            'orderimport.shop' => '{#setting:formfields__orderimport.shop#}',
            'orderstatus.open' => '{#setting:formfields__orderstatus.open#}',
            'orderimport.shippingmethod' => '{#setting:formfields__orderimport.shippingmethod#}',
            'orderimport.paymentmethod' => '{#setting:formfields_idealo__orderimport.paymentmethod#}',
        ),
    ),
    // prepare
    'prepare_details' => array(
        'legend' => array('i18n' => '{#i18n:formgroups_idealo__prepare_details#}'),
        'fields' => array(
            'title' => '{#setting:formfields_idealo__prepare_title#}',
            'Description' => '{#setting:formfields_idealo__prepare_description#}',
            'Image' => '{#setting:formfields_idealo__prepare_image#}',
        ),
    ),
    'prepare_general' => array(
        'legend' => array('i18n' => '{#i18n:formgroups_idealo__prepare_general#}'),
        'fields' => array(
            'subheader.pd' => '{#setting:formfields_idealo__subheader.pd#}',
            'paymentmethod' => '{#setting:formfields_idealo__paymentmethod#}',
            'shippingcountry' => '{#setting:formfields_idealo__shippingcountry#}',
            'shippingmethodandcost' => '{#setting:formfields_idealo__shippingmethodandcost#}',
            'shippingtime' => '{#setting:formfields_idealo__shippingtime#}',
            'subheader.d' => '{#setting:formfields_idealo__subheader.d#}',
            'checkout' => '{#setting:formfields_idealo__checkout#}',
            'shippingmethods' => '{#setting:formfields_idealo__shippingmethod#}',
        ),
    ),
));