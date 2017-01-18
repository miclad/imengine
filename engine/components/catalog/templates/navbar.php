<ul>
	<?foreach($sections as $v){?>
		<li>
			<a href="/catalog<?=$v['url']?>/"><?=$v['name']?></a>
			<?if(count($v['children'])){?>
				<ul class="subnav">
					<?foreach($sections as $ch){?>
						<li>
							<a href="/catalog<?=$ch['url']?>/"><?=$ch['name']?></a>
						</li>
					<?}?>
				</ul>
			<?}?>
		</li>
	<?}?>
</ul>