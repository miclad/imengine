<?

$text = $PAGE->getContent();
$PAGE->clearContent();

$text_array = explode('<hr>', $text);

$PAGE->setAttribute('hide_title', true);
$PAGE->setAttribute('no_page_wrap');

$sections = $db->query('select F.* from folders as F,start_page_catalog_folders as SF where SF.folder_id=F.id order by SF.pos')->fetchArray();

$benefits = $db->query('select * from banners where placing="benefits" and visible=1 order by pos');

$side_banners = $db->query('select * from banners where placing="sidebar" and visible=1 order by pos')->fetchArray();


?>