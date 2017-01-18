<form action="<?=$PAGE->getUrl()?>/" name="forgot-form" method="post" id="forgot-form">

	<?if($response['error']){?>
		<div class="system_messages">
			<div><?=$response['error']?></div>
		</div>
	<?}?>
		
	<div class="form">
		<div class="form__item">
			<label for="auth-form-phone" class="form__item__title">Ваш номер телефона</label>
			<div class="form__item__input">
				<input type="text" name="phone" class="f-field" id="auth-form-phone" value="<?=(isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '')?>" data-required="true" data-type="phone" data-title="Телефон">
			</div>
		</div>
		<div class="form__item">
			<label class="form__item__title">&nbsp;</label>
			<div class="form__item__input">
				<button type="submit" class="btn">Восстановить пароль</button>
			</div>
		</div>
	</div>
				
</form>
