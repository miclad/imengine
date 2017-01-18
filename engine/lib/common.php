<?

class Common{
	
	static function getPostFileData($field, $in_array = array()){
		if(!is_array($in_array))
			$in_array = array();
		if(!count($in_array)){
			if(isset($_FILES[$field]))
				return $_FILES[$field];
		}else{			
			$res = array();
			if(isset($_FILES[$in_array[0]])){
				$in_array[] = $field;
				foreach($_FILES[$in_array[0]] as $k => $v){
					$val = $v;
					for($i = 1; $i < count($in_array); $i++)
						$val = $val[$in_array[$i]];
					$res[$k] = $val;
				}
			}elseif(isset($_FILES[$field]) && count($in_array) == 1 && is_numeric($in_array[0])){
				
				foreach($_FILES[$field] as $k => $v){
					$res[$k] = $v[$in_array[0]];
				}
				
			}
			return $res;			
		}	
		return false;	
	}
	
	static function editQueryString($string, $remove_params = array(), $add_params = array()){
		$str = $string;
		/* remove params */		
		if(is_array($remove_params) && count($remove_params) || $remove_params == 'all'){
			if(!is_array($remove_params)){
				$remove_params = array_keys($_GET);
			}
			$q_pos = strpos($string, '?');
			if($q_pos !== false){
				
				$query_string = substr($string, $q_pos+1);				
				if($query_string != ''){
					$r_exp = array();
					foreach($remove_params as $v){
						$r_exp[] = '(^|&)'.$v.'(\[\])?(=[^&]*|&|$)';											
					}
					$r_exp_string = implode('|', $r_exp);	
					$string = substr($str, 0, $q_pos+1).preg_replace('/'.$r_exp_string.'/', '', urldecode($query_string));					
				}
				
			}
			
			if(substr($string, strlen($string)-1) == '&')
				$string = substr($string, 0, strlen($string)-1);
				
			if(substr($string, strlen($string)-1) == '?')
				$string = substr($string, 0, strlen($string)-1);
			
		}
		
		if(count($add_params)){
			$withget = true;
			if(strpos($string, '?') === false){
				$withget = false;
				$string .= '?';
			}
			$alr = false;
			foreach($add_params as $k=>$v){
				if($withget || $alr)
					$string .= '&';
				$string .= $k.'='.$v;
				$alr = true;
			}
		}
		
		// urlencode
		$t_string = $string;
		$string = '';
		preg_match('/^(.*?)\?(.*)$/', $t_string, $pock);
		if(count($pock) == 3){
			$string = $pock[1].'?';
			$expl = explode('&', $pock[2]);
			//var_dump($expl);
			foreach($expl as $k => $param){
				preg_match('/^(.*?)=(.*)$/', $param, $pock2);
				if($k > 0)
					$string .= '&';
				if(count($pock2) == 3){
					$string .= urlencode($pock2[1]).'='.urlencode($pock2[2]);
				}else{
					$string .= urlencode($param);
				}
			}
		}else{
			$string = $t_string;
		}
		
		if($string == '')
			$string = '/';
			
		return $string;
		
	}
	
	static function makeTimestampFromDB($date){
		
		$t = explode(' ', $date);
		$t_date = explode('-', $t[0]);
		$t_time = count($t) > 1 ? explode(':', $t[1]) : array(0,0);
		
		return mktime($t_time[0], $t_time[1], 0, $t_date[1], $t_date[2], $t_date[0]);
		
	}
	
	static function makeTimestampFromDate($date){
		
		if(strpos($date, '.') !== false){
			$expl = array_map('intval', explode('.', $date));
			if(count($expl) == 3)
				return self::makeTimestampFromDate(sprintf('%04d', $expl[2]).'-'.sprintf('%02d', $expl[1]).'-'.sprintf('%02d', $expl[0]));
		}else{
			return self::makeTimestampFromDB($date);
		}
		
		return 0;
		
	}
	
	static function editUrl($remove = array(), $add = array()){
		return Common::editQueryString($_SERVER['REQUEST_URI'], $remove, $add);
	}
	
	static function unlinkRecursive($dir){
        if( !$dh = @opendir($dir) ) {
            return;
        }
        while( false !== ($obj = readdir($dh)) ) {
            if( $obj == '.' || $obj == '..' ) {
                continue;
            }
            if( !@unlink($dir . '/' . $obj) ) {
                Common::unlinkRecursive($dir.'/'.$obj, true);
            }
        }
        closedir($dh);
        @rmdir($dir);
        return;
	}
	
	static function toUpperCase($string){
		return mb_convert_case($string, MB_CASE_UPPER, 'utf-8');
	}
	
	static function toLowerCase($string){
		return mb_convert_case($string, MB_CASE_LOWER, 'utf-8');
	}
	
	static function capitalize($string){
		return mb_convert_case($string, MB_CASE_TITLE, 'utf-8');
	}
	
	static function makeEnding($num, $word, $end1 = '', $end2 = '', $end3 = ''){
		$postfix = '';
		if($num%100 > 10 && $num%100 < 21 || $num%10 >= 5 && $num%10 <= 9 || $num%10 == 0){
			$postfix = $end3;
		}elseif($num%10 >= 2 && $num%10 <= 4){
			$postfix = $end2;
		}else{
			$postfix = $end1;
		}
		return $word.$postfix;
	}
	
	static function cropText($text, $length, $word = true, $post = '...'){
		
		$text = strip_tags($text);
		
		if($word){
			preg_match("/^(.{".$length."}.*?)(\s|,|\.)/u", $text, $pock);
			if(count($pock) > 1){
				$res = $pock[1];
				if(mb_strlen($res, 'utf-8') < mb_strlen($text, 'utf-8')){
					$res .= $post;
				}
				return $res;
			}
		}
		
		$t = mb_substr($text, 0, $length, 'utf-8');
		if(mb_strlen($t, 'utf-8') < mb_strlen($text, 'utf-8')){
			$t .= $post;
		}
		
		return trim($t);
		
	}
	
	static function detect_utf8($string){
	   return preg_match('%(?:
	      [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	      |\xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
	      |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
	      |\xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
	      |\xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
	      |[\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
	      |\xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
	      )+%xs',
	   $string);
	}
	
	static function prepareGetString($str){
		$str = urldecode($str);
		if(!Common::detect_utf8($str)){
			$str = mb_convert_encoding($str, 'utf-8', 'windows-1251');
		}
		return $str;
	}
	
	static function getDaysLeft($date){
		
		if($date == '')
			return false;
		
		$today = Common::makeTimestampFromDB(date('Y-m-d'));
		$t_date = Common::makeTimestampFromDB($date);
		
		$str = '';
		$dn = floor(($t_date-$today)/86400);
		if($dn == 0){
			$str = 'Истекает сегодня';
		}elseif($dn > 0){
			$str = Common::makeEnding($dn, 'Остал', 'ся', 'ось', 'ось').' '.$dn.' '.Common::makeEnding($dn, 'д', 'ень', 'ня', 'ней');
		}else{			
			$str = 'Опоздание '.(-1*$dn).' '.Common::makeEnding((-1*$dn), 'д', 'ень', 'ня', 'ней');
		}
		
		return array('str' => $str, 'days_num' => $dn);
		
	}
	
	static function stripslashes_rec($arr){
		if(is_array($arr))
			foreach($arr as $k=>$v)
				$arr[$k] = self::stripslashes_rec($v);
		else
			$arr = stripslashes($arr);
		return $arr;
	}
	
	static function striptags_rec($arr){
		if(is_array($arr))
			foreach($arr as $k=>$v)
				$arr[$k] = self::striptags_rec($v);
		else{
			$arr = strip_tags($arr);
		}
		return $arr;
	}
	
	static function arraySortByField($dataArray, $sortColumn, $sortOrder = SORT_DESC, $sortComparsion = SORT_NUMERIC){

	        $sortArray = array();
	        foreach ($dataArray as $subArray){
	            $sortArray[] = $subArray[$sortColumn];
	        }

	        array_multisort($sortArray, $sortOrder, $sortComparsion, $dataArray, $sortOrder, $sortComparsion);
	        return $dataArray;
	}
	
	static function removeRemoteLinks($text){
		/* get all links */
		preg_match_all("/https?:\/\/([^\s]*)/", $text, $pock, PREG_SET_ORDER);
		foreach($pock as $v){
			$url = $v[0];
			$local = str_replace('www.', '', $_SERVER['SERVER_NAME']);
			if(!preg_match("/https?:\/\/(www\.)?".$local.".*?/", $url)){
				$text = str_replace($url, '', $text);
			}
		}
		return $text;
	}
	
	static function makeLinks($text){
		return preg_replace("/https?:\/\/([^\s]*)/", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $text);
	}

}

?>