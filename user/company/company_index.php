<?php
/*
 * 74cms ��ҵ��Ա����
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"index");
if ($act=='index')
{
	$uid=intval($_SESSION['uid']);
	$smarty->assign('title','��ҵ��Ա���� - '.$_CFG['site_name']);

	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$smarty->assign('loginlog',get_loginlog_one($uid,'1001'));
	
	$smarty->assign('user',$user);
	$smarty->assign('points',get_user_points($uid));
	$smarty->assign('concern_id',get_concern_id($uid));
	$smarty->assign('company',$company_profile);
	if ($_CFG['operation_mode']=='2' || $_CFG['operation_mode']=='3')
	{
		$setmeal=get_user_setmeal($uid);
		$smarty->assign('setmeal',$setmeal);
		if ($setmeal['endtime']>0)
		{
					$setmeal_endtime=sub_day($setmeal['endtime'],time());
					if (empty($setmeal_endtime))
					{
						$smarty->assign('meal_min_remind',"�Ѿ�����");
					}
					else
					{
						$smarty->assign('meal_min_remind',$setmeal_endtime);
					}
		}else{
			$smarty->assign('meal_min_remind',"������");
		}
	}
	$smarty->assign('msg_total1',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='2' AND `replyuid`<>'{$uid}'"));
	$smarty->assign('msg_total2',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1' AND `replyuid`<>'{$uid}'"));
	$smarty->assign('total_noaudit_jobs',$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE uid=".$uid." AND audit=2"));
	$smarty->assign('total_audit_jobs',$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs')." WHERE uid=".$uid));
	$smarty->assign('total_nolook_resume',$db->get_total("SELECT COUNT(*) AS num FROM ".table('personal_jobs_apply')." WHERE company_uid=".$uid." AND personal_look=1"));
	$smarty->assign('total_down_resume',$db->get_total("SELECT COUNT(*) AS num FROM ".table('company_down_resume')." WHERE company_uid=".$uid));
	$smarty->assign('total_favorites_resume',$db->get_total("SELECT COUNT(*) AS num FROM ".table('company_favorites')." WHERE company_uid=".$uid));
	$smarty->display('member_company/company_index.htm');
}
unset($smarty);
?>