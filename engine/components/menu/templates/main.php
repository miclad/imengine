<?if(count($pages)){?>
	<ul>
	<?foreach($pages as $k => $v){?><?
		$cl = array();
		if($PAGE->getRootId() == $v['id'] || $PAGE->getId() == $v['id'])
			$cl[] = 'active';
		if($v['main'])
			$v['link'] = '/';
	?>
		<li<?if(count($cl)){?> class="<?=implode(' ', $cl)?>"<?}?>>
			<a href="<?=$v['link']?>"><?=$v['name']?></a>
		</li>
		
	<?}?>
	</ul>	
<?}?>