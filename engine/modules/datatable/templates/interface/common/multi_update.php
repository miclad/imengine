<?
$multi_update = isset($dt->dataDesc['multi_update']) ? $dt->dataDesc['multi_update'] : array();
if(count($multi_update)){
	
	ob_start();
?>

	<div class="panel">
		
			<form class="form" action="<?=$dt->baseUrl?>&act=multi_update" method="POST">
				
				<div class="row">
					<div class="col-6">
					<?
					$i = 0;
					$ipc = ceil(count($multi_update)/2);
					foreach($multi_update as $f){?>
						<?if(isset($dt->fields[$f]) || $f == 'id'){?>
							<div class="form__item">
								<label for="filter_<?=$f?>" class="form__item__title">
									<?
									$title = ($f == 'id' ? 'ID' : (isset($dt->fields[$f]['filter_title']) ? $dt->fields[$f]['filter_title'] : $dt->fields[$f][0]));
									if(!$title && $f == 'folder_id'){
										$title = 'Категория';
									}
									?>
									<?=$title?>
								</label>
								<div class="form__item__input">								
										<?
										if($f == 'id'){
										?>									
											<input type="text" name="multi_update[<?=$f?>]" value="" class="form-control"/>
										<?}else{
											$mode = isset($dt->fields[$f]['filter_mode']) ? $dt->fields[$f]['filter_mode'] : $dt->fields[$f][1][0];
											if($mode == 'input'){
												if(isset($dt->fields[$f][3][0]) && in_array($dt->fields[$f][3][0], array('select', 'checkbox'))){
													$mode = $dt->fields[$f][3][0];
												}
											}
											switch($mode){
												case 'input':
													echo '<input class="form-control input-sm" type="text" id="multi_update_'.$f.'" name="multi_update['.$f.']" value=""/>';
												break;
												case 'select':
													$options = array();
													$is_range = false;
													if(isset($dt->fields[$f][3]['range'])){

														$is_range = true;
														$r = $dt->fields[$f][3]['range'];

														for($i = $r['min']; $i <= $r['max']; $i+=$r['step']){
															$options[] = array($i, $i);
														}

													}else{
														$options = is_array($dt->fields[$f][3][1]) ? $dt->fields[$f][3][1] : $GLOBALS[$dt->fields[$f][3][1]];
													}
													if($is_range){
														
														echo '<div>';
														
															echo '<select style="display: inline; width: 100px;" class="form-control input-sm" name="multi_update['.$f.'][min]" id="multi_update_'.$f.'">';
																echo '<option value="">--</option>';
																foreach($options as $fv)
																	echo '<option value="'.$fv[0].'">'.$fv[1].'</option>';
															echo '</select>';
														
															echo ' &ndash; ';
														
															echo '<select style="display: inline; width: 100px;" class="form-control input-sm" name="multi_update['.$f.'][max]" id="multi_update_'.$f.'">';
																echo '<option value="">--</option>';
																foreach($options as $fv)
																	echo '<option value="'.$fv[0].'">'.$fv[1].'</option>';
															echo '</select>';
															
															echo ' &nbsp; шаг &nbsp; ';
															
															echo '<input type="text" name="multi_update['.$f.'][step]" value="'.$r['step'].'" class="form-control input-sm" style="display: inline; width: 50px;">';
														
														echo '</div>';
														
													}else{
														echo '<select class="form-control input-sm" name="multi_update['.$f.']" id="multi_update_'.$f.'">';
														echo '<option value="">--</option>';
														foreach($options as $fv)
															echo '<option value="'.$fv[0].'">'.$fv[1].'</option>';
														echo '</select>';
													}
												break;
												case 'checkbox':
													echo '<select class="form-control input-sm" name="multi_update['.$f.']" id="multi_update_'.$f.'">';
													echo '<option value="">--</option>';
													echo '<option value="1">да</option>';
													echo '<option value="0">нет</option>';
													echo '</select>';
												break;
												case 'foreign':
													$t = new DataTable($dt->fields[$f][1][1]);
													$f_values = !isset($dt->fields[$f][1]['no_fill']) ? $t->getGrid('', false && $f == 'folder_id' ? 'entity="'.$dt->entity.'"' : '1', true) : array();
													
													if(isset($dt->fields[$f][1]['mode']) && $dt->fields[$f][1]['mode'] == 'text'){
														
														$ff_ent = new DataTable($dt->fields[$f][1][1]);
														$tf = array_keys($ff_ent->getTitleFields());
														
														echo '<input type="text" name="multi_update[_'.$f.'_fk_text]" class="form-control" id="multiinsertField'.$f.'" value="" data-autocomplete="'.$dt->fields[$f][1][1].'" data-autocomplete-field="'.$tf[0].'"/>';
														
													}else{
													
														echo '<select name="multi_update['.$f.']" class="form-control input-sm" id="multi_update_'.$f.'">';
														echo '<option value="">--</option>';
														foreach($f_values as $fv)
															echo '<option value="'.$fv['__value'].'"'.(false && $dt->filter->getValue($f) == $fv['__value'] ? ' selected' : '').'>'.$fv['__title'].'</option>';
														echo '</select>';
													
													}
													
												break;
												case 'multiple_foreign':
													$t = new DataTable($dt->fields[$f][1][1]);
													$f_values = $t->getGrid('', '1', 'true');
													echo '<select name="multi_update['.$f.']" class="form-control input-sm" id="multi_update_'.$f.'">';
													echo '<option value="">--</option>';
													foreach($f_values as $fv)
														echo '<option value="'.$fv['id'].'"'.(false && $dt->filter->getValue($f) == $fv['id'] ? ' selected' : '').'>'.$fv['name'].'</option>';
													echo '</select>';
												break;
											}
										}
										?>
									</div>
								</div>
							<?}?>
						<?if(($i+1) % $ipc == 0 && $i < count($multi_update) - 1){?>
							</div><div class="col-6">
						<?}?>
					<?$i++;}?>
							
					</div>
				</div>
							
				<div class="form__item">
					<button type="submit" class="btn btn-primary btn-sm">Поехали!</button>						
				</div>
	
			</form>
		
	</div>

	<?

	$combo_fields = array();

	foreach($dt->ent->fields as $fk => $f){

		if(isset($f['onchange']['update'])){
			$combo_fields[$fk] = $f['onchange']['update'];
		}

	}

	?>

	<script type="text/javascript">
		<?foreach($combo_fields as $field => $tgt){
			foreach($tgt as $v){
				$target_field = $dt->ent->fields[$v['field']];
				$retrieve_url = '/engine/admin/index.php?module=datatable&section=retrieve_data&entity='.$target_field[1][1];
		?>
			$('select[name="multi_update[<?=$field?>]"]').on('change', function(){

				var val = $(this).val();

				$.ajax({
					type: 'post',
					url: '<?=$retrieve_url?>',
					data: {<?=isset($v['foreign_field']) ? $v['foreign_field'] : $field?>: val},
					success: function(rsp){
						rsp.unshift({'id': '', 'name': '--'});
						fillSelect($('select[name="multi_update[<?=$v['field']?>]"]'), rsp, null, <?=intval($dt->filter->getValue($v['field']))?>);
					},
					dataType: 'json',
					async: false
				});

			});

			$(document).ready(function(){
				if($('select[name="multi_update[<?=$field?>]"]').val()){
					$('select[name="multi_update[<?=$field?>]"]').change();
				}
			});

		<?}}?>

	</script>


<?

	$cnt = ob_get_contents();
	ob_end_clean();
	
	AdminPanel::getInstance()->addSwitchablePanel('Массовое редактирование', $cnt, 'cogs');

}?>