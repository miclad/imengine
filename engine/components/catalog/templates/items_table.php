<div class="catalog_items">
			<?$i=0;while($product = $items->getNext()){?>
				<div class="item">
					<table>
					<tr>
						<td class="pict">
							<a href="/catalog/<?=$product['url']?>/">
								<?if($product['photo_id']){?>
									<img src="/img/catalog/more/med<?=$product['photo_id']?>.jpg" class="photo_item" alt="<?=htmlspecialchars($product['model'])?>"/>
								<?}else{?>
									<i class="glyphicon glyphicon-camera no-photo"></i>
								<?}?>
							</a>
						</td>
						<td class="info">
							<div class="ttl">
								<a href="/catalog/<?=$product['url']?>/"><?=$product['model']?></a><br>
							</div>	
							<?=$product['short_desc']?>
						</td>
						<td class="price_and_cart">
							<div class="price">
								<?if($product['price'] > 0){?>
									<?=Site::formatPrice($product['price'])?> руб.
								<?}else{?>
									Звоните
								<?}?>
							</div>
							<a href="#" data-role="add-to-cart" data-product-id="<?=$product['id']?>" class="btn to_cart">
								<i class="glyphicon glyphicon-shopping-cart"></i> в корзину
							</a>
						</td>
					</tr>
					</table>
				</div>
			<?$i++;}?>
		</div>