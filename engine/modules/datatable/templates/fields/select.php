<?
$options = array();
$is_range = false;
if(isset($f_desc[3]['range'])){
	
	$is_range = true;
	$r = $f_desc[3]['range'];
	
	for($i = $r['min']; $i <= $r['max']; $i+=$r['step']){
		$options[] = array($i, $i);
	}
	
}else{
	$options = is_array($f_desc[3][1]) ? $f_desc[3][1] : $GLOBALS[$f_desc[3][1]];
}
$val = $this->ent->getRowValue($field);
?>
<select name="<?=$field?>" class="form-control" id="formField<?=$field?>">
	<?if(!isset($f_desc[3][2])){?>
	<option value="">--</option>
	<?}?>
	<?foreach($options as $v){?>
	<option value="<?=$v[0]?>"<?if($is_range && strlen($val) && number_format((float)$v[0], 2) == number_format((float)$val, 2) || !$is_range && $v[0] == $val){?> selected<?}?>><?if(isset($v["level"]))for($tl=0;$tl<$v["level"];$tl++)echo "&nbsp;&nbsp;&nbsp;";?><?=str_replace('&nbsp;', '', strip_tags($v[1]))?></option>
	<?}?>
</select>