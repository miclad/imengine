<?
class Form{
	
	var $values = array();
	var $errors = array();
	var $sent = false;
	var $id;
	var $link = "";
	var $filesQueue = array();
	var $mainData = null;
	
	function Form($id=0,$link = ""){
		if(is_numeric($id)&&$id>0){
			$this->id = $id;
			$this->link = $link;
			$this->mainData = DBCommon::getById("forms","*",$this->id);
		}
	}
	
	function read(){
		if($this->mainData)
		foreach($this->getQuestions() as $q){
			foreach($this->getFields($q["id"]) as $f){
				$fname = "fld".$f["id"];
				$res = 0;
				$val = "";
				switch($f["input_type"]){
					case "checkbox":
						if(isset($_POST[$fname]))
							$val = "1";
						else
							$val = "0";
					break;
					case "file":
						$res = CheckData::checkFile($fname,array("","image"),false,512000);
						if($res == 0){
							preg_match("/^(?i).+?\.([^.]+)$/",$_FILES[$fname]["name"],$p);
							$ext = $p[1];
							$val = $ext;
							$this->filesQueue[] = array($f["id"],$ext);
						}		
					break;
					default:
						if(!isset($_POST[$fname]))
							$res = 1;
						else{
							$res = CheckData::CheckString($_POST[$fname],$f["datatype"]);
							$val = trim($_POST[$fname]);
						}
					break;
				}
				if($res==0)
					$this->values[$f["id"]] = substr(trim($val),0,255);
				else
					if($q["req"]==1){
						if(!isset($this->errors[$q["id"]]))
							$this->errors[$q["id"]] = $res;
					}
					else
						$this->values[$f["id"]] = "";
			}
		}
		
	}
	
	function writeRes(){
		if(!$this->haveErrors()){
			$res = $GLOBALS['db']->query("insert into forms_results(date,form_id) values(".time().",".$this->id.")");
			$res_id = $res->lastID();
			if($res_id){
				foreach($this->values as $k=>$v)
					$GLOBALS['db']->query("insert into forms_results_ans(res_id,field_id,answer) values(".$res_id.",".$k.",'".mysql_escape_string($v)."')");
				foreach($this->filesQueue as $f){
					copy($_FILES["fld".$f[0]]["tmp_name"],"img/form_photos/".$res_id."_".$f[0].".".$f[1]);
				}
				if(!mysql_errno())
					$this->sent = true;
			}
		}
	}
	
	function getMailContents(){
	}
	
	function sendEmail(){
		ob_start();
		require B_DIR."engine/components/form/templates/email.php";
		$string = ob_get_contents();
		ob_end_clean();
		mail($this->mainData["email"],"����� ���������",$string,"Content-type: text/plain; charset=windows-1251\nFrom: <admin@unichance.ru>");	
	}
	
	function printForm(){
		if($this->mainData)
		if(!$this->sent){
			$string = "";
			ob_start();
			require B_DIR."engine/components/form/templates/main.php";
			$string = ob_get_contents();
			ob_end_clean();
			return $string;
		}else{
			return "<div>".$this->mainData["message"]."</div>";
		}
		
	}
	
	function haveErrors(){
		return (count($this->errors))?true:false;
	}
	
	function hasError($field){
		return isset($this->errors[$field]);
	}	
	
	function getErrors(){
		$arr = array();
		$res = $GLOBALS['db']->query("select name from forms_questions where id in('".implode("','",array_keys($this->errors))."')");
		while($t = $res->getNext())
			$arr[] = $t['name'];
		return $arr;
	}
	
	function getValue($index){
		return (isset($this->values[$index]))?$this->values[$index]:"";
	}
	
	function getGroups(){
		return DBCommon::getFromBase("*","forms_q_groups","form_id=".$this->id,"pos");
	}
	
	function getQuestions($group = 0){
		$case = "";
		if($group > 0)
			return DBCommon::getFromBase("*","forms_questions","group_id=".$group,"pos");
		else
			return DBCommon::getFromBase("Q.*","forms_questions as Q,forms_q_groups as G","G.id=Q.group_id and G.form_id=".$this->id,"G.pos,Q.pos");
		
	}
	
	function getFields($question){
		return DBCommon::getFromBase("*","forms_fields","question_id=".$question,"pos");
	}
	
}
?>