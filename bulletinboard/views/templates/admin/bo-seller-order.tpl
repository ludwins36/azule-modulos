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
<div class="panel">
    <div class="panel-heading">
        <i class="icon-cart-arrow-down"></i>
        {l s='Sellers List' mod='bulletinboard'}
    </div>
    <div class="table-responsive">
        <table class="table wk-table">
            <thead>
                <tr>
                    <th><span class="title_box">{l s='Shop' mod='bulletinboard'}</span></th>
                    <th>
                        <span class="title_box">{l s='Customer' mod='bulletinboard'}</span>
                    </th>
                    <th>
                        <span class="title_box">{l s='Customer Email' mod='bulletinboard'}</span>
                    </th>
                    <th><span class="title_box">{l s='Product' mod='bulletinboard'}</span></th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$seller_order_details item=seller_product}
            <tr>
            <td>{$seller_product.name}</td>
            <td><a href="{$customer_link}{$seller_product.id_customer}&viewcustomer">{$seller_product.customer}</a></td>
            <td>{$seller_product.email}</td>
            <td>{$seller_product.pr_name}</td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>