<div class="articles-gallery">
	
	<div class="row">
		<?
		$i = 0;
		while($v = $list->getNext()){
			$lnk = $v['direct_link'] ? $v['direct_link'] : ($v['page_url'] != '' ? $v['page_url'] : $PAGE->getUrl()).'/'.$v['id'].'/';
		?>
			
			<div class="col-3">
				<div class="articles-gallery__item">
					<div class="articles-gallery__picture">
						<?if($v['orig_ext']){?>
							<a href="<?=$lnk?>"><img src="/img/articles/sm<?=$v['id']?>.jpg"/></a>
						<?}else{?>
							<i class="glyphicon glyphicon-camera no-photo"></i>
						<?}?>
					</div>
					<div class="articles-gallery__body">
						<div class="articles-gallery__title">
							<a href="<?=$lnk?>"><?=$v['title']?></a>
						</div>
						<div class="articles-gallery__date">
							<?=Site::formatDBDate($v['date'], false, true)?>
						</div>				
						<div class="articles-gallery__text">
							<?=$v['announce']?>
						</div>
					</div>
				</div>
			</div>
		
			<?if(($i+1) % 4 == 0 && $i < $list->selectedRowsCount()-1){?>
				</div><div class="row">
			<?}?>
		
		<?
		$i++;
		}?>
	</div>
	
</div>