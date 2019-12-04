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
    .label-urbaner{
        text-align: right;
    }

    th{
        text-align: center;
        justify-content: center;
        background-color: #f2f2f2 !important;
        width: 10%; 
        border-bottom: 1px solid;
    }

    table, th, td{
        border: solid 1px #BDBDBD;
        padding: .3rem;
        border: none;

    }

    table{
        color: #424242;
        font-size: .8rem;
    }

    tr:nth-child(odd){
        background-color: #fbfbfb;

    }

    tr:nth-child(even) {

        background-color: #f2f2f2;
    }

    .turns{
        width: 100%;
        margin-top: 1rem; 
    }
</style>
<div class='panel'>

	 <div class='row container-table'>
        <table class='turns' id='turns'>
            <tr>
                <th scope='col'>{l s='Estado' mod='vexurbaner'}</th>
                {* <th scope='col'>{l s='ID' mod='vexurbaner'}</th> *}
                <th scope='col'>{l s='Orden' mod='vexurbaner'}</th>
                <th scope='col'>{l s='Orden Prestashop' mod='vexurbaner'}</th>
                <th scope='col'>{l s='Enviar a' mod='vexurbaner'}</th>
                <th scope='col'>{l s='Codigo Ciudad' mod='vexurbaner'}</th>
                <th scope='col'>{l s='Fecha de Creacion' mod='vexurbaner'}</th>
                <th scope='col'>{l s='Entrega preferida' mod='vexurbaner'}</th>
                <th scope='col'>{l s='Accion' mod='vexurbaner'}</th>
            <tr>
            {if $orders}
                {foreach $orders as $value}
                    <tr>
                        <td>{l s=$value.status|escape:'html':'UTF-8' mod='vexurbaner'}</td>
                        <td>{l s=$value.id_traking|escape:'html':'UTF-8' mod='vexurbaner'}</td>
                        <td>{l s=$value.referency|escape:'html':'UTF-8' mod='vexurbaner'}</td>
                        <td>{l s=$value.address|escape:'html':'UTF-8' mod='vexurbaner'}</td>
                        <td>{l s=$value.code_city|escape:'html':'UTF-8' mod='vexurbaner'}</td>
                        <td>{l s=$value.date_creation|escape:'html':'UTF-8' mod='vexurbaner'}</td>
                        <td>{l s=$value.date|escape:'html':'UTF-8' mod='vexurbaner'}</td>


                           
                        {* <td><input type='button' value='Send' onclick='sendOrder({$value.id_vexurbaner})'></td> 
                            *}
                        <td>
                            <div class=''>
                            {if $value.response != 1}
                                {* {if $status.status == $value.status}
                    	        <button type='submit' value='{$value.id_vexurbaner|escape:'htmlall':'UTF-8'}' name='submitOrderSendAction' class='btn btn-default pull-left'>
                        	        {l s='    ENVIAR    ' mod='vexurbaner'}
                   		        </button>
                                {/if} *}
						        <button type='submit' value='{$value.id_vexurbaner|escape:'html':'UTF-8'}' name='submitOrderDeleteAction' class='btn btn-default pull-right'>
                        	        {l s='    BORRAR    ' mod='vexurbaner'}
                   		        </button>

                	        </div>
                            {/if}
                        </td>
                            
                    </tr>
                {/foreach}
            {/if}
        
        </table>
    </div>
   
</div>


