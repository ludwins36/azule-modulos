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

<div class='panel'>
	<div class='panel-heading'>
		<i class='icon-cogs'></i>
        {l s='Configuración de Urbaner!' mod='vexurbaner'}
    </div>
  
    <label class='m-2' style='margin-bottom: 10px;'>
        {l s='Encuentra tus credenciales de Urbaner ' mod='vexurbaner'}
        {* la pagina de urbaner *}
        <a href='https://business.urbanerapp.com/dashboard/profile' target='_blank'>
        {l s='AQUÍ' mod='vexurbaner'}
        </a>
    </label>
	<div class='form-wrapper'>
    <div class="form-group">
            <label class="control-label col-lg-3">Test Modo</label>
            <div class="col-lg-9">
								
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="PE_CONFIG_TESTMODE" id="PE_CONFIG_TESTMODE_on" value="1" {if $test == 1} checked="checked"{/if}/>
					<label for="PE_CONFIG_TESTMODE_on">Sí</label>
					<input type="radio" name="PE_CONFIG_TESTMODE" id="PE_CONFIG_TESTMODE_off" value="0" {if $test == 0} checked="checked"{/if} />
					<label for="PE_CONFIG_TESTMODE_off">No</label>
					<a class="slide-button btn"></a>
				</span>
																								
				<p class="help-block">{l s='Choose between live or test mode' mod='vexurbaner'}</p>							
			</div>
        </div>

        <div class='form-group'>
			<label class='control-label col-lg-3 required' >{l s='Api Key de Urbaner!' mod='vexurbaner'}</label>
            <div class='col-lg-6'>
                <input type='text'  name='VEX_URBANER_API_KEY' value='{$keys.0|escape:'htmlall':'UTF-8'}'/>
            </div>			
		</div>

        <div class='form-group'>
		    <label class='control-label col-lg-3'>{l s='Punto de retorno' mod='vexurbaner'}</label>
            <div class='col-lg-6'>
				<input type='checkbox' name='is_return' {if $return == 'on'}checked {/if}/> {$return} {l s='  Si el servicio de mensajería necesita volver al punto de origen.' mod='vexurbaner'}<br>
            </div>
		</div>
        {if $priceType}
        <div class='form-group'>
		    <label class='control-label col-lg-3'>{l s='Metodos de pago' mod='vexurbaner'}</label>
            <div class='col-lg-6'>
				<select id='urbanerPrice' name="type_price" class='form-control urbaner-times'>
                {foreach $priceType as $price}
                    <option value='{$price.backend}' >{$price.backend}  {l s="    "}   {$price.display}</option>
                {/foreach}
                </select>
            <p class="help-block">{l s='Seleccione la forma de pago de urbaner.' mod='vexurbaner'}</p>		
            </div>
		</div>
        {else}
        <div class="alert alert-danger" role="alert">
        No tiene metodos de pago configurado <a href="https://www.urbaner.com" class="alert-link">click aquí!</a>. Para configurar su cuenta.
        </div>
        
        {/if}
        

        
        <div class='panel-footer'>
            <button type='submit' value='1' name='submitvexurbanerModule' class='btn btn-default pull-right'>
                <i class='process-icon-save' ></i>
                {l s='    GUARDAR    ' mod='vexurbaner'}
            </button>
        </div>

    </div>
</div>


<div class='panel'>
	<div class='panel-heading'>
		<i class='icon-cogs'></i>
        {l s='Configuración de Google Maps' mod='vexurbaner'}
    </div>
    <div class='content'>
        <label class='m-2'>
            {l s='Debes crear una cuenta de Google Console desde  ' mod='vexurbaner'}
            <a href='https://console.cloud.google.com/' target='_blank'>
            {l s='AQUÍ' mod='vexurbaner'}
            </a>
        </label>
    
    </div>

    <div class='content'>
        <label class='m-2' >
            {l s='Cómo obtener la API KEY  ' mod='vexurbaner'}
            <a href='https://developers.google.com/maps/documentation/javascript/get-api-key' target='_blank'>
            {l s='AQUÍ' mod='vexurbaner'}
            </a>
        </label>
    </div>

    <div class='content' style='margin-bottom: 10px;'>
        <label class='m-2' >
            {l s='Y debes tener las siguientes API´S: Places API, Directions API, Geocoding API, Maps JavaScript API.' mod='vexurbaner'}
            
        </label>
    </div>


	<div class='form-wrapper'>
        <div class='form-group'>
			<label class='control-label col-lg-3 required' >{l s='Api Key de Google Maps' mod='vexurbaner'}</label>
            <div class='col-lg-6'>
                <input type='text'  name='VEX_URBANER_GOOGLE_MAPS_KEY' value='{$keys.1|escape:'htmlall':'UTF-8'}'/>
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