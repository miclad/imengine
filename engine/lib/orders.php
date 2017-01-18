<?

class Orders{
	
	function getOrdersByUser($user_id, $regular = false, $where = 1, $order_by = 'id desc'){
		$res = $GLOBALS['db']->query('select O.*,S.name as status_name 
		                              from orders as O 
		                              left join order_statuses as S on O.status=S.id
		                              where user_id='.intval($user_id).'
		                              and is_regular='.intval($regular).'
		                              and '.$where.'
		                              order by '.$order_by);
		return $res;
	}
	
	function getItems($order_id){
		
		$res = $GLOBALS['db']->query('
			select 
				C.*,
				B.name as brand_name,
				I.price as order_price,
				I.discount as order_discount,
				F.url as folder_url,
				I.items_num,
				I.item_variant,
				I.item_variant as v_id,
				I.id as entry_id,
				V.name as variant_name,
				V.name as variant_name,
				(select id from catalog_photos as PH where PH.item_id=C.id order by pos limit 1) as photo_id
			from 
				(order_items as I,catalog as C) 
			left join 
				catalog_item_variants as V 
				on 
				V.id=I.item_variant
			left join 
				folders as F 
				on 
				F.id=C.folder_id
			left join 
				brands as B 
				on 
				B.id=C.brand
			where 
				I.order_id='.intval($order_id).' 
				and 
				C.id=I.item_id
			order by 
				I.id
		');
		return $res->fetchArray();
		
	}
	
	function getItemsNum($order_id){
		$res = $GLOBALS['db']->query('select sum(items_num) as cnt from order_items where order_id='.intval($order_id).' group by order_id')->getNext();
		return intval($res['cnt']);
	}
	
	function getOrderSum($id){
		$res = $GLOBALS['db']->query('select sum(V.price*I.items_num) as total_sum
		                              from (order_items as I,product_variants as V)
		                              where I.order_id='.intval($id).' and I.item_variant=V.id
		                              group by I.order_id');
		$sum_data = $res->getNext();
		if($sum_data){
			return $sum_data;
		}
		return array('total_sum' => 0, 'currency' => SiteCurrency::getInstance()->getId());
	}
	
	function saveAsRegular($oid){
		if(in_array($_POST['regular_settings']['rate'], array('daily', 'weekly', 'monthly'))){
			$day = 0;
			if($_POST['regular_settings']['rate'] == 'weekly' && isset($_POST['regular_settings']['week_day'])){
				$day = intval($_POST['regular_settings']['week_day']);
			}elseif($_POST['regular_settings']['rate'] == 'monthly' && isset($_POST['regular_settings']['month_day'])){
				$day = intval($_POST['regular_settings']['month_day']);
			}
			$upd = array('is_regular' => 1, 'regular_rate' => $_POST['regular_settings']['rate']);
			if($day){
				$upd['regular_day'] = $day;
			}
			
			$o_data = $GLOBALS['db']->query('select * from orders where id='.intval($oid))->getNext();
			if($o_data){
				if(!$o_data['is_regular'] && !$o_data['from_regular']){
					$iid = DataTable::copyElements('orders', 'id='.$oid, $upd);
					if($iid)
						$GLOBALS['db']->query('update orders set from_regular='.$iid.' where id='.$oid);
				}elseif($o_data['is_regular']){
					$GLOBALS['db']->query('update orders set regular_rate="'.$_POST['regular_settings']['rate'].'",regular_day='.($day ? $day : 'NULL').' where id='.$oid);
				}
			}
		}		
	}
	
	function createOrder($values, $items){

		global $db;
		
		$order_id = 0;
		
		$status = $db->query('select * from order_statuses where 1 order by pos limit 1')->getNext();
		if($status)
			$values['status'] = $status['id'];
		
		$res = $db->query(
			'insert into orders('.implode(',',array_keys($values)).') values("'.implode('","',array_map('mysql_escape_string', array_values($values))).'")'
		);
		
		if($res->lastID()){
			$order_id = intval($res->lastID());
			foreach($items as $v){
				$db->query(
					'insert into order_items set 
					order_id='.$order_id.',
					item_id='.intval($v['id']).',
					item_variant='.intval($v['item_variant']).',
					items_num='.intval($v['items_num']).',
					price='.str_replace(',', '.', floatval($v['price_real'])).',
					discount=0,xml_id="'.$v['xml_id'].'"'
				);
			}
		}
		
		return $order_id;
		
	}
	
	function getStatusHistory($order_id){
		
		global $db;
		
		$res = $db->query('
			select
				S.name,
				H.*
			from
				orders_status_history as H,
				order_statuses as S
			where
				H.order_id='.intval($order_id).'
				and
				H.status=S.id
			order by
				H.id desc			
		')->fetchArray();
		
		if(!count($res)){
			$i_status = $db->query('select S.name,S.id as status from order_statuses as S,orders as O where O.id='.intval($order_id).' and O.status=S.id')->getNext();
			if($i_status){
				$res[] = array(
					'status' => $i_status['status'],
					'name' => $i_status['name'],
					'comment' => '',
					'tsmp' => date('Y-m-d H:i:s')
				);
			}
		}
		
		return $res;
		
	}
	
	function calculateCostPrice($order_data){
		
		global $db;
		
		if(is_numeric($order_data)){
			$order_data = $db->query('select * from orders where id='.intval($order_data))->getNext();
		}
		
		$purchase_price = 8; // ID типа закупочной цены
		
		$price = $db->query('
			select
				sum(P.price*O.items_num) as price
			from
				catalog_prices as P,
				order_items as O
			where
				O.order_id = '.$order_data['id'].'
				and
				P.product_id=O.item_id
				and
				P.price_type_id = '.intval($purchase_price).'
		')->getNext('price');
		
		return intval($price);
		
	}
	
}

?>