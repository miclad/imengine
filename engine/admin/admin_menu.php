<?

$admin_menu = array(
	array(
		'name' => 'info',
		'title' => 'Информация',
		'pos' => 2,
		'icon' => 'list'
	),
	array(
		'name' => 'datatable_structure',
		'parent' => 'info',
		'title' => 'Структура сайта',
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
	array(
		'name' => 'datatable_articles_authors',
		'parent' => 'info',
		'title' => 'Авторы',
		'url' => 'index.php?module=datatable&entity=articles_authors',
		'pos' => 2.5
	),
	array(
		'name' => 'datatable_items_properties',
		'parent' => 'info',
		'title' => 'Свойства статей',
		'url' => 'index.php?module=datatable&entity=items_properties',
		'pos' => 3
	),
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
		'title' => 'Справочники',
		'pos' => 40,
		'icon' => 'folder'		
	),
	array(
		'name' => 'datatable_catalog',
		'parent' => 'shop',
		'title' => 'Каталог товаров',
		'url' => 'index.php?module=datatable&entity=catalog',
		'pos' => 2
	),
	array(
		'name' => 'datatable_catalog_price_types',
		'parent' => 'shop',
		'title' => 'Типы цен',
		'url' => 'index.php?module=datatable&entity=catalog_price_types',
		'pos' => 3
	),
	array(
		'name' => 'datatable_catalog_stocks',
		'parent' => 'shop',
		'title' => 'Склады',
		'url' => 'index.php?module=datatable&entity=catalog_stocks',
		'pos' => 3.5
	),
	array(
		'name' => 'datatable_meat_types',
		'parent' => 'shop',
		'title' => 'Отрубы',
		'url' => 'index.php?module=datatable&entity=meat_types',
		'pos' => 5
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
		'name' => 'datatable_catalog_purposes',
		'parent' => 'catalog',
		'title' => 'Назначения',
		'url' => 'index.php?module=datatable&entity=catalog_purposes',
		'pos' => 5
	),
	*/
	array(
		'name' => 'datatable_catalog_special_types',
		'parent' => 'shop',
		'title' => 'Выделенные товары',
		'url' => 'index.php?module=datatable&entity=catalog_special_types',
		'pos' => 6
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
		'pos' => 20
	),
	array(
		'name' => 'datatable_payment_types',
		'parent' => 'shop',
		'title' => 'Способы оплаты',
		'url' => 'index.php?module=datatable&entity=payment_types',
		'pos' => 50
	),
	array(
		'name' => 'datatable_order_statuses',
		'parent' => 'shop',
		'title' => 'Статусы заказов',
		'url' => 'index.php?module=datatable&entity=order_statuses',
		'pos' => 4
	),
	array(
		'name' => 'datatable_discounts',
		'parent' => 'settings',
		'title' => 'Скидки',
		'url' => 'index.php?module=datatable&entity=discounts',
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
		'name' => 'datatable_mailings',
		'parent' => 'services',
		'title' => 'Рассылка',
		'url' => 'index.php?module=datatable&entity=mailings',
		'pos' => 12
	),
	array(
		'name' => 'datatable_mailing_list',
		'parent' => 'services',
		'title' => 'Подписчики',
		'url' => 'index.php?module=datatable&entity=mailing_list',
		'pos' => 15
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
		'title' => 'Группы пользователей',
		'url' => 'index.php?module=datatable&entity=user_groups',
		'pos' => 3
	),
	/*
	array(
		'name' => 'datatable_mailing_list',
		'parent' => 'settings',
		'title' => 'Адреса рассылки',
		'url' => 'index.php?module=datatable&entity=mailing_list',
		'pos' => 2
	)
	*/
	
	array(
		'name' => 'datatable_catalog_comments',
		'parent' => 'services',
		'title' => 'Отзывы',
		'url' => 'index.php?module=datatable&entity=catalog_comments',
		'pos' => 14
	),
	
	array(
		'name' => 'datatable_catalog_groups',
		'parent' => 'info',
		'title' => 'Группы товаров',
		'url' => 'index.php?module=datatable&entity=catalog_groups',
		'pos' => 3
	),
	
	array(
		'name' => 'datatable_affiliates',
		'parent' => 'settings',
		'title' => 'Филиалы',
		'url' => 'index.php?module=datatable&entity=affiliates',
		'pos' => 6
	),
	array(
		'name' => 'datatable_regions',
		'parent' => 'settings',
		'title' => 'Города в попапе',
		'url' => 'index.php?module=datatable&entity=regions',
		'pos' => 6
	),
	array(
		'name' => 'datatable_catalog_products_arrivals',
		'parent' => 'settings',
		'title' => 'Приходы товаров',
		'url' => 'index.php?module=datatable&entity=catalog_products_arrivals',
		'pos' => 7
	),
	array(
		'name' => 'datatable_catalog_arrivals_subscribers',
		'parent' => 'settings',
		'title' => 'Подписка на товары',
		'url' => 'index.php?module=datatable&entity=catalog_arrivals_subscribers',
		'pos' => 8
	),
	array(
		'name' => 'datatable_start_page_products',
		'parent' => 'info',
		'title' => 'Товары на главной',
		'url' => 'index.php?module=datatable&entity=start_page_products',
		'pos' => 2.8
	),
	array(
		'name' => 'datatable_countries',
		'parent' => 'catalog',
		'title' => 'Страны и города',
		'url' => 'index.php?module=datatable&entity=countries',
		'pos' => 10
	),
	array(
		'name' => 'datatable_catalog_terms',
		'parent' => 'catalog',
		'title' => 'Термины',
		'url' => 'index.php?module=datatable&entity=catalog_terms',
		'pos' => 20
	),
	array(
		'name' => 'datatable_catalog_upsell',
		'parent' => 'catalog',
		'title' => 'Апселл в корзине',
		'url' => 'index.php?module=datatable&entity=catalog_upsell',
		'pos' => 30
	),
	array(
		'name' => 'datatable_catalog_nav',
		'parent' => 'catalog',
		'title' => 'Подменю каталога',
		'url' => 'index.php?module=datatable&entity=catalog_nav',
		'pos' => 35
	)
)

?>