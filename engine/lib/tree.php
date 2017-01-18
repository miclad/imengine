<?
class Tree{
	var $table;
	var $q;
	var $left_join = '';
	var $current_element = 0;
	var $path_array = array();
	var $path_array_full  = array();
	var $only_brench = false;
	
	function Tree($table,$q = "1", $left_join = ''){
		$this->table = $table.' as M';
		$this->q = " and ".$q;
		$this->left_join = $left_join;
	}
	
	function getPath($fields, $id){
		$res=array();
		$tmp = DBCommon::getFromBase($fields.',M.parent', $this->table.$this->left_join, "M.id=".intval($id));
		if(count($tmp)){
			$cur = $tmp[0];
			if($cur["parent"]>0)
				$res = array_merge($res, $this->getPath($fields,$cur["parent"]));
			$res[] = $cur;
		}
		return $res;
	}
	
	function setCurrentElement($id, $only_brench = false){
		
		if($id){
			$this->only_brench = $only_brench;
			/* get path to element */
			$this->current_element = $id;
			//$this->path_array = array();
			$this->path_array_full = $this->getPath('*', $id);
			foreach($this->path_array_full as $v){
				$this->path_array[] = $v['id'];
			}
			
			if(count($this->path_array)){
				/* check if last element has children */
				$res = $GLOBALS['db']->query('select count(*) as cnt from folders where parent='.$this->path_array_full[count($this->path_array)-1]['id'])->getNext();
				$this->path_array_full[count($this->path_array)-1]['has_children'] = $res['cnt'] > 0;
			}
		}
	}
	
	function hasChildren($parent){
		if($GLOBALS['db']->query("select id from ".$this->table." where parent='".$parent."'")->selectedRowsCount())
			return true;
		return false;
	}

	function getFullTree($parent=0, $level=0, $level_limit=-1, $order="M.pos", $fields="*", $assoc=true, $ignore_id=0){
		$path_length = count($this->path_array);
		$result = array();
		$tmp = DBCommon::getFromBase($fields, $this->table.$this->left_join, "M.parent=".$parent.$this->q, $order);
		for($i=0; $i<count($tmp); $i++){
			if(
				$tmp[$i]["id"] != $ignore_id && 
				(!$this->only_brench || 
				in_array($tmp[$i]['id'], $this->path_array) || 
				($level == $path_length) || 
				($level == $path_length-1 && $tmp[$i]['parent'] == $this->path_array_full[$path_length-1]['parent'] && !$this->path_array_full[$path_length-1]['has_children']) 
				)
			){			
				$result[] = array_merge(($assoc)?$tmp[$i]:array_values($tmp[$i]),array("level"=>$level,"_tree_level"=>$level));
				if((!$this->current_element || (!isset($tmp[$i]['is_final']) || !$tmp[$i]['is_final'])) && (!$this->only_brench || in_array($tmp[$i]['id'], $this->path_array)) && $this->hasChildren($tmp[$i]["id"]) && ($level_limit == -1 || $level < $level_limit) && (!$this->current_element || in_array($tmp[$i]['id'], $this->path_array)))
					$result = array_merge($result,$this->getFullTree($tmp[$i]["id"],$level+1,$level_limit,$order,$fields,$assoc,$ignore_id));
			}
		}
		return $result;
	}
}
?>