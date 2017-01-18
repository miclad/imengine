(function( $ ) {
	$.fn.animateNumber = function(val) {
	  	
		var formatValue = function(src){
			src = src.toString();
			var cnt = 1;
			var str = '';
			var lngth = 0;
			var res =  '';
			for(var i=src.length-1; i >= 0; i--){
				str += src.substr(i, 1);
				lngth++;
				if((src.length-i)%3 == 0 && i > 0){
					str += ' ';
					lngth++;
				}
			}	
			var t = '';
			for(var i=lngth-1; i>=0; i--){
				res += str.substr(i, 1);
			}
			return res;
		}
		
		var doAnimate = function(element){
			if(animate_array.counter >= steps_num){
				clearInterval(animate_array.timer);
				$(element).text(formatValue(animate_array.value) + (fract ? ',' + fract : ''));
				animate_array = null;					
				return;
			}
			animate_array.counter++;
			animate_array.cur += parseInt(animate_array.step);
			$(element).text(formatValue(animate_array.cur) + (cur_fract ? ',' + cur_fract : ''));
		}
		  
		var oThis = this;
		
		var cur_val_raw = this.text().replace(',', '.');
		var cur_fract_pos = cur_val_raw.indexOf('.')
		var cur_fract = cur_fract_pos > 0 ? cur_val_raw.substr(cur_fract_pos+1) : '';
		var cur_val = parseInt(this.text().replace(/\s/g, ''));
		
		var fract_pos = val.toString().indexOf('.');
		var fract = fract_pos > 0 ? val.toString().substr(fract_pos+1) : '';
		if((cur_fract == '' || parseInt(cur_fract) == 0) && (fract == '' || parseInt(fract) == 0))
			fract = '';
		var val = parseInt(val.toString().replace(/\s/g, ''));
		
		if(val == cur_val)
			return false;
			
		var steps_num = 10;
		var step = (val-cur_val)/10;
		if(step > 0)
			step = Math.floor(step);
		else
			step = Math.ceil(step);
			
		if(step == 0){
			step = 1;
			steps_num = val-cur_val;
			if(steps_num < 0){
				steps_num = -steps_num;
				step = -1;
			}
		}
		
		var animate_array = {
			'value': val, 
			'counter': 0, 
			'cur': cur_val, 
			'step': step,
			'timer': null
		};		
	
		animate_array.timer = setInterval(
			function(){
				doAnimate(oThis);
			},30
		);
	
	};
})( jQuery );