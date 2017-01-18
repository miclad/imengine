<?
$current_type = explode('|', $this->getConfig('current_type'));
$cities = $db->query('select * from cities where 1 order by substr(value,1,1),name');
?>