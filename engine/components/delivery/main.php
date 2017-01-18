<?

$city_name = Region::getInstance()->getName();
$reg = Kladr::getCityRegion(Region::getInstance()->getCode());
$region_name = $reg ? $reg['NAME'] : '';

$order_sum = intval($this->getInitParam('order_sum'));
$is_member = AuthUser::getInstance()->getField('discount_card') ? DiscountCards::cardExists(AuthUser::getInstance()->getField('discount_card')) : 0;

$current_type = explode('|', $this->getConfig('current_type'));
$current_folder = '';

$type_groups = array();
//$groups = $db->query('select * from folders where entity="delivery_types" order by pos')->fetchArray();
$groups[] = array('id' => 0, 'name' => '');

foreach($groups as $t){
	
	$where = $this->is_inner ? '1' : 'show_on_page=1';
	
	$where .= ' and folder_id = '.$t['id'];
	
	if(!$this->getConfig('admin_mode') && ($this->is_inner || Site::isAjaxRequest())){
		
		$group_data = false;
		
		$fixed_cities = array('Санкт-Петербург', 'Москва');
		
		$check_city = '';
		if(in_array($city_name, $fixed_cities)){
			$check_city = $city_name;
		}elseif(in_array($region_name, $fixed_cities)){
			$check_city = $region_name;
		}
		
		if($check_city){
			$group_data = $db->query('select * from delivery_groups where name = "'.mysql_escape_string($check_city).'" limit 1')->getNext();
		}else{
			$group_data = $db->query('select * from delivery_groups where name not in ("'.implode('","', array_map('mysql_escape_string', $fixed_cities)).'") limit 1')->getNext();
		}
		
		if($group_data){
			$where .= ' and (select count(TG.id) from delivery_types_groups as TG where TG.group_id = '.$group_data['id'].' and TG.type_id = T.id) > 0';
		}
		
	}
	
	$types =  $db->query('
		select 
			T.* 
		from 
			delivery_types as T
		where 
			'.$where.'
		order by 
			T.pos
	')->fetchArray();
	
	if($this->is_inner || Site::isAjaxRequest()){
		
		foreach($types as $k => $v){
			if(isset($current_type[0]) && $v['id'] == $current_type[0])
				$current_folder = $v['folder_id'];
			$types[$k]['cities'] = $v['external_calc'] != 'pickpoint' ? $db->query('select * from delivery_cities where type_id='.$v['id'].' order by pos')->fetchArray() : array();
			foreach($types[$k]['cities'] as $ck => $cv){
				//$types[$k]['cities'][$ck]['prices'] = $db->query('select * from delivery_prices where city_id='.$cv['id'].' order by order_sum_from desc')->fetchArray();
			}
			$types[$k]['prices'] = $db->query('select order_sum_from,price'.($is_member ? '_for_members' : '').' as price from delivery_prices where type_id='.$v['id'].' order by order_sum_from desc')->fetchArray();
		}
		
	}
	
	$type_groups[] = array_merge($t, array('types' => $types));
	
}

if(Site::isAjaxRequest()){
	$this->setActionTemplate('types_list');
}elseif(!$this->is_inner)
	$this->setActionTemplate('list');

if(!count($current_type) || !$current_type[0]){
	$current_type = array($type_groups[0]['types'][0]['id']);
	$current_folder = $type_groups[0]['types'][0]['folder_id'];
}



?>