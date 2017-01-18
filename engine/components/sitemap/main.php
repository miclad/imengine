<?

$map_tree = new Tree('structure', '(main=0 or main is null)');
$t_pages = $map_tree->getFullTree(0, 0, -1);
$pages = array();

foreach($t_pages as $k => $v){
	$pages[] = $v;
	if($v['url'] == '/catalog'){
		$sections = $db->query('select * from folders where entity="catalog" and parent=0 and id != '.intval($GLOBALS['sale_catalog_section']).' order by pos');
		while($s = $sections->getNext()){
			$s['hard_link'] = '';
			$s['_tree_level'] = 1;
			$s['url'] = '/catalog'.$s['url'];
			$pages[] = $s;
		}
	}
}

?>