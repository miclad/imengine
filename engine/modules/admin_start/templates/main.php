<div class="content_block start_page">
	
	<?foreach($menu as $v){?>
	
		<?if($v['name'] != 'admin_start'){?>
			<?
			$children = MenuTree::getInstance()->getItems($v['name']);
			if(count($children)){
			?>
			<div class="start_page_box">
				<div class="rnd_ttl">
					<div>
						<span <?if(isset($v['icon']) && $v['icon'] != ''){?> style="background-image:url('/engine/templates/admin/img/<?=$v['icon']?>');"<?}?>><?=$v['title']?></span>
					</div>
				</div>
				<div class="rnd_cnt">
				
					<div class="start_page_menu">
						<?foreach($children as $v){?>
						<div><a href="<?=$v['url']?>"><?=$v['title']?></a></div>
						<?}?>
					</div>
				
				</div>
				<div class="rnd_bot"><div></div></div>
			</div>
			<?}?>
		<?}?>
		
	<?}?>
	
	<div class="clearer"></div>
	
</div>

<h2 class="stat_title">Статистика</h2>
<div class="double_hr"></div>
<div class="stat_text">
	Пользователей &mdash; <?=$users_num['cnt']?><br>
	<?
	/*
	Последний вход в систему: <?=Site::formatDBDate(AuthUser::getInstance()->getField('last_login'), true, true, ' в ')?>, <?=AuthUser::getInstance()->getField('login')?>
	*/
	?>
</div>