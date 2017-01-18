<div class="control_group">
	<select name="delivery[city<?=$this->getConfig('type_id')?>]">
		<option value=""><?=$this->getConfig('choose_city')?></option>
		<?while($t = $cities->getNext()){?>
		<option value="<?=$t['ind']?>"<?if(isset($current_type[1]) && $current_type[1] == $t['ind'] || in_array($t['ind'], $detected_cities)){?> selected<?}?>><?=$t['name']?></option>
		<?}?>
	</select>
	<div class="control_error"></div>
</div>

<?/*
Ваш почтовый индекс <input type="text" name="russianpost_index" value="<?if(isset($current_type[1])){echo htmlspecialchars($current_type[1]);}?>"/>
<input type="button" value="Рассчитать" class="button" onclick="oDelivery.russianpostCalculate()"/>
*/?>
