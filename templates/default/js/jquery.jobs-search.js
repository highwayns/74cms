function allaround(dir,getstr){
	var checkedstr = "";
	fillTrad("#divTradCate"); // ��ҵ�������
	// �ָ���ҵѡ������
	if(getstr) {
		if(getstr[0]) {
			var recoverTradArray = getstr[0].split(",");
			$.each(recoverTradArray, function(index, val) {
				 $("#tradList a").each(function() {
					if(val == $(this).attr('cln')) {
						$(this).addClass('selectedcolor');
					}
				});
			});
			copyTradItem();
			var a_cn = new Array();
			$("#tradAcq a").each(function(index) {
				var checkText = $(this).attr('title');
				a_cn[index]=checkText;
			});
			$("#tradText").html(a_cn.join(","));
			$("#tradText").css("color","#4095ef");
			$("#jobsTrad").css("border-color","#4095ef");
			checkedstr += '<a href="javascript:;" ty="trade_id" class="cnt"><span>'+$("#tradText").html()+'</span><i class="del"></i></a>';
		}
	}
	/* ��ҵ�б�����ʾ����ѡ */
	$("#tradList li a").unbind().live('click', function() {
		// �ж�ѡ��������Ƿ񳬳�
		if($("#tradList .selectedcolor").length >= 5) {
			$("#tradropcontent").show(0).delay(3000).fadeOut("slow");
		} else {
			$(this).addClass('selectedcolor');
			copyTradItem(); // ����ҵ��ѡ�Ŀ���
		}
	});
	// ��ҵȷ��ѡ��
	$("#tradSure").unbind().click(function() {
		var a_cn=new Array();
		var a_id=new Array();
		$("#tradAcq a").each(function(index) {
			var checkID = $(this).attr('rel');
			var checkText = $(this).attr('title');
			a_id[index]=checkID;
			a_cn[index]=checkText;
		});
		if (a_cn.length > 0) {
			$("#tradText").html(a_cn.join(","));
			$("#tradText").css("color","#4095ef");
			$("#jobsTrad").css("border-color","#4095ef");
			$("#trade_cn").val(a_cn.join(","));
			$("#trade_id").val(a_id.join(","));
		} else {
			$("#tradText").html("��ѡ����ҵ���");
			$("#tradText").css("color","#cccccc");
			$("#jobsTrad").css("border-color","#cccccc");
			$("#trade_cn").val("");
			$("#trade_id").val("");
		}
		$("#divTradCate").hide();
	});
	fillJobs("#divJobCate"); // ְλ�������
	// �ָ�ְλѡ������
	if(getstr) {
		if(getstr[1]) {
			var recoverJobArray = getstr[1].split(",");
			$.each(recoverJobArray, function(index, val) {
				 var demojobArray = val.split(".");
				 if(demojobArray[2] == "0") { // ���������������0 ��ֻѡ���ڶ���
				 	$(".jobcatebox p a").each(function() {
				 		if(demojobArray[1] == $(this).attr("rcoid")) {
				 			$(this).addClass('selectedcolor');
				 		}
				 	});
				 } else {
				 	$(".jobcatebox .subcate a").each(function() {
				 		if(demojobArray[2] == $(this).attr("rcoid")) {
				 			$(this).addClass('selectedcolor');
				 		}
				 	});
				 }
			});
			copyJobItem();
			var a_cn=new Array();
			$("#jobAcq a").each(function(index) {
				var checkText = $(this).attr('title');
				a_cn[index]=checkText;
			});
			$("#jobText").html(a_cn.join(","));
			$("#jobText").css("color","#4095ef");
			$("#jobsSort").css("border-color","#4095ef");
			checkedstr += '<a href="javascript:;" ty="jobs_id" class="cnt"><span>'+$("#jobText").html()+'</span><i class="del"></i></a>';
		}
	}
	/* ְλ�б�����ʾ����ѡ */
	$("#divJobCate li p a").unbind().live('click', function() {
		// �ж�ѡ��������Ƿ񳬳�
		if($("#divJobCate .selectedcolor").length >= 5) {
			$("#jobdropcontent").show(0).delay(3000).fadeOut("slow");
		} else {
			$(this).addClass('selectedcolor');
			copyJobItem(); // ��ְλ��ѡ�Ŀ���
		}
	});
	$("#divJobCate .subcate a").unbind().live('click', function() {
		// �ж�ѡ��������Ƿ񳬳�
		if($("#divJobCate .selectedcolor").length >= 5) {
			$("#jobdropcontent").show(0).delay(3000).fadeOut("slow");
		} else {
			if($(this).attr("p") == "qb") {
				$(this).parent().prev().find('font a').addClass('selectedcolor');
				$(this).parent().find('a').removeClass('selectedcolor');
			} else {
				$(this).parent().prev().find('font a').removeClass('selectedcolor');
				$(this).addClass('selectedcolor');
			}
			copyJobItem(); // ��ְλ��ѡ�Ŀ���
		}
	});
	// ְλȷ��ѡ��
	$("#jobSure").unbind().click(function() {
		var a_cn=new Array();
		var a_id=new Array();
		$("#jobAcq a").each(function(index) {
			// ���ѡ����Ƕ������ཫ������������0
			var chid = new Array();
			if($(this).attr('pid')) {
				chid = $(this).attr('pid').split(".");
				if(chid.length < 3) {
					chid.push(0);
				}
			}
			var checkID = chid.join(".");
			var checkText = $(this).attr('title');
			a_id[index]=checkID;
			a_cn[index]=checkText;
		});
		if (a_cn.length > 0) {
			$("#jobText").html(a_cn.join(","));
			$("#jobText").css("color","#4095ef");
			$("#jobsSort").css("border-color","#4095ef");
			$("#jobs_cn").val(a_cn.join(","));
			$("#jobs_id").val(a_id.join(","));
		} else {
			$("#jobText").html("��ѡ��ְλ���");
			$("#jobText").css("color","#cccccc");
			$("#jobsSort").css("border-color","#cccccc");
			$("#jobs_cn").val("");
			$("#jobs_id").val("");
		}
		$("#divJobCate").hide();
	});
	fillCity("#divCityCate"); // �����������
	// �ָ�����ѡ������
	if(getstr) {
		if(getstr[2]) {
			var recoverCityArray = getstr[2].split(",");
			$.each(recoverCityArray, function(index, val) {
				 var democityArray = val.split(".");
				 if(democityArray[1] == 0) { // ����ڶ�������Ϊ 0 ˵��ѡ�����һ������
				 	$(".citycatebox p a").each(function() {
				 		if(democityArray[0] == $(this).attr("rcoid")) {
				 			$(this).addClass('selectedcolor');
				 		}
				 	});
				 } else { // ѡ����Ƕ�������
				 	$(".citycatebox .subcate a").each(function() {
				 		if(democityArray[1] == $(this).attr("rcoid")) {
				 			$(this).addClass('selectedcolor');
				 		}
				 	});
				 }
			});
			copyCityItem();
			var a_cn=new Array();
			$("#cityAcq a").each(function(index) {
				var checkText = $(this).attr('title');
				a_cn[index]=checkText;
			});
			$("#cityText").html(a_cn.join(","));
			$("#cityText").css("color","#4095ef");
			$("#jobsCity").css("border-color","#4095ef");
			checkedstr += '<a href="javascript:;" ty="district_id" class="cnt"><span>'+$("#cityText").html()+'</span><i class="del"></i></a>';
		}
	}
	/* �����б�����ʾ����ѡ */
	$("#divCityCate li p a").unbind().live('click', function(){
		// �ж�ѡ��������Ƿ񳬳�
		if($("#divCityCate .selectedcolor").length >= 5) {
			$("#citydropcontent").show(0).delay(3000).fadeOut("slow");
		} else {
			$(this).addClass('selectedcolor');
			copyCityItem(); // ��������ѡ�Ŀ���
		}
	});
	$("#divCityCate .subcate a").unbind().live('click', function() {
		// �ж�ѡ��������Ƿ񳬳�
		if($("#divCityCate .selectedcolor").length >= 5) {
			$("#citydropcontent").show(0).delay(3000).fadeOut("slow");
		} else {
			if($(this).attr("p") == "qb") {
				$(this).parent().prev().find('font a').addClass('selectedcolor');
				$(this).parent().find('a').removeClass('selectedcolor');
			} else {
				$(this).parent().prev().find('font a').removeClass('selectedcolor');
				$(this).addClass('selectedcolor');
			}
			copyCityItem(); // ��������ѡ�Ŀ���
		}
	});
	// ����ȷ��ѡ��
	$("#citySure").unbind().click(function() {
		var a_cn=new Array();
		var a_id=new Array();
		$("#cityAcq a").each(function(index) {
			// ���ѡ�����һ���������ڶ��������� 0
			var chid = new Array();
			if($(this).attr('pid')) {
				chid = $(this).attr('pid').split(".");
				if(chid.length < 2) {
					chid.push(0);
				}
			}
			var checkID = chid.join(".");
			var checkText = $(this).attr('title');
			a_id[index]=checkID;
			a_cn[index]=checkText;
		});
		if (a_cn.length > 0) {
			$("#cityText").html(a_cn.join(","));
			$("#cityText").css("color","#4095ef");
			$("#jobsCity").css("border-color","#4095ef");
			$("#district_cn").val(a_cn.join(","));
			$("#district_id").val(a_id.join(","));
		} else {
			$("#cityText").html("��ѡ���������");
			$("#cityText").css("color","#cccccc");
			$("#jobsCity").css("border-color","#cccccc");
			$("#district_cn").val("");
			$("#district_id").val("");
		}
		$("#divCityCate").hide();
	});
	// ����ؼ���������
	$("#searckey").focus(function() {
		if($(this).val() == "������ؼ���") {
			$(this).val('');
		}
	}).blur(function() {
		if($(this).val() == "") {
			$(this).val('������ؼ���');
		}
	});
	// ����������
	showOption("#jobswage","wage",QS_wage);	// ְλ��н
	// �ָ�ְλ��нѡ������
	if(getstr) {
		if(getstr[3]) {
			$("#searoptions").show();
			var wagestr = "";
			$("#jobswage a").each(function() {
				$(this).removeClass('selc');
				var demoWageArray = $(this).attr('id').split('-');
				if(getstr[3] == demoWageArray[1]) {
					$(this).addClass('selc');
					wagestr = $(this).html();
				}
			});
			checkedstr += '<a href="javascript:;" ty="wage" class="cnt"><span>'+wagestr+'</span><i class="del"></i></a>';
		}
	}
	showOption("#jobseducation","education",QS_education);	//ѧ��Ҫ��
	// �ָ�ѧ��Ҫ��ѡ������
	if(getstr) {
		if(getstr[4]) {
			$("#searoptions").show();
			var edustr = "";
			$("#jobseducation a").each(function() {
				$(this).removeClass('selc');
				var demoEduArray = $(this).attr('id').split('-');
				if(getstr[4] == demoEduArray[1]) {
					$(this).addClass('selc');
					edustr = $(this).html();
				}
			});
			checkedstr += '<a href="javascript:;" ty="education" class="cnt"><span>'+edustr+'</span><i class="del"></i></a>';
		}
	}
	showOption("#jobsexperience","experience",QS_experience); // ��������
	// �ָ���������ѡ������
	if(getstr) {
		if(getstr[5]) {
			$("#searoptions").show();
			var expstr = "";
			$("#jobsexperience a").each(function() {
				$(this).removeClass('selc');
				var demoExpArray = $(this).attr('id').split('-');
				if(getstr[5] == demoExpArray[1]) {
					$(this).addClass('selc');
					expstr = $(this).html();
				}
			});
			checkedstr += '<a href="javascript:;" ty="experience" class="cnt"><span>'+expstr+'</span><i class="del"></i></a>';
		}
	}
	showOption("#jobsnature","nature",QS_jobsnature);	// ��������
	// �ָ���������ѡ������
	if(getstr) {
		if(getstr[6]) {
			$("#searoptions").show();
			var naturestr = "";
			$("#jobsnature a").each(function() {
				$(this).removeClass('selc');
				var demoNatureArray = $(this).attr('id').split('-');
				if(getstr[6] == demoNatureArray[1]) {
					$(this).addClass('selc');
					naturestr = $(this).html();
				}
			});
			checkedstr += '<a href="javascript:;" ty="nature" class="cnt"><span>'+naturestr+'</span><i class="del"></i></a>';
		}
	}
	// �ָ�����ʱ��ѡ������
	if(getstr) {
		if(getstr[7]) {
			$("#searoptions").show();
			var timestr = "";
			$("#jobsuptime a").each(function() {
				$(this).removeClass('selc');
				var demoTimeArray = $(this).attr('id').split('-');
				if(getstr[7] == demoTimeArray[1]) {
					$(this).addClass('selc');
					timestr = $(this).html();
				}
			});
			checkedstr += '<a href="javascript:;" ty="settr" class="cnt"><span>'+timestr+'</span><i class="del"></i></a>';
		}
	}
	// �ؼ�����ʾ����ѡ��
	if($("#searckey").attr("data")) {
		checkedstr += '<a href="javascript:;" ty="key" class="cnt"><span>'+$("#searckey").attr("data")+'</span><i class="del"></i></a>';
	}
	// �����ʾ��������
	$("#showmoreoption").unbind().click(function(){
		if($("#searoptions").css('display') == "none") {
			$(this).find('span').html("�������");
			$(this).find('i').addClass('sq');
			$("#searoptions").show();
		} else {
			$(this).find('span').html("��������");
			$(this).find('i').removeClass('sq');
			$("#searoptions").hide();
		}
	});
	// ���������ť
	$("#btnsearch").unbind().click(function() {
		search_location();
	});
	// ��������ѡ��ĵ��
	$("#searoptions .opt").click(function(){
		var opt=$(this).attr('id');
		opt=opt.split("-");
		$("#searckeybox input[name="+opt[0]+"]").val(opt[1]);
		search_location();
	});
	// ְλ�б�ҳ��ѡ����������ʾ
	if(checkedstr != "") {
		$("#showselected").html(checkedstr);
		$("#jobselected").show();
	}
	$("#showselected .cnt").click(function(){
		var opt=$(this).attr('ty');
		$("#searckeybox input[name="+opt+"]").val('');
		setTimeout(function() {
			search_location();
		}, 1);
	});
	$("#clearallopt").click(function(){
		$("#searckeybox input[type='hidden']").val('');
		$("#searckeybox input[name='key']").val('');
		setTimeout(function() {
			search_location();
		}, 1);
	});
	// ������ת
	function search_location() {
		var key=$("#searckeybox input[name=key]").val();
		if($("#searckeybox input[name=key]").val() == "������ؼ���") {
			key = '';
		}
		var trade=$("#searckeybox input[name=trade_id]").val();
		var jobcategory=$("#searckeybox input[name=jobs_id]").val();
		var citycategory=$("#searckeybox input[name=district_id]").val();
		var wage=$("#searckeybox input[name=wage]").val();
		var education=$("#searckeybox input[name=education]").val();
		var experience=$("#searckeybox input[name=experience]").val();
		var nature=$("#searckeybox input[name=nature]").val();
		var settr=$("#searckeybox input[name=settr]").val();
		var sort_1=$("#searckeybox input[name=sort]").val();
		var page=$("#searckeybox input[name=page]").val();
		$.get(dir+"plus/ajax_search_location.php", {"act":"QS_jobslist","key":key,"trade":trade,"jobcategory":jobcategory,"citycategory":citycategory,"wage":wage,"education":education,"experience":experience,"nature":nature,"settr":settr,"sort":sort_1,"page":page},
			function (data,textStatus)
			 {	
				 window.location.href=data;
			 },"text"
		);
	}
	// �Ƽ�ְλ ������Ƹ ����ְλ�л���
	$span_tit = $("#jobsmix .tit span");
	$div_morea = $("#jobsmix .tit .more a");
	$info_div = $("#jobsmix div.info");
	$span_tit.click(function() {
		$(this).addClass('slect').siblings().removeClass('slect');
		var index = $span_tit.index(this);
		$div_morea.eq(index).show().siblings('a').hide();
		$info_div.eq(index).show().siblings('.info').hide();
	});
}
/*
 * 74cms ְλ����ҳ�� ��ҵ���ݵ����
|   @param: fillID      -- �����ID
*/
function fillTrad(fillID){
	var tradli = '';
	$.each(QS_trade, function(index, val) {
		if(val) {
			var trads = val.split(",");
		 	tradli += '<li><a title="'+trads[1]+'" cln="'+trads[0]+'" href="javascript:;">'+trads[1]+'</a></li>';
		}
	});
	$(fillID+" ul").html(tradli);
}
/*
 * 74cms ְλ����ҳ�� ������ҵ��ѡ
*/
function copyTradItem() {
	var tradacqhtm = '';
	$("#tradList .selectedcolor").each(function() {
		tradacqhtm += '<a href="javascript:;" rel="'+$(this).attr('cln')+'" title="'+$(this).attr('title')+'"><div class="text">'+$(this).attr('title')+'</div><div class="close" id="c-'+$(this).attr('cln')+'"></div></a>';
	});
	$("#tradAcq").html(tradacqhtm);
	// ��ѡ��Ŀ�󶨵���¼�
	$("#tradAcq a").unbind().click(function() {
		var selval = $(this).attr('title');
		$("#tradList .selectedcolor").each(function() {
			if ($(this).attr('title') == selval) {
				$(this).removeClass('selectedcolor');
				copyTradItem();
			}
		});
	});
	// ���
	$("#tradEmpty").unbind().click(function() {
		$("#tradAcq").html("");
		$("#tradList .selectedcolor").each(function() {
			$(this).removeClass('selectedcolor');
		});
	});
}
/*
 * 74cms ְλ����ҳ�� ְλ���ݵ����
|   @param: fillID      -- �����ID
*/
function fillJobs(fillID){
	var jobstr = '';
	$.each(QS_jobs_parent, function(pindex, pval) {
		if(pval) {
			jobstr += '<tr>';
			var jobs = pval.split(",");
		 	jobstr += '<th>'+jobs[1]+'</th>';
		 	jobstr += '<td><ul class="jobcatelist">';
		 	var sjobsArray = QS_jobs[jobs[0]].split("|");
		 	$.each(sjobsArray, function(sindex, sval) {
		 		if(sval) {
		 			var sjobs = sval.split(",");
			 		jobstr += '<li>';
			 		jobstr += '<p><font><a rcoid="'+sjobs[0]+'" pid="'+jobs[0]+'.'+sjobs[0]+'" title="'+sjobs[1]+'" href="javascript:;">'+sjobs[1]+'</a></font></p>';
			 		if(QS_jobs[sjobs[0]]) {
			 			jobstr += '<div class="subcate" style="display:none;">';
			 			var cjobsArray = QS_jobs[sjobs[0]].split("|");
			 			jobstr += '<a p="qb" href="javascript:;">����</a>';
				 		$.each(cjobsArray, function(cindex, cval) {
				 			if(cval) {
					 			var cjobs = cval.split(",");
					 			jobstr += '<a rcoid="'+cjobs[0]+'" title="'+cjobs[1]+'" pid="'+jobs[0]+'.'+sjobs[0]+'.'+cjobs[0]+'" href="javascript:;">'+cjobs[1]+'</a>';
				 			}
				 		});
			 			jobstr += '</div>';
			 		}
			 		jobstr += '</li>';
		 		}
		 	});
		 	jobstr += '</ul></td>';
			jobstr += '</tr>';
		}
	});
	$(fillID+" tbody").html(jobstr);
	$(".jobcatelist li").each(function() {
		if($(this).find('.subcate').length <= 0) {
			$(this).find('font').css("background","none");
		}
	});
}
/*
 * 74cms ְλ����ҳ�� �������ݵ����
|   @param: fillID      -- �����ID
*/
function fillCity(fillID){
	var citystr = '';
	citystr += '<tr>';
	citystr += '<td><ul class="jobcatelist">';
	$.each(QS_city_parent, function(pindex, pval) {
		if(pval) {
			var citys = pval.split(",");
	 		citystr += '<li>';
	 		citystr += '<p><font><a rcoid="'+citys[0]+'" pid="'+citys[0]+'" title="'+citys[1]+'" href="javascript:;">'+citys[1]+'</a></font></p>';
	 		if(QS_city[citys[0]]) {
	 			citystr += '<div class="subcate" style="display:none;">';
	 			var ccitysArray = QS_city[citys[0]].split("|");
	 			citystr += '<a p="qb" href="javascript:;">����</a>';
		 		$.each(ccitysArray, function(cindex, cval) {
		 			if(cval) {
			 			var ccitys = cval.split(",");
			 			citystr += '<a rcoid="'+ccitys[0]+'" title="'+ccitys[1]+'" pid="'+citys[0]+'.'+ccitys[0]+'" href="javascript:;">'+ccitys[1]+'</a>';
		 			}
		 		});
	 			citystr += '</div>';
	 		}
	 		citystr += '</li>';
		}
	});
	citystr += '</ul></td>';
	citystr += '</tr>';
	$(fillID+" tbody").html(citystr);
	$(".jobcatelist li").each(function() {
		if($(this).find('.subcate').length <= 0) {
			$(this).find('font').css("background","none");
		}
	});
}
/*
 * 74cms ְλ����ҳ�� ����������ѡ
*/
function copyCityItem() {
	var cityacqhtm = '';
	$("#divCityCate .selectedcolor").each(function() {
		cityacqhtm += '<a pid="'+$(this).attr('pid')+'" href="javascript:;" title="'+$(this).attr('title')+'"><div class="text">'+$(this).attr('title')+'</div><div class="close"></div></a>';
	});
	$("#cityAcq").html(cityacqhtm);
	// ��ѡ��Ŀ�󶨵���¼�
	$("#cityAcq a").unbind().click(function() {
		var selval = $(this).attr('title');
		$("#divCityCate .selectedcolor").each(function() {
			if ($(this).attr('title') == selval) {
				$(this).removeClass('selectedcolor');
				copyCityItem();
			}
		});
	});
	// ���
	$("#cityEmpty").unbind().click(function() {
		$("#cityAcq").html("");
		$("#divCityCate .selectedcolor").each(function() {
			$(this).removeClass('selectedcolor');
		});
	});
}
/*
 * 74cms ְλ����ҳ�� ����ְλ��ѡ
*/
function copyJobItem() {
	var jobacqhtm = '';
	$("#divJobCate .selectedcolor").each(function() {
		jobacqhtm += '<a pid="'+$(this).attr('pid')+'" href="javascript:;" title="'+$(this).attr('title')+'"><div class="text">'+$(this).attr('title')+'</div><div class="close"></div></a>';
	});
	$("#jobAcq").html(jobacqhtm);
	// ��ѡ��Ŀ�󶨵���¼�
	$("#jobAcq a").unbind().click(function() {
		var selval = $(this).attr('title');
		$("#divJobCate .selectedcolor").each(function() {
			if ($(this).attr('title') == selval) {
				$(this).removeClass('selectedcolor');
				copyJobItem();
			}
		});
	});
	// ���
	$("#jobEmpty").unbind().click(function() {
		$("#jobAcq").html("");
		$("#divJobCate .selectedcolor").each(function() {
			$(this).removeClass('selectedcolor');
		});
	});
}
/*
 * 74cms ְλ����ҳ�� ����������
|   @param: showid      -- �����ID
|   @param: type      -- ����������
|   @param: QSarr      -- ��������
*/
function showOption(fillID,type,QSarr){
	var  href="javascript:void(0);";
	var opthtm = '';
	for(var i=0;i<QSarr.length;i++)
	{
		arr = QSarr[i].split(",");
		opthtm+='<a href="'+href+'" id="'+type+'-'+arr[0]+'" class="opt">'+arr[1]+'</a>';
	}
	$(fillID).html(opthtm);
}