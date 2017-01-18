<?if(count($dt->filter_fields)){
	ob_start();
?>

	<div class="panel" id="filter_form">
	
			<form class="form" action="<?=$dt->baseUrl?>" method="POST">
			
				<input type="hidden" name="_set_filter"/>
				
				<div class="row">
					<div class="col-6">
					<?
					$i=0;
					$ipc = ceil(count($dt->filter_fields)/2);
					foreach($dt->filter_fields as $f){?>
						<?if(isset($dt->fields[$f]) || $f == 'id'){?>
							<div class="form__item">
								<label for="filter_<?=$f?>" class="form__item__title">
									<?
									$title = ($f == 'id' ? 'ID' : (isset($dt->fields[$f]['filter_title']) ? $dt->fields[$f]['filter_title'] : $dt->fields[$f][0]));
									?>
									<?=$title?>
								</label>
								<div class="form__item__input">								
										<?
										if($f == 'id'){
										?>									
											<input type="text" name="filter[<?=$f?>]" value="<?=htmlspecialchars($dt->filter->getValue($f))?>" class="form-control"/>
										<?}else{
											$mode = isset($dt->fields[$f]['filter_mode']) ? $dt->fields[$f]['filter_mode'] : $dt->fields[$f][1][0];
											if($mode == 'input'){
												if(isset($dt->fields[$f][3][0]) && in_array($dt->fields[$f][3][0], array('select', 'checkbox'))){
													$mode = $dt->fields[$f][3][0];
												}
											}
											switch($mode){
												case 'input':
													echo '<input class="form-control input-sm" type="text" id="filter_'.$f.'" name="filter['.$f.']" value="'.htmlspecialchars($dt->filter->getValue($f)).'"/>';
												break;
												case 'select':
													$options = is_array($dt->fields[$f][3][1]) ? $dt->fields[$f][3][1] : $GLOBALS[$dt->fields[$f][3][1]];
													echo '<select class="form-control input-sm" name="filter['.$f.']" id="filter_'.$f.'">';
													echo '<option value="">--</option>';
													foreach($options as $fv)
														echo '<option value="'.$fv[0].'"'.($dt->filter->getValue($f) == $fv[0] ? ' selected' : '').'>'.$fv[1].'</option>';
													echo '</select>';
												break;
												case 'checkbox':
													echo '<select class="form-control input-sm" name="filter['.$f.']" id="filter_'.$f.'">';
													echo '<option value="">--</option>';
													echo '<option value="1"'.($dt->filter->getValue($f) == '1' ? ' selected' : '').'>да</option>';
													echo '<option value="0"'.($dt->filter->getValue($f) == '0' ? ' selected' : '').'>нет</option>';
													echo '</select>';
												break;
												case 'foreign':
													$t = new DataTable($dt->fields[$f][1][1]);
													$f_values = !isset($dt->fields[$f][1]['no_fill']) ? $t->getGrid('', '1', 'true') : array();
													echo '<select name="filter['.$f.']" class="form-control input-sm" id="filter_'.$f.'">';
													echo '<option value="">--</option>';
													foreach($f_values as $fv)
														echo '<option value="'.$fv['__value'].'"'.($dt->filter->getValue($f) == $fv['__value'] ? ' selected' : '').'>'.$fv['__title'].'</option>';
													echo '</select>';
												break;
												case 'multiple_foreign':
													$t = new DataTable($dt->fields[$f][1][1]);
													$f_values = $t->getGrid('', '1', 'true');
													echo '<select name="filter['.$f.']" class="form-control input-sm" id="filter_'.$f.'">';
													echo '<option value="">--</option>';
													foreach($f_values as $fv)
														echo '<option value="'.$fv['id'].'"'.($dt->filter->getValue($f) == $fv['id'] ? ' selected' : '').'>'.$fv['name'].'</option>';
													echo '</select>';
												break;
											}
										}
										?>
								</div>
							</div>
						<?}?>
					<?if(($i+1) % $ipc == 0 && $i < count($dt->filter_fields) - 1){?>
						</div><div class="col-6">
					<?}?>
					<?$i++;}?>
				</div></div>
							
				<div class="form__item">
					<button type="submit" class="btn">Применить</button>
					<?if(!$dt->filter->isEmpty()){?>
						<a href="<?=$dt->baseUrl?>&_clear_filter=<?=$dt->entity?>" class="btn btn--nobg"><span class="fa fa-remove"></span>Сбросить</a>
					<?}?>
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
			$('select[name="filter[<?=$field?>]"]').on('change', function(){

				var val = $(this).val();

				$.ajax({
					type: 'post',
					url: '<?=$retrieve_url?>',
					data: {<?=isset($v['foreign_field']) ? $v['foreign_field'] : $field?>: val},
					success: function(rsp){
						rsp.unshift({'id': '', 'name': '--'});
						fillSelect($('select[name="filter[<?=$v['field']?>]"]'), rsp, null, <?=intval($dt->filter->getValue($v['field']))?>);
					},
					dataType: 'json',
					async: false
				});

			});

			$(document).ready(function(){
				if($('select[name="filter[<?=$field?>]"]').val()){
					$('select[name="filter[<?=$field?>]"]').change();
				}
			});

		<?}}?>

	</script>


<?

	$cnt = ob_get_contents();
	ob_end_clean();
	
	$id = AdminPanel::getInstance()->addSwitchablePanel('Фильтр', $cnt, 'sliders', '', 'title');
	if(!$dt->filter->isEmpty()){
		AdminPanel::getInstance()->setActivePanel($id);
	}

}?>