<?if(!defined("IN_CONTEXT")) exit();?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Заказ №<?=$order_data['id']?></title>
		<link href="/engine/tools/bootstrap/css/bootstrap.css" rel="stylesheet"/>
		<link href="/engine/templates/admin/css/main.css?<?=filemtime(B_DIR.'engine/templates/admin/css/main.css')?>" rel="stylesheet"/>
		<link rel="stylesheet" type="text/css" href="/engine/templates/admin/css/print.css"/>
		<script src="/engine/tools/jquery-1.10.2.min.js"></script>		
	</head>
	<body>
	
		<?
		$total_sum = $total_num = 0;
		?>
		
		<?
		$delivery_data = false;
		$deliv = Delivery::parseDeliveryType($order_data['delivery_type'], 'str', $delivery_data);
		$payment = $order_data['payment'] ? $db->query('select * from payment_types where id='.intval($order_data['payment']))->getNext() : false;
		?>
		
		<div class="print_version">
			
			<div class="logo">
				<img src="/img/logo.png" width="200px"/>
			</div>
			
			<table class="order_info">
				<tr class="ttl">
					<td class="fn">
						<h1>№<?=$order_data['id']?></h1>
					</td>
					<td>
						<h1><?=$order_data['surname'].' '.$order_data['name'].' '.$order_data['p_name']?></h1>
					</td>
				</tr>				
				<tr>
					<td class="fn">Телефон:</td>
					<td><?=$order_data['phone']?></td>
				</tr>
				<?if($order_data['email']){?>
				<tr>
					<td class="fn">E-mail:</td>
					<td><?=$order_data['email']?></td>
				</tr>	
				<?}?>
				<?if($deliv != ''){?>
				<tr>
					<td class="fn">Способ доставки:</td>
					<td><?=$deliv?></td>
				</tr>
				<?}?>
				<tr>
					<td class="fn">Адрес доставки:</td>
					<td><?=$order_data['address'] ? $order_data['address'] : Delivery::buildAddressString($order_data)?></td>
				</tr>
				<?if($delivery_data && $delivery_data['show_passport'] && $order_data['passport']){?>
				<tr>
					<td class="fn">Паспорт:</td>
					<td><?=htmlspecialchars($order_data['passport'])?></td>
				</tr>	
				<?}?>
				<?if($delivery_data && $delivery_data['show_metro'] && $order_data['metro']){?>
					<tr>
						<td class="fn">Метро:</td>
						<td><?=htmlspecialchars($order_data['metro'])?></td>
					</tr>
				<?}?>	
				<?if($payment){?>
				<tr>
					<td class="fn">Способ оплаты:</td>
					<td><?=$payment['name']?></td>
				</tr>
				<?}?>
				<?if($order_data['comments']){?>
				<tr>
					<td class="fn">Комментарий:</td>
					<td><?=($order_data['comments'] ? $order_data['comments'] : 'нет')?></td>
				</tr>	
				<?}?>
			</table>
			
			<table class="bill_items" width="100%">
				<?if(count($products)){?>
				<tr>
					<td>Наменование</td>
					<td>Цена</td>
					<td>Кол-во</td>
					<td>Стоимость</td>
				</tr>
				<?}?>
				<?foreach($products as $k=>$product){?>
				<?
				$products[$k]['price'] = round($product['price']*(1-$product['discount']/100));
				?>
				<tr class="datarow">
					<td class="name">
						<?=$product['brand_name'].' '.$product['model']?> <?=$product['package']?>
						<?if($product['item_variant']){?><br>(<?=Catalog::buildLensTitle($product)?>)<?}?>
					</td>
					<td class="price"><?=Site::formatPrice(round($product['price']))?> руб.</td>
					<td class="num"><?=$product['items_num']?> шт.</td>
					<?
					$sum = round($products[$k]['price'] * $product['items_num']);
					?>
					<td class="total_price"><?=Site::formatPrice($sum)?> руб.</td>
					<?$total_sum += $sum;$total_num+=$product['items_num']?>
				</tr>	
				<?}?>
				<tr>
					<td><b>Итого</b></td>	
					<td></td>
					<td><b><?=$total_num?> шт.</b></td>
					<td><b><?=Site::formatPrice($order_data['sum'])?> руб.</b></td>
				</tr>
				<?if($order_data['discount_value'] > 0){?>
					<tr>
						<td><b>Скидка</b></td>	
						<td></td>
						<td></td>
						<td><b><?=$order_data['discount_value']?>%</b></td>
					</tr>
					<tr>
						<td><b>Итого с учетом скидки</b></td>	
						<td></td>
						<td><b><?=$total_num?> шт.</b></td>
						<td><b><?=Site::formatPrice($order_data['sum_discount'])?> руб.</b></td>
					</tr>
				<?}?>
				<?if($order_data['delivery_price'] > 0){?>
					<tr>
						<td><b>Доставка</b></td>
						<td></td>
						<td></td>
						<td><b><?=round($order_data['delivery_price']).' руб.'?></b></td>
					</tr>
				<?}?>
				<?if($order_data['use_bonus']){?>
				<tr>
					<td><b>Оплачено бонусными баллами</b></td>	
					<td></td>
					<td></td>
					<td><b><?=$order_data['use_bonus']?> руб.</b></td>
				</tr>
				<?}?>
			</table>
			
			<h1 class="total_sum">Итого к оплате: <b><?=Site::formatPrice(($order_data['sum_discount'] ? $order_data['sum_discount'] : $order_data['sum'])+$order_data['delivery_price']-$order_data['use_bonus'])?> руб.</b></h1>
			
		</div>
		
		<script type="text/javascript">
			$(document).ready(function(){
				window.print();
			})
		</script>
		
	</body>
</html>