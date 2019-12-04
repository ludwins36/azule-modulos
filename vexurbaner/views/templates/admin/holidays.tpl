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

<div id='holyDays' class='tab-content'>
	<div class='tab-pane active'>
		<div class='panel'>
			<div class='panel-heading'>
				<i class='icon-cogs'></i>
            	{l s='Configuración de Dias Feriados' mod='vexurbaner'}
            </div>
            <div class='form-wrapper'>
                <div class='form-group'>
				    <label class='col-lg-3 control-label label-urbaner'>{l s='Día ' mod='vexurbaner'}</label>
        		    <div class='col-lg-3'>
         			    <input type='date' name='holiday_date' step='1'>
        		    </div>
			    </div>
                <div class='form-group'>
				    <label class='col-lg-3 control-label label-urbaner'>{l s='Concepto ' mod='vexurbaner'}</label>
        		    <div class='col-lg-4'>
         			    <input type='text' name='holiday_descrip' step='1'>

        		    </div>
                </div>

                {if $holidays}

                    <table class='turns' id='turns'>
                        <tr>
                            <th>{l s='Fecha' mod='vexurbaner'}</th>
                            <th>{l s='Concepto' mod='vexurbaner'}</th>
                            <th>{l s='Borrar' mod='vexurbaner'}</th>
                        <tr>
                    {foreach $holidays as $value}
                        <tr class='table'>
                            <td>{$value.date|escape:'htmlall':'UTF-8'}</td>
                            <td>{$value.concep|escape:'htmlall':'UTF-8'}</td>
                            <td>


                     	            <input type='checkbox' value='{$value.id|escape:'html':'UTF-8'}' name='submitDeleteHoliday' step='1'>
                        	            {* {l s='BORRAR' mod='vexurbaner'} *}
						            {* <button type='submit' value='1'  name='submitDeleteHoliday' class='btn btn-default pull-center'>
                   		            </button> *}
                            </td>  
                        </tr>
                    {/foreach}
                    </table>
                {/if}
				<div class='panel-footer'>
                    <button type='submit' value='1' id='' name='submitVex_urbanerHolidays' class='btn btn-default pull-right'>
                        <i class='process-icon-save' ></i>
                        {l s='    GUARDAR    ' mod='vexurbaner'}
                    </button>
                </div>
			</div>
		</div>
	</div>
</div>