<script type="text/javascript">

	$(document).ready(function(){
		
		var rules_box = $('#access_rules');
		var rules_params = $(rules_box).find('select');
		
		$('input[name="admin_panel_access"]').click(function(){			
			
			if($(this).is(':checked')){
				
				$(rules_box).show();
				$(rules_params).removeAttr('disabled');
				
			}else{
				
				$(rules_box).hide();
				$(rules_params).attr('disabled', true);
				
			}
			
		});
		
	});

</script>