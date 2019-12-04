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
* (c) 2010 - 2018 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

/**
 * all fields include i18n directly
 */
MLSetting::gi()->add('formfields_etsy', array(
    'access.token' => array(
        'i18n' => '{#i18n:formfields_etsy__access.token#}',
        'name' => 'etsytoken',
        'type' => 'etsy_token',
    ),
    'access.username' => array(
        'i18n' => '{#i18n:formfields_etsy__access.username#}',
        'name' => 'username',
        'type' => 'string',
    ),
    'access.password' => array(
        'i18n' => '{#i18n:formfields_etsy__access.password#}',
        'name' => 'password',
        'type' => 'password',
    ),
    'shop.language' => array(
        'i18n' => '{#i18n:formfields_etsy__shop.language#}',
        'name' => 'shop.language',
        'type' => 'select',
    ),
    'shop.currency' => array(
        'i18n' => '{#i18n:formfields_etsy__shop.currency#}',
        'name' => 'currency',
        'type' => 'select',
    ),
    'prepare.whomade' => array(
        'i18n' => '{#i18n:formfields_etsy__prepare.whomade#}',
        'name' => 'whomade',
        'type' => 'select',
        'values' => '{#i18n:formfields_etsy__whomade__values#}',
    ),
    'prepare.whenmade' => array(
        'i18n' => '{#i18n:formfields_etsy__prepare.whenmade#}',
        'name' => 'whenmade',
        'type' => 'select',
        'values' => '{#i18n:formfields_etsy__whenmade__values#}',
    ),
    'prepare.issupply' => array(
        'i18n' => '{#i18n:formfields_etsy__prepare.issupply#}',
        'name' => 'issupply',
        'type' => 'select',
        'values' => '{#i18n:formfields_etsy__issupply__values#}',
    ),
    'prepare.language' => array(
        'i18n' => '{#i18n:formfields_etsy__prepare.language#}',
        'name' => 'lang',
        'type' => 'select',
    ),
    'prepare.imagesize' => array(
        'i18n' => '{#i18n:formfields_etsy__prepare.imagesize#}',
        'name' => 'imagesize',
        'type' => 'select',
        'default' => 1000,
    ),
    'shippingtemplate' => array(
        'i18n' => '{#i18n:formfields_etsy__shippingtemplate#}',
        'name' => 'shippingtemplate',
        'type' => 'select',
    ),
    'category' => array(
        'i18n' => '{#i18n:formfields_etsy__category#}',
        'name' => 'category',
        'type' => 'etsy_categories',
        'subfields' => array(
            'primary' => array('name' => 'primarycategory', 'type' => 'categoryselect', 'cattype' => 'marketplace'),
        ),
    ),
    'prepare_price' => array(
        'i18n' => '{#i18n:formfields_etsy__prepare_price#}',
        'name' => 'price',
        'type' => 'hidden',
    ),
    'prepare_quantity' => array(
        'i18n' => '{#i18n:formfields_etsy__prepare_quantity#}',
        'name' => 'quantity',
        'type' => 'hidden',
    ),
    'shippingtemplatetitle' => array(
        'i18n' => '{#i18n:formfields_etsy__shippingtemplatetitle#}',
        'name' => 'shippingtemplatetitle',
        'type' => 'string',
    ),
    'shippingtemplatecountry' => array(
        'i18n' => '{#i18n:formfields_etsy__shippingtemplatecountry#}',
        'name' => 'shippingtemplatecountry',
        'type' => 'select',
    ),
    'shippingtemplateprimarycost' => array(
        'i18n' => '{#i18n:formfields_etsy__shippingtemplateprimarycost#}',
        'name' => 'shippingtemplateprimarycost',
        'type' => 'string',
    ),
    'shippingtemplatesecondarycost' => array(
        'i18n' => '{#i18n:formfields_etsy__shippingtemplatesecondarycost#}',
        'name' => 'shippingtemplatesecondarycost',
        'type' => 'string',
    ),
    'shippingtemplatesend' => array(
        'i18n' => '{#i18n:formfields_etsy__shippingtemplatesend#}',
        'name' => 'shippingtemplatesend',
        'type' => 'etsy_shippingtemplatesave',
    ),
    'fixed.price' => array(
        'i18n' => '{#i18n:formfields_etsy__fixed.price#}',
        'name' => 'fixed.price',
        'type' => 'subFieldsContainer',
        'subfields' => array(
            'addkind' => array(
                'name' => 'price.addkind',
                'i18n' => '{#i18n:formfields_etsy__fixed.price.addkind#}',
                'type' => 'select'
            ),
            'factor' => array(
                'name' => 'price.factor',
                'i18n' => '{#i18n:formfields_etsy__fixed.price.factor#}',
                'type' => 'string'
            ),
            'signal' => array(
                'name' => 'price.signal',
                'i18n' => '{#i18n:formfields_etsy__fixed.price.signal#}',
                'type' => 'string'
            ),
        ),
    ),
    'prepare_title' => array(
        'i18n' => '{#i18n:formfields_etsy__prepare_title#}',
        'name' => 'Title',
        'type' => 'string',
        'singleproduct' => true,
    ),
    'prepare_description' => array(
        'i18n' => '{#i18n:formfields_etsy__prepare_description#}',
        'name' => 'Description',
        'type' => 'wysiwyg',
        'singleproduct' => true,
    ),
    'prepare_image' => array(
        'i18n' => '{#i18n:formfields_etsy__prepare_image#}',
        'name' => 'Image',
        'type' => 'imagemultipleselect',
        'singleproduct' => true,
    ),
    'prepare_variationgroups' => array(
        'label' => '{#i18n:formfields_etsy__prepare_variationgroups#}',
        'name' => 'variationgroups',
        'type' => 'etsy_categories',
        'subfields' => array(
            'variationgroups.value' => array(
                'name' => 'variationgroups.value',
                'type' => 'categoryselect',
                'cattype' => 'marketplace',
                'value' => null,
            ),
        ),
    ),
    'prepare_saveaction' => array(
        'name' => 'saveaction',
        'type' => 'submit',
        'value' => 'save',
        'position' => 'right',
    ),
    'prepare_resetaction' => array(
        'name' => 'resetaction',
        'type' => 'submit',
        'value' => 'reset',
        'position' => 'left',
    ),

));