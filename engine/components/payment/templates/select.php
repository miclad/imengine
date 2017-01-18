<?if(isset($list)){?>

	<div class="content-box">
	
		<h3>Способ оплаты</h3>

		<div class="delivery-types">
			<?while($v = $list->getNext()){?>
				<div class="delivery-types__item">
					<input id="payment-type-<?=$v['id']?>" type="radio" name="payment" value="<?=$v['id']?>"<?if(isset($current_type) && $current_type == $v['id']){?> checked<?}?> class="delivery-types__item__control"/>
					<label for="payment-type-<?=$v['id']?>" class="delivery-types__item__label">
						<div class="delivery-types__item__title">
							<?=$v['name']?>
						</div>
						<?if($v['short_description']){?>
							<div class="delivery-types__item__descr">
								<?=nl2br($v['short_description'])?>
							</div>
						<?}?>
					</label>
				</div>
			<?}?>
		</div>

	</div>

<?}?>