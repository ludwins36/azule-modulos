/*!
*  Module: jQuery AJAX-ZOOM for Prestashop, ajaxzoom_ps_back.js
*  Version: 1.13.0
*  Date: 2018-09-14
*  Review: 2018-09-14
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2017 AJAX-ZOOM, Vadim Jacobi
*  @license   https://www.ajax-zoom.com/index.php?cid=download
*/

$(document).ready(function() {
    $('#link-ModuleAjaxzoom > a').html('AJAX-ZOOM');
});

window.az_pannel_toggle = function() {
    $('#module_ajaxzoom .panel-heading, #product-tab-content-ModuleAjaxzoom .panel-heading')
    .css('cursor', 'pointer')
    .on('click', function() {
        var _this = $(this);
        var _next = _this.next();
        _next.slideToggle(150, function(a) {
            var state = _next.css('display');
            var id = _this.attr('id');

            if (state == 'none') {
                $('.az_close_heading', _this)
                .removeClass('fa-minus-square')
                .addClass('fa-plus-square-o');
            } else {
                $('.az_close_heading', _this)
                .removeClass('fa-plus-square-o')
                .addClass('fa-minus-square');
            }

            if (id) {
                if (window.sessionStorage) {
                    if (!window.sessionStorage.getItem('azProdTabs')) {
                        window.sessionStorage.setItem('azProdTabs', '{}');
                    }

                    var stngs = window.sessionStorage.getItem('azProdTabs');
                    stngs = JSON.parse(stngs);
                    stngs[id] = state;
                    window.sessionStorage.setItem('azProdTabs', JSON.stringify(stngs));
                }
            }
        } );
    } );

    $('[id^=az_panel_]').each(function() {
        var closeb = $('<i class="fa fa-minus-square-o pull-right az_close_heading" style="margin-top: 5px; font-size: 120%;" href="#"></i>');
        $(this).append(closeb);
    } );

    setTimeout(function() {
        if (window.sessionStorage && window.sessionStorage.getItem('azProdTabs')) {
            var stngs = window.sessionStorage.getItem('azProdTabs');
            stngs = JSON.parse(stngs);
            if (typeof stngs == 'object') {
                $.each(stngs, function(a, b){
                    if (b == 'none') {
                        $('#'+a).next().slideToggle(0);

                        $('.az_close_heading',  $('#'+a))
                        .removeClass('fa-minus-square')
                        .addClass('fa-plus-square-o');
                    }
                });
            }
        }
    }, 0);
};
