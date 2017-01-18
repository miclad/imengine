<div class="scroll_control"><a onClick="scrollItems('products', 0, this); return false;" href="#" id="scroll_control_left_products"><img src="/img/scroll_left.png" class="inactive"/></a></div>
<div class="scroll_container">

	<div class="scroll_content" id="scroll_content_products">
	
		<table>
		<tr>
			<?while($product = $items->getNext()){?>
			<td class="i">
				<?require 'product_card.php'?>	
			</td>
			<?}?>
		</tr>
		</table>
	
	</div>
	
</div>
<div class="scroll_control rght"><a onClick="scrollItems('products', 1, this); return false;" href="#" id="scroll_control_right_products"><img src="/img/scroll_right.png"<?if($items->selectedRowsCount() < 3){?> class="inactive"<?}?>/></a></div>
	
<div class="clearer"></div>