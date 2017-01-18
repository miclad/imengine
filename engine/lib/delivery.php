<?

class Delivery{
	
	function parseDeliveryType($code, $return_mode = 'str'){
		
		global $db;
		
		$str = '';
		$arr = array();
		$code_array = explode('|', $code);
		if(count($code_array) && is_numeric($code_array[0])){
			
			// get type
			$type = $db->query('select * from delivery_types where id='.intval($code_array[0]))->getNext();
			if($type){
				
				$str = $type['name'];
				$arr[] = $type['name'];
				
				// get city
				if(count($code_array) > 1 && $code_array[1] != ''){
					$city_q = 'id='.intval($code_array[1]);
					$city_table = 'delivery_cities';
					if($type['external_calc'] == 'ems'){
						$city_q = 'value="'.mysql_escape_string($code_array[1]).'"';
						$city_table = 'cities';
					}elseif($type['external_calc'] == 'dellin'){
						$city_q = 'value="'.mysql_escape_string($code_array[1]).'"';
						$city_table = 'cities_dellin';
					}
					$city = $db->query('select name from '.$city_table.' where '.$city_q.' limit 1')->getNext();
					if($city){
						$str .= ', '.$city['name'];
						$arr[] = $city['name'];
					}
				}
				
				// доставка до двери (деловые линии)
				if(count($code_array) > 2 && $code_array[2] && $type['external_calc'] == 'dellin'){
					$str .= ', доставка до двери';
					$arr[] = 'доставка до двери';
				}
				
			}
			
		}
		
		return $return_mode == 'str' ? $str : $arr;
		
	}
	
	function buildString($post_data){
		return (isset($post_data['delivery']['group']) ? $post_data['delivery']['group'].'|' : '').(isset($post_data['delivery']['type']) ? $post_data['delivery']['type'].(isset($post_data['delivery']['city'.$post_data['delivery']['type']]) ? '|'.$post_data['delivery']['city'.$post_data['delivery']['type']].(isset($post_data['dellin_arrival_door']) ? '|1' : '|0').(isset($post_data['ems_nalozhenniy_platezh']) ? '|1' : '|0') : '').(isset($_POST['delivery']['pickpoint_address_'.$_POST['delivery']['type']]) && $_POST['delivery']['pickpoint_address_'.$_POST['delivery']['type']] != '' ? '|'.$_POST['delivery']['pickpoint_address_'.$_POST['delivery']['type']].'|'.$_POST['delivery']['pickpoint_name_'.$_POST['delivery']['type']].'|'.$_POST['delivery']['pickpoint_id_'.$_POST['delivery']['type']] : '') : '');
	}
	
	function buildAddressString($data){
		
		$str = '';
		
		if(isset($data['address_post_index']) && $data['address_post_index'] != '')
			$str .= ($str != '' ? ', ' : '').$data['address_post_index'];
		
		if(isset($data['address_region']) && $data['address_region'] != '')
			$str .= ($str != '' ? ', ' : '').$data['address_region'];
			
		if(isset($data['address_city']) && $data['address_city'] != '')
			$str .= ($str != '' ? ', ' : '').$data['address_city'];
			
		if(isset($data['address_street']) && $data['address_street'] != ''){
			$str .= ($str != '' ? ', ' : '').$data['address_street'];
			if(isset($data['address_house']) && $data['address_house'])
				$str .= ', дом '.$data['address_house'];
			if(isset($data['address_corp']) && $data['address_corp'])
				$str .= ', корп. '.$data['address_corp'];
			if(isset($data['address_flat']) && $data['address_flat'])
				$str .= ', кв. '.$data['address_flat'];
			if(isset($data['address_office']) && $data['address_office'])
				$str .= ', офис '.$data['address_office'];
		}
		
		return $str;
		
	}
	
}

?>