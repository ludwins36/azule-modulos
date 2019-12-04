<?php
/**
*  Module: jQuery AJAX-ZOOM for Prestashop, iframe.php
*
*  Copyright: Copyright (c) 2010-2019 Vadim Jacobi
*  License Agreement: http://www.ajax-zoom.com/index.php?cid=download
*  Version: 1.20.0
*  Date: 2019-06-18
*  Review: 2019-06-18
*  URL: http://www.ajax-zoom.com
*  Demo: prestashop.ajax-zoom.com
*  Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2019 AJAX-ZOOM, Vadim Jacobi
*  @license   http://www.ajax-zoom.com/index.php?cid=download
*/

require '../../../config/config.inc.php';
require '../AzMouseoverSettings.php';

$az_opt_prefix = version_compare(_PS_VERSION_, '1.5', '>=')
    && version_compare(_PS_VERSION_, '1.6', '<') ? '' : 'AJAXZOOM';

function getIssetMod($par)
{
    return Tools::getIsset($par);
}

function getValueMod($par)
{
    return Tools::getValue($par);
}

function getAz360($id_360)
{
    return Db::getInstance()->ExecuteS('SELECT g.*, COUNT(g.id_360) 
        AS qty, s.id_360set 
        FROM `'._DB_PREFIX_.'ajaxzoom360` g 
        LEFT JOIN `'._DB_PREFIX_.'ajaxzoom360set` s ON g.id_360 = s.id_360 
        WHERE g.id_360 = '.(int)$id_360.' 
        GROUP BY g.id_360');
}

function getAzConfig()
{
    $conf = array();
    $data = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'configuration` 
        WHERE name LIKE \'AJAXZOOM\_%\' OR name LIKE \'AZ\_%\' OR name LIKE \'\_%\'');

    foreach ($data as $tmp) {
        if (!stristr($tmp['name'], 'license')) {
            $conf[$tmp['name']] = $tmp['value'];
        }
    }

    return $conf;
}

function prepareSettings($str)
{
    $res = array();
    $settings = (array)Tools::jsonDecode($str);

    foreach ($settings as $key => $value) {
        if ($value == 'false' || $value == 'true' || $value == 'null' || is_numeric($value)
            || Tools::substr($value, 0, 1) == '{' || Tools::substr($value, 0, 1) == '['
        ) {
            $res[] = '"'.$key.'": '.$value;
        } else {
            $res[] = '"'.$key.'": "'.$value.'"';
        }
    }

    return implode(', ', $res);
}

function images360Json()
{
    $arr = array();
    $id_360 = getValueMod('id_360');
    $groups = getAz360($id_360);

    if (isset($groups[0]) && is_array($groups[0]) && !empty($groups[0])) {
        $group = $groups[0];
        $settings = prepareSettings($group['settings']);

        if (!empty($settings)) {
            $settings = ', '.$settings;
        }

        if ($group['qty'] > 0) {
            $crop = empty($group['crop']) ? '[]' : trim(preg_replace('/\s+/', ' ', $group['crop']));
            if ($group['qty'] == 1) {
                $str = '"'.$group['id_360'].'": ';
                $str .= '{"path": "'.__PS_BASE_URI__
                    .'img/p/360/'.$group['id_product'].'/'.$group['id_360'].'/'.$group['id_360set'].'"';
                $str .= $settings;
                $str .= ', "combinations": ['.$group['combinations'].']';

                if ($crop && $crop != '[]') {
                    $str .= ', "crop": '.$crop;
                }

                $str .= '}';
            } else {
                $str = '"'.$group['id_360'].'": {"path": "'.__PS_BASE_URI__
                    .'img/p/360/'.$group['id_product'].'/'.$group['id_360'].'"';
                $str .= $settings;
                $str .= ', "combinations": ['.$group['combinations'].']';

                if ($crop && $crop != '[]') {
                    $str .= ', "crop": '.$crop;
                }

                $str .= '}';
            }
            $arr[] = $str;
        }
    }

    $ret = '{'.implode(',', $arr).'}';
    $ret = str_replace('\\n', '', $ret);
    $ret = preg_replace('/\t+/', '', $ret);
    return $ret;
}

function getLang($cookie)
{
    $lang = 'en';
    if (isset($cookie) && is_object($cookie) && $cookie->id_lang) {
        $lang = Language::getIsoById((int)$cookie->id_lang);
        $lang = Tools::substr($lang, 0, 2);
        $lang = Tools::strtolower($lang);
    }
    return $lang;
}

if (!getIssetMod('id_360')) {
    echo 'No parameters passed.';
    exit;
}

$images360Json = images360Json();

if ($images360Json == '{}') {
    echo '360 not found.';
    exit;
}

$lang = getLang(isset($cookie) ? $cookie : null); // $cookie is defined elsewhere
$config = getAzConfig();

$mouseover_settings = new AzMouseoverSettings();
$init_param = $mouseover_settings->getInitJs(array(
    'cfg' => $config,
    'window' => 'window.',
    'holder_object' => 'jQuery.axZm_psh',
    'exclude_opt' => array(),
    'exclude_cat' => array('video_settings'),
    'ovrprefix' => $az_opt_prefix,
    'differ' => true,
    'min' => false
));

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title>AJAX-ZOOM 360</title>
<link rel="stylesheet" href="../axZm/axZm.css" type="text/css" media="all" />
<link rel="stylesheet" 
    href="../axZm/extensions/axZmThumbSlider/skins/default/jquery.axZm.thumbSlider.css" 
    type="text/css" media="all" />
<link rel="stylesheet" 
    href="../axZm/extensions/axZmMouseOverZoom/jquery.axZm.mouseOverZoom.5.css" 
    type="text/css" media="all" />
<link rel="stylesheet" href="../axZm/extensions/jquery.axZm.expButton.css" type="text/css" media="all" />
<script type="text/javascript" src="../axZm/plugins/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="../axZm/jquery.axZm.js"></script>
<script type="text/javascript" src="../axZm/extensions/axZmThumbSlider/lib/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="../axZm/extensions/axZmThumbSlider/lib/jquery.axZm.thumbSlider.js"></script>
<script type="text/javascript" src="../axZm/plugins/spin/spin.min.js"></script>
<script type="text/javascript" src="../axZm/extensions/axZmMouseOverZoom/jquery.axZm.mouseOverZoom.5.js"></script>
<script type="text/javascript" src="../axZm/extensions/axZmMouseOverZoom/jquery.axZm.mouseOverZoomInit.5.js"></script>
<script type="text/javascript" src="../axZm/extensions/jquery.axZm.expButton.js"></script>
<script type="text/javascript" src="../axZm/extensions/jquery.axZm.imageCropLoad.js"></script>
<script type="text/javascript" src="../axZm/plugins/JSON/jquery.json-2.3.min.js"></script>
<style type="text/css">
    html, body{
        height: 100%; margin: 0;
    }
    body{
        overflow: hidden;
    }
</style>
</head>
<body>

<!-- AJAX-ZOOM mouseover block  -->
<div class="axZm_mouseOverWithGalleryContainer" id="axZm_mouseOverWithGalleryContainer">

    <!-- Parent container for offset to the left or right -->
    <div class="axZm_mouseOverZoomContainerWrap">

        <!-- Alternative container for title of the images -->
        <div class="axZm_mouseOverTitleParentAbove"></div>

        <!-- Container for mouse over image -->
        <div id="<?php echo $config[$az_opt_prefix.'_DIVID']; ?>" class="axZm_mouseOverZoomContainer">

            <!-- Optional CSS aspect ratio and message to preserve layout before JS is triggered -->
            <div class="axZm_mouseOverAspectRatio">
                <div>
                    <span>Loading</span>
                </div>
            </div>

        </div>
    </div>

    <!-- gallery with thumbs (will be filled with thumbs by javascript) -->
    <div id="<?php echo $config[$az_opt_prefix.'_GALLERYDIVID']; ?>" class="axZm_mouseOverGallery"></div>

</div>

<script type="text/javascript">
/*!
*  Module: jQuery AJAX-ZOOM for Prestashop
*  Version: 1.14.0
*  Date: 2018-09-15
*  Review: 2018-09-15
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2018 AJAX-ZOOM, Vadim Jacobi
*  @license   http://www.ajax-zoom.com/index.php?cid=download
*/

$.axZm_psh = { };
var ajaxzoom_images360JSON = '<?php echo $images360Json; ?>';
var fsSupport = document.documentElement.requestFullscreen 
    || (document.documentElement.msRequestFullscreen && window == window.top) 
    || document.documentElement.mozRequestFullScreen 
    || document.documentElement.webkitRequestFullscreen;

var fullscreenEnabled = document.fullscreenEnabled 
    || (document.msFullscreenEnabled && window == window.top) 
    || document.mozFullScreenEnabled 
    || document.webkitFullscreenEnabled;

var fullScreenCornerButton = (fsSupport && fullscreenEnabled) ? true : false;

ajaxzoom_images360JSON = ajaxzoom_images360JSON.replace('"spinDemoTime"', '"fullScreenCornerButton": '
    +fullScreenCornerButton+', "spinDemoTime"');

jQuery.axZm_psh.prevPid = null;
jQuery.axZm_psh.axZmPath = '../axZm/';
jQuery.axZm_psh.shopLang = '<?php echo $lang;?>';
jQuery.axZm_psh.IMAGES_JSON = {};
jQuery.axZm_psh.IMAGES_360_JSON = $.parseJSON(ajaxzoom_images360JSON);
jQuery.axZm_psh.initParam = <?php echo $init_param; ?>;
$.extend(true, jQuery.axZm_psh.initParam, {
    heightRatio: 5, 
    heightRatioOneImg: 5,
    heightMaxWidthRatio: false,
    maxSizePrc: '1.0|-30',
    fullScreenApi: true,
    ajaxZoomOpenMode: 'fullscreen',
    ajaxZoomOpenModeTouch: 'fullscreen'
});

<?php
if (getIssetMod('cropsliderposition')) {
    echo 'jQuery.axZm_psh.initParam.cropSliderPosition = "'.getValueMod('cropsliderposition').'";'."\r\n";
}
?>
var start_az = function() {
    jQuery.mouseOverZoomInit(jQuery.axZm_psh.initParam);
}

var init_az = function() {
    if (jQuery('iframe[src*="modules/ajaxzoom"]', window.parent.document).is(":visible") == true) {
        start_az();
    } else {
        setTimeout(init_az, 500);
    }
};

init_az();
</script>
</body>
</html>