<?
$entities = array(
             'structure'=>array(
                 array('Структура сайта','страницу'),
                 array(
                    'name'=>array('Название',array('input','string'),1,array('input')),  
                    //'lang'=>array('Язык',array('input','string'),1,array('select','langs',1)),
                    'navi_type'=>array('Меню',array('input','string'),1,array('select','navi_types')), 
                    'parent'=>array('Родитель',array('input','string'),1,array('select','parents')),
                    'url'=>array('Адрес',array('input','item_url', 'by_fields' => array('name')),1,array('item_url'), 'unique' => ''),                                                             
                    'content'=>array('Основной контент',array('input','string'),0,array('textarea',1)),
                    'html_title'=>array('HTML Title',array('input','string'),0,array('textarea',0)),
                    'html_keywords'=>array('META keywords',array('input','string'),0,array('textarea',0)),
                    'html_description'=>array('META description',array('input','string'),0,array('textarea',0)),                    
                    'visible'=>array('Отображать в меню',array('input','number'),0,array('checkbox',1)),        
                    'hard_link'=>array('Прямая ссылка',array('input','string'),0,array('input')),
                    'pict'=>array('Баннер',array('file','image','img/pages/'),0),
					'short_descr'=>array('Текст баннера',array('input','string'),0,array('textarea',0)),
                    //'pict_hover'=>array('Ссылка в меню (активная)',array('file','image','img/pages/'),0),
					'affiliates_structure' => array('Филиалы', array('multiple_foreign', 'affiliates', 'page_id', 'affiliate_id'), 0),
                    'allow_extra_params'=>array('ЧПУ',array('input','number'),0,array('checkbox',0)),
                    'extra_params_limit'=>array('Макс. кол-во доп. параметров',array('input','number'),0,array('input')),
                    'set_get_vars'=>array('Назначить параметры запроса переменным',array('input','string'),0,array('input')),
                    'pos'=>array('',array('auto_increment', 'navi_type,parent'),0)         
                 ),array('name','pos'),array(1,0),0,array('navi_type'=>'parent'),
				 'save_author_id' => true
             ),
             'pages_components'=>array(
                 array('Компоненты','компонент'),
                 array(
                    'component'=>array('Компонент',array('input','string'),1,array('select','arr_components')),
                    'page_id'=>array('',array('foreign','structure',1),1),
                    'pos'=>array('',array('auto_increment','page_id'))
                 ),array('component','pos'),array(0,1),
				 'common_edit_title' => true,
				 'init_by' => 'page_id'
             ),
             'components_configs'=>array(
                 array('Параметры',''),
                 array(
                    'centry_id'=>array('',array('foreign','pages_components',1))
                 ),array(),array(),0,0,array('content_admin')
             ),
             'users'=>array(
                 array('Пользователи','пользователя'),
                 array(                    
                    //'surname'=>array('Фамилия',array('input','text'),0,array('input')),
                    'name'=>array('Имя',array('input','text'),0,array('input')),
                    //'company'=>array('Компания',array('input','text'),0,array('input')),
                    //'p_name'=>array('Отчество',array('input','text'),0,array('input')),
                    'email'=>array('E-mail',array('input','email'),0,array('input')),
                    'phone'=>array('Телефон',array('input','text'),0,array('input')),
                    //'country'=>array('Страна',array('input','text'),0,array('input')),
                    //'city'=>array('Город',array('input','text'),0,array('input')),
                    'address'=>array('Адрес',array('input','text'),0,array('textarea', 0)),
                    'orders_sum'=>array('Сумма заказов',array('input','number'),0,array('input')),
                    'login'=>array('Логин',array('input','text'),1,array('input'), 'unique' => ''),
                    'password'=>array('Пароль',array('input','password'),1,array('input')),
                    'group_id' => array('Группа', array('foreign', 'user_groups', 0), 0),
					'is_admin' => array('Администратор', array('input', 'number'), 0, array('checkbox', 0)),
                    'activated' => array('Активирован', array('input', 'number'), 0, array('checkbox', 1)),
					'affiliate_id' => array('Филиал', array('foreign', 'affiliates'), 0),
                    "added_tsmp" => array("", array("timestamp_add")),
                    "activated_tsmp" => array("", array("timestamp_add"))
                 ),array('name,phone,address','is_admin=0'),array(0,0),
                 'filter' => array('login', 'name', 'phone', 'email', 'group_id', 'affiliate_id'),
                 'default_filter' => array('is_tmp' => 0),
                 'use_pager' => 40
             ),
			 'user_groups' => array(
				array('Типы пользователей', 'тип'),
				array(
					'name' => array('Название', array('input', 'text'), 1, array('input')),
					'slug_name' => array('Символическое имя', array('input', 'item_url'), 1, array('input')),
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
             'articles'=>array(
                 array('Статьи','статью'),
                 array(
                    'date'=>array('Дата',array('date','form'),1),
                    'title'=>array('Заголовок',array('input','string'),1,array('input')),
                    'announce'=>array('Краткий текст',array('input','string'),0,array('textarea',0)),
                    'content'=>array('Содержание',array('input','string'),0,array('textarea',1)),
                    'video'=>array('Видео',array('input','string'),0,array('textarea',1)),
                    //'articles_products' => array('Товары', array('multiple_foreign', 'catalog', 'article_id', 'product_id', '1', 4, 'order_by' => 'brand_name,model', 'extra_fields' => 'BR.name as brand_name', 'left_join' => 'brands as BR on BR.id=T.brand', 'group_by' => 'brand_name', 'show_field' => 'model')),                                    
                    'direct_link'=>array('Прямая ссылка',array('input','string'),0,array('input')),
                    'image' => array('Фото', array('image_to_resize', array(array('150,148','sq','img/articles/', 2),array('480,0','m','img/articles/', 0),array('750,0','b','img/articles/', 0)), 'save_orig' => 'img/articles/big/'), 0),
                    'active'=>array('Активность',array('input','number'),0,array('checkbox',1)),
                    'author_id' => array('Автор',array('foreign','articles_authors',0),0),
					'affiliates_articles' => array('Филиалы', array('multiple_foreign', 'affiliates', 'article_id', 'affiliate_id'), 0),
                    'folder_id'=>array('',array('foreign','folders',1),0),
                 ),array('title','date desc'),array(0,1),1,'save_author_id' => true,
                 //'copy' => true,
                 //'change_before_copy' => 'url'
             ),
             'articles_products'=>array(
                 array('Товары','товар'),
                 array(
                    'product_id'=>array('Товар', array('foreign', 'catalog', 1), 1),
                    'article_id'=>array('',array('foreign','articles',1),1),
                    'pos'=>array('',array('auto_increment', 'article_id'),1)
                 ),array('product_id','pos'),array(0,1),
				 'init_by' => 'article_id'
             ),
             'photogallery'=>array(
                 array('Фотогалерея','фото'),
                 array(
                    'image' => array('Фото', array('image_to_resize', array(array('90,80','vs','img/photos/', 2),array('110,100','s','img/photos/', 2),array('680,680','b','img/photos/', 0))), 0),
                    'title'=>array('Описание',array('input','string'),0,array('input')),
                    //'direct_link'=>array('Прямая ссылка',array('input','string'),0,array('input')),
                    'folder_id'=>array('',array('foreign','folders',1),0),
                    'pos'=>array('',array('auto_increment'),1)
                 ),array('image','pos'),array(0,0),1
             ),
             'banners'=>array(
                 array('Баннеры','баннер'),
                 array(
                    'file'=>array('Файл',array('file','img','img/ab/'),0),
                    'link'=>array('Ссылка',array('input','string'),0,array('input')),
                    'code'=>array('Код',array('input','string'),0,array('textarea',0)),
                    'width'=>array('Ширина',array('input','number'),0,array('input')),
                    'height'=>array('Высота',array('input','number'),0,array('input')),
                    'placing'=>array('Размещение',array('input','string'),1,array('select','banners_placings')),
                    //'only_catalog'=>array('Только в каталоге',array('input','number'),0,array('checkbox',0)),
                    //'only_sections'=>array('Только в выбранных разделах',array('input','number'),0,array('checkbox',0)),
					'show_case'=>array('Условия показа',array('input','string'),0,array('input')),
                    'banner_sections' => array('В разделах', array('multiple_foreign', 'folders', 'banner_id', 'section_id', 'entity="catalog" and parent = 0', 'order_by' => 'name')),
                    //'banner_brands' => array('В брендах', array('multiple_foreign', 'brands', 'banner_id', 'brand_id')),                                       
                    'visible'=>array('Активность',array('input','number'),0,array('checkbox',1)),
					'affiliates_banners' => array('Филиалы', array('multiple_foreign', 'affiliates', 'banner_id', 'affiliate_id'), 0),
                    'pos'=>array('',array('auto_increment', 'placing'),1)
                 ),array('file','placing,pos'),array(1,1),
				 'filter' => array('placing', 'affiliates_banners', 'visible'),
				 'save_author_id' => true
             ),
            'files'=>array(
                 array('Файлы','файл'),
                 array(
                    'file'=>array('Файл',array('file','','files/'),1),
                    'description'=>array('Комментарий',array('input','string'),0,array('input')),
                    'pos'=>array('',array('auto_increment','folder_id'),0)
                 ),array('file,description','id'),array(0,0)
             ),
             'forms'=>array(
                 array('Формы','форму'),
                 array(
                    'name'=>array('Название',array('input','string'),1,array('input')),
                    'email'=>array('E-mail для отправки',array('input','text'),1,array('input')),
                    'success_text'=>array('Текст в случае успешной отправки',array('input','text'),1,array('textarea',0))
                 ),array('name','id'),array(0,0)
             ),
             'forms_fields'=>array(
                 array('Поля','поле'),
                 array(
                    'name'=>array('Название',array('input','string'),1,array('input')),
                    'fieldtype'=>array('Поле ввода',array('input','string'),1,array('select','form_fieldtypes')),
                    'datatype'=>array('Тип данных',array('input','string'),1,array('select','form_datatypes')),                    
                    //'list_id'=>array('Список',array('foreign','lists_main',0),0),
                    'req'=>array('Обязательное',array('input','number'),0,array('checkbox',1)),
                    'form_id'=>array('',array('foreign','forms',1),1),
                    'pos'=>array('',array('auto_increment','form_id'),1)
                 ),array('name','pos'),array(0,0),
				 'init_by' => 'form_id'
             ),
             'folders'=>array(
                 array('Разделы','раздел'),
                 array(
                    'name' => array('Название',array('input','string'),1,array('input')),
					'in_remote_regions' => array(isset($_GET['entity']) && $_GET['entity'] == 'catalog' ? 'В регионах' : '', array('input', 'number'), 0, array('checkbox', 0)),
					'products_limiter' => array(isset($_GET['entity']) && $_GET['entity'] == 'start_page_products' ? 'Ограничитель' : '',array('input','number'),0,array('input')),
                    'page_title' => array('Заголовок страницы',array('input','string'),0,array('input')),
                    //'name_accus' => array('Название (вин. падеж)',array('input','string'),0,array('input')),
                    'url'=>array('Адрес',array('input','item_url', 'by_fields' => array('name')),1,array('item_url'), 'unique' => 'entity'),              
                    'short_desc' => array('Описание на баннере',array('input','string'),0,array('textarea',0)),  
                    'description' => array('Описание',array('input','string'),0,array('textarea',1,1)),                    
                    'page_url' => array('Адрес страницы с элементами',array('input','string'), 0, array('input')),
                    'image' => array('Баннер', array('image_to_resize', array(array('1000,0','b','img/folders/', 0))), 0),
					'html_title'=>array('HTML Title',array('input','string'),0,array('textarea',0)),
                    'html_keywords'=>array('META keywords',array('input','string'),0,array('textarea',0)),
                    'html_description'=>array('META description',array('input','string'),0,array('textarea',0)),
					'affiliates_folders' => array('Филиалы', array('multiple_foreign', 'affiliates', 'folder_id', 'affiliate_id'), 0),
                    //'icon' => array('Фото',array('file','image','img/folders/'),0),
                    'parent'=>array('',array('foreign','folders',1),0),
                    'entity' => array('',array('input','string'),1),
                    'pos'=>array('',array('auto_increment','entity,parent'),0)
                 ),array('name','pos'),
				 'no_access_rules' => true,
				 'save_author_id' => true
             ),
             'lists_main'=>array(
                 array('Списки','список'),
                 array(
                    'title' => array('Название',array('input','string'),1,array('input')),
                    'pos'=>array('',array('auto_increment'))
                 ),array('title','pos'),
				 'no_access_rules' => true
             ),
             'lists_elements'=>array(
                 array('Элементы','элемент'),
                 array(
                    'title' => array('Название',array('input','string'),1,array('input')),
                    'list_id'=>array('',array('foreign','lists_main',1),1),
                    'pos'=>array('',array('auto_increment','list_id'))
                 ),array('title','pos'),
				 'no_access_rules' => true,
				 'init_by' => 'list_id'
             ),
             
             /**
              *  project 
              **/
             
             
             
             'orders' => array(
             	array('Заказы', ''),
             	array(
             		'name' => array('Имя', array('input', 'string'),0, array('input')),
             		'surname' => array('Фамилия', array('input', 'string'),0, array('input')),
             		'p_name' => array('Отчество', array('input', 'string'),0),
             		'phone' => array('', array('input', 'string'),0),
             		'email' => array('E-mail', array('input', 'string'),0, array('input')),
             		'delivery_time' => array('', array('input', 'string'),0),
             		'address' => array('', array('input', 'string'),0),
             		'discount_card' => array('', array('input', 'number'),0),
             		'comments' => array('', array('input', 'string'),0),
             		'user_id' => array('', array('input', 'number'),0),
             		'wishlist_id' => array('', array('input', 'number'),0),
             		'is_regular' => array('', array('input', 'number'),0),
             		'from_regular' => array('', array('input', 'number'),0),
             		'status' => array('Статус', array('foreign', 'order_statuses'),0),
             		'regular_rate' => array('', array('input', 'string'),0),
             		'regular_day' => array('', array('input', 'numer'),0),
             		'id' => array('ID', array('input', 'number'), 0, array('input')),
					'affiliate_id' => array('Филиал', array('foreign', 'affiliates'), 0),
             	),array('id', 'date desc'), array(1,0),
             	'filter' => array('id', 'name', 'email', 'status', 'affiliate_id'),
             	'use_pager' => 40
             ),
             'order_items' => array(
             	array('Просмотр заказа', 'товар'),
             	array(
             		'order_id' => array('', array('foreign', 'orders', 1), 1),
             		'item_id' => array('Товар', array('input', 'number'), 0),
             		'items_num' => array('Кол-во', array('input','number'), 1),
             		'item_variant' => array('Вкус', array('input', 'number'), 0)
             	),array('id', 'id'), array(1,1),
				'init_by' => 'order_id'
             ),
             'order_statuses' => array(
             	array('Статусы заказов', 'статус'),
             	array(
             		'name' => array('Название', array('input', 'string'),1),             		
					'allow_payment'=>array('Разрешить оплату',array('input','number'),0,array('checkbox',0)),
             		'pos' => array('', array('auto_increment'))
             	),array('name', 'pos'), array(0,0)
             ),
             
             'brands' => array(
                 array('Производители','производителя'),
                 array(
                    'name' => array('Название',array('input','string'),1,array('input')),
                    //'site_url' => array('Сайт (указывать без http!)',array('input','string'),0,array('input')),
                    'image' => array('Лого', array('image_to_resize', array(array('300,300','s','img/brands/', 0)), 'save_orig' => 'img/brands/', 'put_in_fields' => array('logo')), 0),
					'country_id' => array('Страна', array('foreign', 'countries', 0), 0),
					'city_id' => array('Город', array('foreign', 'countries_cities', 0), 0),
					'geo' => array('Координаты',array('input','string'),0,array('input')),
                    //'top'=>array('Топ',array('input','number'),0,array('checkbox',0)),
                    //'folder_id'=>array('',array('foreign','folders',1),0)
                    //'pos' => array('',array('auto_increment'),1)
                 ),array('name','name'),array(0,0),0,
				 array('country_id' => 'city_id'),
				 'filter' => array('name', 'country_id')
             ),
             'catalog_special_types' => array(
                 array('Типы выделенных товаров','тип'),
                 array(
                    'name'=>array('Название',array('input','string'),1,array('input')),
                 	'background'=>array('Фон',array('input','string'),0,array('input')),
                 	'background_hover'=>array('Фон ховер',array('input','string'),0,array('input')),
					'affiliates_special_types' => array('Филиалы', array('multiple_foreign', 'affiliates', 'type_id', 'affiliate_id'), 0),
                    //'name_single'=>array('Название для одного элемента',array('input','string'),0,array('input')),
                    //'icon'=>array('Иконка',array('file','img','img/special_types/'),0),
                    'pos' => array('', array('auto_increment'))
                 ),array('name', 'pos'),array(0,0),
				 'filter' => array('affiliates_special_types'),
				 'save_author_id' => true
             ),
			 'catalog_special_products' => array(
				 array('Бейджи', 'бейдж'),
				 array(
					 'product_id' => array('', array('foreign', 'catalog', 1), 1),
					 'special_type' => array('Бейдж', array('foreign', 'catalog_special_types', 1), 1, 'unique' => 'product_id')
				 ),
				 array('special_type', 'id'), array(0,0),
				 'force_access_rules' => true
			 ),
             'flavors' => array(
                 array('Вкусы','вкус'),
                 array(
                    'name'=>array('Название',array('input','string'),1,array('input'))
                 ),array('name', 'name'),array(0,0),
				 'no_access_rules' => true
             ),             
             'catalog'=>array(
                 array('Каталог','элемент'),
                 array(
                 	'is_stack' => array('Это набор', array('input', 'number'), 0, array('checkbox', 0)),                 	
                    'model' => array('Название',array('input','string'),1,array('input')),
					'brand' => array('Производитель',array('foreign','brands', 0),0),
                    'meat_type' => array('Отруб', array('foreign', 'meat_types', 0), 0),                 
                    //'article' => array('Артикул',array('input','string'),0,array('input')),
                    'url'=>array('Адрес',array('input','item_url', 'by_fields' => array('brand', 'model')),1,array('item_url'), 'unique' => 'folder_id'),                    
                    'price'=>array('Цена, руб.',array('input','float'),0,array('input')),
                    'discount'=>array('Скидка, %',array('input','float'),0,array('input')),
                 	'weight'=>array('Вес, брутто, кг',array('input','float'),0,array('input')),
                    'old_price'=>array('Старая цена, руб.',array('input','float'),0,array('input')),
                    'price_rest'=>array('Цена в ресторане, руб.',array('input','float'),0,array('input')),
                    'package'=>array('Упаковка',array('input','string'),0,array('input')),                 	
		    		'catalog_special_products' => array('Спец.', array('multiple_foreign', 'catalog_special_types', 'product_id', 'special_type')),
		    		'catalog_recipes' => array('Рецепты с картинками', array('multiple_foreign', 'articles', 'product_id', 'recipe_id')),                    
                    //'currency' => array('Валюта', array('foreign', 'currency', 1), 1),
                    'short_desc'=>array('Краткое описание',array('input','string'),0,array('textarea',0)),
                    'description'=>array('Описание',array('input','string'),0,array('textarea',1,1)),
                    'pict' => array('Фото',array('image_to_resize',array(array('150,148','l','img/catalog/',2),array('256,256','lb','img/catalog/',2),array('500,500','m','img/catalog/',2), array('92,92','sq','img/catalog/',2)), 'save_orig' => 'img/catalog/big/'),0),
                    //'catalog_item_purposes' => array('Назначение', array('multiple_foreign', 'catalog_purposes', 'item_id', 'purpose_id')),                    
                    'pos'=>array('',array('auto_increment', 'folder_id'),1),
                    'is_top'=>array('Топ',array('input','number'),0,array('checkbox',0)),
                    'top_priority' => array('Приоритет в топе',array('input','number'),0,array('input')),
                    'quantity' => array('Остаток',array('input','number'),0,array('input')),
                    'avail'=>array('В наличии',array('input','number'),0,array('checkbox', 1)),
                    'delivery_time' => array('Срок поставки, дн.', array('input','number', 'empty_as_null' => true), 0, array('input')),
                    'active'=>array('Активность',array('input','number'),0,array('checkbox',1)),
                    'yandex_market'=>array('Выгружать в маркет',array('input','number'),0,array('checkbox',1)),
                    'created_by' => array('', array('foreign', 'users', 1), 0),
                    'folder_id'=>array('',array('foreign','folders',1),0),
					'html_title'=>array('HTML Title',array('input','string'),0,array('textarea',0)),
                    'html_keywords'=>array('META keywords',array('input','string'),0,array('textarea',0)),
                    'html_description'=>array('META description',array('input','string'),0,array('textarea',0)),
                    'xml_id' => array('Code', array('input','string'), 0, array('input')),
                    'catalog_stack_products' => array('', array('multiple_foreign', 'catalog', 'product_id', 'stack_id')),
                    'articles_products' => array('', array('multiple_foreign', 'articles', 'product_id', 'article_id')),
					'affiliates_catalog' => array('Филиалы', array('multiple_foreign', 'affiliates', 'product_id', 'affiliate_id'), 0),
                 ),array('model,brand,article','pos'),array(1,1),1,
                 'filter' => array('model', 'brand'),
                 //'copy' => true,
                 //'change_before_copy' => 'url',
				 'save_author_id' => true,
				 'multi_update' => array('discount', 'avail', 'active', 'folder_id'),
             ),
             'catalog_articles'=>array(
                 array('Статьи','статью'),
                 array(
                    'product_id'=>array('', array('foreign', 'catalog', 1), 1),
                    'article_id'=>array('Статья',array('foreign','articles',1),1, 'unique' => 'product_id'),
                    'pos'=>array('',array('auto_increment', 'article_id'),1)
                 ),array('article_id','pos'),array(0,1),
				 'init_by' => 'product_id'
             ),
             'catalog_item_variants'=>array(
                 array('Варианты','вариант'),
                 array(
                 	'name' => array('Название',array('input','string'),1,array('input')),
                 	'package' => array('Размер',array('input','string'),0,array('input')),
                 	'price'=>array('Цена',array('input','float'),0,array('input')),
                    //'avail'=>array('В наличии',array('input','number'),0,array('checkbox', 1)),
                    'item_id'=>array('',array('foreign','catalog',1),0),
                    'pos'=>array('',array('auto_increment', 'item_id'),1)
                 ),array('name','pos'),array(0,0),
				 'no_access_rules' => true
             ),
             'catalog_photos'=>array(
                 array('Фото','фото'),
                 array(
                    'pict' => array('Фото',array('image_to_resize',array(array('150,148','l','img/catalog/more/',2), array('92,92','s','img/catalog/more/',2),array('500,500','m','img/catalog/more/',2)), 'save_orig' => 'img/catalog/more/'),1),
                    'item_id' => array('',array('foreign', 'catalog', 1),0),
                    'pos' => array('',array('auto_increment', 'item_id'),1)
                 ),array('pict','pos'),array(0,0),
				 'init_by' => 'item_id'
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
             'catalog_property_values'=>array(
                 array('',''),
                 array(
                    'property_id' => array('', array('foreign', 'catalog_properties', 1)),
                    'value'=>array('Значение',array('input','string'),0,array('input')),
                    'item_id' => array('', array('foreign', 'catalog', 1)),
                 ),array('property_id','property_id'),array(0,0)
             ),

             'discounts' => array(
                 array('Скидки','скидку'),
                 array(
                    'sum_value'=>array('Сумма от (руб.)',array('input','number'),1,array('input')),
                    'discount'=>array('Скидка (%)',array('input','number'),1,array('input')),
					'affiliates_discounts' => array('Филиалы', array('multiple_foreign', 'affiliates', 'discount_id', 'affiliate_id'), 0),
                    //'icon'=>array('Иконка',array('file','image','img/discounts/'),0)
                 ),array('sum_value,discount', 'sum_value'),array(0,0),
				 'filter' => array('affiliates_discounts'),
				 'save_author_id' => true
             ), 
             
             'discount_cards' => array(
                 array('Дисконтные карты','карту'),
                 array(
                 	'number'=>array('Номер',array('input','number'),1,array('input'), 'unique' => ''),
                    'discount'=>array('Скидка (%)',array('input','number'),1,array('input')),
                    'name'=>array('ФИО',array('input','text'),0,array('input')),
                 ),array('number,discount,name', 'number'),array(0,0),
                 'filter' => array('number', 'name'),
                 'use_pager' => 40,
				 'no_access_rules' => true
             ),
             
             'delivery_types' => array(
                 array('Способы доставки','тип'),
                 array(
                    'name'=>array('Название',array('input','text'),1,array('input')),
                    'avail_from'=>array('Сумма заказа от',array('input','number'),1,array('input')),
                    'external_calc'=>array('Внешний калькулятор',array('input','text'),0,array('select', 'delivery_calc')),
                    'cities_text'=>array('Текст "выберите город"',array('input','text'),0,array('input')),
                    'title_info'=>array('Подпись под заголовком',array('input','text'),0,array('input')),
                    'short_description'=>array('Краткое описание',array('input','string'),0,array('textarea',0)),
                    'description'=>array('Описание',array('input','string'),0,array('textarea',1)),
                    //'icon'=>array('Иконка',array('file','img','img/delivery/'),0),
                    'show_on_page'=>array('Показывать на странице "доставка"',array('input','number'),0,array('checkbox',1)), 
                    'show_address'=>array('Нужен адрес',array('input','number'),0,array('checkbox',1)),
                    'show_phone'=>array('Нужен телефон',array('input','number'),0,array('checkbox',1)),
                    'show_email'=>array('Нужен e-mail',array('input','number'),0,array('checkbox',1)),
                    'folder_id'=>array('',array('foreign','folders',1), 0),
					'affiliates_delivery_types' => array('Филиалы', array('multiple_foreign', 'affiliates', 'type_id', 'affiliate_id'), 0),
					'in_regions'=>array('В регионах',array('input','number'),0,array('checkbox',0)),
                    'pos' => array('', array('auto_increment', 'folder_id'), 0)
                 ),array('name', 'pos'),array(0,0), 1,
				 'save_author_id' => true
             ),
             'delivery_cities' => array(
                 array('Города','город'),
                 array(
                    'name'=>array('Название',array('input','text'),1,array('input')),
                    'price'=>array('Стоимость',array('input','number'),0,array('input')),
                    'type_id'=>array('',array('foreign','delivery_types', 1)),
                    'pos' => array('', array('auto_increment', 'type_id'), 0)
                 ),array('name', 'pos'),array(0,0),
				 'init_by' => 'type_id'
             ),
             'delivery_prices' => array(
                 array('Стоимость доставки','стоимость'),
                 array(
                 	'order_sum_from'=>array('Сумма заказа от',array('input','number'),0,array('input')),
                    'price'=>array('Стоимость',array('input','number'),0,array('input')),
                    //'price_for_members'=>array('Стоимость для владельцев карт',array('input','number'),0,array('input')),
                    'type_id'=>array('',array('foreign','delivery_types', 1)),
                 ),array('order_sum_from,price,price_for_members', 'order_sum_from'),array(0,0),
				 'init_by' => 'type_id'
             ),             
             'payment_types' => array(
                 array('Способы оплаты','способ оплаты'),
                 array(
                    'name'=>array('Название',array('input','text'),1,array('input')),
                    'payments_deliveries' => array('Типы доставки', array('multiple_foreign', 'delivery_types', 'payment_type', 'delivery_type')), 
                    'notes'=>array('Пояснения',array('input','string'),0,array('textarea',0)), 
                    'short_description'=>array('Краткое описание',array('input','string'),0,array('textarea',0)),
                    'description'=>array('Описание',array('input','string'),0,array('textarea',1)),                    
                    'link_url'=>array('Адрес страницы оплаты',array('input','text'),0,array('input')),
                    'link_title'=>array('Текст ссылки',array('input','text'),0,array('input')),
                    'payment_component'=>array('Компонент оплаты',array('input','text'),0,array('input')),
                    //'extra_charge'=>array('Наценка, %',array('input','float'),0,array('input')),
             		'active'=>array('Активность',array('input','number'),0,array('checkbox',1)),
                    //'icon'=>array('Иконка',array('file','img','img/payments/'),0),
                    //'show_on_page'=>array('Показывать на странице "способы оплаты"',array('input','number'),0,array('checkbox',1)), 
                    'folder_id'=>array('',array('foreign','folders',1), 0),
					'affiliates_payment_types' => array('Филиалы', array('multiple_foreign', 'affiliates', 'type_id', 'affiliate_id'), 0),
                    'pos' => array('', array('auto_increment', 'folder_id'), 0)
                 ),array('name', 'pos'),array(0,0), 1,
				 'save_author_id' => true
             ),
             'interesting_links'=>array(
                 array('Популярное','ссылку'),
                 array(
                 	'title' => array('Название', array('input', 'string'), 1, array('input')), 
                 	'link' => array('Ссылка', array('input', 'string'), 1, array('input')),
                 	'pos'=>array('',array('auto_increment'),1)
                 ),array('title','pos'),array(0,0)
             ),
             
             'currency' => array(
             	array('Валюты','валюту'),
             	array(
             		'name' => array('Наименование', array('input','string'), 1, array('input')),
             		'course' => array('Курс', array('input', 'float'), 1, array('input')),
             		'symbol' => array('Шаблон', array('input','string'), 0, array('input')),
             		//'dec_separator' => array('Разделитель', array('input','string'), 0, array('input')),
             		'pos' => array('', array('auto_increment'))
             	),array('name', 'pos'),
				 'no_access_rules' => true
             ),
             'mailing_list'=>array(
                 array('Список рассылки','адрес'),
                 array(
                    'email'=>array('E-mail',array('input','mail'),1,array('input'), 'unique' => ''),
                    'surname'=>array('Фамилия',array('input','string'),0,array('input')),
                    'name'=>array('Имя',array('input','string'),0,array('input')),
					'affiliate_id'=>array('Филиал',array('foreign','affiliates', 0),0),
                    'event'=>array('Событие',array('input','string'),0,array('input')),
                 	'type'=>array('Группа',array('input','string'),1,array('input'))
                 ),array('email,name,affiliate_id','id desc'),array(0,0),
                 'filter' => array('email', 'affiliate_id', 'type'),
                 'use_pager' => 40
             ),         
            
             
             /*
            'catalog_tags_elements'=>array(
                 array('',''),
                 array(
                 	'tag'=>array('',array('input','string'),1,array('input')),
                    'item_id'=>array('',array('foreign','catalog',1),1)
                 ),array('tag','tag'),array(0,0)
             )
             */
             
             'items_properties'=>array(
                 array('Свойства','свойство'),
                 array(
                 	'name'=>array('Название',array('input','string'),1,array('input')),
                 	//'in_card'=>array('Отображать в карточке',array('input','number'),0,array('checkbox', 0)),
                    'pos'=>array('',array('auto_increment'),1),
                    //'folder_id' => array('', array('foreign', 'folders', 1),0),
                 ),array('name','pos'),array(0,0),
				 'no_access_rules' => true
             ),
             'items_property_values'=>array(
                 array('',''),
                 array(
                    'property_id' => array('', array('foreign', 'items_properties', 1)),
                    'value'=>array('Значение',array('input','string'),0,array('input')),
                    'item_id' => array('', array('foreign', 'articles', 1)),
                 ),array('property_id','property_id'),array(0,0)
             ),
             'articles_photos'=>array(
                 array('Фото','фото'),
                 array(
                    'pict' => array('Фото',array('image_to_resize',array(array('150,148','sq','img/articles/more/',2),array('480,0','m','img/articles/more/',1)), 'save_orig' => 'img/articles/more/big/'),1),
                    'title'=>array('Описание',array('input','string'),1,array('textarea',0)),
                    'item_id' => array('',array('foreign', 'articles', 1),0),
                    'pos' => array('',array('auto_increment', 'item_id'),1)
                 ),array('pict','pos'),array(0,0),
				 'init_by' => 'item_id'
             ),
             
             'meat_types'=>array(
                 array('Отрубы','отруб'),
                 array(
                 	'name'=>array('Название',array('input','string'),1,array('input')),
                 	'alt_name'=>array('Еще название',array('input','string'),0,array('input')),
                 	'description'=>array('Описание',array('input','string'),0,array('textarea')),
                 	'picture'=>array('Изображение',array('file','img','img/meat_types/'),0),
                 	'coords'=>array('Координаты',array('input','string'),0,array('input')),
                    'pos'=>array('',array('auto_increment'),1),
                 ),array('name','pos'),array(0,0)
             ),
             
             'folders_articles'=>array(
                 array('Статьи','статью'),
                 array(
                 	'article_id' => array('Статья',array('foreign', 'articles', 1),1, 'unique' => 'folder_id'),
                    'folder_id' => array('',array('foreign', 'folders', 1),1),
                    'pos' => array('',array('auto_increment', 'folder_id'),1)
                 ),array('article_id','pos'),array(0,1),
				 'force_access_rules' => true,
				 'init_by' => 'folder_id'
             ),
             
             'mailings'=>array(
                 array('Рассылки','рассылку'),
                 array(
                 	'email_subject'=>array('Тема письма',array('input','string'),1,array('input')),
                    //'title'=>array('Заголовок',array('input','string'),1,array('textarea', 0)),                    
                    'top_description'=>array('Вводный текст',array('input','string'),0,array('textarea', 1, 1)),
                    //'inner_text'=>array('Текст',array('input','string'),0,array('textarea', 1)),
                    'products_title'=>array('Заголовок над товарами',array('input','string'),0,array('input')),
                    //'show_pod'=>array('Товары дня',array('input','number'),0,array('checkbox',1)),
                    //'show_pow'=>array('Товары недели',array('input','number'),0,array('checkbox',1)),
                    'direct_emails'=>array('Только по указанным адресам',array('input','string'),0,array('textarea', 0)),
                    'address_group' => array('Группа', array('input', 'string'), 0, array('select', 'mailing_groups')),
					'affiliate_id'=>array('Филиал',array('foreign','affiliates', 0),0)
                    //'pict' => array('Картинка',array('image_to_resize',array(array('270,185','s','img/mailings/', 0))),0)
                 ),array('email_subject','id desc'),array(1,0),
                 'use_pager' => 40
             ),
             'mailings_products'=>array(
                 array('Товары','товар'),
                 array(
                    'product_id'=>array('Товар', array('foreign', 'catalog', 1), 1, 'unique' => 'mailing_id'),
                    'mailing_id'=>array('',array('foreign','mailings',1),1),
                    //'special_type'=>array('',array('foreign','catalog_special_types',1),1),
                    'pos'=>array('',array('auto_increment', 'mailing_id'),1)
                 ),array('product_id','pos'),array(0,1),
				 'init_by' => 'mailing_id'
             ),
             'mailings_articles'=>array(
                 array('Статьи','статью'),
                 array(
                    //'article_id'=>array('Статья', array('foreign', 'articles', 1), 1, 'unique' => 'mailing_id'),
                    'title'=>array('Заголовок',array('input','string'),0,array('input')),
                    'link'=>array('Ссылка',array('input','string'),0,array('input')),
                    'content'=>array('Текст',array('input','string'),0,array('textarea',1)),
                    'image' => array('Фото', array('image_to_resize', array(array('207,207','sq','img/mailings/', 2)), 'save_orig' => 'img/mailings/'), 0),
                    'mailing_id'=>array('',array('foreign','mailings',1),1),
                    'pos'=>array('',array('auto_increment', 'mailing_id'),1)
                 ),array('title','pos'),array(0,0),
				 'init_by' => 'mailing_id'
             ),
             
             'catalog_price_types' => array(
                 array('Типы цен', 'тип'),
                 array(
                 	'name' => array('Название', array('input','string'), 1, array('input')),
                    'xml_id' => array('XML ID', array('input','string'), 0, array('input')),
                    'is_base' => array('Базовая', array('input', 'number'), 0, array('checkbox', 0)),
					'affiliate_id' => array('Филиал', array('foreign', 'affiliates'), 0),
                    'pos' => array('', array('auto_increment'), 0),
                 ),array('name', 'pos'),array(0,0)
             ),
             
             'catalog_prices' => array(
             	array('Цены', 'цену'),
             	array(
             		'product_id' => array('', array('foreign', 'catalog', 1), 1),
             		'price_type_id' => array('Тип', array('foreign', 'catalog_price_types', 1), 0, 'unique' => 'product_id'),
					'price' => array('Цена', array('input', 'float'), 1, array('input')),
					'discount' => array('Скидка,%', array('input', 'float'), 0, array('input'))
             	),
				 array('price_type_id,price,discount', 'price_type_id'), array(0,0),
				 'force_access_rules' => true
             ),
             
             'catalog_stocks' => array(
                 array('Склады', 'склад'),
                 array(
                 	'name' => array('Название', array('input','string'), 1, array('input')),
                 	'delivery_time' => array('Срок поставки, дн.', array('input','number'), 0, array('input')),
                    'xml_id' => array('XML ID', array('input','string'), 0, array('input')),
                    'is_base' => array('Базовый', array('input', 'number'), 0, array('checkbox', 0)),
					'affiliate_id' => array('Филиал', array('foreign', 'affiliates'), 0),
                    'pos' => array('', array('auto_increment'), 0),
                 ),array('name', 'pos'),array(0,0)
             ),
             
             'catalog_stocks_products' => array(
             	array('Остатки', 'склад'),
             	array(
             		'stock_id' => array('Склад', array('foreign', 'catalog_stocks', 1), 1, 'unique' => 'product_id'),
             		'product_id' => array('', array('foreign', 'catalog', 1), 1),
             		'quantity' => array('Остаток', array('input','number'), 0, array('input')),
             		//'flavor_id' => array('', array('foreign', 'catalog_products_flavors', 1), 0)
             	),array('stock_id,quantity', 'stock_id'),
				'force_access_rules' => true
             ),
             
             'catalog_stack_products' => array(
                 array('Товары в наборе', 'товар'),
                 array(
                    'product_id' => array('Товар',array('foreign','catalog',1),1),
                    'quantity' => array('Кол-во', array('input', 'number'),1),
                    'stack_id' => array('',array('foreign','catalog',1),1),
                    'pos' => array('', array('auto_increment', 'stack_id'))
                 ),array('product_id,quantity', 'pos'),array(0,1)
             ),
             
             'catalog_stack_alternatives' => array(
                 array('Замены', 'товар'),
                 array(
                    'product_id' => array('Товар',array('foreign','catalog',1),1),
                    'quantity' => array('Кол-во', array('input', 'number'),1),
                    'product_entry_id' => array('',array('foreign','catalog_stack_products',1),1),
                    'pos' => array('', array('auto_increment', 'product_entry_id'))
                 ),array('product_id,quantity', 'pos'),array(0,1),
				 'no_access_rules' => true
             ),
             
             'articles_authors'=>array(
                 array('Авторы','автора'),
                 array(
                 	'name'=>array('Название',array('input','string'),1,array('input')),
                 	'page_title'=>array('Заголовок страницы',array('input','string'),1,array('input')),
                 	'url_page' => array('Урл на сайте', array('input', 'string'), 1, array('input')),
                 	'name_genitive'=>array('Название род. п.',array('input','string'),1,array('input')),
                 	'name_dative'=>array('Название дат. п.',array('input','string'),1,array('input')),
                 	'short_descr'=>array('Краткое описание',array('input','string'),0,array('textarea')),
                 	'short_descr2'=>array('Еще краткое описание',array('input','string'),0,array('textarea')),
                 	'descr'=>array('Описание',array('input','string'),0,array('textarea', 1)),                 	
                 	'url'=>array('Блог URL',array('input','string'),0,array('input')),
                 	'url_ask'=>array('Задать вопрос URL',array('input','string'),0,array('input')),
                 	'big_photo' => array('Фото большое', array('image_to_resize', array(array('500,0','b','img/authors/', 2)), 'save_orig' => 'img/authors/', 'orig_ext_field' => 'orig_ext_big'), 0),
                 	'image' => array('Фото', array('image_to_resize', array(array('142,142','s','img/authors/', 2)), 'save_orig' => 'img/authors/'), 0),
                 	'active'=>array('Активность',array('input','number'),0,array('checkbox',1)),
                 	'pos' => array('', array('auto_increment'))
                 ),array('name','pos'),array(0,0)
             ),
             
             'catalog_reviews'=>array(
                 array('Мнения экспертов', 'мнение'),
                 array(       
                 	'author_id' => array('Автор', array('foreign', 'articles_authors', 0), 1),
                 	'content' => array('Содержание',array('input','string'),1,array('textarea', 0)),                 	
                 	'rating' => array('Рейтинг',array('input','string'),0,array('input')),                 	
                 	'active'=>array('Активность',array('input','number'),0,array('checkbox',1)),
                 	'pos' => array('', array('auto_increment', 'product_id')),
                 	'product_id' => array('',array('foreign', 'catalog', 1),1)
                 ),array('content','pos'),array(0,0),
				 'init_by' => 'product_id'
             ),
	
			 'catalog_comments' => array(
				array('Отзывы', 'отзыв'),
				array(
					'created_at' => array('Дата', array('date', 'form', 1), 0),
					'product_id' => array('', array('foreign', 'catalog', 1), 1),
					'author_name' => array('Имя', array('input', 'text'), 1, array('input')),
					'author_email' => array('E-mail', array('input', 'text'), 1, array('input')),
					'author_about' => array('Кто я?', array('input', 'text'), 0, array('input')),
					'comment' => array('Отзыв', array('input', 'text'), 0, array('textarea',0)),
					'recipe' => array('Как готовили', array('input', 'text'), 0, array('textarea',0)),
					'hidden_comment' => array('Скрытый комментарий', array('input', 'text'), 0, array('textarea',0)),
					'mark' => array('Оценка', array('input', 'number'), 0, array('input')),
					'recommend' => array('Рекомендация', array('input', 'number'), 0, array('checkbox', 1)),
					'user_id' => array('Пользователь', array('foreign', 'users', 0, 'is_tmp=0'), 0),
					'active' => array('Активность',  array('input', 'number'), 0, array('checkbox',0)),
					'only_rate' => array('',  array('input', 'number'), 0, array('checkbox',0))
				),array('comment,mark,active', 'id desc'),array(0,0),
				'use_pager' => 40,
				'force_access_rules' => true,
				'init_by' => 'product_id'
			),
	
	'catalog_groups' => array(
		array('Группы товаров', 'группу'),
		array(
			'name' => array('Название',  array('input', 'string'), 1, array('input')),
			'url' => array('URL',  array('input', 'item_url', 'by_fields' => array('name')), 1, array('item_url')),
			//'discount' => array('Скидка, %',  array('input', 'float'), 0, array('input')),
			'description' => array('Описание',  array('input', 'string'), 0, array('textarea', 0)),
			//'highlight' => array('Выделить', array('input', 'number'), 0, array('checkbox', 0)),
			'active' => array('Активность', array('input', 'number'), 0, array('checkbox', 1)),
			'pos' => array('', array('auto_increment'))
		),array('name', 'pos'),
		array(0,0),
		0
	),
	'catalog_groups_items' => array(
		array('Элементы', 'выборку'),
		array(
			'section_id' => array('Категория', array('foreign', 'folders', 1, 'entity="catalog"'), 0),
			//'brand_id' => array('Производитель', array('foreign', 'brands', 1), 0),
			'product_id' => array('Товар', array('foreign', 'catalog', 1), 0),
			//'title' => array('Текст ссылки',  array('input', 'string'), 0, array('input')),
			'items_limit' => array('Кол-во товаров',  array('input', 'number'), 0, array('input')),
			'order_by' => array('Сортировка',  array('input', 'string'), 0, array('input')),
			'active' => array('Активность', array('input', 'number'), 0, array('checkbox', 1)),
			'group_id' => array('', array('foreign', 'catalog_groups')),
			'pos' => array('', array('auto_increment', 'group_id'))
		),array('section_id,product_id', 'pos'),
		array(0,0),
		0,
		array('section_id' => 'product_id'),
		'init_by' => 'group_id'
	),
	
	'affiliates' => array(
		array('Филиалы', 'филиал'),
		array(
			'name' => array('Название',  array('input', 'string'), 0, array('input')),
			'domain' => array('Домен',  array('input', 'string'), 0, array('input')),
			'email' => array('E-mail',  array('input', 'string'), 0, array('input')),
			'regions' => array('Регионы',  array('input', 'string'), 0, array('input')),
			'cities' => array('Города',  array('input', 'string'), 0, array('input')),
			'is_base' => array('Базовый', array('input', 'number'), 0, array('checkbox', 0)),
			'pos' => array('', array('auto_increment'))
		),array('name', 'pos'),
		array(0,0)
	),
	
	'regions' => array(
		array('Города в попапе', 'тип'),
		array(
			'name' => array('Название', array('input', 'text'), 1, array('input')),
			'is_default' => array('По умолчанию', array('input', 'number'), 0, array('checkbox', 0)),
			'is_bold' => array('Выделить', array('input', 'number'), 0, array('checkbox', 0)),
		),
		array('name', 'name'), array(0, 0)
	),
	
	'start_page_products' => array(
		array('Товары на главной', 'товар'),
		array(
			'product_id' => array('Товар',  array('foreign', 'catalog', 1), 1),
			'folder_id' => array('', array('foreign', 'folders', 1), 0),
			'pos' => array('', array('auto_increment', 'folder_id'))
		),array('product_id', 'pos'), array(0,1), 1,
		'save_author_id' => true
	),
	
	'catalog_products_arrivals'=>array(
		array('Приходы товаров', 'товар'),
		array(
			'tsmp' => array('Дата', array('date', 'form', 1), 1),
			'product_id' => array('Товар', array('foreign', 'catalog', 1), 1),
			'quantity' => array('Кол-во',  array('input', 'string'), 0, array('input')),
			'affiliate_id' => array('Филиал', array('foreign', 'affiliates', 1), 1),
			'notified' => array('Отправлено', array('input', 'number'), 1, array('checkbox', 0)),
		),
		array('tsmp,product_id,quantity,affiliate_id,notified','tsmp desc'),
		array(0,0),
		'filter' => array('product_id', 'affiliate_id'),
		'use_pager' => 40
	),
	'catalog_arrivals_subscribers'=>array(
		array('Подписка на товары', 'подписчика'),
		array(
			'created_at' => array('Дата', array('date', 'form', 1), 0),			
			'email' => array('E-mail', array('input', 'string'), 1, array('input')),
			'product_id' => array('Товар', array('foreign', 'catalog', 1), 1),
			'affiliate_id' => array('Филиал', array('foreign', 'affiliates', 1), 1),
			'notified' => array('Отправлено', array('input', 'number'), 0, array('checkbox', 0)),
		),
		array('created_at,email,product_id,affiliate_id,notified','id desc'),
		array(0,0),
		'filter' => array('product_id', 'notified', 'affiliate_id', 'email'),
		'use_pager' => 40
	),
	
	'countries'=>array(
		array('Страны', 'страну'),
		array(
			'name' => array('Название',  array('input', 'string'), 1, array('input'))
		),
		array('name', 'name'),
		array(0,0),
		'filter' => array('name'),
		'use_pager' => 40
	),
	
	'countries_cities'=>array(
		array('Города', 'город'),
		array(
			'country_id' => array('', array('foreign', 'countries', 1), 1),
			'name' => array('Название',  array('input', 'string'), 0, array('input'))			
		),
		array('name', 'name'),
		array(0,0),
		'use_pager' => 40
	),
	
	'catalog_terms' => array(
		array('Термины', 'термин'),
		array(
			'name' => array('Название',  array('input', 'string'), 0, array('input')),
			'url'=>array('URL', array('input', 'item_url', 'by_fields' => array('name')), 0, array('item_url'), 'unique' => ''),
			'short_descr' => array('Краткое описание',  array('input', 'string'), 0, array('textarea', 0)),
			'descr' => array('Описание',  array('input', 'string'), 0, array('textarea', 1, 1)),
			'folder_id' => array('', array('foreign', 'folders', 1), 1),
			'html_title'=>array('HTML Title',array('input','string'),0,array('textarea',0)),
			'html_keywords'=>array('META keywords',array('input','string'),0,array('textarea',0)),
            'html_description'=>array('META description',array('input','string'),0,array('textarea',0)), 
			'pos' => array('', array('auto_increment', 'folder_id'))
		),
		array('name', 'pos'),
		array(0,0), 1,
		'use_pager' => 40
	),
	
	'catalog_upsell' => array(
		array('Апселл в корзине', 'товар'),
		array(
			'product_id' => array('Товар',  array('foreign', 'catalog', 1), 1),
			'folder_id' => array('', array('foreign', 'folders', 1), 0),
			'pos' => array('', array('auto_increment', 'folder_id'))
		),array('product_id', 'pos'), array(0,1), 1,
		'save_author_id' => true
	),
	
	'catalog_nav' => array(
		array('Подменю каталога', 'элемент'),
		array(
			'catalog_folder_id' => array('Категория каталога', array('foreign', 'folders', 1, 'entity="catalog"'), 1),
			'title' => array('Название',  array('input', 'string'), 1, array('input')),			
			'direct_link' => array('Прямая ссылка',  array('input', 'string'), 0, array('input')),
			'active' => array('Активность', array('input', 'number'), 0, array('checkbox', 1)),
			'folder_id' => array('', array('foreign', 'folders', 1), 0),
			'pos' => array('', array('auto_increment', 'folder_id'))
		),array('title,catalog_folder_id', 'pos'), array(0,0), 1,
 		'save_author_id' => true
	),
	
	'catalog_nav_terms' => array(
		array('Термины', 'термин'),
		array(
			'term_id' => array('Термин', array('foreign', 'catalog_terms', 1), 1, 'unique' => 'catalog_nav_item_id'),
			'catalog_nav_item_id' => array('', array('foreign', 'catalog_nav', 1), 1),
		),array('term_id', 'id'), array(0,1)
	),
             
             
);
?>