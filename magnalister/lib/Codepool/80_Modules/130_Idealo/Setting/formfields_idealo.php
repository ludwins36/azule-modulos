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

// example for overwriting global element (add css-class to form-field)
foreach (array(
    'stocksync.frommarketplace', 'mail.send', 'mail.originator.name',
    'mail.originator.adress', 'mail.subject', 'mail.content', 'mail.copy',
    'importactive', 'import', 'preimport.start', 'customergroup',
    'orderimport.shop', 'orderstatus.open', 'orderimport.shippingmethod',
    'orderimport.paymentmethod', 'maxquantity', 'mwst.fallback',
    'orderstatus.sync', 'orderstatus.shipped', 'orderstatus.carrier.default',
    'orderstatus.canceled',
) as $sIdealoDirectBuyFieldName) {
    MLSetting::gi()->add('formfields__'.$sIdealoDirectBuyFieldName.'__cssclasses', array('mljs-directbuy', ));
}


/**
 * all fields include i18n directly
 * @see ../Codepool/90_System/Form/Setting/formfields.php
 */
MLSetting::gi()->add('formfields_idealo', array(
    'idealotoken' => array(
        'i18n' => '{#i18n:formfields_idealo__idealotoken#}',
        'name' => 'idealotoken',
        'type' => 'subFieldsContainer',
        'subfields' => array(
            'checkout.token' => '{#setting:formfields_idealo__checkout.token#}',
//            'checkout' => '{#setting:formfields_idealo__checkout__default#}',
         ),
    ),
    'checkout.token' => array (
        'i18n' => array ('label' => '', ),
        'name' => 'checkout.token',
        'type' => 'string',
    ),
    'checkout' => array(
        'i18n' => '{#i18n:formfields_idealo__checkout#}',
        'name' => 'checkout',
        'type' => 'bool',
        'cssclasses' => array('mljs-directbuy', ),
    ),
    'checkoutenabled' => array(
            'i18n' => '{#i18n:formfields_idealo__checkoutenabled#}',
            'name' => 'checkoutenabled',
            'type' => 'hidden',
    ),
    'shippingcountry' => array(
        'i18n' => '{#i18n:formfields_idealo__shippingcountry#}',
        'name' => 'shippingcountry',
        'type' => 'select',
    ),
    'shippingmethodandcost' => array(
        'i18n' => '{#i18n:formfields_idealo__shippingmethodandcost#}',
        'name' => 'shippingmethodandcost',
        'type' => 'selectwithtextoption',
        'cssClasses' => array('autoWidth'),
        'subfields' => array(
            'select' => '{#setting:formfields_idealo__shippingcostmethod#}',
            'string' => '{#setting:formfields_idealo__shippingcost#}',
        ),
    ),
    'shippingcostmethod' => array(
        'i18n' => array('label' => ''),
        'name' => 'shippingcostmethod', 
        'type' => 'select',
        'values' => array(
            '__ml_lump' => array(
                'textoption' => true, 
                'title' => '{#i18n:formfields_idealo__shippingcostmethod__values____ml_lump#}'
            ),
            '__ml_weight' => array(
                'textoption' => false, 
                'title' => '{#i18n:formfields_idealo__shippingcostmethod__values____ml_weight#}'
            ),
        ),
    ),
    'shippingcost' => array(
        'i18n' => array('label' => ''),
        'name' => 'shippingcost', 
        'type' => 'string', 
        'default' => '0.00', 
    ),
    'subheader.d' => array(
        'i18n' => '{#i18n:formfields_idealo__subheader.d#}',
        'type' => 'subHeader',
        'name' => 'subheader.d',
        'fullwidth' => true,
        'showdesc' => false,
    ),
    'subheader.pd' => array(
        'i18n' => '{#i18n:formfields_idealo__subheader.pd#}',
        'name' => 'subheader.pd',
        'type' => 'subHeader',
        'fullwidth' => true,
        'showdesc' => false,
    ),
    'paymentmethod' => array(
        'i18n' => '{#i18n:formfields_idealo__paymentmethod#}',
        'name' => 'paymentmethod',
        'type' => 'multipleselect',
        'values' => '{#i18n:formfields_idealo__paymentmethod__values#}',
    ),
    'access.inventorypath' => array(
        'i18n' => '{#i18n:formfields_idealo__access.inventorypath#}',
        'name' => 'access.inventorypath',
        'type' => 'information',
    ),
    'shippingmethod' => array(
        'i18n' => '{#i18n:formfields_idealo__shippingmethod#}',
        'name' => 'shippingmethod',
        'type' => 'select',
        'values' => '{#i18n:formfields_idealo__shippingmethod__values#}',
        'cssclasses' => array('mljs-directbuy', ),
    ),
    'shippingtime' => array(
        'i18n' => '{#i18n:formfields_idealo__shippingtime#}',
        'name' => 'shippingtime',
        'type' => 'shippingtime',
//        'type' => 'selectwithtextoption',
        'subfields' => array(
            'select' => '{#setting:formfields_idealo__shippingtimetype#}',
            'string' => '{#setting:formfields_idealo__shippingtimevalue#}',
        ),
    ),
    'shippingtimetype' => array(
        'i18n' => array(
            'label' => '',
            'values' => '{#i18n:formfields_idealo__shippingtimetype__values#}'
        ),
        'name' => 'shippingtimetype',
        'type' => 'select',
    ),
    'shippingtimevalue' => array(
        'i18n' => array('label' => '', ),
        'name' => 'shippingtimevalue',
        'type' => 'string',
    ),
    'shippingtimeproductfield' => array(
        'i18n' => '{#i18n:formfields_idealo__shippingtimeproductfield#}',
        'name' => 'shippingtimeproductfield',
        'type' => 'select',
        'expert' => true,
    ),
    'orderstatus.cancelreason' => array (
        'i18n' => '{#i18n:formfields_idealo__orderstatus.cancelreason#}',
        'name' => 'orderstatus.cancelreason',
        'type' => 'select',
        'cssclasses' => array('mljs-directbuy', ),
    ),
    'orderstatus.cancelcomment' => array (
        'i18n' => '{#i18n:formfields_idealo__orderstatus.cancelcomment#}',
        'name' => 'orderstatus.cancelcomment',
        'type' => 'string',
        'cssclasses' => array('mljs-directbuy', ),
    ),
    'prepare_title' => array(
        'i18n' => '{#i18n:formfields_idealo__prepare_title#}',
        'name' => 'Title',
        'type' => 'string',
        'singleproduct' => true,
    ),
    'prepare_description' => array(
        'i18n' => '{#i18n:formfields_idealo__prepare_description#}',
        'name' => 'Description',
        'type' => 'wysiwyg',
        'singleproduct' => true,
    ),
    'prepare_image' => array(
        'i18n' => '{#i18n:formfields_idealo__prepare_image#}',
        'name' => 'Image',
        'type' => 'imagemultipleselect',
        'singleproduct' => true,
    ),
    'orderimport.paymentmethod' => array(
        'i18n' => '{#i18n:formfields__orderimport.paymentmethod#}',
        'name' => 'orderimport.paymentmethod',
        'type' => 'selectwithtextoption',
        'subfields' => array(
            'select' => array('name' => 'orderimport.paymentmethod', 'type' => 'select','cssclasses' => array('mljs-directbuy', ),),
            'string' => array('name' => 'orderimport.paymentmethod.name', 'type' => 'string','default' => '{#setting:currentMarketplaceName#}','cssclasses' => array('mljs-directbuy', ),)
        ),
        'expert' => true,
        'cssclasses' => array('mljs-directbuy', ),
    ),
));