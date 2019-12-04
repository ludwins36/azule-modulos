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

<div id='status' class='tab-content'>
		<div class='tab-pane active'>
			<div class='panel'>
				<div class='panel-heading'>
					<i class='icon-cogs'></i>
            		{l s='Configuración de Status' mod='vexurbaner'}
            	</div>
            	<div class='form-wrapper'>
               		<div class='form-group'>
						<label class='control-label col-lg-3'>{l s='Status de la Orden' mod='vexurbaner'}</label>
                    	<div class='col-lg-6'>
                        	<select id='urbaner-status' name='urbaner_status' class='form-control'>
                     		{foreach $statuses as $value}
                            	<option value='{$value.id|escape:'html':'UTF-8'}' {if $status.status == $value.id}selected{/if}>{$value.name}</option>
                    		{/foreach}
							</select>
							<p class='help-block'>{l s='Cree un pedido de Urbaner cuando el pedido de la tienda cambie a cualquiera de estos estados' mod='vexurbaner'}</p>
                    	</div>
					</div>
					<div class='form-group'>
						<label class='control-label col-lg-3'>{l s='Cambio de Status' mod='vexurbaner'}</label>
                    	<div class='col-lg-6'>
						
                        	<span class='switch prestashop-switch fixed-width-lg'>
								<input type='radio' name='urbaner_change' id='PAGOSERPS_LIVE_MODE_on' value='1' {if $status.change == 1}checked='checked'{/if}>
								<label for='PAGOSERPS_LIVE_MODE_on'>{l s='Sí' mod='vexurbaner'}</label>
								<input type='radio' name='urbaner_change' id='PAGOSERPS_LIVE_MODE_off' value='2' {if $status.change == 2}checked='checked'{/if}>
								<label for='PAGOSERPS_LIVE_MODE_off'>{l s='No' mod='vexurbaner'}</label>
								<a class='slide-button btn'></a>
							</span>
							<p class='help-block'>{l s='Habilitar el cambio de estado de la orden de compra en la creación de orden de urbaner' mod='vexurbaner'}</p>
                    	</div>
					</div>
					<div class='form-group'>
						<label class='control-label col-lg-3'>{l s='Nuevo Status' mod='vexurbaner'}</label>
                    	<div class='col-lg-6'>
                        	<select id='urbaner-status' name='urbaner_newStatus' class='form-control'>
                     		{foreach $statuses as $value}
                            	<option value='{$value.id|escape:'html':'UTF-8'}' {if $status.newStatus == $value.id}selected{/if}>{$value.name|escape:'html':'UTF-8'}</option>
                    		{/foreach}
							</select>
            
							<p class='help-block'>{l s='Cree un pedido de Urbaner cuando el pedido de la tienda cambie a cualquiera de estos estados' mod='vexurbaner'}</p>
                    	</div>
					</div>
					<div class='panel-footer'>
                    	<button type='submit' value='1' name='submitvexurbanerModule' class='btn btn-default pull-right'>
                        	<i class='process-icon-save' ></i>
                        	{l s='    GUARDAR    ' mod='vexurbaner'}
                    	</button>
                	</div>
				</div>
			</div>
		</div>
	</div>
