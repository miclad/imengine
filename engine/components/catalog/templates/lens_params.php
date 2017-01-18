<?if($has_params || $this->getConfig('show_anyway')){?>

	<div data-role="product-buy-container">

		<?if($has_params){?>
		
			<div class="lens-params__head">
				<h3>Выберите параметры линз</h3>
				<div class="tabs tabs--lens" data-no-content="true" data-role="lens-params-tabs">
					<a href="#" class="dotted s-active">Одна упаковка</a>
					<a href="#" class="dotted">Выбрать разные</a>
				</div>
			</div>

			<form id="add-to-cart-variants-<?=$this->getConfig('product_id')?>" data-product-id="<?=$this->getConfig('product_id')?>">
				
				<div class="lens-params__wrap">

					<?foreach($eyes as $k => $v){?>
					
						<div class="">

							<div class="content-box lens-params"<?if($k == 'right'){?> style="display: none;"<?}?>>

								<div class="lens-params__title">
									<?=$v?>
								</div>

								<div class="lens-params__items">

									<?if(count($values['color'])){?>
										<div class="lens-params__option lens-params__option--color">
											<span>Цвет</span>
											<select name="variants[<?=$k?>][params][color]" data-param="color">
												<?foreach($values['color'] as $i){?>
													<option value="<?=$i['color']?>"><?=$i['color_title']?></option>
												<?}?>
											</select>
										</div>
									<?}?>
									
									<?if(count($values['lens_intensity'])){?>
										<div class="lens-params__option">
											<span>Интенсивность</span>
											<select name="variants[<?=$k?>][params][lens_intensity]" data-param="lens_intensity">
												<?foreach($values['lens_intensity'] as $_k => $i){?>
													<option value="<?=$i['value']?>"<?if($i['_selected']){?> selected<?}?>><?=$i['value']?></option>
												<?}?>
											</select>
										</div>
									<?}?>

									<?if(count($values['lens_d'])){
										$_sel = false;
									?>
										<div class="lens-params__option">
											<span>Сфера</span>
											<select name="variants[<?=$k?>][params][lens_d]" data-param="lens_d">
												<?foreach($values['lens_d'] as $i){?>
													<option value="<?=$i['value']?>"<?if($i['_selected']){?> selected<?}?>><?=($i['value'] > 0 ? '+' : '').number_format($i['value'], round($i['value'], 1) == $i['value'] ? 1 : 2)?></option>
												<?}?>
											</select>
										</div>
									<?}?>
									
									<?if(count($values['lens_dia'])){?>
										<div class="lens-params__option">
											<span>Диаметр</span>
											<select name="variants[<?=$k?>][params][lens_dia]" data-param="lens_dia">
												<?foreach($values['lens_dia'] as $_k => $i){?>
												<option value="<?=$i['value']?>"<?if($i['_selected']){?> selected<?}?>><?=number_format($i['value'], round($i['value'], 1) == $i['value'] ? 1 : 2)?></option>
												<?}?>
											</select>
										</div>
									<?}?>

									<?if(count($values['lens_cyl'])){?>
										<div class="lens-params__option">
											<span>Цилиндр</span>
											<select name="variants[<?=$k?>][params][lens_cyl]" data-param="lens_cyl">
												<?foreach($values['lens_cyl'] as $_k => $i){?>
													<option value="<?=$i['value']?>"<?if($i['_selected']){?> selected<?}?>><?=$i['value']?></option>
												<?}?>
											</select>
										</div>
									<?}?>

									<?if(count($values['lens_ax'])){?>
										<div class="lens-params__option">
											<span>Ось</span>
											<select name="variants[<?=$k?>][params][lens_ax]" data-param="lens_ax">
												<?foreach($values['lens_ax'] as $_k => $i){?>
													<option value="<?=$i['value']?>"<?if($i['_selected']){?> selected<?}?>><?=round($i['value'])?></option>
												<?}?>
											</select>
										</div>
									<?}?>

									<?if(count($values['lens_bc'])){?>
										<div class="lens-params__option">
											<span>Радиус</span>
											<select name="variants[<?=$k?>][params][lens_bc]" data-param="lens_bc">
												<?foreach($values['lens_bc'] as $_k => $i){?>
												<option value="<?=$i['value']?>"<?if($i['_selected']){?> selected<?}?>><?=number_format($i['value'], round($i['value'], 1) == $i['value'] ? 1 : 2)?></option>
												<?}?>
											</select>
										</div>
									<?}?>

									<div class="lens-params__option">
										<span>Упаковок</span>
										<div class="quantity" data-min-value="1" data-trigger-on-load="true">
											<span class="quantity__minus" data-action="minus"><i class="fa fa-minus"></i></span>
											<input type="text" name="variants[<?=$k?>][quantity]" value="<?=$k == 'right' ? 0 : 1?>"/>
											<span class="quantity__minus" data-action="plus"><i class="fa fa-plus"></i></span>
										</div>
									</div>

								</div>

							</div>
							
						</div>

					<?}?>
					
				</div>

			</form>
		<?}?>

		<div class="product__buy">
			
			<?if($product['price'] > 0){?>
			
				<div class="product__summary">
					<span class="product__summary__title">Итого</span>
					<span class="price"><span data-role="product-price-value" data-price="<?=$product['price']?>"><?=Site::formatPrice($product['price'])?></span> руб.</span>
				</div>

				<a href="#" data-role="add-to-cart" data-product-id="<?=$product['id']?>" class="btn to_cart"><i class="glyphicon glyphicon-shopping-cart"></i> В корзину</a>
			
			<?}else{?>
				
				<div class="price">Звоните</div>
				
			<?}?>

		</div>
			
	</div>

<?}?>