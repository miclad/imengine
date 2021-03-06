<?
$ta=1;
$action = is_numeric($dt->ent->getRowValue("id")) ? 'update' : 'add';
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
	
	
	<div class="form__item" id="field_catalog_folder">
		<label for="formFieldcatalog_folder_id" class="form__item__title col-md-2">Раздел</label>
		<div class="col-md-10">
			<?
			$sel = 0;
			if($dt->getRowValue('product2')){
				$res = $db->query('select folder_id from catalog where id='.$dt->getRowValue('product2'));
				if($t = $res->getNext())
					$sel = $t['folder_id'];
			}
			$tree = new Tree('folders', 'entity="catalog"');
			$folders = $tree->getFullTree(0, 1);
			?>
			<select name="catalog_folder_id" class="form-control">
				<option value="0">Корень</option>
				<?foreach($folders as $v){?>
				<option value="<?=$v['id']?>"<?if($v['id'] == $sel){?> selected<?}?>><?for($i=0; $i<$v['_tree_level']; $i++){?>--<?}?> <?=$v['name']?></option>
				<?}?>
			</select>
		</div>
	</div>
	
	<div class="form__item" id="field_product_id">
		<label for="formFieldproduct_id" class="form__item__title col-md-2">Товар</label>
		<div class="col-md-10">
			<select name="product2" class="form-control">
				<option value="">--</option>
			</select>
		</div>
	</div>

	<?if(AuthUser::getInstance()->hasAccess('datatable_'.$check_access_entity, 'w')){?>
		<div class="form-submit">
			
			<div class="col-md-offset-2 col-md-2">

				<button type="submit" class="btn btn-primary btn-lg">
					<span class="glyphicon glyphicon-ok"></span> <?if(true || is_numeric($dt->ent->getRowValue("id"))){?>Сохранить<?}else{?>Добавить<?}?>
				</button>
				
			</div>
				
			<div class="form--back_to_edit">
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
		<button type="submit" class="btn btn-primary" onclick="doPreview('<?=$dt->dataDesc['preview_link']?>');">Предпросмотр</button>
	<?}?>
	<div class="clear"></div>

</form>

<script type="text/javascript">
	var mpar = new Array("master=catalog_folder");
	var dc = new doubleCombo("catalog_folder_id", document.forms[0].elements["product2"], "/engine/ajax/getoptions.php", mpar, '<?=$dt->getRowValue('product2')?>');
	dc.masterComboChanged();
</script>