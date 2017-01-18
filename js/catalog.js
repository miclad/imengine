$.catalog = {
	
	init: function(){
		
		var self = this;
		
		$('.product-card').on('click', function(evt){
			
			var url = $(this).data('url');
			
			if(url && !$(evt.target).data('content') && !$(evt.target).parent().data('content') && !$(evt.target).data('action') && !$(evt.target).parent().data('action') && $(evt.target).data('role') != 'add-to-cart' && $(evt.target).parent().data('role') != 'add-to-cart'){
				location.href = url;
				evt.preventDefault();
			}
			
		})

		$('#filter-form').find('input[type="checkbox"]').on('click', function(){
			self.filter.sendRequest(this);
		});
		
		$('#filter-form').find('input[type="text"]').typeWatch(
			{
				callback: function(){ self.filter.sendRequest(this); },
				wait: 500,
				highlight: true,
				captureLength: 1
			}
		);

		$('.f-slider').each(function(){
			
			var container = $(this);
			var field_from = container.find('.f-slider__from');
			var field_to = container.find('.f-slider__to');
			
			container.find('.f-slider__range').slider({
				range: true,
				min: $(this).data('min'),
				max: $(this).data('max'),
				step: $(this).data('step'),
				values: [ $(this).data('from'), $(this).data('to') ],
				slide: function( event, ui ){
					
					//field_from.find('span').text(formatNumber(ui.values[0]));
					//field_to.find('span').text(formatNumber(ui.values[1]));
					
					field_from.find('input').val(ui.values[0]);
					field_to.find('input').val(ui.values[1]);
		
				},
				stop: function(event, ui){
					self.filter.sendRequest(container);
				}
			});
			
			$('input', field_from).add('input', field_to).keyup(function(){
				var val = $(this).val().replace(/[^0-9]/g, '');
				if(val == '')
					val = 0;
				if(val !== $(this).val())
					$(this).val(val);
				container.find('.f-slider__range').slider( "values", [ $('input', field_from).val(), $('input', field_to).val() ] );
			});
			
		});

		$('body').on('click', '.b-filter__popup__show', function(){
			$('#filter-form').submit();
			return false;
		});
		
		$('body').on('click', function(evt){
			if(!$(evt.target).parents('.b-filter__group').length && !$(evt.target).parents('.b-filter__popup').length){
				self.filter.hidePopup();
			}
		});

		$("#filter-toggle").on('click', function(){
			var obj = $(this);

			if($(obj).hasClass("s-active")) {
				self.filter.hideFilter();
				obj.removeClass("s-active");
			} else {
				self.filter.showFilter();
				obj.addClass("s-active");
			}

			return false;
		});
		
		$('.catalog_filter_brands select[name="order_by"]').on('change', function(){
			
			var url = location.href;
			if(url.indexOf('?') > -1)
				url += '&';
			else
				url += '?';
			url += 'order_by=' + $(this).val();
			
			location.href = url;
			
		});
		
		$('body').on('quantityChanged', '.lens-params__option .quantity input', function(){
			
			var container = $(this).parents('[data-role="product-buy-container"]');
			var price_container = container.find('[data-role="product-price-value"]');
			var price = price_container.data('price');
			var quantity_fields = container.find('.quantity input');
			
			var quantity = 0;
			quantity_fields.each(function(){
				quantity += parseInt($(this).val());
			});
			
			price_container.animateNumber(price*quantity);
								
		});
		
		$('body').on('tabChanged', '[data-role="lens-params-tabs"]', function(){
			
			var tab = $(this).find('.s-active').prevAll().length;
			
			var lens_params = $(this).parents('[data-role="product-buy-container"]').find('.lens-params');
			var right_eye = lens_params.eq(1);
			var right_eye_quantity = right_eye.find('.quantity input');
			
			if(tab == 1){
				right_eye.show();
				right_eye_quantity.val(1).trigger('quantityChanged');
				$('.lens-params__wrap').addClass('different');
			}else{
				right_eye.hide();
				right_eye_quantity.val(0).trigger('quantityChanged');
				$('.lens-params__wrap').removeClass('different');
			}
			
		});
		
		$('body').on('change', '.lens-params select', function(){
			
			var container = $(this).parents('.lens-params');
			var frm = $(this).parents('form');
			var pid = frm.data('product-id');
			
			var sel = {};
			$('select', container).not($(this)).each(function(){
				sel[$(this).data('param')] = $(this).val();
			});
			
			var lo = new Loader(container);
			
			$.get(
				'/catalog/',
				{
					a: 'lens_params',
					_return: 'json',
					selected: sel,
					product_id: pid,
					initial_param: $(this).data('param'),
					initial_value: $(this).val()
				},
				function(rsp){
					
					for(var k in rsp){
						
						lo.remove();
						
						var opt = container.find('select[data-param="' + k + '"]');
						if(opt.length){
							fillSelect(opt, rsp[k], {value: 'value', text: 'name'});
						}
						
					}

				},
				'json'
			)
			
		});
		
		$('.product__photos').photoSwitcher();
		
		$('body').on('click', '[data-action="addFavorite"]', function(evt){
			
			if(!$(this).data('is-favorite')){
				self.addFavorite($(this).data('product-id'), $(this));
			}else{
				self.removeFavorite($(this).data('product-id'), $(this));	
			}
				
			evt.preventDefault();
			
		});
		
		$('body').on('click', '[data-action="addCompare"]', function(evt){
			
			if(!$(this).data('added')){
				self.addCompare($(this).data('product-id'), $(this));
			}else{
				self.removeCompare($(this).data('product-id'), $(this));	
			}
				
			evt.preventDefault();
			
		});
		
		$('body').on('click', '[data-action="removeCompare"]', function(evt){
			
			self.removeCompare($(this).data('product-id'), $(this));
				
			evt.preventDefault();
			
		});
		
	},
	
	addFavorite: function(product_id, button){
		
		var self = this;
		
		button.attr('disabled', true);
		
		$.post(
			'/catalog/?a=favorite_add',
			{'product_id': product_id},
			function(rsp){
				button.removeAttr('disabled');
				button.addClass('s-active');
				button.find('.fa').addClass('fa-heart').removeClass('fa-heart-o');
				button.data('is-favorite', true);
				var txt = button.find('span');
				if(txt.length)
					txt.text('Удалить из избранного');
				self.showFavoritesCount(rsp);
			}
		);
		
	},
	
	removeFavorite: function(product_id, button){
		
		var self = this;
		
		button.attr('disabled', true);
		
		$.post(
			'/catalog/?a=favorite_remove',
			{'product_id': product_id},
			function(rsp){
				button.removeAttr('disabled');
				button.removeClass('s-active');
				button.find('.fa').addClass('fa-heart-o').removeClass('fa-heart');
				button.data('is-favorite', false);
				if(button.data('remove-item')){
					if(button.parents('#content').find('.product-card').length <= 1){
						$('.page__body').html($('#favorites-empty').html());
					}
					button.parents('.product-card').parent().remove();
				}
				var txt = button.find('span');
				if(txt.length)
					txt.text('Добавить в избранное');
				self.showFavoritesCount(rsp);
			}
		);
		
	},
	
	showFavoritesCount: function(count){
		
		count = parseInt(count);
		var obj = $('#favorites-count');
		if(count)
			obj.text(count).show();
		else
			obj.hide();
		
	},
	
	addCompare: function(product_id, button){
		
		var self = this;
		
		button.attr('disabled', true);
		
		$.post(
			'/catalog/?a=compare_add',
			{'product_id': product_id},
			function(rsp){
				button.removeAttr('disabled');
				button.addClass('s-active');
				button.data('added', true);
				var txt = button.find('span');
				if(txt.length)
					txt.text('Убрать из сравнения');
				self.showCompareCount(rsp);
			}
		);
		
	},
	
	removeCompare: function(product_id, button){
		
		var self = this;
		
		button.attr('disabled', true);
		
		$.post(
			'/catalog/?a=compare_remove',
			{'product_id': product_id},
			function(rsp){
				button.removeAttr('disabled');
				button.removeClass('s-active');
				button.data('is-favorite', false);
				if(button.data('remove-item')){
					
					if(button.parents('table').find('tr:first-child > td').length == 2){
						button.parents('table').parent().remove();
						$('#compare-empty').show();
					}else{
					
						var ind = parseInt(button.parents('td').prevAll().length) + 1;
						button.parents('table').find('td:nth-child(' + ind + ')').remove();					
					
					}
					
				}
				var txt = button.find('span');
				if(txt.length)
					txt.text('Добавить к сравнению');
				self.showCompareCount(rsp);
			}
		);
		
	},
	
	showCompareCount: function(count){
		
		count = parseInt(count);
		var obj = $('#compare-count');
		if(count)
			obj.text(count).show();
		else
			obj.hide();
		
	},

	filter : {
		timer: null,
		content: null,
		filterPopup : $("<div class='b-filter__popup'></div>"),
		showPopup : function(obj){
			var self = this;
			var objPos = $(obj).offset();

			self.popupHover = 0;

			if($("body").find(".b-filter__popup").length <= 0) {
				self.filterPopup.appendTo("body").unbind('mouseenter mouseleave').on({
					'mouseenter': function(){
						self.popupHover = 1;
					},
					'mouseleave': function(){
						self.popupHover = 0;

						self.hidePopup(2000);
					}
				});
			}

			self.filterPopup.html(self.content);

			filterPos($(obj).closest(".b-filter__group").offset().left + 205, objPos.top, $(obj).height());

			function filterPos(x, y, height, no_hide){				
				self.filterPopup.show().css({
					left: x  + 10,
					top: y - (self.filterPopup.height()/2) - 7 + height/2 - 5,
					opacity: 1
				});
				
				setTimeout(
					function(){
						self.filterPopup.addClass('init');
					},
					200
				);
				
				if(!self.filterPopup.hasClass('loading')){
					self.hidePopup(4000);
				}
			};
		},
		hidePopup : function(delay){
			var self = this;

			clearTimeout(self.timer);
			self.timer = setTimeout(function(){
				if(self.popupHover == 0) {

					self.filterPopup.css({
						opacity: 0
					}).removeClass('init');

					setTimeout(function(){
						self.filterPopup.hide();
					}, 300)
				}
			}, delay);

		},
		buildQuery : function(){
			var self = this;
			var request = '';

			var fields = $('#filter-form').find('input');
			
			request = '&avail=' + ($('.b-filter').find('input[name="avail"]').is(':checked') ? 'y' : 'all');
			
			$.each(fields, function(){
				var obj = $(this);
				if(obj.attr('name') != 'avail') {

					if(obj.attr('type') == 'checkbox') {
						if(obj.prop('checked')) {
							request += '&' + obj.attr('name') + '=' + obj.val();
						}
					} else {
						if(obj.val() != '') {
							request += '&' + obj.attr('name') + '=' + obj.val();
						}
					}
				}
			});
					
			var cat_req = '';

			request = $('#filter-form').attr('action') + '?return_count=1' + request + cat_req;
			
			return request;
			
		},
		setContent : function(value){
			var self = this;

			if(value == 0) {
				self.content = $("<div class='b-filter__popup__title nothing'>Ничего не найдено</div>");
			}
			else {
				self.content = makeEnding(value, 'Найден', '', 'о', 'о') + ' ' + value + ' ' + makeEnding(value, 'товар', '', 'а', 'ов');
				self.content = $('<div class="b-filter__popup__title">'+self.content+'</div><div class="b-filter__popup__show"><button class="btn btn--med">Показать</button></div>');

			}
		},
		sendRequest : function(obj){
			var self = this;
			var request = self.buildQuery();
			
			self.filterPopup.addClass('loading');
			self.content = '';//<img src="/img/ajax-loader.gif"/>';
			self.showPopup(obj, true);

			$.ajax({
				url: request,
				success: function(data) {
					self.filterPopup.removeClass('loading');
					self.setContent(parseInt(data));
					self.showPopup(obj);
				}
			})
		},
		showFilter: function(){
			$(".b-page__content").fadeOut(200, function(){
				$("#mainbar").removeClass("b-mainbar--wide");
				$("#sidebar").show();
				$(this).fadeIn(200);
			});

			$.cookie('catalog_show_filter', 1, {path: '/'});
		},
		hideFilter: function(){
			$(".b-page__content").fadeOut(200, function(){
				$("#mainbar").addClass("b-mainbar--wide");
				$("#sidebar").hide();
				$(this).fadeIn(200);
			});

			$.removeCookie('catalog_show_filter', { path: '/' });
		},
		filterGroup: function(obj){
			var obj = $(obj);
			var url = obj.attr('href');
			var n_url = '';
			if(url.indexOf('=y') > -1)
				n_url = url.replace('=y', '=n');
			else
				n_url = url.replace('=n', '=y');
			
			obj.toggleClass('s-active').attr('href', n_url);

			$.loader.show($(".b-page__content"));
			
			$.get(url, {}, function(rsp){
					
					$('#content').html(rsp.content);
					$('#pager').html(rsp.pager);
					$('#pager_short').html(rsp.pager_short);

					$.loader.remove($(".b-page__content"));
					
					ajaxPager.init();
					
				},
				'json'
			);
		}
	}
}

$(document).ready(function(){
	$.catalog.init();
})
