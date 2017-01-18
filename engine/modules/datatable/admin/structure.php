<?

$dt = new DataTable('structure');			
if($dt->isOk){		

	if(isset($_GET['act']) && $_GET['act']=='set_main' && isset($_GET['id']) && is_numeric($_GET['id'])){
		$tid = DBCommon::getById('structure','lang',$_GET['id']);
		if($tid){
			$db->query('update structure set main=0 where 1');
			$db->query('update structure set main=1 where id='.$_GET['id']);
			header('Location: '.Common::editQueryString($_SERVER['REQUEST_URI'], array('act'), array()));
			exit();
		}
	}
	
	$tree = new Tree("structure");
	
	$order_by = '';
	for($i = 0; $i < count($GLOBALS['navi_types']); $i++){
		$order_by .= 'navi_type != "'.$GLOBALS['navi_types'][$i][0].'",';
	}
	$order_by .= 'navi_type,pos';
	
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
	
	if(isset($_GET['show']) && $_GET['show'] == 'element_form'){
		//$GLOBALS['parents'] = array();
		$GLOBALS['parents'] = array_merge(array(array('0','Корень')), $tree->getFullTree(0, 1, -1, 'pos', 'id,name', false, $dt->getRowValue('id')));
	}else{
		$GLOBALS['grid'] = $tree->getFullTree(0,0,-1, $order_by);
	}
	
	if(isset($_GET['action']) && $_GET['action'] == 'load_children'){
	
		echo showPages($_GET['parent'], '', intval($_GET['level'])+1, $dt, true);
		exit();

	}
	
	if($page)
		require $page;
		
	SiteNotices::addNotice($dt->renderErrors(), 'error');
	
}else{	
	throw new Exception('Ошибка инициализации');	
}

function showPages($parent, $navi_type, $level, $dt, $ignore_hide = false){
	
	global $db;
	global $check_access_entity;
	
	$q = 'parent='.intval($parent);
	if($navi_type != ''){
		$q .= ' and navi_type="'.$navi_type.'"';
	}
	
	$list = $db->query('select * from structure where '.$q.' order by pos');
	
	$hide = !$ignore_hide && $parent > 0 && !isset($_COOKIE['tree_structure_open_'.$parent]);
	
	ob_start();
	require B_DIR.'engine/modules/datatable/templates/interface/structure/grid_one_level.php';
	$cnt = ob_get_contents();
	ob_end_clean();
	
	return $cnt;
	
}

?>