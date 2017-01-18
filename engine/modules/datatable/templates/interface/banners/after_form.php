<script type="text/javascript">

	var placing_fields = {<?$alr = false; foreach($GLOBALS['banners_placings'] as $p)if(isset($p['fields']) && count($p['fields'])){if($alr) echo ','; $alr=true;?>'<?=$p[0]?>': new Array("<?=implode('","', $p['fields'])?>")<?}?>}

	function showPlacingFields(){

		$('.form__item').not('#field_placing').not('.form-submit').hide();

		var placing = $('select[name="placing"]').val();
		if(placing_fields[placing]){
			for(var i=0; i<placing_fields[placing].length; i++){
				$('#field_' + placing_fields[placing][i]).show();
			}
			$('#field_visible').show();
		}	

	}
					
	function showOnlySections(el){
		if(el.checked){
			$('#show_only_sections').show();
		}else{
			$('#show_only_sections').hide();
		}
	}
	function showCatalogSections(el){
		if(el.checked){
			$('#banner_sections').show();
		}else{
			$('#banner_sections').hide();
		}
	}
	
	function showOnlyBrands(el){
		if(el.checked){
			$('#show_only_brands').show();
		}else{
			$('#show_only_brands').hide();
		}
	}
	function showCatalogBrands(el){
		if(el.checked){
			$('#banner_brands').show();
		}else{
			$('#banner_brands').hide();
		}
	}

	function showSelectedPages(el){
		if(el.checked){
			$('#selected_pages').show();
		}else{
			$('#selected_pages').hide();
		}
	}
	
	$(document).ready(
		function(){
			showPlacingFields();
			var sel = $('select[name="placing"]');
			toggleShowCases(sel[0]);
			$(sel[0]).change(
				function(){
					showPlacingFields();
					//toggleShowCases(this);
				}
			);
		}
	);
	
	function toggleShowCases(sel){
		if(sel.value == 'in_content' || sel.value == 'start' || sel.value == ''){
			$('#show_cases').hide();
		}else{
			$('#show_cases').show();
		}
	}
	
</script>