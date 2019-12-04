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

<div id="view_wishlist">
<script>
    var baseDir = '{$base_dir|addslashes}';
    var static_token = '{$static_token|addslashes}';
    var isLogged = true;
    var baseUri = '{$base_dir|addslashes}';
    var advansedwishlist_controller_url = '{$advansedwishlist_controller_url nofilter}';
</script>
<h2>{$current_wishlist.firstname}{l s='\'s' mod='advansedwishlist'} {l s='Wishlist' mod='advansedwishlist'}</h2>
{if $wishlists}
<p>
    {l s='Other wishlists of %1s %2s:' sprintf=[$current_wishlist.firstname, $current_wishlist.lastname] mod='advansedwishlist'}
	{foreach from=$wishlists item=wishlist name=i}
		{if $wishlist.id_wishlist != $current_wishlist.id_wishlist}
			<a href="{$link->getModuleLink('advansedwishlist', 'view', ['token' => $wishlist.token])|escape:'html':'UTF-8'}" title="{$wishlist.name}" rel="nofollow">{$wishlist.name}</a>
			{if !$smarty.foreach.i.last}
				/
			{/if}
		{/if}
	{/foreach}
</p>
{/if}

<div class="wlp_bought">
        <table class="table table-striped table-hover wlp_bought_list" id="table_wishlist" >
        <thead>
        <tr>
        <th class="col-xs-3 col-md-2 wishlist-product-img"></th>
        <th class="col-xs-4 col-md-3 wishlist-product-desc">{l s='Items' mod='advansedwishlist'}</th>
        <th class="col-xs-0 hidden-xs-down col-md-1 wishlist-product-price">{l s='Price' mod='advansedwishlist'}</th>
        <th class="col-xs-1 col-md-1 wishlist-product-quantity">{l s='Quantity' mod='advansedwishlist'}</th>
        <th class="col-xs-3 col-md-2 wishlist-product-priority">{l s='Priority' mod='advansedwishlist'}</th>
        <th class="col-xs-0 col-md-2 wishlist-product-actions"></th>
        </tr>
        </thead>
        {foreach from=$products item=product name=i}
            <tr id="wlp_{$product.id_product|escape:'htmlall':'UTF-8'}_{$product.id_product_attribute|escape:'htmlall':'UTF-8'}">
                <td class="col-xs-3 col-md-2 wishlist-product-img">
                   <div class="product_image">
                        <a href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite, null, null, null, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}" title="{l s='Product detail' mod='advansedwishlist'}">
                            <img src="{$link->getImageLink($product.link_rewrite, $product.cover, 'small_default')|escape:'htmlall':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" />
                        </a>
                    </div>
                </td>
                <td class="col-xs-4 col-md-3 wishlist-product-desc">
                        <div class="product_infos">
                        <p class="product_name">{$product.name|truncate:30:'...'|escape:'html':'UTF-8'}</p>
                        <span class="wishlist_product_detail">
                        {if isset($product.attributes_small)}
                            <a href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite, null, null, null, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}" title="{l s='Product detail' mod='advansedwishlist'}">{$product.attributes_small|escape:'html':'UTF-8'}</a>
                        {/if}
                        </span>
                        <span class="hidden-sm-up">{$product.price}</span>
                        </div>
                </td>
                <td class="col-xs-0 hidden-xs-down col-md-1 wishlist-product-price">
                {$product.price}
                </td>
                <td class="col-xs-1 col-md-1 wishlist-product-quantity">
                <input type="text" class="wl_product_qty" id="quantity_{$product.id_product|escape:'htmlall':'UTF-8'}_{$product.id_product_attribute|escape:'htmlall':'UTF-8'}" value="{$product.wl_quantity|intval}"  />
                </td>
                <td class="col-xs-3 col-md-2 wishlist-product-priority">
                <select id="priority_{$product.id_product|escape:'htmlall':'UTF-8'}_{$product.id_product_attribute|escape:'htmlall':'UTF-8'}">
                                <option value="0"{if $product.priority eq 0} selected="selected"{/if}>{l s='High' mod='advansedwishlist'}</option>
                                <option value="1"{if $product.priority eq 1} selected="selected"{/if}>{l s='Medium' mod='advansedwishlist'}</option>
                                <option value="2"{if $product.priority eq 2} selected="selected"{/if}>{l s='Low' mod='advansedwishlist'}</option>
                            </select>
                </td>
                <td class="col-xs-12 col-md-2 wishlist-product-actions">
                <div class="btn_action">
                            {if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
                                {if $advansedwishlistis17 == 1}
                                <a class="btn btn-primary add_cart wishlist_add_to_cart ajax_add_to_cart_button exclusive" id="wishlist_add_to_cart_{$product.id_product|intval}" href="{$link->getAddToCartURL({$product.id_product|intval}, {$product.id_product_attribute})}" rel="nofollow" title="{l s='Add to cart' mod='advansedwishlist'}" data-id-product-attribute="{$product.id_product_attribute|intval}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
                                    <span>{l s='Add to cart' mod='advansedwishlist'}</span>
                                </a>
                                {else}
                                {capture}add=1&amp;id_product={$product.id_product|intval}{if isset($product.id_product_attribute) && $product.id_product_attribute}&amp;ipa={$product.id_product_attribute|intval}{/if}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
                                <a class="btn btn-primary wishlist_add_to_cart exclusive" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='advansedwishlist'}" data-id-product-attribute="{$product.id_product_attribute|intval}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
                                    <span>{l s='Add to cart' mod='advansedwishlist'}</span>
                                </a>
                                {/if}
                            {else}
                                <span class="button ajax_add_to_cart_button btn btn-default disabled">
                                    <span>{l s='Add to cart' mod='advansedwishlist'}</span>
                                </span>
                            {/if}
                </div>
                </td>
            </tr>
        {/foreach}
        </table>
   
</div>

</div>
