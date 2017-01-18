<div class="sidebar affix" data-container=".page__body">
	
	<?/*
	<div class="sidebar__title">
		Моя учетная запись
	</div>
	*/?>
	
	<div class="sidebar__nav">
		<ul>
			<?foreach($items as $v){?>
				<li>
					<a href="<?=$v['url']?>"<?if(Site::isCurrentPage($v['url'])){?> class="s-active"<?}?>><?=$v['title']?></a>
				</li>
			<?}?>
		</ul>
	</div>
	
</div>