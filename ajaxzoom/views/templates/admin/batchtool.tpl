{*
*  Module: jQuery AJAX-ZOOM for Prestashop, batchtool.tpl
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
*}

<style type="text/css">
body {
    background-color: #fff;
}

#content.nobootstrap {
    min-width: 0;
}

#content {
    padding-bottom: 5px;
}

#az_batchtool_parent {
    margin-left: -15px;
    margin-right: -15px;
    position: relative;
}

#az_batch_act_360_parent {
    position: absolute;
    z-index: 5;
    right: 0;
    top: 20px;
}

{if floatval($ps_version) == 1.7}
#main {
    padding-bottom: 0;
}
@media (max-width: 1023px) {
    #content.nobootstrap {
        margin-left: 0;
        padding-top: 130px;
    }
}
{/if}
</style>

<div class="row" style="padding-top: 5px;">
{l s='You do not necessarily need to use the batch tool, because if image tiles and other AJAX-ZOOM caches have not been generated yet, AJAX-ZOOM will process the images on-the-fly. Latest, when they appear at the frontend. However, if you have thousands of images, it is a good idea to batch process all existing images, which you plan to show over AJAX-ZOOM, before launching the new website or before enabling AJAX-ZOOM at frontend.' mod='ajaxzoom'}
</div>

<div class="row" style="position: relative;" id="az_batch_row">
    <div id="az_batch_act_360_parent" style="display: none">
        {l s='Activate 360 / 3D, when cache for it is done' mod='ajaxzoom'}
        - <input type="checkbox" value="1" id="az_batch_act_360" checked="checked">
    </div>
    <div id="az_batchtool_parent">
        <div style="padding: 10px">Loading, please wait...</div>
    </div>
</div>

<script type="text/javascript">
jQuery(function ($) {
   var base_uri = '{$base_uri|escape:'html':'UTF-8'}';
   var base_url = '{$base_url|escape:'html':'UTF-8'}';
   var token = '{$token|escape:'html':'UTF-8'}';
   var ver = '{$ps_version|escape:'html':'UTF-8'}';
   ver = parseFloat(ver);

   var top_adjust = 80;
   if (ver == 1.5) {
       top_adjust = 75;
   } else if (ver == 1.6) {
       top_adjust = 80;
   } else if (ver == 1.7) {
       top_adjust = 20;
   }

   $('#az_batch_row')
   .append('<div class="az_loading" style="position: absolute; left: 0; top: 0; width: 100%; height: 100%"></div>');

    var frameSrc = base_uri + 'modules/ajaxzoom/axZm/zoomBatch.php';
        frameSrc += '?batch_start=1';
        frameSrc += '&token='+token;

    var wh = function()
    {
        var o = $('#az_batchtool').offset();
        if (o) {
            $('#az_batchtool').height($(window).height() - o.top - top_adjust);
        }
    };

    var loadBatchTool = function()
    {
        var frame = $('<iframe style="width: 100%; height: '
        + ($(window).height() - $('#az_batchtool_parent').offset().top - top_adjust) 
        + 'px; border: 0; margin-top: 20px;" id="az_batchtool" src=""></iframe>')
        .attr('src', frameSrc)
        .on('load', function() {
            $('.az_loading').fadeOut(100, function(){ 
                $(this).remove();
            });

            $('#az_batch_act_360_parent').css('display', '');
        });

        $('#az_batchtool_parent').empty().append(frame);
    };

    $(document).ready(function() {
        loadBatchTool();
        wh();
    } );

    $(window).on('resize', wh);

    // Batch tool callback
    window.afterBatchFolderEndJsClb = function(data) {
        if (!$('#az_batch_act_360').is(':checked')) {
            return;
        }

        if ($.isPlainObject(data) && data.picDir) {
            var dta = data.dirTreeArray;
            var key = data.key;

            if (!$.isPlainObject(dta) || !key || !$.isPlainObject(dta[key])) {
                return;
            } else {
                var keyObj = dta[key];
            }

            if (keyObj.DIR_SUB !== 0) {
                return;
            }

            var path = data.picDir.split('/img/p/360/');
            if (!path[1]) {
                return;
            }

            // path left after /img/p/360
            path = path[1];
            if (path.slice(-1) == '/') {
                path = path.slice(0, -1);
            }

            var pp = path.split('/');
            if (pp.length != 3) {
                return;
            }

            var id_product = parseInt(pp[0]);
            var id_360 = parseInt(pp[1]);
            var idSet = parseInt(pp[2]);

            var lastKeyVal = parseInt(key.split('_').reverse()[0]);
            if (!lastKeyVal) {
                return;
            }

            var nextKey = key.slice(0, -(lastKeyVal + '').length) + ((lastKeyVal + 1) + '');
            if (dta[nextKey]) {
                // not last subfolder (3D not ready)
                return;
            }

            // Activate
            $.ajax( {
                url: base_uri+'index.php',
                type: 'GET',
                data: {
                    'action': 'set360Status',
                    'controller': 'image360',
                    'module': 'ajaxzoom',
                    'fc': 'module',
                    'ajax': 1,
                    'id_product': id_product,
                    'id_360': id_360,
                    'status': 1,
                    'token': token
                },
                success: function(d) {
                    d = JSON.parse(d);
                    if (window.showSuccessMessage) {
                        showSuccessMessage(d.confirmations);
                    }
                }
            } );
        }
    };
});
</script>
