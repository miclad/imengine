<div class="start-page">
	
	<div class="container">
		
		<div class="content-wrap">
			<div class="row">
				<div class="col-9">
					<?=Page::includeComponent('banners:main', array('placing' => 'slider'))?>
				</div>
				<div class="col-3">
					<?=Page::includeComponent('banners:main', array('placing' => 'near_slider', 'limit' => 1, 'order_by' => 'rand()'))?>
				</div>
			</div>
		</div>
		
		<div class="content-wrap">			
			
			<div class="title-with-link">
				<h2 class="short-title">Новости</h2>
				<a href="/news/" class="dotted">Все новости</a>
			</div>
			<?=Page::includeComponent('articles:main:gallery', array('folder_id' => $this->getConfig('news_folder'), 'limit' => 4))?>
			
		</div>
		
		<?if(count($sections)){?>
			<?
			$cnt = Page::includeComponent('catalog:items', array('order_by' => 'rating desc', 'limit' => 4, 'items_per_row' => 4), array('folder_id' => $sections[0]['id']));
			if(trim($cnt)){
			?>
				<div class="content-wrap">		
					<div class="title-with-link">
						<h2>Популярные <?=Common::toLowerCase($sections[0]['name'])?></h2>
						<a href="/catalog/<?=$sections[0]['url']?>/" class="dotted">Перейти в раздел</a>
					</div>
					<?=$cnt?>		
				</div>		
			<?}?>
		<?}?>
		
		<div class="content-wrap">
			
			<div class="benefits">
				<div class="slider" data-nav="true" data-bullets="false" data-arrows="true">
							
								<div class="row">
									<?
									$i = 0;
									while($v = $benefits->getNext()){?>
										<div class="benefits__item col-4">
											<div class="benefits__item__icon">
												<img src="/img/ab/<?=$v['file']?>">
											</div>
											<div class="benefits__item__body">
												<div class="benefits__item__title">
													<?if($v['link']){?><a href="<?=$v['link']?>"><?}?>
														<?=$v['title']?>
													<?if($v['link']){?></a><?}?>
												</div>
												<div class="benefits__item__text">
													<?=nl2br($v['text'])?>
												</div>
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
		
		<?if(count($sections) > 1){?>
			<?
			$cnt = Page::includeComponent('catalog:items', array('order_by' => 'rating desc', 'limit' => 4, 'items_per_row' => 4), array('folder_id' => $sections[1]['id']));
			if(trim($cnt)){
			?>
				<div class="content-wrap">		
					<div class="title-with-link">
						<h2>Популярные <?=Common::toLowerCase($sections[1]['name'])?></h2>
						<a href="/catalog/<?=$sections[1]['url']?>/" class="dotted">Перейти в раздел</a>
					</div>
					<?=$cnt?>		
				</div>		
			<?}?>
		<?}?>
		
		<div class="content-wrap">
			<div class="row">
				<div class="col-3">
					
					<!-- VK Widget -->
					<div id="vk_groups"></div>
					<script type="text/javascript">
					VK.Widgets.Group("vk_groups", {mode: 0, width: "240", height: "375", color1: 'FFFFFF', color2: '56b7cb', color3: '55b7ca'}, 51054317);
					</script>
					
				</div>
				<div class="col-3">
					<div class="write-comment-widget">
						
						<h3>Напиши отзыв,<br>получи тыщу</h3>
						
						<i class="icon icon-underwood"></i>
						
						<a href="http://vk.com/" class="btn">Написать</a>
						
					</div>
				</div>
				<div class="col-6">
					
					<div class="title-with-link">
							<h2 class="short-title">Отзывы</h2>
							<a href="/reviews/" class="dotted">Посмотреть все</a>
						</div>
					<div class="comments-box">					
						<?=Page::includeComponent('catalog:comments', array('limit' => 3))?>
					</div>
					
				</div>				
			</div>
		</div>
		
		<?
			$i=0;
			$sct = false;
			$cnt = '';
			if(count($sections) > 2){
				$cnt = Page::includeComponent('catalog:items', array('order_by' => 'rating desc', 'limit' => count($side_banners) > 1 ? 3 : 4, 'items_per_row' => 3), array('folder_id' => $sections[2]['id']));
				if(trim($cnt)){
					$sct = $sections[2];
				}
		}?>
		
		<?if($sct && $cnt){?>
			<div class="content-wrap">

				<div class="title-with-link">
					<h2>Популярные <?=Common::toLowerCase($sct['name'])?></h2>
					<a href="/catalog/<?=$sct['url']?>/" class="dotted">Перейти в раздел</a>
				</div>

				<div class="row">
					<div class="col-<?=count($side_banners) > 1 ? 9 : 12?>">

						<?=$cnt?>

					</div>
					<?if(count($side_banners) > 0){?>
						<div class="col-3">
							<?=DBCommon::showBanner($side_banners[0])?>
						</div>
					<?}?>
				</div>

			</div>
		<?}?>
		
		<?if(count($sections) > 3){?>
			<?
			for($i=3; $i<count($sections); $i++){
				$cnt = Page::includeComponent('catalog:items', array('order_by' => 'rating desc', 'limit' => 4, 'items_per_row' => 4), array('folder_id' => $sections[$i]['id']));
				if(trim($cnt)){
				?>
					<div class="content-wrap">		
						<div class="title-with-link">
							<h2>Популярные <?=Common::toLowerCase($sections[$i]['name'])?></h2>
							<a href="/catalog/<?=$sections[$i]['url']?>/" class="dotted">Перейти в раздел</a>
						</div>
						<?=$cnt?>		
					</div>		
			<?}}?>
		<?}?>
		
		<?if(count($text_array)){?>
			<div class="content-wrap">
			

				<h2 class="pink-title">О магазине</h2>

				<div class="row">
					<div class="col-<?=count($side_banners) > 1 ? 9 : 12?>">
						<div class="row">
							<?
							$size = ceil(12/count($text_array));
							foreach($text_array as $t){?>
								<div class="col-<?=$size?>">
									<?=$t?>
								</div>
							<?}?>
						</div>
					</div>
					<?if(count($side_banners) > 1){?>
						<div class="col-3">
							<?=DBCommon::showBanner($side_banners[1])?>
						</div>
					<?}?>
				</div>

			</div>
		<?}?>
		
	</div>

</div>