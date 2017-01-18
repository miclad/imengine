<?if($this->is_inner || $PAGE->getAttribute('current_brand')){?>
<ul>
	<?while($v = $list->getNext()){?>
		<li>
			<a href="/brands/<?=$v['url']?>/"<?if($PAGE->getAttribute('current_brand') == $v['id']){?> class="cur"<?}?>><?=$v['name']?></a>
		</li>
	<?}?>
	<?if($this->getConfig('limit')){?>
		<li>
			<a href="/brands/"><b>Показать все</b></a>
		</li>
	<?}?>
</ul>
<?}?>