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
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
wap_weixin_logon($_GET['from']);
$row=jobs_one($_GET['id']);
if($_SESSION["uid"] && $_SESSION["utype"]==2){
	$sql="select * from ".table("resume")." where uid=$_SESSION[uid] ";
	$resume_list = $db->getall($sql);
	$smarty->assign('resume_list',$resume_list);
}
$smarty->assign('show',$row);
$smarty->assign('goback',$_SERVER["HTTP_REFERER"]);
$smarty->display("wap/wap-jobs-show.html");
?>