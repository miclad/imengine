<?if($discount['perc'] && $discount['value']){?>
	<table>	
		<tr>
			<td class="ttl">Скидка:</td>
			<td class="val"><?=$discount['perc']?>%</td>
		</tr>
		<tr>
			<td class="ttl">Со скидкой:</td>
			<td class="val"><?=Site::formatPrice($discount['total_sum'])?> руб.</td>
		</tr>
	</table>
<?}?>
