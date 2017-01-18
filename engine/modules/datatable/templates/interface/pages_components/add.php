<?
$ta=1;
$action = is_numeric($dt->ent->getRowValue("id")) ? 'update' : 'add';
?>
<form class="form" action="<?=$dt->baseUrl?>&act=<?if(is_numeric($dt->ent->getRowValue("id"))){?>update&id=<?=$dt->ent->getRowValue("id")?><?}else{?>add<?}?><?if($_GET["show"]=="folder_form")echo "&sp=folder"?>&show=<?=$_GET["show"]?>" method="post" enctype="multipart/form-data">

	<?if(is_numeric($dt->ent->getRowValue("id"))){?>
		<input type="hidden" name="id" value="<?=$dt->ent->getRowValue("id")?>"/>
	<?}?>
	<?if($dt->ent->foreignField!=""){?>
		<input type="hidden" name="<?=$dt->ent->foreignField?>" value="<?=$dt->ent->foreignValue?>"/>
	<?}?>
	<?if(isset($extra_foreign)){?>
		<input type="hidden" name="<?=$extra_foreign[0]?>" value="<?=$extra_foreign[1]?>"/>
	<?}?>
	
	<?
	$fields = $dt->ent->getFormFields();
	?>
	<?foreach($fields as $k => $f){?>
		<div class="form__item" id="field_<?=$k?>">
			<label for="formField<?=$k?>" class="form__item__title col-md-2"><?=$f['title']?></label>
			<div class="col-md-10">
				<?=$f['html']?>
			</div>
		</div>
	<?}?>
	<div id="configs_form"></div>
	
	<?if(AuthUser::getInstance()->hasAccess('datatable_'.$check_access_entity, 'w')){?>
			
		<div class="form__item form-submit">
			
			<div class="col-md-offset-2 col-md-2">

				<button type="submit" class="btn btn-primary btn-lg">
					<span class="glyphicon glyphicon-ok"></span> <?if(true || is_numeric($dt->ent->getRowValue("id"))){?>Сохранить<?}else{?>Добавить<?}?>
				</button>
				
			</div>
				
			<div class="form--back_to_edit">
				<div class="checkbox">
					<label>
						<input type="hidden" name="__back_to_edit" value="0"/>
						<input type="checkbox" name="__back_to_edit" value="1"<?if(isset($_COOKIE['admin_'.($dt->ent->entity != $dt->entity ? $dt->ent->entity.'_' : '').$dt->entity.'_'.$action.'_back_to_edit']) && (!isset($_POST['__back_to_edit'])) || isset($_POST['__back_to_edit']) && $_POST['__back_to_edit'] == 1){?> checked<?}?>>
						и продолжить редактирование
					</label>
				</div>
			</div>
			
		</div>

	<?}?>

	<div class="clear"></div>

</form>

<script type="text/javascript">

	var centry_id = <?=intval($dt->getRowValue('id'))?>;

	$(document).ready(
		function(){			
			$('select[name="component"]').parent().prev().append('&nbsp; <span id="loader"></span>');
			$('select[name="component"]').change(
				function(){
					loadConfigs(this);
				}
			)
		}
	);
	
	function loadConfigs(el){
		var val = $(el).val();
		if(val == ''){
			$('#configs').html('');
		}else{
			var params = {
				'module': 'components',
				'section': 'configs',
				'component': val,
				'centry_id': centry_id
			};
			$('#loader').html('<img src="/engine/templates/admin/img/ajax-loader.gif"/>');
			$.get(
				'/engine/admin/', 
				params, 
				function(rsp){
					$('#loader').html('');
					$('#configs_form').html(rsp);
					initRedactor();
					/*
					tinyMCE.init({
						mode : "specific_textareas",
						editor_selector : /ta_big|ta_short/,
						theme : "advanced",
						convert_urls : false,
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "left",
						theme_advanced_statusbar_location : "bottom",
						plugins : "table,advimage",
						content_css: "/css/mce_styles.css",
						theme_advanced_buttons1 : "bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,forecolor,formatselect,separator,undo,redo",
						theme_advanced_buttons2 : "bullist,numlist,separator,sub,sup,separator,link,unlink,anchor,image,hr,charmap,separator,removeformat,cleanup,code",
						theme_advanced_buttons3 : "tablecontrols",
						extended_valid_elements : "a[name|href|target|title|onclick]",
						theme_advanced_blockformats : "h1,h2,h3",
						language : "en"
					});		
					*/
				}
			)
		}						
	}
	
	<?if($dt->getRowValue('id')){?>
	loadConfigs($('select[name="component"]'));
	<?}?>

</script>