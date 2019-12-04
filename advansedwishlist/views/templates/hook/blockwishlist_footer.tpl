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
{if isset($wishlists) && count($wishlists) > 1}
<div class="hidden wishlist_popup">
<div id="wishlist_popup_form">
<form name="wlform" id="wlform" action="">
<h2>{l s='Add to Wishlist' mod='advansedwishlist'}</h2>
<div class="text">{l s='Select your Wish List below.' mod='advansedwishlist'}
{* to create a new list select 'New Wish List'.*}
 {l s='Manage your list title and more from \'My Account\'.' mod='advansedwishlist'}</div>
<input type="hidden" name="wish_p" id="wish_p" value="0">
<input type="hidden" name="wish_pat" id="wish_pat" value="0">
    <div class="wishlist">
    <select id="wishlist_select_popup">
    {foreach name=wl from=$wishlists item=wishlist}
    <option value="{$wishlist.id_wishlist}">{$wishlist.name}</option>
    {/foreach}
    </select>
    <button class="popup_button_wishlist">{l s='Add to Wishlist' mod='advansedwishlist'}</button>
    </div>
</form>
</div>
</div>
{/if}