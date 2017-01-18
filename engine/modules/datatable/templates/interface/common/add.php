<?
$ta=1;
$action = is_numeric($dt->ent->getRowValue("id")) ? 'update' : 'add';
?>
<form class="form form--hover" role="form" action="<?=$dt->baseUrl?>&act=<?if(is_numeric($dt->ent->getRowValue("id"))){?>update&id=<?=$dt->ent->getRowValue("id")?><?}else{?>add<?}?><?if($_GET["show"]=="folder_form")echo "&sp=folder"?>&show=<?=$_GET["show"]?><?=isset($_GET['page']) ? '&page='.intval($_GET['page']) : ''?>" method="post" enctype="multipart/form-data">

	<?if(is_numeric($dt->ent->getRowValue("id"))){?>
		<input type="hidden" name="id" value="<?=$dt->ent->getRowValue("id")?>"/>
	<?}?>
	
	<?if($dt->ent->foreignField != ''){?>
		<input type="hidden" name="<?=$dt->ent->foreignField?>" value="<?=$dt->ent->foreignValue?>"/>
	<?}?>
	
	<?if(isset($extra_foreign)){?>
		<input type="hidden" name="<?=$extra_foreign[0]?>" value="<?=$extra_foreign[1]?>"/>
	<?}?>
		
	<?
	$fields = $dt->ent->getFormFields();
	?>

	<?foreach($fields as $k => $f){?>
		
		<?
		$field_template = B_DIR.'/engine/modules/datatable/templates/interface/'.$dt->entity.'/field_'.$k.'.php';
		if(file_exists($field_template)){
			require $field_template;
		}else{
		?>
		
			<?
			if(!isset($_GET['show']) || $_GET['show'] == 'element_form'){
				$append = B_DIR.'/engine/modules/datatable/templates/interface/'.$dt->entity.'/before_field_'.$k.'.php';
				if(file_exists($append))
					include $append;
			}
			?>
	
			<div class="form__item<?if($f['type']){?> form__item--type-<?=$f['type']?><?}?>" id="field_<?=$k?>">
				<label for="formField<?=$k?>" class="form__item__title"><?=$f['title']?></label>
				<div class="form__item__input">
					<?=$f['html']?>
				</div>
			</div>
		
			<?
			if(!isset($_GET['show']) || $_GET['show'] == 'element_form'){
				$append = B_DIR.'/engine/modules/datatable/templates/interface/'.$dt->entity.'/after_field_'.$k.'.php';
				if(file_exists($append))
					include $append;
			}
			?>
		
		<?}?>
		
	<?}?>
		
	<?

	$after_last_field = B_DIR.'/engine/modules/datatable/templates/interface/'.$dt->entity.'/after_last_field.php';
	if(file_exists($after_last_field))
		include $after_last_field;

	?>
	
	<?
	if($dt->withFolders){
		$tree = new Tree("folders", "entity='".$dt->entity."'".($dt->foreignValue ? ' and entity_foreign_id='.intval($dt->foreignValue) : '').($dt->ent->entity == "folders" && $dt->ent->getRowValue("id")?" and id!=".$dt->ent->getRowValue("id"):""));
		$folders = $tree->getFullTree();
		$c_f = $dt->ent->entity == "folders"?$dt->ent->getRowValue("parent"):$dt->ent->getRowValue("folder_id");
		if(!$c_f && isset($_GET["folder"]))
			$c_f = $_GET["folder"];
	?>
		<div class="form__item" id="field_folder_id">
			<label for="formFieldfolder_id" class="form__item__title">Категория</label>
			<div class="form__item__input">
				<select name="folder_id" class="form-control"  id="formFieldfolder_id">
					<option value="0">- корень -</option>
					<?foreach($folders as $v){?>
						<option value="<?=$v["id"]?>"<?if($c_f == $v["id"]){?> selected<?}?>><?for($i=-1;$i<$v["level"];$i++){?>--<?}?> <?=$v["name"]?></option>
					<?}?>
				</select>
			</div>
		</div>
	<?}?>

	<?if($dt->ent->isMultilang){?>
		<?
		$langs = DBCommon::getFromBase('*', 'langs', '1', 'pos');
		$ml_ob = $dt->ent->getMultilangObject();
		?>
		<?foreach($langs as $l){?>
			<?
			$ml_ob->clearCurRow();
			$ml_ob->setFieldNameTemplate('langs['.$l['name'].'][{field_name}]');
			if(isset($_POST['langs'][$l['name']])){
				$ml_ob->setPostArray($_POST['langs'][$l['name']]);	
			}
			if($dt->ent->getRowValue('id')){
				$ml_ob->initCurRow('lang='.$l['id'].' and entry_id='.$dt->ent->getRowValue('id'));
			}
				
			?>
			<?if($ml_ob->getRowValue('id')){?>
				<input type="hidden" name="langs[<?=$l['name']?>][id]" value="<?=$ml_ob->getRowValue('id')?>"/>
			<?}?>
			<div class="lang_tab"><?=$l['title']?></div>
			<table class="dataform">
				<?foreach($ml_ob->getFormFields() as $f){?>
					<tr>
						<td class="ttl"><?=$f['title']?></td>
						<td>
							<?=$f['html']?>
						</td>
					</tr>	
				<?}?>
			</table>
		<?}?>
	<?}?>

	<?if(AuthUser::getInstance()->hasAccess('datatable_'.$check_access_entity, 'w')){?>
			
		<div class="form__submit">
			
			<button type="submit" class="btn btn--large">
				<span class="fa fa-save"></span> <?if(true || is_numeric($dt->ent->getRowValue("id"))){?>Сохранить<?}else{?>Добавить<?}?>
			</button>
				
			<div class="form__back-to-edit">
				<div class="checkbox">
					<label>
						<input type="hidden" name="__back_to_edit" value="0"/>
						<input type="checkbox" name="__back_to_edit" value="1"<?if(isset($_COOKIE['admin_'.($dt->ent->entity != $dt->entity ? $dt->ent->entity.'_' : '').$dt->entity.'_'.$action.'_back_to_edit']) && (!isset($_POST['__back_to_edit'])) || isset($_POST['__back_to_edit']) && $_POST['__back_to_edit'] == 1){?> checked<?}?>>
						и продолжить редактирование
					</label>
				</div>
			</div>
			
		</div>

	<?}?>
	
	<?if(isset($dt->dataDesc['preview_link'])){?>
		<button type="submit" class="btn" onclick="doPreview('<?=$dt->dataDesc['preview_link']?>');">Предпросмотр</button>
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

<?

$after_form = B_DIR.'/engine/modules/datatable/templates/interface/'.$dt->entity.'/after_form.php';
if(file_exists($after_form))
	include $after_form;

include  B_DIR.'/engine/modules/datatable/templates/interface/common/scripts.php';

?>