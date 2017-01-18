<?if(count($banners)){?>
	<div class="slider__wrap">
		<div class="slider" data-auto="true" data-interval="5000" data-speed="500" data-bullets="true" data-nav="false" data-spacing="30">
			<?foreach($banners as $k => $v){?>
				<a href="<?=$v['link']?>" class="slider-item<?if($k == 0){?> _slider-item-active<?}?>" style="background-image: url('/img/ab/bg_<?=$v['file']?>')">
					<div class="container">
						<div class="slider-text"><table><tr><td><?=nl2br($v['text'])?></td></tr></table></div>
					</div>
				</a>
			<?}?>
		</div>
	</div>
<?}?>