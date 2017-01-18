<?

class Page{
	
	private static $instance = null;
	
	private $db;
	private $path = array();
	private $attributes = array();
	private $title = '';
	private $page_request = array();
	private $page_data = false;
	private $children = null;
	private $layout = '';
	
	/* page data */
	private $root_id = 0;
	private $id = 0;
	private $name = '';
	private $content = '';	
	private $head_title = '';
	private $meta_keywords = '';
	private $meta_description = '';
	private $parent = '';
	private $navi_type = '';
	private $is_index = '';
	private $meta_params = array();
	
	private $extra_url = '';
	
	private $lang = '';
	
	private function __clone(){}
	
	/**
	 * returns current instance (singleton)
	 *
	 * @return Page
	 */
	static function getInstance(){
		if(self::$instance == null)
			self::$instance = new self;
		return self::$instance;
	}
	
	private function __construct($id = 0){
		
		$this->db = $GLOBALS['db'];
		
		if($id){
			$this->page_data = self::retrievePageData('S.id='.intval($id));
		}else{
			$this->initByRequest();
		}
		
		/* get index page */
		if(!$this->page_data && !count($this->page_request)){
			$this->page_data = self::retrievePageData('S.main=1');
			$this->is_index = true;
		}
		
		if($this->page_data){
					
			$this->id = $this->page_data['id'];
			$this->name = $this->page_data['name'];
			$this->content = $this->page_data['content'] != '' ? $this->page_data['content'] : $this->page_data['content'];	
			$this->parent = $this->page_data['parent'];
			$this->navi_type = $this->page_data['navi_type'];
			$this->is_index = $this->page_data['main'];
			$this->head_title = $this->page_data['html_title'];
			$this->meta_description = $this->page_data['html_description'];
			$this->meta_keywords = $this->page_data['html_keywords'];
			
			$this->initPath();
			$this->parseAttributes();
			
		}
		
	}
	
	private function initByRequest(){
		
		$q = $_SERVER['REQUEST_URI'];
		if($q == '/index.php')
			$q = '';
		if(strpos($_SERVER['REQUEST_URI'], '?') !== false)
			$q = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
		
		$q = preg_replace("/(\/+)/", "/", $q);
		$q  = trim(preg_replace("/^\/*(.*?)\/*$/","$1", $q));

		/* get page request array */
		if($q != '')
			$this->page_request = explode("/", $q);
		
		/* check lang prefix */
		/* multilang
		if(count($this->page_request) && $this->page_request[0] == SiteLang::getInstance()->getName()){
			$this->page_request = array_slice($this->page_request, 1);
		}
		*/
	
		$i = 0;
		foreach($this->page_request as $k=>$p){
			$url = '/'.implode('/', array_slice($this->page_request, 0, count($this->page_request)-$k));
			$this->page_data = self::retrievePageData('S.url="'.mysql_escape_string(urldecode($url)).'"');
			if($this->page_data)
				break;			
			$i++;
		}
		
		/* init extra params */
		if($this->page_data && $i > 0){
			if(!$this->page_data['allow_extra_params']){
				$this->page_data = false;
			}else{
				$extra_params = array_slice($this->page_request, count($this->page_request)-$i);
				$set_get_vars = $this->page_data['set_get_vars'] != '' ? explode(' ', $this->page_data['set_get_vars']) : array();
				$params_limit = $this->page_data['extra_params_limit'] ? $this->page_data['extra_params_limit']*2 - count($set_get_vars) : 0;
				if($params_limit > 0 && $params_limit < count($extra_params)){
					$this->page_data = false;
				}else{
					$this->extra_url = '/'.implode('/', $extra_params);
					for($i=0; $i<count($extra_params); $i++){
						if($i < count($set_get_vars)){
							$_GET[$set_get_vars[$i]] = $extra_params[$i];
						}else{
							$_GET[$extra_params[$i]] = $i < count($extra_params)-1 ? $extra_params[$i+1] : '';
							$i++;
						}
					}
				}
			}
		}
		
	}
	
	static function retrievePageData($q = '1'){
		
		/* multilang
		$res = $GLOBALS['db']->query('
				select S.*
				from structure as S 
			    left join structure_lang as L on S.id=L.entry_id and L.lang='.SiteLang::getInstance()->getID().'
				where '.$q.' 
			    limit 1'
		);
		*/
		
		$res = $GLOBALS['db']->query('
				select S.*
				from structure as S
				where '.$q.' 
			    limit 1'
		);
		
		return $res->getNext();
		
	}
	
	function getChildren(){
		
		if($this->children == null){
			$this->children = DBCommon::getMenu($this->id);
			if(!count($this->children) && $this->parent > 0 && count($this->path) > 1){
				$this->children = DBCommon::getMenu($this->parent);
			}
		}
		return $this->children;
				
	}
	
	function setChildren($list){
		$this->children = $list;
	}
	
	function addChild($name, $link){
		if(!is_array($this->children))
			$this->children = array();
		$this->children[] = array('name' => $name, 'link' => $link);
	}
	
	function hasChildren(){
		$res = $this->db->query('select count(*) as cnt from structure where parent='.$this->id.' and visible=1')->getNext();
		return $res['cnt']>0;
	}
	
	function getField($field){
		return isset($this->page_data[$field]) ? $this->page_data[$field] : '';
	}
	
	function setField($field, $value){
		$this->page_data[$field] = $value;
	}
	
	function getExtraUrl(){
		return $this->extra_url;
	}
	
	/**
	 * Path functions
	 */
	
	
	function initPath(){
		
		/* multilang_mode	
		$tree = new Tree('structure', '1', ' left join structure_lang as L on M.id=L.entry_id and L.lang='.SiteLang::getInstance()->getId());
		$this->path = $tree->getPath('M.*,L.name,L.html_title', $this->getId());
		*/
		
		$tree = new Tree('structure', '1');
		$this->path = $tree->getPath('M.*', $this->getId());
	
		foreach($this->path as $k=>$v){
			$this->path[$k]['link'] = $v['url'].'/';
		}		
		
		if(count($this->path))
			$this->root_id = $this->path[0]['id'];
		
		if(!$this->isIndex()){
			/* multilang
			$this->path = array_merge(array(array('name' => LangContent::getInstance()->getString('index_page'), 'link' => '/')), $this->path);
			*/
			$this->path = array_merge(array(array('name' => 'Главная', 'link' => '/')), $this->path);
		}
		
	}
	
	function clearPath(){
		$this->path = array();
	}
	
	function addItemToPath($title, $link = '', $to_start = false){
		if($to_start){
			$this->path = array_merge(array(array('name' => $title, 'link' => $link)), $this->path);
		}else{
			$this->path[] = array('name' => $title, 'link' => $link);
		}
	}
	
	function getPathItem($ind){
		$arr = array('name' => '', 'link' => '');
		if(isset($this->path[$ind])){
			return $this->path[$ind];
		}
	}
	
	function changePathItem($ind, $name = '', $link = ''){
		if(isset($this->path[$ind])){
			if($name != ''){
				$this->path[$ind]['name'] = $name;
			}
			if($link != ''){
				$this->path[$ind]['link'] = $link;
			}
		}
	}
	
	function printPath($start_from = 0){
		if(true || count($this->path) > 1)
			require TPL_DIR.'path.php';
	}
	
	function getId(){
		return $this->id;
	}
	
	function getRootId(){
		return $this->root_id;
	}
	
	function getParentName(){
		$str = '';
		if($this->hasChildren() || !$this->getField('parent')){
			$str = $this->path[count($this->path)-1]['name'];
		}elseif($this->getField('parent')){
			$str = $this->path[count($this->path)-2]['name'];
		}
		return $str;
	}
	
	function getLang(){
		
	}
	
	function getPathLength(){
		return count($this->path);
	}
	
	function getUrl($index_empty = true){
		if( $this->page_data && (!$index_empty || !$this->isIndex()))
			return $this->page_data['url'];
		return '';
	}
	
	function getUrlNoLang(){
		return preg_replace("/^(\/".SiteLang::getInstance()->getName().")(.*)$/", "$2", $_SERVER['REQUEST_URI']);
	}
	
	
	/**
	 * Content
	 */
	
	function isIndex(){
		return $this->is_index;
	}
	
	function getHeadTitle(){
		if($this->head_title != ''){
			return $this->head_title;
		}else{			
			/* print path */
			$t = array();
			foreach($this->path as $k => $v){
				if($v['link'] != '/')
					$t[] = strip_tags($v['name']);
			}
			if(count($t))
				return $t[count($t)-1];//;implode(' - ', array_reverse($t));
		}
	}
	
	function setHeadTitle($val){
		$this->head_title = $val;
	}
	
	function getTitle(){		
		if($this->title != ''){
			$str = $this->title;
			if(is_array($this->title)){
				$str = '<ul class="b-page__steps">';
				foreach($this->title as $v){
					$str .= '<li class="b-page__steps__item'.($v['active'] ? ' s-active' : '').'">'.($v['link'] ? '<a href="'.$v['link'].'">'.$v['name'].'</a>' : $v['name']).'</li>';
				}
				$str .= '</ul>';
			}
			return $str;
		}elseif($this->getField('page_title')){
			return $this->getField('page_title');
		}elseif(count($this->path)){
			return $this->path[count($this->path)-1]['name'];
		}else{
			return $this->name;
		}
	}
	
	function setTitle($val){
		$this->title = $val;
	}
	
	function getContent(){
		$cnt = $this->content;
		if(Site::isAjaxRequest()){
			$cnt = $this->content;
		}
		return $cnt;
	}
	
	function addContent($cnt){
		$this->content .= $cnt;
	}
	
	function clearContent(){
		$this->content = '';
	}
	
	/**
	 * Meta content
	 */
	
	function getMetaKeywords(){
		return $this->meta_keywords;
	}
	
	function setMetaKeywords($val){
		$this->meta_keywords = $val;
	}
	
	function getMetaDescription(){
		return $this->meta_description;
	}
	
	function setMetaDescription($val){
		$this->meta_description = $val;
	}
	
	function setMeta($name, $value){
		$this->meta_params[] = array($name, $value);
	}
	
	function showMeta(){
		$str = '';
		foreach($this->meta_params as $v){
			$str .= '<meta name="'.$v[0].'" content="'.$v[1].'"/>';
		}
		return $str;
	}
	
	/**
	 * Attributes 
	 */
	
	function setAttribute($name, $value = true, $save_in_session = false){
		$this->attributes[$name] = $value;
		if($save_in_session)
			$_SESSION['page_attribute_'.$name] = $value;
	}
	
	function getAttribute($name, $default = '', $search_in_session = true){
		if(isset($this->attributes[$name]))
			return $this->attributes[$name];
		elseif($search_in_session && isset($_SESSION['page_attribute_'.$name])){
			$this->setAttribute($name, $_SESSION['page_attribute_'.$name]);
			return $_SESSION['page_attribute_'.$name];
		}
		return $default;
	}
	
	/**
	 * Components
	 */
	
	function getComponents(){
		
		$arr = array();
		
		$res = $this->db->query('select * from pages_components where page_id='.$this->id.' order by pos');
		while($t = $res->getNext()){
			$t['params'] = array();
			$res_p = $this->db->query('select * from components_configs where centry_id='.$t['id']);
			while($tt = $res_p->getNext()){
				if(isset($t['params'][$tt['param']]) && !is_array($t['params'][$tt['param']])){
					$t['params'][$tt['param']] = array($t['params'][$tt['param']]);
				}
				if(isset($t['params'][$tt['param']]))
					$t['params'][$tt['param']][] = $tt['value'];
				else
					$t['params'][$tt['param']] = $tt['value'];
			}
			$arr[] = $t;
		}
		
		return $arr;
		
	}
	
	static function includeComponent($name, $params = array(), $init_params = array(), $view = '', $template = '', $inner_request = true){
		
			if(!is_array($init_params) || !count($init_params)){
				$init_params = $_GET;
			}
			
			if(strpos($name, ':') !== false){
				$t_name = explode(':', $name);
				if(count($t_name) > 1){
					$name = $t_name[0];
					$view = $t_name[1];
				}
				if(count($t_name) > 2)
					$template = $t_name[2];
			}
			
			$cnt = '';
			$component = new Component($name, $params, $init_params, $view, '', $template, $inner_request);
			
			ob_start();		
			$component->execute();
			$cnt = ob_get_contents();
			ob_end_clean();
			
			return $cnt;		
	}
	
	function setLayout($lo){
		$this->layout = $lo;
	}
	
	function getLayout(){
		if(isset($_GET['print']) && $this->getAttribute('allow_print'))
			return 'print_layout.php';
		else
			return $this->layout == '' ? 'layout.php' : 'layout_'.$this->layout.'.php';
	}
	
	function parseAttributes(){
		
		if($this->page_data && $this->page_data['attributes'] != ''){
			
			$attr = explode('&', $this->page_data['attributes']);
			foreach($attr as $v){
				
				$t = explode('=', $v);
				if(count($t) >= 2){
					$attr_name = $t[0];
					$attr_value = $t[1];
				}else{
					$attr_name = $v;
					$attr_value = 1;
				}
				
				$this->setAttribute($attr_name, $attr_value);
				
			}
			
		}
		
	}
	
}

?>