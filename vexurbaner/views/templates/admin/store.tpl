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

{foreach $stores as $store}

		<div class='tab-pane {if $store.id == 1}active{/if}' id='page{$store.id|escape:'html':'UTF-8'}'>
			<div class='panel' id='fieldset_0'>
            	<div class='panel-heading'>
					<i class='icon-cogs'></i>
            		{l s='Configuración General' mod='vexurbaner'}
            	</div>
            	<div class='form-wrapper'>
        			<input type='hidden' name='submitStoreIdUrbaner'  value='{$store.id|escape:'html':'UTF-8'}'/>
					<div class='form-group'>
                    	<label class='control-label col-lg-3'>{l s='Nombre de la Tienda' mod='vexurbaner'}</label>
                    	<div class='col-lg-6'>
                        	<input type='text'  name='name'  value="{$store.name_ws|escape:'html':'UTF-8'}" />
							<p class='help-block'>{l s='Debe ser igual al nombre del admin de la tienda.' mod='vexurbaner'}</p>

                    	</div>
                	</div>
                	<div class='form-group'>
                    	<label class='control-label col-lg-3'>{l s='Dirección de la tienda' mod='vexurbaner'}</label>
                    	<div class='col-lg-6'>
                        	<input type='text'  name='address' value='{$store.address|escape:'html':'UTF-8'}'/>
							<p class='help-block'>{l s='Ej: Jose Leal 560, Interior 5' mod='vexurbaner'}</p>

                    	</div>
                	</div>
				 	<div class='form-group'>
                    	<label class='control-label col-lg-3'>{l s='Referencia' mod='vexurbaner'}</label>
                    	<div class='col-lg-6'>
                        	<input type='text'  name='address2' value='{$store.address_2|escape:'html':'UTF-8'}'/>

                    	</div>
                	</div>

					<div class='form-group'>
                    	<label class='control-label col-lg-3'>{l s='Correo de la tienda' mod='vexurbaner'}</label>
                    	<div class='col-lg-6'>
                        	<input type='text'  name='mail' value='{$store.mail|escape:'html':'UTF-8'}'/>

                    	</div>
                	</div>

					<div class='form-group'>
                    	<label class='control-label col-lg-3'>{l s='Código Postal' mod='vexurbaner'}</label>
                    	<div class='col-lg-6'>
                        	<input type='text'  name='zipCode' value='{$store.zip_code|escape:'html':'UTF-8'}'/>
                    	</div>
                	</div>
					

					<div class='form-group'>
                    	<label class='control-label col-lg-3'>{l s='Latitud' mod='vexurbaner'}</label>
                    	<div class='col-lg-6'>
                        	<input type='text'  name='lat' value='{$store.lat|escape:'html':'UTF-8'}'/>
                    	</div>
                	</div>
                	<div class='form-group'>
                    	<label class='control-label col-lg-3'>{l s='Longitud' mod='vexurbaner'}</label>
                    	<div class='col-lg-6'>
                        	<input type='text'  name='lnt' value='{$store.lnt|escape:'html':'UTF-8'}'/>
							<p class='help-block'>{l s='Descubre la Latitud y Longitud ' mod='vexurbaner'}<a role='button' href='https://www.latlong.net/'>{l s='AQUÍ' mod='vexurbaner'}</a></p>

                    	</div>
                	</div>
					<div class='form-group'>
                    	<label class='control-label col-lg-3'>{l s='Persona de contacto' mod='vexurbaner'}</label>
                    	<div class='col-lg-6'>
                        	<input type='text'  name='persone' value='{$store.persone|escape:'html':'UTF-8'}'/>
                    	</div>
                	</div>
                	
                	<div class='form-group'>
                    	<label class='control-label col-lg-3'>{l s='Telefono de contacto' mod='vexurbaner'}</label>
                    	<div class='col-lg-6'>
                        	<input type='text'  name='phone' value='{$store.phone|escape:'html':'UTF-8'}'/>

                    	</div>
                	</div>
					<div class='form-group'>
                    	<label class='control-label col-lg-3'>{l s='Tiempo de preparación' mod='vexurbaner'}</label>
                    	<div class='col-lg-6'>
                        	<input type='text'  name='time' value='{$store.time|escape:'html':'UTF-8'}'/>
                    	</div>
                	</div>
                	
                	<div class='panel-footer'>
                    	<button type='submit' value='1' name='submitStoreAction' class='btn btn-default pull-right'>
                        	<i class='process-icon-save' ></i>
                        	{l s='    GUARDAR    ' mod='vexurbaner'}
                   		</button>
						{if $store.id == 1}
						<a href='javascript:;' id='btnAddPage'  class='btn btn-default pull-left' role='button'>
							<i class='process-icon-plus' ></i>
							{l s='Nueva Tienda' mod='vexurbaner'}
						</a>
						{else}
						<button type='submit' value='{$store.id}' name='submitStoreDeleteUrbaner' class='btn btn-default pull-left'>
                        	<i class='process-icon-close' ></i>
                        	{l s='    ELIMINAR    ' mod='vexurbaner'}
                   		</button>
						{/if}  


                	</div>
            	</div>        
        	</div>
		</div>
{/foreach}