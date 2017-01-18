<?foreach($banners as $k => $v){?>
<?if(!$this->getConfig('no_tags')){?><<?=$tag?><?if($k == count($banners)-1){?> class="last"<?}?>><?}?><?=DBCommon::showBanner($v)?><?if(!$this->getConfig('no_tags')){?></<?=$tag?>><?}?>
<?}?>