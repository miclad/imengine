<div class="content-wrap">
	<div class="container">
		<?=Page::includeComponent('banners:main', array('placing' => 'slider'))?>
	</div>
</div>

<div class="start-page">
	
	<?/*
	<div class="start-page__title">
		<h2>Топ продаж</h2>
	</div>	
	
	<?=Page::includeComponent('catalog:items', array('order_by' => 'rating desc', 'limit' => 8, 'items_per_row' => 4))?>
	*/?>
	
	<div class="content-wrap content-wrap--nobg">
		<div class="container">

			<?while($v = $sections->getNext()){?>
				<?
				$cnt = Page::includeComponent('catalog:items', array('order_by' => 'rating desc', 'limit' => 4, 'items_per_row' => 4), array('folder_id' => $v['id']));
				if(trim($cnt)){
				?>
					<div class="start-page__title">
						<h2><a href="/catalog/<?=$v['url']?>/">Популярные <?=Common::toLowerCase($v['name'])?></a></h2>
					</div>
					<?=$cnt?>
				<?}?>
			<?break;}?>
			
			<div class="row row--cellspace">
				<div class="col-9">
						
						<div class="benefits">
							
							<h2>Почему с нами так хорошо?</h2>
							
							<div class="slider" data-nav="true" data-bullets="true">
							
								<div class="row">
									<?
									$i = 0;
									while($v = $benefits->getNext()){?>
										<div class="benefits__item col-4">
											<div class="benefits__item__icon">
												<img src="/img/ab/<?=$v['file']?>">
											</div>
											<div class="benefits__item__title">
												<?=$v['title']?>
											</div>
											<div class="benefits__item__text">
												<?=nl2br($v['text'])?>
											</div>
										</div>
										<?if(($i+1) % 3 == 0 && $i < $benefits->selectedRowsCount()-1){?>
											</div><div class="row">
										<?}?>
									<?
									$i++;
									}?>
								</div>
								
							</div>
							
						</div>

				</div>
				<div class="col-3">
					<?if(count($side_banners) > 0){?>
						<?=DBCommon::showBanner($side_banners[0])?>		
					<?}?>
				</div>
			</div>
			
		</div>
	</div>
	
	<div class="content-wrap" style="background: #cdf2f2;">
		<div class="container">
	
			<?
			$i=0;
			$sections->clearCounter();
			while($v = $sections->getNext()){?>
				<?
				$i++;
				if($i < $sections->selectedRowsCount()) continue;
				$cnt = Page::includeComponent('catalog:items', array('order_by' => 'rating desc', 'limit' => 4, 'items_per_row' => 4), array('folder_id' => $v['id']));
				if(trim($cnt)){
				?>
					<div class="start-page__title">
						<h2><a href="/catalog/<?=$v['url']?>/">Популярные <?=Common::toLowerCase($v['name'])?></a></h2>
					</div>
					<?=$cnt?>
				<?}?>
			<?}?>
			
		</div>
	</div>
	
	<div class="content-wrap content-wrap--nobg">
		<div class="container">

			<div class="start-page__title">
				<h2>Новости</h2>
			</div>
			<div class="content-box">
				<?=Page::includeComponent('articles:main:gallery', array('folder_id' => $this->getConfig('news_folder'), 'limit' => 3))?>
			</div>
			
			<div class="row row--cellspace">
				<div class="col-3">
					<?if(count($side_banners) > 1){?>
						<?=DBCommon::showBanner($side_banners[1])?>		
					<?}?>
				</div>
				<div class="col-6">
					
					<div class="comments-box">
						<?=Page::includeComponent('catalog:comments', array('limit' => 3))?>
						<div class="comments-box__all">
							<a href="#">Все отзывы <i class="fa fa-long-arrow-right"></i></a>
						</div>
					</div>
					
				</div>
				<div class="col-3">
					
					<!-- VK Widget -->
					<div id="vk_groups"></div>
					<script type="text/javascript">
					VK.Widgets.Group("vk_groups", {mode: 0, width: "247", height: "320", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 51054317);
					</script>
					
				</div>
			</div>
		
		</div>
	</div>	
	
	<?/*
	<div class="content-wrap">
		<div class="container">
	
			<div class="start-page__widget start-page__promo">
				<?=Page::includeComponent('banners:main', array('placing' => 'wide'))?>
			</div>
			
		</div>
	</div>

	 * 
	 */?>	
	
	<div class="content-wrap content-wrap--nobg" style="background: #cdf2f2;">
		<div class="container">

			<div class="start-page__title">
				<h2>Весь каталог</h2>
			</div>
			<?=Page::includeComponent('catalog:sections', array('items_per_page' => 24))?>
		
		</div>
	</div>
	
	<div class="content-wrap">
		<div class="container">

			<?if(count($text_array)){?>

				<div class="start-page__title">
					<h2>О магазине</h2>
				</div>

				<div class="row row--cellspace">
					<?
					$size = ceil(12/count($text_array));
					?>
					<?foreach($text_array as $t){?>
						<div class="col-<?=$size?>">
							<?=$t?>
						</div>
					<?}?>
				</div>

			<?}?>
			
		</div>
	</div>

</div>