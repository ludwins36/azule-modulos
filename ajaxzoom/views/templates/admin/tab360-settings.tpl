{*
*  Module: jQuery AJAX-ZOOM for Prestashop, tab360-settings.tpl
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

<!-- Enable / Disable AJAX-ZOOM tab -->
<div class="panel product-tab az_container">
    <div class="panel-heading tab headingRight" id="az_panel_azAct">{l s='Enable / Disable AJAX-ZOOM for this product' mod='ajaxzoom'}</div>
    <div class="panel-body">
        <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
            <label class="control-label col-lg-3">
                {l s='AJAX ZOOM enabled for this product\'s detail view' mod='ajaxzoom'} 
            </label>
            <div class="bootstrap  col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="az_active" id="az_active_on" value="1" {if $active == 1}checked="checked"{/if}/>
                    <label class="t" for="az_active_on">{l s='Yes' mod='ajaxzoom'}</label>
                    <input type="radio" name="az_active" id="az_active_off" value="0" {if $active == 0}checked="checked"{/if}/>
                    <label class="t" for="az_active_off">{l s='No' mod='ajaxzoom'}</label>
                    <a class="slide-button btn"></a>
                </span>
                <p class="help-block"></p>
            </div>
        </div>
        <div style="{if $ps_version >= 1.6}clear: both;{/if}">
            <a class="btn btn-link bt-icon button" href="javascript:void(0)" id="az_toggle_az_settings">
                <i class="fa fa-cog"></i> {l s='Individual module settings' mod='ajaxzoom'}
            </a>
        </div>
        <div style="{if $ps_version >= 1.6}clear: both;{/if} display: none;" id="az_az_settings">
            {if $ps_version >= 1.6}
            <div class="alert alert-info" style="display: block">
            {else}
            <div class="hint marginLeft260" style="display: block">
            {/if}
                {l s='Override module settings for this product only.' mod='ajaxzoom'}
                {l s='It is not needed and you do not have to set them here individually.' mod='ajaxzoom'}
                {l s='This is just for testing / experimenting and demo.' mod='ajaxzoom'}
                {l s='For reference see in module settings or visit www.ajax-zoom.com/examples/example32.php' mod='ajaxzoom'}
            </div>
            <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                <div class="bootstrap col-lg-12">
                    <div style="{if $ps_version >= 1.6}clear: both;{/if}">
                        <table style="width: 100%">
                            <thead>
                                <tr>
                                    <th>{l s='Name' mod='ajaxzoom'}</th>
                                    <th></th>
                                    <th>{l s='Value' mod='ajaxzoom'}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="az_pairRows_module">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">
                                        <div class="row_">
                                            <a class="btn btn-link bt-icon button" id="az_link_add_option_module" href="javascript:void(0)">
                                                <i class="fa fa-cog"></i> {l s='Add an option' mod='ajaxzoom'}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <table id="az_pairTemplate_module" style="display: none">
                            <tr>
                                <td><input type="text" name="az_pair_name_module[]" value="name_placeholder" 
                                    class="az_pair_names_module form-control" data-list="az_pair_list_names">
                                </td>
                                <td style="width: 20px;">&nbsp; : &nbsp;</td>
                                <td><input type="text" name="az_pair_value_module[]" value="value_placeholder" 
                                    class="az_pair_values_module form-control"></td>
                                <td style="white-space: nowrap; width: 72px;">
                                    <a class="btn btn-link bt-icon link_textarea_option_module" href="#">
                                        <i class="fa fa-pencil-square-o" title="Edit in textarea"></i>
                                    </a>
                                    <a class="btn btn-link bt-icon link_remove_option_module" href="#">
                                        <i class="fa fa-trash-o" title="Delete"></i>
                                    </a>

                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="col-lg-12" style="text-align: right;">
                    <button type="submit" name="az_submitSettings_module" id="az_submitSettings_module" class="btn btn-default button btn-action">
                        <i class="fa fa-floppy-o"></i> {l s='Save and stay' mod='ajaxzoom'}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Settings for existing 360/3D -->
<div id="product-images360sets" class="panel product-tab">

    <div class="panel-heading tab headingRight" id="az_panel_az360Sets">{l s='Settings for existing 360/3D' mod='ajaxzoom'}</div>
    <div class="panel-body {if $ps_version >= 1.6}clearfix{/if}">
        <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
            <label class="control-label col-lg-3">
                {l s='360/3D View' mod='ajaxzoom'}
            </label>
            <div class="bootstrap col-lg-9 ">
                <select id="id_360" name="id_360" style="min-width: 100px" class="form-control">
                    <option value="">{l s='Select' mod='ajaxzoom'}</option>
                    {foreach from=$sets_groups item=group}
                    <option value="{$group.id_360|escape:'htmlall':'UTF-8'}" 
                        data-settings="{$group.settings|urlencode|escape:'htmlall':'UTF-8'}" 
                        data-combinations="[{$group.combinations|urlencode|escape:'htmlall':'UTF-8'}]">
                        {$group.name|escape:'htmlall':'UTF-8'}
                    </option>
                    {/foreach}
                </select>
                <p class="help-block"></p>
            </div>
        </div>

        <div id="az_settings_360" style="display: none">

            <div class="form-group {if $ps_version >= 1.6}clearfix{/if}" id="az_pairs_360">
                <label class="control-label col-lg-3">
                    {l s='Settings' mod='ajaxzoom'}
                </label>
                <div class="bootstrap col-lg-9">
                    <table>
                        <thead>
                            <tr>
                                <th>{l s='Name' mod='ajaxzoom'}</th>
                                <th></th>
                                <th style="width: 220px">{l s='Value' mod='ajaxzoom'}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="az_pairRows_360">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <div class="row_">
                                        <a class="btn btn-link bt-icon button" id="az_link_add_option_360" href="#">
                                            <i class="icon-plus"></i> {l s='Add an option' mod='ajaxzoom'}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <table id="az_pairTemplate_360" style="display: none">
                        <tr>
                            <td><input type="text" name="az_pair_name[]" value="name_placeholder" class="az_pair_names_360 form-control"></td>
                            <td>&nbsp; : &nbsp;</td>
                            <td><input type="text" name="az_pair_value[]" value="value_placeholder" class="az_pair_values_360 form-control"></td>
                            <td>
                                <a class="btn btn-link bt-icon link_textarea_option" href="#">
                                    <i class="fa fa-pencil-square-o" title="Edit in textarea"></i>
                                </a>
                                <a class="btn btn-link bt-icon link_remove_option" href="#">
                                    <i class="fa fa-trash-o" title="Delete"></i>
                                </a>

                            </td>
                        </tr>
                    </table>

                    {if $ps_version >= 1.6}
                    <div class="alert alert-info" style="display: block">
                    {else}
                    <div class="hint marginLeft260" style="display: block">
                    {/if}
                        {l s='AJAX-ZOOM has several hundreds options.' mod='ajaxzoom'} 
                        {l s='Some of them can be set dynamically over JS and here, others need to be set in the config file.' mod='ajaxzoom'} 
                        {l s='The most important are already listed above but you can add more if needed!' mod='ajaxzoom'} 
                        {l s='For more options directly related to 360 please refer to AJAX-ZOOM.' mod='ajaxzoom'} 
                        <a href="http://www.ajax-zoom.com/index.php?cid=docs#VR_Object" target="_blank">
                            {l s='documentation' mod='ajaxzoom'} <i class="icon-external-link-sign"></i>
                        </a>
                    </div>
                </div>
            </div>

            {if $comb}
            <div class="form-group {if $ps_version >= 1.6}clearfix{/if}" id="az_comb_360">
                <label class="control-label col-lg-3">
                    {l s='Combinations' mod='ajaxzoom'}
                </label>
                <div class="bootstrap col-lg-9 ">
                    <input type="button" id="az_comb_check_all_360" style="margin-bottom: 10px;" 
                        value="check all" /><br>

                    {if $ps_version < 1.6}
                        <div class="paddingLeft260">
                    {/if}

                    {foreach from=$comb item=data}
                    <input type="checkbox" name="az_combinations_360[]" value="{$data.id_product_attribute|escape:'htmlall':'UTF-8'}" 
                        class="az_settings_combinations_360"> {$data.name|escape:'htmlall':'UTF-8'}<br>
                    {/foreach}

                    {if $ps_version < 1.6}
                        </div>
                    {/if}

                    {if $ps_version >= 1.6}
                    <div class="alert alert-info" style="display: block; margin-top: 10px;">
                    {else}
                    <div class="hint marginLeft260" style="display: block; margin-top: 10px;">
                    {/if}
                        {l s='Same as with images you can define which 360 should be shown in conjunction with which combinations.' mod='ajaxzoom'} 
                        {l s='If you do not select any, this 360 will be shown for all combinations.' mod='ajaxzoom'}
                    </div>

                </div>
            </div>
            {/if}

            <div class="panel-footer buttonRight">
                <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                    <div class="bootstrap col-lg-3"></div>
                    <div class="bootstrap col-lg-9">
                        <button type="submit" name="az_submitSettings_360" id="az_submitSettings_360" class="btn btn-default button btn-action">
                            <i class="fa fa-floppy-o"></i> {l s='Save and stay' mod='ajaxzoom'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script type="text/javascript">
/*!
*  @author         AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright      2010-2018 AJAX-ZOOM, Vadim Jacobi
*  @license        http://www.ajax-zoom.com/index.php?cid=download
*/

window.az_token = '{$token|escape:'html':'UTF-8'}';
window.az_plugin_opt = {$az_plugin_opt|@json_encode nofilter};
window.az_plugin_prod_opt = JSON.parse('{$az_plugin_prod_opt}'); // cannot escape
var az_lang_module_settings_edited = "{l s='Module settings for this product have been set' mod='ajaxzoom'}";

$(function () {
    function pairLine360(name, value) { 
        if (typeof value == 'object') {
            value = JSON.stringify(value);
        }

        if (typeof value == 'string') {
            value = value.replace(/"/g, "&quot;");
        }

        var line = $("#az_pairTemplate_360").html();
        line = line.replace(/name_placeholder/g, name);
        line = line.replace(/value_placeholder/g, value);
        line = line.replace(/<tbody>/gi, "");
        line = line.replace(/<\/tbody>/gi, "");
        $("#az_pairRows_360").append(line);
    }

    function pairLineModule(name, value) { 
        if (typeof value == 'object') {
            value = JSON.stringify(value).replace(/"/g, "&quot;");
        }

        if (typeof value == 'string') {
            value = value.replace(/"/g, "&quot;");
        }

        var line = $("#az_pairTemplate_module").html();
        line = line.replace(/name_placeholder/g, name);
        line = line.replace(/value_placeholder/g, value);
        line = line.replace(/az_pair_names_module/g, 'az_pair_names_module_a');
        line = line.replace(/az_pair_values_module/g, 'az_pair_values_module_a');
        line = line.replace(/az_pair_list_names/g, 'az_plugin_opt_list');
        line = line.replace(/<tbody>/gi, "");
        line = line.replace(/<\/tbody>/gi, "");
        line = $(line);

        $("#az_pairRows_module").append(line);

        setTimeout(function() {
            $('input.az_pair_names_module_a', line).aZeditableSelect();
        }, 1);
    }

    $('body').on('change', '.az_pair_names_360, .az_pair_values_360', function(e) {
        $('#az_submitSettings_360').addClass('save-require');
    } );

    $('input[name="az_active"]').bind('change', function() {
        doAdminAjax360( { 
            "action": "setActive",
            "id_product": {$id_product|escape:'htmlall':'UTF-8'},
            "active": $('input[name=az_active]:checked').val(),
            "token": window.az_token,
            "fc": "module",
            "module": "ajaxzoom",
            "controller": "image360",
            "ajax": 1
        }, function (data) { 
            var data = $.parseJSON(data);
            showSuccessMessage(data.confirmations);
        } );
    } );

    $('#az_submitSettings_360').click(function (e) { 
        e.preventDefault();

        var active = $('input[name=az_active]:checked').val();

        var inputs = document.getElementsByClassName( 'az_pair_names_360' );
        var names  = [].map.call(inputs, function( input ) { 
            return input.value;
        } ).join( '|' );

        var inputs = document.getElementsByClassName( 'az_pair_values_360' );
        var values  = [].map.call(inputs, function( input ) {
            return input.value;
        } ).join( '|' );

        var tmp = [];
        $('.az_settings_combinations_360').each(function() {
            if ($(this).is(':checked')) {
                tmp.push($(this).val());
            }
        } );

        var combinations = tmp.join( '|' );
        var id_360 = $('select#id_360').val();

        doAdminAjax360( { 
            "action": "saveSettings",
            "id_product": {$id_product|escape:'htmlall':'UTF-8'},
            "id_360": id_360,
            "names": names,
            "combinations": combinations,
            "values": values,
            "active": active,
            "token": window.az_token,
            "fc": "module",
            "module": "ajaxzoom",
            "controller": "image360",
            "ajax": 1
        }, function (data) { 
            var data = $.parseJSON(data);
            $('#id_360').replaceWith(data.select);
            $('#az_settings_360').hide();
            $('select#id_360').val('');
            showSuccessMessage(data.confirmations);
            $('#az_submitSettings_360').removeClass('save-require');
        } );
    } );

    $('#az_link_add_option_360').click(function (e) {
        e.preventDefault();
        pairLine360('', '');
    } );

    $('body').on('click', '.link_remove_option', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        $('#az_submitSettings_360').addClass('save-require');
    } );

    $('body').on('click', '.link_textarea_option', function(e) {
        e.preventDefault();
        var td = $(this).parent().prev();
        if ($('input', td).length == 1) { 
            var Val = $('input', td).val().replace(/"/g, "&quot;");
            $('input', td).replaceWith('<textarea class="az_pair_values_360 form-control" type="text" name="az_pair_value[]">'+Val+'</textarea>');
        } else if ($('textarea', td).length == 1) { 
            var Val = $('textarea', td).val().replace(/"/g, "&quot;");
            $('textarea', td).replaceWith('<input class="az_pair_values_360 form-control" type="text" value="'+Val+'" name="az_pair_value[]">');
        }
    } );

    $('body').on('change', 'select#id_360', function(e) { 
        $('#az_pairRows_360').html('');

        if ($(this).val() != '') { 
            $('#az_settings_360').show();

            var settings = $.parseJSON(decodeURIComponent($('option:selected', $(this)).attr('data-settings').replace(/\+/g, '%20')));
            for (var k in settings) { 
                pairLine360(k, settings[k])
            }

            // set combinations checkboxes
            var combinations = $.parseJSON(decodeURIComponent($('option:selected', $(this)).attr('data-combinations').replace(/\+/g, '%20')));
            $('input.az_settings_combinations_360').attr('checked', false);

            for (var k in combinations) { 
                $('input.az_settings_combinations_360[value=' + combinations[k] + ']').prop('checked', true);
            }

        } else {
            $('#az_settings_360').hide();
        }
    } );

    $('#az_comb_check_all_360')
    .bind('click', function(){
        var dd = $(this).data('state');
        if (dd == 'enabled') {
            $(this).data('state', 'disabled');
            $(this).val('check all');
            $('input.az_settings_combinations_360').prop('checked', false);
        } else {
            $(this).data('state', 'enabled');
            $(this).val('uncheck all');
            $('input.az_settings_combinations_360').prop('checked', true);
        }
    } );

    // Possible plugin options
    if (az_plugin_opt && typeof az_plugin_opt == 'object') {
        var datalist = '<datalist id="az_plugin_opt_list">';
        $.each(az_plugin_opt, function(k, v) {
            datalist += '<option value="'+v+'">';
        } );

        datalist += '</datalist>';
        $('body').append(datalist);
        delete datalist;
    }

    $('#az_link_add_option_module').bind('click', function() {
        pairLineModule('', '');
    } );

    $('#az_toggle_az_settings').bind('click', function() {
        $('#az_az_settings').slideToggle(150);
    } );

    $('body').on('click', '.link_textarea_option_module', function(e) {
        e.preventDefault();
        var td = $(this).parent().prev();
        if ($('input', td).length == 1) { 
            var Val = $('input', td).val().replace(/"/g, "&quot;");
            $('input', td).replaceWith('<textarea class="az_pair_values_module_a form-control" type="text" name="az_pair_value[]">'+Val+'</textarea>');
        } else if ($('textarea', td).length == 1) { 
            var Val = $('textarea', td).val().replace(/"/g, "&quot;");
            $('textarea', td).replaceWith('<input class="az_pair_values_module_a form-control" type="text" value="'+Val+'" name="az_pair_value[]">');
        }
    } );

    $('body').on('click', '.link_remove_option_module', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        $('#az_submitSettings_module').addClass('save-require');
    } );

    $('body').on('change', '.az_pair_names_module_a, .az_pair_values_module_a', function() {
        $('#az_submitSettings_module').addClass('save-require');
    } );

    $.each(window.az_plugin_prod_opt, function(a, b) {
        pairLineModule(a, b);
    } );

    $('#az_submitSettings_module').bind('click', function(e) {
        e.preventDefault();
        $(this).blur();

        var inputs;

        inputs = document.getElementsByClassName('az_pair_names_module_a'); 
        var names = [].map.call(inputs, function( input ) { 
            return input.value;
        } ).join( '|' );

        inputs = document.getElementsByClassName('az_pair_values_module_a');
        var values = [].map.call(inputs, function( input ) {
            return input.value;
        } ).join( '|' );

        $('#az_submitSettings_module').removeClass('save-require');

        doAdminAjax360( { 
            "action": "saveProductAzSettings",
            "id_product": {$id_product|escape:'htmlall':'UTF-8'},
            "names": names,
            "values": values,
            "token": window.az_token,
            "fc": "module",
            "module": "ajaxzoom",
            "controller": "image360",
            "ajax": 1
        }, function (data) { 
            data = $.parseJSON(data);
            window.az_plugin_prod_opt = $.parseJSON(data.moduleSettings);

            $("#az_pairRows_module").empty();
            $.each(window.az_plugin_prod_opt, function(a, b) {
                pairLineModule(a, b);
            } );

            showSuccessMessage(az_lang_module_settings_edited);
        } );
    } );

    {if $ps_version >= 1.6}
    $(document).ready(function() { 
        window.az_pannel_toggle();
    } );
    {/if}
});
</script>
