/* -------------------------------------------------- */
/* switch */

(function(window, $){
	
	function photoSwitcher(elem, options){
		
		this.elem = elem;
		this.options = options;
		
		this.init();
		
	}

	photoSwitcher.prototype = {

		init: function(){
			
			var element = $(this.elem);

			var previews = element.find('[data-role="previews"] a');
			var main_photo = element.find('[data-role="main-photo"] a');
			
			previews.on('click', function(evt){
				
				if(!$(this).hasClass('s-active')){
					var index = $(this).parent().prevAll().length;
					main_photo.eq(index).show().siblings().hide();
					/*
					main_photo.find('img').attr('src', $(this).attr('href'));
					main_photo.attr('href', $(this).data('large'));
					*/
					$(this).addClass('s-active').parent().siblings().find('a').removeClass('s-active');
				}
				
				evt.preventDefault();
				
			});

		}

	}

	$.fn.photoSwitcher = function(options){
		return this.each(function(){
			new photoSwitcher(this, options);
		})
	}
	
})(window, jQuery);

(function(window, $){
	
	function iTabs(elem, options){
		
		this.elem = elem;
		this.options = options;
		
		this.init();
		
	}

	iTabs.prototype = {

		init: function(){
			
			var element = $(this.elem);

			var controls = element.find('a');
			var no_content = element.data('no-content');
			var content = !no_content ? element.next().find('> div') : null;
			
			controls.on('click', function(evt){
				
				if(!$(this).hasClass('s-active')){					
					var index = $(this).prevAll().length;
					$(this).addClass('s-active').siblings().removeClass('s-active');
					if(!no_content){
						content.eq(index).show().siblings().hide();
					}
					element.trigger('tabChanged');					
				}
				
				evt.preventDefault();
				
			});

		}

	}

	$.fn.iTabs = function(options){
		return this.each(function(){
			new iTabs(this, options);
		})
	}
	
})(window, jQuery);

(function(window, $){
	
	function affix(elem, options){
		
		this.items = elem;
		this.options = options;
		
		this.init();
		
	}

	affix.prototype = {

		init: function(){
			
			var self = this;
			
			$(self.items).on('resize', function(){
		
				var container = $(this);
				if(container.hasClass('affix--fixed')){
					container.parent().css({'height': $(this).outerHeight()});
				}

			});
			
			$(window).scroll(function(){
				
				var doc_top = $(document).scrollTop();
				
				$(self.items).each(function(){

					var obj = $(this);
					var container = obj.data('container') ? $($(obj).data('container')) : $('body');

					var offset = 0;
					if($(obj).data('offset'))
						offset = parseInt($(obj).data('offset'));

					var obj_cur_top = $(obj).offset().top;
					var obj_height = obj.outerHeight();
					var container_height = container.outerHeight();
					var container_top = container.offset().top;
					var limit = container_height + container_top - doc_top;
					
					var is_fixed = obj.hasClass('affix--fixed');		
					if(is_fixed)
						obj.parent().css({'height': obj_height})						
					
					if(obj_cur_top - offset <= doc_top){
						
						if(!is_fixed)
							$(obj).css({'top': offset, 'width': obj.width()}).data('top', obj_cur_top).addClass('affix--fixed');//.wrap('<div style="height: ' + obj_height + 'px"></div>');					
						
						if(obj_height + offset > limit)
							obj.css({'top': limit - obj_height - offset});
						else
							obj.css({'top': offset});
						
					}
					
					if(obj.hasClass('affix--fixed')){
						if(obj.data('top') - offset > doc_top){
							obj.css('top', 0).removeClass('affix--fixed');//.unwrap();
						}
					}
				
				});
				
			});

		}

	}

	$.fn.affix = function(options){
		return this.each(function(){
			new affix(this, options);
		})
	}
	
})(window, jQuery);

/* -------------------------------------------------- */
/* toggle */

$.fn.toggle = function(name){
	return this.each(function(){	

		$(this).on('click', name+"__open", function(){

			var open = $(this);
			var container = $(this).parents(name).eq(0);
			var wrap = container.find(name + '__wrap').eq(0);
			var children  = container.data('children') ? $(this).find(container.data('children')) : null;
			
			if(wrap.is(":visible")) {
				wrap.slideUp(167);
				$(children ? children : open).text(container.data('show'));
				container.removeClass("s-open");
			} else {
				wrap.slideDown(167);
				$(children ? children : open).text(container.data('hide'));
				container.addClass("s-open");
			}

			return false;
		});
		
	});
}

$.fn.count = function(){

	return this.each(function(){
		
		var self  = this;
		var container = $(this);
		var plus  = container.find('[data-action="plus"]');
		var minus = container.find('[data-action="minus"]');
		var field = container.find('input');
		var step = parseInt(container.data('step'));
		if(!step)
			step = 1;
		var min_value = parseInt(container.data('min-value'));
		var max_value = parseInt(container.data('max-value'));
		
		field.on('keyup', function(){
			
			var val = $(this).val();
			var f_val = val.replace(/[^0-9]/, '');
			if(f_val == '')
				f_val = 0;
			
			f_val = parseInt(f_val);
			
			if(f_val < min_value){
				f_val = min_value;
			}else{
				
				if(max_value > 0 && f_val > max_value){
					f_val = max_value;
					container.addClass("error_animate");
		
					setTimeout(function(){
						container.removeClass("error_animate");
					}, 800)
				}
				
			}
			
			if(val != f_val){
				$(this).val(f_val);
			}
			
			$(this).trigger('quantityChanged');
			
		});

		plus.add(minus).unbind('click').on('click', function(evt){
			
			var button = $(this);
			var value = parseInt(field.val());
			var nv = 0;

			if(button.data('action') == 'plus') {
				nv = value+step;
				if(max_value > 0 && nv > max_value){
					nv = max_value;
					
					obj.addClass("error_animate");
		
					setTimeout(function(){
						obj.removeClass("error_animate");
					}, 800)
					
				}
				value = nv;
			}
			else {
				nv = value-step;
				if(nv < min_value)
					nv = min_value;
				value = nv;
			}

			field.val(value).trigger('quantityChanged');

			evt.preventDefault();
			
		})
		.attr('unselectable', 'on')
		.css('user-select', 'none')
		.on('selectstart', false);;
		
		if(container.data('trigger-on-load'))
			field.trigger('quantityChanged')
		
	});
	
};

function checkAll(formname, but, el){
	var frm = (typeof formname == "string") ? document.forms[formname] : formname[0];
	var but_ob = (typeof but == "string") ? $('input[name="' + but + '"]', frm) : but;
	var elements = $('input:checkbox[name="' + el + '"]', frm);
	but_ob.blur();
	for(var i=0; i < elements.length; i++){
		if(but_ob.tagName != 'input' || $(but_ob).attr('checked'))
			elements[i].checked = true;
		else
			elements[i].checked = false;
	}
}

function fillSelect(select, options, settings, selected){
	
	if(!settings)
		settings = {'value': 'id', 'text': 'name'};
	
	$(select).find('option').remove();
	var sel = $(select).get(0);
	
	var opt = null;
	
	//console.log(options);
	
	$(options).each(function(){
		
		opt = new Option($(this).attr(settings.text), $(this).attr(settings.value));
		if($(this).attr('_selected') == 1){
			opt.selected = true;
		}
		
		sel.options[sel.options.length] = opt;
		
	});
	
}

/* Common */

function getLocation(){
	
	var loc = window.location.toString();
	
	// no get
	if(loc.indexOf('?') > -1)
		loc = loc.substring(0, loc.indexOf('?'));
		
	loc = loc.replace(/^https?:\/\/[A-z0-9.-]+/i, '');
	
	return loc;
	
}

function openwin(url, width, height, id){
	if(!id)
		id = 'popup';
	if(!width)
		width = 580;
	if(!height)
		height = 400;
	var left = ($(window).width()-width)/2;
	var top = ($(window).height()-height)/2;
	window.open(url, id, 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',location=no,directories=no,status=no,toolbar=no,menubar=no');
}

function formatNumber(src){
	src = src.toString();
	var cnt = 1;
	var str = '';
	var lngth = 0;
	var res =  '';
	for(var i=src.length-1; i >= 0; i--){
		str += src.substr(i, 1);
		lngth++;
		if((src.length-i)%3 == 0 && i > 0){
			str += ' ';
			lngth++;
		}
	}	
	var t = '';
	for(var i=lngth-1; i>=0; i--){
		res += str.substr(i, 1);
	}
	return res;
}

function Loader(obj, control_item, control_text, not_show){	
	this.obj = obj;
	this.control_item = control_item;
	this.control_text = control_text;
	if(!not_show)
		this.show();
}

Loader.prototype = {
	'show': function(){
		$(this.obj).append(
			'<div class="loader" style="width:' + $(this.obj).outerWidth() + 'px; height:' + $(this.obj).outerHeight() + 'px;"><span></span></div>'
		).css({'position': 'relative'});
		if(this.control_item && this.control_text){
			this.control_item.data('orig-text', this.control_item.html()).text(this.control_text);
		}
	},
	'remove': function(){
		$(this.obj).find('.loader').remove();
		if(this.control_item){
			this.control_item.html(this.control_item.data('orig-text'));
		}
	}
}

function makeEnding(num, word, end1, end2, end3){
	num = parseInt(num);
	var postfix = '';
	if(num%100 > 10 && num%100 < 21 || num%10 >= 5 && num%10 <= 9 || num%10 == 0){
		postfix = end3;
	}else{
		if(num%10 >= 2 && num%10 <= 4){
			postfix = end2;
		}else{
			postfix = end1;
		}
	}
	return word + postfix;
}

function copyPreviousElement(lnk, parent_before, callback){
	lnk.blur();
	var new_el = $(lnk).data('add-selector') ? $($(lnk).data('add-selector')).eq(0).clone() : $(lnk).prev().clone();
	var ins = lnk;
	if(parent_before){
		for(var i=0; i<parent_before; i++){
			ins = $(ins).parent();
		}
	}
	$(new_el).insertBefore(ins);
	/* clear inputs */
	$('select', new_el).add('input', new_el).add(new_el).val('');
	if(callback)
		callback(new_el);
}