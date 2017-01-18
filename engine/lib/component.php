<?

class Component{
	
	private $name = '';
	private $folder = '';
	private $config_params = array();
	private $config_values = array();
	private $init_params = array();
	private $current_action = '';
	private $default_action = '';
	private $is_inner = false;
	
	private $template = '';
	private $action_template = '';
	
	private $errors = array();
	
	function __construct($name, $configuration = array(), $init_params = array(), $action = '', $template = '', $action_template = '', $inner_request = false){
		
		if($name != '' && file_exists(COMPONENTS_DIR.$name) && is_dir(COMPONENTS_DIR.$name)){
			
			$this->is_inner = $inner_request;			
			$this->folder = COMPONENTS_DIR.$name.'/';
			
			/* get config params */
			if(file_exists($this->folder.'inc/config.php')){
				
				/* include config  file */
				require $this->folder.'inc/config.php';
				
				if(isset($configs)){
					$this->config_params = $configs;
				}
				
				if(isset($default_action)){
					$this->default_action = $default_action;
				}
				
			}
			
			$this->name = $name;
			$this->config_values = $configuration;
			$this->init_params = $init_params;
			
			$this->initCurrentAction($action);
			
			$this->setTemplate($template);
			$this->setActionTemplate($action_template);
			
			$this->action_template = $action_template;
			
			// check auth
				
			if(isset($need_auth)){
				if((!isset($no_auth_actions) || !in_array($this->current_action, $no_auth_actions)) && !AuthUser::getInstance()->isAuthorized()){
					throw new NeedAuthException();
				}
			}
			
		}else{
			$this->addError('Компонент не найден');
		}
		
	}
	
	function initCurrentAction($action = ''){
		
		if($action == ''){		
			
			$request_action = $this->getInitParam('a');
			$avail_actions = array();
			$dir = opendir($this->folder);
			while(($f = readdir($dir)) !== false){
				if(!is_dir($this->folder.'/'.$f) && $f != '.' && $f != '..')
					$avail_actions[] = $f;					
			}
			if(!in_array($request_action.'.php', $avail_actions)){
				$request_action = '';
			}
						
			if(trim($request_action) != ''){				
				$action = $request_action;
			}elseif($this->default_action != ''){
				$action = $this->default_action;
			}else{				
				$action = 'main';
			}
		}
		
		if($action != '' && !file_exists($this->folder.$action.'.php'))
			$action = '';
			
		if($action == ''){
			$this->addError('Файл компонента не найден');
		}else{
			$this->current_action = $action;
		}
		
	}
	
	function execute(){
		
		global $db;
		global $PAGE;
		global $USER;
		global $wishlist_o;
		
		if(file_exists($this->folder.$this->current_action.'.php'))
			require $this->folder.$this->current_action.'.php';
			
		if(!$this->hasErrors()){
			if($this->getActionTemplate() != ''){
				require $this->getActionTemplate();			
			}
		}else{
			echo implode('<br>',$this->getErrors());
		}	
		
	}
	
	function setTemplate($tpl = ''){
		$this->template = $tpl;
	}
	
	function getTemplate($fld = false){
		return $this->template.($fld && $this->template != '' ? '/' : '');
	}
	
	function setActionTemplate($tpl = ''){
		$this->action_template = $tpl;
	}
	
	function getActionTemplate(){
		if($this->action_template == ''){
			$this->action_template = $this->current_action;
		}
		
		$path = $this->folder.'templates/'.$this->getTemplate(true).$this->action_template.'.php';
		
		if(file_exists($path)){
			return $path;
		}else{
			return '';
		}
	}
	
	function getConfig($param, $search_in_request = true){
		if(isset($this->config_values[$param]))
			return $this->config_values[$param];
		elseif($search_in_request && isset($this->init_params[$param])){
			return $this->init_params[$param];
		}
		elseif(isset($this->config_params[$param]['default']))
			return $this->config_params[$param]['default'];
	}
	
	function setConfig($param, $value){
		$this->config_values[$param] = $value;
	}
	
	function getInitParam($param, $default = ''){
		return isset($this->init_params[$param]) ? $this->init_params[$param] : $default;
	}
	
	function setInitParam($param, $value){
		$this->init_params[$param] = $value;
	}
	
	private function addError($err){
		$this->errors[] = $err;
	}
	
	function getErrors(){
		return $this->errors;
	}
	
	function hasErrors(){
		return count($this->errors);
	}
	
	function getCurrentAction(){
		return $this->current_action;
	}
	
	function getConfigs(){
		return $this->config_values;
	}
	function setConfigs($data){
		$this->config_values = $data;
	}
	
}

?>