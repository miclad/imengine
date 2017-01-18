<?

class SiteLang{
	
	private $langData = null;
	private $isDefault = false;
	private static $init_by = 'domain'; // domain | prefix
	private static $lang_list = null;
	
	private static $instance = null;
	
	private function __construct(){
		if(self::$init_by == 'prefix'){
			// $_GET['q'] - user request
			if(isset($_GET['q'])){
				$t = explode('/', $_GET['q']);
				if(count($t) > 0){
					foreach(SiteLang::getLangs() as $l){
						if($t[0] == $l['name']){
							$this->langData = $l;
							array_shift($t);
							$_GET['q'] = implode('/', $t);
							break;
						}
					}
				}
				if($this->langData == null){
					$t = SiteLang::getLangs();
					if(count($t))
						$this->langData = $t[0];
				}
			}
		}else{
			foreach(SiteLang::getLangs() as $l){
				if(strpos($_SERVER['SERVER_NAME'], $l['domain']) !== false){
					$this->langData = $l;
					break;
				}
			}
		}
		
	}
	
	private function __clone(){
		
	}
	
	function getInstance(){
		if(self::$instance == null)
			self::$instance = new self;
		return self::$instance;
	}
	
	private function getField($index){
		if(isset($this->langData[$index]))
			return $this->langData[$index];
		return '';
	}
	
	function getName(){
		return $this->getField('name');
	}
	
	function getTitle(){
		return $this->getField('title');
	}
	
	function getId(){
		return $this->getField('id');
	}
	
	function getIcon(){
		return $this->getField('icon');
	}
	
	function getUrlPrefix(){
		if(self::$init_by == 'prefix' && $this->getId())
			return '/'.$this->getName();
		return '';
	}
	
	function isDomainMode(){
		return self::$init_by == 'domain';
	}

	static function getLangs(){
		if(self::$lang_list == null){
			$res = $GLOBALS['db']->query('select * from langs where 1 order by pos');
			self::$lang_list = $res->fetchArray();		
		}
		return self::$lang_list;
	}
	
}

?>