<?php
 /*
 * 74cms ��Ա��¼
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
$alias="QS_login";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_user.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);
$smarty->caching = false;
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'login';
if($act == 'logout')
{
	unset($_SESSION['uid']);
	unset($_SESSION['username']);
	unset($_SESSION['utype']);
	setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[username]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[password]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	unset($_SESSION['activate_username']);
	unset($_SESSION['activate_email']);
	header("location:index.php"); 
}
elseif(!$_SESSION['uid'] && !$_SESSION['username'] && !$_SESSION['utype'] &&  $_COOKIE['QS']['username'] && $_COOKIE['QS']['password'] )
{
	if(check_cookie($_COOKIE['QS']['username'],$_COOKIE['QS']['password']))
	{
	update_user_info($_COOKIE['QS']['username'],false,false);
			if($_SESSION['utype']==2)	header("location:personal/wap_user.php");
			if($_SESSION['utype']==1)	header("location:company/wap_user.php");
	}
	else
	{
	setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[username]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[password]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	header("location:index.php"); 
	}
}
elseif ($_SESSION['username'] && $_SESSION['utype'] )
{
			if($_SESSION['utype']==2)	header("location:personal/wap_user.php");
			if($_SESSION['utype']==1)	header("location:company/wap_user.php");
}
elseif ($act=='login')
{
	$smarty->display('wap/wap_login.html');
}
elseif ($act == 'do_login')
{
	require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
	if($_POST['username']=="�û���/�ֻ���/����" || $_POST['password']==""|| $_POST['username']=="" ){
		$smarty->assign('err',"�������û�����");
		$smarty->display('wap/wap_login.html');
	}else{
		$username=isset($_POST['username'])?trim($_POST['username']):"";
		$password=isset($_POST['password'])?trim($_POST['password']):"";
		$expire=isset($_POST['expire'])?intval($_POST['expire']):"";
		if ($username && $password)
		{
			if (wap_user_login($username,$password))
			{
				if($_SESSION['utype']==2)	header("location:personal/wap_user.php");
				if($_SESSION['utype']==1)	header("location:company/wap_user.php");
			}
			else
			{
				$smarty->caching = false;
				$smarty->assign('err',"�û���¼ʧ�ܣ��û������������");
				$smarty->display('wap/wap_login.html');
			}		
		}
	}

}

unset($smarty);
?>