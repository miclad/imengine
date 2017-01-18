<?

class LangContent{
	
	private static $instance = null;
	private $phrases = array();
	
	private function __construct(){
		
		$ln = SiteLang::getInstance()->getName();
		$fn = B_DIR.'engine/langs/'.$ln.'.php';
		if(file_exists($fn)){
			require $fn;
			if(isset($phrases))
				$this->phrases = $phrases;
		}
		
	}
	
	function getInstance(){
		if(self::$instance == null)
			self::$instance = new self;
		return self::$instance;
	}
	
	private function __clone(){}
	
	public function getString($name, $params = null){
		
		if(isset($this->phrases[$name])){
			if($params != null && !is_array($params))
				$params[] = $params;
			if($params && count($params))
				return sprintf($this->phrases[$name], $params);
			return $this->phrases[$name];
		}
		return $name;
		
	}
	
}

?>