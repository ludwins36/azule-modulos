(function($) {
    $.widget("ui.ml_variation_matching", {
        options: {
            urlPostfix: '&kind=ajax',
            i18n: {},
            elements: {}
        },
        i18n: {
            defineName: 'defineName',
            ajaxError: 'ajaxError',
            selectVariantGroup: 'selectVariantGroup',
            pleaseSelect: 'pleaseSelect',
            allSelect: 'allSelect',
            autoMatching: 'autoMatching',
            resetMatching: 'resetMatching',
            manualMatching: 'manualMatching',
            matchingTable: 'matchingTable',
            resetInfo: 'resetInfo',
            shopValue: 'shopValue',
            mpValue: 'mpValue',
            webShopAttribute: 'webShopAttribute',
            chooseCategoryButton: 'chooseCategoryButton',
            beforeAttributeChange: 'beforeAttributeChange',
            buttonOk: 'OK',
            buttonCancel: 'Cancel',
            note: 'Hinweis',
            attributeChangedOnMp: '',
            attributeDifferentOnProduct: '',
            attributeDeletedOnMp: '',
            attributeValueDeletedOnMp: '',
            alreadyMatched: 'alreadyMatched',
        },
        html: {
            shopVariationsDropDown: '',
            valuesBackup: ''
        },
        elements: {
            form: null,
            mainSelectElement: null,
            newGroupIdentifier: null, //hidden input for transport
            matchingHeadline: null,
            matchingInput: null,
            categoryInfo: null
        },
        variationValues: {},

        _init: function() {
            var self = this,
                i;

            for (i in self.options.i18n) {
                if (self.options.i18n.hasOwnProperty(i)) {
                    if (typeof self.i18n[i] !== 'undefined') {
                        self.i18n[i] = $("<div/>").html(self.options.i18n[i]).html();
                    }
                }
            }

            for (i in self.options.elements) {
                if (self.options.elements.hasOwnProperty(i)) {
                    if (typeof self.elements[i] !== 'undefined') {
                        if (typeof self.options.elements[i] === 'string') {
                            self.elements[i] = self.element.find(self.options.elements[i]);
                        } else {
                            self.elements[i] = self.options.elements[i];
                        }
                    }
                }
            }

            if (self.elements.form === null) {
                self.elements.form = self.element.find('form').andSelf().filter('form');
            }

            self.html.valuesBackup = self.elements.matchingInput.html();
            self._initMainSelectElement();

            $('body')
                .on('click', 'button.ml-save-matching', function(){
                    self._saveMatching(this.value);
                })
                .on('click', 'button.ml-delete-row', function(){
                    self._deleteRow(this);
                })
                .on('click', 'button.ml-delete-matching', function(){
                    var button = this;
                    var d = self.i18n.resetInfo;
                    $('<div class="ml-modal dialog2" title="' + self.i18n.note + '"></div>').html(d).jDialog({
                        width: (d.length > 1000) ? '700px' : '500px',
                        buttons: {
                            Cancel: {
                                'text': self.i18n.buttonCancel,
                                click: function() {
                                    $(this).dialog('close');
                                }
                            },
                            Ok: {
                                'text': self.i18n.buttonOk,
                                click: function() {
                                    var selectId = button.value;
                                    $('select#' + selectId).val('');
                                    self._saveMatching(true);
                                    $(this).dialog('close');
                                }
                            }
                        }
                    })
                })
                .on('click', 'button.ml-collapse', function(){
                    var matchedTable = $('div#match_' + this.value);
                    if (matchedTable.css('display') == 'none') {
                        $('span.ml-collapse[name="'+ this.value + '_collapse_span_name"]').css('background-position', '0 -23px');
                        matchedTable.show();
                    } else {
                        $('span.ml-collapse[name="'+ this.value + '_collapse_span_name"]').css('background-position', '0 0px');
                        matchedTable.hide();
                    }
                });
        },

        _initMainSelectElement: function() {
            var self = this;

            self.elements.mainSelectElement.change(function() {
                self.elements.matchingHeadline.css('display', 'none');
                self.elements.matchingInput.html(self.html.valuesBackup).css('display', 'none');
                self.elements.categoryInfo.css('display', 'none');

                var val = $(this).val();
                if (val != null && val !== '' && val != 'null') {
                    self._loadMPVariation(val);
                    self.elements.matchingHeadline.css('display', 'table-row-group');
                    self.elements.matchingInput.css('display', 'table-row-group');
                    self.elements.categoryInfo.css('display', 'block');
                }
            }).trigger('change');
        },

        _render: function(template, data) {
            var out = '',
                current;
            for (var i in data) {
                if (data.hasOwnProperty(i)) {
                    current = template.replace(new RegExp('\{%count%\}', 'g'), i);
                    for (var ii in data[i]) {
                        if (data[i].hasOwnProperty(ii)) {
                            if (
                                typeof data[i][ii] !== 'undefined'
                                && typeof data[i][ii] !== 'object'
                            ) {
                                current = current.replace(new RegExp('\{' + ii + '\}', 'g'), data[i][ii]);
                            }
                        }
                    }
                    out += current;
                }
            }
            return out;
        },

        _getShopVariationsDropDownElement: function() {
            var self = this;

            if (self.html.shopVariationsDropDown === '') {
                self.html.shopVariationsDropDown =
                    '<select class="shopAttrSelector">'
                    + self._render('<option {Disabled} data-custom="{Custom}" value="{Code}">{Name}</option>', $.extend(
                            {0: {Code: 'null', Name: self.i18n.pleaseSelect, Disabled: '', Custom: ''}},
                            self.options.shopVariations
                        )
                    )
                    + '</select>'
                ;
            }

            return $(self.html.shopVariationsDropDown);
        },

        _load: function(data, success) {
            var self = this;

            // some systems (Magento) have additional params that must be appended to each request
            // if so, object window.ml_config.postParams should have them all
            var additionalParams = window.ml_config ? window.ml_config.postParams : null;
            if (additionalParams) {
                for (var key in additionalParams) {
                    if (additionalParams.hasOwnProperty(key) && !data.hasOwnProperty(key)) {
                        data[key] = additionalParams[key];
                    }
                }
            }

            $.blockUI(blockUILoading);
            $.ajax({
                type: 'POST',
                url: self.elements.form.attr('action') + self.options.urlPostfix,
                dataType: 'json',
                data: data,
                success: function() {
                    $.unblockUI();
                    success.apply(this, arguments);
                },
                error: function() {
                    alert(self.options.i18n.ajaxError);
                    $.unblockUI();
                    self._resetMPVariation();
                }
            });
        },

        _resetMPVariation: function() {
            this.variationValues = {};
        },

        _renderOptions: function (options, selectedValue, firstOption, addSeparator) {
            var self = this,
                data = firstOption ? [firstOption] : [],
                i, key;
            if (addSeparator) {
                data.push({
                    key: 'separator_line',
                    value: '------------------------------------------------------------------',
                    disabled: 'disabled',
                    selected: ''
                });
            }

            for (i in options) {
                if (options.hasOwnProperty(i)) {
                    key = options[i].key ? options[i].key : i;
                    data.push({
                        key: key,
                        value: options[i].value ? options[i].value : options[i],
                        selected: key === selectedValue ? 'selected' : '',
                        disabled: ''
                    });
                }
            }

            return self._render('<option {selected} {disabled} value="{key}">{value}</option>', data);
        },

        _buildSelectMatching: function (elem, selector, matchDiv, attributeListDiv) {
            var self = this;

            var addAfterWarning = false;
            var spanWarning = $('span#' + selector.AttributeCode + '_warningMatching');
            if (typeof spanWarning.html() !== 'undefined') {
                addAfterWarning = true;
            }

            if (elem.val() === 'freetext') {
                var freetext = '';
                if (elem.val() === selector.CurrentValues.Code) {
                    freetext = selector.CurrentValues.Values;
                    attributeListDiv.attr('style', 'background-color: #e9e9e9');
                    if (addAfterWarning) {
                        spanWarning.before(
                            '<span id="' + selector.AttributeCode + '_deleteMatching" style="padding-left: 10px">' +
                            '<button type="button" class="mlbtn action ml-delete-matching" value="' + elem.attr('id') + '">-</button>' +
                            '<span style="padding-left: 10px">' + self.i18n.alreadyMatched + '</span>' +
                            '</span>'
                        );
                    } else {
                        $('div#extraFieldsInfo_' + selector.AttributeCode).append(
                            '<span id="' + selector.AttributeCode + '_deleteMatching" style="padding-left: 10px">' +
                            '<button type="button" class="mlbtn action ml-delete-matching" value="' + elem.attr('id') + '">-</button>' +
                            '<span style="padding-left: 10px">' + self.i18n.alreadyMatched + '</span>' +
                            '</span>'
                        );
                    }
                } else {
                    $('div#extraFieldsInfo_' + selector.AttributeCode).hide();
                }

                matchDiv.css('display', 'inline-block').css('width', '40%');
                return matchDiv.append('<input type="text" style="width:100%" name="ml[match][ShopVariation][' + selector.AttributeCode + '][Values]" value="' + freetext + '">');
            }

            if (elem.val() === 'attribute_value') {
                var attr_value = selector.CurrentValues.Values;
                attributeListDiv.attr('style', 'background-color: #e9e9e9');
                if (addAfterWarning) {
                    spanWarning.before(
                        '<span id="' + selector.AttributeCode + '_deleteMatching" style="padding-left: 10px">' +
                        '<button type="button" class="mlbtn action ml-delete-matching" value="' + elem.attr('id') + '">-</button>' +
                        '<span style="padding-left: 10px">' + self.i18n.alreadyMatched + '</span>' +
                        '</span>'
                    );
                } else {
                    $('div#extraFieldsInfo_' + selector.AttributeCode).append(
                        '<span id="' + selector.AttributeCode + '_deleteMatching" style="padding-left: 10px">' +
                        '<button type="button" id="selector.CurrentValues.Code" class="mlbtn action ml-delete-matching"value="' + elem.attr('id') + '">-</button>' +
                        '<span style="padding-left: 10px">' + self.i18n.alreadyMatched + '</span>' +
                        '</span>'
                    );
                }

                matchDiv.css('display', 'inline-block').css('width', '40%');
                var style = selector.CurrentValues.Error ? ' style="border-color:red;"' : '';

                if ((typeof selector.CurrentValues.Code !== 'undefined') && (elem.val() !== selector.CurrentValues.Code)) {
                    $('div#extraFieldsInfo_' + selector.AttributeCode).hide();
                }

                return matchDiv.append(
                    '<select' + style + ' name="ml[match][ShopVariation][' + selector.AttributeCode + '][Values]">'
                    + self._renderOptions(selector.AllowedValues, attr_value, { key: '', value: self.i18n.pleaseSelect, selected: '', disabled: ''}, false)
                    + '</select>');
            }

            return '';
        },

        _getMatchingTableTemplate: function(attributeCode) {
            return '<tr>' +
            '   <td style="width: 35%">' +
            '       <input type="hidden" name="ml[match][ShopVariation][' + attributeCode + '][Values][{key}][Shop][Key]" value="{valueShopKey}">' +
            '       <input type="hidden" name="ml[match][ShopVariation][' + attributeCode + '][Values][{key}][Shop][Value]" value="{valueShopValue}">' +
            '       {valueShopValue}' +
            '   </td>' +
            '   <td style="width: 35%">' +
            '       <select id="ml_matched_value_' + attributeCode + '_{valueShopKey}" style="width: 100%">' +
            '           <option {disabled} value="freetext">' + this.i18n.manualMatching + '</option>' +
            '           <option selected="selected" value="{valueMarketplaceKey}">{valueMarketplaceInfo}</option>' +
            '       </select>' +
            '       <input type="hidden" name="ml[match][ShopVariation][' + attributeCode + '][Values][{key}][Marketplace][Key]" value="{valueMarketplaceKey}">' +
            '       <input type="hidden" name="ml[match][ShopVariation][' + attributeCode + '][Values][{key}][Marketplace][Value]" value="{valueMarketplaceValue}">' +
            '       <input type="hidden" name="ml[match][ShopVariation][' + attributeCode + '][Values][{key}][Marketplace][Info]" value="{valueMarketplaceInfo}">' +
            '   </td>' +
            '   <td id="ml_freetext_value_' + attributeCode + '_{valueShopKey}" style="border: none; display: none;">' +
            '       <input type="hidden" disabled="disabled" id="ml_key_' + attributeCode + '_{valueShopKey}" name="ml[match][ShopVariation][' + attributeCode + '][Values][{key}][Marketplace][Key]" value="manual">' +
            '       <input type="text" id="ml_value_' + attributeCode + '_{valueShopKey}" style="width:100%;">' +
            '   </td>' +
            '   <td style="border: none">' +
            '       <button type="button" class="mlbtn action ml-save-matching" value="' + attributeCode + '" style="display: none;">+</button>' +
            '       <button type="button" class="mlbtn action ml-delete-row" value="' + attributeCode + '">-</button>'
            '   </td>' +
            '</tr>' +
            '<script>' +
            '   jqml("#matched_value_' + attributeCode + '_{valueShopKey}").change();' +
            '   jqml("#value_' + attributeCode + '_{valueShopKey}").change();' +
            '</script>';
        },

        _getMatchingAttributeColumnTemplate: function() {
            return '	<tr id="selRow_{id}">'
                + '         <th {style}>{AttributeName} {redDot}</th>'
                + '         <td id="selCell_{id}">'
                + '             <div id="attributeList_{id}">'
                + '                 <label>' + this.i18n.webShopAttribute + ':</label>'
                + '                 {shopVariationsDropDown}'
                + '             </div>'
                + '             <div id="match_{id}"></div>'
                + '         </td>'
                + '         <td class="info">{AttributeDescription}</td>'
                + '	</tr>';
        },

        _getDeletedAttributeColumnTemplate: function() {
            return '<tr style="color:red;">'
                + '     <th>{AttributeName}</th>'
                + '     <td>'
                + '         <label>' + this.i18n.attributeDeletedOnMp + '</label>'
                + '     </td>'
                + '     <td></td>'
                + '	</tr>';
        },

        _getDeletedAttributeValueColumnTemplate: function() {
            return '<tr style="color:red;">'
                + '     <td style="width: 35%">{AttributeName}</td>'
                + '     <td style="width: 35%">' + this.i18n.attributeValueDeletedOnMp + '</td>'
                + '     <td colspan="2" style="border: none">'
                + '         <button type="button" class="mlbtn action ml-delete-row" value="{Code}">-</button>'
                + '     </td>'
                + '	</tr>';
        },

        _buildMPShopMatching: function(elem, selector, savePrepare) {
            var self = this,
                values = self.options.shopVariations[elem.val()],
                matchDiv = $('div#match_' + selector.id),
                attributeListDiv = $('div#attributeList_' + selector.id),
                mpValues = selector.AllowedValues,
                style = '';

            var addAfterWarning = false;
            var spanWarning = $('span#' + selector.AttributeCode + '_warningMatching');
            if (typeof spanWarning.html() !== 'undefined') {
                addAfterWarning = true;
            }

            matchDiv.html('');
            if (typeof values === 'undefined') {
                return;
            }

            attributeListDiv.removeAttr('style');
            matchDiv.removeAttr('style');

            if (elem.find(":selected").attr('data-custom') == "true") {
                attributeListDiv.attr('style', 'background-color: #e9e9e9');
                if (addAfterWarning) {
                    spanWarning.before(
                        '<span id="' + selector.AttributeCode + '_deleteMatching" style="padding-left: 10px">' +
                        '<button type="button" class="mlbtn action ml-delete-matching" value="' + elem.attr('id') + '">-</button>' +
                        '<span style="padding-left: 10px">' + self.i18n.alreadyMatched + '</span>' +
                        '</span>'
                    );
                } else {
                    $('div#extraFieldsInfo_' + selector.AttributeCode).append(
                        '<span id="' + selector.AttributeCode + '_deleteMatching" style="padding-left: 10px">' +
                        '<button type="button" class="mlbtn action ml-delete-matching" value="' + elem.attr('id') + '">-</button>' +
                        '<span style="padding-left: 10px">' + self.i18n.alreadyMatched + '</span>' +
                        '</span>'
                    );
                }

                if ((typeof selector.CurrentValues.Code !== 'undefined') && (elem.val() !== selector.CurrentValues.Code)) {
                    $('div#extraFieldsInfo_' + selector.AttributeCode).hide();
                }

                return matchDiv.append('<input type="hidden" name="ml[match][ShopVariation][' + selector.AttributeCode + '][Values]" value="true">');
            }

            if (elem.val() === 'freetext' || elem.val() === 'attribute_value') {
                return self._buildSelectMatching(elem, selector, matchDiv, attributeListDiv);
            }

            matchDiv.css('margin-top', '10px');


            if (selector.AllowedValues.length === 0) {
                // if AllowedValues is empty, it indicates there are no required attribute values from marketplace
                // for this, we use shop's values but do not use keys for attributes because we send to marketplace
                // keys, which in case of shop should be values because marketplace cannot recognize those keys
                for (var k in values.Values) {
                    if (values.Values.hasOwnProperty(k)) {
                        mpValues[values.Values[k]] = values.Values[k];
                    }
                }
            }

            matchDiv.append(self._buildMatchingTableSelectors(selector.AttributeCode, values.Values, mpValues, selector.CurrentValues.Error))
                    .append(self._buildMatchingTableBody(selector, elem, savePrepare));

            self._changeTriggerVariationMarketplace(selector.AttributeCode);
            self._orderSelectOptions(selector.AttributeCode, true);

            return matchDiv;
        },

        _buildMatchingTableSelectors: function(attributeCode, shopValues, mpValues, error) {
            var self = this,
                baseName = 'ml[match][ShopVariation][' + attributeCode + '][Values]',
                style = error ? 'style="border-color:red"' : '';

            var out = '<table class="attrTable matchingTable">'
                + '    <tbody>'
                + '        <tr class="headline">'
                + '            <td class="key" style="width: 35%">' + self.i18n.shopValue + '</td>'
                + '            <td class="input" style="width: 35%">' + self.i18n.mpValue + '</td>'
                + '        </tr>'
                + '    </tbody>'
                + '    <tbody>'
                + '        <tr>'
                + '            <td class="key" style="width: 35%">'
                + '                <select ' + style + ' name="' + baseName + '[0][Shop][Key]">'
                +                      self._renderOptions(shopValues, '', null, true)
                + '                </select>'
                + '                <input type="hidden" name="' + baseName + '[0][Shop][Value]">'
                + '            </td>'
                + '            <td class="input" style="width: 35%">'
                + '                <select ' + style + ' name="' + baseName + '[0][Marketplace][Key]">'
                +                      self._renderOptions(mpValues, '', null, true)
                + '                </select>'
                + '                <input type="hidden" name="' + baseName + '[0][Marketplace][Value]">'
                + '            </td>'
                + '            <td id="freetext_' + attributeCode + '" style="border: none; display: none;">'
                + '                <input type="text" name="' + baseName + '[FreeText]" style="width:100%;">'
                + '            </td>'
                + '            <td style="border: none"><button type="button" class="mlbtn action ml-save-matching" value="' + attributeCode + '">+</button></td>'
                + '        <tr>'
                + '    </tbody>'
                + '</table>';
            return $(out);
        },

        _buildMatchingTableBody: function (selector, elem, savePrepare) {
            var self = this;

            if (typeof selector.CurrentValues.Values !== 'undefined'
                && (selector.CurrentValues.Values.length > 0 || Object.keys(selector.CurrentValues.Values).length > 0)
                && elem.val() === selector.CurrentValues.Code)
            {
                // reload saved values
                var tableBody = '',
                    i = 1,
                    disableFreeTextOption = selector.AllowedValues.length !== 0 ? 'disabled' : '';

                var addAfterWarning = false;
                var spanWarning = $('span#' + selector.AttributeCode + '_warningMatching');
                if (typeof spanWarning.html() !== 'undefined') {
                    addAfterWarning = true;
                }

                for (var code in selector.CurrentValues.Values) {
                    if (selector.CurrentValues.Values.hasOwnProperty(code)) {
                        var entry = selector.CurrentValues.Values[code];
                        // if there are not predefined values or current value is in predefined values, render regularly
                        // otherwise, attribute value has been deleted from marketplace
                        if (selector.AllowedValues.length === 0 || selector.AllowedValues[entry.Marketplace.Key] !== undefined) {
                            tableBody += self._render(self._getMatchingTableTemplate(selector.AttributeCode), [{
                                key: i++,
                                valueShopKey: entry.Shop.Key,
                                valueShopValue: entry.Shop.Value,
                                valueMarketplaceKey: entry.Marketplace.Key,
                                valueMarketplaceValue: entry.Marketplace.Value,
                                valueMarketplaceInfo: entry.Marketplace.Info,
                                disabled: disableFreeTextOption
                            }]);
                        } else {
                            tableBody += self._render(self._getDeletedAttributeValueColumnTemplate(), [{
                                AttributeName: entry.Shop.Value
                            }]);
                        }
                    }
                }

                $('div#attributeList_' + selector.id).attr('style', 'background-color: #e9e9e9');
                if (addAfterWarning) {
                    spanWarning.before(
                        '<span id="' + selector.AttributeCode + '_deleteMatching" style="padding-left: 10px">' +
                        '<button type="button" class="mlbtn action ml-delete-matching" value="' + elem.attr('id') + '">-</button>' +
                        '<span style="padding-left: 10px">' + self.i18n.alreadyMatched + '</span>' +
                        '</span>'
                    );
                } else {
                    $('div#extraFieldsInfo_' + selector.AttributeCode).append(
                        '<span id="' + selector.AttributeCode + '_deleteMatching" style="padding-left: 10px">' +
                        '<button type="button" class="mlbtn action ml-delete-matching" value="' + elem.attr('id') + '">-</button>' +
                        '<span style="padding-left: 10px">' + self.i18n.alreadyMatched + '</span>' +
                        '</span>'
                    );
                }

                $('div#extraFieldsInfo_' + selector.AttributeCode).append(
                    '<span id="' + selector.AttributeCode + '_collapseMatching" style="float: right">' +
                    '<button type="button" class="ml-collapse" value="' + selector.AttributeCode + '" name="' + selector.AttributeCode + '_collapse_button_name">' +
                    '<span class="ml-collapse" name="'+ selector.AttributeCode + '_collapse_span_name"></span>' +
                    '</button>' +
                    '</span>'
                );

                if (savePrepare === selector.AttributeCode) {
                    $('span.ml-collapse[name="'+ selector.AttributeCode + '_collapse_span_name"]').css('background-position', '0 -23px');
                    $('div#match_' + selector.AttributeCode).show();
                } else {
                    $('span.ml-collapse[name="'+ selector.AttributeCode + '_collapse_span_name"]').css('background-position', '0 0px');
                    $('div#match_' + selector.AttributeCode).hide();
                }

                if ((typeof selector.CurrentValues.Code !== 'undefined') && (elem.val() !== selector.CurrentValues.Code)) {
                    $('div#extraFieldsInfo_' + selector.AttributeCode).hide();
                }

                return $(
                    '<span id="spanMatchingTable" style="padding-right:2em;">' +
                    '   <div style="font-weight: bold; background-color: #e9e9e9">' + self.i18n.matchingTable + '</div>' +
                    '   <table id="' + selector.AttributeCode + '_button_matched_table" style="width:100%; background-color: #e9e9e9">' +
                    '       <tbody>' +
                                tableBody +
                    '       </tbody>' +
                    '   </table>' +
                    '</span>');
            }

            return '';
        },

        _changeTriggerVariationMarketplace: function (attributeCode) {
            var self = this,
                shopKeySelector = '[name="ml[match][ShopVariation][' + attributeCode + '][Values][0][Shop][Key]"]',
                shopValueSelector = '[name="ml[match][ShopVariation][' + attributeCode + '][Values][0][Shop][Value]"]',
                mpKeySelector = '[name="ml[match][ShopVariation][' + attributeCode + '][Values][0][Marketplace][Key]"]',
                mpValueSelector = '[name="ml[match][ShopVariation][' + attributeCode + '][Values][0][Marketplace][Value]"]';

            $(shopKeySelector).change(function() {
                $(shopValueSelector).val($(shopKeySelector + ' option:selected').html());
            }).trigger('change');

            $(mpKeySelector).change(function() {
                $(mpValueSelector).val($(mpKeySelector + ' option:selected').html());

                var oldValue = $(mpKeySelector).defaultValue;
                if ($(this).val() === 'reset') {
                    var d = self.i18n.resetInfo;
                    $('<div class="ml-modal dialog2" title="' + self.i18n.note + '"></div>').html(d).jDialog({
                        width: (d.length > 1000) ? '700px' : '500px',
                        buttons: {
                            Cancel: {
                                'text': self.i18n.buttonCancel,
                                click: function() {
                                    $(mpKeySelector).val(oldValue);
                                    $(this).dialog('close');
                                }
                            },
                            Ok: {
                                'text': self.i18n.buttonOk,
                                click: function() {
                                    self._saveMatching(true);
                                    $(this).dialog('close');
                                }
                            }
                        }
                    });
                }

                if ($(this).val() === 'manual') {
                    $('td #freetext_' + attributeCode).show();
                } else {
                    $('td #freetext_' + attributeCode).hide();
                }
            }).trigger('change');
        },

        _buildShopVariationSelector: function (data) {
            var self = this,
                kind = 'FreeText',
                baseName = 'ml[match][ShopVariation][' + data.AttributeCode + ']';

            data.id = data.AttributeCode.replace(/[^A-Za-z0-9_]/g, '_'); // css selector-save.
            data.AttributeName = data.AttributeName || data.AttributeCode;
            data.AttributeDescription = data.AttributeDescription || ' ';
            self.variationValues[data.AttributeCode] = data.AllowedValues;
            var variationsDropDown =
                self._getShopVariationsDropDownElement()
                    .attr('id', 'sel_' + data.id)
                    .attr('name', baseName + '[Code]');

            if (data.CurrentValues.Error == true) {
                variationsDropDown.attr('style', 'border-color:red');
                data.style = 'style="color:red"';
            } else {
                data.style = '';
            }

            if (data.AllowedValues.length > 0 || Object.keys(data.AllowedValues).length > 0) {
                kind = 'Matching';
                variationsDropDown.children("option[data-custom='true']").attr('disabled', 'disabled');
                variationsDropDown.children("option[value='freetext']").attr('disabled', 'disabled');
                variationsDropDown.children("option[value='attribute_value']").attr('disabled', null);
            } else {
                if (data.AttributeCode.substring(0, 20) === 'additional_attribute') {
                    variationsDropDown.children("option[value='freetext']").attr('disabled', 'disabled');
                } else {
                    variationsDropDown.children("option[value='freetext']").attr('disabled', null);
                }

                variationsDropDown.children("option[value='attribute_value']").attr('disabled', 'disabled');
                variationsDropDown.children("option[data-custom='true']").attr('disabled', null);
            }

            if (data.Required == true) {
                data.redDot = '<span class="bull">&bull;</span>';
            } else {
                data.redDot = '';
            }

            data.shopVariationsDropDown = $('<div>')
                .append(variationsDropDown)
                .append('<div id="extraFieldsInfo_' +  data.AttributeCode + '" style="display: inline;"></div>')
                .append('<input type="hidden" name="' + baseName + '[Kind]" value="' + kind + '">')
                .append('<input type="hidden" name="' + baseName + '[Required]" value="' + (data.Required ? 1 : 0) + '">')
                .append('<input type="hidden" name="' + baseName + '[AttributeName]" value="' + data.AttributeName + '">')
                .html()
            ;

            return data;
        },

        _buildShopVariationSelectors: function(data, savePrepare) {
            var self = this,
                colTemplate = self._getMatchingAttributeColumnTemplate(),
                deletedAttrTemplate = self._getDeletedAttributeColumnTemplate(),
                i,
                attributes = data.Attributes;

            self.elements.matchingInput.html('');

            for (i in attributes) {
                if (attributes.hasOwnProperty(i)) {
                    if (attributes[i].Deleted) {
                        self.elements.matchingInput.append($(self._render(deletedAttrTemplate, [attributes[i]])))
                    } else {
                        attributes[i] = self._buildShopVariationSelector(attributes[i]);
                        self.elements.matchingInput.append($(self._render(colTemplate, [attributes[i]])));

                        // add warning box if attribute changed on Marketplace
                        if (attributes[i].ChangeDate && data.ModificationDate
                            && new Date(data.ModificationDate.substr(0, 10)) < new Date(attributes[i].ChangeDate.substr(0, 10))
                        ) {
                            $('div#extraFieldsInfo_' + attributes[i].id)
                                .append('<span id="' + attributes[i].id + '_warningMatching" class="ml-warning" title="' + self.i18n.attributeChangedOnMp + '">&nbsp;<span>');
                        }

                        // add warning box if attribute is different from one matched in Variation matching tab
                        if (attributes[i].Modified) {
                            $('div#extraFieldsInfo_' + attributes[i].id)
                                .append('<span id="' + attributes[i].id + '_warningMatching" class="ml-warning" title="' + self.i18n.attributeDifferentOnProduct + '">&nbsp;<span>');
                        }
                    }
                }
            }

            data.Attributes = attributes;

            self.elements.matchingInput.append('<tr class="spacer"><td colspan="3">&nbsp;</td></tr>');
            self.elements.matchingInput.find('select[id^=sel_]').each(function() {
                var previous;
                $(this).on('focus', function() {
                    previous = $(this).val();
                }).change(function() {
                    self._handleAttributeSelectorChange(this, data, previous, savePrepare);
                });
            });

            for (i in attributes) {
                if (attributes.hasOwnProperty(i)) {
                    if (typeof attributes[i].CurrentValues.Code !== 'undefined') {
                        self.elements.matchingInput.find('select[id=sel_' + attributes[i].id + ']').val(attributes[i].CurrentValues.Code).trigger('change');
                    }
                }
            }
        },

        _handleAttributeSelectorChange: function (selectElement, data, lastSelection, savePrepare) {
            var self = this,
                attributes = data.Attributes;
            selectElement = $(selectElement);
            for (var i in attributes) {
                if (attributes.hasOwnProperty(i)) {
                    if ('sel_' + attributes[i].id === selectElement.attr('id')) {
                        if ($.trim($('#' + attributes[i].id + '_button_matched_table').html())) {
                            var d = self.i18n.beforeAttributeChange;
                            $('<div class="ml-modal dialog2" title="' + self.i18n.note + '"></div>').html(d).jDialog({
                                width: (d.length > 1000) ? '700px' : '500px',
                                buttons: {
                                    Ok: {
                                        'text': self.i18n.buttonOk,
                                        click: function() {
                                            selectElement.val(lastSelection);
                                            $(this).dialog('close');
                                        }
                                    }
                                }
                            });
                        } else {
                            self._buildMPShopMatching(selectElement, attributes[i], savePrepare);

                            if (typeof attributes[i].CurrentValues.Values == 'undefined' || attributes[i].CurrentValues.Values.constructor === String) {
                                break;
                            }

                            var currentValues = $.map(attributes[i].CurrentValues.Values, function (value) {
                                return [value];
                            });

                            currentValues.forEach(function (entry) {
                                // remove set values but not ones that were deleted on marketplace
                                if (typeof entry.Shop != 'undefined' && !attributes[i].Deleted) {
                                    self._removeAttributeFromDropDown(attributes[i].AttributeCode, entry.Shop.Key);
                                }
                            });
                        }

                        break;
                    }
                }
            }
        },

        _loadMPVariation: function(val) {
            var self = this;

            self._resetMPVariation();
            if (val === 'null') {
                self.elements.matchingInput.html(self.html.valuesBackup);
                return;
            }

            self._load({
                'ml[method]': 'LoadMPVariations',
                'ml[ajax]': true,
                'ml[kind]': 'ajax',
                'ml[SelectValue]': val
            }, function(data) {
                self._buildShopVariationSelectors(data, true);
            });
        },

        _saveMatching: function(savePrepare) {
            var self = this;
            if (!self.saveInProgress) {
                self.saveInProgress = true;
                self._load({
                    'ml[method]': 'SaveMatching',
                    'ml[ajax]': true,
                    'ml[kind]': 'ajax',
                    'ml[Variations]': $(self.elements.form).serialize()
                }, function (data) {
                    self._buildShopVariationSelectors(data, savePrepare);
                    self.saveInProgress = false;
                });
            }
        },

        _deleteRow: function(r) {
            $(r).closest('table')[0].deleteRow(r.parentNode.parentNode.rowIndex);

            this._saveMatching(r.value);
        },

        _removeAttributeFromDropDown: function(code, key) {
            $('select[name="ml[match][ShopVariation][' + code + '][Values][0][Shop][Key]"] option[value="' + key + '"]').hide();
        },

        _orderSelectOptions: function(code, removeFreeText) {
            var self = this,
                shopKeySelector = 'select[name="ml[match][ShopVariation][' + code + '][Values][0][Shop][Key]"]',
                mpKeySelector = 'select[name="ml[match][ShopVariation][' + code + '][Values][0][Marketplace][Key]"]';

            $(shopKeySelector).append($(shopKeySelector + ' option').remove().sort(function(a, b) {
                var at = $(a).text().toLowerCase(), bt = $(b).text().toLowerCase();
                return (at > bt) ? 1 : (at < bt ? -1 : 0);
            }));

            $(shopKeySelector)
                .prepend('<option value="all">' + self.i18n.allSelect + '</option>')
                .prepend('<option selected="selected" value="null">' + self.i18n.pleaseSelect + '</option>');

            $(mpKeySelector).append($(mpKeySelector + ' option').remove().sort(function(a, b) {
                var at = $(a).text().toLowerCase(), bt = $(b).text().toLowerCase();
                return (at > bt)?1:((at < bt)?-1:0);
            }));

            $(mpKeySelector)
                .prepend('<option ' + (removeFreeText ? 'disabled' : '') + ' value="manual">' + self.i18n.manualMatching + '</option>')
                .prepend('<option value="reset">' + self.i18n.resetMatching + '</option>')
                .prepend('<option value="auto">' + self.i18n.autoMatching + '</option>')
                .prepend('<option selected="selected" value="null">' + self.i18n.pleaseSelect + '</option>');
        }
    });

    $(document).ready(function() {
        $('.magnamain').on('change', '[id^=ml_matched_value_]', function() {
            var me = $(this),
                row = me.parent().parent(),
                cell = row.find("td[id^=ml_freetext_value_]");
            if (me.val() === "freetext") {
                cell.show();
                cell.find("input[id^=ml_key_]").removeAttr("disabled");
                row.find('button.ml-save-matching').show();
                row.find('button.ml-delete-row').hide();
            } else {
                cell.hide();
                cell.find("input[id^=ml_key_]").attr("disabled", "disabled");
                row.find('button.ml-save-matching').hide();
                row.find('button.ml-delete-row').show();
           }
        }).on('change', '[id^=ml_value_]', function() {
            $(this).parent().parent().find('[name$="[Marketplace][Value]"]').val($(this).val());
        });

        $('form[name=apply]').ml_variation_matching({
            urlPostfix: '&kind=ajax&where=' + ml_vm_config.viewName,
            i18n: ml_vm_config.i18n,
            elements: {
                newGroupIdentifier: '#newGroupIdentifier',
                mainSelectElement: '#maincat',
                matchingHeadline: '#tbodyDynamicMatchingHeadline',
                matchingInput: '#tbodyDynamicMatchingInput',
                categoryInfo: '#categoryInfo'
            },
            shopVariations: ml_vm_config.shopVariations
        });
    });
})(jQuery);
