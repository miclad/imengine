<?
$t = new Tree('folders', 'entity="articles"');
$arr = $t->getFullTree(0, 0, -1, 'pos', 'id,name', false);

$configs = array(
	'folder_id' => array('Новости', array('select', $arr, 0))
	//'benefits' => array('Преимущества', array('textarea', 1)),
);

?>