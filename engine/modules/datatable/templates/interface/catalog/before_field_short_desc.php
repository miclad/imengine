<?foreach($section_properties as $pk => $p){
				$is_title = $k < count($section_properties)-1 && $section_properties[$k+1]['_tree_level'] > $p['_tree_level'];
			?>			
				<div class="form__item" id="field_prop<?=$pk?>">
					<label for="formFieldProp<?=$pk?>" class="form__item__title"><?=$p['name']?></label>
					<?if(!$is_title){?>
						<div class="form__item__input">
							<?if($p['type'] == 'checkbox'){?>
								<?
								$terms = $db->query('
									select 
									T.id,T.name 
									from catalog_property_terms as PT,catalog_terms as T 
									where PT.term_id=T.id and PT.property_id='.$p['id'].'
									order by PT.pos'
								);
								?>
								<input type="hidden" name="properties[<?=$p['id']?>]"/>
								<?
								$values = isset($property_values[$p['id']]) ? explode(',', $property_values[$p['id']]) : array();
								while($trm = $terms->getNext()){?>
									<div>
										<label>
											<input type="checkbox" name="properties[<?=$p['id']?>][]" value="<?=$trm['id']?>" class="chb"<?if(in_array($trm['id'], $values)){?> checked<?}?>/>
											<?=$trm['name']?>
										</label>
									</div>
								<?}?>
							<?}elseif($p['type'] == 'list'){?>
								<?
								$terms = $db->query('
									select * from lists_elements where list_id='.intval($p['list_id']).' order by title
								');
								?>
								<?if(!$p['is_multiple']){
									$value = isset($_POST['properties'][$p['id']]) ? intval($_POST['properties'][$p['id']]) : (isset($property_values[$p['id']]) ? intval($property_values[$p['id']]) : '');
								?>
									<select name="properties[<?=$p['id']?>]" class="form-control">
										<option value="">--</option>
										<?while($trm = $terms->getNext()){?>
											<option value="<?=$trm['id']?>"<?if($trm['id'] == $value){?> selected<?}?>><?=$trm['title']?></option>
										<?}?>
									</select>
								<?}else{
									$value = isset($_POST['properties'][$p['id']]) ? $_POST['properties'][$p['id']] : (isset($property_values[$p['id']]) ? explode(',', $property_values[$p['id']]) : array());
									if(!is_array($value))
										$value = array($value);
								?>
									<input type="hidden" name="properties[<?=$p['id']?>]"/>
									<div class="field--multi-check">
										<?while($trm = $terms->getNext()){?>
											<div>
												<label>
													<input type="checkbox" name="properties[<?=$p['id']?>][]" value="<?=$trm['id']?>"<?if(in_array($trm['id'], $value)){?> checked<?}?>/>
													<?=$trm['title']?>
												</label>											
											</div>
										<?}?>
									</div>
								<?}?>
							<?}else{?>
								<?
								$value = isset($_POST['properties'][$p['id']]) ? htmlspecialchars($_POST['properties'][$p['id']]) : (isset($property_values[$p['id']]) ? htmlspecialchars($property_values[$p['id']]) : '');
								?>
								<input type="text" name="properties[<?=$p['id']?>]" value="<?=$value?>" class="form-control"/>
							<?}?>
						</div>
					<?}?>
				</div>
<?}?>