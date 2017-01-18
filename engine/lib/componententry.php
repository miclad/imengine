<?
class ComponentEntry{
	
	var $id;
	var $desc = array();
	
	function ComponentEntry($id){
		$this->id = $id;
		$t = DBCommon::GetByID('pages_components','*',$id);
		if($t && file_exists(COMPONENTS_DIR.$t['component'].'/inc/config.php')){
			require COMPONENTS_DIR.$t['component'].'/inc/config.php';
			$this->desc = $configs;
		}
	}
	
	function getParam($par){
		$mlt = false;
		$val = '';
		if(isset($this->desc[$par])){
			if(isset($this->desc[$par]['multi'])){
				$val = array();
				$mlt = true;
			}
			$res = $GLOBALS['db']->query('select value from components_configs where centry_id='.$this->id.' and param="'.$par.'"');
			while($t = $res->getNext()){
				if($mlt)
					$val[] = $t['value'];			
				else
					$val = $t['value'];
			}
		}
		return $val;
	}
	
	function getConfigs(){
		return $this->desc;
	}
	
}
?>