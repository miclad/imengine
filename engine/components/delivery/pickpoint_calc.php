<?

if(!defined('IN_CONTEXT'))
	require '../../conf/init.php';

$data = $_GET;
$response = array('tariff' => 'not_set', 'time' => '');
if(isset($data['address'])){
	
	$type_data = $db->query('select * from delivery_types where external_calc="pickpoint"')->getNext();
	if($type_data){
	
		$addr_array = explode(',', $data['address']);
		if(count($addr_array) > 2 && $addr_array[2] != ''){
			$res = $db->query('
				select 
					C.*,
					Z.tariff
				from 
					delivery_cities as C
				left join
					delivery_tariff_zones as Z 
					on
					Z.id=C.zone_id
				where 
					C.type_id='.$type_data['id'].'
					and 
					(C.name="'.mysql_escape_string(trim($addr_array[2])).'"'.(count($addr_array) > 3 ? ' or C.name="'.mysql_escape_string(trim($addr_array[3])).'"' : '').')
			')->getNext();
			if($res){
				
				// calculate
				
				$weight = isset($_GET['weight']) ? floatval($_GET['weight']) : 0;
				
				$weight = ceil($weight/0.5) * 0.5;
				
				$response['tariff'] = (floatval($res['tariff']) * $weight) + floatval($type_data['box_tariff']);
				
				if(!$res['is_center']){
					$response['tariff'] *= 1.25;
				}
				
				$response['tariff'] = round($response['tariff']*1.18, 2);
				
				$response['time'] = $res['delivery_time'];
				
			}
		}
	
	}
	
}

echo json_encode($response);

?>