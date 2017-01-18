<?

class Calendar{
	
	private $month;
	private $year;
	public static $monthes = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август','Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');
	
	function __construct($month, $year){
		$this->month = $month;
		$this->year = $year;
	}
	
	function getMonthSize(){
		$t = mktime(0, 0, 0, $this->month, 1, $this->year);
		return date('t', $t);
	}
	
	function getFirstDayNum(){
		return $this->getDayNum(1);
	}
	
	function getYear(){
		return $this->year;
	}
	
	function getMonth($text = false){
		$m = $this->month;
		if($text)
			return self::$monthes[$m-1];
		return $m;
	}	
	
	function getDayNum($day){
		$t = mktime(0, 0, 0, $this->month, $day, $this->year);
		$dn = date('w', $t);
		if($dn == 0)
			$dn = 7;
		return $dn;
	}
	
	function getPreviousDates(){
		return array();
	}
	
	function getNextMonth(){
		if($this->month == 12)
			return array('month' => 1, 'year' => $this->year+1);
		return array('month' => $this->month+1, 'year' => $this->year);		
	}
	
	function getPrevMonth(){
		if($this->month == 1)
			return array('month' => 12, 'year' => $this->year-1);
		return array('month' => $this->month-1, 'year' => $this->year);		
	}
	
}

?>