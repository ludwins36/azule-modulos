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
<div {if $bulletinboardis17}id="js_form_ws_sellers" style="display: block;"{else}class="panel product-tab"{/if}>
    {if $bulletinboardis17}
    <h2 class="title-products">{l s='Sellers' mod='bulletinboard'}</h2>
    {else}
    <h3 class="tab">{l s='Sellers' mod='bulletinboard'}</h3>
    {/if}
    {if $seller_list}
    {if $id_seller}
        <div class="row form-group">
            <div class="col-md-4 current_seller">
            <h4>{l s='Current seller: ' mod='bulletinboard'}</h3>
            <p><i class="icon-cart-arrow-down"></i>{$current_seller->name} </p>
            <p><i class="icon-user"></i><a href="{$current_seller->customer_url}">{$current_customer->firstname} {$current_customer->lastname}</a> </p>
            </div>
        </div>   
        <hr> 
    {/if}
    <div class="row form-group">
    {if $bulletinboardis17}<div class="col-md-4">{/if}
    <label class="control-label col-lg-2">{l s='Select seller' mod='bulletinboard'}</label>
      {if $bulletinboardis17}
      <fieldset class="form-group">
      {else}
      <div class="col-lg-3">
      {/if}
      <select id="form_ws_seller" name="form_ws_seller" {if $bulletinboardis17} data-toggle="select2" data-minimumresultsforsearch="7" class="form-control select2-hidden-accessible" tabindex="-1" aria-hidden="true" {/if}>
      <option value="0">---</option>      
      {foreach from=$seller_list item=seller}
      <option value="{$seller.id_ws_seller}" {if $id_seller == $seller.id_ws_seller}selected="selected"{/if}>{$seller.name} [{$seller.customer}]</option>
       {/foreach}
      </select>
        
        {if $bulletinboardis17}
      </fieldset>
      {else}
      </div>
      {/if}
    {if $bulletinboardis17}</div>{/if}
    </div>
    {/if}
</div>