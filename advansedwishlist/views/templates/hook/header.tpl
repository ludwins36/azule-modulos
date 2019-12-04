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
    var baseDir = '{$base_dir|addslashes}';    
    var isLogged = '{$logged}';
    var single_mode = '{$single_mode}';
    var added_to_wishlist = '{l s='The product was successfully added to your wishlist.' mod='advansedwishlist' js=1}'
    var added_to_wishlist_btn = '{l s='Added to wishlist' mod='advansedwishlist' js=1}'
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
