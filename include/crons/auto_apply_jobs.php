<?php
 /*
 * 74cms �ƻ����� �������
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
if(!defined('IN_QISHI'))
{
die('Access Denied!');
}
	global $_CFG,$db;
	$result = $db->query("select e.*,r.* from ".table('resume_entrust')." as e left join ".table('resume_jobs')." as r on e.id=r.pid");
	while($row = $db->fetch_array($result))
	{
		$jobs = $db->getall("select id,jobs_name,company_id,companyname,uid from ".table('jobs')." where category=".$row['category']." or subclass=".$row['subclass']." limit 5");
		if(empty($jobs)){
			continue;
		}else{
			$resume_basic = $db->getone("select id,display_name,fullname from ".table('resume')." where id=".$row['pid']);
			foreach ($jobs as $key => $value) {
				if (check_jobs_apply($value['id'],$row['pid'],$row['uid']))
				{
				 continue ;
				}
				if ($resume_basic['display_name']=="2")
				{
					$personal_fullname="N".str_pad($resume_basic['id'],7,"0",STR_PAD_LEFT);
				}
				elseif($resume_basic['display_name']=="3")
				{
					$personal_fullname=cut_str($resume_basic['fullname'],1,0,"**");
				}
				else
				{
					$personal_fullname=$resume_basic['fullname'];
				}
		 		$addarr['resume_id']=$resume_basic['id'];
				$addarr['resume_name']=$personal_fullname;
				$addarr['personal_uid']=intval($row['uid']);
				$addarr['jobs_id']=$value['id'];
				$addarr['jobs_name']=$value['jobs_name'];
				$addarr['company_id']=$value['company_id'];
				$addarr['company_name']=$value['companyname'];
				$addarr['company_uid']=$value['uid'];
				$addarr['notes']= "ϵͳ�Զ�Ͷ��";
				$addarr['apply_addtime']=time();
				$addarr['personal_look']=1;
				inserttable(table('personal_jobs_apply'),$addarr);	
			}
			$db->query("delete from ".table('resume_entrust')." where id=".$row['pid']);
			updatetable(table("resume"),array("entrust"=>"0")," id=".$row['pid']." ");
		}
		// $db->query("UPDATE ".table('jobs_search_wage')." SET refreshtime='{$time}' WHERE id='{$row['cp_jobid']}' LIMIT 1");
	}
	//��������ʱ���
	if ($crons['weekday']>=0)
	{
	$weekday=array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$nextrun=strtotime("Next ".$weekday[$crons['weekday']]);
	}
	elseif ($crons['day']>0)
	{
	$nextrun=strtotime('+1 months'); 
	$nextrun=mktime(0,0,0,date("m",$nextrun),$crons['day'],date("Y",$nextrun));
	}
	else
	{
	$nextrun=time();
	}
	if ($crons['hour']>=0)
	{
	$nextrun=strtotime('+1 days',$nextrun); 
	$nextrun=mktime($crons['hour'],0,0,date("m",$nextrun),date("d",$nextrun),date("Y",$nextrun));
	}
	if (intval($crons['minute'])>0)
	{
	$nextrun=strtotime('+1 hours',$nextrun); 
	$nextrun=mktime(date("H",$nextrun),$crons['minute'],0,date("m",$nextrun),date("d",$nextrun),date("Y",$nextrun));
	}
	$setsqlarr['nextrun']=$nextrun;
	$setsqlarr['lastrun']=time();
	updatetable(table('crons'), $setsqlarr," cronid ='".intval($crons['cronid'])."'");
function check_jobs_apply($jobs_id,$resume_id,$p_uid)
{
	global $db;
	$sql = "select did from ".table('personal_jobs_apply')." WHERE personal_uid = '".intval($p_uid)."' AND jobs_id='".intval($jobs_id)."'  AND resume_id='".intval($resume_id)."' LIMIT 1";
	return $db->getone($sql);
}
?>