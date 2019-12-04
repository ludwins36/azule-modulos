/*
* 2007-2016 PrestaShop
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
* @author Snegurka <site@web-esse.ru>
* @copyright 2007-2019 PrestaShop SA
* @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/

$(function () {
	$('.showPopup').fancybox({
		width: '600px'
    });
	
	$('.form_agree').submit(function () {
    	if (!$('[name=agree]').is(':checked'))
        {
            alert('Please read the Terms of Service and confirm your agreement');
            return false;
        }
    });
    
    $(document).on('keyup change', '#price, #tax_rule', function(){
        var fieldset = $('.customer_form_add');
        var expression = "price + (price / 100 * tax_rule)";
        var fields = [{"name":"price","lang":false,"type":"price"},{"name":"tax_rule","lang":false,"type":"int"}];
        var format = "price";
        $.each(fields, function (index, field) {
            var value = '';
            var founded_field = null;
            if (field.lang)
                founded_field = fieldset.find('[name="'+field.name+'['+id_lang+']"]');
            else
                founded_field = fieldset.find('[name="'+field.name+'"]');

            if (founded_field.is('select'))
            {
                if (field.name == 'tax_rule')
                {
                    var rate = founded_field.find('option:selected').text().match(/.*\(([0-9]+)\%\)/);
                    value = 0;
                    if (rate && rate[1] != 'undefined')
                        value = rate[1];
                }
                else
                    value = founded_field.val();
            }
            else if(founded_field.is('input[type=radio], input[type=checkbox]'))
                value = founded_field.find(':checked').val();
            else
                value = founded_field.val();
            if (field.type == 'price')
                value = parseFloat((value ? value : 0));
            else if(field.type == 'int')
                value = parseInt((value ? value : 0));
            expression = expression.split(field.name).join(value);
        });
        //console.log(expression);
        var return_value  = eval(expression);
        /*
        if (format == 'price')
            return_value = formatCurrency(parseFloat(return_value), currencyFormat, currencySign, currencyBlank)
        */
        $('.calc_field_final_price').html(return_value);
    })
$('.form-wrapper').find('[name="price"], [name="tax_rule"]').trigger('keyup');
    
	$('input[name="type_product"]').on('click', function(e) {
				var product_type = $(this).val();
				if (product_type == 2) {
					$("#is_virtual_file_product").removeClass("hidden");
					$('a[id*="VirtualProduct"]').show();
					$('#is_virtual').val(1);
				} else {
					$('#is_virtual').val(0);
					$('div.is_virtual_good').hide();
					$("#is_virtual_file_product").addClass("hidden");	
				}
	});

});
