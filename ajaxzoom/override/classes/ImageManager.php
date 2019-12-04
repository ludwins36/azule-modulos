<?php
/**
*  Module: jQuery AJAX-ZOOM for Prestashop, imageManager.php
*
*  Copyright: Copyright (c) 2010-2017 Vadim Jacobi
*  License Agreement: http://www.ajax-zoom.com/index.php?cid=download
*  Version: 1.7.0
*  Date: 2017-11-26
*  Review: 2017-11-26
*  URL: http://www.ajax-zoom.com
*  Demo: prestashop.ajax-zoom.com
*  Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2017 AJAX-ZOOM, Vadim Jacobi
*  @license   http://www.ajax-zoom.com/index.php?cid=download
*/

class ImageManager extends ImageManagerCore
{
    public static function resize(
        $src_file,
        $dst_file,
        $dst_width = null,
        $dst_height = null,
        $file_type = 'jpg',
        $force_type = false,
        &$error = 0,
        &$tgt_width = null,
        &$tgt_height = null,
        $quality = 5,
        &$src_width = null,
        &$src_height = null
    ) {
        if ((Configuration::get('AZ_UPLOADNOCOMPRESS') == 'true'
            || Configuration::get('AJAXZOOM_UPLOADNOCOMPRESS') == 'true')
            && $dst_width == null && $dst_height == null
        ) {
            return copy($src_file, $dst_file);
        } else {
            return parent::resize(
                $src_file,
                $dst_file,
                $dst_width,
                $dst_height,
                $file_type,
                $force_type,
                $error,
                $tgt_width,
                $tgt_height,
                $quality,
                $src_width,
                $src_height
            );
        }
    }
}
