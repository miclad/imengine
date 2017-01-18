<?

class Friends{
	
	var $pager;
	var $buddies_num;
	var $buddies_num_extra;
	var $user_id;
	var $db;
	var $pagerURL = '';
	var $errors = array();
	
	function Friends($user_id){
		$this->user_id = intval($user_id);
		$this->db = &$GLOBALS['db'];		
	}
	
	/*
	getList
	*/
	function getList($confirmed = 1, $order = '', $only_id = false, $limit = 0){
		
		$friends = array();
		
		$tables = 'friends as F,users as U';
		$fields = 'U.*,F.id_usr0,F.id_usr1,F.message';
		if($confirmed == 1)
			$where = '(id_usr0 = '.$this->user_id.' or id_usr1 = '.$this->user_id.') and ';
		else 
			$where = 'id_usr1='.$this->user_id.' and ';
		$where .= 'tsmp_confirm '.($confirmed ? ' is not null' : ' is null');
		if($order == '')
			if($confirmed)
				$order = 'tsmp_confirm';
			else
				$order = 'tsmp_entry';
				
		$where .= ' and (F.id_usr0 != '.$this->user_id.' and U.id=F.id_usr0 or F.id_usr1 != '.$this->user_id.' and U.id=F.id_usr1)';
		
		$res = $this->db->query('select '.$fields.' from '.$tables.' where '.$where.' order by '.$order.($limit ? ' limit '.$limit : ''));
		//echo 'select '.$fields.' from '.$tables.' where '.$where.' order by '.$order;
		if($only_id){
			$friends = array();
			while($t = $res->getNext()){
				$fid = $t['id_usr0'] == $this->user_id ? $t['id_usr1'] : $t['id_usr0'];
				$friends[] = $fid;
			}
			return $friends;	
		}
		return $res;	

	}
	
	function getFriendsNum($new = false){
		
		if($new){
			$res = $this->db->query('select count(*) as cnt from friends where id_usr1='.$this->user_id.' and tsmp_confirm is null');
			if($t = $res->getNext()){
				return $t['cnt'];
			}
		}else{
			$res = $this->db->query('select count(*) as cnt from friends where tsmp_confirm is not null and (id_usr0='.$this->user_id.' or id_usr1='.$this->user_id.')');
			if($t = $res->getNext()){
				return $t['cnt'];
			}
		}
		return 0;
		
	}
	
	/*
		returns int
		Check if the user already in your buddylist
		results:
		-2 - no connection between users
		-1 - user invited you but you haven't confirmed the request yet
		 0 - you invited user but user haven't confirmed your request yet
		 1 - the user is your buddy
	*/
	function isFriend($cid){
		
		$val = -2;
		$res = $this->db->query("SELECT id_usr0, id_usr1, unix_timestamp(tsmp_confirm) as cnf
		                         FROM friends
		                         WHERE id_usr0=".$cid." and id_usr1=".$this->user_id." or id_usr0=".$this->user_id." and id_usr1=".$cid);
		$t=$res->getNext();
		if($t){
			if($t["cnf"])
				$val = 1;
			else
				if($t['id_usr0'] == $this->user_id)
					$val = 0;
				else
					$val = -1;
		}
		return $val;
			
	}
	
	/* check if it's me */
	function isMe($cid) {
		if ($cid == $this->user_id)
			return true;
		return false;
	}
	
	/*
	invite buddy
	*/
	function inviteFriend($cid, $msg){
							
		$msg = nl2br(htmlspecialchars(trim($msg)));
		$res = $this->db->query("INSERT INTO friends 
		                         SET id_usr0 = ".$this->user_id.",
		                         id_usr1 = ".$cid.",
		                         tsmp_entry = NOW(),
		                         tsmp_confirm = null, 
		                         message = '".mysql_escape_string($msg)."'");
					
	}	
	
	/*
	confirm invitation
	*/
	function confirmFriendship($cid){
		
		$res = $this->db->query("UPDATE friends 
		                         SET tsmp_confirm = NOW(),
		                         message = NULL 
		                         WHERE id_usr0=".$cid." and id_usr1=".$this->user_id." and tsmp_confirm is null");
		
		return $res->affectedRowsCount();
		                              	
	}
	
	/*
	delete user from buddylist
	*/
	function removeFriend($cid){
		
		$res = $this->db->query("DELETE FROM friends
		                         WHERE id_usr0 IN(".$cid.",".$this->user_id.")
		                         AND id_usr1 IN(".$cid.",".$this->user_id.")");
		return $res->affectedRowsCount();
		                         	
	}
	
	
}

?>