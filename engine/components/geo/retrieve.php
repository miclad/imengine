<?

if(!defined(IN_CONTEXT)){
	require '../../conf/init.php';
}

$allowed_entities = array('cities', 'countries');

if(isset($_GET['target']) && in_array($_GET['target'], $allowed_entities) && isset($_GET['term'])){
	
	$dt_o = new DataTable($_GET['target']);
	$items = $dt_o->autocomplete('name', $_GET['term']);
	
	echo json_encode($items);
	
}

?>