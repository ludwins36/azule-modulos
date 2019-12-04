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
    {if $seller->active and $ws_order}
    <div class="tab-pane" id="orders">
        <h2><i class="icon-user"></i> {l s='Customer Details' mod='bulletinboard'}</h2>    
        {$ws_order.customer} <br/>
        {$ws_order.email}
        <h2><i class="icon-truck"></i> {l s='Shipping Address' mod='bulletinboard'}</h2>
        <ul>
        <li>{$ws_order.address1}</li>
        <li>{$ws_order.postcode}</li>
        <li>{$ws_order.city}</li>
        </ul>
    </div>
    {/if}