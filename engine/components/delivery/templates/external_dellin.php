<?/*
<div>
	<a href="http://dellin.ru/calculator/?mode=time" target="_blank" onclick="this.blur()">Рассчитать срок доставки</a>
</div>

<table class="ins">
<tr>
<td>
*/?>
	в &nbsp;
	<select name="delivery[city<?=$this->getConfig('type_id')?>]" class="_not_init_calc">
		<option value=""><?=$this->getConfig('choose_city')?></option>
		<?while($t = $cities->getNext()){?>
		<option value="<?=$t['value']?>"<?if(isset($current_type[1]) && $current_type[1] === $t['value']){?> selected<?}?>><?=$t['name']?></option>
		<?}?>
	</select>
<?/*
</td>
<td><input type="checkbox" name="dellin_arrival_door"<?if(isset($current_type[2]) && $current_type[2]){?> checked<?}?>/></td>
<td>до дверей</td>
<td>
	<input type="button" value="Рассчитать" class="button" onclick="oDelivery.dellinCalculate(true)"/>
</td>
</tr>
</table>
*/?>