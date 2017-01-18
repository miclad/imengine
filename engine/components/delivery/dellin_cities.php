<?

require '../../conf.php';

$db->query('truncate table cities_dellin');

$xml = new DOMDocument();
$data = trim(implode('', file('http://public.services.dellin.ru/calculatorService2/index.html?request=xmlForm')));
$xml->loadXML($data);
$cities = $xml->getElementsByTagName('city');
foreach($cities as $c){
	$name = $c->getElementsByTagName('name')->item(0)->nodeValue;
	$id = $c->getElementsByTagName('id')->item(0)->nodeValue;
	$is_terminal = $c->getElementsByTagName('isTerminal')->item(0)->nodeValue;
	echo $name.' '.$id.'<br>';
	$db->query('insert into cities_dellin set name="'.$name.'",value="'.$id.'",is_terminal='.intval($is_terminal));
}

?>