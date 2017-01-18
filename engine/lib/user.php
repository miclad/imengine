<?

class User extends AbsUser{
	
	private $cart_user_id = null;
	private $access = null;
	private $group_data = null;
	
	function __construct($id){
		
		$res = $GLOBALS['db']->query('select * from users where id='.intval($id));
		$this->user_data = $res->getNext();
		parent::__construct();		
	}
	
	function valid(){
		return $this->id > 0;
	}

	function isAdmin(){
		return $this->getField('is_admin') == 1;
	}
	
	function getType(){
		return $this->getField('level');
	}
	
	function setCartUserId($id){
		$this->cart_user_id = $id;
	}
	
	function getCartUserId(){
		global $db;
		if($this->cart_user_id == null){
			$this->cart_user_id = 0;
			if($this->getId()){
				$res = $db->query('select * from wishlist_users where user_id='.intval($this->getId()))->getNext();
				if($res){
					$this->cart_user_id = $res['id'];
				}
			}
		}
		return $this->cart_user_id;
	}
	
	function adminPanelAccess(){
		
		global $db;
		
		if($this->isAdmin())
			return true;
			
		if($this->group_data === null){
			if($this->getField('group_id'))
				$this->group_data = $db->query('select * from user_groups where id='.intval($this->getField('group_id')))->getNext();
			else
				$this->group_data = false;
		}
		
		if($this->group_data !== null){
			return $this->group_data['admin_panel_access'] > 0;
		}
		
		return false;
		
	}
	
	function hasAccess($target, $access = ''){
		
		global $db;
		
		if($this->isAdmin())
			return true;
			
		if(!$this->getField('group_id'))
			return false;
			
		if(!isset($this->access[$target])){
			
			$this->access[$target] = $db->query(
				'select * from user_groups_access_rules where target="'.mysql_escape_string($target).'" and group_id='.intval($this->getField('group_id'))
			)->getNext();
			
		}
		
		if(isset($this->access[$target]) && $this->access[$target] && $this->access[$target]['access'] != ''){
			if($access == '')
				return true;
			if(strpos($this->access[$target]['access'], $access) !== false){
				return true;
			}
		}
		
		return false;
		
	}
	
	function getById($id){
		
		global $db;
		
		$data = $db->query('select *,concat("/uploads/img/users_main/med", id, ".jpg") as image from users where id='.$id)->getNext();
		if($data){
			
			$data['photos'] = $db->query('
				select
					concat("/uploads/img/users/sq", id, ".jpg") as image_sq,
					concat("/uploads/img/users/med", id, ".jpg") as image_med,
					concat("/uploads/img/users/", id, ".", orig_ext) as image_big
				from
					users_photos
				where
					user_id='.$data['id'].'
				order by
					pos
			')->fetchArray();
			
		}
		
		return $data;
		
	}
	
}
	
?>