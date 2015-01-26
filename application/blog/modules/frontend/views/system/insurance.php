var insurance = {
	options : false,
	cities : <?php echo json_encode($cities)?>,
	household_type : {
		localtown : <?php echo InsuranceOptions::TYPE_LOCAL_TOWN?>,
		localagri : <?php echo InsuranceOptions::TYPE_LOCAL_AGRI?>,
		outertown : <?php echo InsuranceOptions::TYPE_OUTER_TOWN?>,
		outeragri : <?php echo InsuranceOptions::TYPE_OUTER_AGRI?>
	},
	getPre : function(household_type){
		var pre = '';
		if(household_type == insurance.household_type.localtown){
			pre = 'localtown_';
		}else if(household_type == insurance.household_type.localagri){
			pre = 'localagri_';
		}else if(household_type == insurance.household_type.outertown){
			pre = 'outertown_';
		}else if(household_type == insurance.household_type.outeragri){
			pre = 'outeragri_';
		}
		return pre;
	},
	init : function(){
		$("#province").append('<option value="">请选择</option>');
		$.each(insurance.cities, function(i, n){
			if(n.parent == 0){
				$("#province").append('<option value="'+n.id+'">'+n.city+'</option>');
			}
		});
		$("#province").change(function(){
			insurance.setCities($(this).val());
		});
		$("#city").change(function(){
			insurance.getOptions($(this).val());
		});
	},
	setCities : function(province_id, city_id, callback){
		$("#city").html('');
		$.each(insurance.cities, function(i, n){
			if(n.parent == province_id){
				if(n.id == city_id){
					$("#city").append('<option value="'+n.id+'" selected="selected">'+n.city+'</option>');
				}else{
					$("#city").append('<option value="'+n.id+'">'+n.city+'</option>');
				}
			}
		});
		//alert(city_id);
		if(city_id != undefined){
			insurance.getOptions(city_id, callback);
		}else{
			setTimeout(function(){insurance.getOptions($("#city").val(), callback);}, 1);
		}
	},
	getOptions : function(city_id, callback){
		$.ajax({
			type: "GET",
			url: system.url("insurance/get_options"),
			data: {"id":city_id},
			dataType: "json",
			cache: false,
			success: function(data){
				insurance.options = data;
				insurance.showOptions(data);
				if(callback != undefined){
					callback();
				}
			}
		});
	},
	getLowest : function(household_type){
		var pre = insurance.getPre(household_type);
		var data = [insurance.options[pre+'pension_low'], insurance.options[pre+'medicare_low'], 
			insurance.options[pre+'injury_low'], insurance.options[pre+'maternity_low'], insurance.options[pre+'unemployment_low']];
		var lowest = 0;
		$.each(data, function(i, n){
			if((lowest == 0 || n < lowest) && n != 0){
				lowest = n;
			}
		});
		return lowest;
	},
	getHighest : function(household_type){
		var pre = insurance.getPre(household_type);
		var data = [insurance.options[pre+'pension_high'], insurance.options[pre+'medicare_high'], 
			insurance.options[pre+'injury_high'], insurance.options[pre+'maternity_high'], insurance.options[pre+'unemployment_high']];
		var highest = -1;
		$.each(data, function(i, n){
			if(n == 0){//无上限
				highest = 0;
			}
			if(parseFloat(n) > highest && highest != 0){
				highest = parseFloat(n);
			}
		});
		return highest;
	},
	count : function(){
		var pre = insurance.getPre($("input[name='household_type']:checked").val());
		if(insurance.options == false){
			insurance.showError("无数据");
			return false;
		}
		var input = parseFloat($("input[name='base']").val());
		if(isNaN(input)){
			insurance.showError("基数异常");
		}
		var lowest = insurance.getLowest($("input[name='household_type']:checked").val());
		var highest = insurance.getHighest($("input[name='household_type']:checked").val());
		if(input < lowest){
			insurance.showError("低于最低值");
			return false;
		}
		if(highest != 0 && input > highest){
			insurance.showError("高于最大值");
			return false;
		}
		//养老
		if(input > insurance.options[pre+'pension_low']){
			if(insurance.options[pre+'pension_high'] != 0 && input > insurance.options[pre+'pension_high']){
				var pension_base = insurance.options[pre+'pension_high'];
			}else{
				var pension_base = input;
			}
		}else{
			var pension_base = insurance.options[pre+'pension_low'];
		}
		var pension_unit = system.changeTwoDecimal(pension_base * insurance.options[pre+'pension_unit'] / 100);
		var pension_indi = system.changeTwoDecimal(pension_base * insurance.options[pre+'pension_indi'] / 100);
		//医疗
		if(input > insurance.options[pre+'medicare_low']){
			if(insurance.options[pre+'medicare_high'] != 0 && input > insurance.options[pre+'medicare_high']){
				var medicare_base = insurance.options[pre+'medicare_high'];
			}else{
				var medicare_base = input;
			}
		}else{
			var medicare_base = insurance.options[pre+'medicare_low'];
		}
		var medicare_unit = system.changeTwoDecimal(medicare_base * insurance.options[pre+'medicare_unit'] / 100 + parseFloat(insurance.options[pre+'medicare_unit_extra']));
		var medicare_indi = system.changeTwoDecimal(medicare_base * insurance.options[pre+'medicare_indi'] / 100 + parseFloat(insurance.options[pre+'medicare_indi_extra']));
		//失业
		if(input > insurance.options[pre+'unemployment_low']){
			if(insurance.options[pre+'unemployment_high'] != 0 && input > insurance.options[pre+'unemployment_high']){
				var unemployment_base = insurance.options[pre+'unemployment_high'];
			}else{
				var unemployment_base = input;
			}
		}else{
			var unemployment_base = insurance.options[pre+'unemployment_low'];
		}
		var unemployment_unit = system.changeTwoDecimal(unemployment_base * insurance.options[pre+'unemployment_unit'] / 100);
		var unemployment_indi = system.changeTwoDecimal(unemployment_base * insurance.options[pre+'unemployment_indi'] / 100);
		//工伤
		if(input > insurance.options[pre+'injury_low']){
			if(insurance.options[pre+'injury_high'] != 0 && input > insurance.options[pre+'injury_high']){
				var injury_base = insurance.options[pre+'injury_high'];
			}else{
				var injury_base = input;
			}
		}else{
			var injury_base = insurance.options[pre+'injury_low'];
		}
		var injury_unit = system.changeTwoDecimal(injury_base * insurance.options[pre+'injury_unit'] / 100);
		//生育
		if(input > insurance.options[pre+'maternity_low']){
			if(insurance.options[pre+'maternity_high'] != 0 && input > insurance.options[pre+'maternity_high']){
				var maternity_base = insurance.options[pre+'maternity_high'];
			}else{
				var maternity_base = input;
			}
		}else{
			var maternity_base = insurance.options[pre+'maternity_low'];
		}
		var maternity_unit = system.changeTwoDecimal(maternity_base * insurance.options[pre+'maternity_unit'] / 100);

		var disability = parseFloat(insurance.options['disability_base']) * parseFloat(insurance.options['disability_unit']) / 100;
		var unit_total = system.changeTwoDecimal(parseFloat(pension_unit) + parseFloat(medicare_unit) + parseFloat(unemployment_unit) + parseFloat(injury_unit) + parseFloat(maternity_unit) + disability);
		var indi_total = system.changeTwoDecimal(parseFloat(pension_indi) + parseFloat(medicare_indi) + parseFloat(unemployment_indi));
		
		return {
			"pension_unit":pension_unit,
			"pension_indi":pension_indi,
			"medicare_unit":medicare_unit,
			"medicare_indi":medicare_indi,
			"unemployment_unit":unemployment_unit,
			"unemployment_indi":unemployment_indi,
			"injury_unit":injury_unit,
			"maternity_unit":maternity_unit,
			"unit_total":unit_total,
			"indi_total":indi_total,
			"disability":system.changeTwoDecimal(disability)
		};
	},
	"showError" : function(msg){
		alert(msg);
	},
	"showOptions":function(options){}
}