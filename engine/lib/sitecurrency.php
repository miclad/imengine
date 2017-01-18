<?

class SiteCurrency{
	
	private $currency_data = null;
	private static $instance = null;
	
	private function __construct(){
		if(isset($_SESSION['site_currency']) && PAGE::getInstance()->getUrl() == '/dealer-room')
			$cur = $_SESSION['site_currency'];
		else
			$cur = self::getDefaultCurrency();
			
		$res = $GLOBALS['db']->query('select * from currency where id='.$cur);
		$this->currency_data = $res->getNext();
	}
	
	private function __clone(){
		
	}
	
	/**
	 * Singleton
	 *
	 * @return SiteCurrency
	 */
	static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	function setCurrency($id){
		$res = $GLOBALS['db']->query('select * from currency where id='.intval($id));
		$t = $res->getNext();		
		if($t){
			$this->currency_data = $t;
			$_SESSION['site_currency'] = $this->currency_data['id'];
		}
	}
	
	function getCourse(){
		if($this->currency_data && $this->currency_data['course'] > 0){
			return $this->currency_data['course'];
		}
		return 1;
	}
	
	function getId(){
		if($this->currency_data){
			return $this->currency_data['id'];
		}
		return 0;
	}
	
	function getSymbol(){
		if($this->currency_data){
			return $this->currency_data['symbol'];
		}
		return '';
	}
	
	private function getDefaultCurrency(){
		$res = $GLOBALS['db']->query('select * from currency where 1 order by pos limit 1');
		if($t = $res->getNext())
			return $t['id'];
		return 0;
	}
	
	function getDecSeparator(){
		if($this->currency_data && $this->currency_data['dec_separator']){
			return $this->currency_data['dec_separator'];
		}
		return ',';
	}
	
	static function getList(){
		global $db;
		return $db->query('select * from currency where 1 order by pos')->fetchArray();
	}
	
}

?>