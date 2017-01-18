<script type="text/javascript">
	oDelivery.setParam('types', {
			<?$alr = false;foreach($type_groups as $gr)foreach($gr['types'] as $t){?>
				<?if($alr){?>,<?}?>
				'type<?=$t['id']?>': {
					'avail_from': <?=intval($t['avail_from'])?>,
					'external_calc': '<?=$t['external_calc']?>',
					<?if(count($t['prices'])){?>
					'prices': new Array(
					<?foreach($t['prices'] as $kp => $pr){?>
					new Array(<?=$pr['order_sum_from']?>, <?=$pr['price']?>)<?if($kp < count($t['prices'])-1){?>,<?}?>
					<?}?>
					),
					<?}?>
					'order_fields': {
						'address_post_index': <?=intval($t['need_address_post_index'])?>,
						'address_region': <?=intval($t['need_address_region'])?>,
						'address_city': <?=intval($t['need_address_city'])?>,
						'address': <?=intval($t['need_address'])?>,
						'p_name': <?=intval($t['need_p_name'])?>,
						'surname': <?=intval($t['need_surname'])?>,
						'passport': <?=intval($t['need_passport'])?>
					}					
				}
			<?$alr=true;}?>
		}
	);
	oDelivery.setParam('cities', {<?$alr=false;foreach($type_groups as $gr)foreach($gr['types'] as $t)foreach($t['cities'] as $c){?><?if($alr){?>,<?}?>'city<?=$c['id']?>': new Array(<?foreach($c['prices'] as $kp => $pr){?>{order_sum: <?=intval($pr['order_sum_from'])?>, price: <?=intval($pr['price'])?>}<?if($kp < count($c['prices'])-1){?>,<?}?><?}?>)<?$alr=true;}?>});
</script>

<?foreach($type_groups as $gr){?>
	<div class="delivery-types">

		<?foreach($gr['types'] as $type){?>
			<div class="delivery-types__item">

				<input id="delivery-type-<?=$type['id']?>" type="radio" name="delivery[type]" value="<?=$type['id']?>" <?if(isset($current_type[0]) && $current_type[0] == $type['id']){?> checked<?}?> class="delivery-types__item__control"/>

				<label class="delivery-types__item__label" for="delivery-type-<?=$type['id']?>">
					<div class="delivery-types__item__title">
						<?=$type['name']?>
					</div>
					<?if($type['short_description']){?>
						<div class="delivery-types__item__descr">
							<?=nl2br($type['short_description'])?>
						</div>
					<?}?>
				</label>



				<div class="delivery-types__item__params" hidden data-role="delivery-params" id="delivery-params-<?=$type['id']?>">

					<?if(count($type['cities'])){?>

						<select name="delivery[city<?=$type['id']?>]">
							<option value="0"><?=$type['cities_text'] ? $type['cities_text'] : '- выберите город -'?></option>
							<?foreach($type['cities'] as $city){?>
							<option value="<?=$city['id']?>"<?if(isset($current_type[1]) && $current_type[1] == $city['id']){?> selected<?}?>><?=$city['name']?></option>
							<?}?>
						</select>

					<?}?>

					<?if($type['external_calc'] != ''){?>

						<?=Page::includeComponent('delivery', array('type_id' => $type['id'], 'current_type' => $this->getConfig('current_type'), 'choose_city' => ($type['cities_text'] ? $type['cities_text'] : '- выберите город -')), array(), 'external_'.$type['external_calc'])?>

					<?}?>

					<div class="delivery-types__item__result" data-role="delivery-result" id="delivery-result-<?=$type['id']?>">
						<div class="delivery-types__item__price" id="delivery-price-<?=$type['id']?>">
						</div>
						<div class="delivery-types__item__term" id="delivery-term-<?=$type['id']?>">
						</div>
					</div>

				</div>			

			</div>
		<?}?>

	</div>
<?}?>