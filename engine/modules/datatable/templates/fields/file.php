<?if($f_desc[1][0] == 'file'){?>
	<?if($this->ent->getRowValue($field)){?>
		<div><a href="/<?=$f_desc[1][2].$this->ent->getRowValue($field)?>" target="_blank">просмотр</a> &nbsp; <?if(!$this->ent->dataDesc[1][$field][2]){?><input type="checkbox" name="<?=$this->parseFieldName($field)?>[delete]" value="1" class="chb"/> удалить<?}?></div>
	<?}?>
<?}else{?>

	<?
	$orig_ext_field = isset($f_desc[1]['orig_ext_field']) ? $f_desc[1]['orig_ext_field'] : 'orig_ext';
	$format_desc = $f_desc[1][1][count($f_desc[1][1])-1];
	$src = '';	
	if($this->ent->getRowValue($orig_ext_field)){
		$ext = isset($format_desc['orig_ext']) && $format_desc['orig_ext'] ? $this->ent->getRowValue($orig_ext_field) : 'jpg';
		$src = '/'.$format_desc[2].$format_desc[1].$this->ent->getRowValue('id').'.'.$ext;
	}elseif(isset($f_desc[1]['put_in_fields'])){
		$cnt_put = count($f_desc[1]['put_in_fields']);
		if($this->ent->getRowValue($f_desc[1]['put_in_fields'][$cnt_put-1])){
			$src = '/'.$format_desc[2].$this->ent->getRowValue($f_desc[1]['put_in_fields'][$cnt_put-1]);
		}
	}
	$allow_delete = !$f_desc[2];
	?>
	<div>
		<?if($src != ''){?>		
			<a href="<?=$src?>" target="_blank">просмотр</a> &nbsp;
			<?if($allow_delete){?>
				<input type="checkbox" name="<?=$this->parseFieldName($field)?>[delete]" value="1" class="chb"/> удалить
			<?}?>
		<?}?>
	</div>
	
<?}?>
<input type="file" name="<?=$field_name?>"/>