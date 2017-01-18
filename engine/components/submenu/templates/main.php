<div class="submenu clearfix">
	<?foreach($items as $k => $v){?>
		<a href="<?=$v['link']?>" class="btn"><?if($k == 0){?><i class="icon i_arr_l"></i><?}?><?=$v['name']?></a>
	<?}?>
</div>