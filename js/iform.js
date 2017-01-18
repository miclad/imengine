function iForm(params){
	this.errors = '';
	this.form = params.form;
	if(!this.form.data('initialized'))
		this.init();
}

iForm.prototype = {
	
	init: function(){
		
		var oThis = this;
		
		this.initFields();
	
		this.form.data('initialized', true);
		
		if(this.file_fields.length > 0){
			this.form.data('ajax', false);
		}
		
		this.form.submit(function(){
			
			oThis.fields.removeClass('error');
			
			if(oThis.validate()){
				if(oThis.form.data('ajax')){
					oThis.submit();
				}else{
					return true;
				}
			}else{
				
				oThis.moveToTop();
				
			}
			
			return false;
			
		});
		
		this.form.find('.captcha-reload').on('click', function(evt){
			
			oThis.reloadCaptcha();
			
			evt.preventDefault();
			
		});
		
		this.form.find('.select-toggle').on('change', function(){
			
			var cnt = $(this).parents('.form-item').next();
			if($(this).val() == 1){
				cnt.show();
			}else{
				cnt.hide();
			}				
			
		});
		
		$('input[data-type="phone"]').mask('+7 (999) 999-99-99', {placeholder:'_'});
		
	},
	
	initFields: function(){
		
		this.fields = $(this.form).find('input,textarea,select');
		this.file_fields = $(this.form).find('input[type="file"]');
		
		this.fields.focus(function(){
			$(this).removeClass('error');
			$(this).siblings('.error-message').fadeOut();
		});
		
	},
	
	validate: function(){
		
		var oThis = this;
		var has_errors = false;
		var error_str = '';
		
		this.initFields();
		
		$(oThis.fields).each(function(){
			
			if(!$(this).parents(':hidden').length){
				
				error_str = '';
				
				if($(this).attr('type') == 'file'){					
					
					var files = $(this).get(0).files;
					
					if(files.length){
						
						var max_size = $(this).data('max-filesize');
						if(max_size != '' && parseFloat(max_size) > 0){
							
							max_size = parseFloat(max_size);
						
							for(var i=0; i<files.length; i++){
								var fsize = files[i]['size']/1048000;
								if(fsize > max_size){
									
									has_errors = true;
									error_str = 'Превышен максимально допустимый размер файла';
									$(this).addClass('error');
									if(error_str){
										oThis.showError(error_str, $(this));
									}
									
									break;
									
								}
							}
							
						}
						
					}else{
						if($(this).data('required')){
							$(this).addClass('error');
							has_errors = true;
						}
					}
					
				}else{
				
					var value = $(this).val();

					if($(this).data('required') && value.replace(/\s|\t/g, '') == ''){
						$(this).addClass('error');
						has_errors = true;
						if($(this).data('error-text-empty')){
							error_str = $(this).data('error-text-empty');
						}
					}else{

						if(value.replace(/\s|\t/g, '') != ''){						

							var type = $(this).data('type');
							if(type){		
								var error = 0;
								switch(type){
									case 'numeric':
										if(!value.match(/^[0-9]+$/)){
											error = 1;
										}
									break;
									case 'email':
										if(!value.match(/^[a-zA-Z0-9._-]+@(?:[a-zA-z0-9-]+\.)+[a-zA-Z]{1,10}$/i)){										
											error = 1;
										}
									break;
								}	
								if(error){
									has_errors = true;
									$(this).addClass('error');
									if(!error_str){
										error_str = 'Некорректные данные';
									}
									
								}
							}

						}

					}
					
				}
				
				if(error_str){
					console.log($(this).attr('name') + ' ' + error_str);
					oThis.showError(error_str, $(this));
				}else{
					oThis.hideError($(this));
				}
			
			}
			
		});
		
		return !has_errors;
		
	},
	
	submit: function(){
		
		var oThis = this;
		var post_data = {};
		
		$(oThis.fields).each(function(){
			
			if($(this).attr('type') != 'radio' || $(this).is(':checked'))
				post_data[$(this).attr('name')] = $(this).val();
			
		});
		
		post_data['_return_json'] = true;
		
		post_data = $(oThis.form).serialize() + '&_return_json=1';
		
		var lo = new Loader(this.form, this.form.find('button[type="submit"]'), 'Подождите...');
		
		$.post(
			this.form.attr('action'),
			post_data,
			function(rsp){
				
				lo.remove();
				
				var err_cnt = $(oThis.form).find('.form-success.error');
				
				if(rsp.error != null && rsp.error != ''){
					
					if(err_cnt.length)
						err_cnt.html(rsp.error);
					else{
						
						$(oThis.form).prepend('<div class="form-success error">' + rsp.error + '</div>');
						
					}						
					
				}else{
					
					if(err_cnt.length){
						err_cnt.hide();
					}
					
					var target = null;
					var tgt_s = $(oThis.form).data('result-target');
					if(tgt_s)
						target = $(tgt_s);
					else
						target = $(oThis.form);
					
					console.log(target);
					
					target.html('<div class="form-success">' + rsp.success + '</div>');
					
					oThis.form.trigger('formSentSuccess');
					
				}
				
			},
			'json'
		)
		
	},
	
	reloadCaptcha: function(){
		
		var oThis = this;
		
		$(oThis.form).find('.form__item__input--captcha').addClass('loading');
		
		var ms = new Date().getTime();
		this.form.find('.form__item__input--captcha img').attr('src', '/img/cpt.php?' + ms).load(function(){
			$(oThis.form).find('.captcha').removeClass('loading');
		});
		
	},
	
	moveToTop: function(){
		
		var error_fields = $(this.form).find('.error');
		if(error_fields.length){
			
			var field_top = error_fields.eq(0).parent().offset().top;
			var doc_top = $(document).scrollTop();
			
			if(field_top - 50 < doc_top){
				$('html,body').animate({scrollTop: field_top - 40});
			}
			
		}
		
	},
	
	showError: function(error, field){
		
		//return false;
		
		var err_cnt = field.parent().find('.error-message');
		if(false && !err_cnt.length){
			err_cnt = $('<div class="error-message"></div>');
			field.parent().append(err_cnt);
		}
		
		if(err_cnt.length)
			err_cnt.html(error).show();
			
		
	},
	
	hideError: function(field){
		
		var err_cnt = field.parent().find('.error-message');
		if(err_cnt.length){
			err_cnt.html('').hide();
		}
		
	}
	
	
}

function customizeForm(){
	this.init();
}

customizeForm.prototype = {
	
	init: function(){
		if($('#system').data('is-old-ie'))
			return false;
		this.checkbox();
	},
	
	checkbox: function(){
		
		var status_default = {checkbox: 'fa-square', radio: 'fa-circle-o'};
		var status_checked = {checkbox: 'fa-check-square', radio: 'fa-dot-circle-o'};
		
		$('label').find('input[type="checkbox"],input[type="radio"]').each(function(){
			
			if($(this).siblings('i').length > 0){
				return true;
			}
			
			var inp = $(this);
			
			var s_default = inp.data('state-default') ? 'fa-' + inp.data('state-default') : status_default[inp.attr('type')]; 
			var s_checked = inp.data('state-checked') ? 'fa-' + inp.data('state-checked') : status_checked[inp.attr('type')];
			
			inp.css({'position': 'absolute', 'left': -1000});			
			var sp = $('<i class="fa"></i>');
			inp.parent().prepend(sp);
			sp.addClass(inp.is(':checked') ? s_checked : s_default);
			
			inp.on('change', function(){
				
				if($(this).is(':checked'))
					$(this).siblings('i').removeClass(s_default).addClass(s_checked);
				else
					$(this).siblings('i').removeClass(s_checked).addClass(s_default);
				
				$(this).attr('type');
				
				if($(this).attr('type') == 'radio'){
					var others = $(this).parents('form').find('input[name="' + $(this).attr('name') + '"]').not($(this));
					others.each(function(){
						var s_default = $(this).data('state-default') ? 'fa-' + $(this).data('state-default') : status_default['radio']; 
						var s_checked = $(this).data('state-checked') ? 'fa-' + $(this).data('state-checked') : status_checked['radio'];
						$(this).siblings('i').removeClass(s_checked).addClass(s_default);
					});
				}
				
			});
			
		});
		
		
	}
	
}

// async file upload

$(document).ready(function(){
	
	var files = $('input[type="file"]');
	files.each(function(){		
		if($(this).data('async')){		
			var f_upload = new FileUpload($(this));			
		}		
	});
	
	$('body').on('click', '.form-item_uploaded-photo a', function(evt){
		
		if(false && !confirm('Удалить фотографию?')){
			return false;
		}
		
		var cnt = $(this).parents('.form-item_uploaded-photo');
		var inp = cnt.siblings('label').find('input');
		
		var url = $(inp).data('remove-url');
		var value = $(inp).data('remove-value');
		if(!value){
			value = $(this).data('id');
		}
		
		var lo = new Loader(cnt);
		
		var params = {}
		params[$(inp).data('remove-param')] = value;
		
		var form = $(this).parents('form');
		
		if(form.find('input[name="temp_hash"]').length){
			params['temp_hash'] = form.find('input[name="temp_hash"]').val();
		}
		
		$.post(
			url,
			params,
			function(rsp){
				lo.remove();
				cnt.remove();
			}						
		)
		
		evt.preventDefault();
		
	});
	
});

function FileUpload(input){
	
	this.input = input;
	this.init();
	
	this.size_limit = 5;
	
}

FileUpload.prototype = {
	
	init: function(){
		
		var self = this;
		
		this.form = $(this.input).parents('form');
		this.input_cnt = $(this.input).parents('label');		
		this.is_multiple = $(this.input).attr('multiple');
		this.url = this.form.attr('action');
		this.file_name = $(this.input).attr('name');
		this.load_counter = 0;
		this.files_counter = 0;
		this.temp_hash = '';
		if(this.form.find('input[name="temp_hash"]').length){
			this.temp_hash = this.form.find('input[name="temp_hash"]').val();
		}
		
		$(this.input).on('change', function(){
			
			self.loader = new Loader(self.input_cnt);
			self.load_counter = 0;
			
			var errors = new Array();
				
			var files = $(this).get(0).files;
			self.files_counter = files.length;
			for(var i=0; i<files.length; i++){
				var fsize = files[i]['size']/1048000;
				var error = '';
				if(fsize <= self.size_limit){
					if(files[i]['type'] == 'image/jpeg' || files[i]['type'] == 'image/gif' || files[i]['type'] == 'image/png'){
						self.upload(files[i]);
					}else{
						error = 'неверный формат';
					}
				}else{
					error = 'превышен допустимый размер';
				}
				
				if(error != ''){
					self.files_counter--;
					errors[errors.length] = files[i]['name'] + ' - ' + error;
				}
			}
			
			if(errors.length){
				self.showErrors(errors);
			}
			
			$(this).val('');
			
			if(self.files_counter < 1)
				self.loader.remove();
			
		});
		
	},
	
	showErrors: function(errors){
		var str = 'Ошибки при загрузке файлов:\n';
		for(var i=0; i<errors.length; i++){
			if(str != '')
				str += '\n';
			str += errors[i];
		}					
		alert(str);
	},
	
	upload: function(file){
		
		var self = this;
		
		var xhr = this.getTransport();
					
		xhr.open('post', this.url, true);
				
		var formData = new FormData();
		formData.append('async_file_upload', '1');
		formData.append('temp_hash', this.temp_hash);
				
		formData.append(this.file_name, file);				
				
		xhr.send(formData);	
		
		xhr.onreadystatechange = function(){self.success(xhr)};
		
	},
	
	success: function(xhr){
		
		if (xhr.readyState == 4){
			if(xhr.status == 200){
				
				this.load_counter++;
				if(this.load_counter == this.files_counter)
					this.loader.remove();
				
				var rsp = $.parseJSON(xhr.responseText);
				
				var cnt = '<div class="form-item_uploaded-photo">';
				cnt += '<img src="' + rsp.image + '">';
				cnt += '<div class="photo-edit"><a href="#" data-id="' + rsp.id + '"><i class="fa fa-remove"></i></a></div>'
				cnt += '</div>';
							
				if(!this.is_multiple){
					this.input_cnt.siblings('.form-item_uploaded-photo').remove();
				}
				this.input_cnt.parent().append(cnt);
			
			}
		}
		
	},
	
	getTransport: function(){
		
		var xmlhttp;
		try {
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (E) {
				xmlhttp = false;
			}
		}
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		return xmlhttp;
		
	}
	
}