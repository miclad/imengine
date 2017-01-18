<?

if(isset($_GET['city']) && isset($_GET['target'])){
	
	$list = $db->query('
		select * from subway_stations where city_id='.intval($_GET['city'])
	);
	
}

?>