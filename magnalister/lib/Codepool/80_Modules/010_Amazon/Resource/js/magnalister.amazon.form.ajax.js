(function($) {
    $(document).ready(function() {
        function fireAjaxRequest(eElement, ajaxAdditional) {
            mlShowLoading();
            var eForm = eElement.parentsUntil('form').parent(),
                aData = $(eForm).serializeArray(),
                aAjaxData = $.parseJSON(eElement.attr('data-ajax')),
                aAjaxController = $.parseJSON(eElement.attr('data-ajax-controller')),
                i;

            for (i in aAjaxData) {
                if (aAjaxData[i]['value'] === null) {
                    aAjaxData[i]['value'] = ajaxAdditional;
                }

                aData.push(aAjaxData[i]);
            }
            aData = mlSerializer.prepareSerializedDataForAjax(aData);
            eElement.hide('slide', {direction: 'right'});
            $.ajax({
                url: eForm.attr("action"),
                type: eForm.attr("method"),
                data: aData,
                complete: function(jqXHR, textStatus) {
                    var eRow;
                    try {// need it for ebay-categories and attributes, cant do with global ajax, yet
                        var oJson = $.parseJSON(data);
                        var content = oJson.content;
                        eElement.html(content);
                    } catch (oExeception) {
                    }

                    eRow = eElement.parentsUntil('.js-field').parent();
                    if (eElement.text() !== '') {
                        eRow.show();
                    } else {
                        eRow.hide();
                    }

                    initAjaxForm(eElement, true);
                    updateExistingAjaxFormTrigger(eElement);
                    mlHideLoading();

                    eElement.show('slide', {direction: 'right'});

                    if (aAjaxController.autoTriggerOnLoad) {
                        $(eElement).find(aAjaxController.autoTriggerOnLoad.selector).trigger(aAjaxController.autoTriggerOnLoad.trigger);
                    }

                    $(".magnalisterForm select.optional").trigger("change");
                }
            });
        }

        function updateExistingAjaxFormTrigger(searcSelectorsOnlyInEl) {
            var eElements = $('.magnalisterForm'),
                els = eElements.find('.magnalisterAjaxForm').andSelf();

            els.each(function() {
                var eElement = $(this),
                    aAjaxController = $.parseJSON(eElement.attr('data-ajax-controller'));

                if (aAjaxController !== null) {
                    $(searcSelectorsOnlyInEl).find(aAjaxController.selector).on(aAjaxController.trigger, function(event) {
                        fireAjaxRequest(eElement, event.ajaxAdditional);
                    });

                    if (eElement.attr('data-ajax-trigger') === 'true') {
                        // only trigger by first load
                        eElement.attr('data-ajax-trigger', 'false');
                        fireAjaxRequest(eElement);
                    }
                }
            });
        }

        function initAjaxForm(eElements, onlyChildren) {
            var els = eElements.find('.magnalisterAjaxForm');
            if (!onlyChildren) {
                els = els.andSelf();
            }

            els.each(function() {
                var eElement = $(this),
                    aAjaxController = $.parseJSON(eElement.attr('data-ajax-controller')),
                    selectors = null;

                if (aAjaxController !== null) {
                    if (eElement.find(aAjaxController.selector).length === 0) {
                        selectors = $(eElements).find(aAjaxController.selector);
                        selectors.on(aAjaxController.trigger, function(event) {
                            fireAjaxRequest(eElement, event.ajaxAdditional);
                        });
                    } else {
                        selectors = $(eElement);
                        selectors.on(aAjaxController.trigger, $('.magnalisterForm').find(aAjaxController.selector), function(event) {
                            fireAjaxRequest(eElement, event.ajaxAdditional);
                        });
                    }

                    if (eElement.attr('data-ajax-trigger') === 'true') {
                        // only trigger by first load
                        eElement.attr('data-ajax-trigger', 'false');
                        fireAjaxRequest(eElement);
                    }
                }
            });
        }

        initAjaxForm($('.magnalisterForm'));
    });
})(jqml);
