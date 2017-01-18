<?

class NaviPath{
	
	private static $path = array();
	
	function addItem($title, $url = ''){
		self::$path[] = array('title' => $title, 'url' => $url);
	}
	
	function getItems(){
		return self::$path;
	}
	
	function getHeadTitle($prefix = ''){
		$ttl = $prefix;
		foreach(self::$path as $v){
			if($ttl != '')
				$ttl .= ' - ';
			$ttl .= $v['title'];
		}		
		return strip_tags($ttl);
	}
	
	function getPageTitle(){
		return count(self::$path) ? self::$path[count(self::$path)-1]['title'] : '';
	}
	
	function getNavi($sep = ''){
		
		$str = '';
		foreach(self::$path as $k => $v){
			if($str != '')
				$str .= $sep;
			if($k < count(self::$path)-1)
				$str .= '<li><a href="'.$v['url'].'">'.$v['title'].'</a></li>';
			else 
				$str .= '<li class="active">'.$v['title'].'</li>';
		}
		
		return $str;
		
	}
	
	function changeItem($ind, $title, $url = null){
		self::$path[$ind]['title'] = $title;
		if(!is_null($url))
			self::$path[$ind]['url'] = $url;
	}
	
	function getLength(){
		return count(self::$path);
	}
	
	function clear(){
		self::$path = array();
	}
	
	static function isInPath($url){
		$urls = array();
		foreach(self::$path as $v){
			if($v['url'] == $url)
				return true;
		}
	}
	
}

?>