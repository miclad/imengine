<?

class Forum{
	
	function getSections(){		
		global $db;		
		return $db->query('select * from forum_sections order by pos');
	}
	
	function getSectionById($id){
		global $db;
		return $db->query('select * from forum_sections where id='.intval($id))->getNext();
	}
	
	function getTopics($section_id){
		global $db;
		return $db->query('select * from forum_topics where section_id='.intval($section_id).' order by created_date desc');		
	}
	
	function getTopicById($id){
		global $db;
		return $db->query(
			'select T.*,S.name as section_name from forum_topics as T,forum_sections as S
			 where T.id='.intval($id).' and T.section_id=S.id'
		)->getNext();
	}
	
	function createTopic($user_id, $user_name, $section_id, $name, $message = ''){		
		global $db;
		$iid = 0;
		if(trim($name) != ''){
			$res = $db->query('insert into forum_topics set 
			            section_id='.intval($section_id).',name="'.mysql_escape_string($name).'",created_by='.intval($user_id));
			$iid = $res->lastID();
			if($iid){
				$db->query('update forum_sections set topics_num=topics_num+1 where id='.intval($section_id));
				if($message != '')
					Forum::addMessage($user_id, $user_name, $iid, $message);	
			}			
		}
		return $iid;		
	}
	
	function addMessage($user_id, $user_name, $topic_id, $message){		
		global $db;
		$message = trim(Common::makeLinks(Common::removeRemoteLinks($message)));
		if(trim($message) != ''){
			$res = $db->query('insert into forum_messages set user_id='.intval($user_id).',name="'.mysql_escape_string($user_name).'",topic_id='.intval($topic_id).',message="'.mysql_escape_string($message).'",from_admin='.intval(AuthUser::getInstance()->isAdmin()));
			$iid = $res->lastID();
			if($iid){
				if(isset($_FILES['pict']) && file_exists($_FILES['pict']['tmp_name'])){
					$inf = pathinfo($_FILES['pict']['name']);
					if(in_array(strtolower($inf['extension']), array('jpg','jpeg','gif','png'))){
						$rn = $_FILES['pict']['tmp_name'].'.'.$inf['extension'];
						rename($_FILES['pict']['tmp_name'], $rn);
						Images::imageResizeUnified($rn, 600, 600, B_DIR.'img/forum/s'.$iid.'.jpg');
						$db->query('update forum_messages set with_image=1 where id='.$iid);
					}
				}
				$db->query('update forum_topics set messages_num=messages_num+1 where id='.intval($topic_id));
			}
		}
	}
	
	function getMessages($topic_id){
		global $db;
		return $db->query(
			'select M.*,U.name as user_name
			from forum_messages as M
			left join users as U on M.user_id=U.id
			where M.topic_id='.intval($topic_id).' order by M.date desc'
		);		
	}
	
	function getLastMessage($topic_id){
		global $db;
		return $db->query(
			'select M.date,M.user_id,M.name as user_name
			 from forum_messages as M
			 where M.topic_id='.intval($topic_id).'
			 order by date desc limit 1'
		)->getNext();
	}
	
}

?>