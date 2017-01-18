<?

$entity = isset($_GET['entity']) && $_GET['entity'] != '' ? $_GET['entity'] : '';

$check_access_entity = DataTable::getCheckAccessEntity($entity);

if(!AuthUser::getInstance()->hasAccess('datatable_'.$check_access_entity))
	throw new NeedAuthException();

if(file_exists(dirname(__FILE__).'/'.$_GET['entity'].'.php'))
	require(dirname(__FILE__).'/'.$_GET['entity'].'.php');
else{
	
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
		
		if($page)
			require $page;
			
		SiteNotices::addNotice($dt->renderErrors(), 'error');
		
	}else{	
		throw new Exception('Ошибка инициализации');	
	}

}
			
?>