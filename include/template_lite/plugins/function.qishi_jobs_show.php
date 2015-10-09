<?php
function tpl_function_qishi_jobs_show($params, &$smarty)
{
	global $db,$timestamp,$_CFG;
	$arr=explode(',',$params['set']);
	foreach($arr as $str)
	{
	$a=explode(':',$str);
		switch ($a[0])
		{
		case "职位ID":
			$aset['id'] = $a[1];
			break;
		case "列表名":
			$aset['listname'] = $a[1];
			break;
		case "描述长度":
			$aset['brieflylen'] = $a[1];
			break;
		case "填补字符":
			$aset['dot'] = $a[1];
			break;
		}
	}
	$aset=array_map("get_smarty_request",$aset);
	$aset['id']=$aset['id']?intval($aset['id']):0;
	$aset['brieflylen']=isset($aset['brieflylen'])?intval($aset['brieflylen']):0;
	$aset['listname']=$aset['listname']?$aset['listname']:"list";
	$wheresql=" WHERE id={$aset['id']} ";
	
	$sql = "select * from ".table('jobs').$wheresql." LIMIT 1";
	$val=$db->getone($sql);
	if (empty($val))
	{
		header("HTTP/1.1 404 Not Found"); 
		$smarty->display("404.htm");
		exit();
	}
	else
	{
			if ($val['setmeal_deadline']<time() && $val['setmeal_deadline']<>"0" && $val['add_mode']=="2")
			{
			$val['deadline']=$val['setmeal_deadline'];
			}
			$val['amount']=$val['amount']=="0"?'若干':$val['amount'];
			$val['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$val['id']),false);
			$profile=GetJobsCompanyProfile($val['company_id']);
			$val['company']=$profile;
			$val['contact']=GetJobsContact($val['id']);
			$district_cn = $val['district_cn'];
			$d_arr = explode("/", $district_cn);
			$val['district_ch'] = $d_arr[0];
			$val['sdistrict_ch'] = $d_arr[1];
			$val['expire']=sub_day($val['deadline'],time());	
			$wheresql=" WHERE company_uid='{$row['uid']}' AND jobs_id= '{$row['id']}'";
			$val['countresume']=$db->get_total("SELECT COUNT(*) AS num FROM ".table('personal_jobs_apply')." WHERE jobs_id= '{$val['id']}'");
			if ($aset['brieflylen']>0)
			{
				$val['briefly']=cut_str(strip_tags($val['contents']),$aset['brieflylen'],0,$val['dot']);
			}
			else
			{
				$val['briefly']=strip_tags($val['contents']);
			}
			$val['refreshtime_cn']=daterange(time(),$val['refreshtime'],'Y-m-d',"#FF3300");
			$val['company_url']=url_rewrite('QS_companyshow',array('id'=>$val['company_id']));
			if ($val['company']['logo'])
			{
			$val['company']['logo']=$_CFG['main_domain']."data/logo/".$val['company']['logo'];
			}
			else
			{
			$val['company']['logo']=$_CFG['main_domain']."data/logo/no_logo.gif";
			}
			if($val['company']['website']){
				if(strstr($val['company']['website'],"http://")===false){
					$val['company']['website'] = "http://".$val['company']['website'];
				}
			}
			if(intval($_SESSION['utype'])==2){
				$interest_id = get_interest_jobs_id(intval($_SESSION['uid']));
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
	}
$smarty->assign($aset['listname'],$val);
}
function GetJobsCompanyProfile($id)
{
	global $db;
	$sql = "select * from ".table('company_profile')." where id=".intval($id)." LIMIT 1 ";
	return $db->getone($sql);
}
function GetJobsContact($id)
{
	global $db;
	$sql = "select * from ".table('jobs_contact')." where pid=".intval($id)." LIMIT 1 ";
	return $db->getone($sql);
}
function get_interest_jobs_id($uid)
{
	global $db;
	$uid=intval($uid);
	$sql = "select id from ".table('resume')." where   uid='{$uid}' LIMIT 3 ";
	$info=$db->getall($sql);
	if (is_array($info))
	{
		foreach($info as $s)
		{
			$jobsid=get_resume_jobs($s['id']);
			if(is_array($jobsid))
			{
			foreach($jobsid as $cid)
			 {
			 $interest_id[]=$cid['category'];
			 }
			}
		}
		if (is_array($interest_id)) return implode("-",array_unique($interest_id));
	}
	return "";	
}
//获取意向职位
function get_resume_jobs($pid)
{
	global $db;
	$pid=intval($pid);
	$sql = "select * from ".table('resume_jobs')." where pid='{$pid}'  LIMIT 20" ;
	return $db->getall($sql);
}
//模糊匹配
function search_strs($arr,$str)
{
	foreach ($arr as $key =>$list)
	{
		similar_text($list,$str,$percent);
		$od[$percent]=$key;
	}
	krsort($od);
	foreach ($od as $key =>$li)
	{
		if ($key>=60)
		{
			return $li;
		}
		else
		{
			return false;
		}
	}
}
?>