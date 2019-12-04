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
*  @author Snegurka <site@web-esse.ru>
*  @copyright  2007-2019 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $bulletinboardis17 != 1}
{capture name=path}<a href="{$link->getPageLink('my-account.php', true)|escape:'htmlall':'UTF-8'}">{l s='My account' mod='bulletinboard'}</a><span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>{l s='My products' mod='bulletinboard'}{/capture}
{/if}
<h1 class="block_header corners">{l s='Customer products' mod='bulletinboard'}: {$seller->name|escape:'htmlall':'UTF-8'}</h1>
{*
{include file="$tpl_dir./errors.tpl"}
*}
{if $seller->id_ws_seller}

{if !$moderat}
<p class="warning alert alert-warning" style="padding:6px 12px;">{l s='After adding or changing the product will be resent for moderation.' mod='bulletinboard'}</p>
{/if}

        {if isset($form_errors) && count($form_errors)}
            <div class="error alert alert-danger">
                <ul>
                    <li>{implode('</li><li>', $form_errors)|escape:'quotes':'UTF-8'}</li>
                </ul>
            </div>
        {/if}
 
        <form class="form-horizontal bootstrap MultiFile-intercepted customer_form_add" action="" method="POST" enctype="multipart/form-data">
             {if $bulletinboardis17 != 1}<div class="form-wrapper panel product-tab">{else}<div class="form-fields">{/if}
             {if $bulletinboardis17 != 1}<h3 class="tab"> <i class="icon-info"></i> Information</h3>{/if}
               <div class="form-group  hide">
                    {if isset($id_product)}<input type="hidden" name="id_product" id="id_product" value="{$id_product|intval}">{/if}
                    {if $show_type}<input type="hidden" id="is_virtual" name="is_virtual" value="{$edit_product->is_virtual|intval}">{/if}
               </div>
               
               <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
                   <label class="col-md-3 form-control-label required control-label col-lg-3">
                          {l s='Name' mod='bulletinboard'}
                   </label>
                            
                   <div class="col-md-6">

                                    {foreach $languages as $language}
                                        {assign var='value_text' value=$edit_product->name[$language.id_lang]}
                                        {if $languages|count > 1}
                                        <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                            <div class="col-lg-9">
                                        {/if}
                                                <input type="text"
                                                    id="name_{$language.id_lang|intval}"
                                                    name="name_{$language.id_lang|intval}"
                                                    class="form-control"
                                                    value="{$value_text|escape:'html':'UTF-8'}"
                                                    />

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
                      </div>
                  </div>
                 {if $use_description} 
                  <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
                   <label class="col-md-3 form-control-label control-label col-lg-3">
                          {l s='Description short' mod='bulletinboard'}
                   </label>
                            
                   <div class="col-lg-9 ">

                                    {foreach $languages as $language}
                                        {assign var='value_text' value=$edit_product->description_short[$language.id_lang]}
                                        {if $languages|count > 1}
                                        <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                            <div class="col-lg-9">
                                        {/if}
                                                <textarea
                                                    id="description_short_{$language.id_lang|intval}"
                                                    name="description_short_{$language.id_lang|intval}"
                                                    class="textarea-autosize"
                                                    >{$value_text|escape:'html':'UTF-8'}</textarea>    

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
                      </div>
                  </div>
                    {/if}
                    {if $use_description}
                  <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
                   <label class="col-md-3 form-control-label control-label col-lg-3">
                          {l s='Description' mod='bulletinboard'}
                   </label>
                            
                   <div class="col-lg-9 ">

                                    {foreach $languages as $language}
                                        {assign var='value_text' value=$edit_product->description[$language.id_lang]}
                                        {if $languages|count > 1}
                                        <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                            <div class="col-lg-9">
                                        {/if}
                                                <textarea
                                                    id="description_{$language.id_lang|intval}"
                                                    name="description_{$language.id_lang|intval}"
                                                    class="textarea-autosize"
                                                    >{$value_text|escape:'html':'UTF-8'}</textarea>    

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
                      </div>
                  </div>
                  {/if}
                 {if $show_type} 
                 <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
        <label class="control-label col-lg-3" for="simple_product">
             {l s='Type' mod='bulletinboard'}
        </label>
        <div class="col-lg-9">
            <div class="radio">
                <label for="simple_product">
                    <input type="radio" name="type_product" id="simple_product" value="0" {if !$edit_product->is_virtual}checked="checked"{/if}>
                    {l s='Standard product' mod='bulletinboard'}</label>
            </div>
            <div class="radio">
                <label for="virtual_product">
                    <input type="radio" name="type_product" id="virtual_product" value="2" {if $edit_product->is_virtual}checked="checked"{/if}>
                    {l s='Virtual product (services, booking, downloadable products, etc.)' mod='bulletinboard'}</label>
            </div>
        </div>
    </div>
                 {/if}
                  
                  
                 {if $use_reference} 
                 <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
                      <label class="col-md-3 form-control-label control-label col-lg-3">
                             {l s='Reference' mod='bulletinboard'}
                      </label>
                      <div class="col-lg-9 ">
                            <input type="text" name="reference" id="reference" value="{$edit_product->reference|escape:'htmlall':'UTF-8'}" class="form-control">
                      </div> 
                 </div>
                 {/if}
                  {if $use_category}
                 <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
                   <label class="col-md-3 form-control-label control-label col-lg-3 required">
                          {l s='Category' mod='bulletinboard'}
                   </label>
                            
                   <div class="col-lg-9 ">
                    {if isset($categories.children) && !empty($categories.children)}
                        <select multiple name='categories[]' name='categories' >
                        {foreach from=$categories.children item=category}
                            <option class="categories_t" value="{$category.id|intval}" {if !$category.allow}disabled{/if}  {if isset($edit_product->categories_ids) && in_array($category.id, $edit_product->categories_ids)} selected{/if} attr-name='{$category.name|escape:'html':'UTF-8'}'>{$category.name|escape:'html':'UTF-8'}</option>                            
                            {if $category.children|@count > 0}{include file="modules/bulletinboard/views/templates/front/bulletinboard_branch.tpl" categories=$category.children nsbp='----'}{/if}
                        {/foreach}
                        </select>
                    {/if}
                      </div>
                  </div>
                  
                  <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
        <div class="col-lg-1"><span class="pull-right">
</span></div>
        <label class="control-label col-lg-2" for="id_category_default">
            <span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="The default category is the main category for your product, and is displayed by default.">
                {l s='Default category' mod='bulletinboard'}
            </span>
        </label>
        <div class="col-lg-5">
            <select id="id_category_default" name="id_category_default">
                        {foreach from=$categories.children item=category}
                            {if $category.allow}<option class="categories_t" value="{$category.id|intval}" {if isset($edit_product->id_category_default) && $category.id == $edit_product->id_category_default} selected{/if} attr-name='{$category.name|escape:'html':'UTF-8'}'>{$category.name|escape:'html':'UTF-8'}</option>{/if}                           
                        {/foreach}
                            </select>
        </div>
    </div>
                  {/if}
                  {if $use_condition}
                  <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
                   <label class="col-md-3 form-control-label control-label col-lg-3 required">
                          {l s='Condition' mod='bulletinboard'}
                   </label>
                            
                   <div class="col-lg-9 ">
                    <select name="condition" id="condition">
                        <option value="new" selected="selected">{l s='New' mod='bulletinboard'}</option>
                        <option value="used">{l s='Used' mod='bulletinboard'}</option>
                        <option value="refurbished">{l s='Refurbished' mod='bulletinboard'}</option>
                    </select>
                   </div>
                  </div>
                  {/if}
                  {if $use_quantity}
                  <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
                      <label class="col-md-3 form-control-label control-label col-lg-3 required">
                             {l s='Quantity' mod='bulletinboard'}
                      </label>
                      <div class="col-lg-9 ">
                            <input type="text" name="quantity" id="quantity" value="{$edit_product->quantity|escape:'htmlall':'UTF-8'}" class="form-control" required="required">
                      </div>
                 </div>
                  {/if}
                 <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
                      <label class="col-md-3 form-control-label control-label col-lg-3 required">
                             {l s='Price' mod='bulletinboard'}
                      </label>
                      <div class="col-lg-9 ">
                            <input type="text" name="price" id="price" value="{$edit_product->price|escape:'htmlall':'UTF-8'}" class="form-control" required="required">
                      </div>
                 </div>
                 
                 {if $use_taxes}
               <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
                     <label class="col-md-3 form-control-label control-label col-lg-3">
                         {l s='Tax rule' mod='bulletinboard'}
                     </label>
                     <div class="col-lg-9 ">
                          <select name="tax_rule" class=" fixed-width-xl" id="tax_rule">
                                <option value="0">{l s='Without tax' mod='bulletinboard'}</option>
                                {if !empty($tax_rules) && $tax_rules}
                                {foreach from=$tax_rules item=a_tax}
                                <option value="{$a_tax.id_tax_rules_group|intval}" {if isset($edit_product->id) && $a_tax.id_tax_rules_group == $edit_product->id_tax_rules_group}selected{/if}>{$a_tax.name|escape:'html':'UTF-8'}</option>
                                {/foreach}
                                {/if}
                          </select>
                     </div>
              </div>

               <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
                    <label class="col-md-3 form-control-label control-label col-lg-3">
                        {l s='Final price' mod='bulletinboard'}
                    </label>
                                <div class="col-lg-9 ">
                            
                                           <div class="calc_field_final_price">{$edit_product->price|escape:'htmlall':'UTF-8'}</div>

                                                                         
                             </div>
                            
                 </div>                 
                 {/if}
                 
                  {if $use_options}
                  <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
                      <label class="col-md-3 form-control-label control-label col-lg-3">
                             {l s='Options' mod='bulletinboard'}
                      </label>
                    <div class="col-lg-9">
                    <div class="checkbox">
                        <label for="available_for_order">
                            <input type="checkbox" name="available_for_order" id="available_for_order" value="1" checked="checked">
                            {l s='Available for order' mod='bulletinboard'}</label>
                    </div>
                    <div class="checkbox">
                        <label for="show_price">
                            <input type="checkbox" name="show_price" id="show_price" value="1" checked="checked" disabled="disabled">
                            {l s='Show price' mod='bulletinboard'}</label>
                    </div>
                    <div class="checkbox">
                        <label for="online_only">
                            <input type="checkbox" name="online_only" id="online_only" value="1">
                            {l s='Online only (not sold in your retail store)' mod='bulletinboard'}</label>
                    </div>
                </div>
                 </div>
                  {/if}
                  
               {if $use_manufacturer}   
              <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
                     <label class="col-md-3 form-control-label control-label col-lg-3">
                         {l s='Manufacturer' mod='bulletinboard'}
                     </label>
                     <div class="col-lg-9 ">
                          <select name="id_manufacturer" class=" fixed-width-xl" id="id_manufacturer">
                                <option value="0">{l s='-' mod='bulletinboard'}</option>
                                {if !empty($manufacturers) && $manufacturers}
                                {foreach from=$manufacturers item=a_manufacturer}
                                <option value="{$a_manufacturer.id_manufacturer|intval}" {if isset($edit_product->id) && $a_manufacturer.id_manufacturer == $edit_product->id_manufacturer}selected{/if}>{$a_manufacturer.name|escape:'html':'UTF-8'}</option>
                                {/foreach}
                                {/if}
                          </select>
                     </div>
              </div>
                {/if} 
                <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
                        <label class="col-md-3 form-control-label control-label col-lg-3">
                                 {l s='Image' mod='bulletinboard'}
                        </label>
                        <div class="col-lg-9 ">
                        <input name="image_product[]" type="file" class="multi form-txt" />                            
                       
                                        {if isset($image_product) && sizeof($image_product)}
                                        <ul id="image_preview_list">
                                        {foreach from=$image_product item=p_image}
                                        <li>
                                        <img src="{$link->getImageLink($link_rewrite, $p_image.id_image, 'medium_default')|escape:'htmlall':'UTF-8'}"  />
                                        <h5><a href="{$link->getModuleLink('bulletinboard', 'boardcustomer', ['img_del'=>$p_image.id_image, 'edit' => 1, 'id_product' => $id_product], true)|escape:'htmlall':'UTF-8'}">{l s='Delete' mod='bulletinboard'}</a></h5>
                                        </li>
                                        {/foreach}
                                        </ul>
                                        {/if}
                       
                       </div>
                </div>
                
                    <div id="is_virtual_file_product" {if !$edit_product->is_virtual}class="hidden"{/if}>
                                    
                    <div class="form-group {if $bulletinboardis17 == 1} row{/if}">
                    <label id="virtual_product_file_label" for="virtual_product_file" class="col-md-3 form-control-label control-label col-lg-3">
                        <span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="Upload a file from your computer (8.00 MB max.)">
                            {l s='File' mod='bulletinboard'}
                        </span>
                    </label>
                    <div class="col-lg-9">
                    <input id="virtual_product_file_uploader" type="file" name="virtual_product_file_uploader" class="form-txt">


                    <p class="help-block">{l s='Upload a file from your computer (8.00 MB max.)' mod='bulletinboard'}</p>
                    </div>
                    </div>
                    </div>   
                                 
                
        <div class="form-group  {if $bulletinboardis17 == 1} row{/if}">
        <div class="col-lg-1"><span class="pull-right">
        </span></div>
        <label class="control-label col-lg-2">
            {l s='Enabled' mod='bulletinboard'}
        </label>
        <div class="col-lg-9">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="active" id="active_on" value="1" {if $edit_product->active} checked="checked"{/if}>
                <label for="active_on" class="radioCheck">
                   {l s='Yes' mod='bulletinboard'}
                </label>
                <input type="radio" name="active" id="active_off" value="0" {if !$edit_product->active} checked="checked"{/if}>
                <label for="active_off" class="radioCheck">
                    {l s='No' mod='bulletinboard'}
                </label>
            </span>
        </div>
        </div>               
         </div>
                      <div class="panel-footer clearfix">
                      <button type="submit" value="1" id="form_submit_btn" name="submitAddProduct" class="btn btn-default pull-right">
                            <i class="save"></i> {l s='Save' mod='bulletinboard'}
                        </button>
                     </div>
    </form>

{/if}