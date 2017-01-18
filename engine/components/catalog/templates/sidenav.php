<ul>
	<?foreach($sections as $k => $v){?>
		<li<?if($v['id'] == $PAGE->getAttribute('current_section') || $v['id'] == $PAGE->getAttribute('current_section_root')){?> class="active"<?}?>>
			<a href="/catalog<?=$v['url']?>/"><?=$v['name']?></a> 
			<i></i>
					
			<?if($k < count($sections)-1 && $sections[$k+1]['_tree_level'] > $v['_tree_level']){?>
				<ul>
			<?}?>
						
			</li>
					
			<?if($v['_tree_level'] > 0 && ($k == count($sections)-1 || $v['_tree_level'] > $sections[$k+1]['_tree_level'])){
				for($i=0; $i<$v['_tree_level']-($k == count($sections)-1 ? 0 : $sections[$k+1]['_tree_level']); $i++){
					echo '</ul></li>';
				}
			}?>
			
	<?}?>
</ul>