<?
$current_type = explode('|', $this->getConfig('current_type'));
$is_current = isset($current_type[0]) && $current_type[0] == $this->getConfig('type_id');
?>