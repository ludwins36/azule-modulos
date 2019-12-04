{*
*  Module: jQuery AJAX-ZOOM for Prestashop, ajax.tpl
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

<div class="form-group az_container">
    <div class="col-lg-12">
        <input id="{$id|escape:'html':'UTF-8'}" type="file" name="{$name|escape:'htmlall':'UTF-8'}[]"{if isset($url)} data-url="{$url|escape:'htmlall':'UTF-8'}"{/if}{if isset($multiple) && $multiple} multiple="multiple"{/if} style="width:0px;height:0px;" />
        <button class="btn btn-action" data-style="expand-right" data-size="s" type="button" id="{$id|escape:'html':'UTF-8'}-add-button">
            <i class="icon-folder-open"></i> {if isset($multiple) && $multiple}{l s='Add files...' mod='ajaxzoom'}{else}{l s='Add file...' mod='ajaxzoom'}{/if}
        </button>
    </div>
</div>

<div class="well" style="display: none">
    <div id="{$id|escape:'html':'UTF-8'}-files-list"></div>
    <button class="ladda-button btn btn-primary" data-style="expand-right" type="button" id="{$id|escape:'html':'UTF-8'}-upload-button" style="display:none;">
        <span class="ladda-label"><i class="icon-check"></i> {if isset($multiple) && $multiple}{l s='Upload files' mod='ajaxzoom'}{else}{l s='Upload file' mod='ajaxzoom'}{/if}</span>
    </button>
</div>

<div class="row" style="display: none">
    <div class="alert alert-success" id="{$id|escape:'html':'UTF-8'}-success"></div>
</div>

<div class="row" style="display: none">
    <div class="alert alert-danger" id="{$id|escape:'html':'UTF-8'}-errors"></div>
</div>

<script type="text/javascript">
/*!
*  @author         AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright      2010-2018 AJAX-ZOOM, Vadim Jacobi
*  @license        http://www.ajax-zoom.com/index.php?cid=download
*/
function humanizeSize(bytes) {
    if (typeof bytes !== 'number') {
        return '';
    }

    if (bytes >= 1000000000) {
        return (bytes / 1000000000).toFixed(2) + ' GB';
    }

    if (bytes >= 1000000) {
        return (bytes / 1000000).toFixed(2) + ' MB';
    }

    return (bytes / 1000).toFixed(2) + ' KB';
}

$( document ).ready(function() {
    var az_sortUploadRows = function() {
        $("#imageList360").append($("#imageList360>tr").get().sort(function(a, b) {
                return $(a).attr("id") > $(b).attr("id") ? 1 : -1;
        } ));
    };

    {if isset($multiple) && isset($max_files)}
        var {$id|escape:'html':'UTF-8'}_max_files = {$max_files - $files|count};
    {/if}

    {if isset($files) && $files}
    $('#{$id|escape:'html':'UTF-8'}-images-thumbnails').parent().show();
    {/if}

    var {$id|escape:'html':'UTF-8'}_upload_button = Ladda.create( document.querySelector('#{$id|escape:'html':'UTF-8'}-upload-button' ));
    var {$id|escape:'html':'UTF-8'}_total_files = 0;
    var nnn = 0;

    $('#{$id|escape:'html':'UTF-8'}').fileupload( {
        dataType: 'json',
        async: true,
        autoUpload: false,
        singleFileUploads: true,
        maxFileSize: {$post_max_size|escape:'htmlall':'UTF-8'},
        start: function (e) {
            {$id|escape:'html':'UTF-8'}_upload_button.start();
            $('#{$id|escape:'html':'UTF-8'}-upload-button').unbind('click'); //Important as we bind it for every elements in add function
            nnn = 0;
        },
        fail: function (e, data) {
            $('#{$id|escape:'html':'UTF-8'}-errors').html(data.errorThrown.message).parent().show();
            $('#{$id|escape:'html':'UTF-8'}-files-list').html('').parent().hide();
        },
        done: function (e, data) {
            if (data.result) {
                if (typeof data.result.{$name|escape:'html':'UTF-8'} !== 'undefined') {
                    for (var i=0; i<data.result.{$name|escape:'html':'UTF-8'}.length; i++) {
                        if (typeof data.result.{$name|escape:'html':'UTF-8'}[i].error !== 'undefined' && data.result.{$name|escape:'html':'UTF-8'}[i].error != '') {
                            $('#{$id|escape:'html':'UTF-8'}-errors')
                            .html('<strong>'+data.result.{$name|escape:'html':'UTF-8'}[i].name+'</strong> : '+data.result.{$name|escape:'html':'UTF-8'}[i].error)
                            .parent().show();

                            $('#{$id|escape:'html':'UTF-8'}-files-list').html('').parent().hide();
                        } else {
                            $(data.context).appendTo($('#{$id|escape:'html':'UTF-8'}-success'));
                            $('#{$id|escape:'html':'UTF-8'}-success').parent().show();

                            if (data.result.{$name|escape:'html':'UTF-8'}[i] !== null && data.result.{$name|escape:'html':'UTF-8'}[i].status == 'ok') {
                                nnn++;
                                var response = data.result.{$name|escape:'html':'UTF-8'}[i];
                                var cover = "icon-check-empty";
                                var legend = '';

                                if (response.cover == "1") {
                                    cover = "icon-check-sign";
                                }

                                if (typeof response.legend !== 'undefined' && response.legend != null) {
                                    legend = response.legend[{$default_language|intval}];
                                }

                                imageLine360(response.id, response.path, response.position, cover, response.shops, legend);
                                $("#countImage").html(parseInt($("#countImage").html()) + 1);
                                $("#img" + id).remove();

                                if ($.isFunction($.fn.tableDnDUpdate)) {
                                    $("#imageTable360").tableDnDUpdate();
                                }

                                $('#legend_1').val('');

                                // check if we need update an image
                                var elem = $('#az_imageTableSetsRows tr#az_360_set_' + response.id_360set + ' img');

                                if (elem.attr('src').search('no_image') != -1) {
                                    if (data.originalFiles && data.originalFiles[0] && response.nameorg) {
                                        if (data.originalFiles[0]['name'] == response.nameorg) {
                                            elem.attr('src', response.path + '&thumbMode=contain');
                                        }
                                    } else {
                                        elem.attr('src', response.path + '&thumbMode=contain');
                                    }
                                }

                                if (data.originalFiles && data.originalFiles.length == nnn) {
                                    az_sortUploadRows();
                                }
                            }
                        }
                    }
                }

                $(data.context).find('button').remove();
            }
        }
    })
    .on('fileuploadalways', function (e, data) {
        {$id|escape:'html':'UTF-8'}_total_files--;

        if ({$id|escape:'html':'UTF-8'}_total_files == 0) {
            {$id|escape:'html':'UTF-8'}_upload_button.stop();
            $('#{$id|escape:'html':'UTF-8'}-upload-button').unbind('click');
            $('#{$id|escape:'html':'UTF-8'}-files-list').parent().hide();
        }
    })
    .on('fileuploadadd', function(e, data) {
        if (typeof {$id|escape:'html':'UTF-8'}_max_files !== 'undefined') {
            if ({$id|escape:'html':'UTF-8'}_total_files >= {$id|escape:'html':'UTF-8'}_max_files) {
                e.preventDefault();
                alert('{l  mod='ajaxzoom' s='You can upload a maximum of %s files'|sprintf:$max_files}');
                return;
            }
        }

        data.context = $('<div/>').addClass('form-group').appendTo($('#{$id|escape:'html':'UTF-8'}-files-list'));

        var file_name = $('<span/>')
        .append('<i class="icon-picture-o"></i> <strong>'+data.files[0].name+'</strong> ('+humanizeSize(data.files[0].size)+')')
        .appendTo(data.context);

        var button = $('<button/>').addClass('btn btn-default pull-right btn-action')
        .prop('type', 'button')
        .html("<i class='fa fa-trash-o'></i> {l s='Remove file' mod='ajaxzoom'}")
        .appendTo(data.context).on('click', function() {
            {$id|escape:'html':'UTF-8'}_total_files--;
            data.files = null;

            var total_elements = $(this).parent().siblings('div.form-group').length;
            $(this).parent().remove();

            if (total_elements == 0) {
                $('#{$id|escape:'html':'UTF-8'}-files-list').html('').parent().hide();
            }
        } );

        $('#{$id|escape:'html':'UTF-8'}-files-list').parent().show();
        $('#{$id|escape:'html':'UTF-8'}-upload-button').show().bind('click', function () {
            if (data.files != null)
                data.submit();
        });

        {$id|escape:'html':'UTF-8'}_total_files++;
    })
    .on('fileuploadprocessalways', function (e, data) {
        var index = data.index, file = data.files[index];

        if (file.error) {
            $('#{$id|escape:'html':'UTF-8'}-errors')
            .append('<div class="form-group"><i class="icon-picture-o"></i> <strong>'+file.name+'</strong> ('+humanizeSize(file.size)+') : '+file.error+'</div>')
            .parent().show();

            $('#{$id|escape:'html':'UTF-8'}-files-list').html('').parent().hide();
            $(data.context).find('button').trigger('click');
        }
    })
    .on('fileuploadsubmit', function (e, data) {
        var params = new Object();

        $('input[id^="legend_"]').each(function() {
            id = $(this).prop("id").replace("legend_", "legend[") + "]";
            params[id] = $(this).val();
        });

        data.formData = params;
    })
    .on('fileuploadstop', function (e, data) {

    });

    $('#{$id|escape:'html':'UTF-8'}-add-button').on('click', function() {
        $('#{$id|escape:'html':'UTF-8'}-success').html('').parent().hide();
        $('#{$id|escape:'html':'UTF-8'}-errors').html('').parent().hide();
        $('#{$id|escape:'html':'UTF-8'}').trigger('click');
    });
});
</script>
