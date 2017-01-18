<?

global $cart_o;

if(true || $USER->isAuthorized()){
	
	if(true || !$this->getInitParam('sum')){
		$stat = $GLOBALS['cart_o']->getWishlistStat($GLOBALS['cart_o']->getDefaultList());
	}
	$sum = $this->getInitParam('sum')? $this->getInitParam('sum') : $stat['sum'];
	$products = $GLOBALS['cart_o']->getWishlistItems($GLOBALS['cart_o']->getDefaultList());
	$pr = isset($_GET['products']) ? explode(',', $_GET['products']) : '';
	if(is_array($pr)){
		foreach($products as $k=>$v){
			foreach($pr as $pp){
				$p = explode('|', $pp);
				if($v['id'] == $p[0]){
					$products[$k]['items_num'] = $p[1];
					break;
				}
			}
		}
	}
	$discount = Catalog::getDiscount($sum, null, $products);
	
	ob_start();
	require 'templates/discount_info.php';
	$resp = array(
		'sum' => $sum,
		'perc' => $discount['perc'],
		'sum_discount' => $discount['total_sum'],
		'weight' => $cart_o->getTotalWeight($cart_o->getDefaultList()),
		'html' => ob_get_contents(),
		'stat_text' => $stat['text'],
		'items_num' => $stat['count_num']
	);
	ob_end_clean();
	
	echo Site::isAjaxRequest() ? json_encode($resp) : $resp['html'];
	$this->setActionTemplate('dummy');

}

?>