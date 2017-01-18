<?

class Tags{
	
	private static $base_size = 11;
	
	function getTagSize($tag, $elements_num){
		
		$perc = $tag['cnt']/$elements_num*100;
		
		$increase = floor($perc/10);
		
		return self::$base_size + $increase;
		
	}
	
}

?>