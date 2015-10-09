<?php
 /*
 * 74cms ���˻�Ա����
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
function get_resume_list($wheresql,$titlele=12,$countinterview=false,$countdown=false,$countapply=false)
{
		global $db;
		$result = $db->query("{$wheresql} LIMIT 30");
		while($row = $db->fetch_array($result))
		{
			$row['resume_url']=url_rewrite('QS_resumeshow',array('id'=>$row['id']));
			$row['title']=cut_str($row['title'],$titlele,0,"...");
			$row['number']="N".str_pad($row['id'],7,"0",STR_PAD_LEFT);
			$row['lastname']=cut_str($row['fullname'],1,0,"**");
			if ($countinterview)
			{
			$wheresql=" WHERE resume_uid='{$row['uid']}' AND resume_id= '{$row['id']}'";
			$row['countinterview']=$db->get_total("SELECT COUNT(*) AS num FROM ".table('company_interview').$wheresql);
			}
			if ($countdown)
			{
			$wheresql=" WHERE resume_uid='{$row['uid']}' AND resume_id= '{$row['id']}'";
			$row['countdown']=$db->get_total("SELECT COUNT(*) AS num FROM ".table('company_down_resume').$wheresql);
			}
			if ($countapply)
			{
			$wheresql=" WHERE personal_uid='{$row['uid']}' AND resume_id= '{$row['id']}'";
			$row['countapply']=$db->get_total("SELECT COUNT(*) AS num FROM ".table('personal_jobs_apply').$wheresql);
			}
			$interest_jobs_id = get_interest_jobs_id_by_resume($row['uid'],$row['id']);
			$interest_jobs_id = explode("-",$interest_jobs_id);
			$interest_jobs_id = implode(",",$interest_jobs_id);
			if($interest_jobs_id){
				$cate = $db->getall("select id,parentid from ".table('category_jobs')." where id in (".$interest_jobs_id.")");
				foreach($cate as $k=>$v){
					$arr[] = $v['parentid'].".".$v['id'].".0";
				}
				$row['interestjobs'] = implode("|",$arr);
			}else{
				$row['interestjobs'] = "";
			}
			
			$row_arr[] = $row;
		}
		return $row_arr;
}
function get_auditresume_list($uid,$titlele=12)
{
		global $db;
		$uid=intval($uid);
		$result = $db->query("SELECT * FROM ".table('resume')." WHERE uid='{$uid}'".$wheresql);
		while($row = $db->fetch_array($result))
		{
			$row['resume_url']=url_rewrite('QS_resumeshow',array('id'=>$row['id']));
			$row['title']=cut_str($row['title'],$titlele,0,"...");
			$row['number']="N".str_pad($row['id'],7,"0",STR_PAD_LEFT);
			$row['lastname']=cut_str($row['fullname'],1,0,"**");
			$row_arr[] = $row;
		}
		return $row_arr;
}
//��ȡ����������Ϣ
function get_resume_basic($uid,$id)
{
	global $db;
	$id=intval($id);
	$uid=intval($uid);
	$info=$db->getone("select * from ".table('resume')." where id='{$id}'  AND uid='{$uid}' LIMIT 1 ");
	if (empty($info))
	{
	return false;
	}
	else
	{
	$info['age']=date("Y")-$info['birthdate'];
	$info['number']="N".str_pad($info['id'],7,"0",STR_PAD_LEFT);
	$info['lastname']=cut_str($info['fullname'],1,0,"**");
	return $info;
	}
}
//��ȡ���������б�
function get_resume_education($uid,$pid)
{
	global $db;
	if (intval($uid)!=$uid) return false;
	$sql = "SELECT * FROM ".table('resume_education')." WHERE  pid='".intval($pid)."' AND uid='".intval($uid)."' ";
	return $db->getall($sql);
}
//��ȡ ���� ��������
function get_resume_education_one($uid,$pid,$id)
{
	global $db;
	$sql = "select * from ".table('resume_education')." where id='".intval($id)."' AND uid='".intval($uid)."' AND pid='".intval($pid)."' LIMIT 1";
	return $db->getone($sql);
}
//��ȡ����������
function get_resume_work($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_work')." where pid='".$pid."' AND uid=".intval($uid)."" ;
	return $db->getall($sql);
}
//��ȡ ���� ��������
function get_resume_work_one($uid,$pid,$id)
{
	global $db;
	$sql = "select * from ".table('resume_work')." where id='".intval($id)."' AND uid='".intval($uid)."' AND pid='".intval($pid)."' LIMIT 1 ";
	return $db->getone($sql);
}
//��ȡ����ѵ�����б�
function get_resume_training($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_training')." where pid='".intval($pid)."' AND  uid='".intval($uid)."' ";
	return $db->getall($sql);
}
//��ȡ ���� ��ѵ����
function get_resume_training_one($uid,$pid,$id)
{
	global $db;
	$sql = "select * from ".table('resume_training')." where id='".intval($id)."' AND uid='".intval($uid)."'  AND pid='".intval($pid)."'  LIMIT 1 ";
	return $db->getone($sql);
}
//��ȡ����ְλ
function get_resume_jobs($pid)
{
	global $db;
	$pid=intval($pid);
	$sql = "select * from ".table('resume_jobs')." where pid='{$pid}'  LIMIT 20" ;
	return $db->getall($sql);
}
//��������ְλ
function add_resume_jobs($pid,$uid,$str)
{
	global $db;
	$db->query("Delete from ".table('resume_jobs')." WHERE pid='".intval($pid)."'");
	$str=trim($str);
	$arr=explode(",",$str);
	if (is_array($arr) && !empty($arr))
	{
		foreach($arr as $a)
		{
		$a=explode(".",$a);
		$setsqlarr['uid']=intval($uid);
		$setsqlarr['pid']=intval($pid);
		$setsqlarr['topclass']=intval($a[0]);
		$setsqlarr['category']=intval($a[1]);
		$setsqlarr['subclass']=intval($a[2]);
			if (!inserttable(table('resume_jobs'),$setsqlarr))return false;
		}
	}
	return true;
}
//����������ҵ
function add_resume_trade($pid,$uid,$str)
{
	global $db;
	$db->query("Delete from ".table('resume_trade')." WHERE pid='".intval($pid)."'");
	$str=trim($str);
	$arr=explode(",",$str);
	if (is_array($arr) && !empty($arr))
	{
		foreach($arr as $k=>$a)
		{
		$setsqlarr['uid']=intval($uid);
		$setsqlarr['pid']=intval($pid);
		$setsqlarr['trade']=intval($a);
			if (!inserttable(table('resume_trade'),$setsqlarr))return false;
		}
	}
	return true;
}
function get_user_info($uid)
{
	global $db;
	$sql = "select * from ".table('members')." where uid = ".intval($uid)." LIMIT 1";
	return $db->getone($sql);
}
function get_resumetpl()
{
	global $db;
	$sql = "select * from ".table('tpl')." where tpl_type =2 AND tpl_display=1";
	return $db->getall($sql);
}
function get_userprofile($uid)
{
	global $db;
	$sql = "select * from ".table('members_info')." where uid = ".intval($uid)." LIMIT 1";
	return $db->getone($sql);
}
function refresh_resume($pid,$uid)
{
	global $db;
	$time=time();
	$uid=intval($uid);
	if (!$db->query("update  ".table('resume')."  SET refreshtime='{$time}'  WHERE id='{$pid}' AND uid='{$uid}'")) return false;
	if (!$db->query("update  ".table('resume_search_rtime')."  SET refreshtime='{$time}'  WHERE id='{$pid}' AND uid='{$uid}'")) return false;
	if (!$db->query("update  ".table('resume_search_key')."  SET refreshtime='{$time}'  WHERE id='{$pid}' AND uid='{$uid}'")) return false;
	write_memberslog($_SESSION['uid'],2,1102,$_SESSION['username'],"ˢ����idΪ{$pid}�ļ���");
	write_refresh_log($_SESSION['uid'],2001);		
	return true;
}
//ɾ������
function del_resume($uid,$pid)
{
	global $db;
	$uid=intval($uid);
	if (!$db->query("Delete from ".table('resume')." WHERE id='{$pid}' AND uid='{$uid}' ")) return false;
	if (!$db->query("Delete from ".table('resume_jobs')." WHERE pid='{$pid}' AND uid='{$uid}' ")) return false;
	if (!$db->query("Delete from ".table('resume_trade')." WHERE pid='{$pid}' AND uid='{$uid}' ")) return false;
	if (!$db->query("Delete from ".table('resume_education')." WHERE pid='{$pid}' AND uid='{$uid}' ")) return false;
	if (!$db->query("Delete from ".table('resume_training')." WHERE pid='{$pid}' AND uid='{$uid}' ")) return false;
	if (!$db->query("Delete from ".table('resume_work')." WHERE pid='{$pid}' AND uid='{$uid}' ")) return false;
	if (!$db->query("Delete from ".table('resume_search_rtime')." WHERE id='{$pid}' AND uid='{$uid}' ")) return false;
	if (!$db->query("Delete from ".table('resume_search_key')." WHERE id='{$pid}' AND uid='{$uid}' ")) return false;
	if (!$db->query("Delete from ".table('resume_search_tag')." WHERE id='{$pid}' AND uid='{$uid}' ")) return false;
	write_memberslog($_SESSION['uid'],2,1103,$_SESSION['username'],"ɾ������({$pid})");
	return true;
}
//�޸ļ�����Ƭ��ʾ����
function edit_photo_display($uid,$pid,$display)
{
	global $db;
	$db->query("update  ".table('resume')."  SET photo_display='".intval($display)."' WHERE uid='".intval($uid)."' AND id='".intval($pid)."' LIMIT 1");
	return true;
}
//����������ɳ̶�
function check_resume($uid,$pid)
{
	global $db,$timestamp,$_CFG;
	$uid=intval($uid);
	$pid=intval($pid);
	$percent=0;
	$resume_basic=get_resume_basic($uid,$pid);
	$resume_education=get_resume_education($uid,$pid);
	$resume_work=get_resume_work($uid,$pid);
	$resume_training=get_resume_training($uid,$pid);
	$resume_tag=$resume_basic['tag'];
	$resume_specialty=$resume_basic['specialty'];
	$resume_photo=$resume_basic['photo_img'];
	if (!empty($resume_basic))$percent=$percent+40;
	if (!empty($resume_education))$percent=$percent+15;
	if (!empty($resume_work))$percent=$percent+15;
	if (!empty($resume_training))$percent=$percent+15;
	if (!empty($resume_tag))$percent=$percent+5;
	if (!empty($resume_specialty))$percent=$percent+5;
	if (!empty($resume_photo))$percent=$percent+5;
	if ($resume_basic['photo_img'] && $resume_basic['photo_audit']=="1"  && $resume_basic['photo_display']=="1")
	{
	$setsqlarr['photo']=1;
	}
	else
	{
	$setsqlarr['photo']=0;
	}
	$setsqlarr['complete_percent']=$percent;
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']=addslashes($resume_basic['intention_jobs']).addslashes($resume_basic['recentjobs']).addslashes($resume_basic['specialty']);		
	$setsqlarr['key']=addslashes($resume_basic['fullname']).$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=str_replace(","," ",addslashes($resume_basic['intention_jobs']))." {$setsqlarr['key']} ".addslashes($resume_basic['education_cn']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);	
	if (!empty($resume_education))
	{
		foreach($resume_education as $li)
		{
		$setsqlarr['key']=addslashes($li['school'])." {$setsqlarr['key']} ".addslashes($li['speciality']);
		}
	}
	if (!empty($resume_work))
	{
		foreach($resume_work as $li)
		{
		$setsqlarr['key']=addslashes($li['companyname'])." {$setsqlarr['key']} ".addslashes($li['speciality']);
		}
	}
	if (!empty($resume_training))
	{
		foreach($resume_training as $li)
		{
		$setsqlarr['key']=addslashes($li['agency'])." {$setsqlarr['key']} ".addslashes($li['speciality']);
		}
	}
	$setsqlarr['refreshtime']=$timestamp;
	if($setsqlarr['complete_percent']<60){
		$setsqlarr['level'] = 1;
	}elseif($setsqlarr['complete_percent']>=60 && $setsqlarr['complete_percent']<80){
		$setsqlarr['level'] = 2;
	}elseif($setsqlarr['complete_percent']>=80){
		$setsqlarr['level'] = 3;
	}
	updatetable(table('resume'),$setsqlarr,"uid='{$uid}' AND id='{$pid}'");
	// distribution_resume($pid,$uid);
	$j=get_resume_basic($uid,$pid);
	$j = array_map("addslashes", $j);
	$searchtab['sex']=$j['sex'];
	$searchtab['nature']=$j['nature'];
	$searchtab['marriage']=$j['marriage'];
	$searchtab['experience']=$j['experience'];
	$searchtab['district']=$j['district'];
	$searchtab['sdistrict']=$j['sdistrict'];
	$searchtab['wage']=$j['wage'];
	$searchtab['education']=$j['education'];
	$searchtab['photo']=$j['photo'];
	$searchtab['refreshtime']=$j['refreshtime'];
	$searchtab['talent']=$j['talent'];
	$searchtab['audit']=$j['audit'];
	updatetable(table('resume_search_rtime'),$searchtab,"uid='{$uid}' AND id='{$pid}'");
	$searchtab['key']=$j['key'];
	$searchtab['likekey']=$j['intention_jobs'].','.$j['trade_cn'].','.$j['specialty'].','.$j['fullname'];
	updatetable(table('resume_search_key'),$searchtab,"uid='{$uid}' AND id='{$pid}'");
	unset($searchtab);
	if($j['tag']){
		$tag=explode('|',$j['tag']);
		$tagindex=1;
		$tagsql['tag1']=$tagsql['tag2']=$tagsql['tag3']=$tagsql['tag4']=$tagsql['tag5']=0;
		if (!empty($tag) && is_array($tag))
		{
				foreach($tag as $v)
				{
					$vid=explode(',',$v);
					$tagsql['tag'.$tagindex]=intval($vid[0]);
					$tagindex++;
				}
		}
		$tagsql['id']=$j['id'];
		$tagsql['uid']=$j['uid'];
		$tagsql['experience']=$j['experience'];
		$tagsql['district']=$j['district'];
		$tagsql['sdistrict']=$j['sdistrict'];
		$tagsql['education']=$j['education'];
		$tagsql['audit']=$j['audit'];
		updatetable(table('resume_search_tag'),$tagsql,"uid='{$uid}' AND id='{$pid}'");
	}
}
function get_com_downresume($offset,$perpage,$get_sql='')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".intval($offset).','.intval($perpage);
	$select="d.*,c.id,c.companyname,c.addtime,c.district_cn,c.trade_cn,c.nature_cn";
	$sql="SELECT {$select} from ".table('company_down_resume')." AS d {$get_sql} ORDER BY did DESC {$limit}";
	$result = $db->query($sql);
	while($row = $db->fetch_array($result))
	{
	$row['company_url']=url_rewrite('QS_companyshow',array('id'=>$row['id']));
	$row_arr[] = $row;
	}
	return $row_arr;
}
//��������
function get_invitation($offset,$perpage,$get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".intval($offset).','.intval($perpage);
	$select="i.*,j.jobs_name,j.addtime,j.companyname,j.company_addtime,j.district_cn,j.wage_cn,j.deadline,j.refreshtime,j.click";
	$sql="SELECT {$select} from ".table('company_interview')." AS i {$get_sql} ORDER BY did DESC {$limit}";
	$result = $db->query($sql);
	while($row = $db->fetch_array($result))
	{
		if (empty($row['companyname']))
		{
			$jobs=$db->getone("select * from ".table('jobs_tmp')." WHERE id='{$row['jobs_id']}' LIMIT 1");
			$row['jobs_name']=$jobs['jobs_name'];
			$row['addtime']=$jobs['addtime'];
			$row['companyname']=$jobs['companyname'];
			$row['company_addtime']=$jobs['company_addtime'];
			$row['company_id']=$jobs['company_id'];
			$row['wage_cn']=$jobs['wage_cn'];
			$row['district_cn']=$jobs['district_cn'];
			$row['deadline']=$jobs['deadline'];
			$row['refreshtime']=$jobs['refreshtime'];
			$row['click']=$jobs['click'];
		}
	$row['company_url']=url_rewrite('QS_companyshow',array('id'=>$row['company_id']));
	$row['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$row['jobs_id']));
	$row_arr[] = $row;
	}
	return $row_arr;
}
function set_invitation($id,$uid,$setlook)
{
	global $db;
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin)) return false;
	$setsqlarr['personal_look']=intval($setlook);
	$wheresql=" did IN (".$sqlin.") AND resume_uid=".intval($uid)."";
	foreach($id as $aid)
	{
		$members=$db->getone("select m.username from ".table('company_interview')." AS i JOIN ".table('members')." AS m ON i.company_uid=m.uid WHERE i.did='{$aid}' LIMIT 1");
		write_memberslog($_SESSION['uid'],2,1108,$_SESSION['username'],"�鿴�� {$members['username']} ����������");
	}	
	return updatetable(table('company_interview'),$setsqlarr,$wheresql);
}
function add_favorites($id,$uid)
{
	global $db,$timestamp;
		if (strpos($id,"-"))
		{
			$id=str_replace("-",",",$id);
			if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$id)) return false;
		}
		else
		{
		$id=intval($id);
		}
	$sql = "select * from ".table('jobs')." WHERE id IN ({$id}) ";
	$jobs=$db->getall($sql);
	$i=0;
	foreach($jobs as $list)
	{
		$sql1 = "select jobs_id from ".table('personal_favorites')." where jobs_id=".$list['id']." AND personal_uid=".$uid."  LIMIT 1";
		if ($db->getone($sql1))
		{
		continue;
		}
		$setsqlarr['personal_uid']=$uid;
		$setsqlarr['jobs_id']=$list['id'];
		$setsqlarr['jobs_name']=$list['jobs_name'];
		$setsqlarr['addtime']=$timestamp;
		inserttable(table('personal_favorites'),$setsqlarr);
		$i=$i+1;
	}
	return $i;
}
function get_favorites($offset,$perpage,$get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT {$offset},{$perpage}";
	$select=" f.*,j.jobs_name,j.addtime as jobs_addtime,j.companyname,j.company_addtime,j.company_id,j.wage_cn,j.district_cn,j.deadline,j.refreshtime,j.click";
	$result = $db->query("SELECT {$select} FROM ".table('personal_favorites')." AS f {$get_sql} ORDER BY f.did DESC {$limit}");
	while($row = $db->fetch_array($result))
	{
		if (empty($row['companyname']))
		{
			$jobs=$db->getone("select * from ".table('jobs_tmp')." WHERE id='{$row['jobs_id']}' LIMIT 1");
			$row['jobs_name']=$jobs['jobs_name'];
			$row['jobs_addtime']=$jobs['addtime'];
			$row['companyname']=$jobs['companyname'];
			$row['company_addtime']=$jobs['company_addtime'];
			$row['company_id']=$jobs['company_id'];
			$row['wage_cn']=$jobs['wage_cn'];
			$row['district_cn']=$jobs['district_cn'];
			$row['deadline']=$jobs['deadline'];
			$row['refreshtime']=$jobs['refreshtime'];
			$row['click']=$jobs['click'];
		}
	$row['company_url']=url_rewrite('QS_companyshow',array('id'=>$row['company_id']));
	$row['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$row['jobs_id']));
	$row_arr[] = $row;
	}
	return $row_arr;
}
function del_favorites($id,$uid)
{
	global $db;
	$uidsql=" AND personal_uid=".intval($uid)."";
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin)) return false;
	$sql="Delete from ".table('personal_favorites')." WHERE did IN (".$sqlin.") ".$uidsql."";
	write_memberslog($_SESSION['uid'],2,1202,$_SESSION['username'],"ɾ����ְλ�ղ�($sqlin)");
	return $db->query($sql);
}
function check_jobs_apply($jobs_id,$resume_id,$p_uid)
{
	global $db;
	$sql = "select did from ".table('personal_jobs_apply')." WHERE personal_uid = '".intval($p_uid)."' AND jobs_id='".intval($jobs_id)."'  AND resume_id='".intval($resume_id)."' LIMIT 1";
	return $db->getone($sql);
}
function get_now_applyjobs_num($uid)
{
	global $db;
	$uid=intval($uid);
	$now = mktime(0,0,0,date("m"),date("d"),date("Y"));
	$wheresql=" WHERE personal_uid = '{$uid}' AND apply_addtime>{$now} ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('personal_jobs_apply').$wheresql;
	return $db->get_total($total_sql);
}
function get_apply_jobs($offset,$perpage,$get_sql= '')
{
	global $db;
	$limit=" LIMIT ".intval($offset).','.intval($perpage);
	$select=" a.*,j.jobs_name,j.addtime,j.company_id,j.companyname,j.company_addtime,j.wage_cn,j.district_cn,j.deadline,j.refreshtime,j.click";
	$sql="SELECT {$select} FROM ".table('personal_jobs_apply')." AS a{$get_sql} ORDER BY a.did DESC ".$limit;
	$result = $db->query($sql);
	while($row = $db->fetch_array($result))
	{
		if (empty($row['companyname']))
		{
			$jobs=$db->getone("select * from ".table('jobs_tmp')." WHERE id='{$row['jobs_id']}' LIMIT 1");
			$row['addtime']=$jobs['addtime'];
			$row['companyname']=$jobs['companyname'];
			$row['company_addtime']=$jobs['company_addtime'];
			$row['company_id']=$jobs['company_id'];
			$row['wage_cn']=$jobs['wage_cn'];
			$row['district_cn']=$jobs['district_cn'];
			$row['deadline']=$jobs['deadline'];
			$row['refreshtime']=$jobs['refreshtime'];
			$row['click']=$jobs['click'];
		}
		$resume = $db->getone("select title from ".table('resume')." where id=".$row['resume_id']);
		$row['resume_name'] = $resume['title'];
		$row['company_url']=url_rewrite('QS_companyshow',array('id'=>$row['company_id']));
		$row['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$row['jobs_id']));
		$row_arr[] = $row;
	}
return $row_arr;
}
function app_get_jobs($id)
{
	global $db;
	if (strpos($id,"-"))
	{
		$id=str_replace("-",",",$id);
		if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$id)) return false;
	}
	else
	{
	$id=intval($id);
	}
	$sql = "select * from ".table('jobs')." WHERE id IN ({$id}) ";
	return $db->getall($sql);
}
function del_jobs_apply($del_id,$uid)
{
	global $db;
	$uidsql=" AND personal_uid=".intval($uid)." ";
	if (!is_array($del_id)) $del_id=array($del_id);
	$sqlin=implode(",",$del_id);
	if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin)) return false;
	if (!$db->query("Delete from ".table('personal_jobs_apply')." WHERE did IN (".$sqlin.") ".$uidsql."")) return false;
	write_memberslog($_SESSION['uid'],2,1302,$_SESSION['username'],"ɾ����ְλ����($sqlin)");
	return true;
}
function count_resume($uid)
{
	global $db;
	$wheresql=" WHERE uid='".intval($uid)."' ";
	$total=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume').$wheresql);
	return $total;
}
function count_interview($uid,$look=NULL)
{
	global $db;
	$uid=intval($uid);
	$wheresql=" WHERE  resume_uid='{$uid}' ";
	if (intval($look)>0) $wheresql.=" AND  personal_look=".intval($look);
	$total_sql="SELECT COUNT(*) AS num FROM ".table('company_interview')." {$wheresql}";
	return $db->get_total($total_sql);
}
function count_personal_jobs_apply($uid,$look=NULL)
{
	global $db;
	$wheresql=" WHERE personal_uid='{$_SESSION['uid']}' ";
	if(intval($look)>0)	$wheresql.=" AND personal_look='{$look}' ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('personal_jobs_apply').$wheresql;
	return $db->get_total($total_sql);
}
function count_jobs_library($uid,$days=NULL)
{
	global $db;
	$wheresql=" WHERE personal_uid=".intval($uid)." ";
	if (intval($days)>0)
	{
	$settr_val=strtotime("-".$days." day");
	$wheresql.=" AND addtime>".$settr_val;
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('personal_favorites').$wheresql;
	return $db->get_total($total_sql);
}
function get_feedback($uid)
{
	global $db;
	$sql = "select * from ".table('feedback')." where uid='".intval($uid)."' ORDER BY id desc";
	return $db->getall($sql);
}
function del_feedback($del_id,$uid)
{
	global $db;
	if (!$db->query("Delete from ".table('feedback')." WHERE id='".intval($del_id)."' AND uid='".intval($uid)."'  ")) return false;
	write_memberslog($_SESSION['uid'],2,7002,$_SESSION['username'],"ɾ��������Ϣ($del_id)");
	return true;
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
function get_interest_jobs_id_by_resume($uid,$pid)
{
	global $db;
	$uid=intval($uid);
	$sql = "select id from ".table('resume')." where   uid='{$uid}' AND id='{$pid}' LIMIT 3 ";
	$info=$db->getone($sql);
	$interest_id = array();
	$jobsid=get_resume_jobs($info['id']);
	if(is_array($jobsid))
	{
		foreach($jobsid as $cid)
	    {
	    	$interest_id[]=$cid['category'];
	    }
	}
	if (!empty($interest_id)) return implode("-",array_unique($interest_id));
	return "";
}
function get_interest_jobs_list($cid){
	global $db;
	$orderbysql = " order by refreshtime desc ";
	$limitsql = " limit 3 ";
	$list=array();
	if($cid){
		if(strpos("-", $cid)){
			$wheresql = "";
			$arr = explode("-", $cid);
			foreach ($arr as $key => $value) {
				$wheresql .= "OR category=".$value." ";
			}
			$wheresql = $wheresql?" WHERE ".trim($wheresql,"OR"):"";
			$list = $db->getall("select * from ".table('jobs').$wheresql.$orderbysql.$limitsql);
		}else{
			$list = $db->getall("select * from ".table('jobs')." where category=".$cid.$orderbysql.$limitsql);
		}
	}
	if(empty($list)){
		$list = $db->getall("select * from ".table("jobs").$orderbysql.$limitsql);
	}
	return $list;
}
function check_jobs_report($uid,$jobs_id)
{
	global $db;
	$sql = "select id from ".table('report')." WHERE uid = '".intval($uid)."' AND jobs_id='".intval($jobs_id)."' LIMIT 1";
	return $db->getone($sql);
}
function get_pms($offset,$perpage,$get_sql= '')
{
	global $db;
	if(isset($offset)&&!empty($perpage))
	{
	$limit=" LIMIT {$offset},{$perpage}";
	}
	$result = $db->query($get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
		$row_arr[] = $row;
	}
	return $row_arr;
}
//3.4
function app_get_course($id)
{
	global $db;
	$id=intval($id);
	$sql = "select * from ".table('course')." WHERE id ={$id} limit 1";
	return $db->getone($sql);
}
//3.4
function get_now_applycour_num($uid)
{
	global $db;
	$uid=intval($uid);
	$now = mktime(0,0,0,date("m"),date("d"),date("Y"));
	$wheresql=" WHERE personal_uid = '{$uid}' AND apply_addtime>{$now} ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('personal_course_apply').$wheresql;
	return $db->get_total($total_sql);
}
//3.4
function check_course_apply($courseid,$uid)
{
	global $db;
	$sql = "select did from ".table('personal_course_apply')." WHERE personal_uid = '".intval($uid)."' AND course_id='".intval($courseid)."' LIMIT 1";
	return $db->getone($sql);
}
function get_apply_course($offset,$perpage,$get_sql= '')
{
	global $db;
	$limit=" LIMIT {$offset},{$perpage}";
	$result = $db->query("SELECT * FROM ".table('personal_course_apply')."  {$get_sql} ORDER BY did DESC {$limit}");
	while($row = $db->fetch_array($result))
	{
	$row['course_url']=url_rewrite('QS_train_curriculumshow',array('id'=>$row['course_id']),false);
	$row['train_url']=url_rewrite('QS_train_agencyshow',array('id'=>$row['train_id']),false);
	$row_arr[] = $row;
	}
	return $row_arr;
}
function count_personal_cour_apply($uid,$look=NULL)
{
	global $db;
	$wheresql=" WHERE personal_uid='{$_SESSION['uid']}' ";
	if(intval($look)>0)	$wheresql.=" AND personal_look='{$look}' ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('personal_course_apply').$wheresql;
	return $db->get_total($total_sql);
}
function del_apply($del_id,$uid)
{
	global $db;
	$uidsql=" AND personal_uid=".intval($uid)."";
	if (!is_array($del_id)) $del_id=array($del_id);
	$sqlin=implode(",",$del_id);
	if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin)) return false;
	if (!$db->query("Delete from ".table('personal_course_apply')." WHERE did IN ({$sqlin}) {$uidsql}")) return false;
	write_memberslog($_SESSION['uid'],2,1402,$_SESSION['username'],"ɾ���γ�����({$sqlin})");
	return true;
}

function count_personal_resume_down($uid)
{
	global $db;
	$wheresql=" WHERE resume_uid='{$uid}' ";
	$num=$db->get_total("SELECT COUNT(*) AS num FROM ".table('company_down_resume').$wheresql);
	return $num;
}
function get_my_resume($uid){
	global $db;
	$wheresql = " where uid=".$uid." ";
	$sql="SELECT id FROM ".table('resume').$wheresql;
	$my_resume = $db->getall($sql);
	foreach ($my_resume as $key => $value) {
		$idarr[] = $value['id'];
	}
	$idstr = implode(",",$idarr);
	return $idstr;
}
?>