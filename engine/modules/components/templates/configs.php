<?if(isset($configs) && count($configs)){?>	
	<?foreach($configs as $k=>$v){?>	
		<div class="form-group">	
			<label for="formField<?=$k?>" class="control-label col-md-2"><?=$v[0]?></label>
			<div class="col-md-10">
				<?
					$val = isset($centry) ? $centry->getParam($k) : '';
				?>
				<?switch($v[1][0]){
					case "input":?>
						<input type="text" name="<?=$k?>" value="<?=$val?>" class="form-control"/>
					<?break;
						case "select":
							if(isset($v[1][2]) && $v[1][2] == 1)
								$multiple = true;
							else
								$multiple = false;
							$arr = $v[1][1];
					?>
						<select name="<?=$k.($multiple?"[]":"")?>"<?=$multiple?" multiple":""?> class="form-control">
							<?if(!$multiple){?>
							<option value="">--</option>
							<?}?>
							<?foreach($arr as $o){?>
							<option value="<?=$o[0]?>"<?if(($multiple && is_array($val) && in_array($o[0],$val)) || $o[0] == $val){?> selected<?}?>><?if(isset($o["level"]))for($i=0;$i<$o["level"];$i++){?>&nbsp;&nbsp;<?}?><?=$o[1]?></option>
							<?}?>
						</select>
					<?break;case "flag":?>
						<input type="hidden" name="<?=$k?>" value="0"/>
						<input type="checkbox" name="<?=$k?>" value="1"<?if(($v[1][1]&&$val=="")||$val == 1){?> checked<?}?>  class="form-control"/>
					<?break;case "textarea":?>
						<textarea name="<?=$k?>"<?if(isset($v[1][1])){?> class="form-control ta_<?=isset($v[1][2])?"short":"big"?>"<?}else{?> class="form-control"<?}?> <?=!isset($v[1][2])?'style="height:600px;"' : 'style="height:250px;"'?>><?=$val?></textarea>
					<?break;?>
				<?}?>		
			</div>
		</div>
	<?}?>
<?}elseif(!Site::isAjaxRequest()){?>
	<div class="form-group">
		У компонента нет настроек
	</div>
<?}?>