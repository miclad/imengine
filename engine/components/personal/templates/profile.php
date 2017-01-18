<div class="cart-order">
	
	<h2>Редактирование профиля</h2>
	
	<form action="" class="form" id="account-settings-form" method="post">
	
		<div class="content-box">
	
			<h3 style="margin-bottom: 20px;">Основная информация</h3>
		
			<input type="hidden" name="_save"/>
			<div class="form__item">
				<label for="account-field-email" class="form__item__title">Телефон (логин)</label>
				<div class="form__item__input">
					<input type="text" name="phone" class="f-field" id="account-field-phone"<?if(true || !$USER->isAdmin()){?> data-type="phone"<?}?> data-required="true" data-title="Телефон" value="<?=$dt_ob->getRowValue('login')?>">
				</div>
				<div class="form__item__error"></div>
			</div>
			<div class="form__item ">
				<label for="account-field-surname" class="form__item__title">Фамилия</label>
				<div class="form__item__input">
					<input type="text" name="surname" class="f-field" id="account-field-surname" data-type="text" data-required="false" data-title="Фамилия" value="<?=$dt_ob->getRowValue('surname')?>">
				</div>
			</div>
			<div class="form__item">
				<div class="row">
					<div class="col-6">
						<label for="account-field-name" class="form__item__title">Имя</label>
						<div class="form__item__input">
							<input type="text" name="name" class="f-field" id="account-field-surname" data-type="text" data-required="true" data-title="Имя" value="<?=$dt_ob->getRowValue('name')?>">
						</div>
					</div>
					<div class="col-6">
						<label for="account-field-p_name" class="form__item__title">Отчество</label>
						<div class="form__item__input">
							<input type="text" name="p_name" class="f-field" id="account-field-p_name" data-type="text" data-required="false" data-title="Отчество" value="<?=$dt_ob->getRowValue('p_name')?>">
						</div>
					</div>
				</div>
			</div> 
			<div class="form__item">
				<label for="account-field-email" class="form__item__title">E-mail</label>
				<div class="form__item__input">
					<input type="text" name="email" class="f-field" id="account-field-email" data-type="email" data-required="true" data-title="E-mail" value="<?=$dt_ob->getRowValue('email')?>">
				</div>
			</div>
			
			<p>&nbsp;</p>
			<p>
				<h3 style="margin-bottom: 20px;">Адрес доставки</h3>
			</p>
		
			<input type="hidden" name="_save"/>
			
			<div class="form__item">
				<div class="row">
					<div class="col-6">

						<div id="order-field__address_city">
							<div class="form__item__title">Город</div>
							<div class="form__item__input">
								<input type="text" name="address_city" value="<?=htmlspecialchars($dt_ob->getRowValue('address_city'))?>" class="inp"/>				
							</div>
						</div>
						<div></div>

					</div>
					<div class="col-6">

						<div id="order-field__address_post_index">
							<div class="form__item__title">Почтовый индекс</div>
							<div class="form__item__input">
								<input type="text" name="address_post_index" value="<?=htmlspecialchars($dt_ob->getRowValue('address_post_index'))?>" class="inp"/>				
							</div>
						</div>
						<div></div>

					</div>
				</div>
			</div>
			
			<div class="form__item ">
				<label for="account-field-address_region" class="form__item__title">Область</label>
				<div class="form__item__input">
					<input type="text" name="address_region" class="f-field" id="account-field-address_region" data-type="text" data-required="false" data-title="Область" value="<?=$dt_ob->getRowValue('address_region')?>">
				</div>
			</div>
			
			<div class="form__item" id="order-field__address">
				<div class="form__item__title">Адрес</div>	
				<div class="form__item__input">
					<div>
						<input placeholder="Улица" type="text" name="address_street" value="<?=htmlspecialchars($dt_ob->getRowValue('address_street'))?>"/>
					</div>
					<div class="row">
						<div class="col-4">
							<input placeholder="Номер дома" type="text" name="address_house" value="<?=htmlspecialchars($dt_ob->getRowValue('address_house'))?>"/>
						</div>
						<div class="col-4">
							<input placeholder="Корпус" type="text" name="address_corp" value="<?=htmlspecialchars($dt_ob->getRowValue('address_corp'))?>"/>
						</div>
						<div class="col-4">
							<input placeholder="Квартира/офис" type="text" name="address_flat" value="<?=htmlspecialchars($dt_ob->getRowValue('address_flat'))?>"/>
						</div>
					</div>
				</div>
			</div>
			
				
		
		</div>
		
		<div class="cart-submit">
			<button type="submit" class="btn">Сохранить</button>
		</div>
	
	</form>
	
	<?/*
	<form action="" class="form" id="account-settings-form-address" method="post">
	
		<div class="content-box">
	
			<h3 style="margin-bottom: 20px;">Адрес доставки</h3>
		
			<input type="hidden" name="_save"/>
			
			<div class="form__item">
				<div class="row">
					<div class="col-6">

						<div id="order-field__address_city">
							<div class="form__item__title">Город</div>
							<div class="form__item__input">
								<input type="text" name="address_city" value="<?=htmlspecialchars($dt_ob->getRowValue('address_city'))?>" class="inp"/>				
							</div>
						</div>
						<div></div>

					</div>
					<div class="col-6">

						<div id="order-field__address_post_index">
							<div class="form__item__title">Почтовый индекс</div>
							<div class="form__item__input">
								<input type="text" name="address_post_index" value="<?=htmlspecialchars($dt_ob->getRowValue('address_post_index'))?>" class="inp"/>				
							</div>
						</div>
						<div></div>

					</div>
				</div>
			</div>
			
			<div class="form__item" id="order-field__address">
				<div class="form__item__title">Адрес</div>	
				<div class="form__item__input">
					<div>
						<input placeholder="Улица" type="text" name="address_street" value="<?=htmlspecialchars($dt_ob->getRowValue('address_street'))?>"/>
					</div>
					<div class="row">
						<div class="col-4">
							<input placeholder="Номер дома" type="text" name="address_house" value="<?=htmlspecialchars($dt_ob->getRowValue('address_house'))?>"/>
						</div>
						<div class="col-4">
							<input placeholder="Корпус" type="text" name="address_corp" value="<?=htmlspecialchars($dt_ob->getRowValue('address_corp'))?>"/>
						</div>
						<div class="col-4">
							<input placeholder="Квартира/офис" type="text" name="address_flat" value="<?=htmlspecialchars($dt_ob->getRowValue('address_flat'))?>"/>
						</div>
					</div>
				</div>
			</div>
			<div class="form__item">
				<div class="form__item__title"></div>
				<div class="form__item__input" style="text-align: right;">
					<button type="submit" class="btn">Сохранить</button>
				</div>
			</div>
		
		</div>
	
	</form>
	*/?>
	
</div>