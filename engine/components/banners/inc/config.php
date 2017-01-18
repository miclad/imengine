<?

$templates = array(
	array('reviews', 'Отзывы')
);

$configs = array(
	"placing" => array("Тип",array("select", $GLOBALS['banners_placings'], 0)),
	"template" => array("Шаблон",array("select",$templates,0))
	//"items_per_page" => array("Кол-во элементов на странице",array("input"))
);
?>