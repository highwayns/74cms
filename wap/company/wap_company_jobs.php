<?php
 /*
 * 74cms WAP
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/fun_company.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
if ($_SESSION['uid']=='' || $_SESSION['username']==''||intval($_SESSION['utype'])==2)
{
	header("Location: ../wap_login.php");
}
elseif ($act == 'index')
{
	$smarty->cache = false;
	$uid=intval($_SESSION["uid"]);
	$wheresql=" select * from ".table("jobs")." where uid=$_SESSION[uid] union all select * from ".table("jobs_tmp")." where uid=$uid ";
	$row=get_jobs($offset,$perpage,$wheresql,true);
	$smarty->assign('row',$row);
	$smarty->display("wap/company/wap-job-index.html");	
}
// ����ְλ
elseif($act=="jobs_add")
{
	$smarty->cache = false;
	$company_info=get_company(intval($_SESSION['uid']));
	if($company_info['companyname'])
	{
		$smarty->assign('company_info',$company_info);
		if ($_CFG['operation_mode']=="3")
		{
			$setmeal=get_user_setmeal(intval($_SESSION['uid']));
			if (($setmeal['endtime']>time() || $setmeal['endtime']=="0") &&  $setmeal['jobs_ordinary']>0)
			{
			$smarty->assign('setmeal',$setmeal);
			$smarty->assign('add_mode',2);
			}
			elseif($_CFG['setmeal_to_points']=="1")
			{
			$smarty->assign('points_total',get_user_points(intval($_SESSION['uid'])));
			$smarty->assign('points',get_cache('points_rule'));
			$smarty->assign('add_mode',1);
			}
			else
			{
			$smarty->assign('setmeal',$setmeal);
			$smarty->assign('add_mode',2);
			}
		}
		elseif ($_CFG['operation_mode']=="2")
		{
			$setmeal=get_user_setmeal(intval($_SESSION['uid']));
			$smarty->assign('setmeal',$setmeal);
			$smarty->assign('add_mode',2);
		}
		elseif ($_CFG['operation_mode']=="1")
		{
			$smarty->assign('points_total',get_user_points(intval($_SESSION['uid'])));
			$smarty->assign('points',get_cache('points_rule'));
			$smarty->assign('add_mode',1);
		}
		$smarty->display("wap/company/wap-create-job.html");
	}else{
		header("Location: wap_user.php?act=company_info_add");
	}
}
// ����ְλ
elseif($act=="jobs_add_save")
{
	$smarty->cache = false;
	$company_info=get_company(intval($_SESSION['uid']));
	$_POST=array_map("utf8_to_gbk", $_POST);
	$add_mode=trim($_POST['add_mode']);
	$days=intval($_POST['days']);
	if ($days<$_CFG['company_add_days_min'])
	{
	exit("��Ƹ��������Ϊ ".$_CFG['company_add_days_min']." �죡");
	}
	if ($add_mode=='1')
	{
					$points_rule=get_cache('points_rule');
					$user_points=get_user_points($_SESSION['uid']);
					$total=0;
					if ($points_rule['jobs_add']['type']=="2" && $points_rule['jobs_add']['value']>0)
					{
					$total=$points_rule['jobs_add']['value'];
					}
					if ($points_rule['jobs_daily']['type']=="2" && $points_rule['jobs_daily']['value']>0)
					{
					$total=$total+($days*$points_rule['jobs_daily']['value']);
					}
					if ($total>$user_points)
					{
					exit("���".$_CFG['points_byname']."���㣬���ֵ���ٷ�����");
					}
					$setsqlarr['setmeal_deadline']=0;
	}
	elseif ($add_mode=='2')
	{
				$setmeal=get_user_setmeal($_SESSION['uid']);
				if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
				{					
					exit("���ķ����Ѿ����ڣ������¿�ͨ");
				}
				if ($setmeal['jobs_ordinary']<=0)
				{
					exit("��ǰ������ְλ�Ѿ�������������ƣ������������ײͣ�");
				}
				$setsqlarr['setmeal_deadline']=$setmeal['endtime'];
				$setsqlarr['setmeal_id']=$setmeal['setmeal_id'];
				$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
	}
	$setsqlarr['add_mode']=intval($add_mode);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['companyname']=$company_info['companyname'];
	$setsqlarr['company_id']=$company_info['id'];
	$setsqlarr['company_addtime']=$company_info['addtime'];
	$setsqlarr['company_audit']=$company_info['audit'];
	$setsqlarr['jobs_name']=!empty($_POST['jobs_name'])?trim($_POST['jobs_name']):exit('����дְλ���ƣ�');
	$setsqlarr['nature']=intval($_POST['nature'])?trim($_POST['nature']):exit('��ѡ��ְλ����!');
	$setsqlarr['nature_cn']=trim($_POST['nature_cn'])?trim($_POST['nature_cn']):exit('��ѡ��ְλ����!');
	$setsqlarr['topclass']=intval($_POST['topclass']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):exit('��ѡ��ְλ���');
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['amount']=intval($_POST['amount'])?intval($_POST['amount']):exit('����д��Ƹ����!');
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):exit('��ѡ����������');
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['wage']=intval($_POST['wage'])?intval($_POST['wage']):exit('��ѡ��н�ʴ�����');		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	// $setsqlarr['negotiable']=intval($_POST['negotiable']);
	// $setsqlarr['tag']=trim($_POST['tag']);
	$setsqlarr['sex']=intval($_POST['sex'])?intval($_POST['sex']):exit("��ѡ���Ա�!");
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['education']=intval($_POST['education'])?intval($_POST['education']):exit('��ѡ��ѧ��Ҫ��');		
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):exit('��ѡ�������飡');		
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['graduate']=intval($_POST['graduate']);
	// $setsqlarr['age']=trim($_POST['minage'])."-".trim($_POST['maxage']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):exit('��û����дְλ������');
	check_word($_CFG['filter'],$_POST['contents'])?exit($_CFG['filter_tips']):'';
	$setsqlarr['trade']=$company_info['trade'];
	$setsqlarr['trade_cn']=$company_info['trade_cn'];
	$setsqlarr['scale']=$company_info['scale'];
	$setsqlarr['scale_cn']=$company_info['scale_cn'];
	$setsqlarr['street']=$company_info['street'];
	$setsqlarr['street_cn']=$company_info['street_cn'];
	$setsqlarr['addtime']=time();
	$setsqlarr['deadline']=strtotime("".intval($_POST['days'])." day");
	$setsqlarr['refreshtime']=time();
	$setsqlarr['key']=$setsqlarr['jobs_name'].$company_info['companyname'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$company_info['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$setsqlarr['subsite_id']=0;
	$setsqlarr['tpl']=$company_info['tpl'];
	$setsqlarr['map_x']=$company_info['map_x'];
	$setsqlarr['map_y']=$company_info['map_y'];
	if ($company_info['audit']=="1")
	{
	$setsqlarr['audit']=intval($_CFG['audit_verifycom_addjob']);
	}
	else
	{
	$setsqlarr['audit']=intval($_CFG['audit_unexaminedcom_addjob']);
	}
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):exit('��û����д��ϵ�ˣ�');
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):exit('��û����д��ϵ�绰��');
	check_word($_CFG['filter'],$_POST['telephone'])?exit($_CFG['filter_tips']):'';

	$setsqlarr_contact['contact_show']=1;
	$setsqlarr_contact['email_show']=1;
	$setsqlarr_contact['telephone_show']=1;
	$setsqlarr_contact['address_show']=1;
	//���ְλ��Ϣ
	$pid=inserttable(table('jobs'),$setsqlarr,1);
	if(empty($pid)){
		exit("err");
	}
	//�����ϵ��ʽ
	$setsqlarr_contact['pid']=$pid;
	if(!inserttable(table('jobs_contact'),$setsqlarr_contact))exit("��ϵ��ʽ����");
	if ($add_mode=='1')
	{
		if ($points_rule['jobs_add']['value']>0)
		{
		report_deal($_SESSION['uid'],$points_rule['jobs_add']['type'],$points_rule['jobs_add']['value']);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['jobs_add']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"������ְλ��<strong>{$setsqlarr['jobs_name']}</strong>��({$operator}{$points_rule['jobs_add']['value']})��(ʣ��:{$user_points})",1,1001,"����ְλ","{$operator}{$points_rule['jobs_add']['value']}","{$user_points}");
		}
		if (intval($_POST['days'])>0 && $points_rule['jobs_daily']['value']>0)
		{
		$points_day=intval($_POST['days'])*$points_rule['jobs_daily']['value'];
		report_deal($_SESSION['uid'],$points_rule['jobs_daily']['type'],$points_day);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['jobs_daily']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"����ְλ:<strong>{$_POST['jobs_name']}</strong>����Ч��Ϊ{$_POST['days']}�죬({$operator}{$points_day})��(ʣ��:{$user_points})",1,1001,"����ְλ","{$operator}{$points_day}","{$user_points}");
		}
	}	
	elseif ($add_mode=='2')
	{
		action_user_setmeal($_SESSION['uid'],"jobs_ordinary");
		$setmeal=get_user_setmeal($_SESSION['uid']);
		write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"������ְͨλ:<strong>{$_POST['jobs_name']}</strong>�������Է�����ְͨλ:<strong>{$setmeal['jobs_ordinary']}</strong>��",2,1001,"����ְλ","1","{$setmeal['jobs_ordinary']}");
	}
	$searchtab['id']=$pid;
	$searchtab['uid']=$setsqlarr['uid'];
	$searchtab['subsite_id']=$setsqlarr['subsite_id'];
	$searchtab['recommend']=$setsqlarr['recommend'];
	$searchtab['emergency']=$setsqlarr['emergency'];
	$searchtab['nature']=$setsqlarr['nature'];
	$searchtab['sex']=$setsqlarr['sex'];
	$searchtab['topclass']=$setsqlarr['topclass'];
	$searchtab['category']=$setsqlarr['category'];
	$searchtab['subclass']=$setsqlarr['subclass'];
	$searchtab['trade']=$setsqlarr['trade'];
	$searchtab['district']=$setsqlarr['district'];
	$searchtab['sdistrict']=$setsqlarr['sdistrict'];	
	$searchtab['street']=$company_info['street'];
	$searchtab['education']=$setsqlarr['education'];
	$searchtab['experience']=$setsqlarr['experience'];
	$searchtab['wage']=$setsqlarr['wage'];
	$searchtab['refreshtime']=$setsqlarr['refreshtime'];
	$searchtab['scale']=$setsqlarr['scale'];	
	//
	inserttable(table('jobs_search_wage'),$searchtab);
	inserttable(table('jobs_search_scale'),$searchtab);
	//
	$searchtab['map_x']=$setsqlarr['map_x'];
	$searchtab['map_y']=$setsqlarr['map_y'];
	inserttable(table('jobs_search_rtime'),$searchtab);

	unset($searchtab['map_x'],$searchtab['map_y']);
	//
	$searchtab['stick']=$setsqlarr['stick'];
	inserttable(table('jobs_search_stickrtime'),$searchtab);

	unset($searchtab['stick']);
	//
	$searchtab['click']=$setsqlarr['click'];
	inserttable(table('jobs_search_hot'),$searchtab);

	unset($searchtab['click']);
	//
	$searchtab['key']=$setsqlarr['key'];
	$searchtab['likekey']=$setsqlarr['jobs_name'].','.$setsqlarr['companyname'];
	$searchtab['map_x']=$setsqlarr['map_x'];
	$searchtab['map_y']=$setsqlarr['map_y'];
	inserttable(table('jobs_search_key'),$searchtab);

	unset($searchtab);
	//
	// $tag=explode('|',$setsqlarr['tag']);
	// $tagindex=1;
	// $tagsql['tag1']=$tagsql['tag2']=$tagsql['tag3']=$tagsql['tag4']=$tagsql['tag5']=0;
	// if (!empty($tag) && is_array($tag))
	// {
	// 	foreach($tag as $v)
	// 	{
	// 	$vid=explode(',',$v);
	// 	$tagsql['tag'.$tagindex]=intval($vid[0]);
	// 	$tagindex++;
	// 	}
	// }
	$tagsql['id']=$pid;
	$tagsql['uid']=$setsqlarr['uid'];
	$tagsql['subsite_id']=$setsqlarr['subsite_id'];
	$tagsql['topclass']=$setsqlarr['topclass'];
	$tagsql['category']=$setsqlarr['category'];
	$tagsql['subclass']=$setsqlarr['subclass'];
	$tagsql['district']=$setsqlarr['district'];
	$tagsql['sdistrict']=$setsqlarr['sdistrict'];	
	inserttable(table('jobs_search_tag'),$tagsql);
	distribution_jobs($pid,$_SESSION['uid']);
	write_memberslog($_SESSION['uid'],1,2001,$_SESSION['username'],"������ְλ��{$setsqlarr['jobs_name']}");
	echo $pid;
}
// ְλ�����ɹ�ҳ��
elseif($act=="addjobs_save_succeed")
{
	$smarty->cache = false;
	$jobs_id=intval($_GET["id"]);
	$jobs=jobs_one($jobs_id);
	$sql="select * from ".table("resume")." as r left join ".table("resume_jobs")." as rj on rj.pid=r.id where rj.category=$jobs[category] limit 5 ";
	$resume_list=$db->getall($sql);
	foreach ($resume_list as $key => $val) {
		$val['age']=date("Y")-$val['birthdate'];
		$resume_list[$key]=$val;
	}
	$smarty->assign('resume_list',$resume_list);
	$smarty->display("wap/company/wap-create-job-success.html");
}
// ְλ�޸� ҳ��
elseif($act=="jobs_edit")
{
	$smarty->cache = false;
	$jobs=get_jobs_one($_GET['id'],$_SESSION['uid']);
	$company_info=get_company($_SESSION['uid']);
	$smarty->assign('company_info',$company_info);
	$smarty->assign('jobs',$jobs);
	$smarty->display("wap/company/wap-jobs_edit.html");
}
elseif($act=="jobs_edit_save")
{
	$smarty->cache = false;
	$company_info=get_company($_SESSION['uid']);
	$id=intval($_POST['id']);
	$days=intval($_POST['days']);
	$_POST=array_map("utf8_to_gbk", $_POST);
	// var_dump($_POST);die;
	$add_mode=trim($_POST['add_mode']);
	if ($add_mode=='1')
	{
		$points_rule=get_cache('points_rule');
		$user_points=get_user_points($_SESSION['uid']);
		$total=0;
		if($points_rule['jobs_edit']['type']=="2" && $points_rule['jobs_edit']['value']>0)
		{
		$total=$points_rule['jobs_edit']['value'];
		}
		if($points_rule['jobs_daily']['type']=="2" && $points_rule['jobs_daily']['value']>0)
		{
		$total=$total+($days*$points_rule['jobs_daily']['value']);
		}
		if ($total>$user_points)
		{
		exit("���".$_CFG['points_byname']."���㣬���ֵ���ٷ�����");
		}
	}
	elseif ($add_mode=='2')
	{
		$setmeal=get_user_setmeal($_SESSION['uid']);
		if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
		{					
			exit("�����ײ��Ѿ����ڣ������¿�ͨ");
		}
	}
	$setsqlarr['jobs_name']=!empty($_POST['jobs_name'])?trim($_POST['jobs_name']):exit('����дְλ���ƣ�');
	$setsqlarr['nature']=intval($_POST['nature'])?trim($_POST['nature']):exit('��ѡ��ְλ����!');
	$setsqlarr['nature_cn']=trim($_POST['nature_cn'])?trim($_POST['nature_cn']):exit('��ѡ��ְλ����!');
	$setsqlarr['topclass']=intval($_POST['topclass']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):exit('��ѡ��ְλ���');
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['amount']=intval($_POST['amount'])?intval($_POST['amount']):exit('����д��Ƹ����!');
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):exit('��ѡ����������');
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['wage']=intval($_POST['wage'])?intval($_POST['wage']):exit('��ѡ��н�ʴ�����');		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	// $setsqlarr['negotiable']=intval($_POST['negotiable']);
	// $setsqlarr['tag']=trim($_POST['tag']);
	$setsqlarr['sex']=intval($_POST['sex'])?intval($_POST['sex']):exit("��ѡ���Ա�!");
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['education']=intval($_POST['education'])?intval($_POST['education']):exit('��ѡ��ѧ��Ҫ��');		
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):exit('��ѡ�������飡');		
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['graduate']=intval($_POST['graduate']);
	// $setsqlarr['age']=trim($_POST['minage'])."-".trim($_POST['maxage']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):exit('��û����дְλ������');
	check_word($_CFG['filter'],$_POST['contents'])?exit($_CFG['filter_tips']):'';
	if ($add_mode=='1'){
		$setsqlarr['setmeal_deadline']=0;
		$setsqlarr['add_mode']=1;
	}elseif($add_mode=='2'){
		$setmeal=get_user_setmeal($_SESSION['uid']);
		$setsqlarr['setmeal_deadline']=$setmeal['endtime'];
		$setsqlarr['setmeal_id']=$setmeal['setmeal_id'];
		$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
		$setsqlarr['add_mode']=2;
	}
	if ($days>0)
	{
		if (intval($_POST['olddeadline'])>=time())
		{
			 $setsqlarr['deadline']=intval($_POST['olddeadline'])+($days*(60*60*24));
		}
		else
		{
			 $setsqlarr['deadline']=strtotime("{$days} day");
		}
	}
	$setsqlarr['key']=$setsqlarr['jobs_name'].$company_info['companyname'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$company_info['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	if ($company_info['audit']=="1")
	{
	$_CFG['audit_verifycom_editjob']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_verifycom_editjob']):'';
	}
	else
	{
	$_CFG['audit_unexaminedcom_editjob']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_unexaminedcom_editjob']):'';
	}
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):exit('��û����д��ϵ�ˣ�');
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):exit('��û����д��ϵ�绰��');
	check_word($_CFG['filter'],$_POST['telephone'])?exit($_CFG['filter_tips']):'';

	$setsqlarr_contact['contact_show']=1;
	$setsqlarr_contact['email_show']=1;
	$setsqlarr_contact['telephone_show']=1;
	$setsqlarr_contact['address_show']=1;

	if (!updatetable(table('jobs'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) exit("err");
	if (!updatetable(table('jobs_tmp'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) exit("err");
	if (!updatetable(table('jobs_contact'), $setsqlarr_contact," pid='{$id}' ")){
		exit("err");
	}
	if ($add_mode=='1')
	{
		if ($points_rule['jobs_edit']['value']>0)
		{
		report_deal($_SESSION['uid'],$points_rule['jobs_edit']['type'],$points_rule['jobs_edit']['value']);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['jobs_edit']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"�޸�ְλ��<strong>{$setsqlarr['jobs_name']}</strong>��({$operator}{$points_rule['jobs_edit']['value']})��(ʣ��:{$user_points})",1,1002,"�޸���Ƹ��Ϣ","{$operator}{$points_rule['jobs_edit']['value']}","{$user_points}");
		}
		if ($days>0 && $points_rule['jobs_daily']['value']>0)
		{
		$points_day=intval($_POST['days'])*$points_rule['jobs_daily']['value'];
		report_deal($_SESSION['uid'],$points_rule['jobs_daily']['type'],$points_day);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['jobs_daily']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"�ӳ�ְλ({$_POST['jobs_name']})��Ч��Ϊ{$_POST['days']}�죬({$operator}{$points_day})��(ʣ��:{$user_points})",1,1002,"�޸���Ƹ��Ϣ","{$operator}{$points_day}","{$user_points}");
		}
	}
	$searchtab['nature']=$setsqlarr['nature'];
	$searchtab['sex']=$setsqlarr['sex'];
	$searchtab['topclass']=$setsqlarr['topclass'];
	$searchtab['category']=$setsqlarr['category'];
	$searchtab['subclass']=$setsqlarr['subclass'];
	$searchtab['district']=$setsqlarr['district'];
	$searchtab['sdistrict']=$setsqlarr['sdistrict'];
	$searchtab['education']=$setsqlarr['education'];
	$searchtab['experience']=$setsqlarr['experience'];
	$searchtab['wage']=$setsqlarr['wage'];
	//
	updatetable(table('jobs_search_wage'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	updatetable(table('jobs_search_rtime'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	updatetable(table('jobs_search_stickrtime'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	updatetable(table('jobs_search_hot'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	updatetable(table('jobs_search_scale'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}'");
	$searchtab['key']=$setsqlarr['key'];
	$searchtab['likekey']=$setsqlarr['jobs_name'].','.$company_profile['companyname'];
	updatetable(table('jobs_search_key'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	unset($searchtab);
		$tag=explode('|',$setsqlarr['tag']);
		$tagindex=1;
		foreach($tag as $v)
		{
		$vid=explode(',',$v);
		$tagsql['tag'.$tagindex]=intval($vid[0]);
		$tagindex++;
		}
		$tagsql['id']=$id;
		$tagsql['uid']=$_SESSION['uid'];
		$tagsql['topclass']=$setsqlarr['topclass'];
		$tagsql['category']=$setsqlarr['category'];
		$tagsql['subclass']=$setsqlarr['subclass'];
		$tagsql['district']=$setsqlarr['district'];
		$tagsql['sdistrict']=$setsqlarr['sdistrict'];
	updatetable(table('jobs_search_tag'),$tagsql," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	// distribution_jobs($id,$_SESSION['uid']);
	write_memberslog($_SESSION['uid'],$_SESSION['utype'],2002,$_SESSION['username'],"�޸���ְλ��{$setsqlarr['jobs_name']}��ְλID��{$id}");
	exit("ok");
}
// ְλˢ��
elseif($act=="jobs_refresh")
{
	$smarty->cache = false;
	$id=intval($_POST['id']);
	refresh_jobs($id,intval($_SESSION['uid']))?exit("ok"):exit("err");
}
elseif($act=="jobs_match")
{
	echo "ƥ��";
}
// ��ͣ ְλ 
elseif($act=="jobs_pause")
{
	$smarty->cache = false;
	$id=intval($_POST['id']);
	$uid=intval($_SESSION["uid"]);
	$sql="update ".table("jobs")." set display=2 where id=$id and uid=$uid ";
	$db->query($sql)?exit("ok"):exit("err");
}
// ��ְͣλ �ָ�
elseif($act=="jobs_regain")
{
	$smarty->cache = false;
	$id=intval($_POST['id']);
	$uid=intval($_SESSION["uid"]);
	$sql="update ".table("jobs")." set display=1 where id=$id and uid=$uid ";
	$db->query($sql)?exit("ok"):exit("err");
}
// ɾ��ְλ 
elseif($act=="jobs_del")
{
	$smarty->cache = false;
	$id=intval($_POST['id']);
	del_jobs($id,intval($_SESSION['uid']))?exit("ok"):exit("err");
}
?>