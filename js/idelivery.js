function iDelivery(_sum, _sum_full, _types, _cities, _weight, _volume){
	
	this.current_type = 0;
	this.order_sum = _sum;
	this.order_sum_full = _sum_full;
	this.weight = _weight;
	this.types = _types;
	this.cities = _cities;
	this.volume = _volume ? _volume : 0.1;
	
	this.type_text = '';
	
}

iDelivery.prototype = {
	
	setParam: function(param, value){
	
		this[param] = value;
		
	},
	
	init: function(){
		
		oThis = this;
		
		$('body').on('change', '*[name="delivery[type]"]', function(){
			this.blur();
			oThis.typeChanged($(this).val());
		});
		
		$('body').on('change', '[data-role="delivery-params"] select',	function(){
				if(!$(this).hasClass('not_init_calc')){
					oThis.cityChanged();						
				}else{
					oThis.type_text = oThis.current_type;
					if($(this).val() == ''){
						$('input[name="delivery_ok"]').val('')
					}else{
						oThis.type_text += '|' + $(this).val();
						$('input[name="delivery_ok"]').val(1);
						$('input[name="delivery_type_str"]').val(oThis.type_text);
					}
				}
				oThis.saveTypeStr();
		});

		$(window).on('cityChanged', function(){
			
			var lo = new Loader('#delivery-types-container');
			
			$.get(
				'/delivery/',
				{},
				function(rsp){
					
					$('#delivery-types-container').html(rsp);
					$('*[name="delivery[type]"]:checked').trigger('change');
					
				}
			);
			
		})

	},

	typeChanged: function(type_id){
		
		$('input[name="delivery_ok"]').val('');
		$('input[name="delivery_error"]').val('');
		
		$('[data-role="delivery-params"]').hide();
		
		if(!type_id){
			this.updateDeliveryPrice(0);
			$('#payment_types').html('');
			return false;
		}
		
		$('#delivery-params-' + type_id).show();
			
		this.type_text = type_id;		
		this.current_type = type_id;		
		
		if(!this.isDeliveryAvail()){
			
			this.updateDeliveryPrice(0);
			var err_str = 'Доставка возможна при сумме заказа от ' + this.types['type' + this.current_type]['avail_from'] + ' руб.';
			$('#delivery-price-' + this.current_type).show().html('<span class="red">' + err_str + '</span>');
			$('input[name="delivery_error"]').val('Стоимость заказа для выбранного типа доставки должна быть не менее ' + this.types['type' + this.current_type]['avail_from'] + ' руб.');
			
		}else{
				
			if(!this.withCities()){
				
				if(this.getExternalCalc() == 'pickpoint'){
					
					this.updateDeliveryPrice(0);
					$('#delivery-price-' + this.current_type).text('').hide();
					
					if(this.isDeliveryAvail()){
						this.pickPointCalculate();
					}
					
				}else{
				
					$('input[name="delivery_ok"]').val(1);

					var pr = this.getDeliveryPrice(this.current_type);
					this.updateDeliveryPrice(pr.value);
					$('#delivery-price-' + type_id).show().text(pr.text);
				
				}
				
			}else{
				this.cityChanged();
			}
			
		}
		
		$('input[name="delivery_type_str"]').val(this.type_text);
		this.saveTypeStr();
		
		cur_pt = $('input:checked[name="payment"]').val();
		if(!cur_pt)
			cur_pt = current_payment_type;
		$.post(
			'/engine/components/payment/select.php',
			{'delivery_type': type_id, 'current_type': cur_pt},
			function(rsp){
				$('#payment_types').html(rsp);
				//$('#payment_types div.payment_notes').hide();
			}
		);		

		// toggle order fields
		
		for(var field in this.types['type' + this.current_type]['order_fields']){
			
			var f_item = $('#order-field__' + field);
			
			//console.log(this.types['type' + this.current_type]['order_fields'][field]);
			
			if(this.types['type' + this.current_type]['order_fields'][field]){
				f_item.show();
				if(field == 'address_city' || field == 'address_post_index')
					f_item.parents('.form__item').show();
			}else{
				f_item.hide();
				if(field == 'address_city' || field == 'address_post_index')
					f_item.parents('.form__item').hide();
			}
			
		}
		
		return true;
	
	},
	
	cityChanged: function(){
		
		$('input[name="delivery_ok"]').val('');
		
		var price = 0;
		var text = '';
		
		if(this.getExternalCalc() == 'ems'){			
			
			this.updateDeliveryPrice(0);
			if(this.isDeliveryAvail())
				this.emsCalculate();				
			
		}else{
			
			if(this.getExternalCalc() == 'dellin'){
			
				this.updateDeliveryPrice(0);
				if(this.isDeliveryAvail())
					this.dellinCalculate();				
				
			}else{
				
				if(this.getExternalCalc() == 'russianpost'){
						
						this.updateDeliveryPrice(0);
						if(this.isDeliveryAvail())
							this.russianpostCalculate();
						
				}else{
		
					if(this.isDeliveryAvail() && this.withCities()){

						var val = parseInt($('[name="delivery[city' + this.current_type + ']"]').val());			
						this.type_text = this.current_type + '|' + val;
						if(val){
							$('input[name="delivery_ok"]').val(1);
							
							
							price = 0; //this.cities['city' + val];
							
							if(this.cities['city' + val]){
								for(var i=0; i<this.cities['city' + val].length; i++){
									if(this.order_sum >= this.cities['city' + val][i]['order_sum']){
										price = this.cities['city' + val][i]['price'];
										break;
									}
								}
							}
							
							
							text = this.generateDeliveryPriceText(price);
						}

						$('#delivery-price-' + this.current_type).text(text).show();
						if(text == ''){
							$('#delivery-price-' + this.current_type).hide();
						}	

					}			

					this.updateDeliveryPrice(price);	
					
				}
				
			}
			
		}
		
		$('input[name="delivery_type_str"]').val(this.type_text);
		$('input[name="delivery_type_str"]')
		
	},

	getDeliveryPrice: function(type_id){
		
		var price = 0;
		if(this.types['type' + type_id]['prices']){
			for(var i=0; i<this.types['type' + type_id]['prices'].length; i++){
				if(this.order_sum >= this.types['type' + type_id]['prices'][i][0]){
					price = this.types['type' + type_id]['prices'][i][1];
					break;
				}
			}
		}
		return {'value': price, 'text': this.generateDeliveryPriceText(price)};
		
	},

	isDeliveryAvail: function(){
		return this.current_type && (this.types['type' + this.current_type]['avail_from'] <= this.order_sum);
	},
	
	withCities: function(){
		return $('select[name="delivery[city' + this.current_type + ']"]').length;
	},
	
	getExternalCalc: function(){
		return this.types['type' + this.current_type]['external_calc'];
	},
	
	generateDeliveryPriceText: function(price){
		var txt = '';
		if(!price){
			txt = 'Бесплатно';
		}else{
			//txt = 'Стоимость доставки ' + price + ' руб.';
			txt = price + ' руб.';
		}
		return txt;
	},

	updateDeliveryPrice: function(price){
		$('input[name="delivery[price]"]').val(price);
		this.calculateSumPlusDelivery();
	},

	calculateSumPlusDelivery: function(){
		var koef = 1;//this.getExternalCalc() == 'ems' && $('input[name="ems_nalozhenniy_platezh"]').attr('checked') ? 1.01 : 1;
		var deliv = parseInt($('input[name="delivery[price]"]').val());
		var sum = this.order_sum;
		var use_bonus = $('input[name="use_bonus"]').length ? parseInt($('input[name="use_bonus"]').val()) : 0;
		$('#sumPlusDelivery').text(formatNumber(Math.floor((deliv + sum - use_bonus)*koef)));	
		if(deliv > 0){
			$('#sum_container').show();
			//$('#sum').text(formatNumber(Math.floor((sum)*koef)));
		}else{
			//$('#sum').text(formatNumber(this.order_sum));
			$('#sum_container').hide();
		}
	},

	orderSumChanged: function(sum, sum_full, _weight){
		
		this.order_sum = sum;
		this.order_sum_full = sum_full;
		this.weight = _weight;
		
		if(!this.typeChanged(this.current_type)){
			this.calculateSumPlusDelivery();
		}
		
	},
	
	/* EMS functions */
	
	emsCalculate: function(){
		$('#delivery-price-' + this.current_type).text('').hide();
		var city = $('select[name="delivery[city' + this.current_type + ']"]').val();
		this.type_text = this.current_type + '|' + city;
		if(city != ''){
			$('input[name="delivery_ok"]').val(1);
			$('#delivery-price-' + this.current_type).html('<img src="/img/ajax-loader.gif"/>').show();
			oThis = this;
			if(!this.weight){
				this.weight = 1;
			}
			$.getScript('http://emspost.ru/api/rest?callback=oThis.onEMSCalculated&method=ems.calculate&from=city--sankt-peterburg&to=' + city + '&weight=' + this.weight);
		}
	},
	
	onEMSCalculated: function(resp){
		$('#delivery-price-' + this.current_type + '_error').html('').hide();
		$('#delivery-term-' + this.current_type).html('').hide();
		if(resp.rsp.term){			
			this.updateDeliveryPrice(resp.rsp.price);			
			var text = 'Срок: ' + resp.rsp.term.min + ' &ndash; ' + resp.rsp.term.max + ' дн.';
			if(text)
				$('#delivery-term-' + this.current_type).html(text).show();
			var pr = this.generateDeliveryPriceText(resp.rsp.price);
			$('#delivery-price-' + this.current_type).html(pr).show();
		} else {
			this.updateDeliveryPrice(0);
			$('#delivery-price-' + this.current_type + '_error').show().html('<span class="red">Превышен расчетный вес. Стоимость доставки уточните у менеджера</span>');
			$('#delivery-price-' + this.current_type).html('');
		}
	},
	
	/* Dellin functions */
	
	dellinCalculate: function(by_btn){
		$('#delivery-price-' + this.current_type).text('').hide();
		var city = $('select[name="delivery[city' + this.current_type + ']"]').val();
		this.type_text = this.current_group + '|' + this.current_type + '|' + city;
		if(city != ''){
			$('input[name="delivery_ok"]').val(1);
			$('#delivery-price-' + this.current_type).html('<img src="/img/ajax-loader.gif"/>').show();
			if(!this.weight){
				this.weight = 1;
			}				
			var derival_point = '0x805900112FDD658311DA3BC904F59A69';//отправка из Санкт-Петербурга
			var params = {
				'derivalPoint': derival_point,
				'arrivalPoint': city,
				'sizedWeight': this.weight,
				'sizedVolume': this.volume,
				'arrivalDoor': $('input[name="dellin_arrival_door"]').attr('checked') ? 1 : 0
			}
			
			if(params.arrivalDoor){
				this.type_text += '|1';
			}
			
			$('input[name="delivery_type_str"]').val(this.type_text);
			
			$.getJSON(
				'/engine/components/delivery/dellin_calc.php',
				params,
				function(rsp){oThis.onDellinCalculated(rsp);}
			);
		}else{
			if(by_btn)
				alert('Необходимо выбрать город');
		}
	},
	
	onDellinCalculated: function(resp){
		if(resp.price){			
			this.updateDeliveryPrice(resp.price);			
			var text = this.generateDeliveryPriceText(resp.price);
			$('#delivery-price-' + this.current_type).html(text).show();
		}else{
			this.updateDeliveryPrice(0);
			if(resp.error){
				$('#delivery-price-' + this.current_type).html('<span class="red">' + resp.error + '</span>');
			}
		}
	},
	
	// PickPoint
	
	pickPointResponse: function(rsp){
		
		if(rsp.address){
			$('#pickpoint_default_text_' + oDelivery.current_type).hide();
			$('#pickpoint_name_' + oDelivery.current_type).text(rsp.name);
			$('#pickpoint_address_' + oDelivery.current_type).text(rsp.address);
			$('#pickpoint_name_inp_' + oDelivery.current_type).val(rsp.name);
			$('#pickpoint_address_inp_' + oDelivery.current_type).val(rsp.address);
			$('#pickpoint_id_inp_' + oDelivery.current_type).val(rsp.id);
			$('#pickpoint_selected_' + oDelivery.current_type).show();
			oDelivery.pickPointCalculate();
		}
			
	},
	
	pickPointCalculate: function(){
		
		var price = 0;
		var price_found = false;

		if(this.types['type' + this.current_type]['prices'].length){
			for(var i=0; i<this.types['type' + this.current_type]['prices'].length; i++){
				if(this.order_sum >= this.types['type' + this.current_type]['prices'][i][0]){
					price_found = true;
					price = this.types['type' + this.current_type]['prices'][i][1];
					break;
				}
			}
		}
		
		$('#delivery-price-' + this.current_type).text('').hide();
		$('#delivery-term-' + this.current_type).html('').hide();
		
		var addr = $('#pickpoint_address_' + this.current_type).text();
		var oThis = this;
		if(addr){
			$('input[name="delivery_ok"]').val(1);
			$.getJSON(
				'/engine/components/delivery/pickpoint_calc.php',
				{'address': addr, 'weight': oThis.weight},
				function(rsp){
					var tariff = price_found ? price : rsp.tariff;
					oThis.updateDeliveryPrice(tariff != 'not_set' ? parseInt(tariff) : 0);
					var text = '';
					if(tariff != 'not_set'){
						text = oThis.generateDeliveryPriceText(parseInt(tariff));
						if(rsp.time != '')
							$('#delivery-price-' + oThis.current_type + '_info').html('Срок: ' + rsp.time + ' дн.').show();
						else
							$('#delivery-price-' + oThis.current_type + '_info').html('').hide();
						$('#delivery-price-' + oThis.current_type).html(text).show();						
					}else{
						text = '<b>Стоимость доставки уточняйте по телефону</b>';
						$('#delivery-price-' + oThis.current_type + '_info').html(text).show();
					}
					/*if(rsp.time != '')
						text += '<br>Срок: ' + rsp.time;
					*/					
					$('input[name="delivery_result_info"]').val(text);
					
				}
			);
		}
		
	},
	
	/* RSP functions */
	
	russianpostCalculate: function(by_btn){
		
		$('#delivery-price-' + this.current_type).text('').hide();
		//var index = $('input[name="russianpost_index"]').val();
		var index = $('select[name="delivery[city' + this.current_type + ']"]').val();
		this.type_text = this.current_type + '|' + index;
		
		if(index != ''){
			
			$('input[name="delivery_ok"]').val(1);
			$('#delivery-price-' + this.current_type).html('<img src="/img/ajax-loader.gif"/>').show();
			if(!this.weight){
				this.weight = 0;
			}		
			
			$('input[name="delivery_type_str"]').val(this.type_text);
			
			var weight = parseFloat(this.weight);
			/*
			if(weight < 1.001){
				weight += 0.3;
			}else{
				if(weight < 5.001){
					weight += 0.5;
				}else{
					weight += 0.8;
				}
			}
			*/
			
			var params = {
				'to': index,
				'weight': weight
			}
			
			$.getJSON(
				'/engine/components/delivery/russianpost_calc.php',
				params,
				function(rsp){oThis.onRussianpostCalculated(rsp);}
			);
			
		}else{
			if(by_btn)
				alert('Пожалуйста, введите индекс');
		}
	},
	
	onRussianpostCalculated: function(resp){
		
		resp.price = parseInt(resp.price);

		if(resp.price){
			
			resp.price += 150;
			
			$('input[name="delivery_ok"]').val(1);
			
			var price = parseInt(resp.price);
			/*
			if(this.ems_weight > 10)
				price = price*1.3;
				
			if(this.ems_weight  < 1.001){
				price += 50;
			}else{
				if(this.ems_weight < 5.001)
					price += 75;
				else
					price += 100;	
			}
			*/
			
			this.updateDeliveryPrice(price);			
			var text = this.generateDeliveryPriceText(price);
			if(resp.days != '')
				$('#delivery-term-' + this.current_type).html('Срок: ' + resp.days).show();
			else
				$('#delivery-term-' + this.current_type).html('').hide();
			$('#delivery-price-' + this.current_type).html(text).show();
		}else{
			$('input[name="delivery_type_str"]').val('');
			$('input[name="delivery_ok"]').val('');
			this.updateDeliveryPrice(0);
			$('#delivery-price-' + this.current_type).html('Доставка временно недоступна').show();
			if(resp.error){
				$('#delivery-price-' + this.current_type).html('<span class="red">' + resp.error + '</span>');
			}
		}
		
	},
	
	/* CPCR functions */
	
	cpcrCalculate: function(by_btn){
		
		$('#delivery-price-' + this.current_type).text('').hide();
		var city = $('input[name="delivery[city' + this.current_type + ']"]').val();
		var city_id = $('input[name="delivery[city' + this.current_type + '_id]"]').val() ? $('input[name="delivery[city' + this.current_type + '_id]"]').val() : '';
		this.type_text = this.current_type + '|' + city;// + '|' + city_id.replace('|', '-');
		if(city != ''){
			
			$('input[name="delivery_ok"]').val(1);
			$('#delivery-price-' + this.current_type).html('<img src="/img/ajax-loader.gif"/>').show();
			if(!this.weight){
				this.weight = 0;
			}
			
			if(!this.weight)
				this.weight = 1;

			var params = {
				'to': city,
				'to_id': city_id,
				'weight': this.weight,
				'volume': this.volume,
				'cost': this.order_sum				
			}
			
			$('input[name="delivery_type_str"]').val(this.type_text);
			
			$.getJSON(
				'/engine/components/delivery/cpcr_calc.php',
				params,
				function(rsp){oThis.onCpcrCalculated(rsp);}
			);
		}else{
			if(by_btn)
				alert('Необходимо указать город');
		}
	},
	
	onCpcrCalculated: function(resp){
		
		resp.price = Math.round(resp.price);
		
		if(resp.price){			
			this.updateDeliveryPrice(resp.price);			
			var text = this.generateDeliveryPriceText(resp.price);
			$('#delivery-price-' + this.current_type).html(text).show();
			
			if(resp.days != '')
				$('#delivery-term-' + this.current_type).html('Срок: ' + resp.days).show();
			else
				$('#delivery-term-' + this.current_type).html('').hide();
			
		}else{
			this.updateDeliveryPrice(0);
			if(resp.choose_city.length){
				
				var str = '<div style="color:#333;"><div style="padding:4px 0;">Пожалуйста, уточните город:</div>';
				str += '<table>';
				for(var i=0; i<resp.choose_city.length; i++){
					str += '<tr><td style="padding:2px;"><input type="radio" name="delivery[city' + this.current_type + '_id]" value="' + (resp['choose_city'][i].id) + '" style="margin:0;" onclick="oDelivery.cpcrCalculate()"/></td>';
					str += '<td style="padding:2px;">' + resp['choose_city'][i]['name'] + ', ' + resp['choose_city'][i]['region'] + '</td></tr>';
				}
				str += '</table></div>';
				
				$('#choose_city_' + this.current_type).html(str);
				$('#delivery-price-' + this.current_type).html('');
				
			}else{
				if(resp.error != ''){
					$('#delivery-price-' + this.current_type).html('<span class="red">' + resp.error + '</span>');
				}
			}
		}
		
	},
	
	saveTypeStr: function(){
		var val = $('input[name="delivery_type_str"]').val();
		$.post(
			'/cart/save_delivery/',
			{'str': val}
		);
	}
	
}
