<?

if(isset($_GET['search']) && trim($_GET['search']) != ''){
	echo Page::includeComponent('catalog', array_merge($this->getConfigs(), array('is_search' => true)), $_GET, 'items', '', false);
	$PAGE->setAttribute('search_string', trim($_GET['search']));
}else{
	$this->addError('Задан пустой поисковый запрос');
}

$PAGE->addItemToPath('Поиск товаров');

?>