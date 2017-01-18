<?if(!defined('IN_CONTEXT'))exit();?>
<form action="<?=$url?>" method="post">
	
	<?foreach($values as $k => $v){?>
	<input type="hidden" name="<?=$k?>" value="<?=$v?>"/>
	<?}?>
	
	<button type="submit" class="btn btn_green"><span>Перейти к онлайн-оплате заказа</span></button>

</form>