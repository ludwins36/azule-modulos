/**
 * Reset numbering on tab buttons
 */

function reNumberPages() {
  var tabCount = $("#pageTab > li").length;
  $("#pageTab > li").each(function () {
    var pageId = $(this)
      .children("a")
      .attr("href");
    if (pageId == "#page1") {
      return true;
    }
    pageNum++;
    $(this)
      .children("a")
      .html(
        "Page " +
        pageNum +
        "<button class='close' type='button' " +
        "title='Remove this page'>×</button>"
      );
  });
}

$(document).ready(function () {
  /**
   * Add a Tab
   */

  $("#btnAddPage").click(function () {
    pageNum++;
    $("#pageTab").append(
      $(
        "<li ><a href='#page" +
        pageNum +
        "'>" +
        "Tienda" +
        pageNum +
        "<button class='close' type='button' " +
        "title='Eliminar Tienda'>×</button>" +
        "</a></li>"
      )
    );

    $("#pageTabContent").append(
      $(
        "<div class='tab-pane' id='page" +
        pageNum +
        "'>" +
        "<div class='panel' id='fieldset_0'>" +
        "<div class='panel-heading'>" +
        "<i class='icon-cogs'></i>" +
        "Configuración General" +
        "</div>" +
        "<div class='form-wrapper'>" +
        "<input type='hidden' name='submitStoreIdUrbaner' value='" +
        pageNum +
        "'/>" +
        "<div class='form-group'>" +
        "<label class='control-label col-lg-3'>Nombre de la Tienda'</label>" +
        "<div class='col-lg-6'>" +
        "<input type='text'  name='name' />" +
        "<p class='help-block'>Debe ser igaul al nombre del admin de la tienda.</p>" +
        "</div>" +
        "</div>" +
        "<div class='form-group'>" +
        "<label class='control-label col-lg-3'>Dirección de la tienda</label>" +
        "<div class='col-lg-6'>" +
        "<input type='text'  name='address' value=''/>" +
        "<p class='help-block'>Ej: Jose Leal 560, Interior 5</p>" +
        "</div>" +
        "</div>" +
        "<div class='form-group'>" +
        "<label class='control-label col-lg-3'>Referencia</label>" +
        "<div class='col-lg-6'>" +
        "<input type='text'  name='address2' value=''/>" +
        "</div>" +
        "</div>" +
        "<div class='form-group'>" +
        "<label class='control-label col-lg-3'>Correo de la tienda</label>" +
        "<div class='col-lg-6'>" +
        "<input type='text'  name='mail' value=''/>" +
        "</div>" +
        "</div>" +
        "<div class='form-group'>" +
        "<label class='control-label col-lg-3'>Código Postal</label>" +
        "<div class='col-lg-6'>" +
        "<input type='text'  name='zipCode' value=''/>" +
        "</div>" +
        "</div>" +
        "<div class='form-group'>" +
        "<label class='control-label col-lg-3'>Latitud</label>" +
        "<div class='col-lg-6'>" +
        "<input type='text'  name='lat' value=''/>" +
        "</div>" +
        "</div>" +
        "<div class='form-group'>" +
        "<label class='control-label col-lg-3'>Longitud</label>" +
        "<div class='col-lg-6'>" +
        "<input type='text'  name='lnt' value=''/>" +
        "<p class='help-block'>Descubre la Latitud y Longitud <a role='button' href='https://www.latlong.net/'>AQUÍ</a></p>" +
        "</div>" +
        "</div>" +
        "<div class='form-group'>" +
        "<label class='control-label col-lg-3'>Persona de contacto</label>" +
        "<div class='col-lg-6'>" +
        "<input type='text'  name='persone' value=''/>" +
        "</div>" +
        "</div>" +
        "<div class='form-group'>" +
        "<label class='control-label col-lg-3'>Telefono de contacto</label>" +
        "<div class='col-lg-6'>" +
        "<input type='text'  name='phone' value=''/>" +
        "</div>" +
        "</div>" +
        "<div class='form-group'>" +
        "<label class='control-label col-lg-3'>{l s='Tiempo de preparación' mod='vexurbaner'}</label>" +
        "<div class='col-lg-6'>" +
        "<input type='text'  name='time' />" +
        "</div>" +
        "</div>" +
        "<div class='panel-footer'>" +
        "<button type='submit' value='1' name='submitStoreAction' class='btn btn-default pull-right'>" +
        "<i class='process-icon-save' ></i>" +
        "    GUARDAR    " +
        "</button>" +
        "</div>" +
        "</div>" +
        "</div>" +
        "</div>"
      )
    );
    $("#page" + pageNum).tab("show");
  });

  /**
   * Remove a Tab
   */
  $("#pageTab").on("click", " li a .close", function () {
    var tabId = $(this)
      .parents("li")
      .children("a")
      .attr("href");
    $(this)
      .parents("li")
      .remove("li");
    $(tabId).remove();
    $("#pageTab a:first").tab("show");
  });

  /**
   * Click Tab to show its content
   */
  $("#pageTab").on("click", "a", function (e) {
    e.preventDefault();
    $(this).tab("show");
  });
});