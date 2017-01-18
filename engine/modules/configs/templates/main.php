<?$ta = 1;?>
<?if(count($params)){?>

	<form class="form form--hover" action="<?=$_SERVER['REQUEST_URI']?>" method="post" enctype="multipart/form-data">
	
		<?foreach($params as $k => $v){?>
			<div class="form__item">
			
				<label for="formField<?=$k?>" class="form__item__title"><?=$v[0]?></label>
				<div class="form__item__input">
				
					<?if($v[1][0]=="file"){?>
						<?if(isset($data[$k])&&$data[$k]!=""){?><div><a href="/configs/<?=$data[$k]?>" target="_blank">Просмотр</a> <input type="checkbox" class="chb" name="del[<?=$k?>]" value="1"/> Удалить</div><?}?>
						<input type="file" name="<?=$k?>" id="formField<?=$k?>"/>
					<?}elseif($v[1][0]=="field"){?>
						<input type="text" name="<?=$k?>" value="<?=@$data[$k]?>" id="formField<?=$k?>" class="form-control"/>
					<?}elseif($v[1][0]=="textarea"){?>
						<textarea id="formField<?=$k?>" name="<?=$k?>" class="form-control<?if(isset($v[1][1])){?> ta_short<?}?>"<?if(!isset($v[1][1])){?> rows="6"<?}?>><?=isset($data[$k]) ? $data[$k] : ''?></textarea>
					<?}elseif($v[1][0]=="checkbox"){?>
						<input type="hidden" name="<?=$k?>" value="0"/>
						<input id="formField<?=$k?>" type="checkbox" name="<?=$k?>" value="1"<?if($v[1][1] || isset($data[$k]) && $data[$k]) echo ' checked';?>/>
					<?}elseif($v[1][0]=="select"){?>
						<select name="<?=$k?>" id="formField<?=$k?>" class="form-control">
							<option value="">--</option>
							<?foreach($v[1][1] as $sv){?>
							<option value="<?=$sv[$v[1][2]]?>"<?if(isset($data[$k]) && $data[$k] == $sv[$v[1][2]]){?> selected<?}?>><?if(isset($sv["_tree_level"]))for($i=0; $i<$sv["_tree_level"]; $i++){?>--<?}?> <?=$sv[$v[1][3]]?></option>
							<?}?>
						</select>
					<?}?>
					
				</div>
				
			</div>		
		<?}?>
		
		<?if(AuthUser::getInstance()->hasAccess('configs', 'w')){?>
			<div class="form__submit">
			
				<button type="submit" class="btn btn--large">
					<span class="fa fa-save"></span>  <?if(true || is_numeric($dt->ent->getRowValue("id"))){?>Сохранить<?}else{?>Добавить<?}?>
				</button>

			</div>
		<?}?>
		
	</form>
	
<?}else{?>
	Нет настроек
<?}?>