{*
* 2007-2019 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<style>
 #btnAddPage {
    margin-botton: 1rem;
 }

</style>

<form id='store_form' class='defaultForm form-horizontal' action='{$url|escape:'html':'UTF-8'}' method='post' enctype='multipart/form-data' novalidate>

    {* <ul class='nav nav-tabs' role='tablist'>
        <li class='active'><a href='#template_1' role='tab' data-toggle='tab'>{l s='Ordenes Urbaner' mod='vexurbaner'}</a></li>
        <li><a href='#template_2' role='tab' data-toggle='tab'>{l s='Ordenes Pendientes' mod='vexurbaner'}</a></li>
    </ul> *}

    <!-- Tab panes -->
    {* <div class='tab-content'>
        <div class='tab-pane  active' id='template_1'>{include file='./orders.tpl'}</div>
        <div class='tab-pane' id='template_2'>{include file='./pending_orders.tpl'}</div>   
    </div> *}

    <input type='hidden' name='submitVex_urbanerModule' value='1'/>
    
    <div class='tab-content'>
        {include file='./configuration_api.tpl' }
    </div>

    <ul id='pageTab' class='nav nav-tabs'>
    
        {if $stores}
        {foreach $stores as $store}

            <li class='{if $store.id|escape:'htmlall':'UTF-8' == 1}active{/if}'><a href='#page{$store.id|escape:'htmlall':'UTF-8'}' data-toggle='tab'>{l s='Tienda' mod='vexurbaner'}{$store.id|escape:'htmlall':'UTF-8'}</a></li>
        {/foreach}

        {else}
            <li class='active'><a href='#page1' data-toggle='tab'>{l s='Tienda1' mod='vexurbaner'}</a></li>
        {/if}
    </ul>
    
    <div id='pageTabContent' class='tab-content'>
    {if $stores}
        {include file='./store.tpl' }

    {else}
        {include file='./store_defaul.tpl' }
    {/if}
    </div>

    {include file='./horary.tpl' }

    {include file='./holidays.tpl'}
    
    {include file='./status.tpl'}
    
    {* {include file='./type.tpl'} *}
                


    
    
</form> 

<script type='text/javascript'>
    var pageImages = [];
    var pageNum = "{$numStores|escape:'html':'UTF-8'}";
</script>
