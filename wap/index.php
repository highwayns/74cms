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
$emergency_jobs = $db->getall("SELECT * FROM ".table('jobs')." WHERE `emergency`=1 ORDER BY `refreshtime` DESC LIMIT 5");	
foreach ($emergency_jobs as $key => $value) {
	$emergency_jobs[$key]['url'] = wap_url_rewrite("wap-jobs-show",array("id"=>$value["id"]));
}
$smarty->assign('emergency_jobs',$emergency_jobs);
$smarty->display("wap/wap.html");
?>