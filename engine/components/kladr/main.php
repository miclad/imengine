<?

if(!defined('IN_CONTEXT')) require '../../conf/init.php';

if(isset($_GET['action'])){
	
	$code = isset($_GET['code']) ? $_GET['code'] : 0;
	$str = isset($_GET['q']) ? Common::prepareGetString($_GET['q']) : '';
	
	$result = array();
	
	if(strlen($str) > 0){	
		switch($_GET['action']){
			
			case 'get_cities':
				
				$result = Kladr::getCities($str, $code);
				if(isset($_GET['with_region'])){
					foreach($result as $k => $v){
						$r = Kladr::getCityRegion($v['value']);
						if($r){
							$result[$k]['region'] = $r['NAME'];
							$result[$k]['label'] .= ', '.$result[$k]['region'].($r['SOCR'] != 'г' ? ' '.mb_strtolower($r['SOCR'], 'utf-8') : '');
						}
					}
				}
				
			break;
			
			case 'get_streets':
				
				$result = Kladr::getStreets($str, $code);
				
			break;
			
		}
	}
	
	echo json_encode($result);
	
}

?>