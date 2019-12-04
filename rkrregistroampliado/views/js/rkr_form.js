window.multidrop = function() {
  $('.rkr_dropdown_multi').each(function (i) {
    var $me = $(this);
    $me.multiSelect({
      selectableHeader: "<div class='custom-header'>" + $me.data("header-list") + "</div>",
      selectionHeader: "<div class='custom-header'>" + $me.data("header-selected") + "</div>"
    });
  });
};

$(document).ready(function () {
  window.multidrop();

  if(typeof(C4YCreateForm) !== undefined) {
    var nth_child = $('#customer-form .form-group').length;
    var target = $('#customer-form .form-group').eq(nth_child-2);
    $("#rkr-front-wrap .form-group").insertBefore(target);
  }
});
