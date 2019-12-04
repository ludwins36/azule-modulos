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
<div id='horarys' class='tab-content'>
			<div class='panel'>
				<div class='panel-heading'>
					<i class='icon-cogs'></i>
            		{l s='Configuración de Horarios' mod='vexurbaner'}
            	</div>
				<div class='form-wrapper'>
					{foreach $dias as $day}
						<div class='form-group'>
							<label class='control-label col-lg-3'>{l s=$day|escape:'html':'UTF-8' mod='vexurbaner'}</label>
							<input type="hidden" name="horary{$day|escape:'html':'UTF-8'}" value="{$day|escape:'html':'UTF-8'}"/>
							<div class='input-groud date col-lg-2' id='datePicker'>
								<input type='time' class='form-control' name='start{$day|escape:'html':'UTF-8'}' value='{foreach $horarys as $horary}{if $day == $horary.day}{$horary.start|escape:'html':'UTF-8'}{/if}{/foreach}'/>
							</div>
							<div class='input-groud date col-lg-2' id='datePicker'>
								<input type='time' class='form-control' name='end{$day|escape:'html':'UTF-8'}' value='{foreach $horarys as $horary}{if $day == $horary.day}{$horary.end|escape:'html':'UTF-8'}{/if}{/foreach}'/>
							</div>
								<input type='checkbox' name='check{$day|escape:'html':'UTF-8'}' {foreach $horarys as $horary}{if $day == $horary.day}{if $horary.status == 1}checked{/if}{/if}{/foreach}/>{l s='Día Laboral' mod='vexurbaner'}
							</div>

					{/foreach}
                	
					
					<div class='panel-footer'>
                    	<button type='submit' value='1' name='submitVex_urbanerHorarys' class='btn btn-default pull-right'>
                        	<i class='process-icon-save' ></i>
                        	{l s='    GUARDAR    ' mod='vexurbaner'}
                    	</button>
                	</div>
				</div>
			</div>
	</div>
