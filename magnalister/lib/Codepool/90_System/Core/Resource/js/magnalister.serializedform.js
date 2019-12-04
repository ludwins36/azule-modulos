/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
var mlSerializer = null;
(function ($) {
    mlSerializer = {
        submitSerializedForm: function (form, aExtraData) {
            if (typeof tinymce === 'object') {
                // copy values from tinymce iframe to real textarea before serializion
                $('.magna td textarea.tinymce').each(function() {
                    var tinymceVar = tinymce.get($(this).attr('id'));
                    if(typeof tinymceVar !== "undefined"){
                        $(this).val(tinymceVar.getContent());
                    }
                });
            }

            for (var value in aExtraData) {
                if (aExtraData.hasOwnProperty(value)) {
                    form.append('<input type="hidden" name="' + value + '" value="' + aExtraData[value] + '">');
                }
            }
            var sFormData = form.serialize();
            var sForm = '<form action="' + form.attr('action') + '" method="' + form.attr('method') + '" style="display:none">';
            var newFormData = {'ml[FullSerializedForm]': sFormData};
            $.extend(newFormData, $('[data-mlNeededFormFields]').data('mlneededformfields'));
            for (var newFormValue in newFormData) {
                sForm += '<input type="hidden" name="' + newFormValue + '" value="' + newFormData[newFormValue] + '">';
            }
            sForm += '</form>';
            $(sForm).appendTo("div.magna").submit().remove();
        },
        prepareSerializedDataForAjax: function (aData) {
            //serialize array to one string to prevent to exceed max_input_vars
            var sData = $.param(aData);
            aData = aData.filter(function(e){
                if (e.name === 'undefined') {
                    return true;
                } else if (e.name.indexOf('ml') !== -1) {
                    return false;
                }

                return true;
            });
            aData.push({"name":'ml[FullSerializedForm]', "value":sData});
            return aData;
        }
    }
})(jqml);
