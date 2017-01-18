<?

if(!Defined('IN_CONTEXT'))
	require dirname(__FILE__).'/../../conf/init.php';

if(isset($_POST['delivery_type']) && is_numeric($_POST['delivery_type']) && $_POST['delivery_type'] > 0){
	
	$list = $db->query('
		select P.* 
		from payment_types as P,payments_deliveries as D
		where P.id=D.payment_type and D.delivery_type='.intval($_POST['delivery_type']).' 
		group by P.id
		order by P.pos'
	);
	
	if(isset($_POST['current_type']) && $_POST['current_type']){
		$current_type = $_POST['current_type'];
	}else{
		$t = $list->getNext();
		if($t)
			$current_type = $t['id'];
		$list->clearCounter();
	}
	
	if(!isset($this)){
		$template = 'select';
		if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/engine/admin/') !== false)
			$template = 'select_admin';
		require 'templates/'.$template.'.php';
	}
	
}

?>