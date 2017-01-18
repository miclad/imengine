<?

$order_data = $this->getConfig('order_id') ? $db->query('select * from orders where id='.intval($this->getConfig('order_id')))->getNext() : false;

if($order_data){
	
	$values = array(
		'eshopId' => DBCommon::getConfig('rbk_eshopId'),
		'orderId' => $order_data['id'],
		'serviceName' => 'Оплата заказа №'.$order_data['id'],
		'recipientAmount' => ($order_data['sum_discount']+$order_data['delivery_price']),
		'recipientCurrency' => 'RUR',
		'user_email' => $order_data['email'],
		'version' => 1,
		'direct' => 'false',
		'successUrl' => 'http://'.$_SERVER['SERVER_NAME'].'/personal/payment_success/',
		'failUrl' => 'http://'.$_SERVER['SERVER_NAME'].'/pesonal/paiment_fail/'		
	);
	
}else{
	$this->setActionTemplate('dummy');
}

?>