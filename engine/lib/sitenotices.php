<?

class SiteNotices{
	
	private static $list = array('success' => null, 'error' => null);
	
	function addNotice($notice, $type = 'success'){
		$_SESSION['site_notices'][$type][] = $notice;
	}
	
	function getList($type = 'success'){
		if(self::$list[$type] == null){
			if(isset($_SESSION['site_notices'][$type])){				
				self::$list[$type] = implode('<br>', $_SESSION['site_notices'][$type]);
				unset($_SESSION['site_notices'][$type]);
			}
		}
		return self::$list[$type];
	}
	
}

?>