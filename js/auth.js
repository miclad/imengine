$(document).ready(function(){
	
	$('#auth_link').click(function(){
		
		var auth_popup = $('#auth_popup');
		
		if(!auth_popup.is(':visible')){
			$('#login').show();
			$('#registration').hide();
			auth_popup.find('.errors').hide();
		}
		auth_popup.show();
		
		return false;
	
	});
	
});

var Auth = {
		
	'initForm': function(){
		
		$('#login_form').submit(function(){
			
			var login = $(this).find('input[name="login"]');
			var pass = $(this).find('input[name="password"]');
			
			var check = simpleFormCheck(this);
			if(check){
				
				var lo = new loader('#login');
				
				var params = {'login': login.val(), 'password': pass.val()};
				if($('input[name="dont_remember"]').is(':checked')){
					params['dont_remember'] = 1;
				}
				
				$.post(
					'/login/',
					params,
					function(rsp){					
						
						if(rsp != ''){						
							lo.remove();
							$('#login').find('.errors').html(rsp).show();
						}else{
							$('#login').find('.errors').html('').hide();
							location.href = '/';
						}					
						
					}
				);
				
			}
				
			return false;
				
			
		});
		
		$('#reg_form').submit(function(){
			
			var login = $(this).find('input[name="login"]');
			var pass = $(this).find('input[name="password"]');
			var cpt = $(this).find('input[name="captcha_word"]');
			
			var check = simpleFormCheck(this);
			if(check){
				
				var lo = new loader('#registration');
				
				$.post(
					'/registration/',
					{
						'login': login.val(), 
						'password': pass.val(), 
						'captcha_word': cpt.val(), 
						'name': $('input[name="name"]').val(),
						'surname': $('input[name="surname"]').val(),
						'phone': $('input[name="phone"]').val(),
						'agree': $(this).find('input[name="agree"]:checked').length
					},
					function(rsp){	
						
						if(rsp.error != ''){
							lo.remove();
							$('#registration').find('.errors').html(rsp.error).show();
						}else{
						
							if(rsp.success != ''){
								$('.auth_popup').find('.tabs').hide();
								$('#registration').find('.errors').html('').hide();
								$('#registration').html('<div class="success">' + rsp.success + '</div>');
							}else{
								location.reload();
							}
						
						}			
						
					},
					'json'
				);
				
			}
				
			return false;
				
			
		});
		
		$('.auth_popup').find('.tabs a').click(function(){
			if(!$(this).hasClass('active')){
				$(this).addClass('active').siblings('a').removeClass('active');
				$('#' + $(this).data('target')).show().siblings().hide();
			}
			return false;
		});
		
	},
	
	'authVK': function(response){
	
		var error_message = 'Произошла ошибка, повторите попытку позже';
		
		if(response.session){
			
			VK.api(
				'getProfiles', 
				{uids:response.session.user.id, fields: "uid, first_name, last_name, nickname, screen_name, sex, bdate (birthdate), city, country, timezone, photo, photo_medium, photo_big, has_mobile, rate, contacts, education"}, 
				function(data) {
					
					var data = {
						"auth_service_id": vk_id,
						"vk_session": response.session,
						"user_data": data.response[0]
					};
					
					$.post(
						'/login/?a=vk',
						data,
						function(rsp){
							if(rsp == 1){							
								window.location.reload();
							}else{
								alert('Произошла ошибка, повторите попытку позже');
							}
						}
					);
					
				}			
			);
			
		}else{
			alert(error_message);
		}
		
	}
	
}