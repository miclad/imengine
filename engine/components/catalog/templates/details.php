<div class="product">
	
	<div class="product__box row row--cellspace">
		
		<div class="product__sidebar col-6">
			
			<div class="product__photos">
				<?if(count($product['photos'])){?>
					<div class="content-box">
						<div class="product__photos__main popup-gallery" data-role="main-photo">
							<?foreach($product['photos'] as $v){?>
								<a href="/img/catalog/more/big<?=$v['id'].'.jpg'?>"><img src="/img/catalog/more/big<?=$v['id']?>.jpg"/></a>
							<?}?>
						</div>
					</div>
					<?if(count($product['photos']) > 1){?>
						<div class="product__photos__previews row" data-role="previews">
							<?foreach($product['photos'] as $v){?>
							<div class="col-2"><a href="/img/catalog/more/big<?=$v['id']?>.jpg" data-large="/img/catalog/more/big<?=$v['id'].'.'.$v['orig_ext']?>" target="_blank" title="<?=htmlspecialchars($v['title'])?>"><img src="/img/catalog/more/sq<?=$v['id']?>.jpg"/></a></div>
							<?}?>
						</div>
					<?}?>
				<?}else{?>
					<i class="glyphicon glyphicon-camera no-photo"></i>
				<?}?>
			</div>
				
			<div class="_content-box">
				
				<?if($brand_data && $brand_data['description'] || count($certificates)){?>
					<div class="tabs tabs--links">
						<a href="#" class="s-active">Характеристики</a>
						<?if($brand_data && $brand_data['description']){?>
							<a href="#">О производителе</a>
						<?}?>
						<?if(count($certificates)){?>
							<a href="#">Документы</a>
						<?}?>
					</div>
				<?}?>
				
				<div>
				
					<div class="product__properties b-toggle" data-show="Все характеристики" data-hide="Свернуть">
						<table>
							<?if($product['brand']){?>
								<tr>
									<td>
										<div>
											<span>
												Производитель
											</span>
										</div>
									</td>
									<td><a href="/brands/<?=$product['brand']?>/"><?=$product['brand_name']?></a></td>
								</tr>
							<?}?>
							<?
							$i = 0;
							foreach($product['properties'] as $v)if($v['value'] != ''){?>
								<tr>
									<td>
										<div>
											<span>
												<?=$v['name']?>
											</span>
										</div>
									</td>
									<td><?=$v['value'] != '' ? $v['value'] : '-'?></td>
								</tr>
							<?if($i+1 == 5){?>
								</table><table class="hidden b-toggle__wrap">
							<?}?>
							<?$i++;}?>
						</table>
						<?if(count($product['properties']) > 5){?>
							<div>
								<a href="#" class="dotted b-toggle__open">Все характеристики</a>
							</div>
						<?}?>
					</div>
					
					<?if($brand_data && $brand_data['description']){?>
						<div class="hidden">
							<?=$brand_data['description']?>
						</div>
					<?}?>
					
					<?if(count($certificates)){?>
						<div class="hidden">
							<div class="product__certificates">
								<?foreach($certificates as $v){?>
									<div>
										<i class="fa fa-file-o"></i> <a href="/uploads/docs/<?=$v['file']?>" target="_blank"><?=$v['title'] ? $v['title'] : $v['file']?></a>
									</div>
								<?}?>
							</div>
						</div>
					<?}?>
					
				</div>
					
			</div>
				
		</div>

		<div class="product__mainbar col-6">
			
			<div class="product__title">
				<h1><?=$product['model']?></h1>
			</div>
			
			<?=Page::includeComponent('catalog:lens_params', array('product_id' => $product['id'], 'show_anyway' => true, 'collective_buy' => $this->getConfig('collective_buy')))?>
			
			<?
			$is_fav = Catalog::isInFavorites($product['id']);
			?>
			<div class="product__add-to-fav">
				<a href="#"<?if($USER->isAuthorized()){?> data-action="addFavorite" data-product-id="<?=$product['id']?>"<?if($is_fav){?> class="s-active" data-is-favorite="true"<?}?><?if($this->getConfig('favorites')){?> data-remove-item="true"<?}?><?}else{?> class="tooltip" data-content="<a href='/login/'>Войдите на сайт</a>, чтобы добавлять товары в избранное"<?}?>>
					<i class="fa fa-fw fa-heart<?if(!$is_fav){?>-o<?}?>"></i>
					<span>
						<?if(!$is_fav){?>
							Добавить в избранное
						<?}else{?>
							Удалить из избранного
						<?}?>
					</span>
				</a>
			</div>
			
			<?
			$is_comp = Catalog::isInCompare($product['id']);
			?>
			<div class="product__add-to-fav">
				<a href="#"<?if($USER->isAuthorized()){?> data-action="addCompare" data-product-id="<?=$product['id']?>"<?if($is_comp){?> class="s-active" data-added="true"<?}?><?}else{?> class="tooltip" data-content="<a href='/login/'>Войдите на сайт</a>, чтобы сравнивать товары"<?}?>>
					<i class="fa fa-fw fa-balance-scale<?if(!$is_comp){?> --<?}?>"></i>
					<span>
						<?if(!$is_comp){?>
							Добавить к сравнению
						<?}else{?>
							Убрать из сравнения
						<?}?>
					</span>
				</a>
			</div>
			
			<?if(true || $product['short_desc']){?>
				<div class="product__short-descr">
					<?=$product['short_desc']?>
				</div>
			<?}?>
			
			<?
			$comments = Page::includeComponent('catalog:comments', array('product_id' => $product['id'], 'limit' => 2))
			?>
			
			<div class="title-with-link">
					<h2 class="short-title">Отзывы</h2>
					<?if($comments){?>
						<a href="/catalog/<?=$product['url']?>/?show=comments" class="dotted">Посмотреть все</a>
					<?}?>
				</div>
			<?if($comments){?>
				<div class="comments-box">		

						<?=$comments?>

				</div>
				<div class="add-comment">
					<a href="/catalog/<?=$product['url']?>/?show=new_comment" class="btn btn--med">Добавить отзыв</a>
				</div>
			<?}else{?>
				<p>
					Отзывов нет
				</p>
				<a href="/catalog/<?=$product['url']?>/?show=new_comment" class="btn btn--med">Добавить отзыв</a>
			<?}?>
				
			
			
			
			
		</div>
		
	</div>
	
	<?if($product['description']){?>
		<hr/>
		<div class="product__box">
			<h2>Описание <?=$product['model']?></h2>
			<?=$product['description']?>
		</div>
	<?}?>
	
	<?if(trim($related_products)){?>
		<hr/>
		<div class="product__box">
			<h2>Сопутствующие товары</h2>
			<?=$related_products?>
		</div>
	<?}?>

</div>