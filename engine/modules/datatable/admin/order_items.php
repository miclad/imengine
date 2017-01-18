<?

$entity = isset($_GET['entity']) && $_GET['entity'] != '' ? $_GET['entity'] : '';
	
$dt = new DataTable($entity);			
if($dt->isOk){			
	
	$dt_orders = new DataTable('orders');
	$dt_orders->initCurRow('id='.$dt->foreignValue);
	
	MenuTree::getInstance()->setCurrentItem('datatable_orders');

	if(isset($_POST['update_data']) && isset($_POST['id'])){
		$dt_o = new DataTable('orders');
		$dt_o->updateRow();
		Header('Location: '.$_SERVER['REQUEST_URI']);
		exit();
	}
	
	if(isset($_POST['change_discount']) && isset($_GET['order_id']) && isset($_POST['discount'])){
		$order_data = $db->query('select * from orders where id='.intval($_GET['order_id']))->getNext();
		if($order_data){
			$discount = intval($_POST['discount']);
			$sum_discount = round($order_data['sum']*(1-$_POST['discount']/100));
			$db->query('update orders set discount_value='.$discount.',sum_discount='.$sum_discount.' where id='.intval($_GET['order_id']));
		}
		Header('Location: '.$_SERVER['REQUEST_URI']);
		exit();
	}
	
	if(isset($_POST['update_delivery'])){
		
		$delivery_type = Delivery::buildString($_POST);
		
		$data = array(
			'payment' => $dt_orders->getRowValue('payment'),
			'delivery_type' => $delivery_type,
			'delivery_price' => intval(isset($_POST['delivery']['price']) ? $_POST['delivery']['price'] : ''),
			'delivery_tracking_number' => $dt_orders->getRowValue('delivery_tracking_number'),
			'id' => $dt->foreignValue
		);
		
		
		$dt_orders->updateRow($data);
		
		Header('Location: '.$_SERVER['REQUEST_URI']);
		exit();
	}
	
	$order_data = $db->query('select * from orders where id='.$dt->foreignValue)->getNext();
	
	$statuses_history = Orders::getStatusHistory($order_data['id']);
	
	$products = Orders::getItems($order_data['id']);
	
	if(isset($_GET['print'])){	
		require MODULES_DIR.'datatable/templates/interface/order_items/print.php';
		exit();
	}
		
	$dt->execute();
	$page = $dt->getContents();
		
	$path = $dt->getPath();
	foreach($path as $k => $v){
			//NaviPath::addItem($v[0], (isset($v[1]) ? $v[1] : ''));
	}		
	
	$order_ttl = 'Заказ номер '.$dt->foreignValue.', '.Site::formatDBDate($order_data['date']);
	NaviPath::addItem('Заказы', 'index.php?module=datatable&entity=orders');
	NaviPath::addItem($order_ttl, 'index.php?module=datatable&entity=order_items&order_id='.$dt->foreignValue);
	if(isset($_GET['show'])){
		NaviPath::addItem($dt->getRowValue('id') ? 'Редактирование товара' : 'Добавить товар');
	}

	$links = $dt->getLinks();
	foreach($links as $v){	
		MenuTree::getInstance()->addItem('', $v[0], $v[1], array('_controls_buttons', 'datatable_orders'), '', (isset($v[2]) ? $v[2] : ''));
	}
	if(!isset($_GET['show']))
		MenuTree::getInstance()->addItem('', 'Печать', 'index.php?module=datatable&entity=order_items&order_id='.$dt->foreignValue.'&print', array('_controls_buttons', 'datatable_orders'), '', 'print', true);
	
	$statuses_common = $db->query('select * from order_statuses where status_type="common" order by pos')->fetchArray();
	$statuses_payment = $db->query('select * from order_statuses where status_type="payment" order by pos')->fetchArray();
	
	$tsmp_statuses = array('common' => 0, 'payment' => 0);
	foreach($tsmp_statuses as $k => $v){
		$tsmp_statuses[$k] = $db->query('
			select
				H.tsmp
			from
				orders_status_history as H
			where
				H.order_id='.$order_data['id'].'
				and
				H.status='.intval($k == 'common' ? $order_data['status'] : $order_data['status_payment']).'
			order by
				H.id desc
		')->getNext('tsmp');
	}
		
	if($page)
		require $page;
			
	SiteNotices::addNotice($dt->renderErrors(), 'error');
		
}else{	
	throw new Exception('Ошибка инициализации');	
}
			
?>