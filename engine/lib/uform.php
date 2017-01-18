<?
class UForm{
	
	var $values;
	var $formDesc;
	var $formInfo;
	var $errors;
	var $sent = false;
	var $url;
	var $table;
	var $initVals = array();
	var $useCaptcha;
	var $captchaError = false;
	
	function UForm($desc, $table, $url, $initVals = array(), $okmes = "Ваше сообщение отправлено", $usecap = true, $from_db = false){
		
		/* adjust form description taen from db */
		if($from_db){
			foreach($desc as $k=>$v){
				$desc[$k]['title'] = $v['name'];
				$desc[$k]['name'] = 'field'.$v['id'];
				$desc[$k]['input_type'] = $v['fieldtype'];
				if($v['fieldtype'] == 'select'){
					$desc[$k]['values'] = array();
					if($v['list_id']){
						$desc[$k]['values'] = $GLOBALS['db']->query('select title as id, title as name from lists_elements where list_id='.intval($v['list_id']).' order by pos')->fetchArray();
					}
				}
			}
		}
		
		$this->table = $table;
		$this->formDesc = $desc;			
		$this->initVals = $initVals;
		$this->errors = array();
		$this->url = $url;
		$this->okMessage = $okmes;
		$this->useCaptcha = $usecap;
		if(isset($_GET["sent"]))
			$this->sent = true;
	}
	
	function setValues($arr){
		$this->values = $arr;
	}
	
	function setValue($index, $value){
		if(isset($this->initVals[$index]))
			$this->initVals[$index] = $value;
		else
			$this->values[$index] = $value;
	}
	
	function read(){
		foreach($this->formDesc as $v){			
			if(isset($v['fieldtype']) && $v['fieldtype'] == 'file'){				
				if(file_exists($_FILES[$v['name']]['tmp_name'])){					
					$this->file_attach['orig_name'] = basename($_FILES[$v['name']]['name']);
					$this->file_attach['path'] = $_FILES[$v['name']]['tmp_name'];
					$res = 0;
				}				
			}else{
				if(!isset($_POST[$v["name"]]))
					$res = 1;
				else{
					$res = CheckData::checkString($_POST[$v["name"]],$v["datatype"]);
					if($res==0){
						if(isset($v["unique"])){
							$eq = "1";
							$tbl = $this->table;
							if(is_array($v["unique"]))
								$eq = $v["unique"][1];
							elseif(!is_numeric($v['unique'])){
								$tbl = $v['unique'];
							}
							$t = $GLOBALS["db"]->query("select id from ".$tbl." where ".$v["name"]."='".$_POST[$v["name"]]."' and ".$eq);
							if($t->getNext())
								$res = 555;
						}
						if($v["datatype"]=="password"&&$_POST[$v["name"]]!=$_POST[$v["name"]."_repeat"]){
							$res = 666;
						}
						if($v['datatype'] == 'number'){
							$val = intval($_POST[$v['name']]);
							if(isset($v['min_value']) && $v['min_value'] > $val)
								$val = $v['min_value'];
							elseif(isset($v['max_value']) && $v['max_value'] < $val)
								$val = $v['max_value'];
							$_POST[$v['name']] = $val;
						}elseif($v['datatype'] == 'text'){
							if(isset($v['maxlength']) && strlen($_POST[$v['name']]) > $v['maxlength']){
								$_POST[$v['name']] = substr($_POST[$v['name']], 0, $v['maxlength']);
							}
							/*
							if(false && (!isset($v['allow_html']) || !$v['allow_html']))
								$_POST[$v['name']] = strip_tags($_POST[$v['name']]);
							*/
						}elseif($v['datatype'] == 'phone'){
							$_POST[$v['name']] = preg_replace('/[^\d]/', '', $_POST[$v['name']]);
						}
					}					
				}
			}
			
			if($res == 0){
				if($v['datatype'] != 'file'){
					$this->values[$v["name"]] = $v["datatype"]=="password" ? md5($_POST[$v["name"]]) : trim(htmlspecialchars($_POST[$v["name"]]));
				}
			}elseif($v["req"] == 1){
				if(isset($v['errors']['error'.$res]))
					$this->errors[] = $v['errors']['error'.$res];
				else{
					switch($res){
						case "1":
							$this->errors[] = "Поле \"".$v["title"]."\" обязательно для заполнения";
						break;
						case "2":
							$this->errors[] = "Некорректные данные в поле \"".$v["title"]."\"";
						break;
						case "555":
							$this->errors[] = "Введенное значение поля \"".$v["title"]."\" уже используется в базе";
						break;
						case "666":
							$this->errors[] = "Неверное подтверждение пароля";
						break;
					}
				}
			}
		}
		
		if($this->useCaptcha && (!isset($_POST['captcha_word']) || !isset($_SESSION['control_word']) || $_SESSION['control_word'] == '' || md5($_POST['captcha_word']) != $_SESSION['control_word'])){
			$this->captchaError = true;
			$this->errors[] = 'Некорректно введен защитный код';
		}
		
	}
	
	function getUpdateQuery(){
		$arr = array();
		foreach($this->values as $k=>$v){
			$arr[] = $k."='".mysql_escape_string($v)."'";			
		}
		return implode(",",$arr);
	}
	
	
	function printForm($submit_text = 'Отправить', $params = null){
		if(!$submit_text)
			$submit_text = 'Отправить';
		ob_start();
		require TPL_DIR.'uform.php';
		$string = ob_get_contents();
		ob_end_clean();

		return $string;		
	}
	
	function writeInDB($ignore_fields = array()){
		
		$flds = implode(',',array_keys($this->initVals));
		$vls = implode('","',array_values($this->initVals));
		if(count($this->initVals))
			$vls = '"'.$vls.'"';
		/*
		foreach($this->formDesc as $k => $f){
			$flds .= ($k > 0 || count($this->initVals) ? ',' : '').$f['name'];
			$vls .= ($k > 0 || count($this->initVals) ? ',' : '').'"'.mysql_escape_string($this->getValue($f['name'])).'"';
		}
		*/
		$i = 0;
		foreach($this->values as $k => $v){
			if(count($ignore_fields) && in_array($k, $ignore_fields)){
				continue;
			}
			$flds .= ($i > 0 || count($this->initVals) ? ',' : '').$k;
			$vls .= ($i > 0 || count($this->initVals) ? ',' : '').'"'.mysql_escape_string($v).'"';
			$i++;
		}

		$GLOBALS['db']->query('insert into '.$this->table.'('.$flds.') values('.$vls.')');
		//echo 'insert into '.$this->table.'('.$flds.') values('.$vls.')';
		if(!mysql_error()){
			$this->sent = true;
			return mysql_insert_id();
		}else{
			return 0;
		}
	}
	
	function getMailContents($content = ''){
		$cnt = '';
		foreach($this->formDesc as $v){
			if($this->getValue($v['name']) != "")
				$cnt .= $v["title"].": ".$this->getValue($v['name'])."\n";
		}		
		return $cnt.$content;	
	}
	
	function sendEmail($to, $title = "Сообщение с сайта", $content = ""){
		$res = Email::sendMail($to, $title, $this->getMailContents($content), "feedback@".str_replace('www.','',$_SERVER["SERVER_NAME"]), isset($this->file_attach) ? $this->file_attach["path"] : "", isset($this->file_attach) ? $this->file_attach["orig_name"] : "");
		return $res;
	}
	
	function haveErrors(){
		return (count($this->errors))?true:false;
	}
	
	function hasError($field){
		return isset($this->errors[$field]);
	}	
	
	function getValue($index){
		return (isset($this->values[$index])) ? $this->values[$index] : "";
	}
	
	function addInitValues($arr){
		$this->initVals = array_merge($arr, $this->initVals);
	}
	
	function getErrors(){
		$str = "";
		foreach($this->errors as $v)
			$str .= "<div>".$v."</div>";
		return $str;
	}
	
	function getValues(){
		return $this->values;
	}
	
}
?>