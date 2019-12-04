<?php

// 
MLSetting::gi()->add('configuration', array(
    'general' => array(
        'fields' => array(
            'pass' => array(
                'name' => 'general.passphrase',
                'type' => 'string',
            ),
        ),
    ),
    'sku' => array(
        'fields' => array(
            'sku' => array(
                'name' => 'general.keytype',
                'type' => 'radio',
                'alertvalue' => array('pID', 'artNr'),
                'inputCellStyle' => 'line-height: 1.5em;',
                'separator' => '<br/>',
                'default' => 'artNr',
            )
        )
    ),
    'stats' => array(
        'fields' => array(
            'back' => array(
                'name' => 'general.stats.backwards',
                'type' => 'select',
                'default' => '5',
            ),
        ),
    ),
    'orderimport' => array(
        'fields' => array(
            'orderinformation' => array(
                'type' => 'bool',
                'name' => 'general.order.information',
                'default' => array(
                    'val' => false
                )
            ),
        ),
    ),
    'cronTimeTable' => array(
        'fields' => array(
            'editor' => array(
                'name' => 'general.editor',
                'type' => 'radio',
                'expert' => true,
                'inputCellStyle' => 'line-height: 1.5em;',
                'separator' => '<br/>',
                'default' => 'tinyMCE',
            ),
            'stocksyncbyorder' => array(
                'name' => 'general.trigger.checkoutprocess.inventoryupdate',
                'type' => 'bool',
                'expert' => true,
                'default' => array(
                    'val' => true,
                ),
            ),
        ),
    ),
    'articleStatusInventory' => array(
        'fields' => array(
            'statusIsZero' => array(
                'name' => 'general.inventar.productstatus',
                'type' => 'radio',
                'default' => 'false',
            )
        )
    ),
    'productfields' => array(
        'fields' => array(
            'manufacturer' => array(
                'name' => 'general.manufacturer',
                'type' => 'select',
            ),
            'mfnpartno' => array(
                'name' => 'general.manufacturerpartnumber',
                'type' => 'select',
            ),
            'EAN' => array(
                'name' => 'general.ean',
                'type' => 'select',
            ),
            'UPC' => array(
                'name' => 'general.upc',
                'type' => 'select',
            ),
        ),
    ),
        )
);
