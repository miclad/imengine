<div id="access_rules"<?if(!$dt->getRowValue('admin_panel_access')){?> style="display: none;"<?}?>>
		<?foreach($access_rules as $v){?>
			<div class="form__item">
				<label for="formFieldaccess_<?=$v['name']?>" class="form__item__title"><?=$v['title']?></label>
				<div class="form__item__input">
					<select name="access[<?=$v['name']?>][]" id="formFieldaccess_<?=$v['name']?>" class="form-control">
						<option value="">- нет доступа -</option>
						<?foreach($v['levels'] as $lk => $lv){?>
							<option value="<?=$lk?>"<?if(isset($selected_rules[$v['name']]) && strpos($selected_rules[$v['name']]['access'], $lk) !== false){?> selected<?}?>><?=$lv?></option>
						<?}?>
					</select>
				</div>
			</div>
		<?}?>
</div>