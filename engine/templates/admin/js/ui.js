$(function(){
	
	$('.dateField').datepicker({
		dateFormat: 'dd.mm.yy',
		changeMonth: true,
		changeYear: true,
		showOtherMonths: true,
		selectOtherMonths: true,
		showOn: "button",
		buttonText: '',
		firstDay: 1,
		onSelect: function(dt){
			if(dt != '' && $(this).data('time'))
				$(this).val(dt + ', ' + $(this).data('time'));
		}
	});
	
	$('.toggle').iToggle();
	$('.dropdown').dropdown();
	$('.tabs.tabs--js').tabs();
	
	new adminPanel();
	
})