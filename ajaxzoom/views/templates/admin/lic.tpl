{*
*  Module: jQuery AJAX-ZOOM for Prestashop, lic.tpl
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
    <label class="control-label col-lg-3">{l s='Licenses' mod='ajaxzoom'}: </label>
    <div class="col-lg-9">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 24%">{l s='Domain' mod='ajaxzoom'}</th>
                    <th style="width: 10%">{l s='License Type' mod='ajaxzoom'}</th>
                    <th style="width: 34%">{l s='License Key' mod='ajaxzoom'}</th>
                    <th style="width: 14%">{l s='Error200' mod='ajaxzoom'}</th>
                    <th style="width: 14%">{l s='Error300' mod='ajaxzoom'}</th>
                    <th style="width: 5%">&nbsp;</th>
                </tr>
            </thead>
            <tbody id="rowsLicense">
            </tbody>
        </table>

        <table id="templateLicense" style="display:none;">
            <tr>
                <td><input name="licenses[domain][]" value="domain_placeholder" class="lic-domain" style="width: 100%"></td>
                <td>
                    <select name="licenses[type][]" class="lic-type" style="min-width: 100px;">
                        <option value="evaluation">evaluation</option>
                        <option value="developer">developer</option>
                        <option value="simple">simple</option>
                        <option value="basic">basic</option>
                        <option value="standard">standard</option>
                        <option value="business">business</option>
                        <option value="corporate">corporate</option>
                        <option value="enterprise">enterprise</option>
                        <option value="unlimited">unlimited</option>
                    </select>
                </td>
                <td><input name="licenses[key][]" value="key_placeholder" class="lic-key" style="width: 100%"></td>
                <td><input name="licenses[error200][]" value="error200_placeholder" class="lic-error200" style="width: 100%"></td>
                <td><input name="licenses[error300][]" value="error300_placeholder" class="lic-error300" style="width: 100%"></td>
                <td>
                    <a class="btn btn-link bt-icon link_remove_license" href="#">
                        {if $ps_version >= 1.6}
                            <i class="icon-trash"></i> 
                        {else}
                            <img src="../img/admin/delete.gif" alt="Delete" title="Delete">
                        {/if}
                    </a>
                </td>
            </tr>
        </table>

        {if $ps_version >= 1.6}
            <div class="row">
        {else}
            <div class="margin-form" style="margin-top: 10px;">
        {/if}
            <a class="btn btn-link bt-icon link_add_license button" href="#" >
                <i class="icon-plus"></i> {l s='Add a license' mod='ajaxzoom'}
            </a>
        </div>
    </div>

    {if $ps_version >= 1.6}
        <div class="col-lg-9 col-lg-offset-3">
    {else}
        <div class="margin-form" style="margin-top: 10px;">
    {/if}
        <div class="alert alert-info" style="position: relative; margin-top: 20px;">
            <h4>{l s='Statistics' mod='ajaxzoom'}</h4>
            <table style="margin-bottom: 10px"><tbody>
                <tr><td style="padding-right: 15px">{l s='Number of images in the DB' mod='ajaxzoom'}</td><td>{$numImg}</td></tr>
                <tr><td style="padding-right: 15px">{l s='Number of 360 images uploaded' mod='ajaxzoom'}</td><td>{$num360}</td></tr>
                <tr style="border-top: 1px solid #4ac7e0"><td style="padding-right: 15px; padding-top: 5px;">{l s='Total' mod='ajaxzoom'}</td><td style="padding-top: 5px;">{$num360+$numImg}</td></tr>
            </tbody></table>
            <div style="position: absolute; right: 10px; bottom: 10px; text-align: right;">
                <a href="http://www.ajax-zoom.com/index.php?cid=contact" target="_blank">{l s='Ask for support' mod='ajaxzoom'} <i class="icon-external-link-sign"></i></a><br>
                <a href="http://www.ajax-zoom.com/index.php?cid=download#heading_3" target="_blank">{l s='Buy a license' mod='ajaxzoom'} <i class="icon-external-link-sign"></i></a>
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

$(function () { 

    {if substr(trim($licenses), 0, 1) == '['}
    var licenses = {$licenses}; {* cannot escape as it is javascript code *}
    {else}
    var licenses = [];
    {/if}

    function licenseLine(data)
    {
        var line = $("#templateLicense").html();
        line = line.replace(/domain_placeholder/g, data.domain);
        //line = line.replace(/type_placeholder/g, data.type);
        var reg = new RegExp('value="' + data.type + '"',"g");
        line = line.replace(reg, 'value="' + data.type + '" selected');
        line = line.replace(/key_placeholder/g, data.key);
        line = line.replace(/error200_placeholder/g, data.error200);
        line = line.replace(/error300_placeholder/g, data.error300);
        line = line.replace(/<tbody>/gi, "");
        line = line.replace(/<\/tbody>/gi, "");
        $("#rowsLicense").append(line);
    }

    {literal}
    $('.link_add_license').click(function (e) {
        e.preventDefault();
        var data = {domain: '', type: '', key: '', error200: '', error300: ''};
        licenseLine(data);
    });
    {/literal}

    $('.link_remove_license').die().live('click', function(e) {
        e.preventDefault();
        $(this).parent().parent().remove();
    });

    for(var k in licenses) {
        licenseLine(licenses[k]);
    }

} );
</script>
