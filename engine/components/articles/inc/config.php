<?
$t = new Tree('folders', 'entity="articles"');
$arr = $t->getFullTree(0, 0, -1, 'pos', 'id,name', false);

$configs = array(
	'folder_id' => array('Раздел', array('select', $arr, 0)),
	'items_per_page' => array('Кол-во элементов на странице', array('input')),
	//'limit' => array('Лимит', array('input')),
	//'template' => array('Шаблон', array('select', $templates, 0))
);

?>