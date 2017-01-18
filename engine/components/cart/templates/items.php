<h2 class="short-title">Корзина</h2>

<div id="cart-content">

<?if(count($items)){?>

	<?$total_sum = $total_weight = $total_num = 0;?>
	
		<div class="cart" id="cart-items">	
			<?foreach($items as $pi => $product){?>		
				<div class="cart__item" id="cart-item-<?=$product['entry_id']?>">
					<table>
						<?
						//$product['price'] = round($product['price']);
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
									<div class="cart__item__quantity__title">Количество</div>
									<div class="quantity" data-min-value="1">
										<span class="quantity__minus" data-action="minus"><i class="fa fa-minus"></i></span>
										<input type="text" name="items_num[<?=$product['entry_id']?>]" value="<?=$product['items_num']?>"/>
										<span class="quantity__plus" data-action="plus"><i class="fa fa-plus"></i></span>						
									</div>	
								</div>

							</td>
							<td class="cart__item__price">	

								<div class="hidden" id="price_<?=$product['entry_id']?>"><?=$product['price']?></div>
								<?
								$sum = $product['price']*$product['items_num'];
								$total_sum += $sum;
								$total_num += $product['items_num'];
								$total_weight += $product['weight']*$product['items_num'];
								?>	
								<span id="sum_<?=$product['entry_id']?>"><?=Site::formatPrice($sum)?></span> р.
								<input type="hidden" name="weight_<?=$product['entry_id']?>" id="weight_<?=$product['entry_id']?>" value="<?=str_replace(',','.',floatval($product['weight']))?>"/>

							</td>
							<td class="cart__item__delete">

								<a href="#<?=$product['entry_id']?>" data-action="delete" data-id="<?=$product['entry_id']?>">&#x2715;</a>

							</td>
						</tr>
					</table>
				</div>
			<?}?>	
		</div>
		
		
		<div class="cart-summary">
			<div class="cart-summary__body">
				<div class="cart-summary__title">
					Товаров на сумму
				</div>
				<div class="cart-summary__value">
					<span id="totalSum"><?=Site::formatPrice($total_sum)?></span> р.
				</div>
			</div>
		</div>
		
		<div class="cart-submit">
			<a href="<?=$PAGE->getUrl()?>/order/" class="btn">Перейти к оформлению</a>
		</div>
		
		<?
		$dsc = Catalog::getDiscount($total_sum, null, $items);
		?>
		
	</div>
	
	<?/*
	<form action="/cart/?action=do_order" method="post" name="order_form">
	
		<h2>Оформить заказ</h2>
	
		<div class="rnd_box deliv">
			<?=Page::includeComponent('delivery', array('weight' => $total_weight, 'title' => '', 'current_type' => $delivery_type), array('order_sum' => $dsc['total_sum'], 'order_sum_full' => $total_sum), 'main', '', true);?>	
			<div class="clearer"></div>
			<div id="payment_types"></div>
		</div>
	
		<?
		$dsc = Catalog::getDiscount($total_sum, null, $items);
		?>	
		
		<div id="main_order_form">

			<input type="hidden" name="confirm" value="1"/>
			<input type="hidden" name="_save" value="1"/>
			<input type="hidden" name="id" value="<?=$GLOBALS['cart_o']->getDefaultList()?>"/>
			<input type="hidden" name="delivery_price" value=""/>
		
			<div class="form">
				
				<div class="form__item" id="order-field__surname">
					<div class="form__item__title">Фамилия <span class="req">*</span></div>
					<div class="form__item__input">
						<input type="text" name="surname" value="<?=htmlspecialchars($form->getValue('surname'))?>" class="inp"/>
					</div>
				</div>
				
				<div class="form__item" id="order-field__name">
					<div class="form__item__title">Имя <span class="req">*</span></div>
					<div class="form__item__input">
						<input type="text" name="name" value="<?=htmlspecialchars($form->getValue('name'))?>" class="inp"/>
					</div>
				</div>
			
				<div class="form__item" id="order-field__p_name" hidden>
					<div class="form__item__title">Отчество <span class="req">*</span></div>
					<div class="form__item__input">
						<input type="text" name="p_name" value="<?=htmlspecialchars($form->getValue('p_name'))?>" class="inp"/>
					</div>
				</div>
				
				<div class="form__item" id="order-field__passport" hidden>
					<div class="form__item__title">Паспорт <span class="req">*</span></div>
					<div class="form__item__input">
						<input type="text" name="passport" value="<?=htmlspecialchars($form->getValue('passport'))?>" class="inp"/>
					</div>
				</div>

				<div class="form__item">
					<div class="form__item__title">Телефон <span class="req">*</span></div>
					<div class="form__item__input">
						<input type="text" name="phone" data-type="phone" value="<?=htmlspecialchars($form->getValue('phone'))?>" class="inp"/>
					</div>
				</div>
				
				<div class="form__item">
					<div class="form__item__title">Электронная почта <span class="req">*</span></div>
					<div class="form__item__input">
						<input type="text" name="email" value="<?=htmlspecialchars($form->getValue('email'))?>" class="inp"/>				
					</div>
				</div>
				
				<div class="form__item" id="order-field__address_post_index" hidden>
					<div class="form__item__title">Почтовый индекс <span class="req">*</span></div>
					<div class="form__item__input">
						<input type="text" name="address_post_index" value="<?=htmlspecialchars($form->getValue('address_post_index'))?>" class="inp"/>				
					</div>
				</div>
				
				<div class="form__item" id="order-field__address_city" hidden>
					<div class="form__item__title">Город <span class="req">*</span></div>
					<div class="form__item__input">
						<input type="text" name="address_city" value="<?=htmlspecialchars($form->getValue('address_city'))?>" class="inp"/>				
					</div>
				</div>
				
				<div class="form__item" id="order-field__address_region" hidden>
					<div class="form__item__title">Область <span class="req">*</span></div>
					<div class="form__item__input">
						<input type="text" name="address_region" value="<?=htmlspecialchars($form->getValue('address_region'))?>" class="inp"/>				
					</div>
				</div>
				
				<div class="form__item" id="order-field__address" hidden>
					<div class="form__item__title">Адрес доставки <span class="req">*</span></div>	
					<div class="form__item__input">
						<div>
							<input placeholder="Улица" type="text" name="address_street" value="<?=htmlspecialchars($form->getValue('address_street'))?>"/>
						</div>
						<div class="row">
							<div class="col-4">
								<input placeholder="Номер дома" type="text" name="address_house" value="<?=htmlspecialchars($form->getValue('address_house'))?>"/>
							</div>
							<div class="col-4">
								<input placeholder="Корпус" type="text" name="address_corp" value="<?=htmlspecialchars($form->getValue('address_corp'))?>"/>
							</div>
							<div class="col-4">
								<input placeholder="Квартира/офис" type="text" name="address_flat" value="<?=htmlspecialchars($form->getValue('address_flat'))?>"/>
							</div>
						</div>
					</div>
				</div>

				<div class="form__item">
					<div class="form__item__title">Комментарий</div>
					<div class="form__item__input">
						<textarea name="comments" rows="2" class="inp"><?=htmlspecialchars($form->getValue('comments'))?></textarea>
					</div>
				</div>
				
				<div class="form__item">
					<div class="form__item__title">&nbsp;</div>
					<div class="form__item__input">
						<button type="submit" class="btn">Оформить заказ</button>
					</div>
				</div>				
				
			</div>
			
			
		
		</div>
	
	</form>
	*/
	?>

<?}else{?>
	<div class="text_page">Корзина пуста</div>
<?}?>

</div>

<script type="text/javascript">
	var current_payment_type = <?=intval($form->getValue('payment'))?>;
</script>
