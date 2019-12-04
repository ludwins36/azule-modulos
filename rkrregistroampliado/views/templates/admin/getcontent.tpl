{*
*
* NOTICE OF LICENSE
*
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by Rekire.com
*
* @author     Rekire - Tecnología web
* @copyright  2018 Rekire.com All right reserved
* @license    Rekire
* @contact    info@rekire.com
*
*}

<div id="rkr-wrap" class="panel">
  <div class="panel-heading">
    Rekire.com - Módulo de registro ampliado
  </div>
  <div class="form-wrapper">
    Este módulo nos permite integrar los campos de dirección en el formulario de registro. <br /> Para posibles dudas o sugerencias contáctenos en: <a href="mailto:info@rekire.com">info@rekire.com</a><br /><br /> Recuerde que este módulo funciona bajo
    licencia privada y en caso de ser instalado en otra web diferente a la indicada en el proceso de su compra serémos notificados por ello.
    <br/><br/>
    <form class="defaultForm form-horizontal"
          action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}"
          method="post"
          enctype="multipart/form-data"
          novalidate=""
          name="form_rkr_datos"
          id="form_rkr_datos">
      <div class="form-group row">
        <div class="control-label col-md-3"><strong>License key: </strong></div>
        <div class="col-md-7">
          <input type="text" name="licensekey" id="licensekey" placeholder="Inserte su número de licencia" value="{$license_key}" />
        </div>
        <div class="col-md-2">
            <span style="color:{$color};">{$activated}</span>
        </div>
      </div>
      <div class="form-group row">
          <div class="control-label col-md-6" style="text-align: center;">
              <a href="https://www.rekire.com/producto/modulo-de-registro-ampliado/" target="_blank">
                  <img src="http://www.rekire.com/wp-content/uploads/baner-1-rkr.jpg" width="70%"/>
              </a>
          </div>
          <div class="control-label col-md-6" style="text-align: center;">
              <a href="https://www.rekire.com/servicios-prestashop/" target="_blank">
                  <img src="http://www.rekire.com/wp-content/uploads/baner-2-rkr.jpg" width="70%"/>
              </a>
              <!-- <img src="{$smarty.server.SERVER_NAME}{$smarty.server.BASE}/../modules/rkrregistroampliado/views/images/baner2.jpg" /> -->
          </div>
      </div>
      <div class="panel-footer">
        <button type="submit" value="1" id="insert_licensekey" name="insert_licensekey" class="btn btn-default pull-right">
    		  <i class="process-icon-save"></i> Guardar
    		</button>
      </div>
    </form>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {

    // Rekire - Luis Jordán :: Ocultamos el formulario de insertar nuevos campos.
    // $('#configuration_form').hide();
    // Rekire - Luis Jordán :: Ocultamos botones de add y reload
    $('.process-icon-new').hide();
    $('.process-icon-refresh').hide();


    // Tags input
    var $parentFieldSelector = $(".bootstrap input.fieldsSelector").parent().parent();
    if ($parentFieldSelector.length === 0) {
      $parentFieldSelector = $("input.fieldsSelector").parent();
    }
    // Group selector
    var $parentGroupSelector = $('.bootstrap input.groupSelector').parent().parent().parent().parent();
    if ($parentGroupSelector.length === 0) {
      $parentGroupSelector = $("input.groupSelector").parent();
    }
    // Filetype checkbox
    var $parentFilesType = $('.bootstrap #filesType_1').parent().parent().parent().parent();
    if ($parentFilesType.length == 0) {
      $parentFilesType = $("#filesType_1").parent();
    }

    var displayValuesController = function() {
      if ($("#type").val() === "select" || $("#type").val() === "dropdown_multi" || $("#type").val() === "radio") {
        $parentFieldSelector.show();
      } else {
        $parentFieldSelector.hide();
      }

      if ($("#type").val() === "group") {
        $parentGroupSelector.show();
      } else {
        $parentGroupSelector.hide();
      }

      if ($("#type").val() === "upload") {
        $parentFilesType.show();
      } else {
        $parentFilesType.hide();
      }
    };

    $parentFieldSelector.hide();
    $parentFilesType.hide();
    displayValuesController();

    $("#type").on("change", function() {
      displayValuesController();
    });

    $("input.fieldsSelector").tagsInput({
      'height': '75px',
      'width': '100%',
      'interactive': true,
      'defaultText': '{l s='
      add a tag ' mod='
      rkrregistroampliado '}',
      'delimiter': [';'],
      'removeWithBackspace': true,
      'placeholderColor': '#666666'
    });
  });
</script>
