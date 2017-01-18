<div class="browse__popup login-popup">
	
	<div class="form">			

		<form action="/login/" name="auth-form-popup" data-ajax="true" method="post" id="auth-form-popup">
			
				<div class="login-popup__title">
					<h3>Вход</h3>
					<div class="login-popup__title__btn">
						<a href="/registration/" class="btn btn--med btn--bordered">Регистрация</a>
					</div>
				</div>
			
				<div class="form-success error"></div>
				
				<div class="form__item">
					<label for="auth-form-login" class="form__item__title">Логин</label>
					<div class="form__item__input">
						<input type="text" name="login" class="f-field" id="auth-form-popup-login" value="<?=(isset($_POST['login']) ? htmlspecialchars($_POST['login']) : '')?>" data-required="true" data-type="phone" data-title="Телефон">
					</div>
				</div>
				
				<div class="form__item">
					<label for="auth-form-password" class="form__item__title">Пароль</label>
					<div class="form__item__input">
						<input type="password" name="password" class="f-field" id="auth-form-popup-password" value="<?=(isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '')?>" data-required="true" data-type="text" data-title="Пароль">
					</div>
				</div>
			
				<div class="form__item">
					<label class="form__item__title">&nbsp;</label>
					<div class="form__item__input">
						<label>
							<input type="checkbox" name="remember" value="1">
							Запомнить меня
						</label>
					</div>
				</div>
			
				<div class="login-popup__foot">
					<div class="pull-left">
						<a href="/password_recovery/">Забыли пароль?</a>
					</div>
					<div class="pull-right">
						<button type="submit" class="btn btn--med">Войти</button>
					</div>
				</div>
				
		</form>
	
	</div>
	
</div>

<script type="text/javascript">
	$(document).ready(function(){
		
		$('#auth-form-popup').on('formSentSuccess', function(rsp){
			
			location.reload();
			
		});
		
	});
</script>