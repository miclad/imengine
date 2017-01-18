<?

class Bonus{
	
	private static $instance = null;
	private $bonus_percent = 0;
	private $rules = array();
	private $color = '';
	
	private function __clone(){}
	
	private function __construct(){
		
		global $db;
		
		$this->rules = $db->query('select * from bonus_values where 1 order by order_sum')->fetchArray();
		
	}
	
	/**
	 * returns current instance (singleton)
	 *
	 * @return Bonus
	 */
	static function getInstance(){
		if(self::$instance == null)
			self::$instance = new self;
		return self::$instance;
	}
	
	function calculatePercent($sum){
		foreach($this->rules as $v){
			if($v['order_sum'] <= $sum){
				$this->bonus_percent = intval($v['bonus_value']);
				$this->color = $v['color'];
			}
		}
	}
	
	function getPercent(){
		return $this->bonus_percent;
	}
	
	function getValue($sum){
		if(intval($sum) > -1)
			$this->calculatePercent($sum);
		return round($sum*$this->getPercent()/100);
	}
	
	function getColor(){
		return $this->color;
	}
	
	
}


?>