<?if(count($items)){?>
	<?foreach($items as $v){?>
		<div class="item" id="cart_mini_item_<?=$v['entry_id']?>">
			<div class="col_left"><?=$v['model'].($v['variant_name'] ? ' '.$v['variant_name'] : '')?></div>
			<div class="col_right">
				<div class="visible"><span class="num"><?=$v['items_num']?></span> шт.</div>
				<div class="hidden">
					<input type="hidden" name="item_id" value="<?=$v['entry_id']?>"/>
					<a href="#" class="btn_more ti">&nbsp;</a>
					<a href="#" class="btn_less ti">&nbsp;</a>
					<div class="text"><?=$v['items_num']?></div>
				</div>
			</div>
			<div class="cb"></div>
		</div>
	<?}?>
<?}?>