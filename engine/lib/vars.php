<?

/*
******
SYSTEM
******
*/

$navi_types = array(
	array('top', 'Главное меню'),
	array('footer', 'Футер')	
);

$datatypes = array(
);

$ulevels = array(
	array("client", "Клиент"),
	array("service", "Сервис")
);

$access_levels = array(
	array("0","Все пользователи"),
);

/* feedback form */
$form_fieldtypes = array(
	array("simple","Текстовое поле"),
	//array("select","Выпадающий список"),
	array("textarea","Большое текстовое поле"),
	//array("file","Файл")
);

$form_datatypes = array(
	array("text","Текст"),
	array("mail","E-mail"),
	array("phone","Телефон"),
	array("number","Число"),
	//array("file","Файл")
);

$banners_placings = array(
	array(
		'slider',
		'Слайдер на главной',
		'fields' => array('file', 'link'),
		'image_filter' => array(
			array('prefix' => 'bg', 'width' => 780, 'height' => 300, 'resize_type' => 2/*, 'filter' => array('name' => IMG_FILTER_GAUSSIAN_BLUR, 'value' => 25), 'dont_save_proportions' => true*/),
		)
	),
	array(
		'near_slider', 
		'Справа от слайдера',
		'fields' => array('file', 'link', 'code')
	),
	array(
		'benefits', 
		'Преимущества',
		'fields' => array('file', 'title', 'text', 'link')
	),
	/*
	array(
		'wide',
		'Широкий',
		'fields' => array('file'),
		'image_filter' => array(
			array('prefix' => '', 'width' => 1080, 'height' => 180, 'resize_type' => 2),
		)
	),
	
	array(
		'middle',
		'50% ширины',
		'fields' => array('file'),
		'image_filter' => array(
			array('prefix' => '', 'width' => 540, 'height' => 180, 'resize_type' => 2),
		)
	),
	*/
	array(
		'sidebar', 
		'Сайдбар',
		'fields' => array('file', 'link', 'code')
	),
	array(
		'counters', 
		'Счетчик',
		'fields' => array('code')
	),
	array(
		'codes_top', 
		'Коды в начале страницы',
		'fields' => array('code')
	),
	array(
		'codes_bottom', 
		'Коды в конце страницы',
		'fields' => array('code')
	)
);

$banners_show_cases = array(
	//array('main', 'Главная'),
	//array('pages', 'На страницах'),
	array('selected_pages', 'Только на выбранных страницах'),
	//array('catalog_main', 'Главная каталога'),	
	//array('catalog', 'Каталог'),
	//array('brands_main', 'Главная производителей'),	
	//array('brands', 'Бренды')
);

/*
***************
Project Special
***************
*/

$delivery_calc = array(
	//array('ems', 'EMS Почта России'),
	array('russianpost', 'Почта России'),
	array('pickpoint', 'Pickpoint')
);

?>