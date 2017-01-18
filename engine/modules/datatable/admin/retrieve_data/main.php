<?

if(isset($_GET['entity'])){
	
	$fn = dirname(__FILE__).'/entities/'.$_GET['entity'].'.php';	
	if(file_exists($fn)){
		require $fn;
	}else{
		
		if(!isset($GLOBALS['entities'][$_GET['entity']])){
			exit();
		}
		
		if(!AuthUser::getInstance()->isAuthorized())
			throw new NeedAuthException();
	
		$dt = new DataTable($_GET['entity']);
		$dt->use_pager = 0;

		$flt = $_POST;
		if(isset($_GET['filter'])){
			$flt = array_merge($flt, $_GET['filter']);
		}
		
		$fn = dirname(__FILE__).'/filter/'.$_GET['entity'].'.php';
		if(file_exists($fn))
			require $fn;

		$dt->setFilter($flt, false);

		$grid = $dt->getGrid();

		echo json_encode($grid['elements']);
	
	}
	
}

exit();

?>