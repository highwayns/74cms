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
$smarty->assign('leftmenu',"apply");
if ($act=='down')
{
	$perpage=10;
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$joinsql=" LEFT JOIN  ".table('company_profile')." AS c  ON d.company_uid=c.uid ";
	$wheresql=" WHERE d.resume_uid='{$_SESSION['uid']}' ";
	$resume_id =intval($_GET['resume_id']);
	if($resume_id>0)$wheresql.=" AND  d.resume_id='{$resume_id}' ";	
	$settr=intval($_GET['settr']);
	if($settr>0)
	{
	$settr_val=strtotime("-".$settr." day");
	$wheresql.=" AND d.down_addtime>".$settr_val;
	}
	$total_sql="SELECT COUNT(*) AS num from ".table('company_down_resume')." AS d {$wheresql}";
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('title',"˭���ص��ҵļ��� - ���˻�Ա���� - {$_CFG['site_name']}");
	$smarty->assign('mylist',get_com_downresume($offset,$perpage,$joinsql.$wheresql));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('count',$total_val);
	$smarty->assign('act',$act);
	$smarty->assign('resume_list',get_auditresume_list($_SESSION['uid']));
	$smarty->display('member_personal/personal_downresume.htm');
}
//�������� �б�
elseif ($act=='interview')
{
	$perpage=10;
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE  i.resume_uid='{$_SESSION['uid']}' ";
	$look=intval($_GET['look']);
	if($look>0)
	{
	$wheresql.=" AND  i.personal_look={$look}";
	}
	$resume_id =intval($_GET['resume_id']);
	if($resume_id>0)
	{
		$wheresql.=" AND  i.resume_id='{$resume_id}' ";	
		$sql="select title from ".table("resume")." where id=".intval($_GET['resume_id'])." ";
		$row=$db->getone($sql);
		$smarty->assign('resume_title',$row["title"]);
	}
	//ɸѡ ��ְͨλ����(0)  ���Ǹ߼�ְλ����(1)
	$jobs_type=intval($_GET['jobs_type']);
	if($jobs_type != 1)
	{
		$total_sql="SELECT COUNT(*) AS num from ".table('company_interview')." AS i {$wheresql} ";
		$total_val=$db->get_total($total_sql);
		$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
		$currenpage=$page->nowindex;
		$offset=($currenpage-1)*$perpage;
		$joinsql=" LEFT JOIN  ".table('jobs')." AS j  ON  i.jobs_id=j.id ";
		$smarty->assign('interview',get_invitation($offset, $perpage,$joinsql.$wheresql));
	}
	if($total_val > $perpage)
	{
		$smarty->assign('page',$page->show(3));
	}
	$smarty->assign('title','�յ����������� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$count[0]=count_interview($_SESSION['uid'],$jobs_type,1);  //δ��
	$count[1]=count_interview($_SESSION['uid'],$jobs_type,2);  //�ѿ�
	$count[2]=$count[0]+$count[1];
	$smarty->assign('count',$count);
	$smarty->assign('resume_list',get_interview_resumes($_SESSION['uid']));
	$smarty->display('member_personal/personal_interview.htm');
}
elseif ($act=='set_interview')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ����Ŀ��",1);
	$jobs_type=intval($_GET['jobs_type']);
	$n=set_invitation($yid,$_SESSION['uid'],2);
	if($n)
	{
		showmsg("���óɹ���",2);
	}
	else
	{
		showmsg("����ʧ�ܣ�",0);
	}
}
elseif ($act=='interview_del')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ����Ŀ��",1);
	$jobs_type=intval($_GET['jobs_type']);
	$n=del_interview($yid,$_SESSION['uid']);
	if(intval($n) > 0)
	{
	showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
	showmsg("ʧ�ܣ�",0);
	}
}
//ְλ�ղؼ��б�
elseif ($act=='favorites')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE f.personal_uid='{$_SESSION['uid']}' ";
	$settr=intval($_GET['settr']);
	if($settr>0)
	{
	$settr_val=strtotime("-".$settr." day");
	$wheresql.=" AND f.addtime>".$settr_val;
	}
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('personal_favorites')." AS f {$wheresql} ";
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('title','ְλ�ղؼ� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$joinsql=" LEFT JOIN ".table('jobs')." as  j  ON f.jobs_id=j.id ";
	$smarty->assign('favorites',get_favorites($offset,$perpage,$joinsql.$wheresql));
	if($total_val > $perpage)
	{
		$smarty->assign('page',$page->show(3));
	}
	$smarty->display('member_personal/personal_favorites.htm');
}
elseif ($act=='del_favorites')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ����Ŀ��",1);
	if($n=del_favorites($yid,$_SESSION['uid']))
	{
		showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
		showmsg("ɾ��ʧ�ܣ�",0);
	}
}
//�����ְλ�б�
elseif ($act=='apply_jobs')
{
	$joinsql = '';
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE a.personal_uid='{$_SESSION['uid']}' ";
	$resume_id =intval($_GET['resume_id']);
	//ɸѡ����
	if($resume_id>0)
	{
		$wheresql.=" AND  a.resume_id='{$resume_id}' ";	
	}
	//ɸѡ �Է��Ƿ��Ѳ鿴
	$aetlook=intval($_GET['aetlook']);
	if($aetlook>0)
	{
		$wheresql.=" AND a.personal_look='{$aetlook}'";
	}
	//ɸѡ ����ʱ��
	$settr=intval($_GET['settr']);
	if($settr>0)
	{
	$settr_val=strtotime("-".$settr." day");
	$wheresql.=" AND a.apply_addtime>".$settr_val;
	}
	//ɸѡ ���� (1->��ҵδ�鿴  2->������  3->����  4->������  5->����  6->δ��ͨ)
	$reply_id=intval($_GET['reply_id']);
	if($reply_id == 1)
	{
		$wheresql.=" AND a.personal_look='1' ";
	}
	elseif($reply_id == 2)
	{
		$wheresql.=" AND a.personal_look='2' AND a.is_reply=0  ";
	}
	elseif($reply_id == 3)
	{
		$wheresql.=" AND a.personal_look='2' AND a.is_reply=1 ";
	}
	elseif($reply_id == 4)
	{
		$wheresql.=" AND a.personal_look='2' AND a.is_reply=2 ";
	}
	elseif($reply_id == 5)
	{
		$wheresql.=" AND a.personal_look='2' AND a.is_reply=3 ";
	}
	elseif($reply_id == 6)
	{
		$wheresql.=" AND a.personal_look='2' AND a.is_reply=4 ";
	}
	$perpage=10;
	//ɸѡ ��ְͨλ(0) �� ��ͷְλ(1)
	$jobs_type=intval($_GET['jobs_type']);
	$total_sql="SELECT COUNT(*) AS num FROM ".table('personal_jobs_apply')." AS a {$wheresql} ";
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage,'getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$joinsql.=" LEFT JOIN ".table('jobs')." AS j ON a.jobs_id=j.id ";
	$smarty->assign('jobs_apply',get_apply_jobs($offset,$perpage,$joinsql,$wheresql));
	$smarty->assign('title','�������ְλ - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	if($total_val > $perpage)
	{
		$smarty->assign('page',$page->show(3));
	}
	$count[0]=count_personal_jobs_apply($jobs_type,$_SESSION['uid'],1); //δ�鿴
	$count[1]=count_personal_jobs_apply($jobs_type,$_SESSION['uid'],2); //�Ѳ鿴
	$count[2]=$count[0]+$count[1];
	$smarty->assign('count',$count);
	$smarty->assign('resume_list',get_apply_jobs_resumes($_SESSION['uid']));
	$smarty->display('member_personal/personal_apply_jobs.htm');
}
//ɾ��-�����ְλ�б�
elseif ($act=='del_jobs_apply')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("��û��ѡ����Ŀ��",1);
	$jobs_type=intval($_GET['jobs_type']);
	$n=del_jobs_apply($yid,$_SESSION['uid']);
	if(intval($n) > 0)
	{
		showmsg("ɾ���ɹ���",2);
	}
	else
	{
		showmsg("ɾ��ʧ�ܣ�",0);
	}
}
unset($smarty);
?>