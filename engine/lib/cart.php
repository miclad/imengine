<?

class Cart{
	
	private $user_id = 0;
	private $default_list = 0;
	
	function __construct($user_id = 0){
		global $db;
		$this->user_id = intval($user_id);
		if(AuthUser::getInstance()->isAuthorized() && !AuthUser::getInstance()->getCartUserId() && isset($_SESSION['cart_user_id']) && $_SESSION['cart_user_id']){
			$db->query('update wishlist_users set user_id='.AuthUser::getInstance()->getId().' where id='.intval($_SESSION['cart_user_id']));
		}
		if(AuthUser::getInstance()->isAuthorized() && isset($_SESSION['cart_user_id']))
			unset($_SESSION['cart_user_id']);
	}
	
	/**
	 *
	 * COMMON FUNCTIONS
	 *
	 */
	
	function getUserId(){
		return $this->user_id;
	}
	
	function createUser(){
		global $db;
		$res = $db->query('insert into wishlist_users set last_visit_at=NOW()'.(AuthUser::getInstance()->isAuthorized() ? ',user_id='.AuthUser::getInstance()->getId() : ''));
		if($res->lastID()){
			if(!AuthUser::getInstance()->isAuthorized())
				$_SESSION['cart_user_id'] = $res->lastID();
			else{
				AuthUser::getInstance()->setCartUserId($res->lastID());
			}
			$this->user_id = $res->lastID();
		}
	}
	
	function getUserLists(){		
		$res = $GLOBALS['db']->query('select * from wishlist where user_id='.$this->user_id.' order by id');
		return $res->fetchArray();		
	}	
	
	function createWishlist($name, $default = false, $main = 0){
		if(!$this->user_id)
			return false;
			
		if(trim($name) != ''){
			$res = $GLOBALS['db']->query('insert into wishlist set created_tsmp=NOW(),name="'.mysql_escape_string(trim($name)).'", user_id='.intval($this->user_id).', is_default='.intval($default).', main='.intval($main));
			$id = $res->lastId();
			if($default){
				$GLOBALS['db']->query('update wishlist set is_default=0 where user_id='.$this->user_id.' and is_default>0 and id!='.$res->lastID());
			}
			return $id;			
		}		
		return 0;		
	}
	
	function deleteWishlist($id){
		/* delete list */
		$res = $GLOBALS['db']->query('delete from wishlist where user_id='.$this->user_id.' and id='.intval($id).' and main=0');
		$deleted = $res->affectedRowsCount();
		/* delete item */
		if($deleted){
			$GLOBALS['db']->query('delete from wishlist_items where wishlist_id='.intval($id));
			/* set new default */
			$this->default_list = 0;
			if(!$this->getDefaultList())
				$GLOBALS['db']->query('update wishlist set is_default=1 where user_id='.$this->user_id.' order by id limit 1');
		}
		return $deleted;
	}
	
	function clearWishlist($id){
		/* check if yours */
		$res = $GLOBALS['db']->query('select * from wishlist where user_id='.$this->user_id.' and id='.intval($id));
		if($res->getNext()){
			$GLOBALS['db']->query('delete from wishlist_items where wishlist_id='.$id);
		}
		return true;
	}
	
	function getDefaultList(){	
		if(!$this->default_list){
			$res = $GLOBALS['db']->query('select * from wishlist where user_id='.$this->user_id.' and is_default=1');
			if($wl_data = $res->getNext()){
				$this->default_list = $wl_data['id'];
			}
		}
		return $this->default_list;		
	}
	
	function setDefaultList($id){
		$GLOBALS['db']->query('update wishlist set is_default=0 where is_default=1 and user_id='.$this->user_id);
		$GLOBALS['db']->query('update wishlist set is_default=1 where user_id='.$this->user_id.' and id='.intval($id));		
		$this->default_list = $id;
	}
	
	function getListById($id){
		$res = $GLOBALS['db']->query('select * from wishlist where id='.intval($id).' and user_id='.$this->user_id);
		return $res->getNext();
	}
	
	function getWishlistStat($id, $with_text = false, $format_price = false){
		
		$data = array('items_num' => 0, 'sum' => 0, 'sum_no_discount' => 0, 'count_num' => 0);
		$db = $GLOBALS['db'];	
		
		/* get summary */
		
		$res = $db->query('
			select sum(items_num) as cnt, sum(if(V.price is not null and V.price > 0, V.price, C.price)*W.items_num) as sum 
			from (wishlist_items as W,catalog as C)
			left join catalog_item_variants as V on V.id=W.item_variant
			where C.id=W.item_id and W.wishlist_id='.intval($id)
		);
		$t = $res->getNext();
		
		$data['items_num'] = $t['cnt'];		
		
		$items = self::getWishlistItems($id);
		$data['discount'] = Catalog::getDiscount($t['sum'], null, $items);
		$data['discount']['text'] = '';
		if($data['discount']['perc']){
			$data['discount']['text'] = 'С учетом скидки '.$data['discount']['perc'].'%'.($data['discount']['value'] ? '('.$data['discount']['value'].' руб.)' : '');
		}elseif($data['count_num']){
			$data['discount']['text'] = $data['discount']['next_discount_text'];
		}
		
		$data['sum_no_discount'] = $format_price ? Site::formatPrice(round($t['sum'], 2)) : round($t['sum'], 2);
		$data['sum'] = $format_price ? Site::formatPrice(round($data['discount']['total_sum'],2)) : round($data['discount']['total_sum'], 2);		
		
		if($with_text){
			$data['items_num_text'] = $data['items_num'].' '.Common::makeEnding($data['items_num'], 'товар', '', 'а', 'ов');
			$data['text'] = self::getStatText($data['items_num'], $data['discount']['total_sum']);
		}
		
		return $data;
		
	}
	
	public static function getStatText($items_num, $total_sum){
		$str = '';
		if($items_num){
			$str = intval($items_num).' '.Common::makeEnding($items_num, 'товар', '', 'а', 'ов').' на сумму <span class="nowrap">'.Site::formatPrice($total_sum, false).' руб.</span>';
		}else{
			$str = 'нет товаров';
		}
		return $str;
	}
	
	function renameWishlist($id, $name){
		if(trim($name) != ''){
			$res = $GLOBALS['db']->query('update wishlist set name="'.mysql_escape_string(trim($name)).'" where user_id='.$this->user_id.' and id='.intval($id));
			return $res->affectedRowsCount();
		}
		return 0;
	}
	
	function setWishlistNote($id, $note){
		$res = $GLOBALS['db']->query('update wishlist set note="'.mysql_escape_string(trim($note)).'" where user_id='.$this->user_id.' and id='.intval($id));
		return $res->affectedRowsCount();
	}
	
	/**
	 * 
	 * ITEM FUNCTIONS
	 * 
	 */
	
	function getWishlistItems($id){		
		$res = $GLOBALS['db']->query('
			select 
				WL.id as entry_id,
				C.*,
				F.name as section_name,
				F.url as folder_url,
				WL.items_num,
				B.name as brand_name,
				C.price as price_real,
				IF(IV.price is not null and IV.price > 0, IV.price, C.price) as price,
				C.discount,
				IV.name as variant_name,
				WL.item_variant,
				IV.package as variant_package,
				(select id from catalog_photos as PH where PH.item_id=C.id order by pos limit 1) as photo_id,
				IV.lens_ax,
				IV.lens_bc,
				IV.lens_d,
				IV.lens_cyl,
				IV.article as variant_article,
				IV.name as variant_name,
				C_COLORS.id as lens_color_id,
				C_COLORS.name as lens_color_name,
				C_COLORS2.name as product_color_name
			from 
				(
					catalog as C,
					wishlist_items as WL,
					folders as F
				)
			left join 
				brands as B 
				on 
				B.id=C.brand
			left join 
				catalog_item_variants as IV 
				on 
				IV.id=WL.item_variant
			left join
				catalog_colors as C_COLORS
				on
				C_COLORS.id = IV.color
			left join
				catalog_colors as C_COLORS2
				on
				C_COLORS2.id = C.color
			where 
				C.folder_id=F.id 
				and 
				C.id=WL.item_id
				and 
				WL.wishlist_id='.intval($id).' 
			order by 
				WL.id
		');
		return $res->fetchArray();		
	}
	
	function addItem($item_id, $item_variant, $num, $wishlist_id){
		if(!$this->user_id)
			return false;
		
		$db = $GLOBALS['db'];
		if(intval($wishlist_id)){
			$res = $db->query('select * from wishlist where user_id='.$this->user_id.' and id='.$wishlist_id);
			if($wl_data = $res->getNext()){
				$res = $db->query('select * from wishlist_items where item_id='.intval($item_id).' and wishlist_id='.$wl_data['id'].' and item_variant='.$item_variant);
				$has = 0;
				if($tt = $res->getNext()){
					$has = $tt['id'];
					$num += $tt['items_num'];
				}
				if(!$has)
					$res = $db->query('insert into wishlist_items set item_id='.intval($item_id).', wishlist_id='.$wl_data['id'].',items_num='.($num).',item_variant='.$item_variant);
				else
					$res = $db->query('update wishlist_items set item_id='.intval($item_id).', wishlist_id='.$wl_data['id'].',items_num='.($num).',item_variant='.$item_variant.' where id='.$has);
				return $res->lastID();
			}
		}
		return false;
	}
	
	function changeItemsNum($wishlist, $item, $num){
		if(intval($num) && $num > 0){
			$res = $GLOBALS['db']->query('update wishlist as W,wishlist_items as I
		                                  set I.items_num='.intval($num).'
		                                  where W.id=I.wishlist_id and W.id='.intval($wishlist).' 
		                                  and W.user_id='.$this->user_id.' and I.id='.$item);
			return $res->affectedRowsCount();
		}
		return 0;
	}
	
	function deleteItem($item_id, $wishlist_id){
		$db = $GLOBALS['db'];
		$res = $db->query('select * from wishlist where user_id='.$this->user_id.' and id='.intval($wishlist_id));
		if($wl_data = $res->getNext()){
			$res = $db->query('delete from wishlist_items where wishlist_id='.$wishlist_id.' and id='.intval($item_id));
		}
	}
	
	function moveItem($item_id, $from_list, $to_list, $item_variant = 0){
		$db = $GLOBALS['db'];
		$res = $db->query('select * from wishlist where user_id='.$this->user_id.' and id in ('.intval($from_list).','.intval($to_list).')');
		if($res->selectedRowsCount() == 2){
			/* check if already exists */
			$res = $db->query('select * from wishlist_items where wishlist_id='.$to_list.' and item_id='.$item_id.' and item_variant='.intval($item_variant));
			if($res->getNext()){
				$db->query('update wishlist_items set items_num=items_num+1 where wishlist_id='.intval($to_list).' and item_id='.intval($item_id).' and item_id='.intval($item_variant));
				$db->query('delete from wishlist_items where item_id='.intval($item_id).' and wishlist_id='.$from_list.' and item_variant='.intval($item_variant));				
			}else{
				$db->query('update wishlist_items set wishlist_id='.intval($to_list).' where wishlist_id='.intval($from_list).' and item_id='.intval($item_id).' and item_variant='.intval($item_variant));
			}
		}
	}
	
	function isInList($item_id, $item_variant = 0){
		$res = $GLOBALS['db']->query('select M.*,I.items_num from wishlist_items as I,wishlist as M where M.id=I.wishlist_id and M.user_id='.$this->user_id.' and M.id='.$this->getDefaultList().' and I.item_id='.intval($item_id).' and I.item_variant='.intval($item_variant))->getNext();
		if($res){
			return $res['items_num'];
		}
		return 0;
	}
	
	function getTotalWeight($wl_id){
		global $db;
		$res = $db->query('select sum(weight*I.items_num) as sm from catalog as C,wishlist_items as I where C.id=I.item_id and I.wishlist_id='.intval($wl_id))->getNext();
		return str_replace(',','.',floatval($res['sm']));
	}
	
}

?>