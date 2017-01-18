<form action="" class="form comments-form" id="reg-form" method="post">
	
			
			<?if(isset($page_errors) && $page_errors){?>
				<div class="page__alert">
					<ul>
						<?foreach($page_errors as $v){?>
							<li><?=$v?></li>
						<?}?>
					</ul>
				</div>
			<?}?>
	
			<div class="form__item">
				<label for="comment-form-comment" class="form__item__title">Ваш отзыв</label>
				<div class="form__item__input">
					<textarea name="comment" data-required="true" id="comment-form-comment" placeholder="Что вы думаете о товаре?"><?=$dt_ob->getRowValue('comment')?></textarea>
				</div>
			</div>
		
			<div class="form__item">
				<label for="comment-form-author_name" class="form__item__title">Представьтесь</label>
				<div class="form__item__input">
					<input type="text" name="author_name" class="f-field" id="comment-form-author_name" data-type="text" placeholder="Как вас зовут?" data-required="true" data-title="Имя" value="<?=$dt_ob->getRowValue('author_name')?>">
				</div>
			</div> 
			<div class="form__item">
				<label for="comment-form-author_city" class="form__item__title">Из какого вы города?</label>
				<div class="form__item__input">
					<input type="text" name="author_city" class="f-field" id="comment-form-author_city" data-type="text" placeholder="Город" data-required="false" data-title="Город" value="<?=$dt_ob->getRowValue('author_city')?>">
				</div>
			</div> 
			<div class="form__item ">
				<label for="comment-form-author_email" class="form__item__title">E-mail для связи с вами</label>
				<div class="form__item__input">
					<input type="text" name="author_email" class="f-field" id="comment-form-author_email" data-type="email" placeholder="Электронная почта" data-required="true" data-title="E-mail" value="<?=$dt_ob->getRowValue('author_email')?>">
				</div>
			</div>
	
			<?if(!$USER->isAuthorized()){?>
			<div class="form__item">
		
				<div class="form__item__title">
					Код с картинки <span class="field-required">*</span>	 	
				</div>
				
				<div class="form__item__input form__item__input--captcha">				
					<input type="text" name="captcha_word" style="width:82px;" data-required="true"/>
					<img src="/img/cpt.php" class="captcha"/>
				</div>
			
			</div>
			<?}?>
	
			<div class="form__item">
				<div class="form__item__title">&nbsp;</div>
				<div class="form__item__input">
					<button type="submit" class="btn">Отправить отзыв</button>
				</div>
			</div>	
	
	</form>