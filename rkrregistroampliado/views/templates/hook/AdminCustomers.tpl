{*
*
* NOTICE OF LICENSE
*
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by Rekire.com
*
* @author     Rekire - Tecnología web
* @copyright  2018 Rekire.com All right reserved
* @license    Rekire
* @contact    info@rekire.com
*
*}

{* Prestashop 1.6 Bootstrap *}
{if !empty($bootstrap)}
    <div class="col-lg-6" id="rkr_registration" style="display: none;">
        <div class="panel">
            <div class="panel-heading">Custom Registration</div>

            {if $update === true}
                <div class="alert alert-success">{l s='Success update' mod='rkrregistroampliado'}</div>
            {else if $update === false}
                <div class="alert alert-error">{l s='Error' mod='rkrregistroampliado'}</div>
            {/if}
            <form action="#" method="POST" class="std box">
                {foreach from=$fields item=field name=rkr_responses}

                    {if !empty($responses[$field.id])}
                        {assign var="response" value=$responses[$field.id]}
                    {else}
                        {assign var="response" value=null}
                    {/if}

                    <div class="form-group">
                        {if $field.type !== "checkbox"}
                            <label for="rkr_{$field.id|escape:'htmlall':'UTF-8'}">{$field.name|escape:'htmlall':'UTF-8'} {if $field.required}<sup>*</sup>{/if}</label>
                        {/if}

                        {* Simple text *}
                        {if $field.type === "text"}
                            <input type="text" class="{if $field.required}is_required{/if} validate form-control"
                                   data-validate="isGenericName" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
                                   value="{if !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}" />

                            {* Textarea *}
                        {else if $field.type === "textarea"}
                            <textarea name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" class="{if $field.required}is_required{/if} validate form-control" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
                                      data-validate="isGenericName">{if !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}</textarea>

                            {* Select simple *}
                        {else if $field.type === "select"}
                            {assign var=listOption value=";"|explode:$field.fieldsSelector}
                            <select name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}" class="{if $field.required}is_required{/if} validate form-control">
                                {foreach from=$listOption item=option}
                                    <option value="{$option|escape:'htmlall':'UTF-8'}" {if !empty($response.reponse) && $response.reponse === $option}selected="selected"{/if}>{$option|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>

                            {* Select multi *}
                        {else if $field.type === "dropdown_multi"}
                            {assign var=listOption value=";"|explode:$field.fieldsSelector}

                            {if !empty($response.reponse)}
                                {assign var=listResponses value=$response.reponse|unserialize}
                            {else}
                                {assign var=listResponses value=array}
                            {/if}
                            <select name="rkr_{$field.id|escape:'htmlall':'UTF-8'}[]" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}" class="{if $field.required}is_required{/if} validate form-control" multiple>
                                {foreach from=$listOption item=option}
                                    <option value="{$option|escape:'htmlall':'UTF-8'}" {if !empty($response.reponse) && !empty($listResponses) && in_array($option, $listResponses)}selected{/if}>{$option|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>

                            {* Radio *}
                        {else if $field.type === "radio"}
                            {assign var=listOption value=";"|explode:$field.fieldsSelector}
                            <div>
                                {foreach from=$listOption item=option}
                                    <div class="radio-inline">
                                        <input type="radio" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" value="{$option|escape:'htmlall':'UTF-8'}"
                                               {if !empty($response.reponse) && $response.reponse === $option}checked{/if} />
                                        {$option|escape:'htmlall':'UTF-8'}
                                    </div>
                                {/foreach}
                            </div>

                            {* Upload *}
                        {else if $field.type === "upload"}
                            <div>
                                {l s='Accepted filetype' mod='rkrregistroampliado'} :
                                {foreach from=$field.fieldsSelector|@unserialize item="filetype" name=rkr_field_upload}
                                    {if !empty($filetypeName[$filetype])}{$filetypeName[$filetype]|escape:'htmlall':'UTF-8'}{/if}
                                    {if $smarty.foreach.rkr_field_upload.last == false}, {/if}
                                {/foreach}
                                <input type="file" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}">
                                {if !empty($response.reponse)}
                                  <a href="{$rkr_uploads_dir|escape:'htmlall':'UTF-8'}/{$response.reponse|escape:'htmlall':'UTF-8'}">{l s='Download file' mod='rkrregistroampliado'}</a>
                                {/if}
                            </div>
                            <script type="text/javascript">
                              $("#rkr_{$field.id|escape:'htmlall':'UTF-8'}").closest("form").attr("enctype", "multipart/form-data");
                            </script>

                            {* YesNo *}
                        {else if $field.type === "yesno"}
                            <div>
                                <div class="radio-inline">
                                    <input type="radio" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" value="1"
                                           {if !empty($response.reponse)}checked{/if} />
                                    {l s='Yes' mod='rkrregistroampliado'}
                                </div>
                                <div class="radio-inline">
                                    <input type="radio" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" value="0"
                                           {if empty($response.reponse)}checked{/if} />
                                    {l s='No' mod='rkrregistroampliado'}
                                </div>
                            </div>

                            {* Date *}
                        {else if $field.type === "date"}
                            <div>
                                <input type="text" class="{if $field.required}is_required{/if} validate form-control"
                                       data-validate="isDate" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
                                       value="{if !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}" />
                            </div>
                            <script type="text/javascript">
                                $('#rkr_{$field.id|escape:'htmlall':'UTF-8'}').datetimepicker({
                                    lang: 'fr',
                                    timepicker: false,
                                    format: 'd-m-Y'
                                });
                            </script>

                            {* Time *}
                        {else if $field.type === "time"}
                            <div>
                                <input type="text" class="{if $field.required}is_required{/if} validate form-control"
                                       data-validate="isDate" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
                                       value="{if !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}" />
                            </div>
                            <script type="text/javascript">
                                $('#rkr_{$field.id|escape:'htmlall':'UTF-8'}').datetimepicker({
                                    lang: 'fr',
                                    datepicker: false,
                                    format: 'H:i',
                                    step: 30
                                });
                            </script>

                            {* DateTime *}
                        {else if $field.type === "datetime"}
                            <div>
                                <input type="text" class="{if $field.required}is_required{/if} validate form-control"
                                       data-validate="isDate" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
                                       value="{if !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}" />
                            </div>
                            <script type="text/javascript">
                                $('#rkr_{$field.id|escape:'htmlall':'UTF-8'}').datetimepicker({
                                    lang: 'fr',
                                    format: 'd-m-Y H:i',
                                    step: 30
                                });
                            </script>

                            {* Checkbox *}
                        {else if $field.type === "checkbox"}
                            <div class="checkbox">
                                <label for="rkr_{$field.id|escape:'htmlall':'UTF-8'}">
                                    <input type="checkbox" class="validate" data-validate="isGenericName"
                                           name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}" {if !empty($response.reponse)}checked="checked"{/if} />
                                    {$field.name|escape:'htmlall':'UTF-8'} {if $field.required}<sup>*</sup>{/if}
                                </label>
                            </div>
                        {/if}
                        {if $field.desc}<span class="form_info">{$field.desc|escape:'htmlall':'UTF-8'}</span>{/if}
                    </div>
                {/foreach}

                <input type="hidden" name="submitUpdateResponses" value="1" />
                <div class="row">
                    <div class="col-lg-12">
                        <button type="submit" id="submitCustomerNote" class="btn btn-default pull-right">
                            <i class="icon-save"></i>
                            Enregistrer
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {

            $('#container-customer > div.row:first > div:first').append($('#rkr_registration').html());
        });
    </script>
{else}
    <div class="clear"></div>
    <div style="margin: 20px 0px;">
        <form action="#" method="POST" class="std box">
            <h2>{l s='Custom Registration' mod='rkrregistroampliado'}</h2>
            <table class="table" style="width: 50%;">
                <thead>
                    <tr>
                        <th>Campos</th>
                        <th>Valeur(s)</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$fields item=field name=rkr_responses}
                        {if !empty($responses[$field.id])}
                            {assign var="response" value=$responses[$field.id]}
                        {else}
                            {assign var="response" value=null}
                        {/if}
                        <tr>
                            <td>{$field.name|escape:'htmlall':'UTF-8'}</td>
                            <td>
                                {if $field.type === "text"}
                                    <p class="{if $field.required}required{/if} text">
                                        <input type="text" class="{if $field.required}is_required{/if} validate form-control"
                                               data-validate="isGenericName" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
                                               value="{if !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}" />
                                    </p>

                                    {* Textarea *}
                                {else if $field.type === "textarea"}
                                    <p class="{if $field.required}required{/if} textarea">
                                        <textarea name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" class="{if $field.required}is_required{/if} validate" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}">{if !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}</textarea>
                                    </p>

                                    {* Select simple *}
                                {else if $field.type === "select"}
                                    {assign var=listOption value=";"|explode:$field.fieldsSelector}
                                    <p class="select {if $field.required}required{/if}">
                                        <select name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}" class="">
                                            {foreach from=$listOption item=option}
                                                <option value="{$option|escape:'htmlall':'UTF-8'}" {if !empty($response.reponse) && $response.reponse === $option}selected="selected"{/if}>{$option|escape:'htmlall':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                    </p>

                                    {* Select multi *}
                                {else if $field.type === "dropdown_multi"}
                                    {assign var=listOption value=";"|explode:$field.fieldsSelector}
                                    <p class="select {if $field.required}required{/if}">
                                        {if !empty($response.reponse)}
                                            {assign var=listResponses value=$response.reponse|unserialize}
                                        {else}
                                            {assign var=listResponses value=array}
                                        {/if}

                                        <select name="rkr_{$field.id|escape:'htmlall':'UTF-8'}[]" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}" class="" multiple>
                                            {foreach from=$listOption item=option}
                                                <option value="{$option|escape:'htmlall':'UTF-8'}" {if !empty($response.reponse) && !empty($listResponses) && in_array($option, $listResponses)}selected{/if}>{$option|escape:'htmlall':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                    </p>

                                    {* Radio *}
                                {else if $field.type === "radio"}
                                    {assign var=listOption value=";"|explode:$field.fieldsSelector}
                                    <p class="radio {if $field.required}required{/if}">
                                        {foreach from=$listOption item=option name=rkr_field_option}
                                            <input type="radio" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}_{$smarty.foreach.rkr_field_option.index|escape:'htmlall':'UTF-8'}" value="{$option|escape:'htmlall':'UTF-8'}"
                                                   {if !empty($response.reponse) && $response.reponse === $option}checked{/if} class="top" />
                                            <span for="rkr_{$field.id|escape:'htmlall':'UTF-8'}_{$smarty.foreach.rkr_field_option.index|escape:'htmlall':'UTF-8'}">{$option|escape:'htmlall':'UTF-8'}</span>
                                        {/foreach}
                                    </p>

                                    {* YesNo *}
                                {else if $field.type === "yesno"}
                                    <p class="radio {if $field.required}required{/if}">
                                        <input type="radio" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}_yes" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" value="1" {if !empty($response.reponse)}checked{/if} />
                                        <span for="rkr_{$field.id|escape:'htmlall':'UTF-8'}_yes">{l s='Yes' mod='rkrregistroampliado'}</span>
                                        <input type="radio" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}_no" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" value="0" {if empty($response.reponse)}checked{/if} />
                                        <span for="rkr_{$field.id|escape:'htmlall':'UTF-8'}_no">{l s='No' mod='rkrregistroampliado'}</span>
                                    </p>

                                    {* Date *}
                                {else if $field.type === "date"}
                                    <p class="text {if $field.required}required{/if}">
                                        <input type="text" class="{if $field.required}is_required{/if} validate form-control"
                                               data-validate="isDate" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}" value="{if !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}" />
                                    </p>
                                    <script type="text/javascript">
                                        $('#rkr_{$field.id|escape:'htmlall':'UTF-8'}').datetimepicker({
                                            lang: 'fr',
                                            timepicker: false,
                                            format: 'd-m-Y'
                                        });
                                    </script>

                                    {* Time *}
                                {else if $field.type === "time"}
                                    <p class="text {if $field.required}required{/if}">
                                        <input type="text" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
                                               value="{if !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}" />
                                    </p>
                                    <script type="text/javascript">
                                        $('#rkr_{$field.id|escape:'htmlall':'UTF-8'}').datetimepicker({
                                            lang: 'fr',
                                            datepicker: false,
                                            format: 'H:i',
                                            step: 30
                                        });
                                    </script>

                                    {* DateTime *}
                                {else if $field.type === "datetime"}
                                    <p class="text {if $field.required}required{/if}">
                                        <input type="text" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}"
                                               value="{if !empty($response.reponse)}{$response.reponse|escape:'htmlall':'UTF-8'}{/if}" />
                                    </p>
                                    <script type="text/javascript">
                                        $('#rkr_{$field.id|escape:'htmlall':'UTF-8'}').datetimepicker({
                                            lang: 'fr',
                                            format: 'd-m-Y H:i',
                                            step: 30
                                        });
                                    </script>


                                {else if $field.type === "checkbox"}
                                    <p class="checkbox {if $field.required}required{/if}">
                                        <input type="checkbox" name="rkr_{$field.id|escape:'htmlall':'UTF-8'}" id="rkr_{$field.id|escape:'htmlall':'UTF-8'}" value="1"
                                               autocomplete="off" {if !empty($response.reponse)}checked="checked"{/if} />
                                    </p>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
            <input type="hidden" name="submitUpdateResponses" value="1" />
            <input type="submit" id="submitCustomerNote" class="button" value="Enregistrer" style="float:left;margin-top:5px">
        </form>
    </div>
{/if}
