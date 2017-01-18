<?$i=0;while($v = $list->getNext()){?>
	<?$lnk = $v['direct_link'] ? $v['direct_link'] : ($v['page_url'] ? $v['page_url'] : $PAGE->getUrl()).'/'.$v['id'].'/'?>
	<div class="item">
		<div class="date">
			<?=Site::formatDate($v['date'])?>
		</div>			
		<div class="ttl">
			<a href="<?=$lnk?>"><?=$v['title']?></a>
		</div>
		<?=$v['announce']?>
	</div>
<?$i++;}?>

<div class="all_news">
	<a href="/news/">Все новости</a>
</div>