var geo = {
	
	init: function(){
		
		$('body').on('click', '.b-choose-region__list a', function(evt){
			
			geo.setRegion($(this).text());
			geo.saveRegion();
			
			evt.preventDefault();
			
		});
		
		$('.b-choose-region__search input').autocomplete({
			minLength: 2,
			source: function( request, response ) {
				$.ajax({
					url: '/engine/components/kladr/main.php?action=get_cities&with_region=1&q=' + $(this.element).val(),
					dataType: 'json',
					async: false,
					success: function(data) {
						response( $.map( data, function( item ) {
							return {
								label: item.label.replace(/^.*?\.\s/, ''),
								val: item.value
							}
						}));
					}
				});
			},
			select: function( event, ui ) {
				geo.setRegion(ui.item.label, ui.item.val);
				$('.b-choose-region__save').show();
			}
		});

		$('body').on('click', '.b-choose-region__save', function(){
			geo.saveRegion();
			return false;
		});
		
	},
	
	setRegion: function(value, code){
		
		var city = value;
		var region = '';
			
		var i_of = city.indexOf(', ');
		if(i_of > -1){
			region = city.substring(i_of+2);
			city = city.substring(0, i_of);				
		}

		$('[data-role="city-name"]').text(city);
		$('[data-role="region-name"]').text(region);
		$('input[name="city_code"]').val(code ? code : '');

		this.value = value;
		this.code = code;
		this.city = city;
		this.region = region;
		
	},
	
	saveRegion: function(){

		// wait a bit
		$('.b-choose-region__save').attr('disabled', true).text('Подождите...');

		var oThis = this;

		$.get(
			'/index', 
			{'change_region': this.code ? this.code : this.city}, 
			function(rsp){
			
				$('[data-role="city-name"]').text(oThis.city);
				
				$.magnificPopup.close();
				
				var sel_group = $('select[name="delivery[group]"]');
				if(sel_group.length){
					
					if(oThis.city == 'Санкт-Петербург' || oThis.region == 'Ленинградская обл' || oThis.region == 'Санкт-Петербург'){
						sel_group.val(2549).change();
					}else{
						sel_group.val(2550).change();
					}
					
				}

				// phone
				/*
				if(oThis.city == 'Санкт-Петербург'){
					$('.phone.rus').hide();
					$('.phone.spb').show();
				}else{
					$('.phone.rus').show().text(rsp.phone ? rsp.phone : $('.phone.rus').data('base-phone'));
					$('.phone.spb').hide();
				}
				*/

				var product_id = $('body').data('product-id');
				
				var delivery_sidebar = $('#delivery-info-sidebar');

				if(delivery_sidebar.length){
					new Loader(delivery_sidebar);
					$.get(
						'/delivery-and-payment/types/',
						{'a': 'prices', 'tpl': 'sidebar', 'pid': (product_id ? product_id : 0)},
						function(rsp){
							delivery_sidebar.html(rsp);
						}
					);				
				}
				
				var delivery_product = $('#delivery-info-product');
				
				if(delivery_product.length){
					new Loader(delivery_product);
					$.get(
						'/delivery-and-payment/types/',
						{'a': 'prices', 'tpl': 'in_product', 'pid': (product_id ? product_id : 0)},
						function(rsp){
							delivery_product.html(rsp);
						}
					);
				}
				
				$(window).trigger('cityChanged');
				
				if($('body').data('reload-on-region-change')){
					location.reload();
				}
				
			},
			'json'
		);
	}
}