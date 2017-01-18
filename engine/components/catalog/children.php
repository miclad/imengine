<?
$sections = $db->query('
	select F.*,count(C.id) as cnt
	from folders as F
	left join catalog as C on C.folder_id=F.id and C.active=1
	where F.entity="catalog" and F.parent='.intval($this->getConfig('parent')).' 
	group by F.id
	order by F.pos');

$total_num = 0;
while($v = $sections->getNext()){
	$total_num += $v['cnt'];
}

$sections->clearCounter();

?>