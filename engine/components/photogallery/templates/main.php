<div class="photogallery clearfix popup-gallery">

	<?while($p = $list->getNext()){?>
		<div class="photo_item">
			<a href="<?if($p['direct_link']) echo $p['direct_link']; else{?>/img/photos/<?=$p["id"]?>.<?=$p['orig_ext']?><?}?>" rel="gallery<?=$this->getConfig('folder')?>"><img src="/img/photos/s<?=$p["id"]?>.jpg" title="<?=htmlspecialchars($p['title'])?>"/></a>
		</div>
	<?$i++;}?>
	
</div>