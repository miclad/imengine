<?php

class AdminPanel{
	
	private static $instance = null;
	private $switchable_panels = array();
	private $active_panel = null;
	
	
	private function __clone(){}
	
	private function __construct(){}
	
	/**
	 * returns current instance (singleton)
	 *
	 * @return AdminPanel
	 */
	static function getInstance(){
		if(self::$instance == null)
			self::$instance = new self;
		return self::$instance;
	}
	
	public function addSwitchablePanel($title, $content, $font_icon = null, $icon = null, $placing = 'title'){
		
		$id = md5($title.'|'.$font_icon.'|'.$icon);
		
		$this->switchable_panels[] = array(
			'id' => $id,
			'title' => $title,
			'content' => $content,
			'font_icon' => $font_icon,
			'icon' => $icon,
			'placing' => $placing
		);
		
		return $id;
		
	}
	
	public function setActivePanel($id){
		$this->active_panel = $id;
	}
	
	public function getActivePanel(){
		return $this->active_panel;
	}
	
	public function getSwitchablePanels($placing = null){
		
		if($placing === null){
			return $this->switchable_panels;
		}else{
			
			$list = array();
			foreach($this->switchable_panels as $v){
				if($placing == $v['placing'])
					$list[] = $v;
			}

			return $list;
		
		}
		
	}
	
}