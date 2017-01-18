<?if($isok){?>

<h2 class="short-title">Спасибо!</h2>

<div class="cart-success cart-order">
		
		<p>
			<?=str_replace(array('#ORDER_ID#'), array($order_data['id']), nl2br(DBCommon::getConfig('order_success')))?>
		</p>
		
		<div class="content-box">
			
			<h3>Ваш заказ:</h3>
			
			<div class="cart" id="cart-items">	
				<?foreach($products as $product){?>		
					<div class="cart__item">
						<table>
							<?
							$main_package = Catalog::getMainPackage($product);
							?>
							<?$lnk = '/catalog'.$product['folder_url'].'/'.$main_package['url'].'/'?>
							<tr>
								<td class="cart__item__picture">

									<?if($product['photo_id']){?>
										<a href="<?=$lnk?>"><img src="/img/catalog/more/med<?=$product['photo_id']?>.jpg"/></a>
									<?}else{?>
										<i class="glyphicon glyphicon-camera no-photo"></i>
									<?}?>

								</td>
								<td class="cart__item__title">

									<a href="<?=$lnk?>"><?=$product['brand_name'].'<br>'.$product['model'].($product['product_color_name'] ? ' '.$product['product_color_name'] : '').($product['package'] ? '<br>'.$product['package'] : '')?></a>
									<?if($product['item_variant']){?>
										<br><span class="cart__item__article"><?=Catalog::buildLensTitle($product)?></span>
									<?}?>

									<div class="cart__item__quantity">
										<div class="cart__item__quantity__title">Количество: <?=$product['items_num']?></div>
									</div>

								</td>
								<td class="cart__item__price">	

									<?=Site::formatPrice($product['price']*$product['items_num'])?> р.

								</td>
							</tr>
						</table>
					</div>
				<?}?>	
			</div>
			
		</div>
		
		<?if($delivery_type && count($delivery_type) || $payment){?>
			<div class="content-box">

				<?if($delivery_type && count($delivery_type)){?>
					<h3>Доставка</h3>

					<div class="cart-success__delivery">

						<div>
							<i class="fa fa-truck fa-fw"></i> <?=$delivery_type[0]?>
						</div>

						<?if($order_data['address']){?>
							<div>
							<i class="fa fa-map-marker fa-fw"></i>	<?=$order_data['address']?>
							</div>
						<?}?>

						<?if($order_data['delivery_price'] > 0){?>
							<div>
								<i class="fa fa-calculator fa-fw"></i>	Стоимость доставки &mdash; <strong><?=round($order_data['delivery_price'])?>  р.</strong>
							</div>
						<?}?>

					</div>
				<?}?>
				
				<?if($payment){?>
					<h3>Оплата</h3>

					<div class="cart-success__delivery">

						<div>
							<i class="fa fa-money fa-fw"></i> <?=$payment['name']?>
						</div>


					</div>
				<?}?>
					
				<?if($order_data['use_bonus']){?>
					<h3>Баллы</h3>

					<div class="cart-success__delivery">

						<div>
							<i class="fa fa-gift fa-fw"></i> <?=Common::makeEnding($order_data['use_bonus'], 'Использован', '', 'о', 'о')?> <strong><?=$order_data['use_bonus'].' '.Common::makeEnding($order_data['use_bonus'], 'балл', '', 'а', 'ов')?></strong>
						</div>


					</div>
				<?}?>

			</div>
		<?}?>
		
		<?/*
		<div class="sum_total">
			Сумма к оплате: <b><?=Site::formatPrice($order_data['sum_discount']+$order_data['delivery_price'])?>.&ndash;</b>
		</div>
		*/?>
		
		<div class="cart-summary">
			<div class="cart-summary__body">
				<div class="cart-summary__title">
					Итого к оплате
				</div>
				<div class="cart-summary__value">
					<span id="sumPlusDelivery"><?=Site::formatPrice($order_data['sum_discount'] + $order_data['delivery_price'] - $order_data['use_bonus'])?></span> р.
				</div>
			</div>
		</div>
		
		<?if($payment){?>
			<?if($payment['payment_component']){?>
				<div class="cart-submit">
					<?=Page::includeComponent($payment['payment_component'], array('order_id' => $order_data['id']))?>
				</div>
			<?}elseif($payment['link_title'] && $payment['link_url']){?>
				<div class="cart-submit">
					<a href="<?=$payment['link_url']?>" target="_blank" class="btn"><?=$payment['link_title']?></a>
				</div>
			<?}?>
		<?}?>
	
</div>

<?}?>