<?

$admin_menu = array(
	array(
		'name' => 'info',
		'title' => 'Контент',
		'pos' => 2,
		'icon' => 'list'
	),
	array(
		'name' => 'datatable_structure',
		'parent' => 'info',
		'title' => 'Страницы',
		'url' => 'index.php?module=datatable&entity=structure',
		'pos' => 1
	),
	array(
		'name' => 'datatable_articles',
		'parent' => 'info',
		'title' => 'Статьи',
		'url' => 'index.php?module=datatable&entity=articles',
		'pos' => 2
	),
	/*
	array(
		'name' => 'datatable_items_properties',
		'parent' => 'info',
		'title' => 'Свойства статей',
		'url' => 'index.php?module=datatable&entity=items_properties',
		'pos' => 3
	),
	*/
	array(
		'name' => 'datatable_files',
		'parent' => 'info',
		'title' => 'Файлы',
		'url' => 'index.php?module=datatable&entity=files',
		'pos' => 7
	),
	array(
		'name' => 'datatable_banners',
		'parent' => 'info',
		'title' => 'Баннеры',
		'url' => 'index.php?module=datatable&entity=banners',
		'pos' => 6
	),
	array(
		'name' => 'datatable_photogallery',
		'parent' => 'info',
		'title' => 'Фотогалерея',
		'url' => 'index.php?module=datatable&entity=photogallery',
		'pos' => 5
	),
	array(
		'name' => 'catalog',
		'title' => 'Каталог',
		'pos' => 3,
		'icon' => 'list-alt'		
	),
	array(
		'name' => 'datatable_start_page_catalog_folders',
		'parent' => 'catalog',
		'title' => 'Категории на главной',
		'url' => 'index.php?module=datatable&entity=start_page_catalog_folders',
		'pos' => 10
	),
	array(
		'name' => 'datatable_catalog',
		'parent' => 'catalog',
		'title' => 'Товары',
		'url' => 'index.php?module=datatable&entity=catalog',
		'pos' => 1
	),
	array(
		'name' => 'datatable_brands',
		'parent' => 'catalog',
		'title' => 'Производители',
		'url' => 'index.php?module=datatable&entity=brands',
		'pos' => 2
	),
	/*
	array(
		'name' => 'datatable_catalog_properties',
		'parent' => 'catalog',
		'title' => 'Характеристики товаров',
		'url' => 'index.php?module=datatable&entity=catalog_properties',
		'pos' => 4
	),
	*/
	/*
	array(
		'name' => 'datatable_suppliers',
		'parent' => 'catalog',
		'title' => 'Поставщики',
		'url' => 'index.php?module=datatable&entity=suppliers',
		'pos' => 20
	),	
	array(
		'name' => 'datatable_catalog_purposes',
		'parent' => 'catalog',
		'title' => 'Назначения',
		'url' => 'index.php?module=datatable&entity=catalog_purposes',
		'pos' => 5
	),	
	array(
		'name' => 'datatable_catalog_special_types',
		'parent' => 'catalog',
		'title' => 'Типы выделенных товаров',
		'url' => 'index.php?module=datatable&entity=catalog_special_types',
		'pos' => 6
	),
	*/
	array(
		'name' => 'datatable_catalog_special_offers',
		'parent' => 'shop',
		'title' => 'Спецпредложения',
		'url' => 'index.php?module=datatable&entity=catalog_special_offers',
		'pos' => 4
	),
	array(
		'name' => 'shop',
		'title' => 'Магазин',
		'pos' => 4,
		'icon' => 'shopping-cart'
	),
	array(
		'name' => 'datatable_orders',
		'parent' => 'shop',
		'title' => 'Заказы',
		'url' => 'index.php?module=datatable&entity=orders',
		'pos' => 1
	),
	array(
		'name' => 'datatable_delivery_types',
		'parent' => 'shop',
		'title' => 'Способы доставки',
		'url' => 'index.php?module=datatable&entity=delivery_types',
		'pos' => 3
	),
	array(
		'name' => 'datatable_delivery_groups',
		'parent' => 'shop',
		'title' => 'Регионы доставки',
		'url' => 'index.php?module=datatable&entity=delivery_groups',
		'pos' => 3.5
	),
	array(
		'name' => 'datatable_payment_types',
		'parent' => 'shop',
		'title' => 'Способы оплаты',
		'url' => 'index.php?module=datatable&entity=payment_types',
		'pos' => 4
	),
	array(
		'name' => 'datatable_order_statuses',
		'parent' => 'shop',
		'title' => 'Статусы заказов',
		'url' => 'index.php?module=datatable&entity=order_statuses',
		'pos' => 2
	),
	array(
		'name' => 'datatable_discounts',
		'parent' => 'shop',
		'title' => 'Скидки',
		'url' => 'index.php?module=datatable&entity=discounts',
		'pos' => 5
	),
	array(
		'name' => 'datatable_bonus_values',
		'parent' => 'shop',
		'title' => 'Баллы',
		'url' => 'index.php?module=datatable&entity=bonus_values',
		'pos' => 5
	),
	/*
	array(
		'name' => 'datatable_currency',
		'parent' => 'shop',
		'title' => 'Валюты',
		'url' => 'index.php?module=datatable&entity=currency',
		'pos' => 6
	),
	*/
	array(
		'name' => 'services',
		'title' => 'Сервисы',
		'pos' => 5,
		'icon' => 'cog'
	),
	array(
		'name' => 'datatable_forms',
		'parent' => 'services',
		'title' => 'Формы',
		'url' => 'index.php?module=datatable&entity=forms',
		'pos' => 1
	),
	array(
		'name' => 'settings',
		'title' => 'Настройки',
		'pos' => 51,
		'icon' => 'wrench'
	),
	array(
		'name' => 'configs',
		'parent' => 'settings',
		'title' => 'Настройки',
		'url' => 'index.php?module=configs',
		'pos' => 1
	),
	array(
		'name' => 'datatable_users',
		'parent' => 'settings',
		'title' => 'Пользователи',
		'url' => 'index.php?module=datatable&entity=users',
		'pos' => 2
	),
	array(
		'name' => 'datatable_user_groups',
		'parent' => 'settings',
		'title' => 'Типы пользователей',
		'url' => 'index.php?module=datatable&entity=user_groups',
		'pos' => 3
	),
	
	array(
		'name' => 'datatable_catalog_price_types',
		'parent' => 'catalog',
		'title' => 'Типы цен',
		'url' => 'index.php?module=datatable&entity=catalog_price_types',
		'pos' => 3
	),
	array(
		'name' => 'datatable_catalog_certificates_groups',
		'parent' => 'catalog',
		'title' => 'Сертификаты',
		'url' => 'index.php?module=datatable&entity=catalog_certificates_groups',
		'pos' => 4
	),
	array(
		'name' => 'datatable_lists_main',
		'parent' => 'info',
		'title' => 'Справочники',
		'url' => 'index.php?module=datatable&entity=lists_main',
		'pos' => 30
	),
	array(
		'name' => 'datatable_regions',
		'parent' => 'settings',
		'title' => 'Города для выбора',
		'url' => 'index.php?module=datatable&entity=regions',
		'pos' => 30
	)
)

?>