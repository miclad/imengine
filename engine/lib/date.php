<?php
  
class Date{
	
	public static $monthes = array('январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь');
	public static $monthes_2 = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
	
	/**
	* Отображение даты в красивом виде
	* 
	* @param mixed $date
	* @param mixed $shorten
	*/
	
	static function render($date, $shorten = false, $show_year = true, $time_sep = ' '){
		
		$obj = self::createDTObject($date);
		
		$result = '';
		
		if($obj){
			
			if($shorten && $obj->format('Y-m-d') == date('Y-m-d')){
				$result = $obj->format('H:i');
			}else{
				
				$month_name = self::$monthes_2[$obj->format('n')-1];
				if(true || $shorten)
					$month_name = mb_substr($month_name, 0, 3, 'utf8');
				$year = $obj->format('Y');
				
				$result = $obj->format('j').' '.$month_name.($show_year && (!$shorten || date('Y') != $year) ? ' '.$year : '');
				$result .= $time_sep;
				$result .= $obj->format('H:i');
						
			}
			
		}
		
		return $result;
		
	}
	
	static function onlyDate($date){
		
		$obj = self::createDTObject($date);
		$result = '';
		if($obj){
			$month_name = self::$monthes_2[$obj->format('n')-1];
			$year = $obj->format('Y');
			$result = $obj->format('j').' '.$month_name.' '.$year;
		}
		
		return $result;
		
	}
	
	/**
	* Определяет формат даты
	* 
	* @param mixed $date
	* @return string
	*/
	
	static function detectFormat($date){
		
		if(strlen($date) == 10)
			return 'Y-m-d';
		else
			return 'Y-m-d H:i:s';
		
	}
	
	/**
	* Создает объект DateTime из строки с датой
	* 
	* @param mixed $date
	* @return DateTime
	*/
	
	static function createDTObject($date){
		
		$obj = false;
		
		$format = self::detectFormat($date);
		if($format){
			$obj = function_exists('date_create_from_format') ? DateTime::createFromFormat($format, $date) : new DateTime($date);
		}
		
		return $obj;
		
	}
	
}  
  
?>
