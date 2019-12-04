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
<style>
.wishlist i, .wish_link i{
color: {$icon_color|escape:'htmlall':'UTF-8'};
}
</style>
<script>
    var baseDir = '{$base_dir|addslashes}';    
    var isLogged = '{$logged}';
    var single_mode = '{$single_mode}';
    var added_to_wishlist = '{l s='The product was successfully added to your wishlist.' mod='advansedwishlist' js=1}'
    {if $show_btn_text}
    var added_to_wishlist_btn = '{l s='Added to wishlist' mod='advansedwishlist' js=1}';
    var add_to_wishlist_btn = '{l s='Add to wishlist' mod='advansedwishlist' js=1}';
    {else}
    var added_to_wishlist_btn = '';
    var add_to_wishlist_btn = '';
    {/if}
    var static_token = '{$static_token|addslashes}';
    var advansedwishlist_ajax_controller_url = '{$advansedwishlist_ajax_controller_url nofilter}';
    var idDefaultWishlist = '{$id_wishlist}';
    {if $advansedwishlistis17 == 1}
    var wishlist_btn_icon = '<i class="material-icons">favorite</i>';
    var ps_ws_version = 'advansedwishlistis17';
    {else}
    var wishlist_btn_icon = '<i class="icon icon-heart"></i>';
    var ps_ws_version = 'advansedwishlistis16';
    {/if}
</script>
{if $hook_name == 'top'}<div id="block_wishlist_top">{/if}
{if $logged}
<div class="wish_link {if $show_text}wish_link_text{/if} {if $advansedwishlistis17 == 1}wish_link_17{else}wish_link_16{/if}" id="login_wish">
<a href="{$link->getModuleLink('advansedwishlist', 'mywishlist', [], true)|escape:'htmlall':'UTF-8'}" title="{l s='My WishList' mod='advansedwishlist'}">
        {if $show_text}
{l s='My WishList' mod='advansedwishlist'}
{/if}
{if $wl_custom_font == 1 || (!$wl_custom_font && $advansedwishlistis17 == 1)}
<i class="material-icons">favorite</i>
{elseif $wl_custom_font == 3}
<span class="jms-heart-1"></span>
{else}
<i class="icon-heart"></i>
{/if}
<span class="wishlist_count {if $products_count == 0} empty_list{/if}">{$products_count|string_format:"%d"}</span>
</a>
</div>
{else}
<div class="wish_link {if $show_text}wish_link_text{/if} {if $advansedwishlistis17 == 1}wish_link_17{else}wish_link_16{/if}">
        {if $show_text}
<span>{l s='My WishList' mod='advansedwishlist'}</span>
{/if}
{if $wl_custom_font == 1 || (!$wl_custom_font && $advansedwishlistis17 == 1)}
<i class="material-icons">favorite</i>
{elseif $wl_custom_font == 3}
<span class="jms-heart-1"></span>
{else}
<i class="icon-heart"></i>
{/if}
<div class="allert_note">{l s='You must be logged' mod='advansedwishlist'}
    <p class="login_links">
    <a class="inline" href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">{l s='Sign in' mod='advansedwishlist'}</a> | <a class="inline" href="{$link->getPageLink('authentication', true, null, 'create_account=1')|escape:'htmlall':'UTF-8'}">{l s='Register' mod='advansedwishlist'}</a>
    </p>
</div>

    </div>
{/if}
{if $hook_name == 'top'}</div>{/if}

