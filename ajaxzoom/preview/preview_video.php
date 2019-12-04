<?php
/**
*  Module: jQuery AJAX-ZOOM for Prestashop, preview_video.php
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

$cookies = new Cookie('psAdmin');

if (!$cookies->id_employee) {
    echo 'You are not logged in as administrator.';
    exit;
}

function getIssetMod($par)
{
    return Tools::getIsset($par);
}

function getValueMod($par)
{
    return Tools::getValue($par);
}

if (!getIssetMod('id_video')) {
    echo 'No parameters passed';
    exit;
}

/*
$conf = array();
$data = Db::getInstance()->ExecuteS('SELECT * 
    FROM `'._DB_PREFIX_.'configuration` 
    WHERE name LIKE \'AJAXZOOM\_%\' OR name LIKE \'AZ\_%\' OR name LIKE \'\_%\'');

foreach ($data as $tmp) {
    $conf[$tmp['name']] = $tmp['value'];
}
*/

$video = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ajaxzoomvideo` 
    WHERE id_video = \''.(int)getValueMod('id_video').'\'');

?>
<!DOCTYPE html>
<html>
<head>
<title>AJAX-ZOOM Video Preview</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="imagetoolbar" content="no">
<meta name="viewport" content="width=device-width,  minimum-scale=1, maximum-scale=1, user-scalable=no">

<style type="text/css"> 
    html {height: 100%; width: 100%; font-family: Tahoma, Arial; font-size: 10pt; margin: 0; padding: 0;}
    body {height: 100%; width: 100%; overflow: hidden; margin: 0; padding: 0;} 
    body:-webkit-fullscreen {width: 100%; height: 100%;}
    body:-ms-fullscreen {width: 100%; height: 100%;}
    a {color: blue; outline: 0; outline-style: none; text-decoration: none;} 
    a:visited {color: blue;} a:hover {color: green;}
    h2 {padding:0px; margin: 35px 0px 15px 0px; font-size: 22px;}
    h3 {font-family: Arial; color: #1A4A7A; font-size: 18px; padding: 20px 0px 3px 0px; margin: 0;}
    p {text-align: justify; text-justify: newspaper;}
    iframe, video {width: 100%; height: 100%;}
</style>

<script src="../axZm/plugins/jquery-1.8.3.min.js"></script>

</head>

<body>
<?php
if ($video['type'] == 'youtube') {
?>
<iframe src="https://www.youtube.com/embed/<?php echo $video['uid']; ?>" style="width: 100%; height: 100%;" 
    width="100%" height="100%" frameborder="0" 
    webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
<?php
} elseif ($video['type'] == 'vimeo') {
?>
<iframe src="https://player.vimeo.com/video/<?php echo $video['uid']; ?>?color=ff6944&title=0&byline=0&portrait=0" 
    style="width: 100%; height: 100%;" width="100%" height="100%" frameborder="0" 
    webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
<?php
} elseif ($video['type'] == 'dailymotion') {
?>
<iframe frameborder="0" src="//www.dailymotion.com/embed/video/<?php echo $video['uid']; ?>" 
    allowfullscreen></iframe>
<?php
} elseif ($video['type'] == 'videojs') {
?>
<video id="my-video" class="video-js" controls preload="auto" width="100%" height="100%" 
    data-setup="{}" style="width: 100%; height: 100%;">

<source src="<?php echo $video['uid'] ?>" 
    type='video/<?php echo pathinfo($video['uid'], PATHINFO_EXTENSION); ?>'>
</video>
<?php
}
?>

</body>
</html>