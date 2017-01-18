function toggleBlock(id, link, animate, t_class, animate_param, opened_text, closed_text){
	if(!animate_param){
		animate_param = 'height';
	}
	
	var el = typeof(id) == 'object' ? id : $('#' + id);
	
	var res = 1;
	if(t_class){
		$(link).toggleClass(t_class);
	}
	if($(el).css('display') == 'none'){

		if(animate){
			if(animate_param == 'height')
				$(el).animate({'height': 'show'}, 'fast');
			else
				$(el).animate({'width': 'show'}, 'fast');
		}else
			$(el).show();
		
		if(opened_text && link){
			link.innerHTML = opened_text;
		}
			
	}else{
		if(animate){
			if(animate_param == 'height')
				$(el).animate({'height': 'hide'}, 'fast');
			else
				$(el).animate({'width': 'hide'}, 'fast');
		}else
			$(el).hide();
			
		if(closed_text && link){
			link.innerHTML = closed_text;
		}
	}
	if(link)
		link.blur();
	return false;
	
}

function ajaxLoadContent(url, params, content_id, loader_id, lnk, text_closed, text_opened, animate, loader){
	var cnt = document.getElementById(content_id);
	if(cnt.innerHTML == ''){
		if(!loader)
			 loader = '/img/ajax-loader.gif';
		$('#' + loader_id).html('&nbsp;<img src="' + loader + '" align="absmiddle"/>');
		$.get(
			url, 
			params, 
			function(response){
				onAjaxContentLoaded(response, content_id, loader_id, lnk, text_closed, text_opened, animate);
			}
		);
	}else{
		toggleBlock(content_id, lnk, true, animate, '', text_closed, text_opened);
	}
}

function onAjaxContentLoaded(text, content_id, loader_id, lnk, text_closed, text_opened, animate){
	$('#' + content_id).html(text);
	$('#' + loader_id).html('');
	toggleBlock(content_id, lnk, animate, '', '', text_closed, text_opened);
}