<?if(count($list)){?>
	<?if($parent_data && $parent_data['short_desc']){?>
		<p class="section_descr"><?=$parent_data['short_desc']?></p>
	<?}?>
	<div class="row row--cellspace">
		<?$i=0;foreach($list as $v){?>
			<div class="col-3">
				<div class="product-card product-card--category">
					<div class="product-card__inner">
						<?
						if($v['orig_ext'])
							$pict = '/img/folders/l'.$v['id'].'.jpg';
						else
							$pict = '/img/no_photo.jpg';
						?>
						<div class="catalog-grid__item__picture">
							<?if($v['orig_ext']){?>
								<a href="/catalog<?=$v['url']?>/"><img src="<?=$pict?>" class="photo_item"/></a>
							<?}else{?>
								<a href="/catalog<?=$v['url']?>/"><i class="glyphicon glyphicon-camera no-photo"></i></a>
							<?}?>
						</div>
						<div class="catalog-grid__item__title">
							<a href="/catalog<?=$v['url']?>/"><?=$v['name']?></a>
						</div>
					</div>
				</div>
			</div>
		<?$i++;}?>
	</div>
	<?if($parent_data && $parent_data['description']){?>
		<p><?=$parent_data['description']?></p>
	<?}?>
<?}?>