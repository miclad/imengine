<div id="pickpoint">
	<div id="pickpoint_default_text_<?=$this->getConfig('type_id')?>"<?if($is_current && count($current_type) > 2){?> style="display:none;"<?}?>>
		<a onclick="PickPoint.open(oDelivery.pickPointResponse); return false;" href="#" class="dotted">Выберите удобный для вас пункт выдачи</a><br>
		<?/*для определения стоимости доставки*/?>
	</div>
	<div id="pickpoint_selected_<?=$this->getConfig('type_id')?>"<?if($is_current && count($current_type) > 2){?> style="display:block;"<?}else{?> style="display:none;"<?}?>>
		<b>Пункт выдачи:</b><br>
		<span id="pickpoint_name_<?=$this->getConfig('type_id')?>"><?=$is_current && isset($current_type[2]) ? $current_type[2] : ''?></span><br>
		<span id="pickpoint_address_<?=$this->getConfig('type_id')?>"><?=$is_current && isset($current_type[1]) ? $current_type[1] : ''?></span><br>
		<a onclick="PickPoint.open(oDelivery.pickPointResponse); return false;" href="#" class="dotted">Выбрать другой</a><br>
	</div>
</div>
<input type="hidden" name="delivery[pickpoint_name_<?=$this->getConfig('type_id')?>]" id="pickpoint_name_inp_<?=$this->getConfig('type_id')?>" value="<?=$is_current && isset($current_type[2]) ? htmlspecialchars($current_type[2]) : ''?>"/>
<input type="hidden" name="delivery[pickpoint_address_<?=$this->getConfig('type_id')?>]" id="pickpoint_address_inp_<?=$this->getConfig('type_id')?>" value="<?=$is_current && isset($current_type[1]) ? htmlspecialchars($current_type[1]) : ''?>"/>
<input type="hidden" name="delivery[pickpoint_id_<?=$this->getConfig('type_id')?>]" id="pickpoint_id_inp_<?=$this->getConfig('type_id')?>" value="<?=$is_current && isset($current_type[3]) ? htmlspecialchars($current_type[3]) : ''?>"/>