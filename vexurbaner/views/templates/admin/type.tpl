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

<div id='pageTabContent' class='tab-content'>
		<div class='tab-pane active'>
			<div class='panel'>
				<div class='panel-heading'>
					<i class='icon-cogs'></i>
            		{l s='Configuración del tipo de envío' mod='vexurbaner'}
            	</div>
				<div class='form-wrapper'>
					<div class='form-group'>
						<label class='control-label col-lg-3'>{l s='Tipo de envío' mod='vexurbaner'}</label>
						<div class='col-lg-3 p-2'>
							<input type='checkbox' name='type_urbaner_1' {if $type.0 == 'on'}checked {/if}/> {l s='  Express' mod='vexurbaner'}<br>
							{* <input type='checkbox' name='cost_urbaner_2' value='2' {if $price == 2}checked {/if}/> {l s='  Mismo día' mod='vexurbaner'}<br> *}
							<input type='checkbox' name='type_urbaner_2'  {if $type.1 == 'on'}checked {/if}/> {l s='  Día siguiente' mod='vexurbaner'}<br>
						
						</div>
							
					</div>
					<div class='form-wrapper'>
               		
					<div class='panel-footer'>
                    	<button type='submit' value='1' id='module_form_submit_btn' name='submitvexurbanerModule' class='btn btn-default pull-right'>
                        	<i class='process-icon-save' ></i>
                        	{l s='    GUARDAR    ' mod='vexurbaner'}
                    	</button>
                	</div>
				</div>
			</div>
		</div>
	</div>