<?

$order_data = $this->getConfig('order_id') ? $db->query('select * from orders where id='.intval($this->getConfig('order_id')))->getNext() : false;

if($order_data){
	
	$order_data['sum_discount'] += $order_data['delivery_price'] - $order_data['use_bonus'];
	
	if($order_data['payment']){
		$payment_data = $db->query('select * from payment_types where id='.$order_data['payment'])->getNext();
		if($payment_data && isset($payment_data['extra_charge']) && $payment_data['extra_charge'] > 0){
			$order_data['sum_discount'] += ceil($order_data['sum_discount']/100*$payment_data['extra_charge']);
		}
	}
	$order_data['phone'] = substr(preg_replace('/[^0-9]/', '', $order_data['phone']), 1, 10);
	
	$order_data['sum'] = number_format($order_data['sum_discount'], 2, '.', '');
	
	$url = 'https://money.yandex.ru/eshop.xml';
	//$url = 'https://demomoney.yandex.ru/eshop.xml';
	
	$values = array(
		'ShopID' => DBCommon::getConfig('yandex_shop_id'),
		'scid' => DBCommon::getConfig('yandex_scid'),
		'sum' => $order_data['sum'],
		'customerNumber' => $order_data['phone'],
		'orderNumber' => $order_data['id'],
		'shopSuccessURL' => 'http://'.$_SERVER['HTTP_HOST'].'/cart/payment_success/',
		'shopFailURL' => 'http://'.$_SERVER['HTTP_HOST'].'/cart/payment_fail/',
		'cps_email' => $order_data['email'],
		'cps_phone' => $order_data['phone'],
		'paymentType' => $this->getConfig('paymentType') ? $this->getConfig('paymentType') : 'AC'	
	);
	
	if($this->getConfig('return_link')){
		$url = 'http://'.$_SERVER['HTTP_HOST'].'/cart/pay/?order='.$order_data['id'].'&secure='.md5(DBCommon::getConfig('yandex_shop_password').'_'.$order_data['email'].'_'.$order_data['id']);
		echo $url;
		$this->setActionTemplate('dummy');
	}
	
}else{
	$this->setActionTemplate('dummy');
}

?>