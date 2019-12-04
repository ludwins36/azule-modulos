{*
*  Module: jQuery AJAX-ZOOM for Prestashop, tab-pictures.tpl
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

<div class="panel product-tab az_container">
    <div class="panel-heading tab headingRight" id="az_panel_pictures">
        {l s='Add clickable hotspots to product pictures' mod='ajaxzoom'}
    </div>
    <div class="panel-body">

        <div class="row">
            {if $ps_version >= 1.6}
            <div class="alert alert-info" style="display: block">
            {else}
            <div class="hint marginLeft260" style="display: block">
            {/if}
            {l s='You can add hotspots to you normal product pictures. ' mod='ajaxzoom'}
            {l s='The image file itself will be not changed! ' mod='ajaxzoom'}
            {l s='At front view, your users will see the hotspots at full screen or Fancybox. ' mod='ajaxzoom'}
            {l s='For immediate visibility, activate axZmMode option in AJAX-ZOOM module settings. ' mod='ajaxzoom'}
            </div>
        </div>

        <br><br>

        <div class="row">
            <a href="javascript:void(0)" class="btn btn-default button btn-action" 
                style="margin-left: 5px; margin-bottom: 5px;" id="az_refresh_pictures_list">
                <i class="fa fa-refresh"></i> {l s='Refresh list' mod='ajaxzoom'}
            </a>
            <table class="table tableDnD" style="width: 100%;" id="az_picturesTable">
                <thead>
                    <tr class="nodrag nodrop">
                        <th class="fixed-width-lg"><span class="title_box"></span></th>
                        <th><span class="title_box">{l s='Filename' mod='ajaxzoom'}</span></th>
                        <th><span class="title_box">{l s='Active' mod='ajaxzoom'}</span></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="az_picturesTableRows">
                </tbody>
            </table>

            <table style="display: none;">
                <tbody id="az_lineSetPictures">
                    <tr id="az_picture_line_id">
                        <td class="az_tbl_picture_img">
                            <img picture_src 
                                class="img-thumbnail" 
                                style="max-width: 100px; cursor: pointer;">
                        </td>
                        <td class="az_tbl_vid_name" style="word-break: break-all;">
                            picture_name
                        </td>
                        <td>
                            <span class="switch prestashop-switch fixed-width-lg hide_class az_switch_status_picture">
                                <input type="radio" name="status_field" id="status_field_on" value="1" checked_on />
                                <label class="t" for="status_field_on">{l s='Yes' mod='ajaxzoom'}</label>
                                <input type="radio" name="status_field" id="status_field_off" value="0" checked_off />
                                <label class="t" for="status_field_off">{l s='No' mod='ajaxzoom'}</label>
                                <a class="slide-button btn"></a>
                            </span>
                        </td>
                        <td>
                            <a href="#" class="az_edit_picture_hotspots pull-right btn btn-default button btn-action" 
                                style="margin-left: 5px; margin-bottom: 5px;">
                                <i class="fa fa-map-marker"></i> {l s='Edit hotspots' mod='ajaxzoom'}
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
/*!
*  @author         AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright      2010-2018 AJAX-ZOOM, Vadim Jacobi
*  @license        http://www.ajax-zoom.com/index.php?cid=download
*/

jQuery(function($) {
    var az_base_url = '{$base_url|escape:'html':'UTF-8'}';
    var az_base_uri = '{$base_uri|escape:'html':'UTF-8'}';
    var az_base_dir = '{$base_dir|escape:'html':'UTF-8'}';
    var id_product = '{$id_product|escape:'html':'UTF-8'}';
    var az_pictures_lst = {$az_pictures|@json_encode nofilter};

    var az_lang_msg_pictures_refresh = "{l s='Pictures list has been refreshed' mod='ajaxzoom'}";
    var az_lang_hotspots_enabled = "{l s='Hotspots for this image have been enabled' mod='ajaxzoom'}";
    var az_lang_hotspots_disabled = "{l s='Hotspots for this image have been disabled' mod='ajaxzoom'}";

    window.az_refresh_pictures_list = function(msg) { 
        $('#az_picturesTableRows td').css('backgroundColor', '#f4f8fb');
        doAdminAjax360( { 
            "action": "refreshPicturesList",
            "id_product": {$id_product|escape:'html':'UTF-8'},
            "fc": "module",
            "module": "ajaxzoom",
            "controller": "image360",
            "ajax" : 1 
        }, function(data) {
            $('#az_picturesTableRows').empty();
            az_pictures_lst = JSON.parse(data);
            $.each(az_pictures_lst, function(k, v) {
                setLinePicture(v);
            } );

            if (msg) {
                showSuccessMessage(msg);
            }
        } );
    };

    function setLinePicture(dta)
    {
        line = $("#az_lineSetPictures").html();
        line = line.replace(/az_picture_line_id/g, 'az_picture_line_'+dta.id);
        line = line.replace(/picture_name/g, dta.name);
        line = line.replace(/picture_src/g, 'src="'+dta.thumb+'" ');
        if (dta.hotspots) {
            if (dta.active) {
                line = line.replace(/checked_on/g, 'checked');
                line = line.replace(/checked_off/g, '');
            } else {
                line = line.replace(/checked_on/g, '');
                line = line.replace(/checked_off/g, 'checked');
            }

            line = line.replace(/status_field/g, 'picture_status_' + dta.id);
        } else {
            line = $(line);
            $('.az_switch_status_picture', line).replaceWith('-');
        }

        $("#az_picturesTableRows").append(line);
    }

    $.each(az_pictures_lst, function(k, v) { 
        setLinePicture(v);
    } );

    $('#az_refresh_pictures_list')
    .bind('click', function(e) { 
        $(this).blur();
        window.az_refresh_pictures_list(az_lang_msg_pictures_refresh);
    } );

    $('body').on('click', '.az_edit_picture_hotspots', function(e) { 
        e.preventDefault();
        $(this).blur();

        var id_media = $(this).closest('tr').attr('id').replace('az_picture_line_', '');

        id_media = parseInt(id_media);

        var qstr = 'token=' + window.az_token;
        qstr += '&id_product=' + id_product;
        qstr += '&id_media=' + id_media;
        qstr += '&image_path=' + az_pictures_lst[id_media]['path'];

        $.openAjaxZoomInFancyBox( { 
            href: az_base_url + 'modules/ajaxzoom/preview/hotspoteditor.php?' + qstr,
            iframe: true,
            scrolling: 1,
            boxOnClosed: window.az_refresh_pictures_list
        } );
    } );
    

    // deactivate hotspots
    $('body').on('change', '.az_switch_status_picture input', function(e) {
        e.preventDefault();
        var status = $(this).val();
        var id = $(this).closest('tr').attr('id').replace('az_picture_line_', '');

        doAdminAjax360({
            "action": "setHotspotStatus",
            "id_media": id,
            "status": status,
            "id_product": id_product,
            "token": window.az_token,
            "fc": "module",
            "module": "ajaxzoom",
            "controller": "image360",
            "ajax" : 1
        }, function(data) {
            data = JSON.parse(data);
            if (data.status == 1) {
                showSuccessMessage(az_lang_hotspots_enabled);
            } else {
                showSuccessMessage(az_lang_hotspots_disabled);
            }
        } );
    } );
} );
</script>
