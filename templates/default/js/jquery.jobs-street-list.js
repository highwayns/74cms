function jobslist()
{
	var key=$('#key').val();
	if (key)
	{
		key_arr=key.split(" ");
		for (x in key_arr)
		{
		if (key_arr[x]) $('.striking').highlight(key_arr[x]);
		}
	}
	// $(".list:odd").css("background-color","#F4F5FB");
	$(".titsub").hover(	function()	{$(this).addClass("titsub_h");},function(){$(this).removeClass("titsub_h");});
	function setlistbg()
	{
			$(".li_left_check input[type='checkbox']").each(function(i){
				if ($(this).attr("checked"))
				{
					$(this).parent().parent().addClass("seclect");
				}
				else
				{
					$(this).parent().parent().removeClass("seclect");
				}
			}); 
	 }
 	//ȫѡ��ѡ
	$("input[name='selectall']").unbind().click(function(){$("#formjobslist :checkbox").attr('checked',$(this).attr('checked'))});
	//���ѡ������¼���ʽ
	$("#formjobslist input[type='checkbox']").click(function(){setlistbg();});
	//����
	$(function(){
	var _wrap=$('#jobs_list_tip ul');
	var _interval=3000;
	var _moving;
	_wrap.hover(function(){
	clearInterval(_moving);
	},function(){
	_moving=setInterval(function(){
	var _field=_wrap.find('li:first');
	var _h=_field.height();
	_field.animate({marginTop:-_h+'px'},300,function(){
	_field.css('marginTop',0).appendTo(_wrap);
	})
	},_interval)
	}).trigger('mouseleave');
	});
}