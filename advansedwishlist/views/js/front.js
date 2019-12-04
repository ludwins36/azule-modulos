/*
* 2007-2015 PrestaShop
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
* @author PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2019 PrestaShop SA
* @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/
//global variables
var wishlistProductsIds = [];

var ajaxWhish = {
		overrideButtonsInThePage : function(){
			/* For not logged users */
			$(".allert_note").hide();
			
			$(document).on('mouseenter', '.wish_link, .wrap_allert', function(){
				$(this).find(".allert_note").show();
			});
			
			$(document).on('mouseleave', '.wish_link, .wrap_allert', function(){
				$(this).find(".allert_note").hide();
			});
			
			/* Product List Button popup call*/
			$(document).off('click', '.open_wishlist_popup').on('click', '.open_wishlist_popup', function(e){
				e.preventDefault();
				var idProduct =  parseInt($(this).data('id-product'));
				var idProductAttribute =  parseInt($(this).data('id-product-attribute'));
				
				$("#wish_p").val(idProduct);
				$("#wish_pat").val(idProductAttribute);
				
				$.fancybox.open([
									{
										autoScale: true,
										minHeight: 30,
										href : '#wishlist_popup_form'
									}
								]);
			});	
			
			/* Product List Button in Popup*/
			$(document).off('click', '.popup_button_wishlist').on('click', '.popup_button_wishlist', function(e){
				e.preventDefault();	
				var idProduct = parseInt($("#wish_p").val());
				var idProductAttribute =  parseInt($("#wish_pat").val());
				var id_wishlist = parseInt($("#wishlist_select_popup").val());
				WishlistCart('ws_wishlist_block_list', 'add', idProduct, idProductAttribute, 1, id_wishlist);
			});
		},
		wishlistBuyProduct : function(id_product, id_product_attribute, id_quantity, button, static_token)
		{
			if (ps_ws_version == 'advansedwishlistis16') {
				ajaxCart.add(id_product, id_product_attribute, false, button, id_quantity);
			} else {
			    $.post("index.php?controller=cart&update=1&id_product="+id_product+"&id_product_attribute="+id_product_attribute+"&qty="+id_quantity+"&token="+static_token+"&op=up" , function() {
			        // prestashop.emit('updateCart');
			        prestashop.emit('updateCart', {
			            reason: 'update'
			        });
			    });
			    $(".ws_added_alert").removeClass("hidden");
			}
		    var id_wishlist = parseInt($("#table_wishlist").data("id-wishlist"));

		    //WishlistProductManage('wlp_bought', 'delete', id_wishlist, id_product, id_product_attribute, id_quantity);
		},
		displayNewProducts : function(idProduct, idCombination, data) {
			var product = jQuery.parseJSON(data);
			
			//create a container for listing the products and hide the 'no product in the cart' message (only if the cart was empty)
			if ($('#ws_wishlist_block_list dl.products').length == 0)
			{
				$('.wishlist_block_no_products').before('<dl class="products"></dl>');
				$('.wishlist_block_no_products').hide();
			}
			
			var content =  '<dt id="ws_blockwishlist_product_'+idProduct+'" class="wl_block_product">'; 
			content += '<div class="wl_block_product_info">';
			content += '<a class="cart_block_product_name" href="'+product.ws_link+'">'+product.name+'</a>';
			content += '</div>';
			content += '<a class="ajax_cart_block_remove_link" href="javascript:;" onclick="javascript:WishlistCart(\'ws_wishlist_block_list\', \'delete\', '+ idProduct +', '+ idCombination +', 0);" title="" rel="nofollow">';
			if (product.ws_version == 'advansedwishlistis17') {
				content += '<i class="material-icons">delete_forever</i>';
			} else {
				content += '<i class="icon icon-remove"></i>';
			}
			content += '</a></dt>';
			$('#ws_wishlist_block_list dl.products').append(content);
		},
		displayNewProductsFull : function(idProduct, idCombination, data) {
			var product = jQuery.parseJSON(data);
			
			//create a container for listing the products and hide the 'no product in the cart' message (only if the cart was empty)
			if ($('#ws_wishlist_block_list dl.products').length == 0)
			{
				$('.wishlist_block_no_products').before('<dl class="products"></dl>');
				$('.wishlist_block_no_products').hide();
			}
			
			var content =  '<dt id="ws_blockwishlist_product_'+idProduct+'" class="wl_block_product">'; 
			content += '<div class="wl_block_product_info">';
			content += '<a href="'+product.ws_link+'" title="'+product.name+'">';
			content += '<img src="'+product.ws_img_link+'" alt="'+product.name+'" />';
			content += '</a>';
			content += '<div class="wl_product_info">';
			content += '<a class="cart_block_product_name" href="'+product.ws_link+'">'+product.name+'</a>';
			content += '<span class="ws_price">'+product.ws_price+'</span>';
			content += '</div>';
			content += '<input type="text" class="wl_cart_product_qty" data-id-product-attribute="'+ idCombination +'" data-id-product="'+idProduct+'" id="quantity_'+idProduct+'_'+ idCombination +'" value="1"  />';
			content += '<a class="btn btn-primary exclusive wishlist_add_to_cart" href="'+product.ws_cart_link+'" rel="nofollow" title="Add to cart">';
			content += '<span>Add to cart</span>';
			content += '</a>';
			content += '</div>';
			content += '<a class="ajax_cart_block_remove_link" href="javascript:;" onclick="javascript:WishlistCart(\'ws_wishlist_block_list\', \'delete\', '+ idProduct +', '+ idCombination +', 0);" title="" rel="nofollow">';
			if (product.ws_version == 'advansedwishlistis17') {
				content += '<i class="material-icons">delete_forever</i>';
			} else {
				content += '<i class="icon icon-remove"></i>';
			}
			content += '</a></dt>';
			$('#ws_wishlist_block_list dl.products').append(content);
		},
		hideOldProducts : function(idProduct, idCombination) {
			$('#ws_blockwishlist_product_'+idProduct).remove();
		},
		count_wish : function(){
			var wishlist_count = $(".wishlist_count").html();
			wishlist_count = parseInt(wishlist_count) + 1;
			$(".wishlist_count").html(wishlist_count);
			$("#login_wish").find(".allert_note").slideDown().delay('1300').slideUp();
		}
}

$(document).ready(function(){
	ajaxWhish.overrideButtonsInThePage();
	
	wishlistRefreshStatus();

	$(document).on('change', 'select[name=wishlists]', function(){
		WishlistChangeDefault('ws_wishlist_block_list', $(this).val());
	});
	
	$(document).on('change', '.wishlist_group_actions', function(){
			var id_wishlist = $("#table_wishlist").data("id-wishlist");
			var g_action = $(this).val();
			 $('.wishlist_group_chb:checked').each(function(){
				 var idProduct = parseInt($(this).data("id-product"));
				 var idProductAttribute = parseInt($(this).data("id-pa"));
				 var available = $(this).data("available");
				 var tt_qty = parseInt($('#quantity_'+idProduct+'_'+idProductAttribute).val());
				 var tt_prior = $('#priority_'+idProduct+'_'+idProductAttribute).val();
				 if (g_action == '1') {
				 WishlistProductManage('wlp_bought', 'delete', id_wishlist, idProduct, idProductAttribute, tt_qty, tt_prior);
				 }
				 if (g_action == '2' && available == 'a') {
					 var this_btn = $("#wishlist_add_to_cart_"+idProduct);
					 ajaxWhish.wishlistBuyProduct(idProduct, idProductAttribute, tt_qty, this_btn, static_token);
				 }
			});
			$(this).val('0');
	});
	
	if (single_mode && ($("#mywishlist").length > 0)) {
		WishlistManage('block-order-detail', idDefaultWishlist);
	}
	
	// Cart Page Button - add to wishlist
	
    $(document).on('click', '.add_to_ws_wishlist', function (e) {
            var id_product = parseInt($(this).attr('data-id-product'));
            var id_product_attribute = parseInt($(this).attr('data-id-product-attribute'));
            
            WishlistCart('advansedwishlist_cart_block', 'add', id_product, id_product_attribute, 1, idDefaultWishlist);
            
            if (ps_ws_version == 'advansedwishlistis17') {
            	WlRemoveProductFromCart(id_product, id_product_attribute);
            } else {
                var ids = $(this).attr('data-ids');
                deleteProductFromSummary(ids);
            }
    });
	
	// Def controller - add to cart from wishlist
    
	$(document).off('click', '.wishlist_add_to_cart').on('click', '.wishlist_add_to_cart', function(e){
		e.preventDefault();
		var idProduct =  parseInt($(this).data('id-product'));
		var idProductAttribute =  parseInt($(this).data('id-product-attribute'));
		var idCombination = 0;
		var quantity = parseInt($("#quantity_"+idProduct+"_"+idProductAttribute).val());

		ajaxWhish.wishlistBuyProduct(idProduct, idProductAttribute, quantity, this, static_token);
			

		/*
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: url_cart,
			async: true,
			cache: false,
			dataType : "json",
			data: 'action=add-to-cart&id_product=' + idProduct + ( (parseInt(idCombination) && idCombination != null) ? '&id_product_attribute=' + parseInt(idCombination): ''),
			success: function(jsonData,textStatus,jqXHR)
			{
				
			}
		});	
		*/

	});
	
	$(document).off('change', '.wl_cart_product_qty').on('change', '.wl_cart_product_qty', function(e){
		e.preventDefault();
		var idProduct =  parseInt($(this).data('id-product'));
		var idProductAttribute =  parseInt($(this).data('id-product-attribute'));
		var idCombination = 0;
		var quantity = parseInt($(this).val());
		var id_wishlist = parseInt($("#advansedwishlist_cart_block").data('id-wishlist'));

		$.ajax({
			type: 'GET',
			async: true,
			url: advansedwishlist_ajax_controller_url + '?rand=' + new Date().getTime(),
			headers: { "cache-control": "no-cache" },
			data: 'action=updateQtyFromCart&id_wishlist=' + id_wishlist + '&id_product=' + idProduct + '&id_product_attribute=' + idProductAttribute + '&quantity=' + quantity,
			cache: false,
			success: function(data)
			{
				nb_products = 0;
				$("[class^='wl_cart_product_qty']").each(function(index, element){
					nb_products += parseInt(element.value);
				});
				$(".wishlist_count").html(nb_products);
			}
		});
	});	


	$('.wishlist').each(function() {
		current = $(this);
		$(this).children('.wishlist_button_list').popover({
			html: true,
			content: function () {
				return current.children('.popover-content').html();
			}
		});
	});
});

function WlRemoveProductFromCart(id_product, id_product_attribute) {
    $.post("index.php?controller=cart&delete=1&id_product="+id_product+"&id_product_attribute="+id_product_attribute+"&token="+static_token+"" , function() {
        // prestashop.emit('updateCart');
        prestashop.emit('updateCart', {
            reason: 'update'
        });
    });
}

function WishlistCart(id, action, id_product, id_product_attribute, qty, id_wishlist)
{
	var t_quantity = 1
	if ($('#quantity_wanted').length > 0) {
		t_quantity = $('#quantity_wanted').val();		
	}

	if (!id_product_attribute) {
		id_product_attribute = 0;
	}
	
	$.ajax({
		type: 'GET',
		url: advansedwishlist_ajax_controller_url,
		headers: { "cache-control": "no-cache" },
		async: true,
		cache: false,
		data: 'action=' + action + '&id_product=' + id_product + '&quantity=' + t_quantity + '&token=' + static_token + '&id_product_attribute=' + id_product_attribute + '&id_wishlist=' + id_wishlist,
		success: function(data)
		{
			if (action == 'add')
			{
				if (isLogged == true) {
					wishlistProductsIdsAdd(id_product);
					wishlistRefreshStatus();
					
					if (!!$.prototype.fancybox)
						$.fancybox.open([
							{
								type: 'inline',
								autoScale: true,
								minHeight: 30,
								content: '<p class="fancybox-error">'+added_to_wishlist+'</p>'
							}
						], {
							padding: 0
						});
					else
						alert(added_to_wishlist);
					/*
					// MODE already added
					 
					$("#wishlist_button_block").html('<a href="#" id="wishlist_button" class="checked" onclick="return false;">'+wishlist_btn_icon+added_to_wishlist_btn+'</a>');
					if (id == 'ws_wishlist_block_list') {
						$(".wishlistProd_"+id_product).parent().html('<a href="#" class="checked" onclick="return false;">'+wishlist_btn_icon+added_to_wishlist_btn+'</a>');
					}
					*/
					$("#wishlist_button_block").html('<a href="#" id="wishlist_button" class="checked addToWishlist wishlistProd_'+id_product+'" rel="'+id_product+'" onclick="WishlistCart(\'ws_wishlist_block_list\', \'delete\', \''+id_product+'\', \''+id_product_attribute+'\', 1, \''+id_wishlist+'\'); return false;">'+wishlist_btn_icon+added_to_wishlist_btn+'</a>');
					if (id == 'ws_wishlist_block_list') {
						$(".wishlistProd_"+id_product).parent().html('<a href="#" class="checked addToWishlist wishlistProd_'+id_product+'" rel="'+id_product+'" onclick="WishlistCart(\'ws_wishlist_block_list\', \'delete\', \''+id_product+'\', \''+id_product_attribute+'\', 1, \''+id_wishlist+'\'); return false;"">'+wishlist_btn_icon+added_to_wishlist_btn+'</a>');
					}
					if (id_wishlist == idDefaultWishlist) {
						ajaxWhish.count_wish();
					}

				}
				else
				{
					if (!!$.prototype.fancybox)
						$.fancybox.open([
							{
								type: 'inline',
								autoScale: true,
								minHeight: 30,
								content: '<p class="fancybox-error">' + loggin_required + '</p>'
							}
						], {
							padding: 0
						});
					else
						alert(loggin_required);
				}
			}
			if (action == 'delete') {
				wishlistProductsIdsRemove(id_product);
				wishlistRefreshStatus();
				
				$("#wishlist_button_block").html('<a href="#" id="wishlist_button" class="addToWishlist wishlistProd_'+id_product+'" rel="'+id_product+'" onclick="WishlistCart(\'ws_wishlist_block_list\', \'add\', \''+id_product+'\', \''+id_product_attribute+'\', 1, \''+id_wishlist+'\'); return false;">'+wishlist_btn_icon+add_to_wishlist_btn+'</a>');
				if (id == 'ws_wishlist_block_list') {
					$(".wishlistProd_"+id_product).parent().html('<a href="#" class="addToWishlist wishlistProd_'+id_product+'" rel="'+id_product+'" onclick="WishlistCart(\'ws_wishlist_block_list\', \'add\', \''+id_product+'\', \''+id_product_attribute+'\', 1, \''+id_wishlist+'\'); return false;"">'+wishlist_btn_icon+add_to_wishlist_btn+'</a>');
				}
				
				if (id_wishlist == idDefaultWishlist) {
	            var old_count = parseInt($(".wishlist_count").html())-1;
				$(".wishlist_count").html(old_count);
				}
			}
			if(($('#' + id).length != 0) && (id_wishlist == idDefaultWishlist))
			{
				$('#' + id).slideUp('normal');
				if (action == 'add') {
					if (id == 'advansedwishlist_cart_block') {
						ajaxWhish.displayNewProductsFull(id_product, id_product_attribute, data);
					} else {
						ajaxWhish.displayNewProducts(id_product, id_product_attribute, data);
					}
				}
				if (action == 'delete') {
					ajaxWhish.hideOldProducts(id_product, id_product_attribute);
				}

				//document.getElementById(id).innerHTML = data;
				$('#' + id).slideDown('normal');
			}
		}
	});
}

/**
* Change customer default wishlist
*
* @return void
*/
function WishlistChangeDefault(id, id_wishlist)
{
	$.ajax({
		type: 'GET',
		url: baseDir + 'modules/advansedwishlist/cart.php?rand=' + new Date().getTime(),
		headers: { "cache-control": "no-cache" },
		async: true,
		data: 'id_wishlist=' + id_wishlist + '&token=' + static_token,
		cache: false,
		success: function(data)
		{
			$('#' + id).slideUp('normal');
			document.getElementById(id).innerHTML = data;
			$('#' + id).slideDown('normal');
		}
	});
}

/**
* Buy Product
*
* @return void
*/


function WishlistAddProductCart(token, id_product, id_product_attribute, id_quantity)
{
	if ($('#' + id_quantity).val() <= 0)
		return (false);

	$.ajax({
			type: 'GET',
			url: baseDir + 'modules/advansedwishlist/buywishlistproduct.php?rand=' + new Date().getTime(),
			headers: { "cache-control": "no-cache" },
			data: 'token=' + token + '&static_token=' + static_token + '&id_product=' + id_product + '&id_product_attribute=' + id_product_attribute,
			async: true,
			cache: false,
			success: function(data)
			{
				if (data)
				{
					if (!!$.prototype.fancybox)
						$.fancybox.open([
							{
								type: 'inline',
								autoScale: true,
								minHeight: 30,
								content: '<p class="fancybox-error">' + data + '</p>'
							}
						], {
							padding: 0
						});
					else
						alert(data);
				}
				else
					$('#' + id_quantity).val($('#' + id_quantity).val() - 1);
			}
	});

	return (true);
}

/**
* Show wishlist managment page
*
* @return void
*/
function WishlistManage(id, id_wishlist)
{
	$.ajax({
		type: 'GET',
		async: true,
		url: advansedwishlist_controller_url + '?rand=' + new Date().getTime(),
		headers: { "cache-control": "no-cache" },
		data: 'id_wishlist=' + id_wishlist + '&refresh=' + false,
		cache: false,
		success: function(data)
		{
			$('#' + id).hide();
			document.getElementById(id).innerHTML = data;
			$('#' + id).fadeIn('slow');

			$('.wishlist_change_button').each(function(index) {
				$(this).change(function () {
					wishlistProductChange($('option:selected', this).attr('data-id-product'), $('option:selected', this).attr('data-id-product-attribute'), $('option:selected', this).attr('data-id-old-wishlist'), $('option:selected', this).attr('data-id-new-wishlist'));
	 		 	});
			});
		}
	});
}

/**
* Show wishlist product managment page
*
* @return void
*/
function WishlistProductManage(id, action, id_wishlist, id_product, id_product_attribute, quantity, priority)
{
	$.ajax({
		type: 'GET',
		async: true,
		url: advansedwishlist_controller_url + '?rand=' + new Date().getTime(),
		headers: { "cache-control": "no-cache" },
		data: 'action=' + action + '&id_wishlist=' + id_wishlist + '&id_product=' + id_product + '&id_product_attribute=' + id_product_attribute + '&quantity=' + quantity + '&priority=' + priority + '&refresh=' + true,
		cache: false,
		success: function(data)
		{
			if (action == 'delete') {
				$('#wlp_' + id_product + '_' + id_product_attribute).fadeOut('fast');

				var wishlist_count = $(".wishlist_count").html();
				wishlist_count = wishlist_count - quantity;
				$(".wishlist_count").html(wishlist_count);
				$("#wishlist_"+id_wishlist).children('td').eq(1).html(wishlist_count);
			} else if (action == 'update') {
				$('#wlp_' + id_product + '_' + id_product_attribute).fadeOut('fast');
				$('#wlp_' + id_product + '_' + id_product_attribute).fadeIn('fast');
				nb_products = 0;
				$("[id^='quantity']").each(function(index, element){
					nb_products += parseInt(element.value);
				});
				$("#wishlist_"+id_wishlist).children('td').eq(1).html(nb_products);
				$(".wishlist_count").html(nb_products);
			}
		}
	});
}

/**
* Delete wishlist
*
* @return boolean succeed
*/
function WishlistDelete(id, id_wishlist, msg)
{
	var res = confirm(msg);
	if (res == false)
		return (false);

	if (typeof mywishlist_url == 'undefined')
		return (false);

	$.ajax({
		type: 'GET',
		async: true,
		dataType: "json",
		url: mywishlist_url,
		headers: { "cache-control": "no-cache" },
		cache: false,
		data: {
			rand: new Date().getTime(),
			deleted: 1,
			myajax: 1,
			id_wishlist: id_wishlist,
			action: 'deletelist'
		},
		success: function(data)
		{
			var mywishlist_siblings_count = $('#' + id).siblings().length;
			$('#' + id).fadeOut('slow').remove();
			$("#block-order-detail").html('');
			if (mywishlist_siblings_count == 0)
				$("#block-history").remove();

			if (data.id_default)
			{
				var td_default = $("#wishlist_"+data.id_default+" > .wishlist_default");
				$("#wishlist_"+data.id_default+" > .wishlist_default > a").remove();
				td_default.append('<p class="is_wish_list_default"><i class="icon icon-check-square"></i></p>');
			}
		}
	});
}

function WishlistDefault(id, id_wishlist)
{
	if (typeof mywishlist_url == 'undefined')
		return (false);

	$.ajax({
		type: 'GET',
		async: true,
		url: mywishlist_url,
		headers: { "cache-control": "no-cache" },
		cache: false,
		data: {
			rand:new Date().getTime(),
			'default': 1,
			id_wishlist:id_wishlist,
			myajax: 1,
			action: 'setdefault'
		},
		success: function (data)
		{
			var old_default_id = $(".is_wish_list_default").parents("tr").attr("id");
			var td_check = $(".is_wish_list_default").parent();
			if (ps_ws_version == 'advansedwishlistis17') {
				var iconSquare = '<i class="material-icons">check_box_outline_blank</i>';
				var iconCheckSquare = '<i class="material-icons">assignment_turned_in</i>';
			} else {
				var iconSquare = '<i class="icon icon-square"></i>';
				var iconCheckSquare = '<i class="icon icon-check-square"></i>';
			}
			$(".is_wish_list_default").remove();
			td_check.append('<a href="#" onclick="javascript:event.preventDefault();(WishlistDefault(\''+old_default_id+'\', \''+old_default_id.replace("wishlist_", "")+'\'));">'+iconSquare+'</a>');
			var td_default = $("#"+id+" > .wishlist_default");
			$("#"+id+" > .wishlist_default > a").remove();
			td_default.append('<p class="is_wish_list_default">'+iconCheckSquare+'</p>');
		}
	});
}

/**
* Hide/Show bought product
*
* @return void
*/
function WishlistVisibility(bought_class, id_button)
{
	if ($('#hide' + id_button).is(':hidden'))
	{
		$('.' + bought_class).slideDown('fast');
		$('#show' + id_button).hide();
		$('#hide' + id_button).css('display', 'block');
	}
	else
	{
		$('.' + bought_class).slideUp('fast');
		$('#hide' + id_button).hide();
		$('#show' + id_button).css('display', 'block');
	}
}

/**
* Send wishlist by email
*
* @return void
*/
function WishlistSend(id, id_wishlist, id_email)
{
	$.post(
			advansedwishlist_ajax_controller_url,
		{
			token: static_token,
			action: 'sendWL',
			id_wishlist: id_wishlist,
			email1: $('#' + id_email + '1').val(),
			email2: $('#' + id_email + '2').val(),
			email3: $('#' + id_email + '3').val(),
			email4: $('#' + id_email + '4').val(),
			email5: $('#' + id_email + '5').val(),
		},
		function(data)
		{
			if (data)
			{
				if (!!$.prototype.fancybox)
					$.fancybox.open([
						{
							type: 'inline',
							autoScale: true,
							minHeight: 30,
							content: '<p class="fancybox-error">' + data + '</p>'
						}
					], {
						padding: 0
					});
				else
					alert(data);
			}
			else
				WishlistVisibility(id, 'hideSendWishlist');
		}
	);
}

function wishlistProductsIdsAdd(id)
{
	if ($.inArray(parseInt(id),wishlistProductsIds) == -1)
		wishlistProductsIds.push(parseInt(id))
}

function wishlistProductsIdsRemove(id)
{
	wishlistProductsIds.splice($.inArray(parseInt(id),wishlistProductsIds), 1)
}

function wishlistRefreshStatus()
{
	$('.addToWishlist').each(function(){
		if ($.inArray(parseInt($(this).prop('rel')),wishlistProductsIds)!= -1)
			$(this).addClass('checked');
		else
			$(this).removeClass('checked');
	});
}

function wishlistProductChange(id_product, id_product_attribute, id_old_wishlist, id_new_wishlist)
{
	if (typeof mywishlist_url == 'undefined')
		return (false);

	var quantity = $('#quantity_' + id_product + '_' + id_product_attribute).val();

	$.ajax({
		type: 'GET',
		url: mywishlist_url,
		headers: { "cache-control": "no-cache" },
		async: true,
		cache: false,
		dataType: "json",
		data: {
			id_product:id_product,
			id_product_attribute:id_product_attribute,
			quantity: quantity,
			priority: $('#priority_' + id_product + '_' + id_product_attribute).val(),
			id_old_wishlist:id_old_wishlist,
			id_new_wishlist:id_new_wishlist,
			myajax: 1,
			action: 'productchangewishlist'
		},
		success: function (data)
		{
			console.log(data);
			console.log(data.msg);
			if (data.success == true) {
				$('#wlp_' + id_product + '_' + id_product_attribute).fadeOut('slow');
				$('#wishlist_' + id_old_wishlist + ' td:nth-child(2)').text($('#wishlist_' + id_old_wishlist + ' td:nth-child(2)').text() - quantity);
				$('#wishlist_' + id_new_wishlist + ' td:nth-child(2)').text(+$('#wishlist_' + id_new_wishlist + ' td:nth-child(2)').text() + +quantity);
			}
			else
			{
				if (!!$.prototype.fancybox)
					$.fancybox.open([
						{
							type: 'inline',
							autoScale: true,
							minHeight: 30,
							content: '<p class="fancybox-error">' + data.error + '</p>'
						}
					], {
						padding: 0
					});
			}
		}
	});
}
