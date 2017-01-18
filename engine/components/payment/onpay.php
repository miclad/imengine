<?

$order_data = $this->getConfig('order_id') ? $db->query('select * from orders where id='.intval($this->getConfig('order_id')))->getNext() : false;

if($order_data){
	
	$order_data['sum_discount'] += $order_data['delivery_price'];
	
	if($order_data['payment']){
		$payment_data = $db->query('select * from payment_types where id='.$order_data['payment'])->getNext();
		if($payment_data && $payment_data['extra_charge']){
			$order_data['sum_discount'] += ceil($order_data['sum_discount']/100*$payment_data['extra_charge']);
		}
	}
	$order_data['phone'] = substr(preg_replace('/[^0-9]/', '', $order_data['phone']), 1, 10);
	
	$login = DBCommon::getConfig('onpay_login');	
	$order_data['sum'] = number_format($order_data['sum_discount'], 1, null, '');	
	
	$url = 'http://secure.onpay.ru/pay/'.$login;
	
	$values = array(
		'pay_mode' => 'fix',
		'price' => $order_data['sum'],
		'ticker' => 'RUR',		
		'pay_for' => $order_data['id'],
		'convert' => 'yes',
		'url_success' => 'http://'.$_SERVER['SERVER_NAME'].'/cart/payment_ok/',
		'url_fail' => 'http://'.$_SERVER['SERVER_NAME'].'/cart/payment_fail/',
		'user_email' => $order_data['email'],
		'note' => 'заказ №'.Orders::makeDateSlug($order_data['date']),
		'price_final' => 'false'		
	);
	
	$values['md5'] = md5($values['pay_mode'].';'.$values['price'].';'.$values['ticker'].';'.$values['pay_for'].';'.$values['convert'].';'.DBCommon::getConfig('onpay_secret'));
	
	if($this->getConfig('return_link')){
		$alr = false;
		$url .= '?';
		foreach($values as $k => $v){
			if($alr)
				$url .= '&';
			$url .= $k.'='.urlencode($v);
			$alr = true;
		}
		echo $url;
		$this->setActionTemplate('dummy');
	}
	
}else{
	$this->setActionTemplate('dummy');
}

?>