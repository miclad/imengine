function ajaxPager(params){

	var self = this;

	self.current_page = 1;
	self.items_counter = params.page_size_first;
	self.page_size = params.page_size;
	self.items_num = params.items_num;
	self.base_url = params.base_url;

	if(false && typeof(window.location.search.split('?')[1]) != 'undefined')
		self.url_params = '&' + window.location.search.split('?')[1];
	else 
		self.url_params = '';
	
	if(params.base_url) self.base_url = params.base_url;
	else self.base_url = window.location.toString() + '?page';
		
	if(params.pages_num) self.pages_num = params.pages_num;
	else self.pages_num = 0;
		
	self.container = $("#content");
	
	self.item = $(self.itemName);
	
	if(params.pager){
		
		$('.pagination__more a', params.pager).click(function(){			
			self.changeUrl();
			return false;
		});
		
		$('.pagination__all a', params.pager).click(function(){			
			self.showAll(this);
			return false;
		});
		
	}

	$(window).on({
		'hashchange load': function(){

			self.url = window.location.hash.replace(/#/, '');
			
			if(self.url.indexOf('show_all_pages') > -1){
				self.loadContent(true);
				self.url = '?page=1' + self.url_params;
			}else{
			
				var page_number = 0;

				var re = /page\=([0-9]+)/;
				var check = re.exec(self.url);
				if(check && check.length == 2){
					page_number = parseInt(check[1]);
				}

				if(page_number > 0){

					self.pager = $(params.pager);

					if(self.current_page < page_number){

						for(var i = self.current_page+1; i <= page_number; i++){

							self.url = self.url.replace(re, 'page=' + i);
							self.loadContent();

						}

					}

				}
				
			}

		}
	})
}

ajaxPager.prototype = {

	loadContent: function(all){
		var self = this;
		var ur = self.url;

		var lo = new Loader('.pagination', all ? $('.pagination__all a') : $('.pagination__more a'), 'Загружаем...');
		
		if(location.search){
			ur = location.search + '&' + ur;
		}else{
			ur = '?' + ur;
		}
		
		ur = location.pathname + ur;

		if(!self.isFinalPage()){
			$.get(
				ur, 
				{}, 
				function(rsp){
					
					if(all){
						$('.pagination').remove();
					}

					lo.remove();

					self.container.append(rsp);
					self.current_page++;

					if(self.isFinalPage()){
						self.pager.remove();
						return false;
					}

					self.checkCount();
					
					self.container.find('.f-count').each(function(){
						Forms.count($(this), ".f-count");
					});
				}
			);
		}
		
	},
	
	showAll: function(lnk){
		var self = this;

		var url = 'show_all_pages=1&page_offset=' + (this.current_page+1) + self.url_params;		
		this.changeUrl(url);
	},
	
	isFinalPage: function(){
		return this.current_page >= this.pages_num;
	},
	
	buildRequestUrl: function(pages_num){
		var self = this;
		return 'page=' + pages_num;
	},

	changeUrl: function(url){
		var self = this;
		var url_prefix = '';
		if(typeof(self.base_url.split('?')[1]) != 'undefined')
			url_prefix = self.base_url.split('?')[1];
		self.url = '?' + url_prefix + (url_prefix.length > 0 ? '&' : '') + (url ? url : self.buildRequestUrl(self.current_page+1));
		window.location.hash = url ? url : self.buildRequestUrl(self.current_page+1);//self.url;
	},

	checkCount: function(){
		var self = this;
		var items = $(self.itemName).length;
		var url = window.location.hash.split('page=')[1];

		if(items >= self.items_num) self.pager.hide();
		else {
			self.pager.show();

			self.items_counter = url*self.page_size;
			var rest = self.items_num - self.items_counter;
			rest = rest < 0 ? 0 : rest;

			$(".pagination__more").find('span').text(rest < self.page_size ? rest : self.page_size);
		}
	}

}

ajaxPager.init = function(){
	
	$('.pagination').each(function(){
		
		var p = new ajaxPager({
			'pager': $(this),
			'pages_num': $(this).data('pages_num'),
			'items_num': $(this).data('items_num'),
			'page_size': $(this).data('page_size'),
			'page_size_first': $(this).data('pages_size_first'),
			'base_url': $(this).data('base_url'),
			'itemName': '.b-catalog__item'
		});
		
	});
	
}

$(document).ready(function(){
	
	ajaxPager.init();
	
})