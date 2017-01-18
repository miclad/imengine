<?if(count($ids)){?>

	<div class="catalog-compare">

		<table>
			<tr>
				<td><div></div></td>
				<?foreach($items as $product){?>
				<td>
					<div class="catalog-compare__product">
						<div class="catalog-compare__product__picture">
							<?if($product['photo_id']){?>
								<a href="/catalog/<?=$product['url']?>/"><img src="/img/catalog/more/med<?=$product['photo_id']?>.jpg" class="photo_item" alt="<?=htmlspecialchars($product['model'])?>"/></a>
							<?}else{?>
								<i class="glyphicon glyphicon-camera no-photo"></i>
							<?}?>
						</div>
						<div class="catalog-compare__product__title">
							<a href="/catalog/<?=$product['url']?>/"><?=$product['brand_name'].' '.$product['model'].' '.$product['package']?></a>
						</div>
					</div>
				</td>
				<?}?>
			</tr>
			<?foreach($properties as $prop){?>
				<tr>
					<td>
						<?=$prop['name']?>
					</td>
					<?foreach($items as $product){?>
						<td>
							<?if(isset($product['_properties'][$prop['id']]) && $product['_properties'][$prop['id']]['value'] != ''){?>
								<?=$product['_properties'][$prop['id']]['value']?>
							<?}else{?>
								&ndash;
							<?}?>
						</td>
					<?}?>
				</tr>
			<?}?>
			<tr>
				<td><em>Удалить</em></td>
				<?foreach($items as $product){?>
					<td>
						<a href="#" data-action="removeCompare" data-remove-item="true"data-product-id="<?=$product['id']?>"><i class="fa fa-remove"></i></a>
					</td>
				<?}?>
			</tr>
		</table>

	</div>

<?}?>

<div id="compare-empty"<?if(count($items)){?> style="display: none;"<?}?>>
	<?=$this->getConfig('compare_empty_text')?>
</div>
