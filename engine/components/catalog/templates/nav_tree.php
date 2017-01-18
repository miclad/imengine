<?if(count($sections)){?>

	<?foreach($sections as $k => $v){?><?
		$cl = array();
	?>
		<?if($k == 0 || $sections[$k-1]['_tree_level'] < $v['_tree_level']){?>
			<ul>
		<?}?>
		
		<li class="level<?=$v['_tree_level']+1?>">
		
			<a href="/catalog<?=$v['url']?>/"<?if(count($cl)){echo ' class="'.implode(' ', $cl).'"';}?>><?=$v['name']?></a>
		
		<?if($k == count($sections)-1 || $sections[$k+1]['_tree_level'] <= $v['_tree_level']){?>	
			</li>
		<?}?>
		
		<?
		if($v['_tree_level'] > 0 && ($k == count($sections)-1 || $sections[$k+1]['_tree_level'] < $v['_tree_level'])){
			$to_close = $v['_tree_level'] - ($k == count($sections)-1 ? 0 : $sections[$k+1]['_tree_level']);
			for($i = 0; $i < $to_close; $i++){
				echo '</ul></li>';				
			}
		?>
		<?}?>
		
	<?}?>
	
			<?/*
			<li class="level1">
				
				<div class="navbar__search">
					<form action="/catalog/">
						<input type="text" name="search" placeholder="Поиск по каталогу" value="<?=$PAGE->getAttribute('search_word')?>"/>
						<button class="glyphicon glyphicon-search"></button>
					</form>
				</div>
				
			</li>
			*/?>
			
	<li class="level1">
		<a href="/collective/">Коллективная закупка</a>
	</li>
			
	</ul>
<?}?>