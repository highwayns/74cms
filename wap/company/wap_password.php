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
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_company.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'password_edit';
if ($_SESSION['uid']=='' || $_SESSION['username']==''||intval($_SESSION['utype'])==2)
{
	header("Location: ../wap_login.php");
}
elseif ($act == 'password_edit')
{
	$uid = intval($_SESSION['uid']);
	//total�������������Ավ���ŵ����� 
	$smarty->assign('total',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1'"));
	$smarty->assign('title','�޸����� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->display("wap/company/wap-password.html");
	
}
elseif ($act == 'save_password')
{	
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$arr['username']=$_SESSION['username'];
	$arr['oldpassword']=trim($_POST['oldpassword'])?trim($_POST['oldpassword']):exit('����������룡');
	$arr['password']=trim($_POST['password'])?trim($_POST['password']):exit('�����������룡');
	if ($arr['password']!=trim($_POST['password1'])) exit('�����������벻��ͬ�����������룡');
	//fun_user.php��edit_password()�޸�����ķ���
	$info=edit_password($arr);
	if ($info==-1) exit('����������������������룡');
	if ($info==$_SESSION['username']){
		//�����ʼ�
		$mailconfig=get_cache('mailconfig');
		if ($mailconfig['set_editpwd']=="1" && $user['email_audit']=="1")
		{
		dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$_SESSION['uid']."&key=".asyn_userkey($_SESSION['uid'])."&act=set_editpwd&newpassword=".$arr['password']);
		}
		//�ʼ��������
		//sms
		$sms=get_cache('sms_config');
		if ($sms['open']=="1" && $sms['set_editpwd']=="1"  && $user['mobile_audit']=="1")
		{
		dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$_SESSION['uid']."&key=".asyn_userkey($_SESSION['uid'])."&act=set_editpwd&newpassword=".$arr['password']);
		}
		//sms
		if(defined('UC_API'))
		{
		include_once(QISHI_ROOT_PATH.'uc_client/client.php');
		uc_user_edit($arr['username'],$arr['oldpassword'], $arr['password']);
		}
		//����Ա��־�����¼
		write_memberslog($_SESSION['uid'],2,1004 ,$_SESSION['username'],"�޸�����");
		exit('�����޸ĳɹ���');
	 }
}

?>