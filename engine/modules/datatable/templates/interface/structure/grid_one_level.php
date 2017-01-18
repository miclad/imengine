<?if(!$hide){?>
<tr class="table__row--tree-children parent<?=$parent?><?if($hide){?> hide<?}?>">
	<td colspan="3">
		<div<?if($level > 0){?> style="padding-left:<?=(1*45)?>px;"<?}?>>
			<?require 'grid_one_level_inner.php'?>
		</div>
	</td>
</tr>
<?}?>