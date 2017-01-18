<?

global $entities;

$access_rules = array();

foreach($entities as $k => $v){
	
	if(!isset($v['force_access_rules']) && isset($v['init_by']) || !isset($v[0][0]) || $v[0][0] == '' || isset($v['no_access_rules'])){
		continue; // skip sub entity
	}
		
	$access_rules[] = array(
		'name' => 'datatable_'.$k,
		'title' => $v[0][0],
		'levels' => array(
			'r' => 'Просмотр',
			'w' => 'Полный доступ',
			//'owner' => 'Только свои'
		)
	);
	
}

?>