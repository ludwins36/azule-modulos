<?php
/**
*  Module: jQuery AJAX-ZOOM for Prestashop, batch.config.inc.php
*
*  Copyright: Copyright (c) 2010-2019 Vadim Jacobi
*  License Agreement: https://www.ajax-zoom.com/index.php?cid=download
*  Version: 1.20.0
*  Date: 2019-06-18
*  Review: 2019-06-18
*  URL: https://www.ajax-zoom.com
*  Demo: prestashop.ajax-zoom.com
*  Documentation: https://www.ajax-zoom.com/index.php?cid=docs
*
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2019 AJAX-ZOOM, Vadim Jacobi
*  @license   https://www.ajax-zoom.com/index.php?cid=download
*/

if (!headers_sent() && !session_id()) {
    // This file is an include that configurs a third party package.
    // It does not include PS core classes all the time but only once on access.
    // We need to save some variable such as options configuration or image types once in a session.
    session_start(); // cannot remove it, see above comment
}

$axzm_vendor_hash = md5('axZmBtchVendor'.__FILE__);

if (isset($_SERVER['REQUEST_URI'])
    && strstr($_SERVER['REQUEST_URI'], 'batch_start=1')
    && isset($_SESSION[$axzm_vendor_hash])
) {
    // Unset session values when window is opened
    unset($_SESSION[$axzm_vendor_hash]);
}

if (!isset($_SESSION[$axzm_vendor_hash])) {
    require '../../../config/config.inc.php';
    $cookies = new Cookie('psAdmin');

    if (!$cookies->id_employee) {
        echo 'You are not logged in as administrator.';
        exit;
    }

    $filter_names_ps = array();
    if (class_exists('ImageType') && method_exists(ImageType, 'getFormattedName')) {
        $filter_names_ps = array(
            ImageType::getFormattedName('home'),
            ImageType::getFormattedName('cart'),
            ImageType::getFormattedName('large'),
            ImageType::getFormattedName('medium'),
            ImageType::getFormattedName('small'),
            ImageType::getFormattedName('thickbox')
        );
    } else {
        $filter_names_ps = array(
            'cart_default', // method getFormattedName may not exist in older PS versions
            'home_default', // method getFormattedName may not exist in older PS versions
            'large_default', // method getFormattedName may not exist in older PS versions
            'medium_default', // method getFormattedName may not exist in older PS versions
            'small_default', // method getFormattedName may not exist in older PS versions
            'thickbox_default' // method getFormattedName may not exist in older PS versions
        );
    }

    // Read AJAX-ZOOM configuration values
    $conf = array();
    $data = Db::getInstance()->ExecuteS('SELECT * 
        FROM `'._DB_PREFIX_.'configuration` 
        WHERE name LIKE \'AJAXZOOM\_%\' OR name LIKE \'AZ\_%\' OR name LIKE \'\_%\'');

    foreach ($data as $tmp) {
        $conf[$tmp['name']] = $tmp['value'];
    }

    $_SESSION[$axzm_vendor_hash] = array(
        'base_uri' => __PS_BASE_URI__,
        'conf' => $conf,
        'filter_names_ps' => $filter_names_ps
    );
}

if (isset($_SESSION[$axzm_vendor_hash]['base_uri'])) {
    if (!method_exists(new axZmF, 'classVer')) { // axZmF is defined else where
        die('Please update AJAX-ZOOM core files');
    } elseif (version_compare(axZmF::classVer()['ver'], '2.3') < 0) { // axZmF is defined else where
        die('Please update AJAX-ZOOM core files');
    }

    $axzm_batch_config = array(
        'byPassBatchPass' => true,
        'filesFilter' => $_SESSION[$axzm_vendor_hash]['filter_names_ps'],
        'arrayMake' => array(
            'In' => true,
            'Th' => false,
            'tC' => true,
            'Ti' => true
        ),
        'cmsMode' => true,
        'afterBatchFolderEndJsClb' => 'parent.window.afterBatchFolderEndJsClb',
        'fluid' => true,
        'confirmBatch' => false,
        'picBaseDir' => $_SESSION[$axzm_vendor_hash]['base_uri'].'/img/p/',
        'example' => axZmF::confPar('images360example', $axzm_vendor_hash),
        'dynImageSizes' => axZmF::getMouseOverDefault($axzm_vendor_hash),
        'enableBatchThumbs' => true,
        'batchThumbsDynString' => 'width=100&height=100&thumbMode=contain',
        'vendorNote' => array(
            'title' => '
<img src="'.$_SESSION[$axzm_vendor_hash]['base_uri'].'modules/ajaxzoom/views/img/global.png">
<span style="font-weight: bold;">
<span style="color: #29245c;">Presta</span><span style="color: #d40f5e;">Shop</span>
</span> users notes',
            'text' => array(
                'In the 360 folder you will find source images for the 360 / 3D images.
When you proceed this folder, please disable / uncheck the 
<strong>(tC) dynamic thumbs</strong>, 
because this AJAX-ZOOM cache type is not needed for 360 / 3D.'
            )
        )
    );
} else {
    die('Please reload...');
}
