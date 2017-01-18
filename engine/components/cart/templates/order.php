<h2 class="short-title">Доставка и оплата</h2>

<div class="cart-order">
	
	<form action="<?=$PAGE->getUrl().'/order/?action=do_order'?>" method="post">
		
		<input type="hidden" name="confirm" value="1"/>
		<input type="hidden" name="id" value="<?=$GLOBALS['cart_o']->getDefaultList()?>"/>
	
		<div class="content-box">

			<h3>Способ доставки</h3>
			
			<?=Page::includeComponent('delivery', array('weight' => $total_weight, 'title' => '', 'current_type' => $delivery_type), array('order_sum' => $summary['total_sum'], 'order_sum_full' => $total_sum), 'main', '', true);?>
			

		</div>
		
		<div id="payment_types"></div>
		
		<div class="content-box">
			
			<div id="main_order_form">

				<input type="hidden" name="confirm" value="1"/>
				<input type="hidden" name="_save" value="1"/>
				<input type="hidden" name="id" value="<?=$GLOBALS['cart_o']->getDefaultList()?>"/>
				<input type="hidden" name="delivery_price" value=""/>

				<div class="form">

					<div class="form__item" id="order-field__surname">
						<div class="form__item__title">Фамилия <span class="req">*</span></div>
						<div class="form__item__input">
							<input type="text" name="surname" value="<?=htmlspecialchars($form->getValue('surname'))?>" class="inp" data-required="true"/>
						</div>
					</div>

					<div class="form__item" id="order-field__name">
						<div class="form__item__title">Имя <span class="req">*</span></div>
						<div class="form__item__input">
							<input type="text" name="name" value="<?=htmlspecialchars($form->getValue('name'))?>" class="inp" data-required="true"/>
						</div>
					</div>

					<div class="form__item" id="order-field__p_name" hidden>
						<div class="form__item__title">Отчество <span class="req">*</span></div>
						<div class="form__item__input">
							<input type="text" name="p_name" value="<?=htmlspecialchars($form->getValue('p_name'))?>" class="inp" data-required="true"/>
						</div>
					</div>
					
					<div class="form__item">
						<div class="row">
							<div class="col-6">

									<div class="form__item__title">Телефон <span class="req">*</span></div>
									<div class="form__item__input">
										<input type="text" name="phone" data-type="phone" value="<?=htmlspecialchars($form->getValue('phone'))?>" class="inp" data-required="true"/>
									</div>

							</div>
							<div class="col-6">

									<div class="form__item__title">Электронная почта <span class="req">*</span></div>
									<div class="form__item__input">
										<input type="text" name="email" value="<?=htmlspecialchars($form->getValue('email'))?>" class="inp" data-type="email" data-required="true"/>				
									</div>

							</div>
						</div>
					</div>

					<div class="form__item" id="order-field__passport" hidden>
						<div class="form__item__title">Паспорт <span class="req">*</span></div>
						<div class="form__item__input">
							<input type="text" name="passport" value="<?=htmlspecialchars($form->getValue('passport'))?>" class="inp" data-required="true"/>
						</div>
					</div>				
					
					<div class="form__item" hidden>
						<div class="row">
							<div class="col-6">
								<div id="order-field__address_city" hidden>
									<div class="form__item__title">Город <span class="req">*</span></div>
									<div class="form__item__input">
										<input type="text" name="address_city" value="<?=htmlspecialchars($form->getValue('address_city'))?>" class="inp" data-required="true"/>				
									</div>
								</div>
							</div>
							<div class="col-6">
								<div id="order-field__address_post_index" hidden>
									<div class="form__item__title">Почтовый индекс <span class="req">*</span></div>
									<div class="form__item__input">
										<input type="text" name="address_post_index" value="<?=htmlspecialchars($form->getValue('address_post_index'))?>" class="inp" data-required="true"/>				
									</div>
								</div>
							</div>
						</div>
					</div>

					

					<div class="form__item" id="order-field__address_region" hidden>
						<div class="form__item__title">Область <span class="req">*</span></div>
						<div class="form__item__input">
							<input type="text" name="address_region" value="<?=htmlspecialchars($form->getValue('address_region'))?>" class="inp" data-required="true"/>				
						</div>
					</div>

					<div class="form__item" id="order-field__address" hidden>
						<div class="form__item__title">Адрес доставки <span class="req">*</span></div>	
						<div class="form__item__input">
							<div>
								<input placeholder="Улица" type="text" name="address_street" value="<?=htmlspecialchars($form->getValue('address_street'))?>" data-required="true"/>
							</div>
							<div class="row">
								<div class="col-4">
									<input placeholder="Номер дома" type="text" name="address_house" value="<?=htmlspecialchars($form->getValue('address_house'))?>" data-required="true"/>
								</div>
								<div class="col-4">
									<input placeholder="Корпус" type="text" name="address_corp" value="<?=htmlspecialchars($form->getValue('address_corp'))?>"/>
								</div>
								<div class="col-4">
									<input placeholder="Квартира/офис" type="text" name="address_flat" value="<?=htmlspecialchars($form->getValue('address_flat'))?>" data-required="true"/>
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

				</div>
				
			</div>
			
		</div>
		
		<div class="content-box">

			<h3>Использовать баллы</h3>
			
			<div class="cart-order__bonus">
				<?if($USER->getField('bonus')){?>				
					
				<p>Вам доступно <b><?=$USER->getField('bonus').' '.Common::makeEnding($USER->getField('bonus'), 'балл', '', 'а', 'ов')?></b></p>
					
					<div class="quantity" data-min-value="0" data-max-value="<?=$USER->getField('bonus')?>" data-step="10">
						<span class="quantity__minus" data-action="minus"><i class="fa fa-minus"></i></span>
						<input type="text" name="use_bonus" value="0"/>
						<span class="quantity__plus" data-action="plus"><i class="fa fa-plus"></i></span>						
					</div>				
			
				<?}else{?>

					На вашем счете еще нет бонусов.<br>Совершите первую покупку и они появятся!

				<?}?>
				
			</div>
			

		</div>
		
		<div class="cart-summary">
			<div class="cart-summary__body">
				<div class="cart-summary__title">
					Итого к оплате
				</div>
				<div class="cart-summary__value">
					<span id="sumPlusDelivery" data-orig-value="<?=round($summary['total_sum'])?>"><?=Site::formatPrice($summary['total_sum'])?></span> р.
				</div>
			</div>
		</div>
		
		<div class="cart-submit">
			<button type="submit" class="btn">Оформить заказ</button>
		</div>
		
	</form>
	
</div>

<script type="text/javascript">
	var current_payment_type = <?=intval($form->getValue('payment'))?>;
</script>