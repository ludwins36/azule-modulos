{*
*  Module: jQuery AJAX-ZOOM for Prestashop, tab360-sets.tpl
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

<div id="product-images360sets" class="panel product-tab az_container">
    <div class="panel-heading tab headingRight" id="az_panel_azSets">{l s='360/3D Views' mod='ajaxzoom'}</div>
    <div class="panel-body">
        <div class="row paddingLeft260">
            <a class="btn bt-icon button btn-block btn-success" id="az_link_add_360" href="#">
                <i class="icon-plus"></i> {l s='Add a new 360/3D view' mod='ajaxzoom'}
            </a>
        </div>

        <div class="row" id="az_newForm" style="display:none; margin-top: 15px;">
            <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                <label class="control-label col-lg-3">
                    <span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Please enter any name' mod='ajaxzoom'}">
                        {l s='Name' mod='ajaxzoom'}
                    </span>
                </label>
                <div class="col-lg-3">
                    <input type="text" id="az_name" name="az_name" value="" class="form-control" /> 
                </div>
                {if $ps_version < 1.6}
                    <div class="tooltipReplacement">{l s='Please enter any name' mod='ajaxzoom'}</div>
                {/if}
            </div>

            <div id="az_existing_wrapper" style="width: 100%; display: {if $sets_groups}block{else}none{/if};">
                <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                    <label class="control-label col-lg-3">
                    </label>
                    <div class="col-lg-2">
                        <div>{l s='OR' mod='ajaxzoom'}</div>
                    </div>
                </div>
                <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                    <label class="control-label col-lg-3">
                        <span class="label-tooltip" data-toggle="tooltip" 
                            data-original-title="{l s='You should not select anything here unless you want to create 3D (not 360) which contains more than one row!' mod='ajaxzoom'}">
                            {l s='Add to existing 3D as next row' mod='ajaxzoom'}
                        </span>
                    </label>
                    <div class="col-lg-3">
                        <select name="az_existing" id="az_existing" class="form-control">
                            <option value="" style="min-width: 100px">{l s='Select' mod='ajaxzoom'}</option>
                            {if $sets_groups}
                            {foreach from=$sets_groups item=group}
                            <option value="{$group.id_360|escape:'htmlall':'UTF-8'}">{$group.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                            {/if}
                        </select>

                    </div>
                </div>
                {if $ps_version < 1.6}
                    <div class="tooltipReplacement">{l s='You should not select anything here unless you want to create 3D (not 360) which contains more than one row!' mod='ajaxzoom'}</div>
                {/if}
            </div>

            <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                <label class="control-label col-lg-3">
                    <span class="label-tooltip" data-toggle="tooltip" 
                        data-original-title="{l s='This is the most easy and quick way of adding 360 views to your product! Upload over FTP your 360\'s zipped (each images set in one zip file) to /modules/ajaxzoom/zip/ directory. You can also upload a folder with your 360 images. After you did so these zip files / folder names will instantly appear in the select field below. All you have to do then is select one of the zip files / folder names and press \'add\' button. Images from the selected zip file / folder will be instantly imported.' mod='ajaxzoom'}">
                        {l s='Add images from ZIP archive or folder' mod='ajaxzoom'}
                    </span>
                </label>
                <div class="col-lg-3">
                    <input type="checkbox" id="az_zip" name="az_zip" value="1" {if !$files}disabled readonly{/if} />
                </div>
                {if $ps_version < 1.6}
                    <div class="tooltipReplacement">
                        {l s='This is the most easy and quick way of adding 360 views to your product! Upload over FTP your 360\'s zipped (each images set in one zip file) to /modules/ajaxzoom/zip/ directory. You can also upload a folder with your 360 images. After you did so these zip files / folder names will instantly appear in the select field below. All you have to do then is select one of the zip files / folder names and press \'add\' button. Images from the selected zip file / folder will be instantly imported.' mod='ajaxzoom'}
                    </div>
                {/if}
            </div>
            <div class="form-group az_field-arcfile {if $ps_version >= 1.6}clearfix{/if}" style="display: none;">
                <label class="control-label col-lg-3">
                    <span class="label-tooltip">
                        {l s='Select ZIP archive or folder' mod='ajaxzoom'}
                    </span>
                </label>
                <div class="col-lg-3">
                    <select name="az_arcfile" id="az_arcfile" class="form-control">
                        <option value="">{l s='Select' mod='ajaxzoom'}</option>
                        {foreach from=$files item=file}
                        <option value="{$file|escape:'htmlall':'UTF-8'}">{$file|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="form-group az_field-arcfile {if $ps_version >= 1.6}clearfix{/if}" style="display: none;">
                <label class="control-label col-lg-3">
                    <span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Delete Zip/Dir after import' mod='ajaxzoom'}">
                        {l s='Delete Zip/Dir after import' mod='ajaxzoom'}
                    </span>
                </label>
                <div class="col-lg-3">
                    <input type="checkbox" id="az_delete" name="az_delete" value="1" />
                </div>
                {if $ps_version < 1.6}
                    <div class="tooltipReplacement">
                        {l s='Delete Zip/Dir after import' mod='ajaxzoom'}
                    </div>
                {/if}
            </div>

            <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                <div class="col-lg-3"></div>
                <div class="col-lg-9">
                    <a href="#" class="save_set btn btn-default button btn-action">
                        <i class="fa fa-floppy-o"></i> {l s='Add' mod='ajaxzoom'}
                    </a>
                </div>
            </div>
        </div>
        <br><br>

        <div class="row">
            <table class="table tableDnD" id="az_imageTableSets" >
                <thead>
                    <tr class="nodrag nodrop">
                        <th class="fixed-width-lg"><span class="title_box">{l s='Cover Image' mod='ajaxzoom'}</span></th>
                        <th><span class="title_box">{l s='Name' mod='ajaxzoom'}</span></th>
                        <th><span class="title_box">{l s='Active' mod='ajaxzoom'}</span></th>
                        <th></th> <!-- action -->
                    </tr>
                </thead>
                <tbody id="az_imageTableSetsRows">
                </tbody>
            </table>

            <table id="az_lineSet360" style="display: none;">
                <tr id="az_360_set_set_id" data-group="group_id">
                    <td><img src="../modules/ajaxzoom/views/img/image_path.gif" alt="legend" title="legend" class="img-thumbnail" /></td>
                    <td>{l s='legend' mod='ajaxzoom'}</td>
                    <td>
                        <span class="switch prestashop-switch fixed-width-lg hide_class az_switch_status_360">
                            <input type="radio" name="status_field" id="status_field_on" value="1" checked_on />
                            <label class="t" for="status_field_on">{l s='Yes' mod='ajaxzoom'}</label>
                            <input type="radio" name="status_field" id="status_field_off" value="0" checked_off />
                            <label class="t" for="status_field_off">{l s='No' mod='ajaxzoom'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </td>
                    <td>
                        <a href="#" class="az_delete_set pull-right btn btn-default button btn-action" style="margin-left: 5px; margin-bottom: 5px;">
                            <i class="fa fa-trash-o"></i> {l s='Delete' mod='ajaxzoom'}
                        </a>
                        <a href="#" class="az_iframe_set pull-right btn btn-default hide_class button btn-action" style="margin-left: 5px; margin-bottom: 5px;">
                            <i class="fa fa-briefcase"></i> {l s='Iframe' mod='ajaxzoom'}
                        </a>
                        <a href="#" class="az_crop_set pull-right btn btn-default hide_class button btn-action" style="margin-left: 5px; margin-bottom: 5px;">
                            <i class="fa fa-sitemap"></i> {l s='360° Product Tour' mod='ajaxzoom'}
                        </a>
                        <a href="#" class="az_hotspot_set pull-right btn btn-default hide_class button btn-action" style="margin-left: 5px; margin-bottom: 5px;">
                            <i class="fa fa-map-marker"></i> {l s='Hotspots' mod='ajaxzoom'}
                        </a>
                        <a href="#" class="az_images_set pull-right btn btn-default button btn-action" style="margin-left: 5px; margin-bottom: 5px;">
                            <i class="fa fa-picture-o"></i> {l s='Images' mod='ajaxzoom'}
                        </a>
                        <a href="#" class="az_preview_set pull-right btn btn-default hide_class button btn-action" style="margin-left: 5px; margin-bottom: 5px;">
                            <i class="fa fa-eye"></i> {l s='Preview' mod='ajaxzoom'}
                        </a>
                        <a href="#" class="az_settings_set pull-right btn btn-default hide_class button btn-action" style="margin-left: 5px; margin-bottom: 5px;">
                            <i class="fa fa-cog"></i> {l s='Settings' mod='ajaxzoom'}
                        </a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
{literal}
/*!
*  @author      AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright   2010-2018 AJAX-ZOOM, Vadim Jacobi
*  @license     http://www.ajax-zoom.com/index.php?cid=download
*/

if ($.isFunction($.fn.tooltip)){
    $('.label-tooltip', $('#az_newForm')).tooltip();
}

var base_url = '{/literal}{$base_url|escape:'html':'UTF-8'}{literal}';
var base_uri = '{/literal}{$base_uri|escape:'html':'UTF-8'}{literal}';
var id_product = '{/literal}{$id_product|escape:'html':'UTF-8'}{literal}';

function setLine(id, path, position, legend, status, group_id)
{
    if (path && path.indexOf('azImg360') != -1) {
        path += '&_='+(new Date()).getTime();
    }

    line = $("#az_lineSet360").html();
    line = line.replace(/set_id/g, id);
    line = line.replace(/group_id/g, group_id);
    line = line.replace(/legend/g, legend);
    line = line.replace(/status_field/g, 'status_' + id);
    line = line.replace(/\.\.\/modules\/ajaxzoom\/views\/img\/image_path\.gif/g, path);
    line = line.replace(/<tbody>/gi, "");
    line = line.replace(/<\/tbody>/gi, "");

    if (status == '1') {
        line = line.replace(/checked_on/g, 'checked');
        line = line.replace(/checked_off/g, '');
    } else {
        line = line.replace(/checked_on/g, '');
        line = line.replace(/checked_off/g, 'checked');
    }

    if ($('tr[data-group=' + group_id + ']').length) {
        line = line.replace(/hide_class/g, 'hide');
    }

    $("#az_imageTableSetsRows").append(line);
}

function afterUpdateStatus(data) {
    data = $.parseJSON(data);
    showSuccessMessage(data.confirmations);
}

function manageExistingVisibility() {
    if ($('select#az_existing option').length > 1) {
        $('#az_existing_wrapper').css('display', 'block');
    } else {
        $('#az_existing_wrapper').css('display', 'none');
    }
}

function afterAddSet(data) {
    var data = $.parseJSON(data);

    if(data.sets.length > 0) {
        for (var i = 0; i < data.sets.length; i++) {
            var set = data.sets[i];
            if (!set.id_360) {
                set.id_360 = data.sets[0].id_360;
            }

            setLine(set.id_360set, set.path, "", set.name, set.status, set.id_360);
        };
    } else {
        setLine(data.id_360set, data.path, "", data.name, data.status, data.id_360);
    }

    $('#az_link_add_360').find('i').removeClass('icon-minus').addClass('icon-plus');
    $('#az_newForm').hide();
    $('#az_name').val('');
    $('#az_existing').val('');

    if (data.new_id != '') {
        $('select#id_360')
        .append($("<option></option>")
        .attr("value", data.new_id)
        .attr('data-settings', data.new_settings)
        .attr('data-combinations', '[]')
        .text(data.new_name)); 

        $('select#az_existing').append($("<option></option>").attr("value", data.new_id).text(data.new_name)); 
        manageExistingVisibility();
    }

    showSuccessMessage(data.confirmations);
}

function afterGetImages(data) {
    var data = $.parseJSON(data);

    if ($('#az_panel_images360').next().css('display') == 'none') {
        $('#az_panel_images360').trigger('click');
    }

    for (var i = 0; i < data.images.length; i++) {
        imageLine360(data.images[i]['id'], data.images[i]['thumb'], '', "", "", "");
    };

    var pppPos = $('#product-images360').offset();

    if ($.scrollTo && pppPos && pppPos.top){
        $.scrollTo(pppPos.top - 100);
    }
}

$(document).ready(function() {

    $('#az_zip').change(function() {
        if($(this).is(':checked')) {
            $('.az_field-arcfile').css('display', '');
        } else {
            $('.az_field-arcfile').hide();
        }
    });

    $('#az_link_add_360').click(function(e) {
        e.preventDefault();

        var icon = $(this).find('i');

        if(icon.hasClass('icon-plus')) {
            icon.removeClass('icon-plus').addClass('icon-minus');
            $('#az_newForm').show();
        } else {
            icon.removeClass('icon-minus').addClass('icon-plus');
            $('#az_newForm').hide();
        }
    } );

    $('body').on('change', '.az_switch_status_360 input', function(e) {
        e.preventDefault();
        var status = $(this).val();
        var group_id = $(this).closest('tr').data('group');

        doAdminAjax360({
            "action": "set360Status",
            "id_product": {/literal}{$id_product|escape:'html':'UTF-8'}{literal},
            "id_360": group_id,
            "status": status,
            "token": window.az_token,
            "fc": "module",
            "module": "ajaxzoom",
            "controller": "image360",
            "ajax" : 1 
        }, afterUpdateStatus);
    } );

    $('body').on('click', '.az_settings_set', function(e) {
        e.preventDefault();

        var id360 = $(this).closest('tr').data('group');
        var id360set = $(this).closest('tr').attr('id').replace('az_360_set_', '');

        $('#id_360').val(id360).trigger('change');

        if ($('#az_panel_az360Sets').next().css('display') == 'none') {
            $('#az_panel_az360Sets').trigger('click');
        }
    } );

    $('body').on('click', '.az_preview_set', function(e) {
        e.preventDefault();

        var id360 = $(this).closest('tr').data('group');
        var id360set = $(this).closest('tr').attr('id').replace('az_360_set_', '');
        var qty = $('tr[data-group=' + id360 + ']').length;
        var _3dDir = base_uri + 'img/p/360/' + id_product + '/' + id360;
        if (qty < 2) {
            _3dDir += '/' + id360set;
        }

        $.openAjaxZoomInFancyBox({href: base_url+'modules/ajaxzoom/preview/preview.php?3dDir='+_3dDir+'&group='+id360+'&id='+id360set, iframe: true});
    } );

    $('body').on('click', '.az_crop_set', function(e) {
        e.preventDefault();

        var id360 = $(this).closest('tr').data('group');
        var id360set = $(this).closest('tr').attr('id').replace('az_360_set_', '');
        var qty = $('tr[data-group=' + id360 + ']').length;
        var _3dDir = base_uri + 'img/p/360/' + id_product + '/' + id360;
        if (qty < 2) {
            _3dDir += '/' + id360set;
        }

        $.openAjaxZoomInFancyBox({
            href: base_url + 'modules/ajaxzoom/preview/cropeditor.php?token=' + window.az_token + '&3dDir=' + _3dDir + '&group='+id360+'&id='+id360set,
            iframe: true,
            scrolling: 1
        });
    } );

    $('body').on('click', '.az_hotspot_set', function(e) {
        e.preventDefault();

        var id360 = $(this).closest('tr').data('group');
        var id360set = $(this).closest('tr').attr('id').replace('az_360_set_', '');
        var qty = $('tr[data-group=' + id360 + ']').length;
        var _3dDir = base_uri + 'img/p/360/' + id_product + '/' + id360;
        if (qty < 2) {
            _3dDir += '/' + id360set;
        }

        $.openAjaxZoomInFancyBox({
            href: base_url + 'modules/ajaxzoom/preview/hotspoteditor.php?token=' + window.az_token + '&3dDir=' + _3dDir + '&group='+id360+'&id='+id360set,
            iframe: true,
            scrolling: 1
        });

    } );

    $('body').on('click', '.az_iframe_set', function(e) {
        e.preventDefault();

        var id360 = $(this).closest('tr').data('group');
        var id360set = $(this).closest('tr').attr('id').replace('az_360_set_', '');
        var iframe_link = base_uri + 'modules/ajaxzoom/preview/iframe.php?id_360=' + id360 + '&cropsliderposition=left';
        var iframe_embed = '<!-- Copy & paste embed code for CMS or landing pages on '+base_url+' -->\n<iframe src="'+iframe_link+'" width="100%" height="500" frameborder="0" scrolling="no" allowfullscreen></iframe>';

        var _this = $(this);
        var _thisPar = _this.parent();
        var txt_field = '<textarea style="width: 100%; min-height: 100px; margin-top: 5px; display: inline-block;" class="aziframeembedlink form-control">[txt]</textarea>';

        if (!$('.aziframeembedlink', _thisPar).length){
            txt_field = txt_field.replace('[txt]', iframe_embed ); 
            $(txt_field).appendTo(_thisPar);
        } else {
            var txt_field_obj = $('.aziframeembedlink', _thisPar);
            if (txt_field_obj.css('display') == 'none'){
                txt_field_obj.css('display', 'inline-block')
            } else {
                txt_field_obj.css('display', 'none')
            }
        }
    } );

    $('body').on('click', '.az_images_set', function(e) {
        e.preventDefault();

        $('#az_imageTableSetsRows').find('tr').removeClass('az_active');
        $(this).closest('tr').addClass('az_active');
        $('#imageList360').html('');
        $('#file360-success').parent().hide();

        var id = $(this).closest('tr').attr('id').replace('az_360_set_', '');
        doAdminAjax360( {
            "action":"getImages",
            "id_product" : {/literal}{$id_product|escape:'html':'UTF-8'}{literal},
            "id_360set" : id,
            "token" : window.az_token,
            "fc": "module",
            "module": "ajaxzoom",
            "controller": "image360",
            "ajax" : 1 }, afterGetImages
        );

        $('#legend_777').val(id);
        $('#product-images360').show();
    } );

    $('.save_set').click(function(e) {
        e.preventDefault();    
        var arcfileVal = $('#az_arcfile').val();

        doAdminAjax360({
            "action":"addSet",
            "name":$('#az_name').val(),
            "existing":$('#az_existing').val(),
            "zip":$('#az_zip').is(':checked'),
            "delete":$('#az_delete').is(':checked'),
            "arcfile":arcfileVal,
            "id_product" : {/literal}{$id_product|escape:'html':'UTF-8'}{literal},
            "id_category" : '',
            "token": window.az_token,
            //"tab" : "AdminProducts",
            "fc": "module",
            "module": "ajaxzoom",
            "controller": "image360",
            "ajax" : 1 }, afterAddSet
        );

        if ($('#az_zip').is(':checked') && $('#az_delete').is(':checked')) {
            $("#az_arcfile option[value='"+arcfileVal+"']").remove();
            if ($('#az_arcfile option').size() < 2){
                $('#az_zip').trigger('click').attr({disabled: true, readonly: true});
            }
        }
    } );

    $('body').on('click', '.az_delete_set', function(e) {
        e.preventDefault();

        $('#product-images360').hide();
        $('#imageList360').html('');

        var id = $(this).closest('tr').attr('id').replace('az_360_set_', '');
        if (confirm("{/literal}{l s='Are you sure?' js=1}{literal}")) {
            doAdminAjax360( {
                "action": "deleteSet",
                "id_360set": id,
                "id_product": {/literal}{$id_product|escape:'html':'UTF-8'}{literal},
                "id_category": '',
                "token": window.az_token,
                //"tab" : "AdminProducts",
                "fc": "module",
                "module": "ajaxzoom",
                "controller": "image360",
                "ajax": 1 
            }, function(data) {
                var data = $.parseJSON(data);
                $('#az_360_set_' + data.id_360set).remove();

                // remove set option from the dropdowns
                if (data.removed == '1') {
                    $("select#id_360 option[value='" + data.id_360 + "']").remove();
                    $("select#az_existing option[value='" + data.id_360 + "']").remove();
                    manageExistingVisibility();
                }

                showSuccessMessage(data.confirmations);
            });
        }
    } );

    {/literal}
    {foreach from=$sets item=set}
        setLine(
            "{$set.id_360set|escape:'html':'UTF-8'}",
            "{$set.path|escape:'html':'UTF-8'}",
            "",
            "{$set.name|escape:'html':'UTF-8'}",
            "{$set.status|escape:'html':'UTF-8'}",
            "{$set.id_360|escape:'html':'UTF-8'}"
        );
    {/foreach}
    {literal}
});
{/literal}
</script>
