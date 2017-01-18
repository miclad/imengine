<?='<?xml version="1.0" encoding="utf-8"?>'?>

<wishlistStat>
	<?/*
	<items>
	<?foreach($cart_o->getWishlistItems($cart_o->getDefaultList()) as $v){?>
		<item>
			<name><![CDATA[<?=$v['model']?>]]></name>
			<variant><![CDATA[<?=$v['variant_name']?>]]></variant>
			<id><?=$v['id']?></id>
			<num><?=$v['items_num']?></num>
			<price><?=$v['price']?></price>
		</item>
	<?}?>
	</items>
	*/?>
	<statText><![CDATA[<?=Cart::getStatText($wl_data['count_num'], $wl_data['discount']['total_sum'])?>]]></statText>	
	<discountText><?=$wl_data['discount']['text']?></discountText>
	<count><?=$wl_data['count']?></count>
	<totalSum><?=Site::formatPrice($wl_data['sum'])?></totalSum>
	<countNum><?=$wl_data['count_num']?></countNum>
	<currency>р.</currency>
</wishlistStat>