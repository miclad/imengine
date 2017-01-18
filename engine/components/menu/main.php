<?

if($this->getConfig('show_tree')){
	$tree = new Tree('structure');
	$tree->setCurrentElement($PAGE->getId());
	$pages = $tree->getFullTree(intval($this->getConfig('parent')), 0, -1);
}else{
	if($this->getConfig('show_submenu'))
		$pages = $PAGE->getChildren();
	else
		$pages = is_array($this->getConfig('items')) ? $this->getConfig('items') : DBCommon::getMenu(intval($this->getConfig('parent')), $this->getConfig('navi_type'));
}

?>