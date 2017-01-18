<?
$current_type = explode('|', $this->getConfig('current_type'));
$cities = $db->query('select * from cities_dellin where 1 order by name');
?>