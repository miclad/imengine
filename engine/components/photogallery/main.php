<?

$q = '1';
if(is_numeric($this->getConfig('folder')))
	$q = 'folder_id = '.$this->getConfig('folder');	

	
$list = $db->query('select * from photogallery where '.$q.' order by pos');	
if($this->getConfig('items_per_page'))
	$list->startPager($this->getConfig('items_per_page'));

?>