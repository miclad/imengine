<?

if(!AuthUser::getInstance()->isAuthorized()){
	header('Location: /login/');
	exit();
}

$history = array();

$res = $db->query('
	select 
		O.*
	from 
		orders as O
	where
		O.user_id = '.intval($USER->getID()).'
		and (
			O.use_bonus > 0
			or
			O.bonus_updated > 0
		)
	order by
		O.status_updated_at,
		O.bonus_updated_at		
');

while($v = $res->getNext()){
	
	if(intval($v['bonus_updated']) > 0){
		$history[] = array(
			'order_id' => $v['id'],
			'order_sum' => $v['sum_discount'],
			'date' => $v['bonus_updated_at'],
			'value' => $v['bonus_updated']
		);
	}
	
	if(intval($v['use_bonus']) > 0){
		$history[] = array(
			'order_id' => $v['id'],
			'order_sum' => $v['sum_discount'],
			'date' => $v['date'],
			'value' => -$v['use_bonus']
		);
	}
	
}

$history = Common::arraySortByField($history, 'date', SORT_DESC, SORT_STRING);

$PAGE->setAttribute('sidebar', Page::includeComponent('personal:navbar'));
$PAGE->setAttribute('hide_title');