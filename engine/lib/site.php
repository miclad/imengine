<?
class Site{
	
	public static $monthes = array('январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь');

	static function formatDate($tsmp, $show_time = false, $show_year = true){
		$monthes = array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
		return '<span class="day">'.date('j',$tsmp).'</span> <span class="month">'.$monthes[date('m',$tsmp)-1].'</span>'.($show_year ? ' <span class="year">'.date('Y',$tsmp) : '').'</span>'.($show_time ? ', <span class="time">'.date('H:i', $tsmp).'</span>' : '');
	}
	
	static function formatDBDate($date, $show_time = true, $text_mode = true, $sep = ', ', $show_year = true){
		$arr1 = explode(' ',$date);
		$str = '';
		if(count($arr1) > 0 ){
			$arr2 = explode('-', $arr1[0]);
			if(count($arr2) == 3){
				$str = $text_mode ? Site::formatDate(mktime(0,0,0,$arr2[1],$arr2[2],$arr2[0]), false, $show_year && intval($arr2[0])) : $arr2[2].'.'.$arr2[1].'.'.$arr2[0];
				if(count($arr1) == 2 && $show_time){
					$time = explode(':', $arr1[1]);
					$str .= $sep.$time[0].':'.$time[1];
				}
			}
		}
		return $str;
	}
	
	static function renderDate($tsmp, $show_year = true){
		
		if(!is_numeric($tsmp)){
			if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/', $tsmp)){
				$date = explode('.', $tsmp);
				$tsmp = mktime(0, 0, 0, $date[1], $date[0], $date[2]);
			}elseif(preg_match('/^[0-9]{4}\-[0-9]\-[0-9]{2}$/', $tsmp)){
				$date = explode('-', $tsmp);
				$tsmp = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
			}
		}
		
		$date = date('Y-m-d', $tsmp);
		$tsmp_today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		if($tsmp){     
			
			$day = '';
			if($date == date('Y-m-d')){
				$day = 'сегодня';
			}elseif($date == date('Y-m-d', $tsmp_today + 86400)){
				$day = 'завтра';
			}elseif($date == date('Y-m-d', $tsmp_today + (86400*2))){
				//$day = 'послезавтра';
			}
			
			$d_names = array('воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота');
			
			$w = date('w', $tsmp);
			$day_name = isset($d_names[$w]) ? $d_names[$w] : '';
			
			return $day ? $day : Site::formatDate($tsmp, false, $show_year);
			
		}		
	}

	static function generatePassword(){
		$x = array(
			array('bcdfgjklmnprstvwxz'),
			array('aeiou123456789')
		);
		for ($pass = '', $i = 0; $i < 5; $i++)
			$pass .= substr($x[$i%2][0], round(rand(0, strlen($x[$i%2][0])-1)), 1);
		return $pass;
	}
	
	static function showDateForm($field,$start_year,$end_year,$show_hours = true,$selected = array()){
		$monthes_list = array("январь","февраль","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь");
		if(is_numeric($selected)){
			$t = array();
			$t["day"] = date("d",$selected);
			$t["month"] = date("m",$selected);
			$t["year"] = date("Y",$selected);
			$t["hour"] = date("H",$selected);
			$t["minute"] = date("i",$selected);
			$selected = $t;
		}
		$str = "";
		ob_start();
		require TPL_DIR."date.php";
		$str = ob_get_contents();
		ob_end_clean();
		return $str;
	}
	
	static function formatPrice($price, $symbol = ''){
		$val = number_format(round($price, 2), (floor($price) < $price ? 2 : 0), ',', ' ');
		if($symbol != ''){
			if(strpos($symbol, '#') > -1)
				$val = str_replace('#PRICE', $val, $symbol);
			else
				$val = $val.' '.$symbol;
		}
		return $val;
	}
	
	static function getFileExtension($file){
		return substr($file, strrpos($file, '.') + 1);
	}
	
	static function getFileSize($file){
		if(file_exists($file)){
			$fs = filesize($file);
			if($fs < 1024)
				return $fs." byte";
			if($fs < 1048576)
				return str_replace(".",",",round($fs/1024,1)." Kb");
			if($fs)
				return str_replace(".",",",round($fs/1048576,1)." Mb");
		}
		return 0;
	}
	
	static function getReferer(){
		if(isset($_SERVER["HTTP_REFERER"]) && str_replace('http://'.$_SERVER['SERVER_NAME'], '', $_SERVER["HTTP_REFERER"]) != $_SERVER['REQUEST_URI']){
			return preg_replace("/^(?i)http:\\/\\/".$_SERVER['SERVER_NAME']."(.*)$/", "\\1", $_SERVER["HTTP_REFERER"]);
			//return $_SERVER["HTTP_REFERER"];
		}else
			return '';
	}
	
	static function getRealReferer(){
		return isset($_POST['_redir_to']) ? str_replace('http://','',$_POST['_redir_to']) : Site::getReferer();
	}
	
	static function isAjaxRequest(){
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
	}
	
	static function isApp(){
		$hdr = getallheaders();
		return isset($hdr['FIXAM_APP']) && $hdr['FIXAM_APP'] == 1;
	}
	
	static function isCurrentPage($url, $extra = ''){
		
		$current_url_nc = self::trimUrl(Common::editUrl('all'));
		$compare_url_nc = self::trimUrl(Common::editQueryString($url, 'all'));
		
		return $current_url_nc == $compare_url_nc || $extra && self::trimUrl(Common::editQueryString($extra, 'all')) == $current_url_nc;
		
	}
	
	static function trimUrl($url){
		
		return preg_replace('/^\/+|\/+$/', '', $url); 
		
	}
	
	function redir($url){
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
		header('Pragma: no-cache');			
		Header('Location: '.$url, true, 307);
		exit();
	}

}
?>