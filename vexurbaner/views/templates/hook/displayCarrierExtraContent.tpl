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


<div class='form-group'>
			<label class='control-label col-lg-6 required' >{l s='Si desea puede cambiar la dirección del envío.' mod='vexurbaner'}</label>
            <div class='col-lg-12'>
                <input type='text' id="new_address" style="width: 60%; margin-bottom: 10px;" placeholder='Nueva direcciòn de envìo'  name='new_address'/>
            </div>

             <div class='col-lg-12'>
                <input type='text' id="new_address2" style="width: 60%; margin-bottom: 10px;" placeholder='detalles de direcciòn'  name='new_address'/>
            </div>			
    {* <button type="button" id="btn_new_address" class="btn btn-primary" data-toggle="modal" data-target="#modalMap">Cambiar Dirección de envío</button> *}
</div>
<script>
 

    document.addEventListener("DOMContentLoaded", function(){

        let btn = document.getElementByName('confirmDeliveryOption');
        btn[0].addEventListener('click', () => {
            let text  = document.getElementsById('delivery_message');
            console.log(text.value);

        })

    }, false);

</script>