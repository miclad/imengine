

(function(window, $){
	
	function setOptions(elem, data){
		
		this.elem = $(elem);
		this.data = data;
		
		this.init();
		
	}

	setOptions.prototype = {

		init: function(){
			
			if(typeof this.data.text == 'undefined')
				this.data.text = 'name';
			
			if(typeof this.data.value == 'undefined')
				this.data.value = 'id';
			
			var options = this.elem.find('option');

			options.remove();
			var select = this.elem.get(0);
			var opt = null;

			for(var i=0; i<this.data.values.length; i++){

				opt = new Option(this.data.values[i][this.data.text], this.data.values[i][this.data.value]);
				if(typeof this.data.values[i]['selected'] !== 'undefined' && this.data.values[i]['selected'] || typeof this.data.selected !== 'undefined' && options[i][this.data.value] == this.data.selected){
					opt.selected = true;
				}

				select.options[select.options.length] = opt;

			}

		}

	}

	$.fn.setOptions = function(data){
		return this.each(function(){
			new setOptions(this, data);
		})
	}
	
})(window, jQuery);

(function(window, $){
	
	function tabs(elem, options){
		
		this.elem = elem;
		this.options = options;
		
		this.init();
		
	}

	tabs.prototype = {

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

	$.fn.tabs = function(options){
		return this.each(function(){
			new tabs(this, options);
		})
	}
	
})(window, jQuery);


(function(window, $){
	
	function commaInput(elem, data){
		
		this.elem = $(elem);
		this.data = data;
		
		this.init();
		
	}

	commaInput.prototype = {

		init: function(){
			
			var self = this;
			
			this.toggle = this.elem.find('[data-action="toggle"]');
			this.values_container = this.elem.find('[data-role="values"]');
			this.input_container = this.elem.find('[data-role="input"]');
			this.save_button = this.elem.find('[data-action="save"]')
			this.input = this.elem.find('input');
			
			this.toggle.on('click', function(evt){
				
				self.input_container.toggle();
				self.values_container.toggle();
				evt.preventDefault();
				
			});
			
			this.save_button.on('click', function(evt){
				
				self.buildValues();
				
				self.input_container.hide();
				self.values_container.show();
				
				evt.preventDefault();
				
			});
			
			this.values_container.on('click', '.ic', function(){
				
				var li = $(this).parent();
				var ul = li.parent();
				
				li.remove();
				
				if(!ul.find('li').length)
					ul.remove();
				
				self.buildString();
				
			});
			
			self.buildValues();
			

		},
		
		buildValues: function(){
			
			var val = this.input.val();
			
			var list = new Array();
			if(val){
				list = val.split(',');
			}
			
			var str = '';
			
			if(list.length){
				str += '<ul>';
				var _v = '';
				for(var i=0; i<list.length; i++){
					_v = list[i].replace(/^\s+|\s+$/g, '');
					if(_v != ''){
						str += '<li>';
						str += _v;
						str += '<i class="ic ic-close"></i>';
						str += '</li>';
					}
				}					
				str += '</ul>';
			}
						
			this.values_container.html(str);
			
		},
		
		buildString: function(){
			
			var items = this.values_container.find('li');
			var str = '';
			
			for(var i=0; i<items.length; i++){
				if(str != '')
					str += ', ';
				str += items.eq(i).text()
			}
			
			this.input.val(str);
			
		}

	}

	$.fn.commaInput = function(data){
		return this.each(function(){
			new commaInput(this, data);
		})
	}
	
})(window, jQuery);

$.fn.dropdown = function(name){
	return this.each(function(){
		
		if(typeof name == 'undefined'){
			name = '.dropdown';
		}

		$(this).on('click', name + "__open", function(){

			var open = $(this);
			var container = $(this).parents(name);
			var parent = container.parent();
			var wrap = container.find(name + '__body');
			
			$('.dropdown.s-open').not(container).removeClass('s-open');
			
			if(container.hasClass('s-open')) {
				container.removeClass("s-open");				
			}else{
				container.addClass("s-open");
			}

			return false;
			
		});
		
		$('body').on('click', function(evt){
			
			var tgt = $(evt.target);
			var in_dropdown = null;
			
			if(tgt.hasClass('dropdown'))
				in_dropdown = tgt;
			else
				in_dropdown = $(evt.target).parents('.dropdown');
			
			if(!in_dropdown.length){
				
				$('.dropdown.s-open').removeClass('s-open');
				
			}else{
				
				$('.dropdown.s-open').not(in_dropdown).removeClass('s-open');
				
			}
			
		});
		
	});
}

/* -------------------------------------------------- */
/* toggle */

$.fn.iToggle = function(name){
	return this.each(function(){	
		
		name = '.toggle';
		
		var container = $(this);
		var open = container.find(name + '__open').eq(0);
		var parent = container.parent();
		var stack = parent.data('role') == 'toggle-stack' ? parent : null;
		var wrap = container.find(name + '__wrap').eq(0);
		var children  = container.data('children') ? $(this).find(container.data('children')) : null;

		var open_tagname = open.get(0).tagName.toLowerCase();
		
		if(open_tagname == 'select'){
				
				open.on('change', function(){
					
					var opt = $(this).find('option:selected');
					if(opt.data('role') == 'open'){
						wrap.show()
					}else{
						wrap.hide();
					}
					
				});
				
		}else{
		
			open.on('click', function(){

				$(this).blur();

				if(container.hasClass('s-open')) {
					$(children ? children : open).html(container.data('show'));
					container.removeClass("s-open");				
				} else {
					$(children ? children : open).html(container.data('hide'));
					container.addClass("s-open");
				}

				if(stack){
					if(stack.find(name + '.s-open').length)
						stack.addClass('has-open');
					else
						stack.removeClass('has-open');				
				}

				container.trigger('toggled');

				return false;

			});
		
		}
		
	});
}

var iUtils = {

	getLocation: function(){
	
		var loc = window.location.toString();

		// no get
		if(loc.indexOf('?') > -1)
			loc = loc.substring(0, loc.indexOf('?'));

		loc = loc.replace(/^https?:\/\/[A-z0-9.-]+/i, '');

		return loc;

	},
	
	formatNumber: function(src){
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
	},
	
	loader: function(obj, control_item, control_text, not_show){
		
		this.obj = obj;
		this.control_item = control_item;
		this.control_text = control_text;
		if(!not_show)
			this.show();
		
	},
	
	makeEnding: function(num, word, end1, end2, end3){
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
	},
	
	instantFilter: function(params){
	
		this.params = params;
		this.init();

	},
	
	fillSelect: function(select, options, settings, selected){
	
		if(!settings)
			settings = {'value': 'id', 'text': 'name'};

		$(select).find('option').remove();
		var sel = $(select).get(0);

		var opt = null;

		for(var i=0; i<options.length; i++){

			opt = new Option(options[i][settings.text], options[i][settings.value]);
			if(options[i][settings.value] == selected)
				opt.selected = true;

			sel.options[sel.options.length] = opt;

		}

	},
	
	popup: function(content){
	
		content = '<div class="popup">' + content + '</div>';

		$.magnificPopup.open({
			fixedContentPos: true,
			fixedBgPos: true,
			removalDelay: 0,
			mainClass: 'mfp-fade',
			tLoading: '<img src="/img/ajax-loader.gif"/>',
			items: {
				src: content,
				type: 'inline'
			},
			callbacks: popupUtils.callbacks()
		});

	},
	
	popupAjax: function(url){
	
		$.magnificPopup.open({
			type: 'ajax',
			items: {
				src: url
			},
			callbacks: popupUtils.callbacks(),
			overflowY: 'scroll'
		});

	},
	
	popupClose: function(){
		
		$.magnificPopup.close();
		
	},
	
	showInformer: function(title, text){
		
		var cnt = '<div class="box informer--success">';
		cnt += '<div class="box__inner">';
		cnt += '<div class="informer">';
		cnt += '<h4>' + title + '</h4>';
		cnt += '<p>' + text + '</p>';
		cnt += '<span class="informer__close"><i class="ic ic-close"></i></span>';
		cnt += '</div></div></div>';
		
		cnt = $(cnt);
		
		$('.mainbar').prepend(cnt);
		
		setTimeout(function(){
			
			cnt.fadeOut(function(){
				cnt.remove();
			});
			
		}, 4000);
		
	},
	
	translit: function(word){
		
		var dictionary = {
			'а': 'a', 
			'б': 'b',
			'в': 'v',
			'г': 'g',
			'д': 'd',
			'е': 'e',
			'ё': 'e',
			'ж': 'zh',
			'з': 'z',
			'и': 'i',
			'й': 'j',
			'к': 'k',
			'л': 'l',
			'м': 'm',
			'н': 'n',
			'о': 'o',
			'п': 'p',
			'р': 'r',
			'с': 's',
			'т': 't',
			'у': 'u',
			'ф': 'f',
			'х': 'h',
			'ц': 'ts',
			'ч': 'ch',
			'ш': 'sh',
			'щ': 'sch',
			'ъ': '',
			'ы': 'y',
			'ь': '',
			'э': 'e',
			'ю': 'ju',
			'я': 'ja',
			' ': '-',
			'\'': '',
			'"': '',
			'\\': '',
			'/': '',
			'%': '',
			'?': '',
			'#': '',
			',': '',
			'&': 'and'
		}

		var new_str = '';
		var ch = '';
		var code = 0;

		for(var i=0; i < word.length; i++){
			ch = word.substr(i,1).toLowerCase();
			if(dictionary[ch] != null){
				new_str += dictionary[ch];
			}else{
				new_str += ch;
			}
		}

		return new_str;
		
	},
	
	makeSlug: function(fields){
		var slug = '';
		var val = '';
		var inp = null;
		for(var i=0; i<fields.length; i++){
			inp = $('*[name="' + fields[i] + '"]');
			if(inp.length){
				val = inp.is('select') ? inp.find('option:selected').text() : inp.val();
			}
			if(val != ''){
				slug += (slug != '' ? ' ' : '') + val;
			}
		}
		return iUtils.translit(slug);
	}
	
}

iUtils.loader.prototype = {
	show: function(){
			$(this.obj).append(
				'<div class="loader" style="width:' + $(this.obj).outerWidth() + 'px; height:' + $(this.obj).outerHeight() + 'px;"><span></span></div>'
			).css({'position': 'relative'});
			if(this.control_item && this.control_text){
				this.control_item.data('orig-text', this.control_item.html()).text(this.control_text);
			}
	},		
	remove: function(){
		$(this.obj).find('.loader').remove();
		if(this.control_item){
			this.control_item.html(this.control_item.data('orig-text'));
		}
	}
}

iUtils.instantFilter.prototype = {
	
	init: function(){
		
		var self = this;
		
		self.container = this.params.container;
		self.error_container = $('.e-appointment__search__error')
		self.input = self.params.searchField ? self.params.searchField : $(self.container).find('input');
		self.items = $(self.container).find('li');
		
		self.container.find('ul').each(function(){
			
			$(this).data('items-num', $(this).find('li').length);
			
		});
		
		self.input.on('keyup', function(){
			
			var str = $(this).val();
			
			if(str.length == 0){
				
				self.items.show();
				self.container.find('div').show();
				
				self.container.each(function(){
					if($(this).data('hide-no-search') == 'true'){
						$(this).hide();
					}
				});
				
				self.error_container.hide();
				
			}else{
			
				self.items.each(function(){

					var text = $(this).find('a').text();
					
					if(!$(this).data('parent-element')){
						$(this).data('parent-element', $(this).parent().parent());
					}
					if(!$(this).data('container')){
						$(this).data('container', $(this).parents('.e-appointment__section').eq(0));
					}
					var parent_element = $(this).data('parent-element');
					//var title_element = $(this).data('title-element');
					var container = $(this).data('container');

					if(text.toLowerCase().indexOf(str.toLowerCase()) == -1){
						
						$(this).hide();
						
						if(parent_element && !parent_element.find('li:visible').length){
							parent_element.hide();
						}
						
						if(!container.find('li:visible').length){
							container.hide();
						}
						
					}else{
						
						$(this).show();
						
						if(parent_element)
							parent_element.show();
						
						/*
						if(title_element)
							title_element.show();
						*/
						
						container.show();
						
						container.parent().show();
					}

				});
				
				if(!self.items.filter(':visible').length){
					
					self.error_container.show();
					
				}else{
					
					self.error_container.hide();					
					
				}
			
			}
			
		});
		
	}
	
}

var popupUtils = {
	
	callbacks: function(){
		
		var startWindowScroll;
		var self = this;
		
		return{
			parseAjax: function(mfpResponse){
				mfpResponse.data = '<div class="popup">' + mfpResponse.data + '</div>';
			},
			ajaxContentAdded: function(){
				self.initUI();
			},
			afterChange: function(){
				//self.initCenter();
			},
			open: function() {
				
				self.initUI();
				
			},
			beforeOpen: function(){
				startWindowScroll = $(window).scrollTop();
			},
			close: function() {
				
				// blur off
				$('.wrap').removeClass('blurred-body');
				
				// fix ios scrolling
				$('body').css({overflow: 'auto', marginTop: 0, height: 'auto'});
				$(window).scrollTop(startWindowScroll);
				
			}
		}
	},
	
	initUI: function(){
		$('.popup .toggle').iToggle();
		
		$.each($('.popup form'), function(){	
		
			var item = $(this);

			new iForm({form: item});

		});
		
	},
	
	centerContent: function(){
		
		if(!window.matchMedia('(max-width: 768px)').matches){
			return false;
		}
		
		if($('.b-popup_box__body').length){
		
			var window_height = $(window).height();
			var content_height = $('.b-popup_box__body').height();

			var pad = 0;

			if(content_height < window_height){
				pad = (window_height - content_height)/2;
			}
			
			var is_nav_mobile = $('.b-nav-mobile').length && $('.b-nav-mobile').data('role') == 'toggle-stack';

			if(pad > 60){
				$('.b-popup_box__body').animate({paddingTop: pad}, is_nav_mobile ? 500 : 0);
			}else{
				$('.b-popup_box__body').animate({paddingTop: 60}, is_nav_mobile ? 500 : 0);
			}
		
		}
		
	},
	
	initCenter: function(){
		
		var self = this;
		
		self.centerContent();
		$('.b-popup_box__body').find('.b-toggle').on('toggled', function(){
			self.centerContent();
		});
		
	}
	
}