<?
		
	$tsmp = '';
	if(!$this->ent->getRowValue('id')){
		$tsmp = time();
	}else{
		$td = $this->ent->getRowValue($field);
		if($td != ''){
			if(isset($this->ent->dataDesc[1][$field][1][2])){
				$tsmp = Common::makeTimestampFromDB($td);
			}elseif(is_numeric($td)){
				$tsmp = $td;
			}
		}
	}
	
	$show_date = is_numeric($tsmp) && $tsmp > 0 ? date('d.m.Y'.(!isset($this->ent->dataDesc[1][$field][1][3]) ? ', H:i' : ''), $tsmp) : '';
	
	$show_time = substr($this->ent->getRowValue($field), 11, 5);
	if(!$show_time){
		$show_time = date('H:i');
	}
	
?>

<div class="field--date">
	<input type="text" name="<?=$field_name?>" class="dateField" id="formField<?=$field_name?>" value="<?=$show_date?>"<?if(isset($this->ent->dataDesc[1][$field][1]['show_time'])){?> data-time="<?=$show_time?>"<?}?>/>
</div>
	