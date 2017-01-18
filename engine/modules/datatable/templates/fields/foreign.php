<?if(isset($f_desc[1]['mode']) && $f_desc[1]['mode'] == 'text'){

	$val = '';
	$ff_ent = new DataTable($f_desc[1][1]);
	$tf = array_keys($ff_ent->getTitleFields());
	if($this->ent->getRowValue($field)){
		$ff_ent->initCurRow('id='.intval($this->ent->getRowValue($field)));		
		$val = $ff_ent->getRowValue($tf[0], false);
	}
	
	
	
?>
	<?/*
	<input type="hidden" name="<?=$field_name?>" value="<?=$this->ent->getRowValue($field)?>">
	*/?>
	<input type="text" name="_<?=$field_name?>_fk_text" class="form-control" id="formField<?=$field_name?>" value="<?=$val?>" data-autocomplete="<?=$f_desc[1][1]?>" data-autocomplete-field="<?=$tf[0]?>"<?/* data-autocomplete-id-field="<?=$field_name?>"*/?>/>

<?}else{?>

	<select name="<?=$field_name?>" class="form-control" id="formField<?=$field_name?>">
		<?if(true || !$f_desc[2]){?>
		<option value="0">--</option>
		<?}?>
	<?

		$ff_ent = new DataTable($f_desc[1][1]);
		if(!isset($f_desc[1]['no_fill']) || !$f_desc[1]['no_fill']){
			$fd = $ff_ent->getGrid(
				isset($f_desc[1]['order_by']) ? $f_desc[1]['order_by'] : "", 
				isset($f_desc[1][3]) ? $f_desc[1][3] : "1", 
				true, 
				($this->entity != $f_desc[1][1] && !isset($f_desc['no_root']))
			);
		}else{
			$fd = array();
		}

		foreach($fd as $v){
	?>
		<option value="<?=$v["__value"]?>"<?if($v["__value"]===$this->ent->getRowValue($field)){?> selected<?}?>><?if(isset($v["_tree_level"]))for($i=0;$i<=$v["_tree_level"];$i++){?>&nbsp;&nbsp;&nbsp;&nbsp;<?}?><?=isset($v["_tree_level"]) ? ' ' : ''?><?=Common::cropText(str_replace('&nbsp;', '', strip_tags($v["__title"])), 100, false, '...')?></option>
	<?}?>
	</select>

<?}?>