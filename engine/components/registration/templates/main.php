<div class="row cart-order registration">

	<div class="col-8">
	<div class="content-box content-box--blue">

		<form action="" class="form" id="reg-form" method="post">
	
			
				<?if($page_errors){?>
					<div class="page__alert">
						<ul>
							<?foreach($page_errors as $v){?>
								<li><?=$v?></li>
							<?}?>
						</ul>
					</div>
				<?}?>
		
			<div class="form__item ">
				<div class="row">					
					<div class="col-6">
						<label for="account-field-name" class="form__item__title">Ваше имя <span class="req">*</span></label>
						<div class="form__item__input">
							<input type="text" name="name" class="f-field" id="account-field-surname" data-type="text" data-required="true" data-title="Имя" value="<?=$dt_ob->getRowValue('name')?>">
						</div>
					</div>
					<div class="col-6">
						<label for="account-field-surname" class="form__item__title">Фамилия</label>
						<div class="form__item__input">
							<input type="text" name="surname" class="f-field" id="account-field-surname" data-type="text" data-required="false" data-title="Фамилия" value="<?=$dt_ob->getRowValue('surname')?>">
						</div>
					</div>
				</div>
			</div>			
			<div class="form__item">
				<label for="account-field-email" class="form__item__title">E-mail <span class="req">*</span></label>
				<div class="form__item__input">
					<input type="text" name="email" class="f-field" id="account-field-email" data-type="email" data-required="true" data-title="E-mail" value="<?=$dt_ob->getRowValue('email')?>">
				</div>
			</div>
			<div class="form__item">
				<label for="account-field-login" class="form__item__title">Телефон <span class="req">*</span></label>
				<div class="form__item__input">
					<input type="text" name="login" class="f-field" id="account-field-login"<?if(true || !$USER->isAdmin()){?> data-type="phone"<?}?> data-required="true" data-title="Телефон" value="<?=$dt_ob->getRowValue('login')?>">
				</div>
				<div class="form__item__error"></div>
			</div>
			
			<div class="form__item ">
				<label for="account-field-password" class="form__item__title">Придумайте пароль <span class="req">*</span></label>
				<div class="form__item__input">
					<input type="password" name="password" class="f-field" id="account-field-password" data-type="text" data-required="true" data-title="Фамилия" value="<?=$dt_ob->getRowValue('password')?>">
				</div>
			</div>
	
			<div class="form__item">
		
				<div class="form__item__title">
					Код с картинки <span class="field-required">*</span>	 	
				</div>
				
				<div class="form__item__input form__item__input--captcha">				
					<input type="text" name="captcha_word" style="width:82px;" data-required="true"/>
					<img src="/img/cpt.php" class="captcha"/>
					<span class="captcha-reload"><i class="fa fa-repeat"></i></span>
				</div>
			
			</div>
	
			<div class="form__item">
				<div class="form__item__title">&nbsp;</div>
				<div class="form__item__input">
					<button type="submit" class="btn">Зарегистрироваться</button>
				</div>
			</div>	
	
		</form>
		
	</div>
	</div>
	
</div>