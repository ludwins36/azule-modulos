{*
* HarDevel LLC.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://hardevel.com/License.txt
*
* @category  HarDevel
* @package   HarDevel_multicurrency
* @author    HarDevel
* @copyright Copyright (c) 2012 - 2015 HarDevel LLC. (http://hardevel.com)
* @license   http://hardevel.com/License.txt
*}
<script type="text/javascript" src="{$path|escape:'html':'UTF-8'}views/js/hardevelmulticurrency.js"></script>
<link href="{$path|escape:'html':'UTF-8'}views/css/hardevelmulticurrency.css" rel="stylesheet" type="text/css"/>
<input type="hidden" name="secure" id="hardevel_secure" value="{$secure|escape:'html':'UTF-8'}">
<div class="hardevel_currency">
    <form id="hardevel_currency_filter" class="clearfix">
        <div>
        <label>{l mod='hardevelmulticurrency' s='Category:'}</label>
        <select name="id_category">
            <option value="0">{l mod='hardevelmulticurrency' s='choose'}</option>
            {foreach $categories as $category}
            <option value="{$category.id_category|intval}">{for $i=0 to $category.level_depth}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{/for}{$category.name|escape:'html'}</option>
            {/foreach}
        </select>
        </div>
        <div>
        <label>{l mod='hardevelmulticurrency' s='Manufacturer:'}</label>
        <select name="id_manufacturer">
            <option value="0">choose</option>
            {foreach $manufacturers as $manufacturer}
            <option value="{$manufacturer.id_manufacturer|intval}">{$manufacturer.name|escape:'htmlall'}</option>
            {/foreach}
        </select>
        </div>
        <div>
        <label>{l mod='hardevelmulticurrency' s='Supplier:'}</label>
        <select name="id_supplier">
            <option value="0">choose</option>
            {foreach $suppliers as $supplier}
                <option value="{$supplier.id_supplier|intval}">{$supplier.name|escape:'html':'UTF-8'}</option>
            {/foreach}
        </select>
        </div>
        <div>
            <label>{l mod='hardevelmulticurrency' s='Active'}</label>
            <input type="checkbox" name="active" value="1" checked="checked">
        </div>
        <div>
            <label>{l mod='hardevelmulticurrency' s='Combinations'}</label>
            <input type="checkbox" name="combinations" value="1" checked="checked">
        </div>
    </form>
    <div id="hardevel_products">
        <form>
            <fieldset>
                <legend>{l mod='hardevelmulticurrency' s='Change retail prices in all product in list'}</legend>
                <div style="padding-left: 250px;">
                {l mod='hardevelmulticurrency' s='(Current product price)'} <input id="price_change" value="+0.0"/> <button type="button" id="update_prices">Update all</button>
                <br />
                <em> value example(* 1.2) or (+1.1) or (-2.2)  </em>
                <br />
                <em class="error">{l mod='hardevelmulticurrency' s='Don\'t save if something wrong, just update page'}</em>
                </div>
            </fieldset>
        </form>
        <form>
            <fieldset>
                <legend>{l mod='hardevelmulticurrency' s='Add discount to all products in list'}</legend>
                <div class="field_group">
                    <label>Discount:</label><input id="discount_change" value="0.00"/><br />
                </div>
                <div class="field_group">
                    <label>Type:</label> <select id="discount_type"><option value="percentage">percentage</option><option value="amount">amount</option></select>
                </div>
                <div class="field_group" style="display:none;" id="amount_currency">    
                <label>Amount currency:</label><select id="discount_currency"></select>
                </div>    
                <label>&nbsp;</label><button type="button" id="update_discounts">Update all</button><br />
                <label>&nbsp;</label><em class="error">{l mod='hardevelmulticurrency' s='Don\'t save if something wrong, just update page'}</em></button>
            </fieldset>
        </form>
        
        <form id="products">
            <input type="submit" name="submit" value="{l mod='hardevelmulticurrency' s='Save'}">
            <table>
                <thead>
                    <tr>
                        <th class="id_product">{l mod='hardevelmulticurrency' s='ID'}</th>
                        <th class="photo">{l mod='hardevelmulticurrency' s='Photo'}</th>
                        <th class="name">{l mod='hardevelmulticurrency' s='Name'}</th>
                        <th class="reference">{l mod='hardevelmulticurrency' s='Reference'}</th>
                        <th class="price">{l mod='hardevelmulticurrency' s='Wholesale price'}</th>
                        <th class="currency">{l mod='hardevelmulticurrency' s='Wholesale currency'}</th>
                        <th class="price">{l mod='hardevelmulticurrency' s='Price'}</th>
                        <th class="currency">{l mod='hardevelmulticurrency' s='Currency'}</th>
                        <th class="discont">{l mod='hardevelmulticurrency' s='Discont'}</th>
                        <th class="discont_type">{l mod='hardevelmulticurrency' s='Discont type'}</th>
                        <th class="discont_currency">{l mod='hardevelmulticurrency' s='Discont currency'}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <input type="submit" name="submit" value="{l mod='hardevelmulticurrency' s='Save'}">
        </form>
    </div>
    <div class="error" style="display: none;">
        {l mod='hardevelmulticurrency' s='There\'s no products'}
    </div>
</div>