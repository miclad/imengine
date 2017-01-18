<ul>
	<?for($k=$start_from; $k<count($this->path); $k++){$v = $this->path[$k];?>	
		<li>
			<?if($v['link']){?>
				<a href="<?=$v['link']?>"><?=$v['name']?></a>
			<?}else{?>
				<?=strip_tags($v['name'])?>
			<?}?>
		</li>
	<?}?>
</li>