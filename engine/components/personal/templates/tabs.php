<div class="tabs">
	<a href="/personal/profile/"<?if($this->getCurrentAction() == 'profile'){?> class="s-active"<?}?>>Профиль</a>
	<a href="/personal/orders/"<?if($this->getCurrentAction() == 'orders'){?> class="s-active"<?}?>>История заказов</a>
	<a href="/personal/bonus/"<?if($this->getCurrentAction() == 'bonus'){?> class="s-active"<?}?>>Бонусы</a>
	<a href="/personal/change_password/"<?if($this->getCurrentAction() == 'change_password'){?> class="s-active"<?}?>>Изменить пароль</a>
</div>