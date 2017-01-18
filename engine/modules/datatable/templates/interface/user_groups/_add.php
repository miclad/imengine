<?
$ta=1;
?>
<form class="form" role="form" action="<?=$dt->baseUrl?>&act=<?if(is_numeric($dt->ent->getRowValue("id"))){?>update&id=<?=$dt->ent->getRowValue("id")?><?}else{?>add<?}?><?if($_GET["show"]=="folder_form")echo "&sp=folder"?>&show=<?=$_GET["show"]?>" method="post" enctype="multipart/form-data">

	<?if(is_numeric($dt->ent->getRowValue("id"))){?>
		<input type="hidden" name="id" value="<?=$dt->ent->getRowValue("id")?>"/>
	<?}?>
	
	<?if($dt->ent->foreignField != ''){?>
		<input type="hidden" name="<?=$dt->ent->foreignField?>" value="<?=$dt->ent->foreignValue?>"/>
	<?}?>
	
	<?if(isset($extra_foreign)){?>
		<input type="hidden" name="<?=$extra_foreign[0]?>" value="<?=$extra_foreign[1]?>"/>
	<?}?>

	<?foreach($dt->ent->getFormFields() as $k => $f){?>
		<div class="form__item" id="field_<?=$k?>">
			<label for="formField<?=$k?>" class="form__item__title"><?=$f['title']?></label>
			<div class="form__item__input">
				<?=$f['html']?>
			</div>
		</div>
	<?}?>
	
	<div id="access_rules"<?if(!$dt->getRowValue('admin_panel_access')){?> style="display: none;"<?}?>>
		<?foreach($access_rules as $v){?>
			<div class="form__item">
				<label for="formFieldaccess_<?=$v['name']?>" class="form__item__title"><?=$v['title']?></label>
				<div class="form__item__input">
					<select name="access[<?=$v['name']?>][]" id="formFieldaccess_<?=$v['name']?>" class="form-control">
						<option value="">- нет доступа -</option>
						<?foreach($v['levels'] as $lk => $lv){?>
							<option value="<?=$lk?>"<?if(isset($selected_rules[$v['name']]) && strpos($selected_rules[$v['name']]['access'], $lk) !== false){?> selected<?}?>><?=$lv?></option>
						<?}?>
					</select>
				</div>
			</div>
		<?}?>
	</div>

	<?if(AuthUser::getInstance()->hasAccess('datatable_'.$check_access_entity, 'w')){?>
		<div class="form__submit">
			<button type="submit" class="btn btn--large">
				<span class="glyphicon glyphicon-ok"></span> <?if(is_numeric($dt->ent->getRowValue("id"))){?>Сохранить<?}else{?>Добавить<?}?>
			</button>
		</div>
	<?}?>

</form>

<?if(isset($dt->dataDesc[5])){?>
	<script type="text/javascript">
		<?$cnt = 0;?>
		<?foreach($dt->dataDesc[5] as $p=>$c){?>
		var mpar = new Array("master=<?=$p?>","ignore_id=<?=$dt->ent->getRowValue("id")?>");
		var dc<?=$cnt?> = new doubleCombo("<?=$p?>",document.forms[0].elements["<?=$c?>"],"/engine/ajax/getoptions.php",mpar,'<?=$dt->getRowValue($c)?>');
		dc<?=$cnt?>.masterComboChanged();
		<?$cnt++;}?>
	</script>
<?}?>

<?if(isset($dt->dataDesc['preview_link'])){?>
	<script type="text/javascript">
		function doPreview(action){		
			var frm = $('form');
			var old_act = frm.attr('action');
			frm.attr('action', action + '?_preview_mode=1').attr('target', '_blank').submit();
			frm.attr('action', old_act).attr('target', '_self');
		}
	</script>
<?}?>

