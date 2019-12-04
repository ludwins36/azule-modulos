/*!
*  Module: jQuery AJAX-ZOOM for Prestashop, ajaxzoom_ps_front.js
*  Version: 1.19.0
*  Date: 2019-04-30
*  Review: 2019-04-30
*  @author    AJAX-ZOOM <support@ajax-zoom.com>
*  @copyright 2010-2019 AJAX-ZOOM, Vadim Jacobi
*  @license   https://www.ajax-zoom.com/index.php?cid=download
*/
;(function($){

    $.axZm_psh || ($.axZm_psh = {});

    var compVersions = function(strV1, strV2) {
        var nRes = 0, parts1 = strV1.split('.'), parts2 = strV2.split('.'), nLen = Math.max(parts1.length, parts2.length);
        for (var i = 0; i < nLen; i++) {
            var nP1 = (i < parts1.length) ? parseInt(parts1[i], 10) : 0, nP2 = (i < parts2.length) ? parseInt(parts2[i], 10) : 0;
            if (isNaN(nP1)) { nP1 = 0; } if (isNaN(nP2)) { nP2 = 0; } if (nP1 != nP2) { nRes = (nP1 > nP2) ? 1 : -1; break; } 
        }

        return nRes;
    };

    Function.prototype.axzm_psh_clone||(Function.prototype.axzm_psh_clone=function(){var c=this,b=function(){return c.apply(this,arguments)},a;for(a in this)this.hasOwnProperty(a)&&(b[a]=this[a]);return b});

    $.axZm_psh.getCurrentCombination = function() {
        if (typeof window.combinationsHashSet !== 'undefined' && window.location.hash) {
            var choice = [];
            var radio_inputs = parseInt($('#attributes .checked > input[type=radio]').length);
            if (radio_inputs) {
                radio_inputs = '#attributes .checked > input[type=radio]';
            } else {
                radio_inputs = '#attributes input[type=radio]:checked';
            }

            $('#attributes select, #attributes input[type=hidden], ' + radio_inputs).each(function() {
                choice.push(parseInt($(this).val()));
            });

            var combination = combinationsHashSet[choice.sort().join('-')];

            if (combination && $.isArray(combination) && combination['idCombination']) {
                return combination['idCombination'];
            }

            return false;
        } else {
            return false;
        }
    };

    $.axZm_psh.mouseOverZoomInit = function(o) {
        if ($.axZm_psh.displayInTab) {
            if ($.axZm_psh.found_tab && $.axZm_psh.tabC.length) {
                if ($.axZm_psh.tabC.is(':visible')) {
                    $.mouseOverZoomInit(o);
                    $.axZm_psh.tabC.removeData('axzm_psh_data');
                } else {
                    $.axZm_psh.tabC.data('axzm_psh_init_data', o);
                    $.axZm_psh.tabC.data('axzm_psh_data', o);
                }
            }
        } else {
            $.mouseOverZoomInit(o);
        }
    };

    $.axZm_psh.replaceImages = function(o) {
        if ($.axZm_psh.displayInTab) {
            if ($.axZm_psh.found_tab && $.axZm_psh.tabC.length) {
                if ($.axZm_psh.tabC.is(':visible')) {
                    if ($('.axZm_mouseOverZoomContainer', $.axZm_psh.tabC).data('aZ')) {
                        $.mouseOverZoomInit.replaceImages(o);
                    } else {
                        console.log('initing');
                        $.mouseOverZoomInit($.extend({}, $.axZm_psh.tabC.data('axzm_psh_init_data'), o));
                    }

                    $.axZm_psh.tabC.removeData('axzm_psh_data');
                } else {
                    $.axZm_psh.tabC.data('axzm_psh_data', o);
                }
            }
        } else {
            $.mouseOverZoomInit.replaceImages(o);
        }
    };

    $.axZm_psh.redefineRefreshFunc = function() {
        if ($.axZm_psh.displayInTab || $.axZm_psh.displayInSelector) {
            if (typeof window.refreshProductImagesClone === 'function') {
                window.refreshProductImages = function(id_product_attribute, ovrLock, initParam, imm) {
                    window.refreshProductImagesClone(id_product_attribute);
                    $.axZm_psh.refreshProductImages(id_product_attribute, ovrLock, initParam, imm);
                };
            } else {
                window.refreshProductImages = $.axZm_psh.refreshProductImages;
            }
        } else {
            window.refreshProductImages = $.axZm_psh.refreshProductImages;
        }
    };

    $.axZm_psh.placeIntoTab = function() {
        if ($.axZm_psh.displayInTab) {
            if (!$.isEmptyObject($.axZm_psh.IMAGES_360_JSON) || !$.isEmptyObject($.axZm_psh.VIDEOS_JSON)) {
                if ($.axZm_psh.createTabs()) {
                    $('#axZm_mouseOverWithGalleryContainer')
                    .appendTo('#axZmInTabContainer')
                    .css('display', 'block');
                } else {
                    console.log('AJAX-ZOOM: tabs not found in this template');
                }
            }
        }
    };

    $.axZm_psh.placeIntoSelector = function() {
        if ($.axZm_psh.displayInSelector) {
            if (!($.isEmptyObject($.axZm_psh.IMAGES_360_JSON) && $.isEmptyObject($.axZm_psh.VIDEOS_JSON))) {
                var sel = $.axZm_psh.displayInSelector;
                if (sel.indexOf(':eq') == -1) {
                    sel += ':eq(0)';
                }

                if ($(sel).length) {
                    var wraper = $('<div style="position:relative" class="axzm_display_in_selector"></div>');
                    wraper.append($('#axZm_mouseOverWithGalleryContainer').css('display', 'block'));
                    if ($.axZm_psh.displayInSelectorAppend) {
                        wraper.appendTo(sel);
                    } else {
                        wraper.prependTo(sel);
                    }

                    $.axZm_psh.visIntervalInit();
                } else {
                    console.log('AJAX-ZOOM: ' + $.axZm_psh.displayInSelector + ' selector not found.');
                }
            }
        }
    };

    $.axZm_psh.changeLayout = function() {
        if (!$.axZm_psh.displayInTab && !$.axZm_psh.displayInSelector) {
            if ($.axZm_psh.ps15 === true || $.axZm_psh.ps16 === true) {
                // Change layout
                $.axZm_psh.imgBlockTemp = $("#image-block");
                $.axZm_psh.axzoomdivRef = $("#axZm_mouseOverWithGalleryContainer");

                // Preserve layout
                $('.new-box', $.axZm_psh.imgBlockTemp).attr('id', 'new-box-AJAX-ZOOM').prependTo($.axZm_psh.axzoomdivRef);
                $('.sale-box', $.axZm_psh.imgBlockTemp).attr('id', 'sale-box-AJAX-ZOOM').prependTo($.axZm_psh.axzoomdivRef);
                $('.discount', $.axZm_psh.imgBlockTemp).attr('id', 'discount-AJAX-ZOOM').prependTo($.axZm_psh.axzoomdivRef);
                $('.resetimg').css('padding-top', 30);

                // Replace default viewer
                $.axZm_psh.axzoomdivRef.insertBefore($.axZm_psh.imgBlockTemp).css('display', 'block');
                $('#views_block').css('display', 'none');
                $.axZm_psh.imgBlockTemp.css('display', 'none');

                // Template override
                $('.pb-left-column,.container-left').css('overflow', 'visible');

                // Clear
                $.axZm_psh.imgBlockTemp = null;
                $.axZm_psh.axzoomdivRef = null;
            } else {
                if ($('#main .page-content>.images-container').length) {
                    $('#axZm_mouseOverWithGalleryContainer')
                    .insertBefore('#main .page-content>.images-container')
                    .css('display', 'block');
                } else if ($('#main .images-container').length) {
                    $('#axZm_mouseOverWithGalleryContainer')
                    .insertBefore('#main .images-container')
                    .css('display', 'block');
                } else {
                    $('#axZm_mouseOverWithGalleryContainer')
                    .prependTo('#main .page-content')
                    .css('display', 'block');
                }
            }
        }
    };

    $.axZm_psh.bindEvents = function() {
        if ($.axZm_psh.ps17 == true) {
            prestashop.on('updatedProduct', function(ret) {
                window.refreshProductImages(ret.id_product_attribute);
            });

            if ($.axZm_psh.ajaxzoom_attribute_id
                && $.axZm_psh.combinationImages[$.axZm_psh.ajaxzoom_attribute_id]
            ) {
                if (!$.axZm_psh.displayInTab && !$.axZm_psh.displayInSelector && $.axZm_psh.showAllImgBtn) {
                    $('#az_wrapResetImages')
                    .insertAfter('#axZm_mouseOverWithGalleryContainer')
                    .css('display', 'inline-block');
                }

                window.refreshProductImages($.axZm_psh.ajaxzoom_attribute_id, true, true);
            } else {
                $.axZm_psh.mouseOverZoomInit($.axZm_psh.initParam);
            }
        } else {
            var comb = $.axZm_psh.getCurrentCombination();
            if (comb === false && window.axZm_tmp_combination) {
                comb = window.axZm_tmp_combination;
            }

            if (comb) {
                window.refreshProductImages(comb, false, true, true);
            } else {
                if (!$.axZm_psh.displayInTab) {
                    $('#wrapResetImages').hide();
                }

                $.axZm_psh.mouseOverZoomInit($.axZm_psh.initParam);
            }
        }
    };

    $.axZm_psh.applyHeaderFix = function() {
        // Fix for header in some themplates
        if (!$.axZm_psh.displayInTab && $.axZm_psh.headerZindex && $('#header').css('zIndex') < $.axZm_psh.headerZindex) {
            $('#header').css('zIndex', $.axZm_psh.headerZindex);
        }
    };

    $.axZm_psh.visIntervalInit = function() {
        if ($.axZm_psh.visInt) {
            clearInterval($.axZm_psh.visInt);
        }

        var state = $('#axZm_mouseOverWithGalleryContainer').is(':visible');
        $.axZm_psh.visInt = setInterval(function() {
            var vis = $('#axZm_mouseOverWithGalleryContainer').is(':visible');
            if (state != vis && vis) {
                $(window).trigger('resize');
                if ($.axZm) {
                    $.fn.axZm.resizeStart(1);
                }
            }

            state = vis;
        }, 1000);
    };

    // override _PS native function
    $.axZm_psh.refreshProductImages = function(id_product_attribute, ovrLock, initParam, imm) {

        var combinationImages = window.combinationImages;

        if ($.axZm_psh.ps17 && $.axZm_psh.combinationImages) {
            combinationImages = $.axZm_psh.combinationImages;
        }

        if ($.axZm_psh.refreshProductImagesLock && !ovrLock) {
            // psh 1.5 
            if ($.axZm_psh.ps15 && $.axZm_psh.prevPid == 0) {
                refreshProductImages(id_product_attribute, true);
                return;
            } else {
                return;
            }
        }

        if (typeof(id_product_attribute) != 'undefined') {
            $.axZm_psh.prevPid = id_product_attribute;
        }

        $.axZm_psh.refreshProductImagesLock = true;

        setTimeout(function() {
            $.axZm_psh.refreshProductImagesLock = false; 
        }, 500);

        id_product_attribute = parseInt(id_product_attribute);
        $.axZm_psh.ajaxzoom_attribute_id = id_product_attribute;

        if (id_product_attribute > 0
            && typeof(combinationImages) != 'undefined'
            && typeof(combinationImages[id_product_attribute]) != 'undefined'
        ) {
            var imagesArr = combinationImages[id_product_attribute];
            var newAZimagesObj = {};
            var newAZ360Obj = {};
            var newAZVideosObj = {};

            $.each(imagesArr, function(k, v) {
                  newAZimagesObj[v] = $.axZm_psh.IMAGES_JSON[v];
            });

            $.each($.axZm_psh.IMAGES_360_JSON, function(k, v) {
                if (v.combinations && (!v.combinations[0] || $.inArray(id_product_attribute, v.combinations) != -1)) {
                    newAZ360Obj[k] = v;
                }
            });

            $.each($.axZm_psh.VIDEOS_JSON, function(k, v) {
                if (v.combinations && (!v.combinations[0] || $.inArray(id_product_attribute+'', v.combinations) != -1)) {
                    newAZVideosObj[k] = v;
                }
            });

            if (
                initParam
                || ($.axZm_psh.CURRENT_IMAGES_JSON && $.toJSON($.axZm_psh.CURRENT_IMAGES_JSON) != $.toJSON(newAZimagesObj))
                || ($.axZm_psh.CURRENT_IMAGES_360_JSON && $.toJSON($.axZm_psh.CURRENT_IMAGES_360_JSON) != $.toJSON(newAZ360Obj))
                || ($.axZm_psh.CURRENT_VIDEOS_JSON && $.toJSON($.axZm_psh.CURRENT_VIDEOS_JSON) != $.toJSON(newAZVideosObj))
            ) {
                var thisChange = function() {
                    if (initParam) {
                        $.axZm_psh.initParam.images = newAZimagesObj;
                        $.axZm_psh.initParam.images360 = newAZ360Obj;
                        $.axZm_psh.initParam.videos = newAZVideosObj;
                        $.axZm_psh.mouseOverZoomInit($.axZm_psh.initParam);
                    } else {
                        $.axZm_psh.replaceImages( {
                            divID: $.axZm_psh.divID,
                            galleryDivID: $.axZm_psh.galleryDivID,
                            images: newAZimagesObj,
                            images360: newAZ360Obj,
                            videos: newAZVideosObj
                        });
                    }

                    if (compVersions($.axZm_psh.ps_version, '1.7') == -1) {
                        $('#wrapResetImages')
                        .stop(true, true)
                        .show()
                        .unbind('.az')
                        .bind('click.az', function() {
                            $.axZm_psh.replaceImages( {
                                divID: $.axZm_psh.divID,
                                galleryDivID: $.axZm_psh.galleryDivID,
                                images: $.axZm_psh.IMAGES_JSON,
                                images360: $.axZm_psh.IMAGES_360_JSON,
                                videos: $.axZm_psh.VIDEOS_JSON
                            });
                        });
                    } else {
                        if ($.axZm_psh.showAllImgBtn == true) {

                            var ll = $.axZm_psh.showAllImgTxt[$.axZm_psh.shopLang] || $.axZm_psh.showAllImgTxt['en'];
                            if (ll) {
                                $('#az_wrapResetImages')
                                .css('visibility', 'visible')
                                .unbind('.az')
                                .bind('click.az', function() {
                                    $(this).css('visibility', 'hidden');
                                    $.axZm_psh.replaceImages( {
                                        divID: $.axZm_psh.divID,
                                        galleryDivID: $.axZm_psh.galleryDivID,
                                        images: $.axZm_psh.IMAGES_JSON,
                                        images360: $.axZm_psh.IMAGES_360_JSON,
                                        videos: $.axZm_psh.VIDEOS_JSON
                                    });
                                });

                                $('#az_wrapResetImages>span').html(ll);
                            }
                        }
                    }
                };

                if (imm) {
                    thisChange();
                } else {
                    if (document.readyState == "complete") {
                        thisChange();
                    }else{
                        $(document).ready(function() {
                            setTimeout(thisChange, 1);
                        });
                    }
                }
            }

            $.axZm_psh.CURRENT_IMAGES_JSON = $.extend(true, {} , newAZimagesObj); // copy
            $.axZm_psh.CURRENT_IMAGES_360_JSON = $.extend(true, {} , newAZ360Obj); // copy
            $.axZm_psh.CURRENT_VIDEOS_JSON = $.extend(true, {} , newAZVideosObj); // copy

        } else if (id_product_attribute === 0) {
            if (compVersions($.axZm_psh.ps_version, '1.7') == -1) {
                $('#wrapResetImages').stop(true, true).hide();
            } else if ($.axZm_psh.showAllImgBtn == true) {
                $('#az_wrapResetImages').stop(true, true).css('visibility', 'hidden');
            }
        } else if (initParam && id_product_attribute && typeof(combinationImages) == 'undefined') {
            $.axZm_psh.mouseOverZoomInit($.axZm_psh.initParam);
        }
    };

    $.axZm_psh.tab = $();
    $.axZm_psh.tabC = $();

    $.axZm_psh.createTabs = function() {
        var tab_name = $.axZm_psh.tabName || '360 / Video';
        var found_tab = false;
        var inTabPosition = $.axZm_psh.inTabPosition;
        var placeZ = '<!-- Place for AJAX-ZOOM -->';
        var idx;

        var tbClick = function() {
            var nn = 0;
            var to = function() {
                setTimeout(function() {
                    if ($.axZm_psh.tabC.is(':visible')) {
                        var o = $.axZm_psh.tabC.data('axzm_psh_data');
                        if (o) {
                            $.axZm_psh.replaceImages(o);
                        } else {
                            $(window).trigger('resize');
                            if ($.axZm) {
                                $.fn.axZm.resizeStart(1);
                            }
                        }
                    } else {
                        nn++;
                        if (nn < 20) {
                            to();
                        }
                    }
                }, 100);
            };

            to();
        };

        if ($('.page-product-box').length) {
            // 1.6
            $.axZm_psh.tab = $('<h3 class="page-product-heading axZmInTab">'+tab_name+'</h3>');
            $.axZm_psh.tabC = $('<div id="axZmInTabContainer">'+placeZ+'</div>');
            found_tab = 1;

            var el = $('.page-product-box:eq(0)')
            .clone()
            .empty()
            .append($.axZm_psh.tab)
            .append($.axZm_psh.tabC);

            if (inTabPosition == 'first') {
                el.insertBefore($('.page-product-box:first'));
                setTimeout(function(){
                    $.axZm_psh.tab.trigger('click');
                }, 1)
            } else if (inTabPosition == 'afterFirst') {
                el.insertAfter($('.page-product-box:first'));
            } else if (inTabPosition == 'beforeLast') {
                el.insertBefore($('.page-product-box:last'));
            } else {
                // last
                el.insertAfter($('.page-product-box:last'));
            }
        } else if ($('.nav-tabs').length) {
            // 1.7
            found_tab = 1;

            $.axZm_psh.tabC = $('<div id="axZmInTabContainer">'+placeZ+'</div>');
            $.axZm_psh.tab = $('.nav-tabs li:first').clone().addClass('axZmInTab');

            $('a', $.axZm_psh.tab)
            .removeClass('active')
            .attr('href', '#axZmInTabDisplay')
            .attr('aria-controls', 'axZmInTabDisplay')
            .attr('aria-expanded', 'false')
            .html(tab_name);

            if (inTabPosition == 'first') {
                $.axZm_psh.tab.prependTo($('.nav-tabs'));
                setTimeout(function(){
                    $('a', $.axZm_psh.tab).trigger('click');
                }, 1)
            } else if (inTabPosition == 'afterFirst') {
                $.axZm_psh.tab.insertAfter($('.nav-tabs li:first'));
            } else if (inTabPosition == 'beforeLast') {
                $.axZm_psh.tab.insertBefore($('.nav-tabs li:last'));
            } else {
                // last
                $.axZm_psh.tab.appendTo($('.nav-tabs'));
            }

            var navContent = $('#description')
            .clone()
            .empty()
            .removeClass('active')
            .attr('id', 'axZmInTabDisplay')
            .append($.axZm_psh.tabC);

            navContent.appendTo('#tab-content');
        } else if ($('#more_info_tabs').length) {
            // 1.5
            found_tab = 1;
            $.axZm_psh.tabC = $('<div id="axZmInTabContainer">'+placeZ+'</div>');
            idx = $('#more_info_tabs').length;
            idx += 1;

            $.axZm_psh.tab = $('#more_info_tabs li:first').clone().addClass('axZmInTab');

            $('a', $.axZm_psh.tab)
            .removeClass('selected')
            .attr('href', '#idTab'+idx)
            .attr('id', '#axZmInTabDisplayA')
            .html(tab_name);

            if (inTabPosition == 'first') {
                $.axZm_psh.tab.prependTo($('#more_info_tabs'));
                setTimeout(function(){
                    $('a', $.axZm_psh.tab).trigger('click');
                }, 1)
            } else if (inTabPosition == 'afterFirst') {
                $.axZm_psh.tab.insertAfter($('#more_info_tabs li:first'));
            } else if (inTabPosition == 'beforeLast') {
                $.axZm_psh.tab.insertBefore($('#more_info_tabs li:last'));
            } else {
                // last
                $.axZm_psh.tab.appendTo($('#more_info_tabs'));
            }

            var navContent = $('#more_info_sheets>div:eq(0)')
            .clone()
            .empty()
            .attr('id', 'idTab'+idx)
            .addClass('block_hidden_only_for_screen')
            .append($.axZm_psh.tabC);

            navContent.appendTo('#more_info_sheets');
        }

        $.axZm_psh.found_tab = found_tab && $.axZm_psh.tabC.length && $.axZm_psh.tab.length;

        if ($.axZm_psh.tab.length) {
            if ($.axZm_psh.ps15) {
                $('.idTabs').each(function() {
                    var _this = $(this);
                    $('li>a', _this).unbind();
                });

                $('.idTabs').each(function() {
                    $(this).unbind().idTabs({'click': function(a, b, c) {
                        if (a == '#idTab'+idx) {
                            tbClick();
                        }

                        return true;
                    }});
                });
            } else {
                $('body').on('click.az', '.axZmInTab', tbClick);
            }
        }

        return $.axZm_psh.found_tab;
    };

    $.axZm_psh.go = function() {
        if ($.axZm_psh.displayInAzOpt && ($.axZm_psh.displayInTab || $.axZm_psh.displayInSelector)) {
            if ($.axZm_psh.initParam.azOptions360) {
                $.axZm_psh.initParam.azOptions360 = $.extend(true, {}, $.axZm_psh.initParam.azOptions360, $.axZm_psh.displayInAzOpt);
            } else {
                $.axZm_psh.initParam.azOptions360 = $.extend(true, {}, $.axZm_psh.displayInAzOpt);
            }
        }

        $.axZm_psh.changeLayout();
        $.axZm_psh.redefineRefreshFunc();
        $.axZm_psh.placeIntoTab();
        $.axZm_psh.placeIntoSelector();
        $.axZm_psh.bindEvents();
        $.axZm_psh.applyHeaderFix();
    };

})(jQuery);
