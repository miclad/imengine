<?$reg = false;?>
в &nbsp;
<select name="delivery[city<?=$this->getConfig('type_id')?>]">
	<option value=""><?=$this->getConfig('choose_city')?></option>
	<?while($t = $cities->getNext()){?>
	<option value="<?=$t['value']?>"<?if(isset($current_type[1]) && $current_type[1] == $t['value']){?> selected<?}?>><?=$t['name']?></option>
	<?}?>
</select>

<?/*
<div class="space smm"></div>

<table style="margin:0;">
<tr>
	<td style="padding-bottom:0;"><input type="checkbox" name="ems_nalozhenniy_platezh" value="1"<?if(isset($current_type[3]) && $current_type[3]){?> checked<?}?> onclick="oDelivery.calculateSumPlusDelivery()"/></td>
	<td style="padding-bottom:0;">наложенный платеж (<a href="/delivery/payment/details/2/" target="_blank">только для постоянных клиентов</a>)</td>
</tr>
</table>
*/?>