<?if(!defined('IN_CONTEXT'))exit();?>
<div class="space sm"></div>
<form action="<?=$url?>" method="post">
	
	<?foreach($values as $k => $v){?>
	<input type="hidden" name="<?=$k?>" value="<?=$v?>"/>
	<?}?>
	
	<input type="submit" value="Оплатить"/>

</form>