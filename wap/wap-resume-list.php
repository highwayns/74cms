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
$page = empty($_GET['page'])?1:intval($_GET['page']);
$district = intval($_GET['district'])==0?"":intval($_GET['district']);
$sdistrict = intval($_GET['sdistrict'])==0?"":intval($_GET['sdistrict']);
$experience = intval($_GET['experience'])==0?"":intval($_GET['experience']);
$education = intval($_GET['education'])==0?"":intval($_GET['education']);
$topclass = intval($_GET['topclass'])==0?"":intval($_GET['topclass']);
$category = intval($_GET['category'])==0?"":intval($_GET['category']);
$subclass = intval($_GET['subclass'])==0?"":intval($_GET['subclass']);

$talent = intval($_GET['talent'])==0?"":intval($_GET['talent']);

$key = empty($_GET['key'])?"":$_GET['key'];
$jobstable=table('resume_search_rtime');

if($talent<>'')
{
	$wheresql.=" AND `talent`=".$talent." ";
}

if ($district<>'')
{
	$wheresql.=" AND `district`=".$district." ";
}
if ($sdistrict<>'')
{
	$wheresql.=" AND `sdistrict`=".$sdistrict." ";
}
if ($experience<>'')
{
	$wheresql.=" AND `experience`=".$experience." ";
}
if ($education<>'')
{
	$wheresql.=" AND `education`=".$education." ";
}
if ($topclass<>'' || $category<>'' || $subclass<>'')
{
	if ($topclass<>'')
	{
		$joinwheresql.=" AND  topclass=".$topclass;
	}
	if ($category<>'')
	{
		$joinwheresql.=" AND  category=".$category;
	}
	if ($subclass<>'')
	{
		$joinwheresql.=" AND  subclass=".$subclass;
	}
	if (!empty($joinwheresql))
	{
	$joinwheresql=" WHERE ".ltrim(ltrim($joinwheresql),'AND');
	}
	$joinsql="  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_jobs')." {$joinwheresql} )AS j ON  r.id=j.pid ";
}

$orderbysql=" ORDER BY `refreshtime` desc";
if (!empty($key))
{
	$key=trim($key);
	$akey=explode(' ',$key);
	if (count($akey)>1)
	{
	$akey=array_filter($akey);
	$akey=array_slice($akey,0,2);
	$akey=array_map("fulltextpad",$akey);
	$ykey='+'.implode(' +',$akey);
	$mode=' IN BOOLEAN MODE';
	}
	else
	{
	$ykey=fulltextpad($key);
	$mode=' ';
	}
	$wheresql.=" AND  MATCH (`key`) AGAINST ('{$ykey}'{$mode}) ";
	$orderbysql="";
	$jobstable=table('resume_search_key');
}
if (!empty($wheresql))
{
$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
}

	$perpage = 5;
	$count  = 0;
	$page = empty($_GET['page'])?1:intval($_GET['page']);
	if($page<1) $page = 1;
	
	$theurl = "wap-resume-list.php?sdistrict=".$sdistrict."&amp;subclass=".$subclass."&amp;key=".$key;
	$start = ($page-1)*$perpage;
	$total_sql="SELECT COUNT(*) AS num FROM {$jobstable} as r {$joinsql} {$wheresql}";
	$count=$db->get_total($total_sql);
	$limit=" LIMIT {$start},{$perpage}";
	$idresult = $db->query("SELECT id FROM {$jobstable} as r ".$joinsql.$wheresql.$orderbysql.$limit);
	while($row = $db->fetch_array($idresult))
	{
	$id[]=$row['id'];
	}
	if (!empty($id))
	{
		$wheresql=" WHERE id IN (".implode(',',$id).") ";
		$resume = $db->getall("SELECT * FROM ".table('resume').$wheresql.$orderbysql);	
		foreach ($resume as $key => $value) {
			$resume[$key]['url'] = wap_url_rewrite("wap-resume-show",array("id"=>$value["id"]));
		}
		
	}
	else
	{
		$resume=array();
	}
	$smarty->assign('resume',$resume);
	$smarty->assign('pagehtml',wapmulti($count, $perpage, $page, $theurl));
	$smarty->display("wap/wap-resume-list.html");
?>