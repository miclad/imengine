<?
class DataRow{
	
	var $entity;
	var $dataDesc;
	var $errors = array();
	var $values = array();
	var $filesQueue=array();
	var $filesDelQ=array();
	var $imageResizeQueue = array();
	var $files_in_array = array();
	
	function DataRow($entity, $files_in_array = array()){
		$this->entity=$entity;
		$this->dataDesc=$GLOBALS["entities"][$entity][1];
		$this->files_in_array = $files_in_array;
	}
	
	function getInsertQuery(){
		$res=array();
		$res[0]=implode(",",array_keys($this->values));
		$res[1] = '';
		$alr = false;
		foreach($this->values as $v){
			if($alr)
				$res[1] .= ',';
			$res[1] .= (!is_null($v) ? '"' : '') . (is_null($v) ? 'NULL' : mysql_real_escape_string($v)) . (!is_null($v) ? '"' : '');
			$alr = true;
		}
		return $res;
	}
	
	function getUpdateQuery(){
		$str="";
		$alr=false;
		foreach($this->values as $k=>$v){
			if($alr)
				$str.=",";
			$str .= $k."=".(!is_null($v) ? "'" : '').(!is_null($v) ? mysql_real_escape_string($v) : 'NULL').(!is_null($v) ? "'" : '');
			$alr=true;
		}
		return $str;
	}
	
	function haveErrors(){
		if(count($this->errors))
			return true;
		return false;
	}
	
	function getErrors(){
		return $this->errors;
	}		
	
	function read($type="add", $data = null){
		
		global $db;
		
		if(!$data)
			$data = $_POST;
		
		foreach($this->dataDesc as $k=>$v){
			
				if($type=="update" && (in_array($v[1][0], array('timestamp_add')) || !in_array($v[1][0], array('file', 'image_to_resize', 'timestamp_update', 'auto_increment')) && !isset($data[$k]) && !isset($data['_'.$k.'_fk_text']))){
					continue;
				}
				
				$allow_null = isset($GLOBALS["entities"][$this->entity]['allow_null']) && !isset($v['not_null']) || isset($v['allow_null']);
					
				$res=0;		
				switch($v[1][0]){
					case "input":
						if(isset($data[$k])){
							$res=CheckData::checkString($data[$k],$v[1][1]);
							if($res == 0 || ($res == 1 && !$v[2])){
								$this->values[$k]=$v[1][1]=="password" ? md5($data[$k]) : $data[$k];
							}
							if($res == 0 && $v[1][1] == 'password' && (isset($data[$k.'_repeat']) && $data[$k.'_repeat'] != $data[$k]))
								$res = 8; // ERROR - passwords differ
							if(isset($v[1][1]) && $v[1][1] == "number")
								$this->values[$k] = $data[$k] === '' && $allow_null ? '' : intval($data[$k]);
							if(isset($v[1][1]) && $v[1][1] == "float")
								$this->values[$k] = $data[$k] === '' && $allow_null ? '' : str_replace(',','.',floatval(str_replace(',', '.', $data[$k])));
							if(isset($v['unique']) && $res == 0){
								/* check if value is unique */
								$q = $k.' = "'.mysql_escape_string($this->values[$k]).'"';
								if($type == 'update'){
									$q .= ' and id != '.$data['id'];
								}
								if($v['unique'] != ''){
									$val = isset($data[$v['unique']]) ? (is_numeric($data[$v['unique']]) ? intval($data[$v['unique']]) : mysql_escape_string($data[$v['unique']])) : 0;
									$q .= ' and '.$v['unique'].'="'.($val).'"';
								}
								$u_res = $GLOBALS['db']->query('select * from '.$this->entity.' where '.$q);
								if($u_res->selectedRowsCount())
									$res = 9; // value is not unique
							}
							if($type == 'update' && $res == 1 && $v[1][1] == 'password' && !isset($data['password_required'])){
								$res = 0;
								unset($this->values[$k]);
							}
						}else
							$res=1;
						
					break;
					case "file":
						$file_data = Common::getPostFileData($k, $this->files_in_array);
						$res=CheckData::checkFile($file_data, $v[1], true);
						if($res==0){
							/*
							$ext = "";
							preg_match("/(?i)^.+?\.([A-z0-9]{1,10})$/",$_FILES[$k]["name"],$pock);
							if(isset($pock[1]))
								$ext = $pock[1];
							*/
							$this->values[$k]=$file_data["name"];
							$this->filesQueue[] = $k;
						}elseif($type=="update" && isset($data[$k]['delete'])){
							$this->filesDelQ[] = $k;
							$this->values[$k] = "";
						}
						if(!isset($this->values[$k]) && $type != 'update')
							$this->values[$k] = '';
					break;
					case "date":
						
						if($v[1][1]=="auto")
							$this->values[$k]=time();
						else{
							
							$form = &$data;
							
							if(isset($form[$k]) && is_array($form[$k])){
								
								if(isset($v[1]['format']) && $v[1]['format'] == 'db' && isset($form[$k]['day']) && isset($form[$k]['month'])){
									$this->values[$k] = sprintf('%04d', $form[$k]['year']).'-'.sprintf('%02d', $form[$k]['month']).'-'.sprintf('%02d', $form[$k]['day']);
								}else{
									$date=@mktime(@$form[$k]["hour"],@$form[$k]["minute"],0,$form[$k]["month"],$form[$k]["day"],$form[$k]["year"]);
									if($date!==false && $date!==-1)
										$this->values[$k] = isset($v[1]['format']) && $v[1]['format'] == 'db' ? date('Y-m-d', $date) : $date;
									else							
										$res=2;
								}
								
							}elseif(isset($form[$k]) && trim($form[$k]) != ''){
								
								if(preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}(\s[0-9]{2}\:[0-9]{2}\:[0-9]{2})?$/', $data[$k])){
									
									$this->values[$k] = $data[$k];
									
								}else{
									
									$tt = explode(', ', $form[$k]);
									if(count($tt) > 0){
										$d_d = explode('.', $tt[0]);
										$d_t = array();
										if(count($tt) == 2)
											$d_t = explode(':', $tt[1]);
										if(!count($d_t)){
											$d_t = array('00', '00');
										}
										if(count($d_d) == 3 && (count($tt) == 1 || count($d_t) == 2)){
											
											if(isset($v[1][2])){
												$this->values[$k] = $d_d[2].'.'.$d_d[1].'.'.$d_d[0].(count($d_t) ? ' '.$d_t[0].':'.$d_t[1] : '');
											}else{
												$date = mktime($d_t[0], $d_t[1], 0, $d_d[1], $d_d[0], $d_d[2]);
												if($date !== false && $date !== -1)
													$this->values[$k] = $date;
												else							
													$res=2;
											}
										}
									}
									
								}
								
							}else{
								if($type == 'update' && !$v[2]){
									if(!is_array($form[$k])){
										$this->values[$k] = null;
									}else
										$this->values[$k] = 0;
									$res = 0;
								}else 
									$res=1;
							}
						}
						
					break;
					case "timestamp_add":
						$this->values[$k] = date('Y-m-d H:i:s');						
					break;
					case "timestamp_update":
						$this->values[$k] = date('Y-m-d H:i:s');				
					break;
					case "foreign":
						
						$fk_text_index = '_'.$k.'_fk_text';
						
						if(isset($data[$k])){
							if(DBCommon::getById($v[1][1],"*",$data[$k]) || ($data[$k] == 0 && !$v[2])){
								$res=0;
								$this->values[$k] = intval($data[$k]);
							}else{
								$res=1;
							}
						}elseif(isset($data[$fk_text_index])){
							
							$data[$fk_text_index] = trim($data[$fk_text_index]);
							
							if($data[$fk_text_index] == ''){
								
								$this->values[$k] = 0;
								if($v[2]){
									$res = 1;
								}
								
							}else{
							
								// get title field
								$t_fields = explode(',', $GLOBALS['entities'][$v[1][1]][2][0]);
								$tf = '';
								foreach($t_fields as $fv){
									if($GLOBALS['entities'][$v[1][1]][1][$fv][1][1] == 'input'){
										$tf = $GLOBALS['entities'][$v[1][1]][1][$fv][0];
										break;
									}
								}
								if(!$tf)
									$tf = 'name';

								if($tf){
									$t = $db->query('select id from '.$v[1][1].' where '.$tf.'="'.mysql_escape_string($data[$fk_text_index]).'"')->getNext();
									if($t){
										$this->values[$k] = intval($t['id']);
									}else{

										if($v[1]['allow_create'] && AuthUser::getInstance()->hasAccess('datatable_'.$v[1][1], 'w')){
											$f_ent = new DataTable($v[1][1]);
											$this->values[$k] = $f_ent->insertRow(array($tf => $data[$fk_text_index]));
										}

										if(!$this->values[$k])
											$res = '11';

									}
								}else{
									$res = 1;
								}
								
							}
							
						}else{
							$res=1;
						}
						if(isset($v['unique']) && $res == 0){
							/* check if value is unique */
							$q = $k.' = "'.mysql_escape_string($this->values[$k]).'"';
							if($type == 'update'){
								$q .= ' and id != '.$data['id'];
							}
							if($v['unique'] != ''){
								$q .= ' and '.$v['unique'].'='.(isset($data[$v['unique']]) ? intval($data[$v['unique']]) : 0);
							}
							$u_res = $GLOBALS['db']->query('select * from '.$this->entity.' where '.$q);
							if($u_res->selectedRowsCount())
								$res = 9; // value is not unique
						}
					break;
					case "image_to_resize":						
						if($type=="update" && isset($data[$k]['delete'])){
							$this->filesDelQ[] = $k;
						}else{
							$file_data = Common::getPostFileData($k, $this->files_in_array);
							$res = CheckData::checkFile($file_data, array('','image'), true);
							if($res == 0){
								$this->imageResizeQueue[] = $k;
							}
						}
					break;
					case "auto_increment":
						$cond="1";
						if(isset($v[1][1])){
							foreach(explode(",",$v[1][1]) as $t_c){
								$cond.=" and ".$t_c."='".@$data[$t_c]."'";
							}
						}
						if($type != 'update')
							$this->values[$k]=DBCommon::getMaxPos($this->entity,$cond);
						else{
							if(isset($data['pos'])){
								$this->values[$k] = intval($data['pos']);
							}
							/*
							$val = 0;
							if(isset($data['id'])){
								$tt = $GLOBALS['db']->query('select pos from '.$this->entity.' where id='.intval($data['id']))->getNext();
								if($tt){
									$val = $tt['pos'];
								}
							}
							$this->values[$k] = $val;
							*/
						}
					break;
				}
				
				$escape_mode = isset($v[1]['escape_mode']) ? $v[1]['escape_mode'] : (isset($GLOBALS['entities'][$this->entity]['escape_mode']) ? $GLOBALS['entities'][$this->entity]['escape_mode'] : '');
				if($res == 0 && isset($this->values[$k]) && $escape_mode){
					switch($escape_mode){
						case 'escape':
							$this->values[$k] = htmlspecialchars($this->values[$k]);
						break;
						case 'strip_tags':
							$this->values[$k] = strip_tags($this->values[$k]);
						break;
					}
				}

				if($res>0 && $v[2] || $res > 1){
					if($type != 'update' || $res > 1 || $res == 1 && $v[1][0] != 'file' && $v[1][0] != 'image_to_resize')
						$this->errors[$k] = $res;
				}
				
				if($allow_null && isset($this->values[$k]) && $this->values[$k] === '')
					$this->values[$k] = null;
					
				
		}
		
	}
	
}
?>