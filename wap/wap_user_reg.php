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
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'reg';
$smarty->caching = false;
if ($act == 'reg')
{
$smarty->display("wap/wap_reg.html");
}
elseif ($act=='form')
{
	if ($_CFG['closereg']=='1')WapShowMsg("��վ��ͣ��Աע�ᣬ���Ժ��ٴγ��ԣ�",1);
	if (intval($_GET['type'])==0)WapShowMsg("��ѡ��ע�����ͣ�",1);
	if(intval($_GET['type'])>2){
		WapShowMsg("��Ա���Ͳ���ȷ��������ѡ��",1);
	}
	$smarty->assign('type',$_GET['type']);
	$captcha=get_cache('captcha');
	$smarty->assign('verify_userreg',$captcha['verify_userreg']);
	$smarty->display('wap/reg_form.html');
}
elseif ($act == 'do_reg')
{
	require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
	require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
	$username = isset($_POST['username'])?trim($_POST['username']):"";
	$password = isset($_POST['password'])?trim($_POST['password']):"";
	$member_type =intval($_POST['utype']);
	$email = isset($_POST['email'])?trim($_POST['email']):"";
	if (empty($username)||empty($password)||empty($member_type)||empty($email))
	{
	$err="��Ϣ������";
	}
	elseif (strlen($username)<6 || strlen($username)>18)
	{
	$err="�û�������Ϊ6-18���ַ�";
	}
	elseif (strlen($password)<6 || strlen($password)>18)
	{
	$err="���볤��Ϊ6-18���ַ�";
	}
	elseif ($password<>$_POST['password1'])
	{
	$err="������������벻ͬ";
	}
	elseif (empty($email) || !ereg("^[-a-zA-Z0-9_\.]+\@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$",$email))
	{
	$err="���������ʽ����";
	}
	if (get_user_inusername($username))
	{
	$err="�û����Ѿ�����";
	}
	if (get_user_inemail($email))
	{
	$err="���������Ѿ�����";
	}	
	if ($err)
	{
	$smarty->assign('err',$err);
	$smarty->assign('type',$member_type);
	$smarty->display("wap/reg_form.html");
	exit();
	}	
	
	$register=user_register($username,$password,$member_type,$email,$mobile);
	if ($register>0)
	{
		$login_js=wap_user_login($username,$password);
		if ($login_js)
		{
			header("location:".$login_js['qs_login']);
		}
	}
	else
	{
	header("location:wap/wap_reg.php");
	}
}
?>