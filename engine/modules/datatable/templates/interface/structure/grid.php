
	
	<?foreach($GLOBALS['navi_types'] as $navi_type){?>
		<div class="grid grid--tree">
			<form action="<?=$dt->baseUrl?>&act=update_all" method="post">		
		
				<div class="navi_type_title grid__separator" data-target="<?=$navi_type[0]?>">
					<table class="table table-hover">
						<thead>
							<th class="sort">
								<?if(isset($dt->fields["pos"])){?>
									<a href="#"><i class="fa fa-sort"></i></a>
								<?}?>
							</th>
							<th>
								<?=$navi_type[1]?>
							</th>
						</thead>
					</table>
				</div>

				<div<?if(false && !isset($_COOKIE['tree_structure_open_navi_'.$navi_type[0]])){?> style="display: none;"<?}?> id="navi_type_<?=$navi_type[0]?>">
					<?
					echo showPages(0, $navi_type[0], 0, $dt);
					?>
				</div>
				
				<div class="grid__submit">		
					<button type="submit" class="btn"><span class="fa fa-save"></span> Сохранить</button>
				</div>
		
			</form>
		</div>
	<?}?>
	
	<?if(!count($GLOBALS['navi_types'])){?>
		<?
		echo showPages(0, '', 0, $dt);
		?>
	<?}?>

<script type="text/javascript">
	
	$(function(){
		
		initToggleChildren();
		
	});
	
	function initToggleChildren(){
		
		$('.children-toggle').unbind('click').bind('click', function(){
			
			var container = $(this).parents('tr').eq(0);
			
			var row = $(this).parents('tr').eq(0);
			var child_obj = $('.parent' + parseInt(row.data('id')));
			
			if(!child_obj.length){
				
				var oThis = this;
				$(this).parent().next().append('&nbsp; <img src="/engine/templates/admin/img/ajax-loader.gif"/>');
			
				$.post(
					'/engine/admin/index.php?module=datatable&entity=structure&action=load_children&parent=' + $(this).data('id') + '&level=' + $(this).data('level'),
					{},
					function(rsp){
						//alert(rsp);
						$(container).after(rsp);
						initToggleChildren();
						$(oThis).parent().next().find('img').remove();
					}
				);
	
			}else{
		
				$('.parent' + parseInt(row.data('id'))).toggleClass('hide');
				
			}
			
			var icon = $(this).find('i');
			
			$(this).toggleClass('fa-folder').toggleClass('fa-folder-open');

			if($(this).hasClass('fa-folder-open')){
				$.cookieStorage.set('tree_structure_open_' + row.data('id'), 1);
			}else{
				$.cookieStorage.remove('tree_structure_open_' + row.data('id'));
			}

			return false;

		});
		
	}
	
	/*
	$('.navi_type_title').click(function(evt){
		
		var target = $(this).data('target');
		$('#navi_type_' + target).toggle();
		
		if($('#navi_type_' + target).is(':visible')){
			$.cookieStorage.set('tree_structure_open_navi_' + target, 1);
		}else{
			$.cookieStorage.remove('tree_structure_open_navi_' + target);
		}
		
		evt.preventDefault();
		
	});
	*/
	
</script>