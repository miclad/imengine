<?if(!$items->selectedRowsCount()){?>
	Товары не найдены
<?}?>

<div class="catalog_rating">

	<?$i=0;while($product = $items->getNext()){?>
		<div class="item<?if($i == 0){?> first<?}?>">
		
			<table>
				<tr>
					<td class="pict">
					
						<?
						$pict = $i == 0 ? 'm'.$product['id'].'.jpg' : 'l'.$product['id'].'.jpg';
						?>
						<?if($product['orig_ext']){?>
							<div class="rel_container">
								<a href="/catalog<?=$product['folder_url']?>/<?=$product['url']?>/"><img src="/img/catalog/<?=$pict?>"/></a>
								<?if($i < 3){?>
									<div class="rating_place place<?=$i+1?>"><?=$i+1?></div>
								<?}?>
							</div>
						<?}?>
					
					</td>
					<td class="cnt">
					
						<div class="info">
							<h3><a href="/catalog<?=$product['folder_url']?>/<?=$product['url']?>/"><?=$product['brand_name'].' '.$product['model']?></a></h3>
							<?=$product['short_desc']?>
						</div>
						<?if($product['brand_logo']){?>
							<div class="brand_logo">
								<img src="/img/brands/<?=$product['brand_logo']?>"/>
							</div>
						<?}?>
						
						<div class="clearer"></div>
						
						<div class="packages">
							<table>
								<tr>
									<td>
										<?=$product['package']?>
									</td>
									<td class="in_stock">
										в наличии
									</td>
									<td class="price">
										<?=Site::formatPrice($product['price'])?>.&ndash;
									</td>
									<td class="tcrt">
										<a onclick="addToCart(<?=$product['id']?>); return false" href="#" class="tocart">В корзину</a>
									</td>
								</tr>
							</table>		
						</div>
					
					</td>
				</tr>
			</table>			
			
		</div>
		
		<?if($i+1 == intval($this->getConfig('with_limiter'))){?>
			<div class="hidden" id="rating_items">
		<?}?>
		
	<?$i++;}?>
	
	<?if($items->selectedRowsCount() > intval($this->getConfig('with_limiter'))){?>
		</div>
		<div class="rating_show_all">
			<a onclick="return toggleBlock('rating_items', this, true, null, 'height', 'скрыть', 'показать еще');" href="#" class="js_link">показать еще</a>
		</div>
	<?}?>
	
</div>