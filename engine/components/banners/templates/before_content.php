<?foreach($banners as $v){?>
	<div class="page_intro" style="background-image: url('/img/ab/bg_<?=$v['file']?>')">
		<div class="cnt">
			<div class="inner">
				<div class="ttl"><?=$v['title']?></div>
				<div class="txt"><?=$v['text']?></div>
			</div>
		</div>
	</div>
<?}?>
