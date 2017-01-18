<?

class Kladr{
	
	private static $limit = 20;
	
	function getCities($name, $code = ''){
		
		global $db;
		
		$avail_socr = array('г', 'дп', 'д', 'кп', 'пгт', 'рп', 'с/мо', 'с/п', 'с/с', 'с/о', 'с/а', 'с', 'ст-ца', 'сл', 'ст', 'снт');		
		$code = self::trimCode($code);
		
		$q = array();
		if($code != '')
			$q[] = 'CODE like "'.mysql_escape_string($code).'%"';
		if($name != '')
			$q[] = 'NAME like "'.mysql_escape_string($name).'%"';
		if(count($avail_socr))
			$q[] = 'SOCR in ("'.implode('","', $avail_socr).'")';
			
		if(!count($q))
			$q[] = 1;
		
		$res = $db->query('
			select
			*
			from kladr_cities
			where '.implode($q, ' and ').'
			group by CODE
			order by NAME'.(self::$limit ? ' limit '.self::$limit : '')
		);
		
		$list = array();
		while($v = $res->getNext()){
			
			$list[] = array(
				'value' => $v['CODE'],
				'label' => $v['SOCR'].'. '.$v['NAME']
			);
			
		}
		
		return $list;
		
	}
	
	function getStreets($name, $code){
		
		global $db;
	
		$code = self::trimCode($code, 11);
		
		$avail_socr = array();
		
		$q = array();
		if($code != '')
			$q[] = 'CODE like "'.mysql_escape_string($code).'%"';
		if($name != '')
			$q[] = 'NAME like "'.mysql_escape_string($name).'%"';
		if(count($avail_socr))
			$q[] = 'SOCR in ("'.implode('","', $avail_socr).'")';
			
		if(!count($q))
			$q[] = 1;
			
		$res = $db->query('
			select
			*
			from kladr_streets
			where '.implode($q, ' and ').'
			group by NAME
			order by NAME'.(self::$limit ? ' limit '.self::$limit : '')
		);
		
		$list = array();
		while($v = $res->getNext()){
			
			$list[] = array(
				'value' => $v['CODE'],
				'label' => $v['SOCR'].'. '.$v['NAME']
			);
			
		}
		
		return $list;
		
	}
	
	function trimCode($code, $length = 0){
		
		$code = preg_replace('/0+$/', '', $code);
		if($length && strlen($code) > 0){
			$code = str_pad($code, $length, '0');
		}
		
		return $code;
		
	}
	
	function getObjectByCode(){
		
		//global $db;
		
	}
	
	function getCityRegion($code){
		
		global $db;
		
		if($code == '7800000000000' || $code == '7700000000000')
			return false;
		
		$region_code = str_pad(substr($code, 0, 3), 13, 0);
		
		$res = $db->query('select * from kladr_cities where CODE = "'.$region_code.'"')->getNext();
		
		return $res;
		
	}
	
	function getRegionCenter($city){
		
		global $db;
		
		$center = false;

		$q = '';
		
		if(is_array($city)){
			$q = 'NAME in ("'.implode('","', array_map('mysql_escape_string', $city)).'")';
		}else{
			$q = 'NAME = "'.mysql_escape_string($city).'"';
		}
		
		$q .= ' and STATUS < 4';
		
		$res = $db->query('select * from kladr_cities where '.$q.' order by STATUS desc limit 1')->getNext();
		
		if($res){
			
			var_dump($res);
			
			$code = substr($res['CODE'], 0, 2);
			
			$center_res = $db->query('select * from kladr_cities where code like "'.$code.'%" and STATUS > '.intval($res['STATUS']).' and STATUS < 4 order by STATUS desc limit 1')->getNext();
			if($center_res){
				$center = $center_res['NAME'];
			}
			
		}
		
		return $center;
		
	}
	
	function findCityByName($name){
		
		global $db;
		
		$res = $db->query('select * from kladr_cities where NAME="'.mysql_escape_string($name).'" and SOCR="г"')->getNext();
		return $res;
		
	}
	
	function getCityCode($name){
		
		$res = self::findCityByName($name);
		return $res ? $res['CODE'] : '';
		
	}
	
}

?>