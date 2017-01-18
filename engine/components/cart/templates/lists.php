<?foreach($user_wishlists as $v){?>
<div class="wishlist<?if($v['is_default']){?> default<?}?>">
	<?if($v['is_default']){?>
		<div class="rnd_top_grey"><div></div></div>
		<div class="rnd_cnt_grey nopad">
	<?}else{?>
		<div class="sp"></div>
	<?}?>
	<div class="wishlist_name"><a href="/wishlist/?action=set_default_list&id=<?=$v['id']?>"><?=$v['name']?></a></div>
	<div class="wishlist_date"><?=Site::formatDBDate($v['created_tsmp'], false)?></div>
	<?if($v['is_default']){?>
		</div>
		<div class="rnd_bot_grey"><div></div></div>
	<?}else{?>
		<div class="sp"></div>
	<?}?>
</div>
<?}?>

<a onClick="toggleBlock('create_wishlist', this); return false;" href="#" class="create_wishlist_link">Создать список</a>
<div id="create_wishlist">
	<form action="/wishlist/?action=create_list" method="POST" onSubmit="return onWLCreateSubmit(this)">
		<input type="text" name="name" class="inp"/> <input type="submit" value="+"/>
	</form>
</div>

<div class="hr"></div>

<div class="wishlist_left_content">
	<div><a href="/personal/?a=my_orders">История заказов</a></div>
	<div><a href="/personal/?a=my_orders&regular">Регулярные заказы</a></div>	
	<div><a href="/personal/?a=history">История просмотров</a></div>
	<div><a href="/personal/">Мои настройки</a></div>
</div>