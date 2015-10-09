<?php
function tpl_function_qishi_resume_show($params, &$smarty)
{
global $db,$_CFG,$QS_cookiepath,$QS_cookiedomain;
$arr=explode(',',$params['set']);
foreach($arr as $str)
{
$a=explode(':',$str);
	switch ($a[0])
	{
	case "简历ID":
		$aset['id'] = $a[1];
		break;
	case "列表名":
		$aset['listname'] = $a[1];
		break;
	}
}
$aset=array_map("get_smarty_request",$aset);
$aset['id']=$aset['id']?intval($aset['id']):0;
$aset['listname']=$aset['listname']?$aset['listname']:"list";
$wheresql=" WHERE  id=".$aset['id']."";
$val=$db->getone("select * from ".table('resume').$wheresql." LIMIT  1");
if(intval($_SESSION['utype'])==1){
	$company_profile = $db->getone("select companyname from ".table('company_profile')." where uid=".intval($_SESSION['uid']));
}
if ($val)
{
	setcookie('QS[view_resume_log]['.$val['id'].']',$val['id'],0,$QS_cookiepath,$QS_cookiedomain);
	
	if ($val['display_name']=="2")
	{
		$val['fullname']="N".str_pad($val['id'],7,"0",STR_PAD_LEFT);
		$val['fullname_']=$val['fullname'];		
	}
	elseif($val['display_name']=="3")
	{
		$val['fullname']=cut_str($val['fullname'],1,0,"**");
		$val['fullname_']=$val['fullname'];	
	}
	else
	{
		$val['fullname_']=$val['fullname'];
		$val['fullname']=$val['fullname'];
	}
	$val['education_list']=get_this_education($val['uid'],$val['id']);
	$val['work_list']=get_this_work($val['uid'],$val['id']);
	$val['training_list']=get_this_training($val['uid'],$val['id']);
	$val['age']=date("Y")-$val['birthdate'];
	if ($val['photo']=="1")
	{
	$val['photosrc']=$_CFG['resume_photo_dir_thumb'].$val['photo_img'];
	}
	else
	{
	$val['photosrc']=$_CFG['resume_photo_dir_thumb']."no_photo.gif";
	}
	if ($val['tag'])
	{
		$tag=explode('|',$val['tag']);
		$taglist=array();
		if (!empty($tag) && is_array($tag))
		{
			foreach($tag as $t)
			{
			$tli=explode(',',$t);
			$taglist[]=array($tli[0],$tli[1]);
			}
		}
		$val['tag']=$taglist;
	}
	else
	{
	$val['tag']=array();
	}
	if(intval($_GET['apply'])==1){
		$val['apply'] = 1;
		$apply = $db->getone("select * from ".table('personal_jobs_apply')." where `resume_id`=".$val['id']);
		$val['jobs_name'] = $apply['jobs_name'];
		$val['jobs_url'] = url_rewrite('QS_jobsshow',array('id'=>$apply['jobs_id']));
	}else{
		$val['apply'] = 0;
	}
}
else
{
	header("HTTP/1.1 404 Not Found"); 
	$smarty->display("404.htm");
	exit();
}
$smarty->assign($aset['listname'],$val);
}
function get_this_education($uid,$pid)
{
	global $db;
	$sql = "SELECT * FROM ".table('resume_education')." WHERE uid='".intval($uid)."' AND pid='".intval($pid)."' ";
	return $db->getall($sql);
}
function get_this_work($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_work')." where uid=".intval($uid)." AND pid='".$pid."' " ;
	return $db->getall($sql);
}
function get_this_training($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_training')." where uid='".intval($uid)."' AND pid='".intval($pid)."'";
	return $db->getall($sql);
}
?>