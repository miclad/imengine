<?

class Modules{
	
	function runAdmin($name, $section = '', $params = null){
		
		global $db;
		
		$filename = MODULES_DIR.$name.'/admin'.($section ? '/'.$section : '').'/main.php';
		$output = '';
		
		if(file_exists($filename)){
			
			// устанавливаем текущий элемент меню
			$item_name = $name == 'datatable' ? 'datatable'.(isset($_GET['entity']) ? '_'.$_GET['entity'] : '') : $name.($section ? '_'.$section : '');
			MenuTree::getInstance()->setCurrentItem($item_name);
			
			// выполняем административную часть модуля
			ob_start();
			require $filename;
			$output = ob_get_contents();
			ob_end_clean();
			
		}else{
			throw new PageNotFoundException();
		}
		
		return $output;
		
	}
	
	function getAccessRules($module){
		
		$res = array();
		$file = MODULES_DIR.$module.'/access_rules.php';
		if(file_exists(MODULES_DIR.$module.'/access_rules.php')){
			require $file;
			if(isset($access_rules))
				$res = $access_rules;
		}
		
		return $res;
		
	}
	
	function getList(){
		
		$res = array();
		$dir = opendir(MODULES_DIR);
		while(($f = readdir($dir)) !== false){
			if($f == '..' || $f == '.' || !is_dir(MODULES_DIR.'/'.$f))
				continue;
			$res[] = $f;
		}
		
		return $res;
		
	}
	
}

?>