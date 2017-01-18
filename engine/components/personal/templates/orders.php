
	<h2 class="short-title">История заказов</h2>

	<?if($orders->selectedRowsCount()){?>
		<ul class="orders-history">
			<li class="orders-history__item orders-history__item--head">
				<table>
					<tr>
						<td class="orders-history__item__id">№ заказа</td>
						<td class="orders-history__item__date">Дата</td>
						<td class="orders-history__item__status">Статус</td>
						<td class="orders-history__item__count">Товаров</td>
						<td class="orders-history__item__price">Сумма</td>
						<td class="orders-history__item__toggle">Детали</td>
					</tr>
				</table>
			</li>
			<?
			$total = 0;
			while($order = $orders->getNext()){
				$order['items'] = Orders::getItems($order['id']);
				$deliv = Delivery::parseDeliveryType($order['delivery_type'], 'str');
				$payment = $db->query('select * from payment_types where id='.intval($order['payment']))->getNext();
				$payment_link = '';
				if(!$order['payed'] && $payment && $payment['payment_component']){
					$payment['link_title'] = 'Оплатить';
					$payment_link = Page::includeComponent($payment['payment_component'], array('return_link' => true, 'order_id' => $order['id']), array());
				}elseif($payment['link_title'] && $payment['link_url']){
					//$payment_link = $payment['link_url'];
				}
				$total += $order['sum_discount'] + $order['delivery_price'];
			?>
				<li class="orders-history__item b-toggle"  data-show="Развернуть" data-hide="Свернуть">
					<table>
						<tr>
							<td class="orders-history__item__id"><?=$order['id']?></td>
							<td class="orders-history__item__date"><?=Site::formatDBDate($order['date'], false, false)?></td>
							<td class="orders-history__item__status"><?=($order['status_name'] != '' ? $order['status_name'] : '-')?></td>
							<td class="orders-history__item__count">
								<?=$order['items_num']?>
							</td>
							<td class="orders-history__item__price">
								<?=Site::formatPrice($order['sum_discount']+$order['delivery_price']).' руб.'?>
							</td>							
							<td class="orders-history__item__toggle">
								<a href="#"  class="b-toggle__open">Развернуть</a>
							</td>
						</tr>
					</table>
					<div class="b-toggle__wrap">
						<div class="orders-history__item__details">
							<div class="orders-history__item__products">
								<table>
									<tbody>
										<?foreach($order['items'] as $product){
											$lnk = '/catalog/'.urlencode($product['url']).'/';
										?>       
											<tr>
												<td class="orders-history__product__picture">
													<?if($product['photo_id']){?>
														<img src="/img/catalog/more/med<?=$product['photo_id']?>.jpg" class="photo_item" alt="<?=htmlspecialchars($product['model'])?>"/>
													<?}else{?>
														<i class="glyphicon glyphicon-camera no-photo"></i>
													<?}?>
												</td>
												<td class="orders-history__product__label">
													<a href="/catalog/<?=$product['url']?>/"><?=$product['brand_name'].' '.$product['model'].' '.$product['package']?></a><br>
													<span><?=Catalog::buildLensTitle($product)?></span>
												</td>
												<td class="orders-history__product__quantity">
													<?=$product['items_num']?> шт.
												</td>
												<td class="orders-history__product__price">
													<?=Site::formatPrice($product['price'])?> руб.
												</td>
											</tr>
										<?}?>
									</tbody>
								</table>
							</div>
							<div class="orders-history__item__foot">
								
								<table>									

									<tr>
										<td>Способ доставки</td>
										<td><?=$deliv?></td>
									</tr>
									
									<?
									$order['address'] = Delivery::buildAddressString($order);
									?>

									<?if($order['address']){?>
										<tr>
											<td>Адрес доставки</td>
											<td><?=$order['address']?></td>
										</tr>
									<?}?>

									<?if($order['delivery_price'] > 0){?>
										<tr>
											<td>Стоимость доставки</td>
											<td><?=round($order['delivery_price'])?> руб.</td>
										</tr>
									<?}?>
										
									<?if($payment){?>
										<tr>
											<td>Способ оплаты</td>
											<td>
												<?=$payment['name']?>
											</td>
										</tr>
									<?}?>	
										
									
										<tr>
											<td></td>
											<td>
												<div class="row">
													<div class="col-5">
														<a href="/cart/?action=repeat_order&id=<?=$order['id']?>" class="btn btn--med">Повторить заказ</a>
													</div>
													<?if(!$order['payed'] == 0 && $payment['payment_component']){?>
														<div class="col-5">
															<?=Page::includeComponent($payment['payment_component'], array('order_id' => $order['id'], 'button_class' => 'btn--med'))?>
														</div>
													<?}?>
												</div>
											</td>
										</tr>
									
								</table>

							</div>
						</div>
					</div>
				</li>
			<?}?>
			<li class="orders-history__item orders-history__item--foot">
				<table>
					<tr>
						<td class="orders-history__item__id">&nbsp;</td>
						<td class="orders-history__item__date">&nbsp;</td>
						<td class="orders-history__item__status">&nbsp;</td>
						<td class="orders-history__item__count">&nbsp;</td>
						<td class="orders-history__item__price" colspan="2">Всего заказов на сумму:<br><h2 class="short-title"><?=Site::formatPrice($total)?> руб.</h2></td>
					</tr>
				</table>
			</li>
		</ul>
	<?}else{?>
		Нет заказов
	<?}?>
