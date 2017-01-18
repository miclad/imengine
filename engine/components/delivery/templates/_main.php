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
						'passport': <?=intval($t['need_passport'])?>
					}					
				}
			<?$alr=true;}?>
		},
		{<?$alr=false;foreach($type_groups as $gr)foreach($gr['types'] as $t)foreach($t['cities'] as $c){?><?if($alr){?>,<?}?>'city<?=$c['id']?>': <?=$c['price']?><?$alr=true;}?>},
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

<?foreach($type_groups as $gr){?>

<div class="delivery-types">

	<div class="delivery-types__title">Способ доставки:</div>
	<div class="delivery-types__body">
		<select name="delivery[type]">
			<option value="">--</option>
			<?foreach($gr['types'] as $type){?>
			<option value="<?=$type['id']?>"<?if(isset($current_type[0]) && $current_type[0] == $type['id']){?> selected<?}?>><?=$type['name']?></option>
			<?}?>
		</select>
	</div>
	
	<div class="delivery-types__details">
	
			<?foreach($gr['types'] as $type){?>
						
				<div class="delivery-types__details__item" data-role="delivery-details" hidden id="deliv_info_<?=$type['id']?>">
												
						<div class="delivery-types__settings">
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
							
						</div>
									
									<div class="delivery-types__price">
										<div id="delivery_price_<?=$type['id']?>"<?if(count($type['cities']) || $type['external_calc'] != ''){?> class="delivery_price"<?}?>>						
										</div>
									</div>
								
				</div>
					
			<?}?>
			
		</div>
		
</div>
		
	<?/*
	<td>

				
				<div class="delivery_types_container" id="types<?=$gr['id']?>"<?if($current_folder != $gr['id']){?> style="display:none;"<?}?>>
				<table class="delivery_table">
					<?foreach($gr['types'] as $type){?>
					<tr>
						<td class="radio_btn"><input type="radio" name="delivery[type]" value="<?=$type['id']?>" align="absmiddle" /></td>
						<td>
						
							<div class="delivery_type_name"><?=$type['name']?></div>
					
							<div class="delivery_info" id="deliv_info_<?=$type['id']?>">							
							
								<?if(count($type['cities'])){?>
								
									<select name="delivery[city<?=$type['id']?>]">
										<option value="0"><?=$type['cities_text'] ? $type['cities_text'] : 'выберите город'?></option>
										<?foreach($type['cities'] as $city){?>
										<option value="<?=$city['id']?>"<?if(isset($current_type[1]) && $current_type[1] == $city['id']){?> selected<?}?>><?=$city['name']?></option>
										<?}?>
									</select>
								
								<?}?>
								
								<?if($type['external_calc'] != ''){?>
									<?=Page::includeComponent('delivery', array('type_id' => $type['id'], 'current_type' => $this->getConfig('current_type'), 'choose_city' => ($type['cities_text'] ? $type['cities_text'] : 'Выберите город')), array(), 'external_'.$type['external_calc'])?>
								<?}?>
								
								<div id="delivery_price_<?=$type['id']?>"<?if(count($type['cities']) || $type['external_calc'] != ''){?> class="delivery_price"<?}?> style="float:left;">						
								</div>
								
								<?if($type['title_info']){?>
									<div class="short_note">&nbsp;<?=$type['title_info']?></div>
								<?}?>
								
								<div class="clearer"></div>	
							
							</div>
							
						</td>
					</tr>
					<?}?>
				</table>
				</div>
				
	</td>
	*/?>
	
<?}?>