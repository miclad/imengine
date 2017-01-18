<?
$prop_limit = 5;
$item_height = 21;
?>

	
	<form action="<?if($this->getConfig('collective_buy')){?>/collective/<?}elseif(isset($brand_data)){?>/brands/<?=$brand_data['url']?>/<?}elseif($folder_id){?>/catalog<?=$folder_data['url']?>/<?}else{?>/catalog/<?if($product_ids && count($product_ids))?>favorites/<?}?>" method="get" id="filter-form">
		
		<?if(isset($_GET['search'])){?>
			<input type="hidden" name="a" value="search"/>
			<input type="hidden" name="search" value="<?=htmlspecialchars($_GET['search'])?>"/>
		<?}?>

		<div class="b-filter">
			
			<div class="b-filter__body">

				<?/*
				<div class="b-filter__group">
					<label>
						<?
						$checked = isset($_GET['avail']) && $_GET['avail'] == 'y';//!isset($_SESSION['show_unavail_products']);
						?>
						<input type="hidden" name="avail" value="all"/>
						<input type="checkbox" name="avail" value="y"<?if($checked){?> checked<?}?>/>
						Только товары в наличии
					</label>
				</div>
				*/?>
				
				<?if($price_max['price'] > 0){?>
						<div class="b-filter__group">
							<div class="b-filter__group__head">Цена</div>
							<div class="b-filter__group__body">
								<div class="f-slider" data-min="<?=$price_min['price']?>" data-max="<?=$price_max['price']?>" data-step="1" data-from="<?=isset($_GET['filter']['price_min']) ? intval($_GET['filter']['price_min']) : $price_min['price']?>" data-to="<?=isset($_GET['filter']['price_max']) ? intval($_GET['filter']['price_max']) : $price_max['price']?>">
									<div class="f-slider__range"></div>
									<div class="f-slider__from">
										<?/*
										<span><?=Site::formatPrice(isset($_GET['filter']['price_min']) ? intval($_GET['filter']['price_min']) : $price_min['price'])?></span> руб.
										*/?>
										<input type="text" class="f-field" name="filter[price_min]" value="<?=isset($_GET['filter']['price_min']) ? intval($_GET['filter']['price_min']) : $price_min['price']?>">
										&mdash;
									</div>
									<div class="f-slider__to">
										<?/*
										<span><?=Site::formatPrice(isset($_GET['filter']['price_max']) ? intval($_GET['filter']['price_max']) : $price_max['price'])?></span> руб.
										*/?>
										<input type="text" class="f-field" name="filter[price_max]" value="<?=isset($_GET['filter']['price_max']) ? intval($_GET['filter']['price_max']) : $price_max['price']?>"> руб.
									</div>
								</div>
							</div>
						</div>
				<?}?>

				<?if(isset($brand_data) && $brand_data){?>

					<?if(isset($sections) && count($sections) > 1){?>

						<?$ch = isset($_GET['brand'])?>

						<div class="b-filter__group">   
							<div class="b-filter__group__head">Категории</div>
							<div class="b-filter__group__body">

							<?/*
							<div class="f-search">
								<input type="text" class="f-field" placeholder="Поиск по брендам">
								<button type="submit">
									<span class="i-icon i-icon--search"></span>
								</button>
							</div><!-- f-search -->
							*/?>

							<ul class="b-nav">
								<?$i=0;foreach($sections as $v){?>
									<?
									$checked = isset($_GET['filter']['section']) && (is_array($_GET['filter']['section']) && in_array($v['id'], $_GET['filter']['section']));
									?>
									<li>
										<label>
											<input type="checkbox" name="filter[section][]" value="<?=$v['id']?>"<?if($checked){?> checked<?}?>/>
											<?=$v['name']?>
										</label>
									</li>

									<?if($i == $prop_limit-1 && $i < count($sections)-1){?>
										</ul>

										<div class="b-toggle" data-hide="Скрыть" data-show="Показать все">
											<div class="b-toggle__wrap">
												<ul class="b-nav">
									<?}?>

								<?$i++;}?>

								<?if($prop_limit && count($sections) > $prop_limit){?>
											</ul>
										</div>
										<a href="#" class="b-toggle__open dotted">Показать все</a>
									</div>
								<?} else {?>
									</ul>
								<?}?>

							</div>					

						</div><!-- b-filter__group -->

					<?}?>

				<?}else{?>

					<?$sections = '';//Page::includeComponent('catalog:sections:sections_submenu', array('parent' => $this->getConfig('section_parent')))?>
					<?if($sections != ''){?>
					<div class="b-filter__group b-filter__group--navbar">

						<div class="b-filter__group__body">
							<ul>
								<?=$sections?>
							</ul>
						</div>

					</div>
					<?}?>

					<?if(isset($brands) && count($brands) > 1){?>

						<?$ch = isset($_GET['brand'])?>

						<div class="b-filter__group">   
							<div class="b-filter__group__head">Производители</div>
							<div class="b-filter__group__body">

							<ul class="b-nav">
								<?$i=0;foreach($brands as $v){?>
									<?
									$checked = isset($_GET['brand']) && (is_numeric($_GET['brand']) && $_GET['brand'] == $v['id'] || is_array($_GET['brand']) && in_array($v['id'], $_GET['brand']));
									?>
									<li>
										<label>
											<input type="checkbox" name="brand[]" value="<?=$v['id']?>"<?if($checked){?> checked<?}?>/>
											<?=$v['name']?>
										</label>
									</li>

									<?if($i == $prop_limit-1 && $i < count($brands)-1){?>
										</ul>

										<div class="b-toggle" data-hide="Скрыть" data-show="Показать все">
											<div class="b-toggle__wrap">
												<ul class="b-nav">
									<?}?>

								<?$i++;}?>

								<?if($prop_limit && count($brands) > $prop_limit){?>
											</ul>
										</div>
										<a href="#" class="b-toggle__open dotted">Показать все</a>
									</div>
								<?} else {?>
									</ul>
								<?}?>

							</div>					

						</div><!-- b-filter__group -->

					<?}?>

					<?foreach($properties as $prop){?>

						<?if(isset($prop['variants']) && $prop['variants']->selectedRowsCount() > 1){?>

							<?if($prop['variants']->selectedRowsCount()){?>

								<?
								$ch = isset($_GET['filter']['prop'][$prop['id']]);
								?>

								<div class="b-filter__group">

									<?if(false && $ch){?>
										<div class="b-filter__reset">
											<?$remove = array(urlencode('filter[prop]['.$prop['id'].'][]'));?>
											<a href="<?=Common::editUrl($remove, array())?>" class="b-remove">
												<span>Очистить</span>
											</a>
										</div>
									<?}?>

									<div class="b-filter__group__head"><?=str_replace(':', '', $prop['filter_title'] ? $prop['filter_title'] : $prop['name'])?></div>
									<div class="b-filter__group__body">

										<ul class="b-nav">
											<?$i=0;while($v = $prop['variants']->getNext()){?>
												<?
												$checked = isset($_GET['filter']['prop'][$prop['id']]) && (is_numeric($_GET['filter']['prop'][$prop['id']]) && $_GET['filter']['prop'][$prop['id']] == $v['id'] || is_array($_GET['filter']['prop'][$prop['id']]) && in_array($v['id'], $_GET['filter']['prop'][$prop['id']]));
												?>
												<li>
													<label>
														<input type="checkbox" name="filter[prop][<?=$prop['id']?>][]" value="<?=$v['id']?>"<?if($checked){?> checked<?}?>/>
														<?=$v['name']?>
													</label>
												</li>

												<?if($i == $prop_limit-1 && $i < $prop['variants']->selectedRowsCount()-1){?>
													</ul>

													<div class="b-toggle" data-hide="Скрыть" data-show="Показать все">
														<div class="b-toggle__wrap">
															<ul class="b-nav">
												<?}?>

										<?$i++;}?>

										<?if($prop_limit && $prop['variants']->selectedRowsCount() > $prop_limit){?>
													</ul>
												</div>
												<a href="#" class="b-toggle__open dotted">Показать все</a>
											</div>
										<?}else{?>
											</ul>
										<?}?>

									</div>					

								</div><!-- b-filter__group -->

							<?}?>

						<?}elseif($prop['type'] == 'number'){?>

							<div class="b-filter__group">
								<div class="b-filter__group__head"><?=$prop['name']?></div>
								<div class="b-filter__group__body">
									<div class="f-slider" data-min="<?=$prop['values']['min_val']?>" data-max="<?=$prop['values']['max_val']?>" data-step="100" data-from="<?=isset($_GET['filter']['prop'][$prop['id']]['min']) ? intval($_GET['filter']['prop'][$prop['id']]['min']) : $prop['values']['min_val']?>" data-to="<?=isset($_GET['filter']['prop'][$prop['id']]['max']) ? intval($_GET['filter']['prop'][$prop['id']]['max']) : $prop['values']['max_val']?>">
										<div class="f-slider__range"></div>
										<div class="f-slider__from">
											<?/*
											<span><?=Site::formatPrice(isset($_GET['filter']['prop'][$prop['id']]['min']) ? intval($_GET['filter']['prop'][$prop['id']]['min']) : $prop['values']['min_val'])?></span> руб.
											*/?>
											<input type="text" class="f-field" name="filter[prop][<?=$prop['id']?>][min]" value="<?=isset($_GET['filter']['prop'][$prop['id']]['min']) ? intval($_GET['filter']['prop'][$prop['id']]['min']) : $prop['values']['min_val']?>">
										</div>
										<div class="f-slider__to">
											<?/*
											<span><?=Site::formatPrice(isset($_GET['filter']['prop'][$prop['id']]['max']) ? intval($_GET['filter']['prop'][$prop['id']]['max']) : $prop['values']['max_val'])?></span> руб.
											*/?>
											<input type="text" class="f-field" name="filter[prop][<?=$prop['id']?>][max]" value="<?=isset($_GET['filter']['prop'][$prop['id']]['max']) ? intval($_GET['filter']['prop'][$prop['id']]['max']) : $prop['values']['max_val']?>">
										</div>
									</div>
								</div>
							</div>

						<?}?>

					<?}?>

				<?}?>

				<div class="b-filter__group">
					<button type="submit" class="btn">Показать</button>
				</div>
				
			</div>

		</div><!-- b-filter -->

		<?if(isset($_GET['category']) && is_array($_GET['category'])){?>
			<?foreach($_GET['category'] as $v){?>
				<input type="hidden" name="category[]" value="<?=intval($v)?>"/>
			<?}?>
		<?}?>

	</form>