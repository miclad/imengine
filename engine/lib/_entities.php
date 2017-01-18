<?
$entities = array(
	'structure' => array(
		array('Структура сайта', 'страницу'),	
		array(
			'name' => array('Название', array('input', 'string'), 1, array('input')),  
			//'lang' => array('Язык', array('input', 'string'), 1, array('select', 'langs',1)),
			'navi_type' => array('Меню', array('input', 'string'), 1, array('select', 'navi_types')), 
			'parent' => array('Родитель', array('input', 'string'), 1, array('select', 'parents')),
			'url' => array('Адрес', array('input', 'item_url', 'by_fields' => array('name'), 'show_link' => true), 1, array('item_url'), 'unique' => ''),						 
			'content' => array('Контент', array('input', 'string'), 0, array('textarea',1)),
			//'pict_bg' => array('Фон', array('file', 'image', 'img/pages/'), 0),
			'html_title' => array('SEO Title', array('input', 'string'), 0, array('textarea', 0)),
			'html_keywords' => array('SEO keywords', array('input', 'string'), 0, array('textarea', 0)),
			'html_description' => array('SEO description', array('input', 'string'), 0, array('textarea', 0)),			
			'visible' => array('Отображать в меню', array('input', 'number'), 0, array('checkbox',1)),		
			'hard_link' => array('Прямая ссылка', array('input', 'string'), 0, array('input')),			
		  	//'pict' => array('Иконка для меню', array('file', 'image', 'img/pages/'), 0),		  	
			//'pict_hover' => array('Ссылка в меню (активная)', array('file', 'image', 'img/pages/'), 0),
			'allow_extra_params' => array('ЧПУ', array('input', 'number'), 0, array('checkbox', 0)),
			'extra_params_limit' => array('Макс. кол-во параметров', array('input', 'number'), 0, array('input')),
			'set_get_vars' => array('Назначить параметры переменным', array('input', 'string'), 0, array('input')),
			'attributes' => array('Параметры', array('input', 'string'), 0, array('input')),
			'pos' => array('', array('auto_increment', 'parent'), 0)		 
		), array('name', 'pos'), array(1,0), 0, array('navi_type'=>'parent')
	),
	'pages_components' => array(
		array('Компоненты', 'компонент'),
		array(
			'component' => array('Компонент', array('input', 'string'), 1, array('select', 'arr_components')),
			'page_id' => array('', array('foreign', 'structure',1), 1),
			'pos' => array('', array('auto_increment', 'page_id'))
		), 
		array('component', 'pos'), 
		array(0,1),
		'common_edit_title' => true,
		'init_by' => 'page_id'
	),
	'components_configs' => array(
		array('', ''),
		array(
			'centry_id' => array('', array('foreign', 'pages_components',1))
		), array(), 
		array(),
		0,
		0,
		array('content_admin')
	),
	'users' => array(
		array('Пользователи', 'пользователя'),
		array(			
			'surname' => array('Фамилия', array('input', 'text'), 0, array('input')),
			'name' => array('Имя', array('input', 'text'), 1, array('input')),			
			'p_name' => array('Отчество', array('input', 'text'), 0, array('input')),
			'phone' => array('Телефон', array('input', 'text'), 0, array('input')),
			'email' => array('E-mail', array('input', 'mail'), 0, array('input')),
			'address' => array('Адрес', array('input', 'text'), 0, array('textarea', 0)),
			'address_post_index' => array('Почтовый индекс', array('input', 'text'), 0, array('input')),
			'address_region' => array('Область', array('input', 'text'), 0, array('input')),
			'address_city' => array('Город', array('input', 'text'), 0, array('input')),
			'address_street' => array('Улица', array('input', 'text'), 0, array('input', 0)),
			'address_house' => array('Дом', array('input', 'text'), 0, array('input', 0)),
			'address_corp' => array('Корпус', array('input', 'text'), 0, array('input', 0)),
			'address_flat' => array('Квартира/офис', array('input', 'text'), 0, array('input', 0)),
			'about' => array('Описание', array('input', 'text'), 0, array('textarea', 0)),
			'user_photo' => array('Фото (лого)', array('image_to_resize', array(array('300,300', 'med', 'uploads/img/users/', 2), array('1280,1280', 'big', 'uploads/img/users/', 0))), 0),
			'bonus' => array('Бонусы', array('input', 'bonus'), 0, array('input')),
			'login' => array('Логин', array('input', 'text'), 1, array('input'), 'unique' => ''),
			'password' => array('Пароль', array('input', 'password'), 1, array('input')),
			'group_id' => array('Группа', array('foreign', 'user_groups', 0), 0),
			'is_admin' => array('Администратор', array('input', 'number'), 0, array('checkbox', 0)),
			'subscribe' => array('', array('input', 'number'), 0, array('checkbox', 1)),
			'activated' => array('Активирован', array('input', 'number'), 0, array('checkbox', 1)),
			"added_tsmp" => array("", array("timestamp_add")),
			"activated_tsmp" => array("", array("timestamp_add")),
		), array('login,name,surname', 'is_admin=0,id'), array(0,0),
		'filter' => array('login', 'name', 'surname', 'is_admin', 'group_id'),
		'default_filter' => array('is_tmp' => 0),
		'use_pager' => 40,
		'escape_mode' => 'strip_tags'
	),
	'articles' => array(
		array('Статьи', 'статью'),
		array(
			'date' => array('Дата', array('date', 'form', 1), 1),
			'title' => array('Заголовок', array('input', 'string'), 1, array('input')),
			'announce' => array('Краткий текст', array('input', 'string'), 0, array('textarea', 0)),
			'content' => array('Содержание', array('input', 'string'), 0, array('textarea',1)),
			//'video' => array('Видео', array('input', 'string'), 0, array('textarea',1)),
			//'articles_products' => array('Товары', array('multiple_foreign', 'catalog', 'article_id', 'product_id', '1', 4, 'order_by' => 'brand_name,model', 'extra_fields' => 'BR.name as brand_name', 'left_join' => 'brands as BR on BR.id=T.brand', 'group_by' => 'brand_name', 'show_field' => 'model')),				  
			'direct_link' => array('Прямая ссылка', array('input', 'string'), 0, array('input')),
			'photogallery_id' => array('Фотогалерея', array('foreign', 'folders', 0, 'entity="photogallery"'), 0),
			'image' => array('Фото', array('image_to_resize', array(array('200,200', 'sm', 'img/articles/', 0)), 'save_orig' => 'img/articles/'), 0),
			'active' => array('Активность', array('input', 'number'), 0, array('checkbox',1)),
			'folder_id' => array('', array('foreign', 'folders',1), 0),
		), array('title', 'date desc'), array(0,0), 1,'save_author_id' => true,
		//'copy' => true,
		//'change_before_copy' => 'url'
	),
	'photogallery' => array(
		array('Фотогалерея', 'фото'),
		array(
			'image' => array('Фото', array('image_to_resize', array(array('90,80', 'vs', 'img/photos/', 2), array('178,146', 's', 'img/photos/', 2), array('680,680', 'b', 'img/photos/', 0)), 'save_orig' => 'img/photos/'), 0),
			'title' => array('Описание', array('input', 'string'), 0, array('input')),
			//'direct_link' => array('Прямая ссылка', array('input', 'string'), 0, array('input')),
			'folder_id' => array('', array('foreign', 'folders',1), 0),
			'pos' => array('', array('auto_increment'), 1)
		), array('image', 'pos'), array(0,0), 1,
		'multi_upload' => true
	),
	'banners' => array(
		array('Баннеры', 'баннер'),
		array(
			'placing' => array('Тип', array('input', 'string'), 1, array('select', 'banners_placings')),
			'file' => array('Файл', array('file', 'img', 'img/ab/'), 0),
			'code' => array('Код', array('input', 'string'), 0, array('textarea', 0)),
			'title' => array('Заголовок', array('input', 'string'), 0, array('input')),
			'text' => array('Текст', array('input', 'string'), 0, array('textarea', 0)),
			'link' => array('Ссылка', array('input', 'string'), 0, array('input')),			
			//'width' => array('Ширина', array('input', 'number'), 0, array('input')),
			//'height' => array('Высота', array('input', 'number'), 0, array('input')),			
			//'only_catalog' => array('Только в каталоге', array('input', 'number'), 0, array('checkbox', 0)),
			//'only_sections' => array('Только в выбранных разделах', array('input', 'number'), 0, array('checkbox', 0)),
			//'banner_sections' => array('В разделах', array('multiple_foreign', 'folders', 'banner_id', 'section_id', 'entity="catalog"', 'order_by' => 'name', 'no_limit' => 1)),
			//'banner_brands' => array('В брендах', array('multiple_foreign', 'brands', 'banner_id', 'brand_id', 'no_limit' => 1)),
			'show_case' => array('Условия показа', array('input', 'string'), 0, array('input')),
			'banner_pages' => array('Страницы', array('multiple_foreign', 'structure', 'banner_id', 'page_id', 'no_limit' => 1)),
			'new_window' => array('Открывать в новом окне', array('input', 'number'), 0, array('checkbox', 0)),
			'visible' => array('Активность', array('input', 'number'), 0, array('checkbox',1)),
			'pos' => array('', array('auto_increment', 'placing'), 1)
		), array('file', 'placing,pos'), array(1,0),
		'filter' => array('placing', 'visible'),
		'copy' => true
	),
	'files' => array(
		array('Файлы', 'файл'),
		array(
			'file' => array('Файл', array('file', '', 'uploads/files/'), 1),
			'description' => array('Комментарий', array('input', 'string'), 0, array('input')),
			'pos' => array('', array('auto_increment', 'folder_id'), 0)
		), array('file,description', 'id'), array(0,0)
	),
	'forms' => array(
		array('Формы', 'форму'),
		array(
			'name' => array('Название', array('input', 'string'), 1, array('input')),
			'email' => array('E-mail для отправки', array('input', 'text'), 1, array('input')),
			'success_text' => array('Текст в случае успешной отправки', array('input', 'text'), 1, array('textarea', 0)),
			'submit_text' => array('Текст кнопки', array('input', 'string'), 0, array('input')),
			'use_captcha' => array('CAPTCHA', array('input', 'number'), 0, array('checkbox', 1)),
		), array('name', 'id'), array(0,0)
	),
	'forms_fields' => array(
		array('Поля', 'поле'),
		array(
			'name' => array('Название', array('input', 'string'), 1, array('input')),
			'placeholder' => array('Описание', array('input', 'string'), 0, array('input')),
			'fieldtype' => array('Поле ввода', array('input', 'string'), 1, array('select', 'form_fieldtypes')),
			'datatype' => array('Тип данных', array('input', 'string'), 1, array('select', 'form_datatypes')),			
			//'list_id' => array('Список', array('foreign', 'lists_main', 0), 0),
			'req' => array('Обязательное', array('input', 'number'), 0, array('checkbox',1)),
			'form_id' => array('', array('foreign', 'forms',1), 1),
			'pos' => array('', array('auto_increment', 'form_id'), 1)
		), array('name', 'pos'), array(0,0),
		'init_by' => 'form_id'
	),
	'folders' => array(
		array('Категории', 'категорию'),
		array(
			'name' => array('Название', array('input', 'string'), 1, array('input')),					
			'url' => array('Адрес', array('input', 'item_url', 'by_fields' => array('name')), 1, array('item_url'), 'unique' => 'entity'),	 
			'show_products' => array(isset($_GET['entity']) && $_GET['entity'] == 'catalog' ? 'Раскрыть подкатегории' : '', array('input', 'number'), 0, array('checkbox',0)),
			'collective_buy' => array(isset($_GET['entity']) && $_GET['entity'] == 'catalog' ? 'Коллективная закупка' : '', array('input', 'number'), 0, array('checkbox',0)),
			'short_desc' => array('Краткое описание', array('input', 'string'), 0, array('textarea', 0)),  
			'description' => array('Полное описание', array('input', 'string'), 0, array('textarea', 1)),
			'catalog_related_folders' => array(isset($_GET['entity']) && $_GET['entity'] == 'catalog' ? 'Сопутствующие категории' : '', array('multiple_foreign', 'folders', 'folder1', 'folder2', 'entity="catalog"', 'no_limit' => 1)),
			//'page_url' => array('Адрес страницы с элементами', array('input', 'string'), 0, array('input')),
			//'image' => array('Иконка', array('image_to_resize', array(array('178,146', 'l', 'img/folders/', 2), array('346,280', 'm', 'img/folders/', 0)), 'save_orig' => 'img/folders/'), 0),
			'html_title' => array('SEO Title', array('input', 'string'), 0, array('textarea', 0)),
			'html_keywords' => array('SEO keywords', array('input', 'string'), 0, array('textarea', 0)),
			'html_description' => array('SEO description', array('input', 'string'), 0, array('textarea', 0)),
			'parent' => array('', array('foreign', 'folders',1), 0),
			'entity' => array('', array('input', 'string'), 1),
			'entity_foreign_id' => array('', array('input', 'number'), 0),
			'pos' => array('', array('auto_increment', 'entity,parent,entity_foreign_id'), 0),
			'active' => array('Активность', array('input', 'number'), 0, array('checkbox',1))
		), array('name', 'pos')
	),
	'lists_main' => array(
		array('Справочники', 'справочник'),
		array(
			'title' => array('Название', array('input', 'string'), 1, array('input')),
			'folder_id' => array('', array('foreign', 'folders', 1), 0),
			'pos' => array('', array('auto_increment', 'folder_id'))
		), array('title', 'pos'), array(0,0), 1
	),
	'lists_elements' => array(
		array('Элементы', 'элемент'),
		array(
			'title' => array('Название', array('input', 'string'), 1, array('input')),
			'list_id' => array('', array('foreign', 'lists_main',1), 1),
			//'pos' => array('', array('auto_increment', 'list_id'))
		), array('title', 'title'),
		'init_by' => 'list_id'
	),
	
	'items_properties' => array(
		array('Свойства', 'свойство'),
		array(
			'name' => array('Название', array('input', 'string'), 1, array('input')),
			//'in_card' => array('Отображать в карточке', array('input', 'number'), 0, array('checkbox', 0)),
			'pos' => array('', array('auto_increment'), 1),
			//'folder_id' => array('', array('foreign', 'folders', 1), 0),
		), array('name', 'pos'), array(0,0)
	),
	'items_property_values' => array(
		array('', ''),
		array(
			'property_id' => array('', array('foreign', 'items_properties', 1)),
			'value' => array('Значение', array('input', 'string'), 0, array('input')),
			'item_id' => array('', array('foreign', 'articles', 1)),
		), array('property_id', 'property_id'), array(0,0)
	),
	'articles_photos' => array(
		array('Фото', 'фото'),
		array(
			'pict' => array('Фото', array('image_to_resize', array(array('150,148', 'sq', 'img/articles/more/', 2), array('480,0', 'm', 'img/articles/more/',1)), 'img/articles/more/big/'), 1),
			'title' => array('Описание', array('input', 'string'), 1, array('textarea', 0)),
			'item_id' => array('', array('foreign', 'articles', 1), 0),
			'pos' => array('', array('auto_increment', 'item_id'), 1)
		), array('pict', 'pos'), array(0,0)
	),
		
	'user_groups' => array(
		array('Типы пользователей', 'тип'),
		array(
			'name' => array('Название', array('input', 'text'), 1, array('input')),
			'admin_panel_access' => array('Доступ в систему управления', array('input', 'number'), 0, array('checkbox', 0))
		),
		array('name', 'pos'), array(0, 1)
	),
	'user_groups_access_rules' => array(
		array('Правила доступа', 'правило'),
		array(
			'target' => array('Объект', array('input', 'text'), 1, array('input')),
			'access' => array('Доступ', array('input', 'text'), 1, array('input')),
			'group_id' => array('', array('foreign', 'user_groups', 1), 1)
		),
		'no_access_rules' => true
	),	
	
	/**
	 *  Project 
	 **/
	
	'orders' => array(
		array('Заказы', ''),
		array(
			'date' => array('', array('date', 'form', 1), 0),	
			//'company' => array('Компания', array('input', 'string'), 0, array('input')),
			'surname' => array('Фамилия', array('input', 'string'), 0, array('input')),
			'name' => array('Имя', array('input', 'string'), 0, array('input')),
			'p_name' => array('Отчество', array('input', 'string'), 0),
			'passport' => array('Паспорт', array('input', 'string'), 0),
			'phone' => array('Телефон', array('input', 'string'), 0),
			'email' => array('E-mail', array('input', 'string'), 0, array('input')),
			'delivery_time' => array('', array('input', 'string'), 0),
			'address_post_index' => array('Почтовый индекс', array('input', 'text'), 0, array('input')),
			'address_region' => array('Область', array('input', 'text'), 0, array('input')),
			'address_city' => array('Город', array('input', 'text'), 0, array('input')),
			'address_street' => array('Улица', array('input', 'text'), 0, array('input', 0)),
			'address_house' => array('Дом', array('input', 'text'), 0, array('input', 0)),
			'address_corp' => array('Корпус', array('input', 'text'), 0, array('input', 0)),
			'address_flat' => array('Квартира/офис', array('input', 'text'), 0, array('input', 0)),
			//'address' => array('Адрес', array('input', 'text'), 0, array('textarea', 0)),
			'comments' => array('Комментарий', array('input', 'string'), 0, array('textarea', 0)),
			'sum' => array('', array('input', 'number'), 0),
			'sum_discount' => array('', array('input', 'number'), 0),
			'user_id' => array('', array('input', 'number'), 0),
			'wishlist_id' => array('', array('input', 'number'), 0),
			'is_regular' => array('', array('input', 'number'), 0),
			'from_regular' => array('', array('input', 'number'), 0),
			'payment' => array('', array('input', 'string'), 0, array('input')),
			'delivery_type' => array('', array('input', 'string'), 0, array('input')),
			'delivery_price' => array('', array('input', 'number'), 0, array('input')),
			'delivery_tracking_number' => array('', array('input', 'string'), 0, array('input')),
			'status' => array('', array('foreign', 'order_statuses'), 0),
			'status_payment' => array('', array('foreign', 'order_statuses'), 0),
			'regular_rate' => array('', array('input', 'string'), 0),
			'regular_day' => array('', array('input', 'numer'), 0),
			'id' => array('', array('input', 'number'), 0, array('input'))
		), array('id,date,name,surname,sum_discount', 'date desc'), array(1,0)
		,'filter' => array('id', 'name', 'email', 'status', 'status_payment'),
		'use_pager' => 40
	),
	'order_items' => array(
		array('Просмотр заказа', 'товар'),
		array(
			'order_id' => array('', array('foreign', 'orders', 1), 1),
			'item_id' => array('Товар', array('input', 'number'), 0),
			'items_num' => array('Кол-во', array('input', 'number'), 1),
			'price' => array('Цена', array('input', 'number'), 0),
			//'item_variant' => array('Вкус', array('input', 'number'), 0)
		), array('id', 'id'), array(1,1),
		'init_by' => 'order_id'
	),
	'order_statuses' => array(
		array('Статусы заказов', 'статус'),
		array(
			'name' => array('Название', array('input', 'string'), 1),		
			'status_type' => array(
				'Тип', 
				array('input', 'string'), 
				1, 
				array(
					'select',
					array(
						array('common', 'Общий'),
						array('payment', 'Оплата')
					)
				)
			),
			'append_bonus' => array('Начислить бонусы', array('input', 'number'), 0, array('checkbox', 0)),
			'pos' => array('', array('auto_increment', 'status_type'))
		), array('name,status_type', 'status_type,pos'), array(0,0)
	),
	'orders_status_history' => array(
		array(),
		array(
			'order_id' => array('', array('foreign', 'orders', 1))
		)
	),
	
	'brands' => array(
		array('Производители', 'производителя'),
		array(
			'name' => array('Название', array('input', 'string'), 0, array('input')),
			'url' => array('Адрес', array('input', 'item_url', 'by_fields' => array('name')), 1, array('item_url')),
			'collective_buy' => array('Коллективная закупка', array('input', 'number'), 0, array('checkbox',0)),
			//'site_url' => array('Сайт', array('input', 'string'), 0, array('input')),
			'image' => array('Лого', array('image_to_resize', array(array('300,300', 's', 'img/brands/', 0)), 'put_in_fields' => array('logo')), 0),
			'short_desc' => array('Краткое описание', array('input', 'string'), 0, array('textarea', 0)),
			'description' => array('Полное описание', array('input', 'string'), 0, array('textarea',1)),
			'html_title' => array('SEO Title', array('input', 'string'), 0, array('textarea', 0)),
			'html_keywords' => array('SEO keywords', array('input', 'string'), 0, array('textarea', 0)),
			'html_description' => array('SEO description', array('input', 'string'), 0, array('textarea', 0)),
			//'photogallery_id' => array('Фотогалерея', array('foreign', 'folders', 0, 'entity="photogallery"'), 0),
			'active' => array('Активность', array('input', 'number'), 0, array('checkbox',1)),			
			//'pos' => array('', array('auto_increment'), 1)
		), array('name', 'name'), array(0,0),
		 'filter' => array('name', 'collective_buy')
	),
	'catalog_special_types' => array(
		array('Типы выделенных товаров', 'тип'),
		array(
			'name' => array('Название', array('input', 'string'), 1, array('input')),
			//'name_single' => array('Название для одного элемента', array('input', 'string'), 0, array('input')),
			//'icon' => array('Иконка', array('file', 'img', 'img/special_types/'), 0),
			'pos' => array('', array('auto_increment'))
		), array('name', 'pos'), array(0,0)
	),		  
	'catalog' => array(
		array('Каталог', 'товар'),
		array(			
			'brand' => array('Производитель', array('foreign', 'brands', 0), 1),
			'model' => array('Наименование', array('input', 'string'), 1, array('input')),
			'package' => array('Упаковка', array('input', 'string'), 0, array('input')),
			'color' => array('Цвет', array('foreign', 'catalog_colors', 0, 'mode' => 'text', 'allow_create' => true), 0, array('input')),
			'article' => array('Артикул', array('input', 'string'), 0, array('input')),
			'url' => array('Адрес', array('input', 'item_url', 'by_fields' => array('brand', 'model', 'package', 'color')), 1, array('item_url'), 'unique' => 'folder_id'),			
			'avail' => array('Наличие', array('input', 'number'), 0, array('checkbox', 1)),
			'price' => array('Цена, руб.', array('input', 'float'), 0, array('input')),
			//'currency' => array('Валюта', array('foreign', 'currency', 1), 1),				  
			'discount' => array('Скидка, %', array('input', 'number'), 0, array('input')),
			'weight' => array('Вес, г', array('input', 'float'), 0, array('input')),	
			'collective_buy' => array('Коллективная закупка', array('input', 'number'), 0, array('checkbox',0)),
			//'catalog_special_products' => array('Выделить', array('multiple_foreign', 'catalog_special_types', 'product_id', 'special_type')),
			//'catalog_item_purposes' => array('Назначение', array('multiple_foreign', 'catalog_purposes', 'item_id', 'purpose_id')),
			'short_desc' => array('Краткое описание', array('input', 'string'), 0, array('textarea', 0)),
			'description' => array('Полное описание', array('input', 'string'), 0, array('textarea',1)),
			//'image' => array('Фото', array('image_to_resize', array(array('300,300', 'med', 'img/catalog/', 0)), 'save_orig' => 'img/catalog/'), 0),
			'html_title' => array('SEO Title', array('input', 'string'), 0, array('textarea', 0)),
			'html_keywords' => array('SEO keywords', array('input', 'string'), 0, array('textarea', 0)),
			'html_description' => array('SEO description', array('input', 'string'), 0, array('textarea', 0)),
			'pos' => array('', array('auto_increment', 'folder_id'), 1),		  
			'folder_id' => array('', array('foreign', 'folders',1), 0),	
			'catalog_related_products' => array('', array('multiple_foreign', 'catalog', 'product2', 'product1')),
			'active' => array('Активность', array('input', 'number'), 0, array('checkbox', 1))
		),
		array('brand,model,package,price', 'pos'),
		array(0,0),
		1,
		'title_fields' => array('brand', 'model', 'package', 'collective_buy'),
		'filter' => array('model', 'brand', 'article', 'avail', 'active'),
		'import' => true,
		'import_settings' => array(
			'fields' => array('article', 'price'),
			'unique' => 'article',
			'section_columns_num' => 3
		),
		'multi_update' => array('price', 'discount', 'avail', 'collective_buy', 'folder_id'),
		'copy' => true,
		'change_before_copy' => 'url'
	),
	
	
	
	'catalog_price_types' => array(
		array('Типы цен', 'тип'),
		array(
		 	'name' => array('Название', array('input','string'), 1, array('input')),
			//'xml_id' => array('XML ID', array('input','string'), 0, array('input')),
			'is_base' => array('Базовый', array('input', 'number'), 0, array('checkbox', 0)),
			'pos' => array('', array('auto_increment'), 0),
		),array('name', 'pos'),array(0,0)
	),
			
	'catalog_prices' => array(
		array('Цены', 'цену'),
		array(
			'product_id' => array('', array('foreign', 'catalog', 1), 1),
			'price_type_id' => array('Тип', array('foreign', 'catalog_price_types', 1), 0, 'unique' => 'product_id'),
			'price' => array('Цена', array('input', 'float'), 1, array('input'))
		),
		array('price_type_id,price', 'price_type_id'), 
		array(0,0),
		'force_access_rules' => true,
		'init_by' => 'product_id'
	),
	
	'catalog_item_variants' => array(
		array('Варианты', 'вариант'),
		array(
			'article' => array('Артикул', array('input', 'string'), 0, array('input')),
			'color' => array('Цвет', array('foreign', 'catalog_colors', 0, 'mode' => 'text', 'allow_create' => true), 0, array('input')),
			'lens_bc' => array(
				'Радиус кривизны', 
				array('input', 'float'), 
				0, 
				array(
					'select',
					'range' => array(
						'min' => 7,
						'step' => 0.1,
						'max' => 10
					)
				)
			),
			'lens_d' => array(
				'Оптическая сила', 
				array('input', 'float'), 
				0, 
				array(
					'select',
					'range' => array(
						'min' => -20,
						'step' => 0.25,
						'max' => 20
					)
				)
			),
			'lens_intensity' => array(
				'Интенсивность', 
				array('input', 'number'), 
				0, 
				array(
					'select',
					'range' => array(
						'min' => 0,
						'step' => 5,
						'max' => 300
					)
				)
			),
			'lens_dia' => array(
				'Диаметр', 
				array('input', 'float'), 
				0, 
				array(
					'select',
					'range' => array(
						'min' => 0,
						'step' => 0.1,
						'max' => 20
					)
				)
			),
			'lens_ax' => array(
				'Ось', 
				array('input', 'float'), 
				0, 
				array(
					'select',
					'range' => array(
						'min' => 10,
						'step' => 10,
						'max' => 200
					)
				)
			),
			'lens_cyl' => array(
				'Цилиндр', 
				array('input', 'float'), 
				0, 
				array(
					'select',
					'range' => array(
						'min' => -5,
						'step' => 0.25,
						'max' => 0
					)
				)
			),
			//'price' => array('Цена', array('input', 'float'), 0, array('input')),
			'quantity' => array('Наличие, шт.', array('input', 'float'), 0, array('input'), 'not_null' => true),
			//'avail' => array('В наличии', array('input', 'number'), 0, array('checkbox', 1)),
			'item_id' => array('', array('foreign', 'catalog',1), 0),
			'pos' => array('', array('auto_increment', 'item_id'))
		), array('article,color,lens_bc,lens_d,quantity', 'pos'), array(0,0),
		'init_by' => array('item_id'),
		'allow_null' => true,
		'multi_insert' => array('color', 'lens_bc', 'lens_d', 'lens_intensity', 'lens_dia', 'lens_ax', 'lens_cyl')
	),
	
	'catalog_photos' => array(
		array('Фото', 'фото'),
		array(
			'pict' => array('Фото', array('image_to_resize', array(array('300,300', 'med', 'img/catalog/more/', 0), array('150,150', 'sq', 'img/catalog/more/', 1), array('1280,1280', 'big', 'img/catalog/more/', 0)), 'save_orig' => 'img/catalog/more/'), 0),
			'title' => array('Описание', array('input', 'string'), 0, array('input')),
			'item_id' => array('', array('foreign', 'catalog', 1), 0),
			'pos' => array('', array('auto_increment', 'item_id'))
		), array('pict', 'pos'), array(0,0),
		'init_by' => 'item_id',
		'multi_upload' => true
	),
	
	'catalog_item_variants_photos' => array(
		array('Фото', 'фото'),
		array(
			'pict' => array('Фото', array('image_to_resize', array(array('150,150', 'med', 'img/catalog/variants/', 2), array('1280,1280', 'big', 'img/catalog/variants/', 0)), 'save_orig' => 'img/catalog/variants/'), 1),
			'variant_id' => array('', array('foreign', 'catalog_item_variants', 1), 0),
			'pos' => array('', array('auto_increment', 'variant_id'), 1)
		), array('pict', 'pos'), array(0,0),
		'init_by' => 'variant_id',
		'multi_upload' => true
	),
	
	'catalog_related_products' => array(
		array('Сопутствующие товары', 'товар'),
		array(
			'product1' => array('', array('foreign', 'catalog', 1), 1),
			'product2' => array('Товар', array('foreign', 'catalog', 1), 1, 'unique' => 'product1'),
			'pos' => array('', array('auto_increment', 'product1'), 1)
		), array('product2', 'pos'), array(0,1),
		'init_by' => array('product1')
	),
	'catalog_colors' => array(
		array('Цвета', 'цвет'),
		array(
			'name' => array('Название', array('input', 'string'), 1, array('input'))
		), array('name', 'id'), array(0,0)
	),
	'catalog_special_types' => array(
		array('Типы выделенных товаров', 'тип'),
		array(
			'name' => array('Название', array('input', 'string'), 1, array('input')),
			//'name_single' => array('Название для одного элемента', array('input', 'string'), 0, array('input')),
			//'icon' => array('Иконка', array('file', 'img', 'img/news/'), 0),
			'pos' => array('', array('auto_increment'))
		), array('name', 'pos'), array(0,0)
	),
	
	'catalog_properties' => array(
		array('Характеристики товаров', 'характеристику'),
		array(
			'name' => array('Название', array('input', 'string'), 1, array('input')),
			'filter_title' => array('Название в фильтре', array('input', 'string'), 0, array('input')),
			'type' => array(
				'Тип', 
				array('input', 'string'), 
				1, 
				array(
					'select',
					array(
						array('text', 'Текст'),
						array('list', 'Справочник'),
						array('number', 'Число')
					)
				)
			),
			'list_id' => array('Справочник', array('foreign', 'lists_main', 0), 0),
			'is_multiple' => array('Множественное', array('input', 'number'), 0, array('checkbox', 0)),
			'in_card' => array('На странице товара', array('input', 'number'), 0, array('checkbox', 1)),
			'show_in_filter' => array('В фильтре', array('input', 'number'), 0, array('checkbox', 0)),
			'pos' => array('', array('auto_increment', 'folder_id,group_id'), 1),
			'folder_id' => array('', array('foreign', 'folders', 1)),
		), array('name', 'pos'), array(0,0),
		'init_by' => (!isset($_GET['entity']) || in_array($_GET['entity'], array('catalog', 'catalog_properties')) ? 'folder_id' : ''),
		'allow_no_init' => true
	),
	
	'catalog_property_groups' => array(
		array('Группы', 'группу свойств'),
		array(
			'name' => array('Название', array('input', 'string'), 1, array('input')),
			'pos' => array('', array('auto_increment', 'folder_id'), 1),
			'folder_id' => array('', array('foreign', 'folders', 1)),
		), array('name', 'pos'), array(0,0)
	),
	
	'catalog_item_properties' => array(
		array('', ''),
		array(
			'property_id' => array('', array('foreign', 'catalog_properties', 1)),
			'value' => array('Значение', array('foreign', 'catalog_property_values', 1), 0, array('input')),
			'value_int' => array('Значение числовое', array('input', 'number'), 0, array('input')),
			'list_element_id' => array('Значение справочника', array('foreign', 'lists_elements', 1), 0, array('input')),
			'item_id' => array('', array('foreign', 'catalog', 1)),
		), array('property_id', 'property_id'), array(0,0)
	),
	
	'catalog_property_values' => array(
		array('', ''),
		array(
			'name' => array('Значение', array('input', 'string'), 0, array('input')),
		), array('name', 'name'), array(0,0)
	),
	
	'catalog_comments'=>array(
		array('Отзывы','отзыв'),
		array(
			'created_at' => array('Дата', array('date', 'form', 1), 0),
			'product_id'=>array('', array('foreign', 'catalog', 1), 1),                    
			//'pro'=>array('Достоинства', array('input', 'text'), 0, array('textarea',0)),
			//'contra'=>array('Недостатки', array('input', 'text'), 0, array('textarea',0)),
			'comment'=>array('Отзыв', array('input', 'text'), 0, array('textarea',0)),
			//'mark'=>array('Оценка', array('input', 'number'), 0, array('input')),
			'user_id'=>array('Пользователь', array('foreign', 'users', 0, 'is_tmp=0'), 0),
			'author_name'=>array('Имя', array('input', 'text'), 0, array('input')),
			'author_email'=>array('E-mail', array('input', 'text'), 0, array('input')),
			'author_city'=>array('Город', array('input', 'text'), 0, array('input')),
			'rate_good'=>array('Нравится', array('input', 'number'), 0, array('input')),
			'rate_bad'=>array('Не нравится', array('input', 'number'), 0, array('input')),
			'active'=>array('Опубликовать',array('input','number'),0,array('checkbox',0))
		),array('comment,rate_good,rate_bad','id desc'),array(0,0),
		'use_pager' => 40,
		'direct_access_rules' => true,
		'init_by' => 'product_id'
	),

	'discounts' => array(
		array('Скидки', 'скидку'),
		array(
			'sum_value' => array('Сумма от (руб.)', array('input', 'number'), 1, array('input')),
			'discount' => array('Скидка (%)', array('input', 'number'), 1, array('input')),
			//'icon' => array('Иконка', array('file', 'image', 'img/discounts/'), 0)
		), array('sum_value,discount', 'sum_value'), array(0,0)
	), 
	
	'discount_cards' => array(
		array('Дисконтные карты', 'карту'),
		array(
			'number' => array('Номер', array('input', 'number'), 1, array('input'), 'unique' => ''),
			'discount' => array('Скидка (%)', array('input', 'number'), 1, array('input')),
			'name' => array('ФИО', array('input', 'text'), 0, array('input')),
		), array('number,discount,name', 'number'), array(0,0),
		'filter' => array('number', 'name'),
		'use_pager' => 40
	),
	
	'delivery_types' => array(
		array('Способы доставки', 'способ доставки'),
		array(
			'name' => array('Название', array('input', 'text'), 1, array('input')),
			'avail_from' => array('Сумма заказа от', array('input', 'number'), 1, array('input')),
			'external_calc' => array('Внешний калькулятор', array('input', 'text'), 0, array('select', 'delivery_calc')),
			'box_tariff'=>array('Тариф за выдачу через ячейку',array('input','float'),0,array('input')),
			'cities_text' => array('Текст "выберите город"', array('input', 'text'), 0, array('input')),
			//'title_info' => array('Подпись под заголовком', array('input', 'text'), 0, array('input')),
			'short_description' => array('Краткое описание', array('input', 'string'), 0, array('textarea', 0)),
			'description' => array('Описание', array('input', 'string'), 0, array('textarea',1)),
			//'icon' => array('Иконка', array('file', 'img', 'img/delivery/'), 0),
			//'show_on_page' => array('Показывать на странице "доставка"', array('input', 'number'), 0, array('checkbox',1)), 
			'delivery_types_groups' => array('Регионы', array('multiple_foreign', 'delivery_groups', 'type_id', 'group_id')), 
			'need_address_post_index' => array('Почтовый индекс', array('input', 'number'), 0, array('checkbox',0)),
			'need_address_region' => array('Область', array('input', 'number'), 0, array('checkbox',0)),
			'need_address_city' => array('Город', array('input', 'number'), 0, array('checkbox',0)),
			'need_address' => array('Адрес', array('input', 'number'), 0, array('checkbox',0)),
			'need_passport' => array('Паспорт', array('input', 'number'), 0, array('checkbox',0)),
			'need_surname' => array('Фамилия', array('input', 'number'), 0, array('checkbox',0)),
			'need_p_name' => array('Отчество', array('input', 'number'), 0, array('checkbox',0)),			
			'folder_id' => array('', array('foreign', 'folders',1), 0),
			'pos' => array('', array('auto_increment', 'folder_id'), 0)
		), array('name', 'pos'), array(0,0)
	),
	'delivery_cities' => array(
		array('Города', 'город'),
		array(
			'name' => array('Название', array('input', 'text'), 1, array('input')),
			'zone_id' => array('Тарифная зона', array('foreign', 'delivery_tariff_zones', 0, (isset($_GET['type_id']) ? 'type_id='.intval($_GET['type_id']) : '1')), 0),
			'is_center' => array('Областной центр', array('input', 'number'), 0, array('checkbox', 0)),
			//'price' => array('Стоимость', array('input', 'number'), 0, array('input')),
			'type_id' => array('', array('foreign', 'delivery_types', 1)),
			'pos' => array('', array('auto_increment', 'type_id'), 0)
		), array('name', 'pos'), array(0,0),
		'init_by' => 'type_id'
	),
	'delivery_tariff_zones' => array(
		array('Тарифные зоны','зону'),
		array(
			'name'=>array('Название',array('input','text'),1,array('input')),
			'tariff'=>array('Тариф',array('input','float'),0,array('input')),
			'type_id'=>array('',array('foreign','delivery_types', 1)),
		),array('name,tariff', 'id'),array(0,0),'use_pager' => 40,
		'init_by' => 'type_id'
	),
	'delivery_prices' => array(
		array('Стоимость доставки', 'стоимость'),
		array(
			'order_sum_from' => array('Сумма заказа от', array('input', 'number'), 0, array('input')),
			'price' => array('Стоимость', array('input', 'number'), 0, array('input')),
			//'price_for_members' => array('Стоимость для владельцев карт', array('input', 'number'), 0, array('input')),
			'type_id' => array('', array('foreign', 'delivery_types', 1)),
			'city_id' => array('', array('foreign', 'delivery_cities', 1)),
		), array('order_sum_from,price,price_for_members', 'order_sum_from'), array(0,0),
		'init_by' => isset($_GET['entity']) && $_GET['entity'] == 'delivery_cities' || isset($_GET['city_id']) ? 'city_id' : 'type_id'
	),
	'delivery_groups' => array(
		array('Регионы доставки','регион'),
		array(
			'name'=>array('Название',array('input','text'),1,array('input')),
			//'short_descr'=>array('Краткое описание',array('input','string'),0,array('textarea',0)),
			//'description'=>array('Описание',array('input','string'),0,array('textarea',1)),
			'pos' => array('', array('auto_increment'), 0)
		),
		array('name', 'pos'),array(0,0)
	),
	'payment_types' => array(
		array('Способы оплаты', 'способ оплаты'),
		array(
			'name' => array('Название', array('input', 'text'), 1, array('input')),
			'payments_deliveries' => array('Типы доставки', array('multiple_foreign', 'delivery_types', 'payment_type', 'delivery_type')), 
			//'notes' => array('Пояснения', array('input', 'string'), 0, array('textarea', 0)), 
			'short_description' => array('Краткое описание', array('input', 'string'), 0, array('textarea', 0)),
			'description' => array('Описание', array('input', 'string'), 0, array('textarea',1)),			
			'link_url' => array('Адрес страницы оплаты', array('input', 'text'), 0, array('input')),
			'link_title' => array('Текст ссылки', array('input', 'text'), 0, array('input')),
			'payment_component' => array('Компонент оплаты', array('input', 'text'), 0, array('input')),
			//'icon' => array('Иконка', array('file', 'img', 'img/payments/'), 0),
			//'show_on_page' => array('Показывать на странице "способы оплаты"', array('input', 'number'), 0, array('checkbox',1)), 
			'folder_id' => array('', array('foreign', 'folders',1), 0),
			'pos' => array('', array('auto_increment', 'folder_id'), 0)
		), array('name', 'pos'), array(0,0)
	),
	
	'currency' => array(
		array('Валюты', 'валюту'),
		array(
			'name' => array('Наименование', array('input', 'string'), 1, array('input')),
			'name' => array('Код', array('input', 'string'), 1, array('input')),
			'course' => array('Курс', array('input', 'float'), 1, array('input')),
			//'symbol' => array('Шаблон', array('input', 'string'), 0, array('input')),
			//'dec_separator' => array('Разделитель', array('input', 'string'), 0, array('input')),
			'pos' => array('', array('auto_increment'))
		), array('name', 'pos')
	),
	
	'catalog_special_offers' => array(
		array('Спецпредложения', 'акцию'),
		array(
			'name' => array('Название',  array('input', 'string'), 1, array('input')),
			'url' => array('URL',  array('input', 'item_url', 'by_fields' => array('name')), 1, array('item_url')),
			'discount' => array('Скидка',  array('input', 'float'), 0, array('input')),
			'unit' => array(
				'Единица измерения',
				array(
					'input',
					'string'
				),
				1,
				array(
					'select',
					array(
						array('percent', '%'),
						array('money', 'руб.')
					)
				)
			),
			'rule' => array(
				'Применение',
				array(
					'input',
					'string'
				),
				1,
				array(
					'select',
					array(
						array('any', 'Любой товар'),
						array('combo', 'Комбо')
					)
				)
			),
			'start_at' => array('Начало',  array('date', 'form', 1), 0, array('input')),
			'end_at' => array('Окончание',  array('date', 'form', 1), 0, array('input')),
			//'banner_title' => array('Заголовок баннера',  array('input', 'string'), 0, array('input')),
			'short_descr' => array('Краткое описание',  array('input', 'string'), 0, array('textarea', 0)),
			//'link_text' => array('Текст ссылки',  array('input', 'string'), 0, array('input')),
			//'image' => array('Фото', array('image_to_resize', array(array('1000,0','b','img/promo/',2)), 'img/promo/'), 0),
			'description' => array('Подробное описание',  array('input', 'string'), 0, array('textarea', 1)),
			//'show_banner' => array('Показывать баннер', array('input', 'number'), 0, array('checkbox', 1)),
			//'show_banner_text' => array('Текст на баннере', array('input', 'number'), 0, array('checkbox', 1)),
			//'show_countdown' => array('Показывать счетчик', array('input', 'number'), 0, array('checkbox', 1)),
			//'group_by' => array('Группировать', array('input', 'string'), 0, array('select', 'groups_group_by')),
			//'active' => array('Активность', array('input', 'number'), 0, array('checkbox', 1)),
			//'pos' => array('', array('auto_increment')),
		),
		array('name,discount,start_at,end_at', 'end_at < NOW(),start_at,end_at'),
		array(0,0),
		'allow_null' => true
	),
	'catalog_special_offers_items' => array(
		array('Элементы', 'выборку'),
		array(
			'section_id' => array(
				'Категория', 
				array(
					'foreign', 
					'folders', 
					1, 
					'entity="catalog"'
				), 
				0,
				'onchange' => array(
					'update' => array(
						array(
							'field' => 'product_id',
							'foreign_field' => 'folder_id'
						)
					)
				)
			),
			'brand_id' => array('Производитель', array('foreign', 'brands', 1), 0),
			'product_id' => array('Товар', array('foreign', 'catalog', 1, 'no_fill' => true), 0, 'foreign_title_fields' => array('brand', 'model', 'package')),
			'discount' => array('Скидка',  array('input', 'float'), 0, array('input')),
			'unit' => array(
				'Единица измерения',
				array(
					'input',
					'string'
				),
				0,
				array(
					'select',
					array(
						array('percent', '%'),
						array('money', 'руб.')
					)
				)
			),
			'min_order' => array('Минимальный заказ',  array('input', 'number'), 0, array('input')),
			'group_quantity' => array('Кратность',  array('input', 'number'), 0, array('input')),
			//'title' => array('Текст ссылки',  array('input', 'string'), 0, array('input')),
			//'items_limit' => array('Кол-во товаров',  array('input', 'number'), 0, array('input')),
			//'order_by' => array('Сортировка',  array('input', 'string'), 0, array('input')),
			//'active' => array('Активность', array('input', 'number'), 0, array('checkbox', 1)),
			'offer_id' => array('', array('foreign', 'catalog_special_offers', 1), 1),
			'pos' => array('', array('auto_increment', 'offer_id'))
		),array('section_id,brand_id,product_id', 'pos'),
		array(0,0),
		0,
		'allow_null' => true,
		'init_by' => 'offer_id'
	),
	
	'start_page_catalog_folders' => array(
		array('Категории на главной', 'категорию'),
		array(
			'folder_id' => array('Категория', array('foreign', 'folders',1), 0),
			'pos' => array('', array('auto_increment'))
		), array('folder_id', 'pos'), array(0,0)
	),
	
	'catalog_certificates_groups' => array(
		array('Сертификаты', 'группу'),
		array(
			'title' => array('Наименование', array('input', 'string'), 1, array('input')),
			'list_id' => array(
				'Справочник', 
				array('foreign', 'lists_main', 0), 
				1,
				'onchange' => array(
					'update' => array(
						array(
							'field' => 'list_element_id',
							'foreign_field' => 'list_id'
						)
					)
				)
			),
			'list_element_id' => array('Значение', array('foreign', 'lists_elements', 0), 1, array('input')),
			'pos' => array('', array('auto_increment'))
		), array('title', 'pos')
	),
	'catalog_certificates' => array(
		array('Сертификаты', 'документ'),
		array(
			'file' => array('Файл', array('file', '', 'uploads/docs/', 'orig_filename' => true), 1, array('input')),
			'title' => array('Наименование', array('input', 'string'), 0, array('input')),
			'group_id' => array('', array('foreign', 'catalog_certificates_groups', 1), 1),
			'pos' => array('', array('auto_increment', 'group_id'))
		), array('file,title', 'pos'),
		'init_by' => 'group_id'
	),
	
	'regions' => array(
		array('Города для выбора', 'тип'),
		array(
		   'name' => array('Название', array('input', 'text'), 1, array('input')),
		   'is_default' => array('По умолчанию', array('input', 'number'), 0, array('checkbox', 0)),
		   'is_bold' => array('Выделить', array('input', 'number'), 0, array('checkbox', 0)),
		),
		array('name', 'name'), array(0, 0)
	),
	
	'bonus_values' => array(
		array('Бонусы','бонус'),
		array(
			'order_sum'=>array('Сумма от, руб.',array('input','number'), 1, array('input')),
			'bonus_value'=>array('Бонус, %',array('input','number'), 1, array('input')),
			//'color'=>array('Цвет',array('input', 'string'),0,array('input'))
		),
		array('order_sum,bonus_value', 'order_sum'),
		array(0,0)
	)
	
);
?>