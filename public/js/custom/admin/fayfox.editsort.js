jQuery.extend({
	feditsort_timeout:null
});
jQuery.fn.extend({
	feditsort: function(options){
		var defaults = {
			'timeout':200
		}
		$.each(options, function(i, n){
			defaults[i] = n;
		});
		var o = this;
		$(o).keydown(function(event){
			if((event.keyCode >= 48 && event.keyCode <= 57) || //大键盘0-9
				(event.keyCode >= 96 && event.keyCode <= 105 || //小键盘0-9
				event.keyCode == 8 || //退格键
				event.keyCode == 46)){//Delete
				
				clearTimeout($.feditsort_timeout);
				$(this).next('img').remove();
				$(this).after('<img src="'+system.url()+'images/throbber.gif" />');
				$.feditsort_timeout = setTimeout((function(o){
					return function(){
						$(o).next('img').remove();
						$(o).after('<img src="'+system.url()+'images/throbber.gif" />');
						$.ajax({
							type: "GET",
							url: defaults.url,
							data: {
								"id":$(o).attr("data-id"),
								"sort":$(o).val()
							},
							dataType: "json",
							cache: false,
							success: function(resp){
								if(resp.status){
									if(parseInt($(o).val()) == resp.sort){
										$(o).val(resp.sort)
											.next("img").attr("src", system.url() + "images/tick-circle.png")
											.attr('title', '');
									}else{
										$(o).val(resp.sort)
											.next("img").attr("src", system.url() + "images/exclamation.png")
											.attr('title', '排序字段取值为0-65535之间');;
									}
								}else{
									$(o).next("img").attr("src", system.url() + "images/cross-circle.png")
										.attr('title', resp.message);
									alert(resp.message);
								}
							}
						});
					}
				})(this), 500);
			}else if((event.keyCode >= 37 && event.keyCode <= 40) ||//方向键
				event.keyCode == 116){//F5
				//无操作
			}else{
				return false;
			}
		});
	}
});