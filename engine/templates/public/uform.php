<?if($this->haveErrors()){?>
<div class="form-success error"><?=$this->getErrors()?></div>
<?}?>

<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" id="form<?=$this->formInfo["id"]?>" enctype="multipart/form-data"<?if(!isset($params['no_ajax'])){?> data-ajax="true"<?}?>>

	<input type="hidden" name="_send"/>
	<input type="hidden" name="_referer" value="<?=Site::isAjaxRequest() ? urlencode($_SERVER['HTTP_REFERER']) : (isset($_POST['_referer']) && $_POST['_referer'] != '' ? $_POST['_referer'] : '')?>">
	
	<div class="form">
	
		<?foreach($this->formDesc as $v){?>
			<div class="form__item">
		
				<div class="form__item__title">
					<?=$v["title"]?> <?if($v['req']){?><span class="field-required">*</span><?}?>
				</div>
					
				<div class="form__item__input">
						
							<?
							if(!isset($v['input_type']) || $v['input_type'] == 'simple')
								$v['input_type'] = 'field';
							?>
							<?if($v['input_type'] == 'field'){?>
								<input <?if(isset($v['placeholder'])){?> placeholder="<?=$v['placeholder']?>"<?}?> <?if($v['req']){?> data-required="true"<?}?> data-type="<?=$v['datatype'] == 'mail' ? 'email' : $v['datatype']?>" type="<?=($v["datatype"]=="password"?"password":"text")?>" name="<?=$v["name"]?>" value="<?=$v["datatype"]!="password"?$this->getValue($v["name"]):""?>"/>
							<?}elseif($v['input_type'] == 'textarea'){?>
								<textarea <?if(isset($v['placeholder'])){?> placeholder="<?=$v['placeholder']?>"<?}?> <?if($v['req']){?> data-required="true"<?}?> name="<?=$v["name"]?>"><?=$this->getValue($v["name"])?></textarea>
							<?}elseif($v['input_type'] == 'select' && isset($v['values']) && is_array($v['values'])){?>
								<select <?if($v['req']){?> data-required="true"<?}?> name="<?=$v['name']?>">
									<option value="">--</option>
									<?foreach($v['values'] as $o){?>
									<option value="<?=$o['id']?>"<?if($o['id'] == $this->getValue($v["name"])){?> selected<?}?>><?=$o['name']?></option>
									<?}?>
								</select>
							<?}elseif($v['input_type'] == 'file'){?>
								<input type="file" name="<?=$v['name']?>"/>
							<?}?>
							
				</div>
				
			</div>
			
			<?if($v["datatype"]=="password"){?>
				
				<div class="form__item">
				
					<div class="form__item__title">
						Повторите пароль <span class="field-required">*</span>
					</div>
					<div class="form__item__input">
						<input type="password" name="<?=$v["name"]?>_repeat" data-required="true"/>
					</div>
					
				</div>
					
			<?}?>
			
		<?}?>
		
		<?if($this->useCaptcha){?>
		
			<div class="form__item">
		
				<div class="form__item__title">
					Код с картинки <span class="field-required">*</span>	 	
				</div>
				
				<div class="form__item__input form__item__input--captcha">				
					<input type="text" name="captcha_word" style="width:82px;" data-required="true"/>
					<img src="/img/cpt.php" class="captcha"/>
					<div>
						<span class="captcha-reload fa fa-repeat"></span>
					</div>
				</div>
			
			</div>
			
		<?}?>
		
		<div class="form__item form__item--submit">		
			<div class="form__item__title">&nbsp;</div>
			<div class="form__item__input">
				<button type="submit" class="btn"><?=$submit_text?></button>
			</div>
		</div>
		
	</div>
		
</form>

<?if(Site::isAjaxRequest()){?>
	<script type="text/javascript">
		var form<?=$this->formInfo["id"]?> = new iForm({form: $('#form<?=$this->formInfo["id"]?>')});
	</script>
<?}?>