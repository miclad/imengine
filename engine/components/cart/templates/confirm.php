<form action="<?=$PAGE->getUrl()?>?action=do_order" method="post">

	<input type="hidden" name="confirm" value="1"/>
	
	<?foreach($_POST as $k => $v){?>
		<?if(!is_array($v)){?>
			<input type="hidden" name="<?=$k?>" value="<?=mysql_escape_string($v)?>"/>
		<?}else{?>
			<?foreach($v as $k1 => $v1){?>
			<input type="hidden" name="<?=$k?>[<?=$k1?>]" value="<?=mysql_escape_string($v1)?>"/>
			<?}?>
		<?}?>
	<?}?>
	
	<?$total_sum = $total_weight = 0;?>
	
	<table class="confirm_items">
		<?foreach($items as $v){?>
		<tr>
			<td class="nm">
				<a href="/catalog<?=$v['folder_url'].'/'.$v['url']?>"><?=$v['brand_name'].' '.$v['model']?></a><br>
				<?=$v['package']?><?=($v['v_name'] != '' ? $v['v_name'] : '')?>
			</td>
			<td class="pr">
				<?=Site::formatPrice($v['price'])?> руб./шт.
			</td>
			<td class="num">
				<?=$v['items_num']?> шт.
			</td>
			<td class="sm">
				<?=Site::formatPrice($v['price']*$v['items_num'])?> руб.
			</td>
		</tr>
		<?
			$sum = $v['price']*$v['items_num'];
			$total_sum += $sum;
			$total_weight += $v['weight']*$v['items_num'];
		?>
		<?}?>
	</table>
	
	<div class="space sm"></div>
	
	<?
	$dsc = Catalog::getDiscount($total_sum, null, $items);
	?>
	
	<div class="cart_total">	
		<table>
			<tr>
				<td class="ttl">Итого:</td>
				<td class="val"><span id="totalSum"><span><?=Site::formatPrice($total_sum)?></span> руб.</span></td>
			</tr>
		</table>
		<div id="discount_info">
			<?=Page::includeComponent('cart', array(), array(), 'discount_info', '', true)?>
		</div>
	</div>
	
	<div class="space sm"></div>
	
	<div class="dotted_line"></div>
	
	<div class="confirm_ttl">Способ доставки</div>
	<div class="edit_link"><a href="/cart/?edit#delivery">Изменить</a></div>
	<div class="clearer"></div>
	<div class="confirm_cnt">
		<?if($delivery_data){?>
			<?foreach($delivery_data as $k => $v){?>
			<div <?if($k == 0){?> class="bold"<?}?>><?=$v?></div>
			<?}?>
			<?if($delivery_price){?>
				<?=Site::formatPrice($delivery_price)?> руб.
			<?}else{?>
				Бесплатно
			<?}?>
		<?}else{?>
			<b>не указан</b>
		<?}?>
	</div>
	
	<div class="dotted_line"></div>
	
	<div class="confirm_ttl">Способ оплаты</div>
	<div class="edit_link"><a href="/cart/?edit#payment">Изменить</a></div>
	<div class="clearer"></div>
	<div class="confirm_cnt">
		<b><?=$payment ? $payment['name'] : 'не указан'?></b>
	</div>
	
	<div class="dotted_line"></div>
	
	<div class="confirm_ttl">Контактная информация</div>
	<div class="edit_link"><a href="/cart/?edit#info">Изменить</a></div>
	<div class="clearer"></div>
	<div class="confirm_cnt">
		<div><b><?=htmlspecialchars($form->getValue('address'))?></b></div>
		<div><?=htmlspecialchars($form->getValue('surname').' '.$form->getValue('name').' '.$form->getValue('p_name'))?></div>
		<div><?=$form->getValue('email')?></div>
		<div><?=htmlspecialchars($form->getValue('phone'))?></div>
		<?if($form->getValue('delivery_date')){?>
			<div>Дата доставки: <b><?=$form->getValue('delivery_date')?></b></div>
		<?}?>
		<?if($form->getValue('delivery_time')){?>
			<div>Время доставки: <b><?=$form->getValue('delivery_time')?></b></div>
		<?}?>
		<?if($form->getValue('delivery_time')){?>
			<div>Номер дисконтной карты: <b><?=sprintf('%04d',$form->getValue('discount_card'))?></b></div>
		<?}?>
		<?if($form->getValue('comments')){?>
			<div>Комментарий: <i><?=$form->getValue('comments')?></i></div>
		<?}?>
	</div>
	
	<div class="dotted_line"></div>
	
	<div class="cart_total big">	
		<table>
			<tr id="sum_container">
				<td class="ttl" style="width:85px;">К оплате:</td>						
				<td class="val"><span><span id="sumplusdelivery" style="font-size:20px;"><?=Site::formatPrice($dsc['total_sum'] + $delivery_price)?></span> руб.</span></td>
			</tr>
		</table>
	</div>
	
	<div class="confirm_btn">
		<input type="image" src="/img/confirm_btn.gif" class="im" alt="Подтверждаю заказ"/>
		<br><br>
		&larr; <a href="/cart/?edit">Вернуться к редактированию заказа</a>
	</div>
	
</form>