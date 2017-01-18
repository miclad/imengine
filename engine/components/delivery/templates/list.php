<?foreach($type_groups as $grk => $gr){?>
	<?foreach($gr['types'] as $k => $type){?>
		<h4><a href="<?=$PAGE->getUrl()?>/details/<?=$type['id']?>/"><?=$type['name']?></a></h4>
		<?=$type['short_description']?>
		<div class="space sm"></div>
	<?}?>
<?}?>