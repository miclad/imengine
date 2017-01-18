<?

if(!AuthUser::getInstance()->hasAccess('datatable_structure'))
	throw new NeedAuthException();
	
if(isset($_GET['component']) && trim($_GET['component']) != ''){
	
	$comp_name = $_GET['component'];
	if(file_exists(COMPONENTS_DIR.$comp_name)){
		
		if(file_exists(COMPONENTS_DIR.$comp_name.'/inc/config.php'))
			require COMPONENTS_DIR.$comp_name.'/inc/config.php';
	
		if(isset($_GET['centry_id']) && isset($configs)){
			$centry = new ComponentEntry($_GET["centry_id"]);			
		}
	
		if(!isset($config_template_dir)){
			$page = MODULES_DIR.'components/templates/configs.php';
		}else{
			$page = MODULES_DIR.'components/templates/'.$config_template_dir.'/main.php';
		}
				
		require $page;
		
	}else{
		throw new Exception('Компонент не найден');
	}

}else{
	throw new Exception('Компонент не найден');
}

?>