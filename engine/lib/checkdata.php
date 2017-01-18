<?
class CheckData{
	
	function checkString($string, $datatype){
		$string = trim($string);
		if($string == "")
			return 1;
			
		switch($datatype){
			case "number":
				if(!is_numeric($string))
					return 2;
			break;
			case 'item_url':
				if(false && !preg_match("/^[A-z0-9_.;-]+$/",$string))
					return 2;
			break;
			case "mail":
				if(!preg_match("/(?i)^[a-zA-Z0-9._-]+@(?:[a-zA-z0-9-]+\.)+[a-zA-Z]{1,10}$/",$string))
					return 2;
			break;
			case "login":
				if(!preg_match("/^[A-z0-9_-]{2,30}$/",$string))
					return 2;
			break;
			case "password":
				if(!preg_match("/^[A-z0-9_-]{2,30}$/",$string))
					return 2;
			break;
			case "phone":
				if(!preg_match("/(?i)^\+?[0-9]?\s?\(?[0-9]{3}\)?\s?[0-9]{3}\-?[0-9]{2}\-?[0-9]{2}/",$string))
					return 2;
			break;
			case "url":
				if(!preg_match("/^(https?\:\\\\)?[A-zА-я0-9.,;:\/=)(&-]+$/u", $string))
					return 2;
			break;
			case "time":
				if(!preg_match("/^[0-9]{1,2}\:[0-9]{2}(\:[0-9]{2})?$/", $string))
					return 2;
			break;
		}
		
		return 0;
	}
	
	
	function checkFile($file_data, $datadesc, $autoname = false){
		if(!$file_data || !file_exists($file_data["tmp_name"]))
			return 1;
			
		switch($datadesc[1]){
			case "image":
				if(!preg_match("/(?i).+?\.(jpg|jpeg|gif|png|bmp|swf)/",$file_data["name"]))
					return 4;
			break;
			case "doc":
				if(preg_match("/(?i).+?\.(php|php3|php4|php5|cpp|c|asm|js)/",$file_data["name"]))
					return 4;
			break;
		}

		if(!$autoname && !preg_match("/[A-z0-9_.]+/",$file_data["name"]))
			return 3;
		
		if(!$autoname && file_exists(B_DIR.$datadesc[2].$file_data["name"])){
			return 5;
		}
			
		return 0;
	}
	
	function checkDate($date){
		$arr = array("day","month","year","hour","minute");
		foreach($arr as $v)
			if(!isset($date[$v]) || !is_numeric($date[$v])){
				echo $v;
				return false;
			}
		return true;
	}
	
	function trimPhone($phone){
		return preg_replace('/[^\d]/', '', $phone);
	}
	
}
?>