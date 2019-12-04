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
MLSetting::gi()->crowdfox_prepare_apply_form = array(
    'details' => array(
        'fields' => array(
            array(
                'name' => 'itemtitle',
                'type' => 'string',
                'maxlength' => 255,
            ),
            array(
                'name' => 'description',
                'type' => 'text',
                'attributes' => array(
                    'rows' => '30',
                ),
                'maxlength' => 5000,
            ),
            array(
                'name' => 'gtin',
                'type' => 'string',
                'maxlength' => 13,
            ),
            array(
                'name' => 'brand',
                'type' => 'string',
                'maxlength' => 255,
            ),
            array(
                'name' => 'mpn',
                'type' => 'string',
                'maxlength' => 50,
            ),
            array(
                'name' => 'price',
                'type' => 'price',
                'currency' => 'EUR',
                'enabled' => false,
            ),
            array(
                'name' => 'obligationinfo',
                'type' => 'obligationinfo',
                'currency' => 'EUR',
                'enabled' => false,
            ),
            array(
                'name' => 'images',
                'type' => 'imagemultipleselect',
            ),
        ),
    ),
    'category' => array(
        'legend' => array(
            'classes' => array('mlhidden'),
        ),
        'fields' => array(
            'variationgroups.value' => array(
                'name' => 'variationgroups.value',
                'type' => 'select',
                'value' => 'CrowdfoxPlaceholderCategory',
                'classes' => array('mlhidden'),
            ),
        ),
    ),
    'variationmatching' => array(
        'type' => 'ajaxfieldset',
        'legend' => array(
            'template' => 'two-columns',
        ),
        'field' => array(
            'name' => 'variationmatching',
            'type' => 'ajax',
        ),
    ),
    'other' => array(
        'fields' => array(
            array(
                'name' => 'deliverytime',
                'type' => 'string',
            ),
            array(
                'name' => 'deliverycost',
                'type' => 'string',
            ),
            array(
                'name' => 'shippingmethod',
                'type' => 'select',
            ),
        ),
    ),
);

MLSetting::gi()->crowdfox_prepare_variations = array(
    'variations' => array(
        'legend' => array(
            'classes' => array('mlhidden'),
        ),
        'fields' => array(
            array(
                'name' => 'variationgroups.value',
                'type' => 'select',
                'value' => 'CrowdfoxPlaceholderCategory',
                'classes' => array('mlhidden'),
            ),
        ),
    ),
    'variationmatching' => array(
        'type' => 'ajaxfieldset',
        'legend' => array(
            'template' => 'two-columns',
        ),
        'field' => array(
            'name' => 'variationmatching',
            'type' => 'ajax',
        ),
    ),
    'action' => array(
        'legend' => array(
            'classes' => array(
                'mlhidden',
            ),
        ),
        'row' => array(
            'template' => 'action-row-row-row',
        ),
        'fields' => array(
            array(
                'name' => 'saveaction',
                'value' => 'save',
                'type' => 'submit',
                'position' => 'right',
            ),
            array(
                'name' => 'resetaction',
                'value' => 'reset',
                'type' => 'submit',
                'position' => 'left',
            ),
        ),
    ),
);