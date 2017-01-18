<?if(isset($dt->dataDesc['multi_upload']) && AuthUser::getInstance()->hasAccess('datatable_'.$check_access_entity)){

	$field_name = 'image';
	
	foreach($dt->fields as $k => $f){
		if($f[1][0] == 'image_to_resize'){
			$field_name = $k;
			break;
		}
	}
	
	ob_start();
	
?>

	<div class="panel">
	
		<a href="#" onclick="return toggleBlock('multiupload_form', this, true, '', 'height')" class="panel-heading">
			<span class="glyphicon glyphicon-upload"></span> Массовая загрузка фотографий
		</a>
		
			<script type="text/javascript" src="/engine/tools/uploadify/jquery.uploadify.min.js"></script>
			<link rel="stylesheet" type="text/css" href="/engine/tools/uploadify/uploadify.css"/>
				
			<script type="text/javascript">
				$(document).ready(function() {
					$('#file_upload').uploadify({
						'swf': '/engine/tools/uploadify/uploadify.swf',
						'uploader': '/engine/admin/index.php?module=datatable&entity=<?=$dt->entity?>&act=add&folder=<?=$dt->curFolder?><?=$dt->foreignField ? '&'.$dt->foreignField.'='.$dt->foreignValue : ''?>',
						'buttonText': 'Выбрать файлы',
						'width': 200,
						'buttonClass': 'btn btn-primary',
						'fileObjName' : '<?=$field_name?>',
						'fileSizeLimit': '32000KB',
						'formData': {'<?=$dt->foreignField?>': '<?=$dt->foreignValue?>', 'folder_id': <?=$dt->curFolder?>, '<?php echo session_name();?>': '<?php echo session_id();?>', 'multi_upload': 1},
						'onUploadSuccess' : function(file, data, response) {
							//$('#photos_list').prepend(data);
						},
						'onQueueComplete' : function(){
							location.reload();
						}
					});
				});
			</script>
				
			<input type="file" name="file_upload" id="file_upload"/>
		
	</div>

<?

	$cnt = ob_get_contents();
	ob_end_clean();
	
	AdminPanel::getInstance()->addSwitchablePanel('Массовая загрузка', $cnt, 'upload');

}?>