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
			<label class='control-label col-lg-3 required' >{l s='Direccón a enviar' mod='vexurbaner'}</label>
            <div class='col-lg-10'>
                <input type='text'  name='new_address' value='{$address|escape:'htmlall':'UTF-8'}'/>
            </div>			
    <button type="button" id="btn_new_address" class="btn btn-primary" data-toggle="modal" data-target="#modalMap">Cambiar Dirección de envío</button>
</div>
<!-- Modal -->
<div class="modal fade" id="modalMap" tabindex="-1" role="dialog" aria-labelledby="modalMap" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Cambiar Dirección</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class='col-lg-10'>
            <input type='text' placeholder="Escriba su nueva dirección"  name='new_address_modal' />
        </div>	
        <div id="map"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>

    let lat = "{$lat}";
    let lnt = "{$lnt}";

   jQuery(document).ready(function() {
                    if (typeof google === 'undefined') {
                        let script = document.createElement('script');
                        script.type = 'text/javascript';
                        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + {$apiGoogle} + '&libraries=places&sensor=false&callback=initMapGlovo';
                        document.head.appendChild(script);

                    } else {
                        initMapGlovo();
                    }

    });
</script>
