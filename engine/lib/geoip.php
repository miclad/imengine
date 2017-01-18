<?

class GeoIP{	

	function detect($check_session = true){
		
		$result = false;
		
		/*
		if($check_session && isset($_SESSION['geo_ip']) && $_SESSION['geo_ip'])
			$result = $_SESSION['geo_ip'];
		*/
			
		if(!$result){
		
			$ip = $_SERVER['REMOTE_ADDR'];
			if($ip == '127.0.0.1'){
				$ip = '195.239.0.1';
			}
			
			$result = array('city' => '', 'region' => '');
			
			$response = @file_get_contents('http://ipgeobase.ru:7020/geo?ip='.$ip);

			if($response){
				
				//echo $response;
				
				$xml = new DOMDocument();
				$xml->loadXML($response);
				
				if($xml){
					
					$c = $xml->getElementsByTagName('city');
					if(count($c)){
						$result['city'] = $c->item(0)->nodeValue;
					}
					
					$r = $xml->getElementsByTagName('region');
					if(count($r)){
						$result['region'] = $r->item(0)->nodeValue;
					}
					
				}
				
			}
			
			if(!$result['city'] && !$result['region'])
				$result = false;
			
			//$_SESSION['geo_ip'] = $result;
		
		}

		return $result;		
		
	}
	
}
	
?>