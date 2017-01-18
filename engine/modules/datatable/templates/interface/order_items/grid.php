<?
$total_sum = $total_weight = 0;
?>

</div>

<div class="content-box">
	
	<h1>Товары</h1>

	<div class="grid order-items">
		<table class="table table-hover">
			<?if(count($products)){?>
			<thead>
				<th>Наменование</th>
				<th>Цена</th>
				<th>Кол-во</th>
				<th>Стоимость</th>
				<th></th>
			</thead>
			<?}?>
			<?foreach($products as $k => $product){?>
				<?
				$products[$k]['price'] = round($product['price']*(1-$product['discount']/100));
				$url = $dt->baseUrl.'&show=element_form&id='.$product["entry_id"];
				$variant_title = Catalog::buildLensTitle($product);
				?>
				<tr class="datarow with_url"<?if($url){?> data-url="<?=$url?>"<?}?>>
					<td>
						<?=$product['brand_name'].' '.$product['model']?>
						<?if($product['package']){?>(<?=$product['package']?>)<?}?>
						<?if($product['article']){?><span class="order-items__article">(арт. <?=$product['article']?>)</span><?}?>
						<?if($variant_title){?>
							<br><span class="order-items__variant"><?=$variant_title?></span>
						<?}?>
					</td>
					<td><?=Site::formatPrice($products[$k]['price'])?> руб.</td>
					<td><?=$product['items_num']?> шт.</td>
					<?
					$sum = ($products[$k]['price'] * $product['items_num']);
					?>
					<td><?=Site::formatPrice($sum)?> руб.</td>
					<td class="control">
						<?if(AuthUser::getInstance()->hasAccess('datatable_'.$check_access_entity, 'w')){?>
							<a href="<?=$dt->baseUrl?>&act=delete&id=<?=$product["entry_id"]?><?=isset($_GET['page']) ? '&page='.intval($_GET['page']) : ''?>" class="glyphicon glyphicon-remove" title="Удалить"></a>
						<?}?>
					</td>
					<?$total_sum += $sum;$total_weight += $product['weight']*$product['items_num']?>
				</tr>	
			<?}?>
			<tfoot>
				<tr class="with-border">
					<td colspan="3">Товаров на сумму:</td>
					<td colspan="2"><?=Site::formatPrice($order_data['sum'])?> руб.</td>
				</tr>
				<tr>
					<td colspan="3">Скидка:</td>
					<td colspan="2"><?=$order_data['discount_value']?>%</td>
				</tr>
				<tr>
					<td colspan="3">Итого с учетом скидки:</td>
					<td colspan="2"><?=Site::formatPrice($order_data['sum_discount'])?> руб.</td>
				</tr>
				<tr>
					<td colspan="3" style="border-bottom:none;">Итого с доставкой:</td>
					<td colspan="2" style="border-bottom:none;"><?=Site::formatPrice($order_data['sum_discount']+$order_data['delivery_price'])?> руб.</td>
				</tr>
				<?/*
				<tr>
					<td colspan="3">Оплачено баллами:</td>
					<td colspan="2">
						<?=$order_data['use_bonus']?> руб.
						<input type="hidden" name="use_bonus" value="<?=$order_data['use_bonus']?>"/>
					</td>
				</tr>
				<tr>
					<td colspan="3">Итого к оплате:</td>
					<td colspan="2"><span id="sumPlusDelivery"><?=Site::formatPrice($order_data['sum_discount']+$order_data['delivery_price']-$order_data['use_bonus'])?></span> руб.</td>
				</tr>
				<tr class="with-border">
					<td colspan="3">Себестоимость:</td>
					<td colspan="2">
						<?
						$cost_price = Orders::calculateCostPrice($order_data);
						?>
						<?=Site::formatPrice($cost_price)?> руб.
					</td>
				</tr>
				<tr>
					<td colspan="3">Выручка:</td>
					<td colspan="2">
						<?=Site::formatPrice($order_data['sum_discount']-$cost_price - $order_data['use_bonus'])?> руб.
					</td>
				</tr>
				 * 
				 */?>
			</tfoot>
		</table>
	</div>
	
</div>

<?if($order_data['payed'] > 0){?>
	<div class="content-box">
		<b>Оплачен</b>
	</div>
<?}?>

<div class="row">
	
	<div class="col-6">
	
		<div class="content-box">

			<h1>Информация о заказчике</h1>

			<form class="form" role="form" action="" method="post" name="order_data">

					<input type="hidden" name="id" value="<?=$order_data['id']?>"/>
					<input type="hidden" name="update_data" value="1"/>
					
					<?
					$fields = $dt_orders->getFormFields();
					?>
					
					<?foreach($fields as $k => $f){?>
		
						<?
						$field_template = B_DIR.'/engine/modules/datatable/templates/interface/'.$dt_orders->entity.'/field_'.$k.'.php';
						if(file_exists($field_template)){
							require $field_template;
						}else{
						?>

							<div class="form__item" id="field_<?=$k?>">
								<label for="formField<?=$k?>" class="form__item__title col-md-3"><?=$f['title']?></label>
								<div class="form__item__input">
									<?=$f['html']?>
								</div>
							</div>

						<?}?>
					
					<?}?>

					<div class="form__item form-submit">
						<div class="form__item__input col-lg-offset-3">
							<button type="submit" class="btn btn-primary btn-md">
								<span class="glyphicon glyphicon-ok"></span> Сохранить
							</button>
						</div>
					</div>

			</form>

		</div>
		
	</div>
	
	<div class="col-6">

		<div class="content-box">

			<?//$discount = Catalog::getDiscount($total_sum, intval($order_data['discount_card']), $products)?>

			<h1>Доставка и оплата</h1>

			<form class="form" action="" method="post">
					<input type="hidden" name="order_id" value="<?=$order_data['id']?>"/>
					<input type="hidden" name="update_delivery" value="1"/>
					<?=Page::includeComponent('delivery', array('admin_mode' => true, 'title' => 'Доставка', 'current_type' => $order_data['delivery_type'], 'weight' => $total_weight), array('order_sum_full' => $order_data['sum'], 'order_sum' => ($order_data['sum_discount'] ? $order_data['sum_discount'] : $order_data['sum'])), 'main', 'admin')?>
					<div class="form__item" id="field_delivery_tracking_number">
						<label for="formField_delivery_tracking_number" class="form__item__title">Номер отправления</label>
						<div class="form__item__input">
							<input type="text" name="delivery_tracking_number" value="<?=isset($order_data['delivery_tracking_number']) ? htmlspecialchars($order_data['delivery_tracking_number']) : ''?>" class="form-control" id="formField_delivery_tracking_number"/>
						</div>
					</div>
					<div id="payment_types"></div>
					<div class="form__item form-submit">
						<div class="form__item__input col-lg-offset-3">
							<button type="submit" class="btn btn-primary btn-md">
								<span class="glyphicon glyphicon-ok"></span> Сохранить
							</button>
						</div>
					</div>
			</form>

		</div>
		
		<div class="content-box">

			<h1>Статус заказа</h1>

			<form class="form no-submit" role="form" action="" method="post" name="order_status">
					
					<input type="hidden" name="id" value="<?=$order_data['id']?>"/>
					<input type="hidden" name="update_data" value="1"/>

					<div class="form__item" id="field_status">
						<label for="formFieldstatus" class="form__item__title col-lg-3">Статус</label>
						<div class="form__item__input">
							<select name="status" class="form-control" id="formFieldstatus" data-value="<?=$order_data['status']?>">
								<option value="0">--</option>
								<?foreach($statuses_common as $v){?>
									<option value="<?=$v['id']?>"<?if($v['id'] == $order_data['status']){?> selected<?}?>><?=$v['name']?></option>
								<?}?>
							</select>
							<?if($tsmp_statuses['common']){?>
								<span class="field-descr">
									Изменен <?=Site::formatDBDate($tsmp_statuses['common'], true, true, ' в ')?>
								</span>
							<?}?>
						</div>
					</div>
					
					<div class="form__item" id="field_status_comment" hidden>
						<label for="formFieldstatus_comment" class="form__item__title col-lg-3">Комментарий</label>
						<div class="form__item__input">
							<textarea name="status_comment" class="form-control" id="formFieldstatus_comment" placeholder=""></textarea>
						</div>
					</div>
					
					<div class="form__item" id="field_status_payment">
						<label for="formFieldstatus_payment" class="form__item__title col-lg-3">Статус оплаты</label>
						<div class="form__item__input">
							<select name="status_payment" class="form-control" id="formFieldstatus_payment" data-value="<?=$order_data['status_payment']?>">
								<option value="0">--</option>
								<?foreach($statuses_payment as $v){?>
									<option value="<?=$v['id']?>"<?if($v['id'] == $order_data['status_payment']){?> selected<?}?>><?=$v['name']?></option>
								<?}?>
							</select>
							<?if($tsmp_statuses['payment']){?>
								<span class="field-descr">
									Изменен <?=Site::formatDBDate($tsmp_statuses['payment'], true, true, ' в ')?>
								</span>
							<?}?>
						</div>
					</div>
					
					<div class="form__item" id="field_status_comment_payment" hidden>
						<label for="formFieldstatus_payment_comment" class="form__item__title col-lg-3">Комментарий</label>
						<div class="form__item__input">
							<textarea name="status_payment_comment" class="form-control" id="formFieldstatus_payment_comment" placeholder=""></textarea>
						</div>
					</div>

					<div class="form__item form-submit" id="form_status_submit" hidden>
						<div class="form__item__input col-lg-offset-3">
							<button type="submit" class="btn btn-primary btn-md">
								<span class="glyphicon glyphicon-ok"></span> Сохранить
							</button>
						</div>
					</div>

			</form>
			
			<?if(count($statuses_history)){?>
			
				
				<table class="table table-hover order-status-history toggle">
					<thead>
						<th colspan="2" class="form__item__input">							
							<a href="#" class="toggle__open btn"><i class="glyphicon glyphicon-calendar"></i> История статусов</a>
						</th>
					</thead>
					<tbody class="toggle__wrap">
						<?foreach($statuses_history as $v){?>				
							<tr class="datarow">
								<td class="col-lg-3">
									<span class="order-status-history__date"><?=Site::formatDBDate($v['tsmp'], true, false)?></span>
								</td>
								<td class="form__item__input">
									<div>
										<?=$v['name']?>										
									</div>
									<?if($v['comment']){?>
										<div class="order-status-history__comment"><?=$v['comment']?></div>
									<?}?>
								</td>
							</tr>					
						<?}?>
					</tbody>
				</table>
				

			<?}?>

		</div>
		
	</div>

</div>
	
<script type="text/javascript">
	
	var current_payment_type = <?=intval($order_data['payment'])?>;
	
	$(document).ready(function(){
		
		$('select[name="status"]').add('select[name="status_payment"]').on('change', function(){
			
			var fields = $(this).parents('.form__item').next();
			
			if($(this).data('value') == $(this).val()){
				fields.hide();
				if($('select[name="status"]').data('value') == $('select[name="status"]').val() && $('select[name="status_payment"]').data('value') == $('select[name="status_payment"]').val()){
					$(this).parents('form').addClass('no-submit');
					$('#form_status_submit').hide();
				}
			}else{
				fields.show();
				$(this).parents('form').removeClass('no-submit');
				$('#form_status_submit').show();
			}
			
		});
		
	});
	
</script>
