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
{if $bulletinboardis17 == 0}
<li>
{/if}
	<a {if $bulletinboardis17 == 1}class="col-lg-4 col-md-6 col-sm-6"{/if} href="{$module_link|escape:'quotes':'UTF-8'}">
		{if $bulletinboardis17 == 1}<span class="link-item">{/if}
		<i {if $bulletinboardis17 == 1}class="material-icons"{else}class="icon-list-alt"{/if}><i class="material-icons">{if $bulletinboardis17 == 1}store{/if}</i></i>
		{if $bulletinboardis17 == 0}<span>{/if}{l s='Simple marketplace' mod='bulletinboard'}{if $bulletinboardis17 == 0}</span>{/if}
		{if $bulletinboardis17 == 1}</span>{/if}
	</a>
{if $bulletinboardis17 == 0}
</li>
{/if}