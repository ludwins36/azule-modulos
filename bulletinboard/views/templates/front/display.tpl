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

{if $action_delete}
    <p class="success alert alert-success">{l s='Product has been removed!' mod='bulletinboard'}</p>
{elseif $added}
    <p class="success alert alert-success">{l s='Product added or updated' mod='bulletinboard'}{if !$moderat} {l s=' and sent for moderation.' mod='bulletinboard'}{/if}</p>
{/if}
<div class="col-xs-12 col-sm-4 col-md-3">
<div id="main_profile" class="{if $bulletinboardis17 == 1}market_17{else}market_16{/if}">
<div class="seller_logo">
<img src="/img/co/seller_{$seller->id_ws_seller|escape:'htmlall':'UTF-8'}.jpg">
</div>
<div id="bb_user_info">
{if $bonus_on}
{l s='Your rewards' mod='bulletinboard'}: <b>{$rewards|escape:'htmlall':'UTF-8'}</b>% <a class="showPopup" href="#rewards_note"><span class="note_balanse">?</span></a><br/>
{l s='Your balance is: ' mod='bulletinboard'} <b>{$ballance|escape:'htmlall':'UTF-8'}</b><a class="showPopup" href="#balance_note"><span class="note_balanse">?</span></a><br/>
{/if}
{l s='Products: ' mod='bulletinboard'} <b>{$count_products|escape:'htmlall':'UTF-8'}</b>
</div>
</div>

<div class="hidden">
<div id="rewards_note">
<b>{l s='Your rewards' mod='bulletinboard'}</b><br/>
{l s='The reward is accrued after the sale of products and deduction of the commission of the store.' mod='bulletinboard'}
{l s='Example:' mod='bulletinboard'}<br/>
{l s='You set the price at $15.' mod='bulletinboard'}
{l s='After the sale of products you will receive a reward ($15-15% of commissions ) so $12,25' mod='bulletinboard'}<br/>
</div>
<div id="balance_note">
<b>{l s='Your balance' mod='bulletinboard'}</b><br/>
{l s='Rewards that has been approved and can be cashed out.' mod='bulletinboard'}
</div>
</div>
                <!-- SIDEBAR MENU -->
                <div class="profile-usermenu">
                    <ul class="nav">
                        <li class="nav-item ">
                            <a data-toggle="tab" role="tab" class="nav-link active" href="#seller_products">
                            <i class="glyphicon glyphicon-home"></i>
                            {l s='Seller products' mod='bulletinboard'}
                            </a>
                        </li>
                        {if $seller->active and $orders_on}
                        <li class="nav-item ">
                            <a class="nav-link " data-toggle="tab" role="tab" class="nav-link" href="#orders">
                            <i class="glyphicon glyphicon-user"></i>
                            {l s='Orders history' mod='bulletinboard'} </a>
                        </li>
                        {/if}
                        <li class="nav-item ">
                            <a data-toggle="tab" role="tab" class="nav-link" href="#seller_profile">
                            <i class="glyphicon glyphicon-ok"></i>
                            {l s='Edit the Seller profile' mod='bulletinboard'} </a>
                        </li>
                        {if $seller->active and $bonus_on}
                        <li class="nav-item ">
                            <a class="nav-link" data-toggle="tab" role="tab" class="nav-link" href="#withdraw">
                            <i class="glyphicon glyphicon-user"></i>
                            {l s='Withdraw' mod='bulletinboard'}</a>
                        </li>
                        {/if}
                        {if $seller->active}
                        <li class="nav-item ">
                            <a class="nav-link" href="{$link->getModuleLink('bulletinboard', 'default', ['id_seller'=>{$seller->id_ws_seller|escape:'htmlall':'UTF-8'}], true)|escape:'htmlall':'UTF-8'}">
                            <i class="glyphicon glyphicon-ok"></i>
                            {l s='My Shop' mod='bulletinboard'}</a>
                        </li>
                        {/if}
                    </ul>
                </div>
                <!-- END MENU -->
</div>
<div id="seller_account" class="tab-content left-column col-xs-12 col-sm-8 col-md-9 ">
    <div class="tab-pane active" id="seller_products">
    <div class="statistic_products">
    {if $bulletinboardis17 == 1}<i class="material-icons">shopping_basket</i>{/if} {l s='Total Products Sold' mod='bulletinboard'}: {$total_q} <br />
    {if $bonus_on}{if $bulletinboardis17 == 1}<i class="material-icons">payment</i>{/if} {l s='Total Earning' mod='bulletinboard'}: {$total_summ}{/if}
    </div>
    {if $seller->active}         
              <table class="table table-striped table-bordered">
                <thead>
                    <th></th>
                    <th></th>
                    <th>{l s='Model' mod='bulletinboard'}</th>
                    <th>{l s='Name' mod='bulletinboard'}</th>
                    <th>{l s='Price' mod='bulletinboard'}</th>
                    <th>{l s='Quantity' mod='bulletinboard'}</th>
                    <th>{l s='Sold total' mod='bulletinboard'}</th>
                    <th>{l s='status' mod='bulletinboard'}</th>
                    <th>{l s='action' mod='bulletinboard'}</th>
                </thead>
                <tbody>
                    {foreach from=$products item=product}
                        <tr>
                            <td>{$product.id_product|intval}</td>
                            <td>
                            {if isset($product.image)}
                                <img src="{$link->getImageLink($product.link_rewrite, $product.image, 'small_default')|escape:'htmlall':'UTF-8'}"  />
                            {/if}
                            </td>
                            <td>{$product.reference|escape:'htmlall':'UTF-8'}</td>
                            <td>
                                <a href="{$link->getProductLink($product.id_product)|escape:'quotes':'UTF-8'}">{$product.name|escape:'quotes':'UTF-8'}</a>
                            </td>
                            <td>
                                {$product.price|escape:'htmlall':'UTF-8'}
                            </td>
                            <td>{$product.quantity|intval}</td>
                            <td>{$product.sold_total|escape:'htmlall':'UTF-8'}</td>
                            <td>
                            {if $product.active}
                                {if $bulletinboardis17 == 1}
                                <i class="material-icons active_p">check_circle</i>
                                {else}
                                <i class="icon-check-circle active_p"></i>
                                {/if}
                            {else}
                                {if $bulletinboardis17 == 1}
                                <i class="material-icons wait_p">hourglass_full</i>
                                {else}
                                <i class="icon-clock-o wait_p"></i>
                                {/if}                            
                            {/if}</td>
                            <td>
                                <a href="{$link->getModuleLink('bulletinboard', 'boardcustomer', ['edit' => 1, 'id_product' => $product.id_product])|escape:'quotes':'UTF-8'}" class="edit btn btn-default">
                                {if $bulletinboardis17 == 1}
                                <i class="material-icons">create</i>
                                {else}
                                    <i class="icon-pencil"></i>
                                    {/if}
                                     {l s='Edit' mod='bulletinboard'}
                                </a>
                                <a href="{$link->getModuleLink('bulletinboard', 'boardcustomer', ['delete' => 1, 'id_product' => $product.id_product])|escape:'quotes':'UTF-8'}" class="delete btn btn-default">
                                    {if $bulletinboardis17 == 1}
                                <i class="material-icons">delete</i>
                                {else}
                                    <i class="icon-eraser"></i>
                                    {/if}
                                     {l s='Delete' mod='bulletinboard'}
                                </a>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
            
                   {if $pagin_on}
           <div class="paging-container">
           {foreach from=$pages item="page"}
                {if $page.type === 'current'}
                    <b>{$page.page|escape:'htmlall':'UTF-8'}</b>
                {else}
                    <a href="{$this_link|escape:'htmlall':'UTF-8'}{$page.rp|escape:'htmlall':'UTF-8'}" title="{$page.page|escape:'htmlall':'UTF-8'}">{$page.page|escape:'htmlall':'UTF-8'}</a>
                {/if}
           {/foreach}
            </div>
         {/if}
            
            <div class="row_btn">
                <a href="{$link->getModuleLink('bulletinboard', 'boardcustomer', ['add' => 1])|escape:'htmlall':'UTF-8'}" class="btn btn-default m-b-2 m-r-1 btn-primary pointer">
                   {if $bulletinboardis17 == 1}<i class="material-icons">add_circle_outline</i>{else}<i class="icon-plus"></i>{/if} {l s='Add new product' mod='bulletinboard'}
                </a>
            </div>
    {else}
    <p class="warning alert alert-warning" style="padding:6px 12px;">{l s='Your profile is on moderation' mod='bulletinboard'}</p>
    {/if}
    </div>
    
    {if $seller->active and $orders_on}
    <div class="tab-pane" id="orders">
            <table class="table table-striped table-bordered">
                <thead>
                    <th>{l s='#Order' mod='bulletinboard'}</th>
                    <th>{l s='Product' mod='bulletinboard'}</th>
                    <th>{l s='Customer' mod='bulletinboard'}</th>
                    <th>{l s='Status' mod='bulletinboard'}</th>
                    <th>{l s='Date' mod='bulletinboard'}</th>
                    <th>{l s='Action' mod='bulletinboard'}</th>
                </thead>
                <tbody>
                    {foreach from=$orders_history item=order}
                    <tr>
                    <td>{$order.reference}</td>
                    <td>{$order.pr_name}</td>
                    <td>{$order.customer}</td>
                    <td>{$order.status}</td>
                    <td>{$order.date_add}</td>
                    <td><a href="{$link->getModuleLink('bulletinboard', 'boardcustomer', ['order_detail' => $order.id_order])|escape:'quotes':'UTF-8'}">{l s='View' mod='bulletinboard'}</a></td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
    </div>
    {/if}
    
    <div class="tab-pane" id="seller_profile">
    <form action="" method="POST" enctype="multipart/form-data">
    <section class="form-fields">
        <input type="hidden" name="id_ws_seller" value="{$seller->id_ws_seller|escape:'htmlall':'UTF-8'}">
        <div class="form-group row">
                      <label class="col-md-3 form-control-label control-label col-lg-3">
                             {l s='Shop name' mod='bulletinboard'}
                      </label>
                      <div class="col-lg-9 ">
                        <input type="text" class="form-control" name="seller_name" value="{$seller->name|escape:'htmlall':'UTF-8'}">
                      </div>
        </div>
        
       <div class="form-group row">
                      <label class="col-md-3 form-control-label control-label col-lg-3">
                             {l s='Payment account' mod='bulletinboard'}
                      </label>
                      <div class="col-lg-9 ">
                        <input type="text" class="form-control" name="payment_acc" value="{$seller->payment_acc|escape:'htmlall':'UTF-8'}">
                      </div>
        </div>
                      
                   <div class="form-group row">
                   <label class="col-md-3 form-control-label control-label col-lg-3">
                          {l s='Shop Description' mod='bulletinboard'}
                   </label>
                            
                   <div class="col-lg-9 ">

                                    {foreach $languages as $language}
                                        {*{assign var='value_text' value=$seller->description[$language.id_lang]}*}
                                        {if $languages|count > 1}
                                        <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                            <div class="col-lg-9">
                                        {/if}
                                        <textarea id="seller_description_{$language.id_lang|intval}" name="seller_description_{$language.id_lang|intval}" class="textarea-autosize" rows="10" cols="45">{if isset($seller->description[$language.id_lang]) && $seller->description[$language.id_lang] != ''}{$seller->description[$language.id_lang]|escape:'html':'UTF-8'|stripslashes}{/if}</textarea>    

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
                      
                    <div class="form-group row">
                        <label class="col-md-3 form-control-label control-label col-lg-3">
                                 {l s='Shop Image' mod='bulletinboard'}
                        </label>
                        <div class="col-lg-9 ">
                        <input name="image_seller" type="file" class="multi form-txt" />                            
                       </div>
                </div>
       </section>         
               <footer class="form-footer clearfix">
                      <button type="submit" value="1" id="seller_profile_submit" name="submitSellerProfile" class="continue btn btn-primary pull-xs-right">
                            <i class="save"></i> {l s='Save' mod='bulletinboard'}
                        </button>
              </footer>
    </form>
    </div>
    {if $seller->active and $bonus_on}
    <div class="tab-pane" id="withdraw">
        <form method="post" action="" >
        <section class="form-fields">
        <div class="form-group row ">
            <label for="summ" class="col-md-3 form-control-label required">{l s='Amount' mod='bulletinboard'}:</label>
            <div class="col-md-6">
            <input type="text" class="form-txt" name="summ" placeholder="{l s='Amount' mod='bulletinboard'}" size="7" />
            </div>
        </div>
        <div class="form-group row ">
            <label for="message" class="col-md-3 form-control-label required">{l s='Comment' mod='bulletinboard'}:</label>
            <div class="col-md-6">
            <textarea name="message" rows="5" cols="45" placeholder="{l s='Comment' mod='bulletinboard'}" ></textarea>
            </div>
        </div>
        </section>
        {if $int_ballance > 0 }
        <footer class="form-footer clearfix">
                <input type="submit" class="continue btn btn-primary pull-xs-right" name="submitPayment" value="{l s='Order' mod='bulletinboard'}" />
        </footer>
        {/if}
    </form>

    
    {if isset($payments) && count($payments)}
    <p><b>{l s='Operation history' mod='bulletinboard'}</b></p>
    <table class="table" style="width: 100%;">
        <thead>
        <tr>
            <td width="20%"><b>{l s='Operation' mod='bulletinboard'}</b></td>
            <td width="35%"><b>{l s='Comment' mod='bulletinboard'}</b></td>
            <td width="20%"><b>{l s='Amount' mod='bulletinboard'}</b></td>
            <td width="25%"><b>{l s='Date' mod='bulletinboard'}</b></td>
        </tr>
        </thead>
        <tbody>
        {foreach from=$payments item=payment}
        <tr>
            <td>
                {if $payment.status==1}
                    {l s='Query' mod='bulletinboard'}
                {elseif $payment.status==2}
                    {l s='Payment' mod='bulletinboard'}
                {elseif $payment.status==3}
                    {l s='BID' mod='bulletinboard'}
                {elseif $payment.status==4}
                    {l s='Up Account' mod='bulletinboard'}
                {elseif $payment.status==5}
                    {l s='Refund' mod='bulletinboard'}
                {else}
                    {l s='Sale' mod='bulletinboard'}
                {/if}
            </td>
            <td>{$payment.description|escape:'htmlall':'UTF-8'}</td>
            <td>{$payment.summ|escape:'htmlall':'UTF-8'}</td>
            <td>{$payment.date_upd|escape:'htmlall':'UTF-8'}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    {else}
        <div class="warning alert alert-warning">{l s='Your history is empty!' mod='bulletinboard'}</div>
    {/if}
    
        </div>
    {/if}
    
</div>            
{else}
<div class="panel panel-default">
<div class="panel-body">
            <form method="POST" class="form_agree" action="">
            
                <p>{l s='Do you want to sell in our store?' mod='bulletinboard'}</p>
                {if $cms}
                    <input type="checkbox" name="agree" class="chb_agree" value="1">
                    <span class="agree_label">{l s='I have read and agree to the' mod='bulletinboard'} <a class="showPopup" href="#showPopup">{l s='"Terms of Service"' mod='bulletinboard'}</a></span>
                    <div class="hidden">
                        <div id="showPopup" class="rte">
                            {$cms->content|escape:'quotes':'UTF-8' nofilter}
                        </div>
                    </div>

                {/if}
                <input value="{l s='Become a seller' mod='bulletinboard'}" class="btn btn-default submitAgree" name="submitAgree" type="submit">
            </form>
            </div>
 </div>           
{/if}