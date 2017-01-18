function Cart(){
	
	this.full_mode = $('#cart-content').length > 0;
	
	this.total_sum = 0;
	this.total_sum_no_discount = 0;
	this.total_weight = 0;
	this.items_num = 0;
	this.delivery_obj = null;
	
	this.init();
	
}

Cart.prototype = {
	
	init: function(){
		
		var oThis = this;
		
		if(this.full_mode){
			
			// change quantity
		   
		   $('#cart-items .quantity input').on('quantityChanged', function(){
			   
			   var product_id = $(this).attr('name').slice(10, -1);
			   oThis.changeQuantity(product_id, $(this).val());
			   
		   });
			
			$('#cart-items a[data-action="delete"]').click(function(){
				
				if(true || confirm('Удалить товар?')){
					
					var product_id = $(this).data('id');
					oThis.deleteItem(product_id);
					
				}
				
				return false;
				
			});
		
		}
		
		$('input[name="use_bonus"]').on('quantityChanged', function(){

			oDelivery.calculateSumPlusDelivery()
				
		});
		
		$('body').on('click', '[data-role="add-to-cart"]', function(evt){
			
			var product_id = $(this).data('product-id');
			var variant_id = $(this).data('variant-id');
			var quantity = $(this).data('quantity');
			
			if($(this).data('check-params')){
				
				$.get(
					'/catalog/',
					{'a': 'lens_params', 'product_id': product_id},
					function(rsp){
						
						if(rsp){
							
							oThis.message('<div class="popup-ajax-content">' + rsp + '</div>');
							$('.popup-ajax-content .quantity').count();
							$('.popup-ajax-content .tabs').iTabs();
							
						}else{
							cart.addItem(product_id, quantity, variant_id);
						}
						
					}
				);
				
			}else{
				
				cart.addItem(product_id, quantity, variant_id);
				
			}			
			
			evt.preventDefault();
			
		});
		
	},
	
	sendRequest: function(params, query){
		
		var oThis = this;
		
		$.getJSON(
			'/cart/?' + query,
			params,
			function(response){
				
				oThis.update(response);
				
				if(params.action && params.action == 'add_item'){
					
					var popup_cnt = '<div class="custom_alert cart_added">';
					popup_cnt += '<div class="ttl">Товар добавлен в корзину!</div>';
					popup_cnt += '<div class="buttons">';
					popup_cnt += '<a href="/cart/" class="btn">Оформить заказ</a>';
					popup_cnt += '<a href="#" class="btn" onclick="$.magnificPopup.close(); return false;">Продолжить покупки</a>'
					popup_cnt += '</div>';
					
					oThis.message(popup_cnt);
					
				}	
				
			}
		);
		
	},
	
	update: function(data){
		
		this.total_sum = data.sum;
		this.total_sum_no_discount = data.sum_no_discount;
		this.total_num = parseInt(data.items_num);
		this.total_weight = data.weight;
		
		
		if(this.total_num > 0){
			$('#cart-mini-text').html(data.short_text);
			$('#cart-mini-count').text(this.total_num).show();
			$('#cart-mini-popup').removeClass('hidden');
		}else{
			$('#cart-mini-text').html('');
			$('#cart-mini-count').text(0);//.hide();
			$('#cart-mini-popup').addClass('hidden');
		}
		
		if(this.full_mode){
			
			$('#totalSum').animateNumber(parseFloat(this.total_sum).toFixed(2));
			$('#totalNum').text(this.total_num);
			
		}
		
	},
	
	addItem: function(product_id, quantity, variant){
		
		var variants_form = $('#add-to-cart-variants-' + product_id);
			
		if(variants_form.length){			
			
			var variants = variants_form.serialize();
			var request = {action: 'add_item', id: product_id};
				
			this.sendRequest(request, variants);
				
		}else{

			if(!quantity){
				var quantity_field = $('input[name="product-quantity-' + product_id + '_' + variant + '"]');
				var quantity = quantity_field.length ? quantity_field.val() : 1;
			}
			
			this.sendRequest({
				'action': 'add_item', 
				'id': product_id, 
				'num': quantity, 
				'item_variant': variant
			});
			
		}		
		
	},
	
	changeQuantity: function(product_id, quantity){
		
		quantity = parseInt(quantity);
		
		var item = this.getItem(product_id);
		if(item){			
			var sum = parseFloat(item.price*quantity).toFixed(2);
			$('#sum_' + product_id).animateNumber(sum);			
		}
		
		this.sendRequest({
			'action': 'change_items_num', 
			'num': quantity, 
			'item': product_id
		});
		
	},
	
	deleteItem: function(product_id){
		
		if($('#cart-item-' + product_id).length){
			$('#cart-item-' + product_id).remove();
			if($('#cart-items').find('.cart__item').length == 0)
				$('#cart-content').html('Корзина пуста');
		}
		
		this.sendRequest({
			'action': 'delete_item',
			'item': product_id,
		});
		
	},
	
	message: function(content){
		
		$.magnificPopup.open({
			items: {
				src: content,
				type: 'inline'
			}
		});
		
	},
	
	getItem: function(id){
		
		var data = false;
		var row = $('#cart-item-' + id);
		
		if($(row).length){
			
			data = {
				'price': parseFloat($('#price_' + id).text().replace(/\s/g, '').replace(',', '.')),
				'quantity': parseInt($('.quantity input', row).val())
			};
			
		}
		
		return data;
		
	}
	
}

var cart = null;

$(document).ready(function(){
	
	cart = new Cart();
	
});