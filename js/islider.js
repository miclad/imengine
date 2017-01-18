function iSlider(_params){
	
	this.params = _params;
	
	this.init();
	
}

iSlider.prototype = {
	
	init: function(){
		
		var self = this;

		this.cur_item = 0;
		this.container = $(this.params.container);
		this.items = $(this.container).find('.slider-item');
		
		var avail_params = new Array('speed', 'auto', 'interval', 'axis', 'bullets', 'arrows', 'effect');
		
		for(var i=0; i<avail_params.length; i++){
			if(this.container.data(avail_params[i])){
				this.params[avail_params[i]] = this.container.data(avail_params[i]);
			}
		}
		if(!this.params.axis)
			this.params.axis = 'x';
		
		if(!this.params.speed)
			this.params.speed = 500;
		
		if(!this.params.effect)
			this.params.effect = 'slide';
		
		this.interval_obj = null;		
		this.counter = 0;
		this.in_move = false;
		
		if(this.params.auto)
			this.automate();	
		
		if(this.params.arrows)
			this.buildArrowsNav();
		
		if(this.params.bullets)
			this.buildBulletsNav();
		
	},
	
	buildBulletsNav: function(){
		
		var self = this;
		
		var html = '<div class="slider-nav">';
		
		for(var i=0; i<this.items.length; i++){
			html += '<span' + (i == 0 ? ' class="slider-nav-active"' : '') + '></span>'
		}
		
		html += '</div>';
		
		var obj = $(html);
		
		obj.find('span').on('click', function(evt){
			
			var pos = $(this).prevAll().length;
		 	self.move(1, pos+1, this);
		 	if(self.params.auto){
				self.automate();
		 	}
			
			evt.preventDefault();
			
		});
		
		this.container.append(obj);
		//obj.css({'width': obj.width() + 'px'});
		
	},
	
	buildArrowsNav: function(){
		
		var self = this;
		
		var html = '<div class="slider-arrows">';
		html += '<span data-direction="back"></span>';
		html += '<span data-direction="forward"></span>';
		html += '</div>';
		
		var obj = $(html);
		
		obj.find('span').on('click', function(evt){
			if(!self.in_move){
				if(self.params.auto){
					self.automate();
				}
				self.move($(this).prev().length ? 1 : 0, null, this);
			}
			evt.preventDefault();
		});

		this.container.append(obj);
		
	},
	
	automate: function(){
		
		var self = this;
		
		if(self.interval_obj){
			clearInterval(self.interval_obj);
		}
			
		
		self.interval_obj = setInterval(
			function(){
				self.move(1);
			},
			self.params.interval
		);
		
	},
	
	stop: function(){
		
		if(this.interval_obj){
			clearInterval(this.intervalObj);
		}
		
	},
	
	move: function(dir, extra_pos, lnk){
		
		if(this.in_move)
			return false;
		
		if(extra_pos){
			var new_item = extra_pos-1;
			dir = this.cur_item < new_item ? 1 : 0; //$(this.items[new_item]).prevAll('.cur').length ? 1 : 0;
		}else{
			var new_item = dir > 0 ? this.cur_item+1 : this.cur_item-1;
			if(new_item > this.items.length-1)
				new_item = 0;
			else{
				if(new_item < 0)
					new_item = this.items.length-1;
			}
		}
		
		if(this.cur_item == new_item)
			return false;
		
		this.counter++;

		if(this.params.bullets){
			$('.slider-nav').find('span').eq(new_item).addClass('slider-nav-active').siblings().removeClass('slider-nav-active');
		}
		
		//alert(this.params.effect);
		
		if(this.params.effect == 'slide'){
		
			var container_size = this.params.axis == 'x' ? this.container.innerWidth() : this.container.innerHeight();
			var new_item_pos = null;
			var cur_item_pos = null;
			if(dir > 0){
				new_item_pos = container_size + 20;
				cur_item_pos = -container_size - 20;
			}else{
				new_item_pos = -container_size - 20;
				cur_item_pos = container_size + 20;
			}

			var axis_param = this.params.axis == 'x' ? 'left' : 'top';

			this.in_move = true;
			var self = this;

			

			if(!this.params.mode3d){

				$(this.items[new_item])
					.css({'left': new_item_pos})		
					.animate(
						{'left': '0px'}, 
						this.params.speed
					)
					.addClass('slider-item-active');

				$(this.items[this.cur_item])
					.animate(
						{'left': cur_item_pos}, 
						this.params.speed, 
						function(){
							self.in_move = false; 
							$(this).removeClass('slider-item-active');
						}
					);	

			}else{

				if(dir == 0)
					this.params.steps_real = this.params.steps_back;
				else
					this.params.steps_real = this.params.steps;

				//alert(this.cur_item + ' ' + new_item);

				var current_step = 0;
				var current_step_real = 0;
				var container_id = $(this.items[this.cur_item]).attr('id');
				var container_id_new = $(this.items[new_item]).attr('id');

				var ready_to_stop = false;

				$('.slider_controls').hide();

				var cur_width = Math.round(1041 + ($(document).width()-1041)/2);

				$('#' + container_id_new).css({'display': 'none'});		
				$('#' + container_id).css({'display': 'none'});

				for(var i=0; i<this.params.steps_real.length; i++){

					// move new items to hidden positions
					var n_el = $('#' + container_id_new + ' .'+ this.params.steps_real[i]['class']);
					var n_cpos = parseInt($(n_el).css('left'));
					$(n_el).css({'left': n_cpos + ((dir==0 ? -1 : 1)*cur_width)});

					// move current to visible positions
					var c_el = $('#' + container_id + ' .'+ this.params.steps_real[i]['class']);
					var cpos = parseInt($(c_el).css('left'));
					$(c_el).css({'left': cpos + ((dir==0 ? -1 : 1)*cur_width)});				

				}

				$('#' + container_id_new).css({'left': 0}).addClass('cur').show();			
				$('#' + container_id).css({'left': (dir==0 ? 1 : -1)*cur_width}).removeClass('cur').show();

				for(var i=0; i<this.params.steps_real.length; i++){

					setTimeout(
						function(){

							var step_data = self.params.steps_real[current_step];

							if(current_step == self.params.steps_real.length-1){
								ready_to_stop = true;
							}

							setTimeout(
								function(){
									var c_el = $('#' + container_id + ' .' + step_data['class']);
									var c_el_cpos = parseInt($(c_el).css('left'));
									$(c_el).animate({'left': c_el_cpos+(dir==0 ? 1 : -1)*cur_width}, step_data['speed']);
								},
								step_data['class'] == 'bg' ? 10 : 0
							);


							var n_el = $('#' + container_id_new + ' .'+ step_data['class']);
							var n_el_cpos = parseInt($(n_el).css('left'));
							$(n_el).animate({'left': n_el_cpos+(dir==0 ? 1 : -1)*cur_width}, step_data['speed'], function(){
								if(current_step_real == self.params.steps_real.length-1){
									setTimeout(
										function(){
											self.in_move = false;
											ready_to_stop = false;
											$('.slider_controls').show();
										},
										true || current_step_real == self.params.steps_real.length-1 ? 0 : 500
									);
								}
								current_step_real++;
							});

							current_step++;

						},
						this.params.steps[i].delay
					);

				}

			}
			
		}else{
			
			$(this.items[new_item])		
				.fadeIn(
					this.params.speed
				)
				.addClass('slider-item-active');

			$(this.items[this.cur_item])
				.fadeOut(
					this.params.speed, 
					function(){
						$(this).removeClass('slider-item-active');
					}
				);			
			
		}
		
		if(this.caption_container){
			$(this.caption_container).text($(this.items[new_item]).find('span').text());
		}
		
		if(this.navi_container){
			$(this.navi_container).find('a').removeClass('cur');
			$($(this.navi_container).find('a').get(new_item)).addClass('cur');
		}
		
		this.cur_item = new_item;
		
		
	}

}