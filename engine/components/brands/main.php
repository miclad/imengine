<?

$PAGE->setAttribute('show_catalog_select', true);
$PAGE->setAttribute('cur_catalog_type', 'catalog');

if(isset($_GET['id'])){
	$brand_data = $db->query('select * from brands where id="'.mysql_escape_string($_GET['id']).'"')->getNext();
	if($brand_data){
		$PAGE->setAttribute('current_brand', $brand_data['id']);
		$PAGE->addItemToPath($brand_data['name'], '');
		$params = array('brand' => $brand_data['id']);
		if(isset($_GET['category']))
			$params['folder_id'] = $_GET['category'];
		$params = array_merge($params, $_GET);
		echo Page::includeComponent('catalog', array('show_filter' => true, 'items_per_page' => 20), $params, 'items', '', false);
		
		/* SEO */
		if($brand_data['html_title'])
			$PAGE->setHeadTitle(trim($brand_data['html_title']));
		if($brand_data['html_keywords'])
			$PAGE->setMetaKeywords($brand_data['html_keywords']);
		if($brand_data['html_description'])
			$PAGE->setMetaDescription($brand_data['html_description']);
		
	}else{
		throw new PageNotFoundException();
	}
}else{
	
	$list = $db->query('select * from brands where 1 order by name');
	$this->setActionTemplate('index');
	
}

?>