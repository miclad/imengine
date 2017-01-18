<div class="articles">

	<?$i=0;while($v = $list->getNext()){?>
	<?$lnk = true || $v['content'] != '' ? ($v['direct_link'] ? $v['direct_link'] : ($v['page_url'] ? $v['page_url'] : $PAGE->getUrl()).'/'.$v['id'].'/') : ''?>
	<div class="articles__item">		
		<div class="articles__item__picture">
			<?if($v['orig_ext']){?>
				<?if($lnk){?><a href="<?=$lnk?>"><?}?><img src="/img/articles/sm<?=$v['id']?>.jpg"/><?if($lnk){?></a><?}?>
			<?}else{?>
				<i class="fa fa-camera"></i>
			<?}?>
		</div>
		<div class="articles__item__body">
			<?if($this->getConfig('show_date')){?>
				<div class="articles__item__date">
					<?=Date::onlyDate($v['date'])?>
				</div>
			<?}?>
			<div class="articles__item__title">
				<?if($lnk){?><a href="<?=$lnk?>"><?}?><?=$v['title']?><?if($lnk){?></a><?}?>
			</div>
			<div class="articles__item__text">
				<?
				$announce = explode("\n", $v['announce']);
				if(count($announce) == 1)
					echo $announce[0];
				else{
					foreach($announce as $a)if($a != ''){
						echo '<p>'.$a.'</p>';
					}
				}
				?>
			</div>
		</div>
	</div>
	<?$i++;}?>
	
</div>

<?=$list->printPager()?>