<?

class GMaps{
	
	private static $db_cache = array();
		
	function getLatLng($address){
		
		$db = $GLOBALS['db'];
		$md = md5($address);
		
		if(isset(self::$db_cache[$md])){
			return self::$db_cache[$md];
		}else{
			$db_data = $db->query('select * from geocode where address="'.mysql_escape_string($address).'"')->getNext();
			if($db_data){
				self::$db_cache[$md] = $db_data;
				return $db_data;
			}else{
				// make geocode request
				$request = 'http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false';
				//$request = 'http://maps.google.com/maps/geo?q='.urlencode($address).'&output=csv&oe=utf8&sensor=false';
				//$rsp = implode(',', file($request));	
				$rsp = file_get_contents($request);
				if($rsp != ''){			
					$rsp_obj = json_decode($rsp);	
					$rsp_array = stdToArray($rsp_obj);
					if(count($rsp_array['results']) > 0){
						$ins = array(
							'address' => '"'.mysql_escape_string($address).'"',
							'lat' => $rsp_array['results'][0]['geometry']['location']['lat'],
							'lng' => $rsp_array['results'][0]['geometry']['location']['lng']
						);
						$db->query('insert into geocode('.implode(',', array_keys($ins)).') values('.implode(',', array_values($ins)).')');
						self::$db_cache[$md] = $ins;
						return $ins;
					}
				}
			}
		}
		
		return false;		
		
	}
	
}

function stdToArray($obj){
	$rc = (array)$obj;
	foreach($rc as $key => $field){
		if(is_object($field) || is_array($field))$rc[$key] = stdToArray($field);
	}
	return $rc;
}

?>