<?

require '../../conf.php';

$db->query('truncate table cities');

$requests = array(
	'http://emspost.ru/api/rest/?method=ems.get.locations&type=cities&plain=true',
	'http://emspost.ru/api/rest/?method=ems.get.locations&type=regions&plain=true'
);

foreach($requests as $r){
	
	$str = implode(File($r));
	
	echo $r.'<br>';

	preg_match_all('/\"value\"\:\"(.+?)\"\,\"name\"\:\"(.+?)\"/', $str, $pock, PREG_SET_ORDER);
	
	foreach($pock as $v){
		$db->query('insert into cities set value="'.mysql_escape_string($v[1]).'",name="'.mysql_escape_string($v[2]).'"');
	}

}

?>