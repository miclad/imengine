$(document).ready(
	function(){
		
		initMagnificPopup();
		initForms();
		
		$('body').toggle('.b-toggle');
		
		$('.quantity').count();
		
		$('.affix').affix();
		
		/*
		$('.slider').each(function(){			
			var slider = new iSlider({'container': $(this)});			
		});
		*/
	   
	   $('.slider').each(function(){
			var elem = $(this);
			var data = elem.data();

			var slider = elem.royalSlider({
				arrowsNav: data.nav,
				arrowsNavAutoHide: false,
				controlNavigation: (data.labels || data.bullets ? 'bullets' : 'none'),
				bulletsText: data.labels,
				loop: true,
				slidesSpacing: data.spacing,
				navigateByClick: false,
				controlsInside: false,
				autoPlay: {
					enabled: data.auto,
					pauseOnHover: true,
					delay: data.auto ? data.interval : 0
				}
			});
		});
		
		// searchbar
		
		$('.navbar__search').on('click', 'button', function(evt){
			
			if(!$('.navbar__search').hasClass('s-open')){

				$('.navbar__search').addClass('s-open').find('input[type="text"]').focus();

				evt.preventDefault();
			
			}
			
		});
		
		$('body').on('click', function(evt){
			
			if(!$(evt.target).parents('.navbar__search').length){
				$('.navbar__search').removeClass('s-open');
			}
			
		});
		
		$('.tabs').iTabs();
		
		var rubbish = $('.rubbish').find('.icon');
		
		if(rubbish.length){
		
			var initial_scroll = $(window).scrollTop();

			$(window).scroll(function(){
				
				var new_pos = $(window).scrollTop();
		
				var delta = new_pos - initial_scroll;
				var direction = delta > 0 ? 'down' : 'up';
				initial_scroll = new_pos;		
				
				//console.log(direction);
				
				var ind = 1;
				
				$(rubbish).each(function(){
					
					var ct = $(this).css('top').replace('px', '');
					var cl = $(this).css('left').replace('px', '');
					var cb = $(this).css('bottom').replace('px', '');
					var cr = $(this).css('right').replace('px', '');
					
					var ch = Math.round(delta * 0.3);
					
					if(ct != 'auto')
						$(this).css({'top': parseInt(ct) - ch});
					
					if(cb != 'auto')
						$(this).css({'bottom': parseInt(cb) - ch});
					
					/*
					if(cl != 'auto')
						$(this).css({'left': parseInt(cl) + (offset > 0 ? -5 : 5)});
					
					if(cr != 'auto')
						$(this).css({'right': parseInt(cr) + (offset > 0 ? -5 : 5)});
					*/
				   
				   ind++;
					
				});
								

			});
		
		}
		
		$('.browse__open').on('click', function(evt){
			
			var container = $(this).parents('.browse');
			var popup = container.find('.browse__popup');
			
			if(!container.hasClass('s-open')){
				
				container.addClass('s-open');
				
			}else{
				
				container.removeClass('s-open')
				
			}
			
			evt.preventDefault();
			
		});
		
		$('body').on('click', function(evt){
			
			if(!$(evt.target).parents('.browse').length){
				$('.browse').removeClass('s-open');
			}
			
		});
		
		$('body').on('click', '.tooltip', function(evt){
			
			$.magnificPopup.open({
				items: {
					src: '<div class="popup-ajax-content" style="text-align: center;">' + $(this).data('content') + '</div>',
					type: 'inline'
				}
			});
			
			evt.preventDefault();
			
		})
		
	}
);

function initMagnificPopup(){
	
	$('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
		disableOn: 700,
		type: 'iframe',
		mainClass: 'mfp-fade'
	});
	
	$('.popup-image').magnificPopup({type:'image'});
	
	$('.popup-gallery').each(function() {
		$(this).magnificPopup({
			delegate: 'a',
			type: 'image',
			gallery:{enabled:true}
		});
	}); 
	
	$('.popup-ajax').magnificPopup({
		type: 'ajax',
		callbacks: {
			parseAjax: function(mfpResponse){
				mfpResponse.data = '<div class="popup-ajax-content">' + mfpResponse.data + '</div>';
			},
			ajaxContentAdded: function(){
				initMagnificPopup();
				initForms();				
			}
		}
	});
	
}

function formInputs(){
	$('input, textarea').focus(function(){
		$(this).removeClass('error');
	});
}

function initForms(){
	
	$.each($('form'), function(){
		
		var f = new iForm({form: $(this)});
		
	});
	
}

