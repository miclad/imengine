<?

class DBFilter{
	
	private $entity;
	private $fields;
	private $filter_fields;
	private $values = array();
	private $folder_recursive = false;
	private $obj;
	
	function __construct($ent, $obj = null){
		$this->entity = $ent;
		$this->obj = $obj;
		$this->fields = isset($GLOBALS['entities'][$ent][1]) ? $GLOBALS['entities'][$ent][1] : array();
		$this->filter_fields = isset($GLOBALS['entities'][$ent]['filter']) ? $GLOBALS['entities'][$ent]['filter'] : array();
		if(isset($_POST['_set_filter']) && isset($_POST['filter'])){
			if(isset($_GET['folder']))
				$_POST['filter']['folder_id'] = $_GET['folder'];
			if(isset($obj) && $obj->foreignValue)
				$_POST['filter'][$obj->foreignField] = $obj->foreignValue;
			$this->setFilter($_POST['filter']);
			Header('Location: index.php?module=datatable&entity='.$this->entity.(isset($obj) && $obj->foreignValue ? '&'.$obj->foreignField.'='.$obj->foreignValue : '').(isset($_GET['folder']) ? '&folder='.$_GET['folder'] : ''));
			exit(0);
		}
		if(isset($obj) && $obj->foreignField){
			if(isset($_SESSION['db_filter'][$ent][$this->obj->foreignField.$this->obj->foreignValue])){
				$this->values = $_SESSION['db_filter'][$ent][$this->obj->foreignField.$this->obj->foreignValue];
			}
		}else{
			if(isset($_SESSION['db_filter'][$ent])){
				$this->values = $_SESSION['db_filter'][$ent];
			}
		}
	}
	
	function setFilter($arr, $use_session = true){		
		if($use_session){
			if($this->obj && $this->obj->foreignField)
				$_SESSION['db_filter'][$this->entity][$this->obj->foreignField.$this->obj->foreignValue] = $arr;		
			else
				$_SESSION['db_filter'][$this->entity] = $arr;				
		}	
		$this->values = $arr;
	}
	
	function addFilter($arr){
		$this->values = array_merge($this->values, $arr);
	}
	
	function setFolderRecursive($val){
		$this->folder_recursive = $val;
	}
	
	function isEmpty(){
		return count($this->values) == 0;
	}
	
	function clearFilter(){
		$this->values = array();
		if(isset($_SESSION['db_filter'][$this->entity])){
			if($this->obj && $this->obj->foreignField)
				unset($_SESSION['db_filter'][$this->entity][$this->obj->foreignField.$this->obj->foreignValue]);
			else
				unset($_SESSION['db_filter'][$this->entity]);
		}
	}
	
	function getQuery(){
		
		$query = array('tables' => '', 'fields' => '', 'where' => '', 'group_by' => '');
		$query['tables'][] = $this->entity.' as T';
		$query['fields'] = 'T.*';
		$query['group_by'] = 'T.id';
		
		$cond = array();
		
		foreach(array_keys($this->values) as $f){
			
			if(($f == 'id' || $f == 'created_by' || isset($this->fields[$f])) && (is_array($this->getValue($f)) || $this->getValue($f) !== '')){
				
				if(isset($this->fields[$f])){
					
					$filter_mode = isset($this->fields[$f]['filter_mode']) ? $this->fields[$f]['filter_mode'] : '';
					
					if($filter_mode == 'checkbox'){
						
						$val = $this->getValue($f);
						
						$cond[] = 'ifnull(T.'.$f.', 0)'.($val ? ' > 0' : ' = 0');
					
					}elseif($this->fields[$f][1][0] == 'foreign'){	
						
						$val = $this->getValue($f);
						if($this->fields[$f][1][1] == 'folders' && is_numeric($val) && $this->folder_recursive){
							if($this->getValue($f) != 0){
								$tree = new Tree('folders');
								$sections = $tree->getFullTree($this->getValue($f));
								$folders = array();
								for($i=0; $i < count($sections); $i++){
									if($i == count($sections)-1 || $sections[$i+1]['_tree_level'] <= $sections[$i]['_tree_level']){
										$folders[] = $sections[$i]['id'];
									}							
								}
								if(!count($folders)){
									$folders[] = $this->getValue($f);
								}
								$cond[] = 'T.'.$f.' in ('.implode(',', $folders).')';
							}
						}elseif(is_numeric($val)){
								$cond[] = 'T.'.$f.'='.intval($val);
						}elseif(is_array($val)){
								$cond[] = 'T.'.$f.' in ('.implode(',', array_map('intval',$val)).')';
						}
						
					}elseif($this->fields[$f][1][0] == 'input'){
						
						if($this->fields[$f][1][1] == 'number')
							$cond[] = 'T.'.$f.'='.intval($this->getValue($f));
						elseif(isset($this->fields[$f][3][0]) && $this->fields[$f][3][0] == 'select')
							$cond[] = 'T.'.$f.'="'.mysql_escape_string($this->getValue($f)).'"';
						else
							$cond[] = 'T.'.$f.' like "%'.mysql_escape_string($this->getValue($f)).'%"';
							
					}elseif($this->fields[$f][1][0] == 'multiple_foreign'){
						
						$vals = $this->getValue($f);
						if(is_numeric($vals))
							$vals = array($vals);
						if(is_bool($vals) || is_array($vals) && count($vals)){
							$query['tables'][] = $f;
							if(is_bool($vals)){
								$cond[] = 'T.id='.$f.'.'.$this->fields[$f][1][2];
							}else{
								$tw = array();
								for($i=0; $i<count($vals); $i++)
									$tw[] = 'T.id='.$f.'.'.$this->fields[$f][1][2].' and '.$f.'.'.$this->fields[$f][1][3].'='.intval($vals[$i]);
								$cond[] = '('.implode(' or ', $tw).')';
							}
							$query['group_by'] = 'T.id';
						}
						
					}elseif($this->fields[$f][1][0] == 'date'){
						
						$vals = $this->getValue($f);
						if(is_array($vals) && isset($vals['from']) && isset($vals['to'])){
							
							$vals['from'] = Common::makeDBDate($vals['from'], false);
							$vals['to'] = Common::makeDBDate($vals['to'], false);
							
							$q = '';
							
							$cond = array();
							
							if($vals['from'])
								$cond[] = 'T.'.$f.'>="'.$vals['from'].'"';
							if($vals['to'])
								$cond[] = 'T.'.$f.'<="'.$vals['to'].'"';
							
						}
						
					}
					
				}elseif($f == 'created_by'){
					
					if(is_array($this->getValue('created_by'))){
						$cond[] = 'T.created_by in ('.implode(',', array_map('intval', $this->getValue('created_by'))).')';
					}else{
						$cond[] = 'T.created_by='.intval($this->getValue('created_by'));
					}
					
				}elseif($f == 'id'){
					
					if(is_array($this->getValue('id'))){
						$cond[] = 'T.id in ('.implode(',', array_map('intval', $this->getValue('id'))).')';
					}else{
						$cond[] = 'T.id='.intval($this->getValue('id'));
					}
					
				}
				
			}
		}
		
		$query['tables'] = implode(',', $query['tables']);
		$query['where'] = count($cond) ? implode(' and ', $cond) : 1;
		
		return $query;
	}
	
	function getValue($ind){
		return isset($this->values[$ind]) ? $this->values[$ind] : '';
	}
	
	function buildQuery($q){
		
		$str = '
			select '.$q['fields'].'
			from ('.$q['tables'].')'.(isset($q['left_join']) ? $q['left_join'] : '').'
			where '.$q['where'].
			(isset($q['group_by']) && $q['group_by'] ? ' group by '.$q['group_by'] : '').
		    (isset($q['having']) && $q['having'] ? ' having '.$q['having'] : '').
			(isset($q['order_by']) && $q['order_by'] ? ' order by '.$q['order_by'] : '').
			(isset($q['limit']) && $q['limit'] ? ' limit '.$q['limit'] : ''
		);
		
		return $str;
		
	}
	
	public static function buildSearchTextQuery($fields, $search){
		
		$search = str_replace(array('%', '*', '-'), array('', '', ' '), Common::prepareGetString($search));
		$search_query = '';
		$words = explode(' ', $search);
		foreach($words as $t){
			if(mb_strlen($t, 'utf8') > 0){
				$t = mysql_escape_string($t);
				if($search_query != '')
					$search_query .= ' and ';
				$arr = array();
				foreach($fields as $f){
					$arr[] = $f.' like "%'.$t.'%"';
				}
				$search_query .= '('.implode(' or ', $arr).')';
			}
		}
		
		return $search_query;
		
	}
	
}

?>