<?php
/**
*  Module: jQuery AJAX-ZOOM for Prestashop, lic.php
*
*  Copyright: Copyright (c) 2010-2018 Vadim Jacobi
*  License Agreement: http://www.ajax-zoom.com/index.php?cid=download
*  Version: 1.16.0
*  Date: 2018-10-31
*  Review: 2018-10-31
*  URL: http://www.ajax-zoom.com
*  Demo: prestashop.ajax-zoom.com
*  Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2018 AJAX-ZOOM, Vadim Jacobi
*  @license   http://www.ajax-zoom.com/index.php?cid=download
*/

$zoom = $zoom;

if (!isset($zoom)) { /* variable $zoom is defined in the external APP */
    exit;
}

if (file_exists(dirname(__FILE__).'/../../app/config/parameters.php')) {
    $tmp_ps_par = include dirname(__FILE__).'/../../app/config/parameters.php';
    error_reporting(0);
    if (!empty($tmp_ps_par) && is_array($tmp_ps_par)) {
        if (isset($tmp_ps_par['parameters'])
            && is_array($tmp_ps_par['parameters'])
            && isset($tmp_ps_par['parameters'][database_host])
        ) {
            $tmp_ps_par = $tmp_ps_par['parameters'];
        }

        define(_DB_SERVER_, $tmp_ps_par['database_host']);
        define(_DB_USER_, $tmp_ps_par['database_user']);
        define(_DB_PASSWD_, $tmp_ps_par['database_password']);
        define(_DB_NAME_, $tmp_ps_par['database_name']);
        define(_DB_PREFIX_, $tmp_ps_par['database_prefix']);
        unset($tmp_ps_par);
    }
} else {
    include dirname(__FILE__).'/../../config/settings.inc.php';
    error_reporting(0);
}

if (function_exists('mysqli_connect')) {
    if ($mysqli = new mysqli(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_)) {
        if ($result = $mysqli->query('SELECT * FROM `'._DB_PREFIX_.'configuration` 
            WHERE name = \'AJAXZOOM_LICENSE\'')) {
            $data = $result->fetch_array();
            mysqli_close($mysqli);

            $licenses = explode('"},{"', str_replace(array('[{"', '"}]'), '', $data['value']));
            $license = array();

            foreach ($licenses as $str) {
                $pairs = explode('","', $str);

                foreach ($pairs as $pair) {
                    list($key, $value) = explode('":"', $pair);
                    $license[$key] = $value;
                }

                $zoom['config']['licenses'][$license['domain']] = array(
                    'licenceType' => $license['type'],
                    'licenceKey' => $license['key'],
                    'error200' => $license['error200'],
                    'error300' => $license['error300']
                );
            }
        }
    }
} elseif (function_exists('mysql_connect')) {
    $db_connect = mysql_connect(_DB_SERVER_, _DB_USER_, _DB_PASSWD_); // old versions
    $db = mysql_select_db(_DB_NAME_); // old versions, function_exists above
    $data_query = mysql_query('SELECT * FROM `'._DB_PREFIX_.'configuration` 
        WHERE name = \'AJAXZOOM_LICENSE\'');// old versions, function_exists above
    $data = mysql_fetch_array($data_query); // old versions, function_exists above
    mysql_close($db_connect); // old versions, function_exists above

    $licenses = explode('"},{"', str_replace(array('[{"', '"}]'), '', $data['value']));
    $license = array();
    foreach ($licenses as $str) {
        $pairs = explode('","', $str);

        foreach ($pairs as $pair) {
            list($key, $value) = explode('":"', $pair);
            $license[$key] = $value;
        }

        $zoom['config']['licenses'][$license['domain']] = array(
            'licenceType' => $license['type'],
            'licenceKey' => $license['key'],
            'error200' => $license['error200'],
            'error300' => $license['error300']
        );
    }
}
