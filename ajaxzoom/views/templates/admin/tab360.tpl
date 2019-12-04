{*
*  Module: jQuery AJAX-ZOOM for Prestashop, tab360.tpl
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

{if isset($id_product) && isset($product)}

<input type="hidden" name="legend_777" id="legend_777" value="" />

<!-- 360 images upload and list -->
<div id="product-images360" class="panel product-tab az_container" style="display: none">
    <div class="panel-heading tab" id="az_panel_images360">{l s='Images 360' mod='ajaxzoom'}</div>
    <div class="panel-body">
        <input type="hidden" name="submitted_tabs[]" value="Images360" />
        <div class="row">
            <div class="form-group clearfix" style="margin-bottom: 5px;">
                <label class="control-label col-lg-3 file_upload_label">
                    <span class="label-tooltip" 
                        data-toggle="tooltip" 
                        title="{l s='Format:' mod='ajaxzoom'} JPG, GIF, PNG. 
                        {l s='Filesize:' mod='ajaxzoom'} 
                        {$max_image_size|string_format:"%.2f"|escape:'htmlall':'UTF-8'} 
                        {l s='MB max.' mod='ajaxzoom'}">
                        {if isset($id_image)}{l s='Edit this product\'s image:' mod='ajaxzoom'}
                        {else}
                        {l s='Add a new image to this image set' mod='ajaxzoom'}
                        {/if}"
                    </span>
                </label>
                <div class="col-lg-9">
                    {$image_uploader} {* this output provided by native HelperImageUploader->render() function *}
                </div>
            </div>
        </div>
        <table class="table tableDnD" id="imageTable360">
            <thead>
                <tr class="nodrag nodrop">
                    <th class="fixed-width-lg"><span class="title_box">{l s='Image' mod='ajaxzoom'}</span></th>
                    <th></th> <!-- action -->
                </tr>
            </thead>
            <tbody id="imageList360">
            </tbody>
        </table>
        <table id="lineType360" style="display: none;">
            <tr id="image_id">
                <td>
                    <img src="{$base_uri|escape:'html':'UTF-8'}modules/ajaxzoom/views/img/image_path.gif" 
                        alt="legend" title="legend" class="img-thumbnail" />
                </td>
                <td>
                    <a href="#" class="delete_product_image360 pull-right btn btn-default button btn-action">
                        <i class="fa fa-trash-o"></i> {l s='Delete this image' mod='ajaxzoom'}
                    </a>
                </td>
            </tr>
        </table>
        <div class="panel-footer">
            <a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}" class="btn btn-default button btn-action">
                <i class="process-icon-cancel"></i> {l s='Cancel' mod='ajaxzoom'}
            </a>
            <a href="javascript:void(0)" class="btn btn-default button btn-action" id="az_close_upload">
                <i class="process-icon-cancel"></i> {l s='Close' mod='ajaxzoom'}
            </a>
        </div>
    </div>
</div>

<script type="text/javascript">
/*!
*  @author         AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright      2010-2018 AJAX-ZOOM, Vadim Jacobi
*  @license        http://www.ajax-zoom.com/index.php?cid=download
*/

var hookGetUrlParam = function(name, url) { 
    if (!url) {
        url = window.location.href;
    } 

    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(url);
    if (!results) { 
        return undefined;
    } 

    return results[1] || undefined;
};

$(document)
.bind('ajaxComplete.azHook', function(event, xhr, settings) {
    var action = '';
    var dataString = '';

    if (typeof settings.data == 'string' && settings.data.indexOf('action') != -1) { 
        dataString = '?'+settings.data;
    } else if (typeof settings.url == 'string' && settings.url.indexOf('action') != -1) { 
        dataString = settings.url;
    } else {
        return;
    }

    action = hookGetUrlParam('action', dataString);

    if (action == 'ModuleAjaxzoom') {
        {if $ps_version < 1.6}
        window.az_pannel_toggle();
        {/if}
    }

    // removed image
    if (action == 'deleteProductImage') {
        var id_image = hookGetUrlParam('id_image', dataString);
        doAdminAjax360( { 
            "action": "clearAzImageCache",
            "deletedImgID": id_image,
            "token": window.az_token,
            "fc": "module",
            "module": "ajaxzoom",
            "controller": "image360",
            "ajax": 1
        } );

        window.az_refresh_pictures_list();
    } else if (action == 'addProductImage') {
        window.az_refresh_pictures_list();
    }
} );

{literal}
function imageLine360(id, path, position, cover, shops, legend) {
    var line = $("#lineType360").html();
    line = line.replace(/image_id/g, id);
    line = line.replace(/<img[^>]*>/, '<img src="'+path+'" alt="legend" title="legend" class="img-thumbnail">');
    line = line.replace(/icon-check-empty/g, cover);
    line = line.replace(/<tbody>/gi, "");
    line = line.replace(/<\/tbody>/gi, "");
    $("#imageList360").append(line);
}

function doAdminAjax360(data, success_func, error_func) {
    $.ajax( {
        url: '{/literal}{$base_dir|escape:'htmlall':'UTF-8'}{literal}index.php',
        data: data,
        type: 'GET',
        success: function(data) {
            if (success_func) {
                return success_func(data);
            }

            data = $.parseJSON(data);
            if (data.confirmations.length != 0) {
                showSuccessMessage(data.confirmations);
            } else {
                showErrorMessage(data.error);
            }
        },
        error: function(data) {
            if (error_func) {
                return error_func(data);
            }

            alert("[TECHNICAL ERROR]");
        }
    } );
}

$(document).ready(function() {

    $('body').on('click', '#az_close_upload', function(e) {
        $('#product-images360').css('display', 'none');
    } );

    $('body').on('click', '.delete_product_image360', function(e) {
        e.preventDefault();
        id = $(this).parent().parent().attr('id');
        var id_360set = $('#legend_777').val();
        var ext = $(this).parent().parent().find('img').attr('src').split('.').pop();
        if (confirm("{/literal}{l s='Are you sure?' js=1 mod='ajaxzoom'}{literal}"))

        doAdminAjax360( {
            "action": "deleteProductImage360",
            "id_image": id,
            'id_360set': id_360set,
            "ext": ext,
            "id_product": {/literal}{$id_product|escape:'htmlall':'UTF-8'}{literal},
            "id_category": '',
            "token": window.az_token,
            //"tab": "AdminProducts",
            "fc": "module",
            "module": "ajaxzoom",
            "controller": "image360",
            "ajax": 1 
        }, function(data) {
            data = $.parseJSON(data);
            if (data) {
                id = data.content.id;
                if (data.status == 'ok') {
                    $("#" + id).remove();
                }

                $("#countImage360").html(parseInt($("#countImage360").html()) - 1);
                //refreshImagePositions($("#imageTable360"));
                showSuccessMessage(data.confirmations);
            }
        } );
    } );
} );

{/literal}
</script>
{/if}
