<?
$current_type = explode('|', $this->getConfig('current_type'));
$cities = $db->query('select ems_location_display_name as name,default_ops as ind from postcalc_ems_locations where 1 order by name');

$detected_cities = array();

$city_name = '';//Region::getInstance()->getName();
$reg_name = '';
$reg = '';//Kladr::getCityRegion(Region::getInstance()->getCode());
if($reg)
	$reg_name = $reg['NAME'];
if($city_name){
			
	$q = 'trim(ems_location_display_name)="'.mysql_escape_string($city_name).'"';
	if($reg_name){
		$q .= ' or trim(ems_location_display_name) like "'.mysql_escape_string($reg_name).'%"';
	}
			
	$res = $db->query('select * from postcalc_ems_locations where '.$q.' limit 1');
	while($v = $res->getNext()){
		$detected_cities[] = $v['default_ops'];
	}
			
}

//var_dump($detected_cities);

?>