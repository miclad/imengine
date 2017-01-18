<?

class Region{
	
	private $id = 0;
	private $data = array();
	private static $instance = null;
	
	private function __construct(){
		
		global $db;
		
		$is_auto_detected = false;
		
		if(isset($_GET['change_region']) && $_GET['change_region'] != ''){
			
			$_GET['change_region'] = urldecode($_GET['change_region']);
			
			// устанавливаеем вручную, сохраняя в сессии и куке
			
			$this->data = self::retrieveData($_GET['change_region'], true);
			if($this->data){
				if(AuthUser::getInstance()->isAuthorized())
					$db->query('update users set city="'.mysql_escape_string($this->data['name']).'" where id='.AuthUser::getInstance()->getId());
				$_SESSION['region_data'] = $this->data;
				setcookie('chosen_region', $this->data['code'], time() + 86400, '/', str_replace('www.', '.', $_SERVER['SERVER_NAME']));
				if(Site::isAjaxRequest()){
					echo json_encode($this->data);
				}else{
					Header('Location: '.Common::editUrl(array('change_region'), array()));
				}				
				exit();
			}
			
		}elseif(isset($_SESSION['region_data'])){
			
			// берем из сессии
			
			$this->data = $_SESSION['region_data'];
			
		}elseif(isset($_COOKIE['chosen_region'])){
			
			// берем из куки
			
			$this->data = self::retrieveData($_COOKIE['chosen_region']);
			
		}
		
		if(!$this->data){
			
			// определяем по ip
			
			$this->data = self::searchByIp();
			
			if(!$this->data){				
				// выбираем регион по умолчанию				
				$t = $db->query('select * from regions where 1 order by is_default=0,parent,pos limit 1')->getNext();
				if($t)
					$this->data = self::retrieveData($t['name']);
			}
			
			if($this->data)
				$is_auto_detected = true;
			
		}
		
		if($this->data && (!isset($_SESSION['region_data']) || !$_SESSION['region_data'])){
			$_SESSION['region_data'] = $this->data;
		}
		
		if($is_auto_detected && (!isset($_COOKIE['region_confirmed']) || $_COOKIE['region_confirmed'] != $this->data['code']) || !$this->data){
			if(isset($_COOKIE['region_confirmed'])){
				setcookie('region_confirmed', '', time() - 86400, '/', str_replace('www.', '.', $_SERVER['SERVER_NAME']));
				unset($_COOKIE['region_confirmed']);
			}
		}elseif(!isset($_COOKIE['region_confirmed'])){
			if(true){
				setcookie('region_confirmed', $this->data['code'], time() + 86400, '/', str_replace('www.', '.', $_SERVER['SERVER_NAME']));
				$_COOKIE['region_confirmed'] = $this->data['code'];
			}
		}
		
		if(isset($this->data['id'])){
			$this->id = $this->data['id'];
		}
		
	}
	
	/**
	 * returns current instance (singleton)
	 *
	 * @return Region
	 */
	static function getInstance(){
		if(self::$instance == null)
			self::$instance = new self;
		return self::$instance;
	}
	
	function getId(){
		return intval($this->id);
	}
	
	static function getList(){
		global $db;
		$tree = new Tree('regions');
		return $tree->getFullTree();
	}
	
	function getName(){
		return isset($this->data['name']) ? $this->data['name'] : '';
	}
	
	function getRegionName(){
		return isset($this->data['region']) ? $this->data['region'] : '';
	}
	
	function getCode(){
		return isset($this->data['code']) ? $this->data['code'] : '';
	}
	
	function getParent(){
		return isset($this->data['parent']) ? $this->data['parent'] : 0;
	}
	
	function searchByIp(){
		
		global $db;
		
		$data = false;
		
		$region = GeoIP::detect();
		//var_dump($region);
		if($region){
				
			$res = false;
				
			// ищем город
			if($region['city']){
				//$res = $db->query('select * from regions where name="'.mysql_escape_string($region['city']).'"')->getNext();
				$res = self::retrieveData($region['city']);
			}
				
			// ищем регион
			if(!$res && $region['region']){
				//$res = $db->query('select * from regions where name="'.mysql_escape_string($region['region']).'"')->getNext();
				$res = self::retrieveData($region['region']);
			}
				
			if($res){	
				$data = $res;
			}
				
		}
		
		return $data;
		
	}
	
	function needConfirm(){
		return !isset($_COOKIE['region_confirmed']) || !$_COOKIE['region_confirmed'];
	}
	
	function retrieveData($name, $get_region = false){
		
		global $db;
		
		$data = false;
		
		if($name != ''){
			
			$code_mode = preg_match('/^[0-9]+$/', $name);

			$q = $code_mode ? 'CODE="'.$name.'"' : 'NAME="'.mysql_escape_string($name).'" and SOCR="г"';
			$t = $db->query('select * from kladr_cities where '.$q)->getNext();
			if($t){
				$data = array('name' => $t['NAME'], 'code' => $t['CODE'], 'region' => '');
				if($get_region){
					$reg = Kladr::getCityRegion($t['CODE']);
					if($reg)
						$data['region'] = $reg['NAME'].($reg['SOCR'] != 'г' ? ' '.mb_strtolower($reg['SOCR'], 'utf-8').'.' : '');
				}
			}
		
			/*
			$data = $db->query('select * from regions where name="'.mysql_escape_string($name).'"')->getNext();
			if(!$data){
				$t = $db->query('select * from kladr_cities where NAME="'.mysql_escape_string($name).'" and socr="г"')->getNext();
				if($t){
					$data = array('name' => $t['NAME']);
				}
			}
			*/
		
		}
		
		return $data;
		
	}
	
	function isMsk(){
		
		$reg = Kladr::getCityRegion($this->getCode());
		return $this->getName() == 'Москва' || $reg && ($reg['NAME'] == 'Московская' || $reg['NAME'] == 'Москва');
		
	}
	
}

?>