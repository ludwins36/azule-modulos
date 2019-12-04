{*
* 2007-2018 PrestaShop
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
{if $logged}
<div class="wish_link {if $show_text}wish_link_text{/if}" id="login_wish">
<a href="{$link->getModuleLink('advansedwishlist', 'mywishlist', [], true)|escape:'htmlall':'UTF-8'}">
{if $show_text}
{l s='My WishList' mod='advansedwishlist'}
{else}
<i class="icon-heart"></i>
{/if}
<span class="wishlist_count">{$products_count|escape:'htmlall':'UTF-8'|string_format:"%d"}</span>
</a>
</div>
{else}
<div class="wish_link {if $show_text}wish_link_text{/if}">
    {if $show_text}{l s='My WishList' mod='advansedwishlist'}{else}<i class="icon-heart"></i>{/if}   

    <div class="allert_note">{l s='You must be logged' mod='advansedwishlist'}
    <p class="login_links">
    <a class="inline" href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">{l s='Sign in' mod='advansedwishlist'}</a> | <a class="inline" href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">{l s='Register' mod='advansedwishlist'}</a>
    </p>
    </div>

    </div>
{/if}