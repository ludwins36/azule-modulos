{*
*  Module: jQuery AJAX-ZOOM for Prestashop, tab360-videos.tpl
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
    <div class="panel-heading tab headingRight" id="az_panel_videos">
        {l s='Videos: YouTube, Vimeo, Dailymotion, MP4' mod='ajaxzoom'}
    </div>

    <div class="panel-body" id="az_video_panel">

        <div class="row">
            {if $ps_version >= 1.6}
            <div class="alert alert-info" style="display: block">
            {else}
            <div class="hint marginLeft260" style="display: block">
            {/if}
                {l s='This widget will let you define videos from YouTube, Vimeo, Dailymotion or link to mp4 videos located else where.' mod='ajaxzoom'}
                {l s='For variable product, you are able to associate videos only with certain product variations.' mod='ajaxzoom'}
                {l s='Also you can, but not obligated to, define alternative video sources for shop languages.' mod='ajaxzoom'}
                {l s='Let us know if your are missing something' mod='ajaxzoom'}
                {l s='Your ideas are greatly appreciated!' mod='ajaxzoom'}
            </div>
        </div>

        <!-- Existing videos -->
        <div class="row">
            <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                <label class="control-label col-lg-3">
                    <span class="label-tooltip" data-toggle="tooltip" 
                        data-original-title="{l s='Select a video to change the settings' mod='ajaxzoom' mod='ajaxzoom'}">
                        {l s='Existing videos' mod='ajaxzoom'}
                    </span>
                </label>
                <div class="bootstrap col-lg-9">
                    <select id="az_id_video" name="az_id_video" style="min-width: 100px" class="form-control">
                        <option value="">{l s='Select' mod='ajaxzoom'}</option>
                    </select>
                </div>
            </div>

            <div id="az_settings_video" style="display: none; padding-top: 30px;">
                <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                    <label class="control-label col-lg-3">
                        <span>
                            {l s='Name' mod='ajaxzoom'}
                        </span>
                    </label>
                    <div class="col-lg-9">
                        <input type="text" id="az_video_name_edit" name="az_video_name_edit" value="" class="form-control" />
                    </div>
                </div>

                <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                    <label class="control-label col-lg-3">
                        <span>
                            {l s='Type' mod='ajaxzoom'}
                        </span>
                    </label>
                    <div class="col-lg-9">
                        <select class="form-control" id="az_video_type_edit" name="az_video_type_edit">
                            <option value="youtube">YouTube</option>
                            <option value="vimeo">Vimeo</option>
                            <option value="dailymotion">Dailymotion</option>
                            <option value="videojs">HTML5 / videojs</option>
                        </select>
                    </div>
                </div>

                <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                    <label class="control-label col-lg-3">
                        <span>
                            {l s='Key / Url' mod='ajaxzoom'}
                        </span>
                    </label>
                    <div class="col-lg-9">
                        <input type="text" id="az_video_uid_edit" name="az_video_uid_edit" value="" class="form-control" /> 
                    </div>
                </div>

                {if $languages|@count > 1}
                <div class="form-group {if $ps_version >= 1.6}clearfix{/if}" style="padding-top: 20px">
                    <label class="control-label col-lg-3">
                        <span>
                            {l s='Key / Url (international)' mod='ajaxzoom'}
                        </span>
                    </label>
                    <div class="col-lg-9">
                        {foreach from=$languages item=lang}
                        <div class="row">
                            <div class="col-md-3 az_nopadding">{$lang.name|escape:'html':'UTF-8'}</div>
                            <div class="col-md-9 az_nopadding">
                            <input type="text" name="az_video_lang_edit[{$lang.iso_code|escape:'html':'UTF-8'}]"
                                data-lang="{$lang.iso_code|escape:'html':'UTF-8'}" 
                                value="" class="az_video_lang_edit form-control" style="margin-bottom: 5px;" />
                            </div>
                        </div>
                        {/foreach}
                    </div>
                </div>
                {/if}

                {if $comb}
                <div class="form-group {if $ps_version >= 1.6}clearfix{/if}" id="az_comb_video">
                    <label class="control-label col-lg-3">
                        {l s='Combinations' mod='ajaxzoom'}
                    </label>
                    <div class="bootstrap col-lg-9">
                        <input type="button" id="az_comb_check_all_video" style="margin-bottom: 10px;" value="check all" /><br>

                        {if $ps_version < 1.6}
                            <div class="paddingLeft260">
                        {/if}

                        {foreach from=$comb item=data}
                        <input type="checkbox" name="az_combinations_video[]" value="{$data.id_product_attribute|escape:'htmlall':'UTF-8'}" 
                            class="az_settings_combinations_video"> {$data.name|escape:'htmlall':'UTF-8'}<br>
                        {/foreach}

                        {if $ps_version < 1.6}
                            </div>
                        {/if}

                    </div>
                </div>
                {/if}

                <div class="form-group {if $ps_version >= 1.6}clearfix{/if}" id="az_comb_video">
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
                            <tbody id="az_pairRows_video">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">
                                        <div class="row_">
                                            <a class="btn btn-link bt-icon button" id="az_link_add_option_video" href="#">
                                                <i class="icon-plus"></i> {l s='Add an option' mod='ajaxzoom'}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <table id="az_pairTemplate_video" style="display: none">
                            <tr>
                                <td><input type="text" name="az_pair_name[]" value="name_placeholder" class="az_pair_names_video form-control"></td>
                                <td>&nbsp; : &nbsp;</td>
                                <td><input type="text" name="az_pair_value[]" value="value_placeholder" class="az_pair_values_video form-control"></td>
                                <td>
                                    <a class="btn btn-link bt-icon link_textarea_option_video" href="#">
                                        <i class="fa fa-pencil-square-o" title="Edit in textarea"></i>
                                    </a>
                                    <a class="btn btn-link bt-icon link_remove_option_video" href="#">
                                        <i class="fa fa-trash-o" title="Delete"></i>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="panel-footer" style="margin-top: 20px; margin-bottom: 20px;">
                    <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                        <div class="bootstrap col-lg-3"></div>
                        <div class="bootstrap col-lg-9">
                            <button type="submit" name="az_submitSettings_video" id="az_submitSettings_video" class="btn btn-default button btn-action">
                                <i class="fa fa-floppy-o"></i></i> {l s='Save and stay' mod='ajaxzoom'}
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        {if $ps_version < 1.6}
        <div class="separation"></div>
        {/if}
        <!-- New video button -->
        <div class="row paddingLeft260">
            <a class="btn bt-icon button btn-block btn-success" id="az_link_add_video" href="javascript:void(0)">
                <i class="icon-plus"></i> {l s='Add a new video' mod='ajaxzoom'}
            </a>
        </div>

        <!-- New video form -->
        <div class="row" id="az_newFormVideo" style="display: none; margin-top: 15px;">

            <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                <label class="control-label col-lg-3">
                    <span class="label-tooltip" data-toggle="tooltip" 
                        data-original-title="{l s='Please enter any name' mod='ajaxzoom' mod='ajaxzoom'}">
                        {l s='Name' mod='ajaxzoom'}
                    </span>
                </label>
                <div class="col-lg-3">
                    <input type="text" id="az_video_name_new" name="az_video_name_new" value="" class="form-control" />
                </div>
                {if $ps_version < 1.6}
                <div class="tooltipReplacement">
                    {l s='Please enter any name' mod='ajaxzoom' mod='ajaxzoom'}
                </div>
                {/if}
            </div>

            <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                <label class="control-label col-lg-3">
                    <span class="label-tooltip" data-toggle="tooltip" 
                        data-original-title="{l s='Please choose video type' mod='ajaxzoom'}">
                        {l s='Type' mod='ajaxzoom'}
                    </span>
                </label>
                <div class="col-lg-3">
                    <select class="form-control" id="az_video_type_new" name="az_video_type_new">
                        <option value="youtube">YouTube</option>
                        <option value="vimeo">Vimeo</option>
                        <option value="dailymotion">Dailymotion</option>
                        <option value="videojs">HTML5 / videojs</option>
                    </select>
                </div>
                {if $ps_version < 1.6}
                <div class="tooltipReplacement">{l s='Please choose video type' mod='ajaxzoom'}</div>
                {/if}
            </div>

            <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                <label class="control-label col-lg-3">
                    <span class="label-tooltip" data-toggle="tooltip" 
                        data-original-title="{l s='Enter the video key or URL for mp4' mod='ajaxzoom'}">
                        {l s='Key / Url' mod='ajaxzoom'}
                    </span>
                </label>
                <div class="col-lg-3">
                    <input type="text" id="az_video_uid_new" name="az_video_uid_new" value="" class="form-control" /> 
                </div>

                {if $ps_version < 1.6}
                <div class="tooltipReplacement">
                    {l s='Enter the video key or URL for mp4' mod='ajaxzoom'}
                </div>
                {/if}
            </div>

            <div class="form-group {if $ps_version >= 1.6}clearfix{/if}">
                <label class="control-label col-lg-3">
                </label>
                <div class="col-lg-9">
                    <a href="javascript:void(0)" class="btn btn-default button btn-action" id="az_add_video">
                        <i class="fa fa-floppy-o"></i> {l s='Add' mod='ajaxzoom'}
                    </a>
                </div>
            </div>
        </div>

        <br><br>

        <div class="row">
            <table class="table tableDnD" id="az_videosTable">
                <thead>
                    <tr class="nodrag nodrop">
                        <th class="fixed-width-lg"><span class="title_box"></span></th>
                        <th><span class="title_box">{l s='Name' mod='ajaxzoom'}</span></th>
                        <th><span class="title_box">{l s='Active' mod='ajaxzoom'}</span></th>
                        <th><span class="title_box">{l s='Type' mod='ajaxzoom'}</span></th>
                        <th><span class="title_box">{l s='Key / Link' mod='ajaxzoom'}</span></th>
                        <th></th> <!-- action -->
                    </tr>
                </thead>
                <tbody id="az_videosTableRows">
                </tbody>
            </table>

            <table id="az_lineSetVideo" style="display: none;">
                <tr id="az_video_line_id">
                    <td class="az_tbl_vid_img">
                        <img src="{$base_uri|escape:'html':'UTF-8'}modules/ajaxzoom/views/img/default-video-thumbnail.jpg" 
                            alt="img" title="img" 
                            class="img-thumbnail" 
                            style="max-width: 100px; cursor: pointer;">
                    </td>
                    <td class="az_tbl_vid_name">
                        video_name
                    </td>
                    <td>
                        <span class="switch prestashop-switch fixed-width-lg hide_class az_switch_status_video">
                            <input type="radio" name="status_field" id="status_field_on" value="1" checked_on />
                            <label class="t" for="status_field_on">{l s='Yes' mod='ajaxzoom'}</label>
                            <input type="radio" name="status_field" id="status_field_off" value="0" checked_off />
                            <label class="t" for="status_field_off">{l s='No' mod='ajaxzoom'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </td>
                    <td class="az_tbl_vid_type">video_type</td>
                    <td class="az_tbl_vid_uid" style="word-break: break-all;">video_uid</td>
                    <td>
                        <a href="#" class="az_delete_video pull-right btn btn-default button btn-action" style="margin-left: 5px; margin-bottom: 5px;">
                            <i class="fa fa-trash-o"></i> {l s='Delete' mod='ajaxzoom'}
                        </a>
                        <a href="#" class="az_preview_video pull-right btn btn-default button btn-action" style="margin-left: 5px; margin-bottom: 5px;">
                            <i class="fa fa-eye"></i> {l s='Preview' mod='ajaxzoom'}
                        </a>
                        <a href="#" class="az_edit_video pull-right btn btn-default button btn-action" style="margin-left: 5px; margin-bottom: 5px;">
                            <i class="fa fa-cog"></i> {l s='Edit' mod='ajaxzoom'}
                        </a>
                    </td>
                </tr>
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

window.thumbnail_auto = '{$base_uri|escape:'html':'UTF-8'}modules/ajaxzoom/views/img/default-video-thumbnail.jpg';

var az_base_url = '{$base_url|escape:'html':'UTF-8'}';
var az_base_uri = '{$base_uri|escape:'html':'UTF-8'}';
var az_base_dir = '{$base_dir|escape:'html':'UTF-8'}';
var az_videos = {$videos|@json_encode nofilter};
var az_select_videos = $('#az_id_video').clone();
var az_languages = {$languages|@json_encode nofilter};
var az_id_product = '{$id_product|escape:'html':'UTF-8'}';
var az_token = '{$token|escape:'html':'UTF-8'}';

var az_lang_define_key = "{l s='Please define Key / Url field' mod='ajaxzoom'}";
var az_lang_success_change_settings = "{l s='Video settings have been changed' mod='ajaxzoom'}";
var az_lang_new_video_added = "{l s='Video settings have been changed' mod='ajaxzoom'}";
var az_lang_video_not_added = "{l s='Video has not been added' mod='ajaxzoom'}";
var az_lang_are_you_sure = "{l s='Are you sure?' mod='ajaxzoom'}";
var az_lang_video_deleted = "{l s='Video has been deleted' mod='ajaxzoom'}";
var az_lang_video_not_deleted = "{l s='Video has not been deleted' mod='ajaxzoom'}";
var az_lang_video_enabled = "{l s='Video has been enabled' mod='ajaxzoom'}";
var az_lang_video_disabled = "{l s='Video has been disabled' mod='ajaxzoom'}";
</script>

<script type="text/javascript" src="{$base_url|escape:'html':'UTF-8'}modules/ajaxzoom/views/js/ajaxzoom_ps_video.js"></script>
<script type="text/javascript">
/*!
*  @author         AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright      2010-2018 AJAX-ZOOM, Vadim Jacobi
*  @license        http://www.ajax-zoom.com/index.php?cid=download
*/
if ($.isFunction($.fn.tooltip)) {
    $('.label-tooltip', $('#az_video_panel')).tooltip();
    $('#az_id_video').on('change', function(){
        setTimeout(function(){
            $('.label-tooltip', $('#az_settings_video')).tooltip();
        }, 10);
    });
}
</script>
