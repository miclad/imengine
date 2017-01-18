<?

class MenuTree{
	
	private static $instance = null;
	
	public $menu = array();
	private $root_item = '';
	private $current_item = '';	
	private $path = array();
	
	private function __construct(){	
		
		require ENGINE_DIR.'admin/admin_menu.php';
		$this->menu = $admin_menu;	
		
	}
	
	private function __clone(){}
	
	/**
	 * Singleton
	 *
	 * @return MenuTree
	 */
	static function getInstance(){
		
		if(self::$instance == null)
			self::$instance = new self;
		return self::$instance;
		
	}
	
	function addItem($name, $title, $url, $par = '', $pos = '', $icon = '', $new_window = false){
		
		if(!is_array($par))
			$par = array($par);
		
		foreach($par as $parent){
		
			if($pos === ''){
				$siblings = $this->getItems($parent);
				$pos = count($siblings)+1;
				if(count($siblings))
					$pos = $siblings[count($siblings)-1]['pos']+1;
			}
			
			$this->menu[] = array(
				'name' => $name,
				'title' => $title,
				'url' => $url,
				'pos' => $pos,
				'parent' => $parent,
				'icon' => $icon,
				'new_window' => $new_window
			);
		
		}
		
	}
	
	function getTopParent($item, $child = ''){
		
		for($i=0; $i<count($this->menu); $i++){
			if($this->menu[$i]['name'] == $item){
				if(isset($this->menu[$i]['parent']) && $this->menu[$i]['parent'] != ''){
					return $this->getTopParent($this->menu[$i]['parent'], $item);
				}else{
					return isset($this->menu[$item]['url']) ? $item : $child;
				}
			}
		}
		
	}
	
	function getItems($parent = ''){
		
		$result = array();
		
		for($i=0; $i<count($this->menu); $i++){
			if($parent == '' && !isset($this->menu[$i]['parent']) || isset($this->menu[$i]['parent']) && $this->menu[$i]['parent'] == $parent){
				
				if(!$this->menu[$i]['name'])
					$this->menu[$i]['name'] = 'dummy'.$i;
					
				$par = $this->getTopParent($this->menu[$i]['name'], $this->menu[$i]['name']);
				if($parent == 'datatable_structure'){
					//echo 'a2 '.$par.' '.$this->menu[$i]['url'];
				}

				if(isset($this->menu[$i]['parent']) && (!AuthUser::getInstance()->hasAccess($par) && $this->menu[$i]['name'] != 'dummy'.$i)) continue;
				
				if(!isset($this->menu[$i]['url']) || !$this->menu[$i]['url']){
					$ch = $this->getItems($this->menu[$i]['name']);
					if(count($ch) && isset($ch[0]['url']))
						$this->menu[$i]['url'] = $ch[0]['url'];
				}
				$result[] = $this->menu[$i];
				
			}
		}
		
		return Common::arraySortByField($result, 'pos', SORT_ASC);
		
	}
	
	function setCurrentItem($item){
		$this->current_item = $item;		
		$this->rebuildPath();
	}
	
	function rebuildPath(){
		$this->path = array();
		$this->buildPath($this->getCurrentItem());	
		array_reverse($this->path);
	}
	
	function getCurrentItem(){
		return $this->current_item;
	}
	
	function buildPath($item){
		
		for($i=0; $i<count($this->menu); $i++){
			if($this->menu[$i]['name'] == $item){
				$this->path[] = $this->menu[$i]['name'];
				if(isset($this->menu[$i]['parent']) && $this->menu[$i]['parent'] != ''){
					$this->buildPath($this->menu[$i]['parent']);
				}else{
					$this->path = array_reverse($this->path);
				}
			}
		}
		
	}
	
	function getPath(){
		return $this->path;
	}
	
	function isActive($name){		
		return in_array($name, array_slice($this->path, 0, 2));		
	}
	
	function getRootItem(){
		return (count($this->path) ? $this->path[0] : '');
	}
	
}

?>