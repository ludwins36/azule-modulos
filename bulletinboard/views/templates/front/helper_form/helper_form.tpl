{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2019 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">

if (!id_language)
    var id_language = Number(1);

function hideOtherLanguage(id)
{
    $('.translatable-field').hide();
    $('.lang-' + id).show();

    updateCurrentText();
}


function updateCurrentText()
{
    $('#current_product').html($('#name_' + id_language).val());
}


</script>

 <form class="form-horizontal col-lg-10 col-md-9" action="{if isset($action)}{$action|escape:'quotes':'UTF-8'}{/if}" method="POST" enctype="multipart/form-data">
        {foreach $fields as $f => $fieldset}
        {foreach $fieldset.form as $key => $field}
        {if $key == 'input'}
             <div class="form-wrapper">
             {foreach $field as $input}
              {block name="input_row"}
               <div class="form-group {if $input.type == 'hidden'} hide{/if}">
                  {if $input.type == 'hidden'}
                            <input type="hidden" name="{$input.name|escape:'html':'UTF-8'}" id="{$input.name|escape:'html':'UTF-8'}" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
                  {else}
                            {block name="label"}
                                {if isset($input.label)}
                                    <label class="control-label col-lg-3{if isset($input.required) && $input.required && $input.type != 'radio'} required{/if}">
                                        {if isset($input.hint)}
                                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{if is_array($input.hint)}
                                                    {foreach $input.hint as $hint}
                                                        {if is_array($hint)}
                                                            {$hint.text|escape:'html':'UTF-8'}
                                                        {else}
                                                            {$hint|escape:'html':'UTF-8'}
                                                        {/if}
                                                    {/foreach}
                                                {else}
                                                    {$input.hint|escape:'html':'UTF-8'}
                                                {/if}">
                                        {/if}
                                        {$input.label|escape:'html':'UTF-8'}
                                        {if isset($input.hint)}
                                        </span>
                                        {/if}
                                    </label>
                                {/if}
                            {/block}
                            {block name="field"}
                                <div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if} {if !isset($input.label)}col-lg-offset-3{/if}">
                            {block name="input"}
   {if $input.type == 'text' || $input.type == 'tags'}
                                    {if isset($input.lang) AND $input.lang}
                                    {if $languages|count > 1}
                                    <div class="form-group">
                                    {/if}
                                    {foreach $languages as $language}
                                        {assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
                                        {if $languages|count > 1}
                                        <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                            <div class="col-lg-9">
                                        {/if}
                                                {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                                <div class="input-group{if isset($input.class)} {$input.class|escape:'htmlall':'UTF-8'}{/if}">
                                                {/if}
                                                {if isset($input.maxchar)}
                                                <span id="{if isset($input.id)}{$input.id|intval}_{$language.id_lang|intval}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|intval}{/if}_counter" class="input-group-addon">
                                                    <span class="text-count-down">{$input.maxchar|intval}</span>
                                                </span>
                                                {/if}
                                                {if isset($input.prefix)}
                                                    <span class="input-group-addon">
                                                      {$input.prefix|escape:'htmlall':'UTF-8'}
                                                    </span>
                                                    {/if}
                                                <input type="text"
                                                    id="{if isset($input.id)}{$input.id|intval}_{$language.id_lang|intval}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|intval}{/if}"
                                                    name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|intval}"
                                                    class="form-control {if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
                                                    value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                                                    onkeyup="if (isArrowKey(event)) return ;"
                                                    {if isset($input.size)} size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                                                    {if isset($input.maxchar)} data-maxchar="{$input.maxchar|escape:'htmlall':'UTF-8'}"{/if}
                                                    {if isset($input.maxlength)} maxlength="{$input.maxlength|escape:'htmlall':'UTF-8'}"{/if}
                                                    {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                                    {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                                    {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                                    {if isset($input.required) && $input.required} required="required" {/if}
                                                    {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder|escape:'htmlall':'UTF-8'}"{/if} />
                                                    {if isset($input.suffix)}
                                                    <span class="input-group-addon">
                                                      {$input.suffix|escape:'htmlall':'UTF-8'}
                                                    </span>
                                                    {/if}
                                                {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                                </div>
                                                {/if}
                                        {if $languages|count > 1}
                                            </div>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                                    {$language.iso_code|escape:'htmlall':'UTF-8'}
                                                    <i class="icon-caret-down"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    {foreach from=$languages item=language}
                                                    <li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'htmlall':'UTF-8'}</a></li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                        </div>
                                        {/if}
                                    {/foreach}
                                    {if isset($input.maxchar)}
                                    <script type="text/javascript">
                                    function countDown($source, $target) {
                                        var max = $source.attr("data-maxchar");
                                        $target.html(max-$source.val().length);

                                        $source.keyup(function(){
                                            $target.html(max-$source.val().length);
                                        });
                                    }

                                    $(document).ready(function(){
                                    {foreach from=$languages item=language}
                                        countDown($("#{if isset($input.id)}{$input.id|intval}_{$language.id_lang|intval}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|intval}{/if}"), $("#{if isset($input.id)}{$input.id|intval}_{$language.id_lang|intval}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|intval}{/if}_counter"));
                                    {/foreach}
                                    });
                                    </script>
                                    {/if}
                                    {if $languages|count > 1}
                                    </div>
                                    {/if}
                                    {else}
                                        {assign var='value_text' value=$fields_value[$input.name]}
                                        {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                        <div class="input-group{if isset($input.class)} {$input.class|escape:'htmlall':'UTF-8'}{/if}">
                                        {/if}
                                        {if isset($input.maxchar)}
                                        <span id="{if isset($input.id)}{$input.id|intval}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar|escape:'htmlall':'UTF-8'}</span></span>
                                        {/if}
                                        {if isset($input.prefix)}
                                        <span class="input-group-addon">
                                          {$input.prefix|escape:'htmlall':'UTF-8'}
                                        </span>
                                        {/if}
                                        <input type="text"
                                            name="{$input.name|escape:'htmlall':'UTF-8'}"
                                            id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                                            value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                                            class="form-control{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
                                            {if isset($input.size)} size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                                            {if isset($input.maxchar)} data-maxchar="{$input.maxchar|escape:'htmlall':'UTF-8'}"{/if}
                                            {if isset($input.maxlength)} maxlength="{$input.maxlength|escape:'htmlall':'UTF-8'}"{/if}
                                            {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                            {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                            {if isset($input.required) && $input.required } required="required" {/if}
                                            {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder|escape:'htmlall':'UTF-8'}"{/if}
                                            />
                                        {if isset($input.suffix)}
                                        <span class="input-group-addon">
                                          {$input.suffix|escape:'htmlall':'UTF-8'}
                                        </span>
                                        {/if}

                                        {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                        </div>
                                        {/if}
                                        {if isset($input.maxchar)}
                                        <script type="text/javascript">
                                        function countDown($source, $target) {
                                            var max = $source.attr("data-maxchar");
                                            $target.html(max-$source.val().length);

                                            $source.keyup(function(){
                                                $target.html(max-$source.val().length);
                                            });
                                        }
                                        $(document).ready(function(){
                                            countDown($("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}_counter"));
                                        });
                                        </script>
                                        {/if}
                                    {/if}
  {*{elseif $input.type == 'categories_select'}
                    <select name="{$input.name|escape:'htmlall':'UTF-8'}">
                         {foreach $input.options.query.children AS $category}
                            <option class="categories_t" value="{$category.id}"  {if isset($post.categories) && in_array($category.id, $post.categories)} selected{else}{if isset($aProductCategoriesIds) && in_array($category.id, $aProductCategoriesIds)} selected{/if}{/if} attr-name='{$category.name|escape:'html':'UTF-8'}'>{$category.name|escape:'html':'UTF-8'}</option>                            
                          {if $category.children|@count > 0}
                          {include file="$addprod_branch" node=$child}
                          {/if}
                         {/foreach}
                        </select>
                    </select>
      *}              
   {* {elseif $input.type == 'categories'}
               {$categories_tree}    *}             
   {elseif $input.type == 'select'}
                                    {if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
                                        {$input.empty_message|escape:'htmlall':'UTF-8'}
                                        {$input.required = false}
                                        {$input.desc = null}
                                    {else}
                                        <select name="{$input.name|escape:'htmlall':'UTF-8'}"
                                                class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if} fixed-width-xl"
                                                id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                                                {if isset($input.multiple) && $input.multiple} multiple="multiple"{/if}
                                                {if isset($input.size)} size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                                                {if isset($input.onchange)} onchange="{$input.onchange|escape:'htmlall':'UTF-8'}"{/if}
                                                {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}>
                                            {if isset($input.options.default)}
                                                <option value="{$input.options.default.value|escape:'htmlall':'UTF-8'}">{$input.options.default.label|escape:'htmlall':'UTF-8'}</option>
                                            {/if}
                                            {if isset($input.options.optiongroup)}
                                                {foreach $input.options.optiongroup.query AS $optiongroup}
                                                    <optgroup label="{$optiongroup[$input.options.optiongroup.label]|escape:'htmlall':'UTF-8'}">
                                                        {foreach $optiongroup[$input.options.options.query] as $option}
                                                            <option value="{$option[$input.options.options.id]|escape:'htmlall':'UTF-8'}"
                                                                {if isset($input.multiple)}
                                                                    {foreach $fields_value[$input.name] as $field_value}
                                                                        {if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
                                                                    {/foreach}
                                                                {else}
                                                                    {if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
                                                                {/if}
                                                            >{$option[$input.options.options.name]|escape:'htmlall':'UTF-8'}</option>
                                                        {/foreach}
                                                    </optgroup>
                                                {/foreach}
                                            {else}
                                                {foreach $input.options.query AS $option}
                                                    {if is_object($option)}
                                                        <option value="{$option->$input.options.id|escape:'htmlall':'UTF-8'}"
                                                            {if isset($input.multiple)}
                                                                {foreach $fields_value[$input.name] as $field_value}
                                                                    {if $field_value == $option->$input.options.id}
                                                                        selected="selected"
                                                                    {/if}
                                                                {/foreach}
                                                            {else}
                                                                {if $fields_value[$input.name] == $option->$input.options.id}
                                                                    selected="selected"
                                                                {/if}
                                                            {/if}
                                                        >{$option->$input.options.name|escape:'htmlall':'UTF-8'}</option>
                                                    {elseif $option == "-"}
                                                        <option value="">-</option>
                                                    {else}
                                                        <option value="{$option[$input.options.id]|escape:'htmlall':'UTF-8'}"
                                                            {if isset($input.multiple)}
                                                                {foreach $fields_value[$input.name] as $field_value}
                                                                    {if $field_value == $option[$input.options.id]}
                                                                        selected="selected"
                                                                    {/if}
                                                                {/foreach}
                                                            {else}
                                                                {if $fields_value[$input.name] == $option[$input.options.id]}
                                                                    selected="selected"
                                                                {/if}
                                                            {/if}
                                                        >{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>

                                                    {/if}
                                                {/foreach}
                                            {/if}
                                        </select>
                                    {/if} 
    {elseif $input.type == 'calc'}
                                        <div class="calc_field_{$input.name|escape:'quotes':'UTF-8'}"></div>
                                        {if isset($input.exp_fields) && count($input.exp_fields)}
                                        <script>
                                            $('.form-wrapper')
                                                .find('[name="{implode('"], [name="', $input.exp_fields)|escape:'quotes':'UTF-8'}"]')
                                                .live('keyup change', function () {
                                                var fieldset = $('.form-wrapper');
                                                var expression = "{$input.expression|escape:'quotes':'UTF-8'}";
                                                var fields = {$input.fields|json_encode|escape:'quotes':'UTF-8'};
                                                var format = "{if isset($input.return_format)}{$input.return_format|escape:'quotes':'UTF-8'}{else}string{/if}";
                                                $.each(fields, function (index, field) {
                                                    var value = '';
                                                    var founded_field = null;
                                                    if (field.lang)
                                                        founded_field = fieldset.find('[name="'+field.name+'['+id_lang+']"]');
                                                    else
                                                        founded_field = fieldset.find('[name="'+field.name+'"]');

                                                    if (founded_field.is('select'))
                                                    {
                                                        if (field.name == 'tax_rule')
                                                        {
                                                            var rate = founded_field.find('option:selected').text().match(/.*\(([0-9]+)\%\)/);
                                                            value = 0;
                                                            if (rate && rate[1] != 'undefined')
                                                                value = rate[1];
                                                        }
                                                        else
                                                            value = founded_field.val();
                                                    }
                                                    else if(founded_field.is('input[type=radio], input[type=checkbox]'))
                                                        value = founded_field.find(':checked').val();
                                                    else
                                                        value = founded_field.val();
                                                    if (field.type == 'price')
                                                        value = parseFloat((value ? value : 0));
                                                    else if(field.type == 'int')
                                                        value = parseInt((value ? value : 0));
                                                    expression = expression.split(field.name).join(value);
                                                });
                                                console.log(expression);
                                                var return_value  = eval(expression);
                                                if (format == 'price')
                                                    return_value = formatCurrency(parseFloat(return_value), currencyFormat, currencySign, currencyBlank)
                                                $('.calc_field_{$input.name|escape:'quotes':'UTF-8'}').html(return_value);
                                            });
                                            $('.form-wrapper').find('[name="{implode('"], [name="', $input.exp_fields)|escape:'quotes':'UTF-8'}"]').trigger('keyup');
                                        </script>
                                        {/if}
   {elseif $input.type == 'textarea'}
                                      {assign var=use_textarea_autosize value=true}
                                      {if isset($input.lang) AND $input.lang}
                                        {foreach $languages as $language}
                                        {if $languages|count > 1}
                                        <div class="form-group translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
                                        <div class="col-lg-9">
                                        {/if}
                                            <textarea {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'htmlall':'UTF-8'}{/if}">{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}</textarea>
                                        {if $languages|count > 1}
                                        </div>
                                        <div class="col-lg-2">
                                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                                {$language.iso_code|escape:'htmlall':'UTF-8'}
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                {foreach from=$languages item=language}
                                                <li>
                                                    <a href="javascript:hideOtherLanguage({$language.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$language.name|escape:'htmlall':'UTF-8'}</a>
                                                </li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                        </div>
                                        {/if}
                                        {/foreach}

                                      {else}
                                            <textarea {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name|escape:'htmlall':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}" {if isset($input.cols)}cols="{$input.cols|escape:'htmlall':'UTF-8'}"{/if} {if isset($input.rows)}rows="{$input.rows|escape:'htmlall':'UTF-8'}"{/if} class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'htmlall':'UTF-8'}{/if}">{$fields_value[$input.name]|escape:'html':'UTF-8'}</textarea>
                                      {/if}
                                    {elseif $input.type == 'image'}
                                    <input name="image_product[]" type="file" class="multi form-txt" />
                                        {*
                                        {foreach from=$fields_value.image_product item=image}
                                        {$image|escape:'quotes':'UTF-8'}
                                        {/foreach}
                                        
                                        <div class="image_preview preview_{$input.name|escape:'quotes':'UTF-8'}">
                                            {if is_array($fields_value[$input.name]) && (isset($field_value.image_product))}
                                                {$fields_value.image_product}
                                            {/if}
                                        </div>
                                        *}
                                        {if isset($fields_value.image_product) && sizeof($fields_value.image_product)}
                                        <ul id="image_preview_list">
                                        {foreach from=$fields_value.image_product item=p_image}
                                        <li>
                                        <img src="{$link->getImageLink($fields_value.ads_product->link_rewrite, $p_image.id_image, 'medium_default')|escape:'htmlall':'UTF-8'}"  />
                                        <h5><a href="{$link->getModuleLink('bulletinboard', 'boardcustomer', ['img_del'=>$p_image.id_image, 'edit' => 1, 'id_product' => $fields_value.id_product], true)|escape:'htmlall':'UTF-8'}">{l s='Delete' mod='bulletinboard'}</a></h5>
                                        </li>
                                        {/foreach}
                                        </ul>
                                        {/if}
                                        
                                        <div class="clearfix"></div>

                                    {elseif $input.type == 'file'}
                                    {$input.file|escape:'quotes':'UTF-8'}
                                    {/if}
                              {/block}
                             </div>
                            {/block}
                {/if}
             </div>
             {/block}
             {/foreach}
             </div>
        {/if}

        {/foreach}
        
            {block name="footer"}
            {capture name='form_submit_btn'}{counter name='form_submit_btn'}{/capture}
                {if isset($fieldset['form']['submit']) || isset($fieldset['form']['buttons'])}
                    <div class="panel-footer clearfix">
                        {if isset($fieldset['form']['submit']) && !empty($fieldset['form']['submit'])}
                        <button type="submit" value="1" id="{if isset($fieldset['form']['submit']['id'])}{$fieldset['form']['submit']['id']|escape:'htmlall':'UTF-8'}{else}form_submit_btn{/if}{if $smarty.capture.form_submit_btn > 1}_{($smarty.capture.form_submit_btn - 1)|intval}{/if}" name="{if isset($fieldset['form']['submit']['name'])}{$fieldset['form']['submit']['name']|escape:'htmlall':'UTF-8'}{/if}{if isset($fieldset['form']['submit']['stay']) && $fieldset['form']['submit']['stay']}AndStay{/if}" class="{if isset($fieldset['form']['submit']['class'])}{$fieldset['form']['submit']['class']|escape:'htmlall':'UTF-8'}{else}btn btn-default pull-right{/if}">
                            <i class="{if isset($fieldset['form']['submit']['icon'])}{$fieldset['form']['submit']['icon']|escape:'htmlall':'UTF-8'}{else}process-icon-save{/if}"></i> {$fieldset['form']['submit']['title']|escape:'htmlall':'UTF-8'}
                        </button>
                        {/if}
                        {if isset($show_cancel_button) && $show_cancel_button}
                        <a href="{$back_url|escape:'html':'UTF-8'}" class="btn btn-default" onclick="window.history.back();">
                            <i class="process-icon-cancel"></i> {l s='Cancel' mod='bulletinboard'}
                        </a>
                        {/if}
                    </div>
                {/if}
            {/block}
        
        {/foreach}
        
    </form>
