<div class="catalog_index">

	<table class="brands_list">
		<tr>
		<?$i=0; while($v = $list->getNext()){?>
			
			<td>
				<?if($v['orig_ext'] && file_exists(B_DIR.'img/brands/s'.$v['id'].'.jpg')){?>
					<a href="/brands/<?=urlencode($v['url'])?>/"><img src="/img/brands/s<?=$v['id']?>.jpg"/></a>
				<?}else{?>
					
				<?}?>
				<h3><a href="/brands/<?=urlencode($v['url'])?>/"><?=$v['name']?></a></h3>
			</td>
			<?if(($i+1) % 3 == 0){?>
				</tr><tr>
			<?}?>
		<?$i++;}?>
		</tr>
	</table>

</div>