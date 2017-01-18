<?if(!Site::isAjaxRequest()){?>
<script type="text/javascript" src="/js/idelivery.js?<?=filemtime(B_DIR.'js/idelivery.js')?>"></script>
<script type="text/javascript" src="http://pickpoint.ru/select/postamat.js"/></script>
<script type="text/javascript">

	var oDelivery = new iDelivery(
		<?=intval($this->getInitParam('order_sum'))?>,// order sum
		<?=intval($this->getInitParam('order_sum_full'))?>, // order sum without discount
		{
			<?$alr = false;foreach($type_groups as $gr)foreach($gr['types'] as $t){?>
				<?if($alr){?>,<?}?>
				'type<?=$t['id']?>': {
					'avail_from': <?=intval($t['avail_from'])?>,
					'external_calc': '<?=$t['external_calc']?>'
					<?if(count($t['prices'])){?>,
					'prices': new Array(
					<?foreach($t['prices'] as $kp => $pr){?>
					new Array(<?=$pr['order_sum_from']?>, <?=$pr['price']?>)<?if($kp < count($t['prices'])-1){?>,<?}?>
					<?}?>
					)
					<?}?>
				}
			<?$alr=true;}?>
		},
		{<?$alr=false;foreach($type_groups as $gr)foreach($gr['types'] as $t)foreach($t['cities'] as $c){?><?if($alr){?>,<?}?>'city<?=$c['id']?>': new Array(<?foreach($c['prices'] as $kp => $pr){?>{order_sum: <?=intval($pr['order_sum_from'])?>, price: <?=intval($pr['price'])?>}<?if($kp < count($c['prices'])-1){?>,<?}?><?}?>)<?$alr=true;}?>},
		<?=str_replace(',', '.', floatval($this->getConfig('weight') ? $this->getConfig('weight') : 1))?>
	);
	
	sumChangeHandler = oDelivery;
	
	$(document).ready(
		function(){
			oDelivery.init();
			<?if(isset($current_type[0]) && $current_type[0]){?>
			oDelivery.typeChanged(<?=$current_type[0]?>);
			<?}?>
		}
	);
	

</script>
<?}?>

<input type="hidden" name="delivery_ok" value=""/>
<input type="hidden" name="delivery_error" value=""/>
<input type="hidden" name="delivery_type_str" value="<?=$this->getConfig('current_type')?>"/>
<input type="hidden" name="delivery[price]" value="0"/>
	
<div class="form__item" id="order-delivery-type">
		<label class="form__item__title">Способ доставки</label>
		<div class="form__item__input">
			
			<select name="delivery[type]" class="form-control">
				<?foreach($type_groups as $gr){?>
					<?if($gr['name']){?><optgroup label="<?=$gr['name']?>"><?}?>
						<option value="">- без доставки -</option>
						<?foreach($gr['types'] as $type){?>
							<option value="<?=$type['id']?>"<?if(isset($current_type[0]) && $current_type[0] == $type['id']){?> selected<?}?>><?=$type['name']?></option>
						<?}?>
					<?if($gr['name']){?></optgroup><?}?>
				<?}?>
			</select>

		</div>
</div>
	
<?foreach($gr['types'] as $type){?>
	
		<div data-role="delivery-params" id="delivery-params-<?=$type['id']?>" hidden>
											
			<?if(count($type['cities'])){?>
				<div class="form__item">
					<label class="form__item__title"><?=$type['cities_text'] ? $type['cities_text'] : 'Город'?></label>
					<div class="form__item__input">
						<select name="delivery[city<?=$type['id']?>]" class="form-control">
							<option value="0">--</option>
							<?foreach($type['cities'] as $city){?>
								<option value="<?=$city['id']?>"<?if(isset($current_type[1]) && $current_type[1] == $city['id']){?> selected<?}?>><?=$city['name']?></option>
							<?}?>
						</select>
					</div>
				</div>
			<?}?>
									
			<?if($type['external_calc'] != ''){?>
				<div class="form__item">
					<div class="form__item__input">
						<?=Page::includeComponent('delivery', array('type_id' => $type['id'], 'current_type' => $this->getConfig('current_type'), 'choose_city' => ($type['cities_text'] ? $type['cities_text'] : '- выберите город -')), array(), 'external_'.$type['external_calc'])?>
					</div>
				</div>
			<?}?>
			
			<div class="form__item" id="order-delivery-type">
				<label class="form__item__title">Стоимость доставки</label>
				<div class="form__item__input only-value">
					<span id="delivery-price-<?=$type['id']?>"><span>
				</div>
			</div>
								
		</div>
					
<?}?>