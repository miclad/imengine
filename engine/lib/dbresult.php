<?
class DBResult{
	
	var $res;
	var $conn;
	var $pager;
	var $counter = 0;
	var $array_index = 0;
	var $array_counter = 0;
	
	function DBResult($res, $conn){
		$this->res = $res;
		$this->conn = $conn;
		if(!is_array($res) && is_null($conn)){
			throw new Exception('Соединение не задано');
		}
	}
	
	function selectedRowsCount(){
		if($this->isSelectQuery()){
			if(is_array($this->res))
				return count($this->res);
			else
				return mysql_num_rows($this->res);
		}
		return false;
	}

	function affectedRowsCount(){
		if(!$this->isSelectQuery())
			return mysql_affected_rows($this->conn);
		return false;
	}
	
	function getNext($field = ''){
		if(is_array($this->res)){
			if($this->isSelectQuery() && (isset($this->res[$this->array_index]) && (!$this->pager || $this->array_counter < $this->pager->pageSize))){
				$this->array_counter++;
				return $this->res[$this->array_index++];
			}
		}else{
			if( $this->isSelectQuery() && ($t = mysql_fetch_assoc($this->res)) && (!$this->pager || $this->pager->currentPage > 1 && $this->counter < $this->pager->pageSize || $this->pager->currentPage == 1 && $this->counter < $this->pager->pageSizeFirst)){
				$this->counter++;
				return $field !== '' ? (isset($t[$field]) ? $t[$field] : ''): $t;			
			}
		}
		return false;		
	}

	function isSelectQuery(){
		if(is_array($this->res) || is_resource($this->res) && get_resource_type($this->res) == "mysql result")
			return true;
		return false;
	}
	
	function fetchArray($field = ''){		
		if($this->isSelectQuery()){
			$arr = array();
			while($t = $this->getNext($field))
				$arr[] = $t;
			return $arr;
		}			
		return false;	
	}
	
	/*
		Engages limited items output
		Parameters:
			look comments for Pager::__construct
	*/
	function startPager($pageSize, $url = "", $pagerType = 1, $pagerPar = 5, $curPagePar = "page", $page_size_first = 0){
		if($this->isSelectQuery() && is_numeric($pageSize)>0 && $pageSize>0){
			$this->pager = new Pager($this->selectedRowsCount(),$pageSize,$curPagePar,$pagerType,$pagerPar,$url);
			if($page_size_first)
				$this->pager->setPageSizeFirst($page_size_first);
			$off = $this->pager->getOffset();
			if($off > 0){
				if(is_array($this->res)){
					$this->array_index = $off;
					$this->array_counter = 0;
				}else{
					mysql_data_seek($this->res, $off);
				}
			}
		}
	}
	
	function setPager($pager){
		$this->pager = $pager;
	}

	function lastID(){
		return !$this->isSelectQuery() ? mysql_insert_id($this->conn):false;
	}	
	
	function printPager($template = ''){
		if($this->pager)
			return $this->pager->printPager($template);
	}
	
	function clearCounter(){
		if($this->selectedRowsCount()){
			if(is_array($this->res)){
				$this->array_index = 0;
				$this->array_counter = 0;
			}else{
				mysql_data_seek($this->res, ($this->pager ? $this->pager->getOffset() : 0));
				$this->counter = 0;
			}
		}
	}
	
	function seek($num){
		if($this->isSelectQuery()){
			$num--;
			$nor = false;
			if($num >= $this->selectedRowsCount()){
				$nor = true;
				$num = $this->selectedRowsCount()-1;				
			}
			if(is_array($this->res)){
				$this->array_index = $num;
				$this->array_counter = 0;
			}else{
				mysql_data_seek($this->res, $num);
			}
			if($nor)
				$this->getNext();
		}
	}
	
}
?>