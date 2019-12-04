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

<h1 class="page-heading product-listing">
        {l s='List of products by seller' mod='bulletinboard'}&nbsp;{$seller->name|escape:'html':'UTF-8'}
</h1>

<div class="content_scene_seller">
<div class="seller_logo">
<img src="/img/co/seller_{$seller->id_ws_seller|escape:'htmlall':'UTF-8'}.jpg">
</div>

<div class="seller_description">
{$seller->description|escape:'htmlall':'UTF-8'}
</div>
</div>

{if isset($products) && $products}
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
    {include file="$tpl_dir./product-list.tpl" class='seller_products' id='seller_products'}
{else}
<ul id="seller_products" class="seller_products">
    <li class="alert alert-info">{l s='No featured products at this time.' mod='bulletinboard'}</li>
</ul>
{/if}