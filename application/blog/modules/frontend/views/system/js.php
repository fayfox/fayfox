var system = {
	"base_url" : "<?php echo $this->url()?>",
	"current_user" : "<?php echo $this->session->get('id', 0)?>",
	"url" : function(router, params){
		if(router == undefined){
			return this.base_url;
		}else{
			if(params == undefined){
				params = {};
			}
			var url = this.base_url + router;
			$.each(params, function(i, e){
				url += "/" + i + "/" + e;
			});
			return url;
		}
	},
	"dateFormat" : function(timestamp){
		date = new Date(parseInt(timestamp) * 1000);
		return date.getFullYear() + '-' + (date.getMonth() + 1) + '-' +date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds();
	},
	"HTMLEncode" : function(str){
		var s = "";
		if (str == undefined || str.length == 0) return "";
		s = str.replace(/&/g, "&amp;");
		s = s.replace(/</g, "&lt;");
		s = s.replace(/>/g, "&gt;");
		s = s.replace(/ /g,"&nbsp;");
		s = s.replace(/\'/g, "&#39;");
		s = s.replace(/\"/g, "&quot;");
		s = s.replace(/\n/g, "<br>");
		return s;
	},
	"changeTwoDecimal" : function(x){
		var f_x = parseFloat(x);
		if (isNaN(f_x)){
			//alert('function:changeTwoDecimal->parameter error');
			return false;
		}
		var f_x = Math.round(x*100)/100;
		var s_x = f_x.toString();
		var pos_decimal = s_x.indexOf('.');
		if (pos_decimal < 0){
			pos_decimal = s_x.length;
			s_x += '.';
		}
		while (s_x.length <= pos_decimal + 2){
			s_x += '0';
		}
		return s_x;
	}
}