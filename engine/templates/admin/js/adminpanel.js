function adminPanel(){
	
	this.init();
	
}

adminPanel.prototype = {
	
	init: function(){
		
		this.initSidebarHeight();
		this.initTable();		
		this.initSidenav();
		this.initAutocomplete();
		this.initSwitchablePanels();
		this.initSortMode();
		this.initAlert();
		
	},
	
	initSidebarHeight: function(){
		
		$(window).on({
			'load resize': function(){

				var wh = parseInt($(window).height());
				var body_h = parseInt($('body').height());
				$('.sidebar').css({'min-height': (wh > body_h ? wh : body_h) - 80})

			}
		})
		
	},
	
	initTable: function(){
		
		$('.table').on('click', '.datarow.with_url', function(evt){
			var par = $(evt.target).parent();			
			if(!$(evt.target).data('is-control') && !par.hasClass('control') && evt.target.tagName.toLowerCase() != 'input' && evt.target.tagName.toLowerCase() != 'a' && par.get(0).tagName.toLowerCase() != 'a'){
				location.href = $(this).data('url');
			}
		});
		
		this.deleteConfirm();
		this.datarowControls();
		
	},
	
	deleteConfirm: function(){
		
		var links = $('a[href*="act=delete"]');
		
		links.on('click', function(){
			
			return confirm('Вы действительно хотите удалить запись?');
			
		})
		
	},
	
	datarowControls: function(){
		
		if($('.table').length){
		
			$('.datarow').on('click', '.controls__open', function(evt){

				var parent = $(this).parents('.controls');
				var row = parent.parent();

				$('.controls.s-open').not(parent).removeClass('s-open').parent().removeClass('selected');
				parent.toggleClass('s-open');
				row.toggleClass('selected')

				evt.preventDefault();

			});

			$('body').on('click', function(evt){

				var el = $(evt.target);
				if(!el.hasClass('controls') && !el.parents('.controls').length){
					$('.controls.s-open').removeClass('s-open').parent().removeClass('selected');
				}

			})
		
		}
		
	},
	
	initSidenav: function(){
		
		$('.sidebar .nav__group-title').on('click', function(){
			var ch = $(this).next();
			if(ch.length && !ch.is(':visible')){
				$(this).parent().addClass('s-active').siblings().removeClass('s-active').find('ul');
				return false;
			}
		});
		
	},
	
	initAutocomplete: function(){
		
		$('input[data-autocomplete]').each(function(){
			
			var inp = $(this);
			var entity = inp.data('autocomplete');
			var field = inp.data('autocomplete-field');
			var id_field = inp.data('autocomplete-id-field');
			
			inp.autocomplete({
				source: '/engine/admin/index.php?module=datatable&section=autocomplete&entity=' + entity + '&field=' + field,
				minLength: 1,
				select: function(event, ui){
					$('input[name="' + id_field + '"]').val(ui.item ? ui.item.id : 0);
				},
				change: function(event, ui){
					$('input[name="' + id_field + '"]').val(0);
				}
			})
			
		});
		
	},
	
	initSwitchablePanels: function(){
		
		$('[data-role="sw-panels-controls"] a').on('click', function(evt){
			
			var id = $(this).data('panel-id');
			var container = $('#swp-' + id);
			var siblings = container.siblings();
			var siblings_visible = siblings.filter(':visible');
			
			$(this).parent().toggleClass('s-active');
			
			if(!container.is(':visible')){
				container.slideDown(siblings_visible.length ? 0 : 300);
				if(siblings_visible.length){
					siblings_visible.hide();
					$(this).parent().siblings('.s-active').removeClass('s-active');
				}
			}else{
				container.slideUp();
			}
			
			evt.preventDefault();
			
		});
		
	},
	
	initSortMode: function(){
		
		$('.table thead .sort a').on('click', function(evt){
			
			$(this).parents('.grid').toggleClass('sort-mode');
			evt.preventDefault();
			
		});
		
	},
	
	initAlert: function(){
		
		$('body').on('click', '.alert .close', function(){

			$(this).parents('.alert').fadeOut();
			
		});
		
	}
	
}

