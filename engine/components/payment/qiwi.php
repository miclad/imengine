<?

$order_data = $this->getConfig('order_id') ? $db->query('select * from orders where id='.intval($this->getConfig('order_id')))->getNext() : false;

if($order_data){
	
	$order_data['sum_discount'] += $order_data['delivery_price'];
	
	if($order_data['payment']){
		$payment_data = $db->query('select * from payment_types where id='.$order_data['payment'])->getNext();
		if($payment_data && isset($payment_data['extra_charge']) && $payment_data['extra_charge']){
			$order_data['sum_discount'] += ceil($order_data['sum_discount']/100*$payment_data['extra_charge']);
		}
	}
	$order_data['phone'] = substr(preg_replace('/[^0-9]/', '', $order_data['phone']), 1, 10);
	
	$login = DBCommon::getConfig('qiwi_login');	
	$order_data['sum'] = number_format($order_data['sum_discount'], 2, null, '');	
	
	$url = $login == '' || $login == 'powar' ? 'http://w.qiwi.ru/setInetBill_utf.do' : 'http://w.qiwi.ru/setInetBill_utf.do'; // check if using in test mode
	
	$values = array(
		'from' => $login,
		'to' => $order_data['phone'],
		'summ' => $order_data['sum'],
		'com' => 'Оплата заказа №'.$order_data['id'].' в интернет-магазине '.str_replace('www.', '', $_SERVER['SERVER_NAME']),
		'lifetime' => 24,
		'check_agt' => 'false',
		'txn_id' => $order_data['id']
	);
	
}else{
	$this->setActionTemplate('dummy');
}

?>