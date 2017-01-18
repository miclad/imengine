<div class="form__item" id="field_show_cases">
			<label class="form__item__title col-md-2">Условия показа</label>
			<div class="col-md-10">
				<?
				$sc = $dt->getRowValue('show_case');
				?>
				<?foreach($GLOBALS['banners_show_cases'] as $v){?>
					<div>			
						<?				
						$check = is_array($sc) && isset($sc[$v[0]]) || is_string($sc) && strpos($sc, ' '.$v[0].' ') !== false;
						?>
						<input type="checkbox" name="show_case[<?=$v[0]?>]" value="1" class="chb"<?if($v[0] == 'catalog'){?> onclick="showOnlySections(this)"<?}elseif($v[0] == 'brands'){?> onclick="showOnlyBrands(this)"<?}elseif($v[0] == 'selected_pages'){?> onclick="showSelectedPages(this)"<?}?><?if($check){?> checked<?}?>/> <b><?=$v[1]?></b>
					</div>
					<?if($v[0] == 'catalog'){?>
						<?
						$catalog_chosen = is_array($sc) && isset($sc['catalog']) || is_string($sc) && strpos($sc, ' catalog ') !== false;
						$check = is_array($sc) && isset($sc['only_sections']) || is_string($sc) && strpos($sc, ' only_sections ') !== false;
						?>
						<div id="show_only_sections" style="<?if(!$catalog_chosen){?>display:none;<?}?>">
							<div><input type="checkbox" name="show_case[only_sections]" value="1" onClick="showCatalogSections(this)" class="chb"<?if($check){?> checked<?}?>/> Только в выбранных разделах</div>
							<div id="banner_sections" style="<?if(!$check){?>display:none;<?}?>padding-bottom:6px; border-bottom:1px solid #ccc; margin-bottom:6px;">
								<?=$fields['banner_sections']['html']?>
							</div>
						</div>
					<?}elseif($v[0] == 'brands'){?>
						<?
						$b_chosen = is_array($sc) && isset($sc['brands']) || is_string($sc) && strpos($sc, ' brands ') !== false;
						$check = is_array($sc) && isset($sc['only_brands']) || is_string($sc) && strpos($sc, ' only_brands ') !== false;
						?>
						<div id="show_only_brands"<?if(!$b_chosen){?> style="display:none;"<?}?>>
							<div><input type="checkbox" name="show_case[only_brands]" value="1" onClick="showCatalogBrands(this)" class="chb"<?if($check){?> checked<?}?>/> Только в выбранных брендах</div>
							<div id="banner_brands" style="<?if(!$check){?>display:none;<?}?>">
								<?=$fields['banner_brands']['html']?>
							</div>
						</div>
					<?}elseif($v[0] == 'selected_pages'){?>
						<?
						$pages_chosen = is_array($sc) && isset($sc['selected_pages']) || is_string($sc) && strpos($sc, ' selected_pages ') !== false;
						?>
						<div>
							<div id="selected_pages"<?if(!$pages_chosen){?> style="display:none;"<?}?>>
								<div style="padding-bottom: 6px;">
									<?=$fields['banner_pages']['html']?>
								</div>
							</div>
						</div>
					<?}?>
				<?}?>
			</div>
		</div>