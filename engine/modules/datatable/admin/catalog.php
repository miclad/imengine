<?

$entity = isset($_GET['entity']) && $_GET['entity'] != '' ? $_GET['entity'] : '';
	
$dt = new DataTable($entity);			
if($dt->isOk){				
		
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
	
	// получаем список свойств категории
	if(isset($_GET['show']) && $_GET['show'] == 'element_form'){
		$section_properties = Catalog::getSectionProperties($dt->curFolder);
		if($dt->getRowValue('id')){
			$property_values = Catalog::getElementProperties($dt->getRowValue('id'));
		}
	}
	
	if(isset($_SESSION['import_result_stat'][$dt->entity])){
		$import_result = $_SESSION['import_result_stat'][$dt->entity];
		unset($_SESSION['import_result_stat'][$dt->entity]);
	}
		
	if($page)
		require $page;
		
	SiteNotices::addNotice($dt->renderErrors(), 'error');
		
}else{	
	throw new Exception('Ошибка инициализации');	
}

			
?>