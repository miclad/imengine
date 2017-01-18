<?

if(!defined('IN_CONTEXT'))
	require '../../conf/init.php';

$response = array(
	'price' => 0,
	'error' => ''
);
	
if(isset($_GET['derivalPoint']) && isset($_GET['arrivalPoint']) && isset($_GET['sizedWeight']) && isset($_GET['sizedVolume'])){
	
	$xml = new DOMDocument();
	//$data = trim(implode('', file('http://public.services.dellin.ru/calculatorService2/index.html?request=xmlResult&derivalPoint='.$_GET['derivalPoint'].'&arrivalPoint='.$_GET['arrivalPoint'].'&sizedWeight='.intval($_GET['sizedWeight']).'&sizedVolume='.$_GET['sizedVolume'].(isset($_GET['arrivalDoor']) && $_GET['arrivalDoor'] ? '&arrivalDoor=1' : ''))));
	$ch = curl_init();
	@curl_setopt($ch, CURLOPT_URL, 'http://public.services.dellin.ru/calculatorService2/index.html?request=xmlResult&derivalPoint='.$_GET['derivalPoint'].'&arrivalPoint='.$_GET['arrivalPoint'].'&sizedWeight='.$_GET['sizedWeight'].'&sizedVolume='.$_GET['sizedVolume'].(isset($_GET['arrivalDoor']) && $_GET['arrivalDoor'] ? '&arrivalDoor=1' : ''));
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($ch);
	curl_close($ch);  
	
	if($data){
		$xml->loadXML($data);
		$err = $xml->getElementsByTagName('error');
		if($err->length){
			$response['error'] = $err->item(0)->nodeValue;
		}else{
			$pr = $xml->getElementsByTagName('price');
			if($pr->length){
				$response['price'] = trim($pr->item(0)->nodeValue);
			}
		}
	}else{
		$response['error'] = 'Произошла ошибка';
	}
	
}else{
	$response['error'] = 'Не заданы параметры доставки';
}

echo json_encode($response);

?>