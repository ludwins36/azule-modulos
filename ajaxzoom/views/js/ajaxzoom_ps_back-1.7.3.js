/*!
*  Module: jQuery AJAX-ZOOM for Prestashop, ajaxzoom_ps_back-1.7.3.js
*  Version: 1.13.0
*  Date: 2018-09-14
*  Review: 2018-09-14
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2018 AJAX-ZOOM, Vadim Jacobi
*  @license   https://www.ajax-zoom.com/index.php?cid=download
*/

jQuery(document).ready(function($) {
    $('#module_ajaxzoom .label-tooltip').each(function() {
        if ($.fn.pstooltip) {
            $(this).attr('data-toggle', 'pstooltip').pstooltip();
        } else if ($.fn.tooltipster) {
            var _this = $(this);
            _this.tooltipster({
                animation: 'fade',
                delay: 100,
                contentAsHTML: true,
                interactive: true,
                maxWidth: 350,
                content: function() {
                    return _this.attr('data-original-title');
                }
            });
        }
    })
});
