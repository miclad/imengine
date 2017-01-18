<div class="form">			

		<form action="/login/" name="auth-form" method="post" id="auth-form">
				
				<?if($page_errors){?>
					<div class="page__alert">
						<ul>
							<?foreach($page_errors as $v){?>
								<li><?=$v?></li>
							<?}?>
						</ul>
					</div>
				<?}?>
				
				<div class="form__item">
					<label for="auth-form-login" class="form__item__title">Телефон (логин)</label>
					<div class="form__item__input">
						<input type="text" name="login" class="f-field" id="auth-form-login" value="<?=(isset($_POST['login']) ? htmlspecialchars($_POST['login']) : '')?>" data-required="true" data-type="phone" data-title="Телефон">
					</div>
				</div>
				
				<div class="form__item">
					<label for="auth-form-password" class="form__item__title">Пароль</label>
					<div class="form__item__input">
						<input type="password" name="password" class="f-field" id="auth-form-password" value="<?=(isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '')?>" data-required="true" data-type="text" data-title="Пароль">
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
				
				<div class="form__item">
					<label class="form__item__title">&nbsp;</label>
					<div class="form__item__input">
						<div class="row row--cellspace">
							<div class="col-4">
								<button type="submit" class="btn">Войти</button>
							</div>
							<div class="col-7">
								<a href="/registration/">Зарегистрироваться</a><br>
								<a href="/password_recovery/">Забыли пароль?</a>
							</div>
						</div>
					</div>
				</div>		
				
			</form>
	
</div>