<?

class AuthUser extends User{
	
	private $db;
	private static $instance = null;
	
	function __construct(){
		
		if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
			//file_put_contents(B_DIR.'au.txt', print_r($_SERVER, true));
			Auth::login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
		}
		
		$this->user_data = Auth::authenticate();
		
		if($this->user_data)
			$this->id = $this->user_data['id'];
		
	}
	
	private function __clone(){		
	}
	
	/**
	 * Singleton
	 *
	 * @return AuthUser
	 */
	static function getInstance(){
		if(self::$instance == null)
			self::$instance = new self;
		return self::$instance;
	}	
	
	function reAuth(){
		$this->user_data = Auth::authenticate();
		if($this->user_data){
			$this->id = $this->user_data['id'];
		}
	}
	
	function isAuthorized(){
		return $this->id > 0;
	}
	
}

?>