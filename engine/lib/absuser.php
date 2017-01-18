<?

abstract class AbsUser{
	
	protected $type = '';
	public $user_data = array();
	protected $id = 0;
	
	function __construct($ignore_not_found = false, $is_auth = false){
		if($this->user_data){
			if(!$is_auth && isset($this->user_data['deleted']) && $this->user_data['deleted'] && !$ignore_not_found){
				throw new Exception('Страница удалена');
			}
			$this->id = $this->user_data['id'];
		}elseif(!$ignore_not_found){
			throw new Exception('Пользователь не найден');
		}
	}
	
	function getEType(){
		return $this->type;
	}
	
	function getField($f){
		return isset($this->user_data[$f]) ? $this->user_data[$f] : '';
	}
	
	function setField($field, $val){
		$this->user_data[$field] = $val;
	}
	
	function getId(){
		return intval($this->id);
	}
	
	function isEditor(){
		
	}
	
	function getBasePage(){
		if($this->type == 'user')
			return '/profile/';
		else
			return '/groups/';
	}
	
	function getIdParam(){
		if($this->type == 'user')
			return 'uid';
		else
			return 'gid';
	}
	
}

?>