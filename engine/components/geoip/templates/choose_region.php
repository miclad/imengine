<?/*
<div>
	<span></span>
	<a href="#" id="region_confirm" <?if(isset($_COOKIE['region_confirmed'])){?> class="hidden"<?}?>>Сохранить</a>
</div>
*/?>

<div class="b-choose-region">
	
	<input type="hidden" name="city_code" value="<?=$city_code?>"/>
	
	<div class="b-choose-region__head" id="chosen_city">
		<h1 data-role="city-name"><?=$current_region?></h1>
		<button class="btn btn--med b-choose-region__save">
			Сохранить
		</button>
		<div class="b-choose-region__region-name" data-role="region-name"><?=$city_region?></div>
	</div>
	
	<div class="b-choose-region__body">
		
		<div class="b-choose-region__search">
			<form action="#">
				<input type="text" name="search_region" placeholder="Введите название вашего города">
			</form>
		</div>
		
		<div class="b-choose-region__list">
			<table>
				<tr>
					<td>
						<ul class="b-nav">
						<?$i=0;while($v = $cities->getNext()){?>
							<li<?if($v['name'] == 'Санкт-Петербург'){?> class="b-nav__sep"<?}?>>
								<?if($v['is_bold']){?><b><?}?>
								<a href="<?=Common::editUrl(array('change_region' => urlencode($v['name'])), array())?>"><?=$v['name']?></a>
								<?if($v['is_bold']){?></b><?}?>
							</li>
							<?if(($i+1)%$ipc == 0 && $i < $cities->selectedRowsCount()-1){?>
								</ul></td>
								<td><ul class="b-nav">
							<?}?>
						<?$i++;}?>
						</ul>
					</td>
				</tr>
			</table>
		</div>

	</div>
	
</div>

<script>

	$(document).ready(function(){
		geo.init();		
	});	

</script>