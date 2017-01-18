<?

$cities = $db->query('select * from regions where 1 order by name != "Москва", name != "Санкт-Петербург", name');
$ipc = ceil($cities->selectedRowsCount()/4);

$current_region = Region::getInstance()->getName();
$city_region = '';

$city_code = Region::getInstance()->getCode();
if(!$city_code){
	$city_kladr = Kladr::findCityByName($current_region);
	if($city_kladr)
		$city_code = $city_kladr['CODE'];
}

if($city_code){
	$t = Kladr::getCityRegion($city_code);
	if($t)
		$city_region = $t['NAME'].($t['SOCR'] != 'г' ? ' '.mb_strtolower($t['SOCR'], 'utf-8').'.' : '');
}

?>