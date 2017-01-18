<?

$address = '';

if($this->getInitParam('object') && $this->getInitParam('id')){
	
	switch($this->getInitParam('object')){
		case 'place':
			$data = $db->query('
				select 
				P.*,C.name as city_name
				from 
				places as P, cities as C 
				where 
				P.id='.intval($this->getInitParam('id')).' and P.city_id=C.id'
			)->getNext();
			if($data){
				$address = $data['city_name'].', '.$data['address'];
			}
		break;
		case 'event':
			$data = $db->query('
				select 
				P.*,C.name as city_name
				from 
				events as P left join cities as C on P.city_id=C.id
				where 
				P.id='.intval($this->getInitParam('id'))
			)->getNext();
			if($data){
				$address = $data['city_name'].', '.$data['address'];
			}
		break;
	}
	
}

$center = false;

if($address){
	$center = GMaps::getLatLng($address);
}

if(!$center){
	echo 'Объект не найден';
	$this->setActionTemplate('dummy');
}

?>