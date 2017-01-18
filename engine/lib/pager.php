<?
class Pager{
	
	var $firstPage;
	var $lastPage;
	var $itemsNum;
	var $totalPages;
	var $currentPage;
	var $outputType;
	var $outputPar;
	var $pageSize;
	var $pageSizeFirst;
	var $baseUrl;
	var $template = '';
	var $is_all_pages = false;
	
	/*
	 Constructor
	 Parameters:
	  $total    - count rows in result
	  $pageSize - items limit on the page
	  $cp_par   - index in $_GET which tells about current page
	  $ot       - pager style (0 or 1)
	  $op       - parameter for pager output, sets pager size
	  $url      - base url of the page with pager
	*/
	function Pager($total, $pageSize, $cp_par, $ot, $op, $url = ''){
		
		if(isset($_GET['show_all_pages'])){
			$this->is_all_pages = true;
		}

		$this->itemsNum = $total;
		$this->pageSize = $pageSize;
		$this->pageSizeFirst = $this->pageSize;
		$this->totalPages = ceil($total/$this->pageSize);
		
		if(isset($_GET[$cp_par]) && is_numeric($_GET[$cp_par]))
			$this->currentPage = $_GET[$cp_par];
		else
			$this->currentPage = 1;
			
		$this->outputType = $ot;
		$this->outputPar = $op;
		$this->getFirstPage();
		$this->getLastPage();
		
		/* get base url */
		$this->baseUrl = $_SERVER['REQUEST_URI'];
		if(strpos($this->baseUrl, '?') === false)
			$this->baseUrl .= '?';
		else{
			$this->baseUrl = Common::editQueryString($this->baseUrl, array($cp_par));
			if(substr($this->baseUrl, strlen($this->baseUrl)-1) != '?')
				$this->baseUrl .= '&';
		}			
		
		if($this->currentPage > $this->lastPage)
			$this->currentPage = $this->lastPage;
			
		if($this->is_all_pages && isset($_GET['page_offset']))
			$this->currentPage = intval($_GET['page_offset']);
			
	}
	
	function setPageSizeFirst($val){
		$this->pageSizeFirst = intval($val);
		if($this->pageSizeFirst > 0 && $this->pageSizeFirst != $this->pageSize){
			$this->totalPages = 1;
			$rest = $this->itemsNum-$this->pageSizeFirst;
			if($rest < 0)
				$rest = 0;
			$this->totalPages += ceil($rest/$this->pageSize);
		}
	}
	
	function setTemplate($tpl){
		$this->template = $tpl;
	}
	
	function getFirstPage(){
    	if($this->outputType == 0){
			if($this->currentPage <= $this->outputPar)
				$this->firstPage = 1;
			else{
				$ost = $this->currentPage%$this->outputPar;
				if(!$ost)
					$ost = $this->$this->outputPar;
				$this->firstPage = $this->currentPage - $ost+1;
			}
		}else{			
			$this->firstPage = $this->currentPage - $this->outputPar;
			if($this->firstPage<1)
				$this->firstPage = 1;
		}
	}
	
	function getLastPage(){
		if($this->outputType == 0)
			$this->lastPage = $this->firstPage + $this->outputPar - 1;
		else
			$this->lastPage = $this->currentPage + $this->outputPar;
			
		if($this->lastPage > $this->totalPages)
			$this->lastPage = $this->totalPages;
	}
	
	function showPrefix(){
		if($this->firstPage == 1)
			return false;
		return true;
	}
	
	function showPostfix(){
		if($this->lastPage<$this->totalPages)
			return true;
		return false;
	}
	
	/*
		getOffset:
		returns offset for mysql_data_seek in DBResult::startPager
	*/	
	function getOffset(){
		if($this->currentPage > 0){
			if(!$this->is_all_pages){
				$fp_diff = $this->currentPage > 1 ? $this->pageSize-$this->pageSizeFirst : 0;
				$res = ($this->currentPage-1)*$this->pageSize - $fp_diff;
				return $res;
			}else{
				$fp_diff = $this->currentPage > 1 ? $this->pageSize-$this->pageSizeFirst : 0;
				$res = ($this->currentPage-1)*$this->pageSize - $fp_diff;
				$this->pageSize = $this->itemsNum;
				return $res;
			}
		}else
			return 0;
	}
	
	function printPager($template = ''){
		$content = '';
		if($template == '')
			$template = 'pager.php';
		if($this->totalPages>1 || $this->is_all_pages){
			ob_start();
			require B_DIR.'engine/templates/'.($this->template != '' ? ''.$this->template : 'public').'/'.$template;
			$content = ob_get_contents();
			ob_end_clean();
		}
		return $content;
	}
	
}
?>