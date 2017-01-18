<?

$order_data = $this->getConfig('order_id') ? $db->query('select * from orders where id='.intval($this->getConfig('order_id')))->getNext() : false;

if($order_data){
	
	$login = DBCommon::getConfig('robokassa_login');	
	$password = DBCommon::getConfig('robokassa_password1');
	$order_data['sum'] = number_format($order_data['sum_discount'], 2, null, '');	
	$signature = md5($login.':'.$order_data['sum'].':'.$order_data['id'].':'.$password);
	
	$url = $login == '' || $login == 'powar' ? 'http://test.robokassa.ru/Index.aspx' : 'https://merchant.roboxchange.com/Index.aspx'; // check if using in test mode
	
	$values = array(
		'MrchLogin' => $login,
		'OutSum' => $order_data['sum'],
		'InvId' => $order_data['id'],
		'SignatureValue' => $signature,
		//'IncCurrLabel' => 'RUR',
		'Email' => $order_data['email'],
		'Culture' => 'ru'
	);
	
}else{
	$this->setActionTemplate('dummy');
}

?>