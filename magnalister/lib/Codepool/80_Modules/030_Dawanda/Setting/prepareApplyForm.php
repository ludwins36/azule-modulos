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

MLSetting::gi()->dawanda_prepare_apply_form = array(
    'details' => array(
        'fields' => array(
            array(
                'name' => 'producttype',
                'type' => 'select',
            ),
            array(
                'name' => 'listingduration',
                'type' => 'select',
            ),
            array(
                'name' => 'returnpolicy',
                'type' => 'select',
            ),
            array(
                'name' => 'shippingservice',
                'type' => 'select',
            ),
            array(
                'name' => 'shippingtime',
                'type' => 'select',
            )
        )
    ),
    'categories' => array(
        'fields' => array(
            array(
                'name' => 'variationgroups',
                'type' => 'categoryselect',
                'subfields' => array(
                    'variationgroups.value' => array('name' => 'variationgroups.value', 'type' => 'categoryselect', 'cattype' => 'marketplace'),
                    'secondary' => array('name' => 'secondarycategory', 'type' => 'categoryselect', 'cattype' => 'marketplace'),
                    'store' => array('name' => 'storecategory', 'type' => 'categoryselect', 'cattype' => 'store')
                ),
            ),
        ),
    ),
    'variationmatching' => array(
        'type' => 'ajaxfieldset',
        'legend' => array(
            'template' => 'two-columns',
        ),
        'field' => array (
            'name' => 'variationmatching',
            'type' => 'ajax',
        ),
    ),
);

MLSetting::gi()->dawanda_prepare_variations = array(
    'variations' => array(
        'fields' => array(
            array(
                'name' => 'variationgroups',
                'type' => 'categoryselect',
                'subfields' => array(
                    'variationgroups.value' => array('name' => 'variationgroups.value', 'type' => 'categoryselect', 'cattype' => 'marketplace'),
                ),
            ),
        ),
    ),
    'variationmatching' => array(
        'type' => 'ajaxfieldset',
        'legend' => array(
            'template' => 'two-columns',
        ),
        'field' => array (
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
