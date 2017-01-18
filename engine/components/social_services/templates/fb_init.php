<div id="fb-root"></div>
<script>
	
	window.fbAsyncInit = function() {
		
		FB.init({
			appId      : <?=DBCommon::getConfig('facebook_id')?>,
			status     : true,
			cookie     : true,
			xfbml      : true
		});
          
		FB.Event.subscribe('edge.create', function(rsp){
			var comment_id = detectCommentId(rsp);
			if(comment_id)
				updateCommentRating(comment_id, 1);
		});
		
		FB.Event.subscribe('edge.remove', function(rsp){
			var comment_id = detectCommentId(rsp);
			if(comment_id)
				updateCommentRating(comment_id, -1);
		});
          
	};
        
	(function(d){
		var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement('script'); js.id = id; js.async = true;
		js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1";
		ref.parentNode.insertBefore(js, ref);
	}(document));
	
</script>