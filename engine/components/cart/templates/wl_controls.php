<div class="rndl f_right">
	<div>
		<span class="a">
		<a onClick="toggleBlock('wishlist_rename', this);" href="#" class="js_link orange">переименовать</a> &nbsp;				
		<a onClick="toggleBlock('wishlist_note_form', this);" href="#" class="js_link blue">заметка</a> &nbsp;	
		<?if(count($items)){?>
		<a onClick="this.blur(); return confirm('Вы уверены?');" href="/wishlist/?action=clear_list&id=<?=$current_wishlist['id']?>" class="js_link red">очистить</a> &nbsp;	
		<?}?>
		<?if(!$current_wishlist['main']){?>
		<a onClick="this.blur(); return confirm('Вы уверены?');" href="/wishlist/?action=delete_list&id=<?=$current_wishlist['id']?>" class="js_link red">удалить</a>
		<?}?>					
		</span>
	</div>
</div>