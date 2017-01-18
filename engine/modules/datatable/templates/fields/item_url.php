<?
$f_val = $this->ent->getRowValue($field);
$par_val = '';
if($f_val != ''){
	$val = preg_match("/^(.*?)([^\/]+)$/", $f_val, $pock);
	if(count($pock) > 2){
		$f_val = $pock[2];
		$par_val = $pock[1];
	}
}elseif(intval($this->ent->getRowValue('parent'))){
	$par_res = $GLOBALS['db']->query('select * from '.$this->ent->entity.' where id='.$this->ent->getRowValue('parent'))->getNext();
	if($par_res){
		$par_val = $par_res['url'].'/';
	}
}
?>

<div class="field--url">
	<div class="field--url__prefix<?if(!$par_val){?> hidden<?}?>" id="item_url">
		<?if($par_val != ''){?>
			<?=$par_val?>
		<?}?>
	</div>
	<div class="field--url__input">
		<input type="text" name="<?=$field_name?>" value="<?if($f_desc[1][1]!="password")echo htmlspecialchars($f_val);?>" class="form-control"/>
	</div>
	<?if(isset($f_desc[1]['by_fields'])){?>
		<div class="field--url__button">
			<button type="button" class="btn" onclick="$('input[name=<?=$field_name?>]').val(iUtils.makeSlug(new Array('<?=implode('\',\'', $f_desc[1]['by_fields'])?>')))">
				Сгенерировать
			</button>
		</div>
	<?}?>
	<?if($this->ent->getRowValue($field) != '' && isset($f_desc[1]['show_link']) && $f_desc[1]['show_link']){?>
		<div class="field--url__show">
			<?
			$lnk = (isset($f_desc[1]['link_prefix']) ? $f_desc[1]['link_prefix'] : '').$this->ent->getRowValue($field).'/';
			?>
			<i class="fa fa-eye"></i> <a href="<?=$lnk?>" target="_blank"><?=$lnk?></a>
		</div>
	<?}?>
</div>

<script type="text/javascript">

	$(document).ready(
		function(){		

			if($('select[name="folder_id"]').length){
				requestParentUrl($('select[name="folder_id"]').val());
			}
					
			$('select[name="parent"]').change(
				function(){
					requestParentUrl($(this).val());
				}
			);	
			
			$('select[name="folder_id"]').change(
				function(){
					requestParentUrl($(this).val());
				}
			);	
		}
	);
	
	function requestParentUrl(val){
		$.get('/engine/modules/datatable/admin/retrieve_data/get_parent_url.php', {'parent': val, 'table': '<?=$this->ent->entity?>'}, onParentUrlLoaded);
	}
	
	function onParentUrlLoaded(resp){
		if(resp != ''){
			$('#item_url').text(resp + '/').removeClass('hidden');
		}
	}

</script>