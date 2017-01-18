<?

$PAGE->setAttribute('show_catalog_select', true);
$PAGE->setAttribute('cur_catalog_type', 'catalog');
//$PAGE->setAttribute('hide_title', true);

$extra_url = $PAGE->getExtraUrl();

if($extra_url && !in_array($extra_url, array('/favorites', '/compare'))){

	$data = Folders::getDataByUrl('catalog', $extra_url);
	if(!$data){
		
		throw new PageNotFoundException();
		
	}else{
		
		if($data['type'] == 'item'){
			
			echo Page::includeComponent('catalog', array_merge(array('id' => $data['data']['id'], 'collective_buy' => $this->getConfig('collective_buy')), $this->getConfigs()), array(), 'details', '', true);
			$PAGE->setAttribute('show_catalog_select', false);
			
		}elseif($data['type'] == 'folder'){	
			
			/* SEO */
			if($data['data']['html_title'])
				$PAGE->setHeadTitle(trim($data['data']['html_title']));
			else
				$PAGE->setHeadTitle($data['data']['name']);
				
			if($data['data']['html_keywords'])
				$PAGE->setMetaKeywords($data['data']['html_keywords']);
				
			if($data['data']['html_description'])
				$PAGE->setMetaDescription($data['data']['html_description']);
				
			$PAGE->setAttribute('current_section', $data['data']['id']);
			$PAGE->setAttribute('current_section_data', $data['data']);
			
			$tree = new Tree('folders', 'entity="catalog"');
			$path = $tree->getPath('*', $data['data']['id']);
			
			//$PAGE->clearPath();
			//$PAGE->addItemToPath('Главная', '/');
			
			foreach($path as $k => $v){
				
				if(true)
					$PAGE->addItemToPath($v['name'], '/catalog'.$v['url'].'/');
					
				if($k == 0){					
					$PAGE->setAttribute('current_section_root', $v['id']);
					$PAGE->setAttribute('current_section_data_root', $v);					
				}
				
				if(isset($v['pict_bg']) && $v['pict_bg']){
					$PAGE->setAttribute('pict_bg', '/img/folders/'.$v['pict_bg']);
				}
				
			}
			
			// check if has children
			$res = $db->query('select count(*) as cnt from folders where parent='.$data['data']['id'])->getNext();
			if(!$res['cnt'] || count($path) > 1 || $data['data']['show_products']){
					
				$PAGE->clearContent();
				echo Page::includeComponent('catalog', array('collective_buy' => $this->getConfig('collective_buy'), 'ipr' => 4, 'show_filter' => true, 'items_per_page' => $this->getConfig('items_per_page'), 'picture_format' => 'med'), array_merge($_GET,array('folder_id' => $data['data']['id'])), 'items', '', false);
			
			}else{
				
				echo Page::includeComponent('catalog', array('parent' => $data['data']['id']), array(), 'sections');
				
			}
			
		}
		
	}

}elseif(in_array($extra_url, array('/favorites'))){
	echo Page::includeComponent('catalog', $this->getConfigs(), array(), 'favorites');
}elseif(in_array($extra_url, array('/compare'))){
	echo Page::includeComponent('catalog', $this->getConfigs(), array(), 'compare');
}elseif(isset($_GET['search'])){
	$PAGE->clearPath();
	$PAGE->addItemToPath('Поиск по каталогу');
	if(trim($_GET['search']) == '')
		echo 'Задан пустой поисковый запрос';
	else{
		echo Page::includeComponent('catalog', array('items_per_page' => $this->getConfig('items_per_page'), 'search' => $_GET['search'], 'is_search' => true), array(), 'items', '', false);
	}
}elseif(isset($_GET['return_count'])){
	
	echo Page::includeComponent('catalog:items', array('collective_buy' => $this->getConfig('collective_buy')));
	
}elseif($this->getConfig('collective_buy')){
	
	echo Page::includeComponent('catalog', array('collective_buy' => $this->getConfig('collective_buy'), 'ipr' => 4, 'show_filter' => true, 'items_per_page' => $this->getConfig('items_per_page'), 'picture_format' => 'med'), array_merge($_GET,array('folder_id' => $data['data']['id'])), 'items', '', false);
	
}else{
	
	// index page
	echo Page::includeComponent('catalog', array('parent' => 0), array(), 'sections');
	
}

?>