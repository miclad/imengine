<?

require 'templates/tabs.php';

echo Page::includeComponent('personal:profile:change_password', array('change_password' => true));

$this->setActionTemplate('dummy');