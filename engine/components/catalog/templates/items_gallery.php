<div class="row row--cellspace">
	<?
	$i=0;
	$ipr = intval($this->getConfig('items_per_row'));
	if(!$ipr)
		$ipr = 3;
	if(!Site::isAjaxRequest() && !$this->is_inner && !$PAGE->getAttribute('sidebar')){
		$ipr = 4;
	}
	$col_size = ceil(12/$ipr);
	$url = $this->getConfig('collective_buy') ? $PAGE->getUrl().'/' : '/catalog/';
	while($product = $items->getNext()){
		$is_fav = Catalog::isInFavorites($product['id']);
		$in_compare = Catalog::isInCompare($product['id']);
	?>
		<div class="col-<?=$col_size?>">
			<div class="product-card" data-url="<?=$url.$product['url'].'/'?>">
				<div class="product-card__inner">
					
					<div class="product-card__section">
						<?=$product['section_name']?>
					</div>
					
					<div class="product-card__title">
						<a href="<?=$url.$product['url']?>/">
							<?=$product['brand_name']?> <?=$product['_model'].($product['color'] ? ' '.$product['color']: '')?><?if($product['package']){?> <span class="product-card__title__package"><?=$product['package']?></span><?}?>
						</a>
					</div>
					
					<div class="product-card__picture">
						<table><tr><td>
							<a href="<?=$url.$product['url']?>/">
								<?if($product['photo_id']){?>
									<img src="/img/catalog/more/med<?=$product['photo_id']?>.jpg" class="photo_item" alt="<?=htmlspecialchars($product['model'])?>"/>
								<?}else{?>
									<i class="glyphicon glyphicon-camera no-photo"></i>
								<?}?>
							</a>
						</td></tr></table>
					</div>				
					
					<div class="product-card__buy">
					
						<div class="product-card__price">
							<?if($product['price'] > 0){?>
								<?=Site::formatPrice($product['price'])?> <span class="ruble">р.</span>
							<?}else{?>
								Звоните
							<?}?>
						</div>

						<div class="product-card__tocart">

							<a href="#" data-role="add-to-cart" data-product-id="<?=$product['id']?>" data-check-params="true" class="btn to_cart" title="Добавить в корзину">
								<i class="fa fa-shopping-cart"></i>
							</a>

						</div>
						
					</div>
					
					<div class="product-card__compare">
						<a href="#"<?if($USER->isAuthorized()){?> data-action="addCompare" data-product-id="<?=$product['id']?>"<?if($in_compare){?> class="s-active" data-added="true"<?}}else{?> class="tooltip" data-content="<a href='/login/'>Войдите на сайт</a>, чтобы сравнивать товары"<?}?>>
							<i class="fa fa-balance-scale"></i>
						</a>
					</div>
					
					<div class="product-card__favorite">
						<a href="#"<?if($USER->isAuthorized()){?> data-action="addFavorite" data-product-id="<?=$product['id']?>"<?if($is_fav){?> class="s-active" data-is-favorite="true"<?}?><?if($this->getConfig('favorites')){?> data-remove-item="true"<?}?><?}else{?> class="tooltip" data-content="<a href='/login/'>Войдите на сайт</a>, чтобы добавлять товары в избранное"<?}?>>
							<i class="fa fa-heart<?if(!$is_fav){?>-o<?}?>"></i>
						</a>
					</div>

				</div>
			</div>
		</div>
	
		<?if(false && ($i+1) % $ipr == 0){?>
			</div><div class="row row--cellspace">
		<?}?>
	
	<?$i++;}?>
</div>