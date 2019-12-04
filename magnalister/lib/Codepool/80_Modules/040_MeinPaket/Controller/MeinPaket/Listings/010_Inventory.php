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
MLFilesystem::gi()->loadClass('Listings_Controller_Widget_Listings_InventoryAbstract');

class ML_MeinPaket_Controller_MeinPaket_Listings_Inventory extends ML_Listings_Controller_Widget_Listings_InventoryAbstract {

    protected $aParameters = array('controller');

    public static function getTabTitle() {
        return MLI18n::gi()->get('ML_GENERIC_INVENTORY');
    }

    public static function getTabActive() {
        return MLModul::gi()->isConfigured();
    }
	
    protected function getFields() {
        $aFields = parent::getFields();
        return array_merge(
            $aFields,
            array(
                'ItemID' => array(
                    'Label' => MLI18n::gi()->ML_MAGNACOMPAT_LABEL_MP_ITEMID,
                    'Sorter' => 'meinpaketid',
                    'Getter' => null,
                    'Field' => 'MeinpaketID',
                )
            )
        );
    }
	
	
    protected function getItemId($item) {
        return '<td title="' . fixHTMLUTF8Entities($item['MeinpaketID'], ENT_COMPAT) . '">' . $item['MeinpaketID'] . '</td>';
    }

    protected function isSplit($item)
    {
        return
            '<td>' .
            (empty($item['IsSplit']) ? MLI18n::gi()->get('ML_BUTTON_LABEL_NO') : MLI18n::gi()->get('ML_BUTTON_LABEL_YES')) .
            '</td>';
    }
	
}
