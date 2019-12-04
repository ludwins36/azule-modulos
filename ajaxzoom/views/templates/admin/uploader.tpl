{*
*  Module: jQuery AJAX-ZOOM for Prestashop, uploader.tpl
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
*  @copyright 2010-2017 AJAX-ZOOM, Vadim Jacobi
*  @license   http://www.ajax-zoom.com/index.php?cid=download
*}

<div class="file-upload-wrapper az_container">
    <div id="file-uploader-360">
        <noscript>
            <p>{l s='Please enable JavaScript to use file uploader:' mod='ajaxzoom'}</p>
        </noscript>
    </div>
    <div id="progressBarImage" class="progressBarImage"></div>
    <div id="showCounter" style="display: none;"><span id="imageUpload">0</span><span id="imageTotal">0</span></div>
    <p class="preference_description" style="clear: both;">
        {l s='Format:' mod='ajaxzoom'} JPG, GIF, PNG. 
        {l s='Filesize:' mod='ajaxzoom'} 
        {$max_image_size|string_format:"%.2f"|escape:'htmlall':'UTF-8'} 
        {l s='MB max.' mod='ajaxzoom'}
    </p>
</div>

<script type="text/javascript">
/*!
*  @author         AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright      2010-2018 AJAX-ZOOM, Vadim Jacobi
*  @license        http://www.ajax-zoom.com/index.php?cid=download
*/

var filecheck = 1;
var upbutton = "{l s='Upload an image' mod='ajaxzoom'}";
var come_from = "{$table|escape:'htmlall':'UTF-8'}";
var success_add = "{l s='image has been successfully added' mod='ajaxzoom'}";
var id_tmp = 0;
var current_shop_id = {$current_shop_id|intval};

{literal}

var uploader = new qq.FileUploader( {
    element: document.getElementById("file-uploader-360"),
    action: "{/literal}{$base_dir|escape:'htmlall':'UTF-8'}{literal}/index.php",
    debug: false,
    params: {
        id_product: {/literal}{$id_product|escape:'htmlall':'UTF-8'}{literal},
        id_360set: $('#legend_777').val(),
        token: "{/literal}{$token|escape:'htmlall':'UTF-8'}{literal}",
        tab: "AdminProducts",
        action: 'addProductImage360',
        fc: "module",
        module: "ajaxzoom",
        controller: "image360",
        ajax: 1
    },
    onComplete: function(id, fileName, responseJSON) {
        // set image
        $tr = $('tr[data-group=' + responseJSON.id_360 + ']');
        if($tr.find('img[src*=no_image-100x100]')) {
            $tr.find('img[src*=no_image-100x100]').attr('src', responseJSON.path);
        }

        var percent = ((filecheck * 100) / nbfile);
        $("#progressBarImage").progressbar({value: percent});

        if (percent != 100) {
            $("#imageUpload").html(parseInt(filecheck));
            $("#imageTotal").html(" / " + parseInt(nbfile) + " {/literal}{l s='Images'}{literal}");
            $("#progressBarImage").show();
            $("#showCounter").show();
        } else {
            $("#progressBarImage").progressbar({value: 0});
            $("#progressBarImage").hide();
            $("#showCounter").hide();
            nbfile = 0;
            filecheck = 0;
        }

        if (responseJSON.status == 'ok') {
            cover = "forbbiden";
            if (responseJSON.cover == "1") {
                cover = "enabled";
            }

            imageLine360(responseJSON.id, responseJSON.path, responseJSON.position, cover, responseJSON.shops, '')
            $("#imageTable tr:last").after(responseJSON.html);
            $("#countImage").html(parseInt($("#countImage").html()) + 1);
            $("#img" + id).remove();
            $("#imageTable").tableDnDUpdate();
            showSuccessMessage(responseJSON.name + " " + success_add);
        } else {
            showErrorMessage(responseJSON.error);
        }

        filecheck++;
    },
    onSubmit: function(id, filename) {
        $(this)[0].params.id_360set = $('#legend_777').val();

        $("#imageTable").show();
        $("#listImage").append("<li id='img"+id+"'><div class=\"float\" >" + filename + "</div></div><a style=\"margin-left:10px\"href=\"javascript:delQueue(" + id +");\"><img src=\"../img/admin/disabled.gif\" alt=\"\" border=\"0\"></a><p class=\"errorImg\"></p></li>");
    }
});
</script>
{/literal}