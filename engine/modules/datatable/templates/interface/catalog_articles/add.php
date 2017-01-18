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
	
	
	<div class="form__item" id="field_art_folder">
		<label for="formFieldarticle_folder_id" class="form__item__title col-md-2">Раздел</label>
		<div class="col-md-10">
			<?
			$sel = 0;
			if($dt->getRowValue('product_id')){
				$res = $db->query('select folder_id from articles where id='.$dt->getRowValue('article_id'));
				if($t = $res->getNext())
					$sel = $t['folder_id'];
			}
			$tree = new Tree('folders', 'entity="articles"');
			$folders = $tree->getFullTree(0, 1);
			?>
			<select name="article_folder_id" class="form-control">
				<option value="0">Корень</option>
				<?foreach($folders as $v){?>
					<option value="<?=$v['id']?>"<?if($v['id'] == $sel){?> selected<?}?>><?for($i=0; $i<$v['_tree_level']; $i++){?>--<?}?> <?=$v['name']?></option>
				<?}?>
			</select>
		</div>
	</div>
	
	<div class="form__item" id="field_article_id">
		<label for="formFieldarticle_id" class="form__item__title col-md-2">Статья</label>
		<div class="col-md-10">
			<select name="article_id" class="form-control">
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
	var mpar = new Array("master=article_folder");
	var dc = new doubleCombo("article_folder_id", document.forms[0].elements["article_id"], "/engine/ajax/getoptions.php", mpar, '<?=$dt->getRowValue('article_id')?>');
	dc.masterComboChanged();
</script>