<?

class City{
	
	private $id = 0;
	private $data = array();
	private static $instance = null;
	
	private function __construct(){
		
		global $db;
		
		if(isset($_GET['change_city']) && is_numeric($_GET['change_city'])){
			$this->data = $db->query('select * from cities where id='.intval($_GET['change_city']))->getNext();
			if($this->data){
				$_SESSION['city_data'] = $this->data;
				Header('Location: '.Common::editUrl(array('change_city'), array()));
				exit();
			}
		}elseif(isset($_SESSION['city_data'])){
			$this->data = $_SESSION['city_data'];
		}else{
			$this->data = $db->query('select * from cities where 1 order by is_default=0,name limit 1')->getNext();
		}
		
		if(isset($this->data['id'])){
			$this->id = $this->data['id'];
		}
		
	}
	
	/**
	 * returns current instance (singleton)
	 *
	 * @return city
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
		return $db->query('select * from cities where 1 order by pos=0,pos,name')->fetchArray();
	}
	
	function getName(){
		return isset($this->data['name']) ? $this->data['name'] : '';
	}
	
}

?>