<div class="article">
	<?=$data['content']?>
</div>

<?if($data['photogallery_id']){
	echo Page::includeComponent('photogallery:main', array('folder' => $data['photogallery_id']));
}?>