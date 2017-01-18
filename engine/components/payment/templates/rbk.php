<?if(!defined('IN_CONTEXT'))exit();?>
<form action="https://rbkmoney.ru/acceptpurchase.aspx" name="pay" method="POST">
	<?foreach($values as $k => $v){?>
		<input type="hidden" name="<?=$k?>" value="<?=htmlspecialchars($v)?>"/>
	<?}?> 
	<input type="submit" name="button" value=" оплатить "> 
</form>