<script type="text/javascript">

	$(document).ready(function(){
		
		$('select[name="type"]').on('change', function(){
			
			if($(this).val() == 'list'){
				$('#field_list_id').show();
				$('#field_is_multiple').show();
			}else{
				$('#field_list_id').hide();
				$('#field_is_multiple').hide();
			}
			
		}).trigger('change');
		
		
	});
	
</script>