<?
$e = $f_desc[1][1];
$f_ob = new DataTable($e);
$f_list = $f_ob->getGrid((isset($f_desc[1]['order_by']) ? $f_desc[1]['order_by'] : $f_ob->dataDesc[2][1]), (false && $e == $this->entity ? 'id != '.intval($this->getRowValue('id')).' and ' : '') . (isset($f_desc[1][4]) ? $f_desc[1][4] : '1'), true, false, (isset($f_desc[1]['left_join']) ? $f_desc[1]['left_join'] : ''), (isset($f_desc[1]['extra_fields']) ? $f_desc[1]['extra_fields'] : ''));

$table = $field;
$field_name = $field;

$chosen_q = $f_desc[1][2]."=".$this->ent->getRowValue("id");
		
if(isset($f_desc[1]['table'])){
	$table = $f_desc[1]['table'];
	$field_name = $f_desc[1]['table'];
}
if(isset($f_desc[1]['additional_field']) && isset($f_desc[1]['additional_value'])){
	$field_name .= '['.$f_desc[1]['additional_value'].']';
	$chosen_q .= ' and '.$f_desc[1]['additional_field'].'="'.$f_desc[1]['additional_value'].'"';
}

$vals = array();
if(is_numeric($this->ent->getRowValue('id'))){
	$r = $GLOBALS['db']->query('select '.$f_desc[1][3].' from '.$table.' where '.$chosen_q);
	while($t = $r->getNext())
		$vals[] = $t[$f_desc[1][3]];
}
$si = 0;
$limit = !isset($f_desc[1]['no_limit']) ? 10 : 0;

$ch = $this->ent->getRowValue($field);

?>

<div class="field--multi-check" id="mf_<?=$field?>">

	<?foreach($f_list as $k => $v){?>

		<div<?if(isset($v['_tree_level'])){?> style="padding-left:<?=($v['_tree_level']*20)?>px;"<?}?>>
			<label>
				<input type="checkbox" name="<?=$field_name?>[]" value="<?=$v["__value"]?>"<?if(is_array($ch) && in_array($v["__value"], $ch)){?> checked<?}?>/>
				<?=isset($f_desc[1]['show_field']) ? $v[$f_desc[1]['show_field']] : $v['__title']?>
			</label>
		</div>

	<?$si++;}?>

</div>