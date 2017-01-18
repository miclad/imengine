<table width="600px">
<tr>
	<td>
	
	ООО «Рога»<br>
	190000, ...
	<br><br>
	<?/*<div align="center">Образец заполнения платежного поручения</div><br>*/?>
	
	<table cellpadding="4px" border="1" width="100%">
		<tr>
			<td>
				ИНН 000
			</td>
			<td>
				КПП 000
			</td>
			<td rowspan="2">
				Сч.№
			</td>
			<td rowspan="2">
				000
			</td>
		</tr>
		<tr>
			<td colspan="2">
				Получатель: ООО «Рога»
			</td>
		</tr>
		<tr>
			<td rowspan="2" colspan="2">
				Банк получателя: ХХХ
			</td>
			<td>
				БИК
			</td>
			<td rowspan="2">
				 ХХХ<br><br>
				 ХХХ
			</td>
		</tr>
		<tr>
			<td>
				Сч.№
			</tr>
		</tr>
	</table>
	
	<br><br>
	<h2>СЧЕТ № <?=$order_data['id']?> от <?=Site::formatDate(time())?> г.</h2>
	
	Покупатель: <?=($order_data['payment'] == 'beznal' ? $order_data['company'] : $order_data['name'])?>
	
	<br><br>
	
	<table cellpadding="4px" border="1" width="100%">
		<tr>
			<td>№</td>
			<td>Наименование товара, работ, услуг</td>
			<td>Ед. изм.</td>
			<td>Кол-во</td>
			<td>Цена, руб.</td>
			<td>Сумма, руб.</td>
		</tr>
		<?
		$sum = $order_data['with_delivery'] ? intval($order_data['delivery_price']) : 0;
		?>
		<?foreach($items as $k=>$v){?>
			<?		
			$t_pr = $v['items_num']*round($v['price']);
			$sum += $t_pr;
			?>
			<tr>
				<td><?=($k+1)?></td>
				<td><?=$v['brand_name'].' '.$v['model']?></td>
				<td>шт.</td>
				<td><?=$v['items_num']?></td>
				<td><?=number_format(round($v['price']),2,'.','\'')?></td>
				<td><?=number_format(round($t_pr),2,'.','\'')?></td>
			</tr>
		<?}?>
		<?if($order_data['with_delivery'] && $order_data['delivery_price']){?>
			<tr>
				<td><?=($k+1)?></td>
				<td>Доставка</td>
				<td>шт.</td>
				<td>1</td>
				<td><?=number_format($order_data['delivery_price'],2,'.','\'')?></td>
				<td><?=number_format($order_data['delivery_price'],2,'.','\'')?></td>
		<?}?>
		<tr>
			<td colspan="5" align="right">Итого:</td>
			<td><?=number_format($sum,2,'.','\'')?></td>
		</tr>
		<tr>
			<?
			$nds = number_format($sum - ($sum/1.18),2,'.','\'');
			?>
			<td colspan="5" align="right">В том числе НДС (18%):</td>
			<td><?=$nds?></td>
		</tr>
		<?$sum = number_format($sum,2,'.','\'')?>
		<tr>
			<td colspan="5" align="right"><b>Всего к оплате:</b></td>
			<td><b><?=$sum?><b></td>
		</tr>
	</table>
	
	<br><br>
	
	Всего наименований - <?=count($items) + ($order_data['with_delivery'] && intval($order_data['delivery_price']) ? 1 : 0)?>, на сумму <?=$sum?> руб.
	
	<br><br><br>
	
	Руководитель предприятия ______________________________ (ХХХ)<br><br>
	Главный бухгалтер _____________________________________ (ХХХ)
	
	<br><br>
	<img src="/img/stamp.jpg"/>
	
</td>
</tr>
</table>