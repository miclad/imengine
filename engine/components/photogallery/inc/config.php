<?
$t = new Tree("folders","entity='photogallery'");
$arr = $t->getFullTree(0,0,-1,"pos","id,name",false);
$configs = array(
	"folder" => array("Раздел",array("select",$arr,0)),
	//"items_per_page" => array("Кол-во элементов на странице",array("input"))
);
?>