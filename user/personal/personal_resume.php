<?php
/*
 * 74cms ���˻�Ա����
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/personal_common.php');
$smarty->assign('leftmenu',"resume");
//�����б�
if ($act=='resume_list')
{
	$wheresql=" WHERE uid='".$_SESSION['uid']."' ";
	$sql="SELECT * FROM ".table('resume').$wheresql;
	$smarty->assign('title','�ҵļ��� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('total',$total);
	$smarty->assign('resume_list',get_resume_list($sql,12,true,true,true));
	$smarty->display('member_personal/personal_resume_list.htm');
}
elseif ($act=='refresh')
{
		$resumeid = intval($_GET['id'])?intval($_GET['id']):showmsg("��û��ѡ�������");
		$refrestime=get_last_refresh_date($_SESSION['uid'],"2001");
		$duringtime=time()-$refrestime['max(addtime)'];
		$space = $_CFG['per_refresh_resume_space']*60;
		$refresh_time = get_today_refresh_times($_SESSION['uid'],"2001");
		if($_CFG['per_refresh_resume_time']!=0&&($refresh_time['count(*)']>=$_CFG['per_refresh_resume_time']))
		{
		showmsg("ÿ�����ֻ��ˢ��".$_CFG['per_refresh_resume_time']."��,�������ѳ������ˢ�´������ƣ�",2);	
		}
		elseif($duringtime<=$space){
		showmsg($_CFG['per_refresh_resume_space']."�����ڲ����ظ�ˢ�¼�����",2);
		}
		else 
		{
		refresh_resume($resumeid,$_SESSION['uid'])?showmsg('�����ɹ���',2):showmsg('����ʧ�ܣ�',0);
		}
}
//ɾ������
elseif ($act=='del_resume')
{
	if (intval($_GET['id'])==0)
	{
	exit('��û��ѡ�������');
	}
	else
	{
	del_resume($_SESSION['uid'],intval($_GET['id']))?exit('success'):exit('fail');
	}
}
//��������-������Ϣ
elseif ($act=='make1')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$_SESSION['send_mobile_key']=mt_rand(100000, 999999);
	$smarty->assign('send_key',$_SESSION['send_mobile_key']);
	$smarty->assign('resume_basic',get_resume_basic($uid,$pid));
	$smarty->assign('resume_education',get_resume_education($uid,$pid));
	$smarty->assign('resume_work',get_resume_work($uid,$pid));
	$smarty->assign('resume_training',get_resume_training($uid,$pid));
	$smarty->assign('act',$act);
	$smarty->assign('pid',$pid);
	$smarty->assign('user',$user);
	$smarty->assign('userprofile',get_userprofile($_SESSION['uid']));	
	$smarty->assign('title','�ҵļ��� - ���˻�Ա���� - '.$_CFG['site_name']);
	$captcha=get_cache('captcha');
	$smarty->assign('verify_resume',$captcha['verify_resume']);
	$smarty->assign('go_resume_show',$_GET['go_resume_show']);
	$smarty->display('member_personal/personal_make_resume_step1.htm');
}
//�������� -���������Ϣ����ְ����
elseif ($act=='make1_save')
{
	$captcha=get_cache('captcha');
	$postcaptcha = trim($_POST['postcaptcha']);
	if($captcha['verify_resume']=='1' && empty($postcaptcha) && intval($_REQUEST['pid'])===0)
	{
		showmsg("����дϵͳ��֤��",1);
 	}
	if ($captcha['verify_resume']=='1' && intval($_REQUEST['pid'])===0 &&  strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
	{
		showmsg("ϵͳ��֤�����",1);
	}
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['telephone']=trim($_POST['mobile'])?trim($_POST['mobile']):showmsg('����д�ֻ��ţ�',1);
		
	if($user['mobile_audit']==0){
		$mobile_audit=0;
		$verifycode=trim($_POST['verifycode']);
		if($verifycode){
			if (empty($_SESSION['mobile_rand']) || $verifycode<>$_SESSION['mobile_rand'])
			{
				showmsg("�ֻ���֤�����",1);
			}
			else
			{
				$verifysqlarr['mobile'] = $setsqlarr['telephone'];
				$verifysqlarr['mobile_audit'] = 1;
				$mobile_audit=1;
				updatetable(table('members'),$verifysqlarr," uid='{$setsqlarr['uid']}'");
				unset($verifysqlarr);
			}
		}
		unset($_SESSION['verify_mobile'],$_SESSION['mobile_rand']);
	}else{
		$mobile_audit=1;
	}
	
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$setsqlarr['title']=trim($_POST['title'])?trim($_POST['title']):"δ��������";
	check_word($_CFG['filter'],$_POST['title'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['fullname']=trim($_POST['fullname'])?trim($_POST['fullname']):showmsg('����д������',1);
	check_word($_CFG['filter'],$_POST['fullname'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['display_name']=intval($_POST['display_name']);
	$setsqlarr['sex']=trim($_POST['sex'])?intval($_POST['sex']):showmsg('��ѡ���Ա�',1);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['birthdate']=intval($_POST['birthdate'])>1945?intval($_POST['birthdate']):showmsg('����ȷ��д�������',1);
	$setsqlarr['residence']=trim($_POST['residence'])?trim($_POST['residence']):showmsg('��ѡ���־�ס�أ�',1);
	$setsqlarr['residence_cn']=trim($_POST['residence_cn']);
	$setsqlarr['education']=intval($_POST['education'])?intval($_POST['education']):showmsg('��ѡ��ѧ��',1);
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):showmsg('��ѡ��������',1);
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['email']=trim($_POST['email'])?trim($_POST['email']):showmsg('����д���䣡',1);
	check_word($_CFG['filter'],$_POST['email'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['email_notify']=$_POST['email_notify']=="1"?1:0;
	$setsqlarr['height']=intval($_POST['height']);
	$setsqlarr['householdaddress']=trim($_POST['householdaddress']);
	$setsqlarr['householdaddress_cn']=trim($_POST['householdaddress_cn']);	
	$setsqlarr['marriage']=intval($_POST['marriage']);
	$setsqlarr['marriage_cn']=trim($_POST['marriage_cn']);;
	$setsqlarr['intention_jobs']=trim($_POST['intention_jobs'])?trim($_POST['intention_jobs']):showmsg('��ѡ������ְλ��',1);
	$setsqlarr['trade']=$_POST['trade']?trim($_POST['trade']):showmsg('��ѡ��������ҵ��',1);
	$setsqlarr['trade_cn']=trim($_POST['trade_cn']);
	$setsqlarr['district']=trim($_POST['district'])?intval($_POST['district']):showmsg('��ѡ����������������',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['nature']=intval($_POST['nature'])?intval($_POST['nature']):showmsg('��ѡ��������λ���ʣ�',1);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['wage']=intval($_POST['wage'])?intval($_POST['wage']):showmsg('��ѡ������н�ʣ�',1);
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['entrust']=intval($_POST['entrust']);
	$setsqlarr['refreshtime']=$timestamp;
	$setsqlarr['audit']=intval($_CFG['audit_resume']);
	$total=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume')." WHERE uid='{$_SESSION['uid']}'");
	if ($total>=intval($_CFG['resume_max']))
	{
	showmsg("�������Դ���{$_CFG['resume_max']} �ݼ���,�Ѿ�������������ƣ�",1);
	}
	else
	{
	$setsqlarr['addtime']=$timestamp;
	$pid=inserttable(table('resume'),$setsqlarr,1);
	$searchtab['id'] = $pid;
	$searchtab['uid'] = $_SESSION['uid'];
	inserttable(table('resume_search_key'),$searchtab);
	inserttable(table('resume_search_rtime'),$searchtab);
	if (empty($pid))showmsg("����ʧ�ܣ�",0);
	add_resume_jobs($pid,$_SESSION['uid'],$_POST['intention_jobs_id'])?"":showmsg('����ʧ�ܣ�',0);
	add_resume_trade($pid,$_SESSION['uid'],$_POST['trade'])?"":showmsg('����ʧ�ܣ�',0);
	check_resume($_SESSION['uid'],$pid);
	if(intval($_POST['entrust'])){
		set_resume_entrust($pid);
	}
	write_memberslog($_SESSION['uid'],2,1101,$_SESSION['username'],"�����˼���");
	
	if(!get_userprofile($_SESSION['uid'])){
		$infoarr['realname']=$setsqlarr['fullname'];
		$infoarr['sex']=$setsqlarr['sex'];
		$infoarr['sex_cn']=$setsqlarr['sex_cn'];
		$infoarr['birthday']=$setsqlarr['birthdate'];
		$infoarr['residence']=$setsqlarr['residence'];
		$infoarr['residence_cn']=$setsqlarr['residence_cn'];
		$infoarr['education']=$setsqlarr['education'];
		$infoarr['education_cn']=$setsqlarr['education_cn'];
		$infoarr['experience']=$setsqlarr['experience'];
		$infoarr['experience_cn']=$setsqlarr['experience_cn'];
		$infoarr['height']=$setsqlarr['height'];
		$infoarr['householdaddress']=$setsqlarr['householdaddress'];
		$infoarr['householdaddress_cn']=$setsqlarr['householdaddress_cn'];
		$infoarr['marriage']=$setsqlarr['marriage'];
		$infoarr['marriage_cn']=$setsqlarr['marriage_cn'];
		$infoarr['phone']=$setsqlarr['telephone'];
		$infoarr['email']=$setsqlarr['email'];
		$infoarr['uid']=intval($_SESSION['uid']);
		inserttable(table('members_info'),$infoarr);
	}
	header("Location: ?act=make1_succeed&pid=".$pid);
	}	
}
elseif($act=='make1_succeed'){
	$pid = intval($_GET['pid']);
	$smarty->assign('pid',$pid);
	$smarty->assign('title','�ҵļ��� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_personal/personal_make_resume_step1_succeed.htm');
}
elseif($act=='ajax_get_interest_jobs'){
	global $_CFG;
	$uid=intval($_SESSION['uid']);
	$pid=intval($_GET['pid']);
	$html = "";
	$interest_id = get_interest_jobs_id_by_resume($uid,$pid);
	$jobs_list = get_interest_jobs_list($interest_id);
	if(!empty($jobs_list)){
		foreach($jobs_list as $k=>$v){
			$jobs_url = url_rewrite("QS_jobsshow",array("id"=>$v['id']));
			$company_url = url_rewrite("QS_companyshow",array("id"=>$v['company_id']));
			$html.='<div class="l1 link_bk"><a href="'.$jobs_url.'" target="_blank">'.$v["jobs_name"].'</a></div>
			  <div class="l2 link_bk"><a href="'.$company_url.'" target="_blank">'.$v["companyname"].'</a></div>
			  <div class="l3">'.$v["district_cn"].'</div>
			  <div class="l4">'.$v["wage_cn"].'</div>';
			  $html.='<div class="clear"></div>';
		}
		$html.='<div class="more link_lan"><a target="_blank" href="'.url_rewrite("QS_jobslist").'">����ְλ>></a></div>';
	}
	exit($html);
}
elseif ($act=='ajax_save_basic')
{
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['telephone']=trim($_POST['mobile'])?trim($_POST['mobile']):exit('����д�ֻ��ţ�');
	$go_verify=0;
	if($user['mobile_audit']==0){
		$verifycode=trim($_POST['verifycode']);
		if($verifycode){
			if (empty($_SESSION['mobile_rand']) || $verifycode<>$_SESSION['mobile_rand'])
			{
				exit("�ֻ���֤�����");
			}
			else
			{
				$verifysqlarr['mobile'] = $setsqlarr['telephone'];
				$verifysqlarr['mobile_audit'] = 1;
				$go_verify=1;
				updatetable(table('members'),$verifysqlarr," uid='{$setsqlarr['uid']}'");
				unset($verifysqlarr);
			}
		}
		unset($_SESSION['verify_mobile'],$_SESSION['mobile_rand']);
	}
	$setsqlarr['title']=trim($_POST['title'])?utf8_to_gbk(trim($_POST['title'])):"δ��������";
	check_word($_CFG['filter'],$setsqlarr['title'])?exit($_CFG['filter_tips']):'';
	$setsqlarr['fullname']=trim($_POST['fullname'])?utf8_to_gbk(trim($_POST['fullname'])):exit('����д������');
	check_word($_CFG['filter'],$setsqlarr['fullname'])?exit($_CFG['filter_tips']):'';
	$setsqlarr['display_name']=intval($_POST['display_name']);
	$setsqlarr['sex']=trim($_POST['sex'])?intval($_POST['sex']):exit('��ѡ���Ա�');
	$setsqlarr['sex_cn']=utf8_to_gbk(trim($_POST['sex_cn']));
	$setsqlarr['birthdate']=intval($_POST['birthdate'])>1945?intval($_POST['birthdate']):exit('����ȷ��д�������');
	$setsqlarr['residence']=trim($_POST['residence'])?utf8_to_gbk(trim($_POST['residence'])):exit('��ѡ���־�ס�أ�');
	$setsqlarr['residence_cn']=utf8_to_gbk(trim($_POST['residence_cn']));
	$setsqlarr['education']=intval($_POST['education'])?intval($_POST['education']):exit('��ѡ��ѧ��');
	$setsqlarr['education_cn']=utf8_to_gbk(trim($_POST['education_cn']));
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):exit('��ѡ��������');
	$setsqlarr['experience_cn']=utf8_to_gbk(trim($_POST['experience_cn']));
	$setsqlarr['email']=trim($_POST['email'])?utf8_to_gbk(trim($_POST['email'])):exit('����д���䣡');
	check_word($_CFG['filter'],$setsqlarr['email'])?exit($_CFG['filter_tips']):'';
	$setsqlarr['email_notify']=$_POST['email_notify']=="1"?1:0;
	$setsqlarr['height']=intval($_POST['height']);
	$setsqlarr['householdaddress']=trim($_POST['householdaddress']);
	$setsqlarr['householdaddress_cn']=utf8_to_gbk(trim($_POST['householdaddress_cn']));	
	$setsqlarr['marriage']=intval($_POST['marriage']);
	$setsqlarr['marriage_cn']=utf8_to_gbk(trim($_POST['marriage_cn']));
	$setsqlarr['intention_jobs']=utf8_to_gbk(trim($_POST['intention_jobs']));
	$setsqlarr['trade']=$_POST['trade']?trim($_POST['trade']):exit('��ѡ��������ҵ��');
	$setsqlarr['trade_cn']=utf8_to_gbk(trim($_POST['trade_cn']));
	$setsqlarr['district']=trim($_POST['district'])?intval($_POST['district']):exit('��ѡ����������������');
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=utf8_to_gbk(trim($_POST['district_cn']));
	$setsqlarr['nature']=intval($_POST['nature'])?intval($_POST['nature']):exit('��ѡ��������λ���ʣ�');
	$setsqlarr['nature_cn']=utf8_to_gbk(trim($_POST['nature_cn']));
	$setsqlarr['wage']=intval($_POST['wage'])?intval($_POST['wage']):exit('��ѡ������н�ʣ�');
	$setsqlarr['wage_cn']=utf8_to_gbk(trim($_POST['wage_cn']));
	$setsqlarr['refreshtime']=$timestamp;
	$_CFG['audit_edit_resume']!="-1"?$setsqlarr['audit']=intval($_CFG['audit_edit_resume']):"";
	updatetable(table('resume'),$setsqlarr," id='".intval($_REQUEST['pid'])."'  AND uid='{$setsqlarr['uid']}'");
	add_resume_jobs(intval($_REQUEST['pid']),$_SESSION['uid'],$_POST['intention_jobs_id'])?"":showmsg('����ʧ�ܣ�',0);
	add_resume_trade(intval($_REQUEST['pid']),$_SESSION['uid'],$_POST['trade'])?"":showmsg('����ʧ�ܣ�',0);
	check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
	if($_CFG['audit_edit_resume']!="-1"){
		set_resume_entrust(intval($_REQUEST['pid']));
	}
	write_memberslog($_SESSION['uid'],2,1105,$_SESSION['username'],"�޸��˼���({$_POST['title']})");
	if($go_verify){
		$wheresql = " WHERE uid=".intval($_SESSION['uid']);
		$infoarr['phone']=$setsqlarr['telephone'];
		updatetable(table('members_info'),$infoarr,$wheresql);
		unset($infoarr);
		$infoarr['mobile']=$setsqlarr['telephone'];
		$infoarr['mobile_audit'] = 1;
		updatetable(table('members'),$infoarr,$wheresql);
	}
	exit("success");
}
//��������-�ڶ���
elseif ($act=='make2')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$link[0]['text'] = "���ؼ����б�";
	$link[0]['href'] = '?act=resume_list';
	if ($uid==0 || $pid==0) showmsg('���������ڣ�',1,$link);
	$resume_basic=get_resume_basic($uid,$pid);
	$link[0]['text'] = "��д����������Ϣ";
	$link[0]['href'] = '?act=make1';
	if (empty($resume_basic)) showmsg("������д����������Ϣ��",1,$link);
	$smarty->assign('resume_basic',$resume_basic);
	$smarty->assign('resume_education',get_resume_education($uid,$pid));
	$smarty->assign('resume_work',get_resume_work($uid,$pid));
	$smarty->assign('resume_training',get_resume_training($uid,$pid));
	$resume_jobs=get_resume_jobs($pid);
	if ($resume_jobs)
	{
		foreach($resume_jobs as $rjob)
		{
		$jobsid[]=$rjob['topclass'].".".$rjob['category'].".".$rjob['subclass'];
		}
		$resume_jobs_id=implode(",",$jobsid);
	}
	$smarty->assign('resume_jobs_id',$resume_jobs_id);
	$smarty->assign('act',$act);
	$smarty->assign('pid',$pid);
	$smarty->assign('title','�ҵļ��� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('go_resume_show',$_GET['go_resume_show']);
	$smarty->display('member_personal/personal_make_resume_step2.htm');
}
elseif ($act=='make2_photo_ready')
{	
	!$_FILES['photo']['name']?exit('���ϴ�ͼƬ��'):"";
	require_once(QISHI_ROOT_PATH.'include/cut_upload.php');
	if (intval($_REQUEST['pid'])==0) exit('��������');
	$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_REQUEST['pid']));
	if (empty($resume_basic['photo_img']))
	{
	$setsqlarr['photo_audit']=$_CFG['audit_resume_photo'];
	}
	else
	{
	$_CFG['audit_edit_photo']!="-1"?$setsqlarr['photo_audit']=intval($_CFG['audit_edit_photo']):"";
	}

	$up_res_original="../../data/photo/original/";
	$up_res_120="../../data/photo/120/";
	$up_res_thumb="../../data/photo/thumb/";
	make_dir($up_res_original.date("Y/m/d/"));
	make_dir($up_res_120.date("Y/m/d/"));
	make_dir($up_res_thumb.date("Y/m/d/"));
	$setsqlarr['photo_img']=_asUpFiles($up_res_original.date("Y/m/d/"),"photo",$_CFG['resume_photo_max'],'gif/jpg/bmp/png',true);
	$setsqlarr['photo_img']=date("Y/m/d/").$setsqlarr['photo_img'];
	if ($setsqlarr['photo_img'])
	{
		makethumb($up_res_original.$setsqlarr['photo_img'],$up_res_thumb.date("Y/m/d/"),280,350);
		!updatetable(table('resume'),$setsqlarr," id='".intval($_REQUEST['pid'])."' AND uid='".intval($_SESSION['uid'])."'")?exit("����ʧ�ܣ�"):'';
		exit($setsqlarr['photo_img']);
	} else {
		showmsg('����ʧ�ܣ�',1);
	}
}
elseif ($act=='make2_photo_save')
{	
	$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_REQUEST['pid']));
	if (empty($resume_basic)) showmsg("������д����������Ϣ��",0);
	require_once(QISHI_ROOT_PATH.'include/cut_upload.php');
	require_once(QISHI_ROOT_PATH.'include/imageresize.class.php');
	$imgresize = new ImageResize();
	if($resume_basic['photo_img']){
		$up_res_original="../../data/photo/original/";
		$up_res_120="../../data/photo/120/";
		$up_res_thumb="../../data/photo/thumb/";
		$imgresize->load($up_res_thumb.$resume_basic['photo_img']);
		$imgresize->cut(intval($_POST['w']),intval($_POST['h']), intval($_POST['x']), intval($_POST['y']));
		$imgresize->save($up_res_thumb.$resume_basic['photo_img']);

		makethumb($up_res_thumb.$resume_basic['photo_img'],$up_res_120.date("Y/m/d/"),120,150);

		@unlink($up_res_original.$resume_basic['photo_img']);
		// @unlink($up_res_thumb.$resume_basic['photo_img']);

		check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
		showmsg("����ɹ���",2);
	}else{
		showmsg("���ϴ�ͼƬ��",1);
	}
}
elseif($act == "tag_save"){
	if (intval($_POST['pid'])==0 ) showmsg('��������',1);
	$setsqlarr['tag']=trim($_POST['tag']);
	$setsqlarr['specialty']=!empty($_POST['specialty'])?$_POST['specialty']:showmsg('����д����������',1);
	check_word($_CFG['filter'],$_POST['specialty'])?showmsg($_CFG['filter_tips'],0):'';
	$_CFG['audit_edit_resume']!="-1"?$setsqlarr['audit']=intval($_CFG['audit_edit_resume']):"";
	updatetable(table('resume'),$setsqlarr," id='".intval($_POST['pid'])."' AND uid='".intval($_SESSION['uid'])."'");
	check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
	showmsg("����ɹ���",2);
	// header('Location: ?act=edit_resume&pid='.$_REQUEST['pid']);
}
elseif($act=='save_education'){
	$id=intval($_POST['id']);
	$setsqlarr['uid'] = intval($_SESSION['uid']);
	$setsqlarr['pid'] = intval($_REQUEST['pid']);
	if ($setsqlarr['uid']==0 || $setsqlarr['pid']==0 ) exit('���������ڣ�');
	$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_REQUEST['pid']));
	if (empty($resume_basic)) exit("������д����������Ϣ��");
	$resume_education=get_resume_education($_SESSION['uid'],$_REQUEST['pid']);
	if (count($resume_education)>=6) exit('�����������ܳ���6����');
	$school = utf8_to_gbk(trim($_POST['school']));
	$speciality = utf8_to_gbk(trim($_POST['speciality']));
	$education_cn = utf8_to_gbk(trim($_POST['education_cn']));
	$setsqlarr['school'] = $school?$school:exit("����дѧУ���ƣ�");
	check_word($_CFG['filter'],$setsqlarr['school'])?exit($_CFG['filter_tips']):'';
	$setsqlarr['speciality'] = $speciality?$speciality:exit("����дרҵ���ƣ�");
	check_word($_CFG['filter'],$setsqlarr['speciality'])?exit($_CFG['filter_tips']):'';
	$setsqlarr['education'] = intval($_POST['education'])?intval($_POST['education']):exit("��ѡ����ѧ����");
	$setsqlarr['education_cn'] = $education_cn?$education_cn:exit("��ѡ����ѧ����");
	if(trim($_POST['edu_start_year'])==""||trim($_POST['edu_start_month'])==""||trim($_POST['edu_end_year'])==""||trim($_POST['edu_end_month'])==""){
		exit("��ѡ��Ͷ�ʱ�䣡");
	}
	$setsqlarr['startyear'] = intval($_POST['edu_start_year']);
	$setsqlarr['startmonth'] = intval($_POST['edu_start_month']);
	$setsqlarr['endyear'] = intval($_POST['edu_end_year']);
	$setsqlarr['endmonth'] = intval($_POST['edu_end_month']);
	if($id){
		updatetable(table("resume_education"),$setsqlarr,array("id"=>$id));
		exit("success");
	}else{
		$insert_id = inserttable(table("resume_education"),$setsqlarr,1);
		if($insert_id){
			check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
			exit("success");
		}else{
			exit("err");
		}
	}
	
}
elseif($act=='ajax_get_education_list'){
	$pid=intval($_GET['pid']);
	$uid=intval($_SESSION['uid']);
	$education_list = get_resume_education($uid,$pid);
	$html="";
	if($education_list){
		foreach ($education_list as $key => $value) {
			$html.='<div class="jl1">
				 	 <div class="l1">'.$value["startyear"].'��'.$value["startmonth"].'��-'.$value["endyear"].'��'.$value["endmonth"].'��</div>
					 <div class="l2">'.$value["school"].'</div>
					 <div class="l3">'.$value["speciality"].'</div>
					 <div class="l4">'.$value["education_cn"].'</div>
					 <div class="l5">
					 <a class="edit_education" href="javascript:void(0);" url="?act=edit_education&id='.$value["id"].'&pid='.$pid.'"></a>
					 <a class="del_education d" href="javascript:void(0);" pid="'.$pid.'" edu_id="'.$value["id"].'" ></a><div class="clear"></div>
					 </div>
					 <div class="clear"></div>
				</div>';
		}
	}else{
		$js='<script type="text/javascript">$("#add_education").hide();</script>';
		$html.='<div class="noinfo" id="education_empty_box">
		 	 <div class="txt">��������������������ѧ����רҵ���������������������ҵ��HR�����ɣ�</div>
			 <div class="addbut">
			 	<input type="button" name="" id="empty_add_education" value="��Ӿ���"  class="but130lan_add"/>
			 </div>
		</div>';
		$html.=$js;
	}
	
	exit($html);
}
//��������-�޸Ľ�������
elseif ($act=='edit_education')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	if ($uid==0 || $pid==0) exit('���������ڣ�');
	$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_REQUEST['pid']));
	if (empty($resume_basic)) exit("������д����������Ϣ��");
	$id=intval($_GET['id'])?intval($_GET['id']):exit('��������');
	$education_edit = get_resume_education_one($_SESSION['uid'],$pid,$id);
	foreach ($education_edit as $key => $value) {
		$education_edit[$key] = gbk_to_utf8($value);
	}
	$json_encode = json_encode($education_edit);
	exit($json_encode);
}
//��������-ɾ����������
elseif ($act=='del_education')
{
	$id=intval($_GET['id']);
	$sql="Delete from ".table('resume_education')." WHERE id='{$id}'  AND uid='".intval($_SESSION['uid'])."' AND pid='".intval($_REQUEST['pid'])."' LIMIT 1 ";
	if ($db->query($sql))
	{
	check_resume($_SESSION['uid'],intval($_REQUEST['pid']));//���¼������״̬
	exit('ɾ���ɹ���');
	}
	else
	{
	exit('ɾ��ʧ�ܣ�');
	}	
}
elseif($act=='save_work'){
	$id=intval($_POST['id']);
	$setsqlarr['uid'] = intval($_SESSION['uid']);
	$setsqlarr['pid'] = intval($_REQUEST['pid']);
	if ($setsqlarr['uid']==0 || $setsqlarr['pid']==0 ) exit('���������ڣ�');
	$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_REQUEST['pid']));
	if (empty($resume_basic)) exit("������д����������Ϣ��");
	$resume_work=get_resume_work($_SESSION['uid'],$_REQUEST['pid']);
	if (count($resume_work)>=6) exit('�����������ܳ���6����');
	$companyname = utf8_to_gbk(trim($_POST['companyname']));
	$jobs = utf8_to_gbk(trim($_POST['jobs']));
	$achievements = utf8_to_gbk(trim($_POST['achievements']));
	$setsqlarr['companyname'] = $companyname?$companyname:exit("����д��˾���ƣ�");
	check_word($_CFG['filter'],$setsqlarr['companyname'])?exit($_CFG['filter_tips']):'';
	$setsqlarr['jobs'] = $jobs?$jobs:exit("����дְλ���ƣ�");
	check_word($_CFG['filter'],$setsqlarr['jobs'])?exit($_CFG['filter_tips']):'';
	if(trim($_POST['work_start_year'])==""||trim($_POST['work_start_month'])==""||trim($_POST['work_end_year'])==""||trim($_POST['work_end_month'])==""){
		exit("��ѡ����ְʱ�䣡");
	}
	$setsqlarr['startyear'] = intval($_POST['work_start_year']);
	$setsqlarr['startmonth'] = intval($_POST['work_start_month']);
	$setsqlarr['endyear'] = intval($_POST['work_end_year']);
	$setsqlarr['endmonth'] = intval($_POST['work_end_month']);
	$setsqlarr['achievements'] = $achievements?$achievements:exit("����д����ְ��");
	check_word($_CFG['filter'],$setsqlarr['achievements'])?exit($_CFG['filter_tips']):'';
	
	if($id){
		updatetable(table("resume_work"),$setsqlarr,array("id"=>$id));
		exit("success");
	}else{
		$insert_id = inserttable(table("resume_work"),$setsqlarr,1);
		if($insert_id){
			check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
			exit("success");
		}else{
			exit("err");
		}
	}
	
}
elseif($act=='ajax_get_work_list'){
	$pid=intval($_GET['pid']);
	$uid=intval($_SESSION['uid']);
	$work_list = get_resume_work($uid,$pid);
	$html="";
	if($work_list){
		foreach ($work_list as $key => $value) {
			$html.='<div class="jl2">
					 	 <div class="l1">'.$value["startyear"].'��'.$value["startmonth"].'��-'.$value["endyear"].'��'.$value["endmonth"].'��</div>
						 <div class="l2">'.$value["companyname"].'</div>
						 <div class="l3">'.$value["jobs"].'</div>
						 <div class="l4">
						 <a class="edit_work" href="javascript:void(0);" url="?act=edit_work&id='.$value["id"].'&pid='.$pid.'"></a>
						 <a class="del_work d" href="javascript:void(0);" pid="'.$pid.'" work_id="'.$value["id"].'" ></a><div class="clear"></div>
						 <div class="clear"></div>
						 </div>
						 <div class="l5">����ְ��</div>
						 <div class="l6">'.$value["achievements"].'
						 </div>
						 <div class="clear"></div>
					</div>';
		}
	}else{
		$js='<script type="text/javascript">$("#add_work").hide();</script>';
		$html.='<div class="noinfo" id="work_empty_box">	
			 	 <div class="txt">�������������������ḻ�������ͳ��ڵĹ�������������н�귭���ĳ���ŶHR�����ɣ�</div>
				 <div class="addbut">
				 	<input type="button" name="" id="empty_add_work" value="��Ӿ���"  class="but130lan_add"/>
				 </div>
			</div>';
		$html.=$js;
	}
	
	exit($html);
}
//��������-�޸Ĺ�������
elseif ($act=='edit_work')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	if ($uid==0 || $pid==0) exit('���������ڣ�');
	$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_REQUEST['pid']));
	if (empty($resume_basic)) exit("������д����������Ϣ��");
	$id=intval($_GET['id'])?intval($_GET['id']):exit('��������');
	$work_edit = get_resume_work_one($_SESSION['uid'],$pid,$id);
	foreach ($work_edit as $key => $value) {
		$work_edit[$key] = gbk_to_utf8($value);
	}
	$json_encode = json_encode($work_edit);
	exit($json_encode);
}
//��������-ɾ����������
elseif ($act=='del_work')
{
	$id=intval($_GET['id']);
	$sql="Delete from ".table('resume_work')." WHERE id='{$id}'  AND uid='".intval($_SESSION['uid'])."' AND pid='".intval($_REQUEST['pid'])."' LIMIT 1 ";
	if ($db->query($sql))
	{
	check_resume($_SESSION['uid'],intval($_REQUEST['pid']));//���¼������״̬
	exit('ɾ���ɹ���');
	}
	else
	{
	exit('ɾ��ʧ�ܣ�');
	}	
}
elseif($act=='save_training'){
	$id=intval($_POST['id']);
	$setsqlarr['uid'] = intval($_SESSION['uid']);
	$setsqlarr['pid'] = intval($_REQUEST['pid']);
	if ($setsqlarr['uid']==0 || $setsqlarr['pid']==0 ) exit('���������ڣ�');
	$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_REQUEST['pid']));
	if (empty($resume_basic)) exit("������д����������Ϣ��");
	$resume_work=get_resume_work($_SESSION['uid'],$_REQUEST['pid']);
	if (count($resume_work)>=6) exit('��ѵ�������ܳ���6����');
	$agency = utf8_to_gbk(trim($_POST['agency']));
	$course = utf8_to_gbk(trim($_POST['course']));
	$description = utf8_to_gbk(trim($_POST['description']));
	$setsqlarr['agency'] = $agency?$agency:exit("����д��ѵ������");
	check_word($_CFG['filter'],$setsqlarr['agency'])?exit($_CFG['filter_tips']):'';
	$setsqlarr['course'] = $course?$course:exit("����д��ѵ�γ̣�");
	check_word($_CFG['filter'],$setsqlarr['course'])?exit($_CFG['filter_tips']):'';
	if(trim($_POST['training_start_year'])==""||trim($_POST['training_start_month'])==""||trim($_POST['training_end_year'])==""||trim($_POST['training_end_month'])==""){
		exit("��ѡ����ѵʱ�䣡");
	}
	$setsqlarr['startyear'] = intval($_POST['training_start_year']);
	$setsqlarr['startmonth'] = intval($_POST['training_start_month']);
	$setsqlarr['endyear'] = intval($_POST['training_end_year']);
	$setsqlarr['endmonth'] = intval($_POST['training_end_month']);
	$setsqlarr['description'] = $description?$description:exit("����д��ѵ���ݣ�");
	check_word($_CFG['filter'],$setsqlarr['description'])?exit($_CFG['filter_tips']):'';
	
	if($id){
		updatetable(table("resume_training"),$setsqlarr,array("id"=>$id));
		exit("success");
	}else{
		$insert_id = inserttable(table("resume_training"),$setsqlarr,1);
		if($insert_id){
			check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
			exit("success");
		}else{
			exit("err");
		}
	}
	
}
elseif($act=='ajax_get_training_list'){
	$pid=intval($_GET['pid']);
	$uid=intval($_SESSION['uid']);
	$training_list = get_resume_training($uid,$pid);
	$html="";
	if($training_list){
		foreach ($training_list as $key => $value) {
			$html.='<div class="jl2">
			 	 <div class="l1">'.$value["startyear"].'��'.$value["startmonth"].'��-'.$value["endyear"].'��'.$value["endmonth"].'��</div>
				 <div class="l2">'.$value["agency"].'</div>
				 <div class="l3">'.$value["course"].'</div>
				 <div class="l4">
				 <a class="edit_training" href="javascript:void(0);" url="?act=edit_training&id='.$value["id"].'&pid='.$pid.'"></a>
				 <a class="del_training d" href="javascript:void(0);" pid="'.$pid.'" training_id="'.$value["id"].'" ></a><div class="clear"></div>
				 </div>
				 <div class="l5">��ѵ���ݣ�</div>
				 <div class="l6">'.$value["description"].'</div>
				 <div class="clear"></div>
			</div>';
		}
	}else{
		$js='<script type="text/javascript">$("#add_training").hide();</script>';
		$html.='<div class="noinfo" id="training_empty_box">	
		 	 <div class="txt">��ѵ�������������Ͻ�����õ����֣�����˵˵����������ѧϰ�����ɣ�</div>
			 <div class="addbut">
			 	<input type="button" name="" id="empty_add_training" value="��Ӿ���"  class="but130lan_add"/>
			 </div>
		</div>';
		$html.=$js;
	}
	exit($html);
}
//��������-�޸���ѵ����
elseif ($act=='edit_training')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	if ($uid==0 || $pid==0) exit('���������ڣ�');
	$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_REQUEST['pid']));
	if (empty($resume_basic)) exit("������д����������Ϣ��");
	$id=intval($_GET['id'])?intval($_GET['id']):exit('��������');
	$training_edit = get_resume_training_one($_SESSION['uid'],$pid,$id);
	foreach ($training_edit as $key => $value) {
		$training_edit[$key] = gbk_to_utf8($value);
	}
	$json_encode = json_encode($training_edit);
	exit($json_encode);
}
//��������-ɾ����ѵ����
elseif ($act=='del_training')
{
	$id=intval($_GET['id']);
	$sql="Delete from ".table('resume_training')." WHERE id='{$id}'  AND uid='".intval($_SESSION['uid'])."' AND pid='".intval($_REQUEST['pid'])."' LIMIT 1 ";
	if ($db->query($sql))
	{
	check_resume($_SESSION['uid'],intval($_REQUEST['pid']));//���¼������״̬
	exit('ɾ���ɹ���');
	}
	else
	{
	exit('ɾ��ʧ�ܣ�');
	}	
}
elseif ($act=='edit_resume')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$_SESSION['send_mobile_key']=mt_rand(100000, 999999);
	$smarty->assign('send_key',$_SESSION['send_mobile_key']);
	$smarty->assign('resume_basic',get_resume_basic($uid,$pid));
	$smarty->assign('resume_education',get_resume_education($uid,$pid));
	$smarty->assign('resume_work',get_resume_work($uid,$pid));
	$smarty->assign('resume_training',get_resume_training($uid,$pid));
	$resume_jobs=get_resume_jobs($pid);
	if ($resume_jobs)
	{
		foreach($resume_jobs as $rjob)
		{
		$jobsid[]=$rjob['topclass'].".".$rjob['category'].".".$rjob['subclass'];
		}
		$resume_jobs_id=implode(",",$jobsid);
	}
	$smarty->assign('resume_jobs_id',$resume_jobs_id);
	$smarty->assign('act',$act);
	$smarty->assign('pid',$pid);
	$smarty->assign('user',$user);
	$smarty->assign('title','�ҵļ��� - ���˻�Ա���� - '.$_CFG['site_name']);
	$captcha=get_cache('captcha');
	$smarty->assign('verify_resume',$captcha['verify_resume']);
	$smarty->assign('go_resume_show',$_GET['go_resume_show']);
	$smarty->display('member_personal/personal_resume_edit.htm');
}
elseif ($act=='save_resume_privacy')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$setsqlarr['display']=intval($_POST['display']);
	$setsqlarr['display_name']=intval($_POST['display_name']);
	$setsqlarr['photo_display']=intval($_POST['photo_display']);
	$wheresql=" uid='".$_SESSION['uid']."' ";
	!updatetable(table('resume'),$setsqlarr," uid='{$uid}' AND  id='{$pid}'");
	$setsqlarrdisplay['display']=intval($_POST['display']);
	!updatetable(table('resume_search_key'),$setsqlarrdisplay," uid='{$uid}' AND  id='{$pid}'");
	!updatetable(table('resume_search_rtime'),$setsqlarrdisplay," uid='{$uid}' AND  id='{$pid}'");
	!updatetable(table('resume_search_tag'),$setsqlarrdisplay," uid='{$uid}' AND  id='{$pid}'");
	write_memberslog($_SESSION['uid'],2,1104,$_SESSION['username'],"���ü�����˽({$pid})");
}
elseif ($act=='talent_save')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$resume=get_resume_basic($uid,$pid);
	if ($resume['complete_percent']<$_CFG['elite_resume_complete_percent'])
	{
	showmsg("��������ָ��С��{$_CFG['elite_resume_complete_percent']}%����ֹ���룡",0);
	}
	$setsqlarr['talent']=3;
	$wheresql=" uid='{$uid}' AND id='{$pid}' ";
	updatetable(table('resume'),$setsqlarr,$wheresql);
	write_memberslog($uid,2,1107,$_SESSION['username'],"����߼��˲�");
	showmsg('����ɹ�����ȴ�����Ա��ˣ�',2);
}
unset($smarty);
?>