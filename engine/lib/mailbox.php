<?

class  Mailbox{
	
	var $user_id;
	var $db;
	var $limit;
	public static $new_messages_num = null;

	function Mailbox($user_id){
		$this->user_id = intval ($user_id);
		$this->db = $GLOBALS['db'];
		$this->limit = 10;
	}

	function getList($type, $user_id = 0){
		
		if($type == 'in')
			$where = 'del_to = "no" and id_usr_to = '.$this->user_id;
		elseif($type == 'out')
			$where = 'del_from = "no" and id_usr_from = '.$this->user_id;
		
		if($user_id){
			$user_id = intval($user_id);
			$where = 'id_usr_to='.$user_id.' and id_usr_from='.$this->user_id.' and del_from="no"';
			$where .= ' or id_usr_to='.$this->user_id.' and id_usr_from='.$user_id.' and del_to="no"';
		}
			
		$order = 'date_send desc';
		$res = $this->db->query('select * from mailbox where '.$where.' order by '.$order);
		return $res;
		
	}

	function getNewMessagesNum() {
		if(Mailbox::$new_messages_num == null){
			$res = $this->db->query("SELECT COUNT(id) AS  msgCount FROM mailbox WHERE id_usr_to=".$this->user_id." AND date_read IS NULL AND del_to='no'");
			$t = $res->getNext();
			Mailbox::$new_messages_num = $t['msgCount'];
		}
		return Mailbox::$new_messages_num;
	 	
	}

	function sendMessage($id_usr_to, $message, $subject = "...", $reply_to = 0) {
		if($subject == '')
			$subject = '...';
		$subject = htmlspecialchars($subject);
		$message = trim(htmlspecialchars($message));
		if($message != ''){
			$res = $this->db->query("INSERT INTO mailbox SET id_msg_answer=".intval($reply_to).", id_usr_to=".$id_usr_to.", subject='".mysql_escape_string($subject)."', date_send=NOW(), id_usr_from=".$this->user_id.", message='".mysql_escape_string($message)."'");
			return intval($res->LastID());
		}
		return 0;
	}

	function readMessage($id) {
		$id=intval($id);
		$res = $this->db->query("SELECT * FROM mailbox WHERE (id_usr_to=".$this->user_id." OR id_usr_from=".$this->user_id.") AND id = ".intval($id));
		$val = $res->getNext();
		if($val){			
			$val['inbox'] = ($val['id_usr_to'] == $this->user_id);
			$val['outbox'] = ($val['id_usr_from']==$this->user_id);
			if($val['inbox'] && $val['del_to'] == 'yes' || $val['outbox'] && $val['del_from'] == 'yes')
				return false;
			$val['message'] = nl2br($val['message']);
			if($val['inbox'] && !$val['date_read']) 
				$this->db->query("UPDATE mailbox SET date_read=NOW() WHERE id_usr_to=".$this->user_id." AND id = $id");
		}
	 	return $val;
	}
	
	function deleteMessage($id){
		
		$t = $this->readMessage($id);
		if($t){
			$field = $t['inbox'] ? 'del_to' : 'del_from';
			$res = $this->db->query('update mailbox set '.$field.' = "yes" where (id_usr_from='.$this->user_id.' or id_usr_to='.$this->user_id.') and id='.intval($id));
			return $res->affectedRowsCount();
		}
		return false;
		
	}
	
	function prepareAnswerSubject($subject){
		$renum = preg_match_all("/(?i)Re(\([0-9]+\))?\:/", $subject, $out);
		$message_subject = 'Re'.($renum ? '('.($renum+1).')' : '').': ';
		$subj = trim(preg_replace("/(?i)Re(\([0-9]+\))?\:/", '', $subject));
		if($subj == '')
			$subj = '...';
		$message_subject .= $subj;
		return $message_subject;
	}

}
?>