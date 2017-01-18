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
		$('select[name="<?=$field?>"]').on('change', function(){
			
			var val = $(this).val();
			
			$.ajax({
				type: 'post',
				url: '<?=$retrieve_url?>',
				data: {<?=isset($v['foreign_field']) ? $v['foreign_field'] : $field?>: val},
				success: function(rsp){
					rsp.unshift({'id': 0, 'name': '--'});
					fillSelect($('select[name="<?=$v['field']?>"]'), rsp, null, <?=intval($dt->getRowValue($v['field']))?>);
				},
				dataType: 'json',
				async: false
			});
			
		});
		
		$(document).ready(function(){
			$('select[name="<?=$field?>"]').change();
		});
		
	<?}}?>
	
</script>
