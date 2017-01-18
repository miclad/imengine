<?$i=0;?>
<div class="photogallery albums clearfix">

	<?while($v = $albums->getNext()){
		$v['name'] = preg_replace('/([0-9]+\.[0-9]+\.[0-9]+)\s+\-\s+/', '$1<hr/>', $v['name']);
	?>
		<div>
			<a href="<?=$v['page_url'] ? $v['page_url'] : $PAGE->getUrl()?>/<?=$v['id']?>/">
				<img src="/img/photos/s<?=$v['ph']?>.jpg"/>
				
			</a>
			<span class="bg"></span>
			<span class="c">
				<span><?=$v['name']?></span>
			</span>	
		</div>
	<?$i++;}?>
	
</div>

<?=$albums->printPager()?>