<h2 class="short-title">Бонусы</h2>

	<p>На вашем счете: <strong><?=$USER->getField('bonus').' '.Common::makeEnding($USER->getField('bonus'), 'балл', '', 'а', 'ов')?></strong><p>
	<p>Бонусы начисляются за каждую покупку, совершенную в магазине EyesColor.ru, размер бонусов зависит от суммы покупки.</p>
		
	<?if(count($history)){?>
	<hr/>
				<h3>История начислений и списаний</h3>
				<ul class="orders-history">
					<li class="orders-history__item orders-history__item--head">
						<table>
							<tr>
								<td class="orders-history__item__date" style="width: 20%;">Дата</td>
								<td class="orders-history__item__id" style="width: 20%;">№ заказа</td>								
								<td class="orders-history__item__price">Сумма</td>
								<td class="orders-history__item__toggle">Баллы</td>
							</tr>
						</table>
					</li>
					<?foreach($history as $v){?>
						<li class="orders-history__item">
							<table>
								<tr>
									<td class="orders-history__item__date" style="width: 20%;"><?=Site::formatDBDate($v['date'], false, false)?></td>
									<td class="orders-history__item__id" style="width: 20%;">№<?=$v['order_id']?></td>									
									<td class="orders-history__item__price"><b><?=Site::formatPrice($v['order_sum'])?> руб.</b></td>
									<td class="orders-history__item__toggle">
										<b class="orders-history__item__bonus__<?=$v['value'] > 0 ? 'plus' : 'minus'?>"><?=($v['value'] > 0 ? '+' : '').$v['value']?></b>
									</td>
								</tr>
							</table>
						</li>
					<?}?>
				</ul>
	<?}?>