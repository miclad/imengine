<?

$dt = new DataTable('catalog_property_groups');			
if($dt->isOk){
	
	MenuTree::getInstance()->setCurrentItem('datatable_catalog');
	
	$dt->execute();
	$page = $dt->getContents();				
		
	$links = $dt->getLinks();
	foreach($links as $v){	
		MenuTree::getInstance()->addItem('', $v[0], $v[1], 'datatable_catalog', '', (isset($v[2]) ? $v[2] : ''));
	}
				
	// get path
	
	$path = $dt->getPath();
	$op = $path;
	$tree = new Tree('folders', 'entity="catalog"');
	$t_p = $tree->getPath('*', $dt->foreignValue);
	$path = array();
	$path[] = array('Каталог', 'index.php?module=datatable&entity=catalog');
	foreach($t_p as $v){
		$path[] = array($v['name'], 'index.php?module=datatable&entity=catalog&folder='.$v['id']);
	}
	$path[count($path)-1][0] = $GLOBALS['entities']['catalog_properties'][0][0].' '.$path[count($path)-1][0].'';
	$path[count($path)-1][1] = 'index.php?module=datatable&entity=catalog_properties&folder_id='.$dt->foreignValue;
	$path = array_merge($path, array_slice($op, 3, 1));
	
	foreach($path as $v){
		NaviPath::addItem($v[0], isset($v[1]) ? $v[1] : '');
	}
	
	require $page;
	
	SiteNotices::addNotice($dt->renderErrors(), 'error');
	
}else{
	throw new Exception('Ошибка инициализации');
}
			
?>