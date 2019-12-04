/**
 * HarDevel LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://hardevel.com/License.txt
 *
 * @category  HarDevel
 * @package   HarDevel_multicurrency
 * @author    HarDevel
 * @copyright Copyright (c) 2012 - 2015 HarDevel LLC. (http://hardevel.com)
 * @license   http://hardevel.com/License.txt
 */

$.fn.quickChange = function(handler) {
    return this.each(function() {
        var self = this;
        self.qcindex = self.selectedIndex;
        var interval;
        function handleChange() {
            if (self.selectedIndex != self.qcindex) {
                self.qcindex = self.selectedIndex;
                handler.apply(self);
            }
        }
        $(self).focus(function() {
            interval = setInterval(handleChange, 100);
        }).blur(function() { window.clearInterval(interval); })
            .change(handleChange); //also wire the change event in case the interval technique isn't supported (chrome on android)
    });
};

$(function(){
    $('#hardevel_currency_filter select').quickChange(filter);
    $('#hardevel_currency_filter input[type=checkbox]').change(filter);
    $('#update_prices').click(function(){
        update_prices($('#price_change').val());
    })
    $('#update_discounts').click(function(){
        update_discounts($('#discount_change').val(),$('#discount_type').val(),$('#discount_currency').val());
    })
    $('#hardevel_currency_filter').submit(function(e){
        filter();
       return false;
    });
    $('#discount_type').change(function(){
        if($(this).val() == 'percentage')
            $('#amount_currency').hide();
        else
            $('#amount_currency').show();
    })
    $('#hardevel_products').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: '../modules/hardevelmulticurrency/ajax.php',
            type: 'post',
            dataType: 'json',
            data: $('#products').serialize()+'&save=true&'+$('#hardevel_secure').val(),
            success: function(result){
                if(result.error){
                    alert(result.error);
                }else{
                    alert(result.ok)
                }
            }
        })
    });
});
function update_prices(expression)
{
    try{
        eval('parseFloat(10)' +expression);
    }catch(err){
        alert('Wrong expression');
        return; 
    }
        
    $.each($('td.retail.price input'),function(){
        var $elem = $(this);
        $elem.val(eval('parseFloat(' + $elem.val() + ')' +expression));
    });
    
}
function update_discounts(reduction, reduction_type, reduction_currency)
{
    $('td.price.reduction input').val(reduction);
    $('td.reduction_type select').val(reduction_type);    
    $('td.reduction_type select').val(reduction_type);
    
    $('td.reduction_currency select').val(reduction_currency);
    if(reduction_type == 'percentage')
       $('td.reduction_currency select').hide();
    else
       $('td.reduction_currency select').show();       
}
function bind_reduction_type_change()
{
    $('td.reduction_type select').change(function(){
        $elem = $(this);
        var reduction_currency_select = $elem.parent().next().find('select');
        if($(this).val() == 'percentage')
            reduction_currency_select.hide();
        else
            reduction_currency_select.show();
            
    })
}
function filter()
{
    $('#hardevel_products').hide();
    $.ajax({
        url: '../modules/hardevelmulticurrency/ajax.php',
        type: 'post',
        dataType: 'json',
        data: $('#hardevel_currency_filter').serialize() + '&filter=true&'+$('#hardevel_secure').val(),
        success: function(result){
            var currency_option = ''
            for(var k=0; k<result.currencies.length; k++){
                currency_option += '<option value="'+result.currencies[k]['id_currency']+'" selected="selected">'+result.currencies[k]['name']+'</option>';
            }
            
            $('#discount_currency').html(currency_option);        
            if(result.error){
                $('div.error').show();
                return;
            }
            $('.hardevel_currency div.error').hide();
            $('#hardevel_products').show();
            var html='';
            for(var i = 0; i < result.products.length; i++){
                var product = result.products[i];
                html += '<tr>';
                if(product['id_product_attribute']==0){
                    html += '    <td>' + product['id_product'] + '</td>';
                    html += '    <td class="img"><img src="' + product['image'] + '"/></td>';
                    html += '    <td>' + product['name'] + '</td>';
                    html += '    <td>' + product['reference'] + '</td>';
                    html += '    <td class="price"><input name="products['+product['id_product']+'][0][wholesale_price]" value="'+product['wholesale_price']+'"/></td>';
                    html += '    <td><select name="products['+product['id_product']+'][0][id_wholesale_currency]">';
                    for(var k=0; k<result.currencies.length; k++){
                        html +=          '<option value="'+result.currencies[k]['id_currency']+'" '+(result.currencies[k]['id_currency']==product['id_wholesale_currency'] || (product['id_wholesale_currency']==0 && result.currencies[k]['id_currency']==result.default_currency) ? ' selected="selected"' : '')+'>'+result.currencies[k]['name']+'</option>'
                    }
                    html += '    </select></td>';
                    
                    html += '    <td class="price retail"><input name="products['+product['id_product']+'][0][price]" value="'+product['price']+'"/></td>';
                    html += '    <td><select name="products['+product['id_product']+'][0][id_currency]">';
                    for(var k=0; k<result.currencies.length; k++){
                        html +=          '<option value="'+result.currencies[k]['id_currency']+'" '+(result.currencies[k]['id_currency']==product['id_currency'] || (product['id_currency']==0 && result.currencies[k]['id_currency']==result.default_currency) ? ' selected="selected"' : '')+'>'+result.currencies[k]['name']+'</option>'
                    }
                    html += '    </select></td>';
                    html += '    <td class="price reduction"><input name="products['+product['id_product']+'][0][reduction]" value="'+product['reduction']+'"/></td>';
                    html += '    <td class="reduction_type"><select name="products['+product['id_product']+'][0][reduction_type]">';
                    html +=          '<option value="percentage" '+(product['reduction_type'] == 'percentage'  ? ' selected="selected"' : '')+'>percentage</option>';
                    html +=          '<option value="amount" '+(product['reduction_type'] == 'amount'  ? ' selected="selected"' : '')+'>amount</option>';
                    html += '    </select></td>';
                    
                    html += '    <td class="currency reduction_currency"><select '+(product['reduction_type'] != 'amount' ? 'style="display: none;"' : '')+ 'name="products['+product['id_product']+'][0][id_reduction_currency]">';
                    for(var k=0; k<result.currencies.length; k++){
                        html +=          '<option value="'+result.currencies[k]['id_currency']+'" '+(result.currencies[k]['id_currency']==product['id_reduction_currency'] || (product['id_reduction_currency']==0 && result.currencies[k]['id_currency']==result.default_currency) ? ' selected="selected"' : '')+'>'+result.currencies[k]['name']+'</option>'
                    }
                    html += '    </select></td>';
                    
                }else{
                    html += '    <td colspan="2"></td>';
                    html += '    <td class="combination">' + product['name'] + '</td>';
                    html += '    <td class="combination">' + product['reference'] + '</td>';
                    html += '    <td class="combination price">+&nbsp;<input name="products['+product['id_product']+']['+product['id_product_attribute']+'][wholesale_price]" value="'+product['wholesale_price']+'"/></td>';
                    html += '    <td></td>';
                    html += '    <td class="combination price">+&nbsp;<input name="products['+product['id_product']+']['+product['id_product_attribute']+'][price]" value="'+product['price']+'"/></td>';
                    html += '    <td></td>';
                    html += '    <td></td>';
                    html += '    <td></td>';
                    html += '    <td></td>';
                    
                    
                }


                html += '</tr>';
            }
            $('#hardevel_products table tbody').html(html);
            bind_reduction_type_change();

        }
    });
}