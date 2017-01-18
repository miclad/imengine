<?if(!defined("IN_CONTEXT")) exit();?>
<table class="date_form">
<tr>
<td>
	<select name="<?=$field?>[day]">
	<option value="">--</option>
	<?for($i=1;$i<32;$i++){?>
	<option value="<?=$i?>"<?if(isset($selected["day"]) && $selected["day"]==$i){?> selected<?}?>><?=$i?></option>
	<?}?>
	</select>
</td>
<td>
			
	<select name="<?=$field?>[month]">
	<option value="">--</option>
		<?for($i=1;$i<=count($monthes_list);$i++){?>
		<option value="<?=$i?>"<?if(isset($selected["month"]) && $selected["month"]==$i){?> selected<?}?>><?=$monthes_list[$i-1]?></option>
		<?}?>
	</select>

</td>
<td>
			
	<select name="<?=$field?>[year]">
	<option value="">--</option>
		<?for($i=$start_year; $i<$end_year; $i++){?>
		<option value="<?=$i?>"<?if(isset($selected["year"]) && $selected["year"]==$i){?> selected<?}?>><?=$i?></option>
		<?}?>
	</select>

</td>
<?if($show_hours){?>
<td style="padding-left:15px;">
	
	<select name="<?=$field?>[hour]">
	<option value="">--</option>
		<?for($i=0; $i<24;$i++){?>
		<option value="<?=$i?>"<?if(isset($selected["hour"]) && $selected["hour"]==$i){?> selected<?}?>><?=$i?></option>
		<?}?>
	</select>

</td>
<td>ч.</td>
<td>
			
	<select name="<?=$field?>[minute]">
	<option value="">--</option>
		<?for($i=0; $i<60; $i++){?>
		<option value="<?=$i?>"<?if(isset($selected["minute"]) && $selected["minute"]==$i){?> selected<?}?>><?=$i?></option>
		<?}?>
	</select>

</td>
<td>мин.</td>
<?}?>
</tr>
</table>