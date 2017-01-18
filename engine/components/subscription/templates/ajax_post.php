</div>

<script type="text/javascript">

	$(document).ready(
		function(){
			var frm = $('#subscribe_form form');
			$(frm).submit(
				function(){
					var email = $('[name="email"]', frm).val();
					var name = $('[name="name"]', frm).val();
					$.post(
						$(frm).attr('action'),
						{'email': email, 'name': name},
						function(rsp){
							if(rsp.error)
								$('#subscribe_form_errors').html(rsp.error);
							else{
								$('#subscribe_form_errors').html('');
								$('#subscribe_form').html(rsp.result).css({'padding': '20px 0', 'text-align': 'center'});
							}
						},
						'json'
					);
					return false;
				}
			);
		}
	);

</script>