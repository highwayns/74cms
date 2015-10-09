<?php
 /*
 * 74cms 共用配置文件
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
if(!defined('IN_QISHI')) exit('Access Denied!');
define('QISHI_ROOT_PATH',dirname(dirname(__FILE__)).'/');
error_reporting(E_ERROR);
ini_set('session.save_handler', 'files');
session_save_path(QISHI_ROOT_PATH.'data/sessions/');
session_start();
require_once(QISHI_ROOT_PATH.'data/config.php');
header("Content-Type:text/html;charset=".QISHI_CHARSET);
require_once(QISHI_ROOT_PATH.'include/common.fun.php');
require_once(QISHI_ROOT_PATH.'include/74cms_version.php');
$QSstarttime=exectime();

if (!empty($_GET))
{
$_GET  = addslashes_deep($_GET);
}
if (!empty($_POST))
{
$_POST = addslashes_deep($_POST);
}
$_COOKIE   = addslashes_deep($_COOKIE);
$_REQUEST  = addslashes_deep($_REQUEST);
date_default_timezone_set("PRC");
$timestamp = time();
$online_ip=getip();
$ip_address=convertip($online_ip);
$_NAV=get_cache('nav');
$_PAGE=get_cache('page');
$_CFG=get_cache('config');
$_PLUG=get_cache('plug');
$_CFG['main_domain'] = $_CFG['site_domain'].$_CFG['site_dir'];
$_CFG['wap_domain'] = $_CFG['wap_domain']==""?$_CFG['main_domain']."wap":$_CFG['wap_domain'];
$_CFG['version']=QISHI_VERSION;
$_CFG['web_logo']=$_CFG['web_logo']?$_CFG['web_logo']:'logo.gif';
$_CFG['upfiles_dir']=$_CFG['main_domain']."data/".$_CFG['updir_images']."/";
$_CFG['thumb_dir']=$_CFG['main_domain']."data/".$_CFG['updir_thumb']."/";
$_CFG['resume_photo_dir']=$_CFG['main_domain']."data/".$_CFG['resume_photo_dir']."/";
$_CFG['resume_photo_dir_thumb']=$_CFG['main_domain']."data/".$_CFG['resume_photo_dir_thumb']."/";
$_CFG['teacher_photo_dir']=$_CFG['main_domain']."data/train_teachers/";
$_CFG['teacher_photo_dir_thumb']=$_CFG['main_domain']."data/train_teachers/thumb/";
$_CFG['hunter_photo_dir']=$_CFG['main_domain']."data/hunter/";
$_CFG['hunter_photo_dir_thumb']=$_CFG['main_domain']."data/hunter/thumb/";
$_CFG['subsite_id']=0;
subsiteinfo($_CFG);
$_CFG['site_template']=$_CFG['main_domain'].'templates/'.$_CFG['template_dir'];
$mypage=$_PAGE[$alias];
$mypage['tag']?$page_select=$mypage['tag']:'';
require_once(QISHI_ROOT_PATH.'include/tpl.inc.php');
	if ($_CFG['isclose'])
	{
				$smarty->assign('info',$_CFG['close_reason']=$_CFG['close_reason']?$_CFG['close_reason']:'站点暂时关闭...');
				$smarty->display('warning.htm');
				exit();
	}
	if ($_CFG['filter_ip'] && check_word($_CFG['filter_ip'],$online_ip))
	{
			$smarty->assign('info',$_CFG['filter_ip_tips']);
			$smarty->display('warning.htm');
			exit();
	}
?>