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
<script>
var advansedwishlist_ajax_controller_url = '{$advansedwishlist_ajax_controller_url nofilter}';
</script>
<div id="advansedwishlist_cart_block" data-id-wishlist="{$id_wishlist|escape:'html':'UTF-8'}" class="block account card {if $advansedwishlistis17 == 1}advansedwishlist_17{/if}">
    <div class="card-block">
    <h4 class="title_block h4">
        <a href="{$module_link}" title="{l s='My wishlists' mod='advansedwishlist'}" rel="nofollow">{l s='Wishlist' mod='advansedwishlist'}</a>
    </h4>
    <hr class="separator" />
    </div>
    <div class="block_content">
        <div id="ws_wishlist_block_list" class="expanded">
        {if $wishlist_products}
            <dl class="products">
            {foreach from=$wishlist_products item=product name=i}
                <dt id="ws_blockwishlist_product_{$product.id_product|escape:'html':'UTF-8'}" class="wl_block_product">
                    <div class="wl_block_product_info">
                    <a href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite, null, null, null, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}" title="{l s='Product detail' mod='advansedwishlist'}">
                       <img src="{$link->getImageLink($product.link_rewrite, $product.cover, 'small_default')|escape:'htmlall':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" />
                    </a>
                    <div class="wl_product_info">
                    <a class="wl_product_name"
                    href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html'}" title="{$product.name|escape:'html':'UTF-8'}">
                    {$product.name|truncate:30:'...'|escape:'html':'UTF-8'}</a>
                    {if isset($product.attributes_small)}
                    <a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html'}" title="{l s='Product detail' mod='advansedwishlist'}">{$product.attributes_small|escape:'html':'UTF-8'}</a>
                    {/if}
                    <span class="ws_price">{$product.price}</span>
                    </div>
                    <input type="text" class="wl_cart_product_qty" data-id-product-attribute="{$product.id_product_attribute|intval}" data-id-product="{$product.id_product|intval}" id="quantity_{$product.id_product|escape:'htmlall':'UTF-8'}_{$product.id_product_attribute|escape:'htmlall':'UTF-8'}" value="{$product.wl_quantity|intval}"  />
                                {if $advansedwishlistis17 == 1}
                                <a class="btn btn-primary add_cart wishlist_add_to_cart ajax_add_to_cart_button exclusive" id="wishlist_add_to_cart_{$product.id_product|intval}" href="{$link->getAddToCartURL({$product.id_product|intval}, {$product.id_product_attribute})}" rel="nofollow" title="{l s='Add to cart' mod='advansedwishlist'}" data-id-product-attribute="{$product.id_product_attribute|intval}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
                                    <span>{l s='Add to cart' mod='advansedwishlist'}</span>
                                </a>
                                {else}
                                {capture}add=1&amp;id_product={$product.id_product|intval}{if isset($product.id_product_attribute) && $product.id_product_attribute}&amp;ipa={$product.id_product_attribute|intval}{/if}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
                                <a class="btn btn-primary exclusive" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='advansedwishlist'}" data-id-product-attribute="{$product.id_product_attribute|intval}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
                                    <span>{l s='Add to cart' mod='advansedwishlist'}</span>
                                </a>
                                {/if}
                    </div>
                    <a class="ajax_cart_block_remove_link" href="javascript:;" onclick="javascript:WishlistCart('ws_wishlist_block_list', 'delete', '{$product.id_product}', {$product.id_product_attribute}, '0', {$id_wishlist});" title="{l s='remove this product from my wishlist' mod='advansedwishlist'}" rel="nofollow">
                                {if $advansedwishlistis17 == 1}
            <i class="material-icons">delete_forever</i>
            {else}
            <i class="icon icon-remove"></i>
            {/if}
                    </a>
                </dt>

            {/foreach}
            </dl>
        {else}
            <dl class="wishlist_block_no_products">
                <dt>{l s='No products' mod='advansedwishlist'}</dt>
            </dl>
        {/if}
        </div>
        {*
        <p class="lnk">
        {if $wishlists}
            <select name="wishlists" id="wishlists" onchange="WishlistChangeDefault('wishlist_block_list', $('#wishlists').val());">
            {foreach from=$wishlists item=wishlist name=i}
                <option value="{$wishlist.id_wishlist}"{if $id_wishlist eq $wishlist.id_wishlist or ($id_wishlist == false and $smarty.foreach.i.first)} selected="selected"{/if}>{$wishlist.name|truncate:22:'...'|escape:'html':'UTF-8'}</option>
            {/foreach}
            </select>
        {/if}
        
            <a href="{$module_link}" title="{l s='My wishlists' mod='advansedwishlist'}" rel="nofollow">&raquo; {l s='My wishlists' mod='advansedwishlist'}</a>
        </p>
        *}
    </div>
</div>
