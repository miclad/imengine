<input type="text" name="<?=$field_name?>" value="<?if($f_desc[1][1]!="password")echo $this->ent->getRowValue($field);?>"<?if(isset($f_desc[1]['by_fields'])){?> style="width:180px;"<?}?> class="form-control" id="formField<?=$field_name?>"/>
<?if(isset($f_desc[1]['by_fields'])){?>
	&nbsp; <input type="button" value="Сгенерировать" class="button" onClick="document.forms[0].elements['<?=$field_name?>'].value = makeSlug(new Array('<?=implode('\',\'', $f_desc[1]['by_fields'])?>'))"/>
<?}?>