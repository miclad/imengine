<?

$entity = isset($_GET['entity']) && $_GET['entity'] != '' ? $_GET['entity'] : '';

$dt = new DataTable($entity);			
if($dt->isOk){
	
		// build arr_components array
		$GLOBALS['arr_components'] = array();
		$dir = opendir(COMPONENTS_DIR);
		while(($f = readdir($dir)) !== false){
			
			if(!is_dir(COMPONENTS_DIR.$f) || $f == '.' || $f == '..')
				continue;
				
			$component_descr = array();
			
			$descr_file = COMPONENTS_DIR.$f.'/inc/descr.php';
			if(file_exists($descr_file)){
				require $descr_file;
				if(isset($component_descr['name']) && $component_descr['name'] != ''){
					$GLOBALS['arr_components'][] = array($f, $component_descr['name']);
				}
			}
			
		}
		
		$GLOBALS['arr_components'] = Common::arraySortByField($GLOBALS['arr_components'], 1, SORT_ASC, SORT_STRING);
		
		$dt->execute();
		$page = $dt->getContents();
		
		$path = $dt->getPath();
		foreach($path as $v){
			NaviPath::addItem($v[0], (isset($v[1]) ? $v[1] : ''));
		}		
		
		$ents = $dt->getEntitiesList();
		if(count($ents)){
			$par = $dt->getParentEntity();
			foreach($ents as $k => $v){
				MenuTree::getInstance()->addItem($v['name'], $v['title'], $v['url'], array('_controls_tabs', 'datatable_'.($par ? $par : $entity)));
			}
			MenuTree::getInstance()->rebuildPath();
		}
		
		$links = $dt->getLinks();
		foreach($links as $v){	
			MenuTree::getInstance()->addItem('', $v[0], $v[1], array('_controls_buttons', 'datatable_'.$entity), '', (isset($v[2]) ? $v[2] : ''));
		}
		
		if($page)
			require $page;
			
		SiteNotices::addNotice($dt->renderErrors(), 'error');
		
}else{	
	throw new Exception('Ошибка инициализации');	
}
			
?>