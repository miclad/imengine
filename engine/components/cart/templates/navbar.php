<div class="cart-navbar affix" data-container=".page__body">
	<?foreach($items as $k => $v){
		$show_link = $k == 0 && Site::isCurrentPage($items[1]['url']);
	?>
		<div class="cart-navbar__item<?if(Site::isCurrentPage($v['url'])){?> s-active<?}?>">
			<?if($show_link){?>
				<a href="<?=$v['url']?>">
			<?}?>
			<div class="cart-navbar__item__icon">
				<?foreach($v['icon'] as $i){?>
					<span><i class="fa fa-<?=$i?>"></i></span>
				<?}?>
			</div>
			<div class="cart-navbar__item__title">
				<?=$v['title']?>
			</div>
			<?if($show_link){?>
				</a>
			<?}?>
		</div>
	<?}?>
</div>