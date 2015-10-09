<?php
 /*
 * 74cms �������� �����û���غ���
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
 //******************************��������**********************************
function get_resume_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query($get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row['resume_url']=url_rewrite('QS_resumeshow',array('id'=>$row['id']));
	$row_arr[] = $row;
	}
	return $row_arr;
}
function del_resume($id)
{
	global $db;
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	if (!$db->query("Delete from ".table('resume')." WHERE id IN ({$sqlin})")) return false;
	$return=$return+$db->affected_rows();
	if (!$db->query("Delete from ".table('resume_jobs')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_trade')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_education')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_training')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_work')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_search_rtime')." WHERE id IN ({$sqlin})")) return false;
	if (!$db->query("Delete from ".table('resume_search_key')." WHERE id IN ({$sqlin})")) return false;
	if (!$db->query("Delete from ".table('resume_search_tag')." WHERE id IN ({$sqlin})")) return false;
	return $return;
	}
	return $return;
}
function del_resume_for_uid($uid)
{
	global $db;
	if (!is_array($uid)) $uid=array($uid);
	$sqlin=implode(",",$uid);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$result = $db->query("SELECT id FROM ".table('resume')." WHERE uid IN (".$sqlin.")");
		while($row = $db->fetch_array($result))
		{
		$rid[]=$row['id'];
		}
		if (empty($rid))
		{
		return true;
		}
		else
		{
		return del_resume($rid);
		}		
	}
}
function edit_resume_audit($id,$audit,$reason,$pms_notice)
{
	global $db,$_CFG;
	$audit=intval($audit);
	if (!is_array($id))  $id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('resume')." SET audit='{$audit}'  WHERE id IN ({$sqlin}) ")) return false;
		if (!$db->query("update  ".table('resume_search_key')." SET audit='{$audit}'  WHERE id IN ({$sqlin}) ")) return false;
		if (!$db->query("update  ".table('resume_search_rtime')." SET audit='{$audit}'  WHERE id IN ({$sqlin}) ")) return false;
		if (!$db->query("update  ".table('resume_search_tag')." SET audit='{$audit}'  WHERE id IN ({$sqlin}) ")) return false;
		foreach ($id as $key => $value) {
			set_resume_entrust($value);
		}
		// distribution_resume($id);
		//����վ����
		if ($pms_notice=='1')
		{
				$result = $db->query("SELECT  fullname,title,uid  FROM ".table('resume')." WHERE id IN ({$sqlin})");
				$reason=$reason==''?'ԭ��δ֪':'ԭ��'.$reason;
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					$setsqlarr['message']=$audit=='1'?"�������ļ�����{$list['title']},��ʵ������{$list['fullname']},�ɹ�ͨ����վ����Ա��ˣ�":"�������ļ�����{$list['title']},��ʵ������{$list['fullname']},δͨ����վ����Ա���,{$reason}";
					$setsqlarr['msgtype']=1;
					$setsqlarr['msgtouid']=$user_info['uid'];
					$setsqlarr['msgtoname']=$user_info['username'];
					$setsqlarr['dateline']=time();
					$setsqlarr['replytime']=time();
					$setsqlarr['new']=1;
					inserttable(table('pms'),$setsqlarr);
				 }
		}
		//���δͨ������ԭ��
		if($audit=='3'){
			foreach($id as $list){
				$auditsqlarr['resume_id']=$list;
				$auditsqlarr['reason']=$reason;
				$auditsqlarr['addtime']=time();
				inserttable(table('audit_reason'),$auditsqlarr);
			}
		}
			
			//�����ʼ�
				$mailconfig=get_cache('mailconfig');//��ȡ�ʼ�����
				$sms=get_cache('sms_config');
				if ($audit=="1" && $mailconfig['set_resumeallow']=="1")//���ͨ��
				{
						$result = $db->query("SELECT * FROM ".table('resume')." WHERE id IN ({$sqlin}) ");
						while($list = $db->fetch_array($result))
						{
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&act=set_resumeallow");
						}
				}
				if ($audit=="3" && $mailconfig['set_resumenotallow']=="1")//���δͨ��
				{
					$result = $db->query("SELECT * FROM ".table('resume')." WHERE id IN ({$sqlin}) ");
						while($list = $db->fetch_array($result))
						{
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&act=set_resumenotallow");
						}
				}
				//sms		
				if ($audit=="1" && $sms['open']=="1" && $sms['set_resumeallow']=="1" )
				{
					$result = $db->query("SELECT * FROM ".table('resume')." WHERE id IN ({$sqlin}) ");
						while($list = $db->fetch_array($result))
						{
							$user_info=get_user($list['uid']);
							if ($user_info['mobile_audit']=="1")
							{
							dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&act=set_resumeallow");
							}
						}
				}
				//sms
				if ($audit=="3" && $sms['open']=="1" && $sms['set_resumenotallow']=="1" )//��֤δͨ��
				{
					$result = $db->query("SELECT * FROM ".table('resume')." WHERE id IN ({$sqlin}) ");
						while($list = $db->fetch_array($result))
						{
							$user_info=get_user($list['uid']);
							if ($user_info['mobile_audit']=="1")
							{
							dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&act=set_resumenotallow");
							}
						}
				}
				//sms
			//�����ʼ�
	return true;
	}
	return false;
}
//�޸���Ƭ���״̬
function edit_resume_photoaudit($id,$audit)
{
	global $db;
	$audit=intval($audit);
	if (!is_array($id)) $id=array($id);
	if (!empty($id))
	{
		foreach($id as $i)
		{
			$i=intval($i);
			$tb1=$db->getone("select photo_img,photo_audit,photo_display from ".table('resume')." WHERE id='{$i}' LIMIT  1");
			if (!empty($tb1))
			{
				if ($tb1['photo_img'] && $audit=="1" && $tb1['photo_display']=="1")
				{
				$photo=1;
				}
				else
				{
				$photo=0;
				}	
				$db->query("update  ".table('resume')." SET photo_audit='{$audit}',photo='{$photo}' WHERE id='{$i}' LIMIT 1 ");
				$db->query("update  ".table('resume_search_rtime')." SET photo='{$photo}' WHERE id='{$i}' LIMIT 1 ");
				$db->query("update  ".table('resume_search_key')." SET photo='{$photo}' WHERE id='{$i}' LIMIT 1 ");				
			}
		}
	}
	return true;
}
//�޸��˲ŵȼ�
function edit_resume_talent($id,$talent)
{
	global $db;
	$talent=intval($talent);
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	if (!$db->query("update  ".table('resume')." SET talent={$talent}  WHERE id IN ({$sqlin})")) return false;
	if (!$db->query("update  ".table('resume_search_rtime')." SET talent={$talent}  WHERE id IN ({$sqlin})")) return false;
	if (!$db->query("update  ".table('resume_search_key')." SET talent={$talent}  WHERE id IN ({$sqlin})")) return false;
	return true;
	}
	return false;
}
//��UID��ȡ���м���
function get_resume_uid($uid)
{
	global $db;
	$uid=intval($uid);
	$result = $db->query("select * FROM ".table('resume')." where uid='{$uid}'");
	while($row = $db->fetch_array($result))
	{ 
	$row['resume_url']=url_rewrite('QS_resumeshow',array('id'=>$row['id']));
	$row_arr[] = $row;
	}
	return $row_arr;	
}
function refresh_resume($id)
{
	global $db;
	$return=0;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('resume')." SET refreshtime='".time()."'  WHERE id IN (".$sqlin.")")) return false;
		$return=$return+$db->affected_rows();
		if (!$db->query("update  ".table('resume_search_rtime')." SET refreshtime='".time()."'  WHERE id IN (".$sqlin.")")) return false;
		if (!$db->query("update  ".table('resume_search_key')." SET refreshtime='".time()."'  WHERE id IN (".$sqlin.")")) return false;
	}
	return $return;
}
//**************************���˻�Ա�б�
function get_member_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;	
	$result = $db->query("SELECT * FROM ".table('members')." as m ".$get_sql.$limit);
		while($row = $db->fetch_array($result))
		{
		$row_arr[] = $row;
		}
	return $row_arr;
}
function delete_member($uid)
{
	global $db;
	if (!is_array($uid)) $uid=array($uid);
	$sqlin=implode(",",$uid);
		if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
		{
					if(defined('UC_API'))
					{
						include_once(QISHI_ROOT_PATH.'uc_client/client.php');
						foreach($uid as $tuid)
						{
						$userinfo=get_user($tuid);
						$uc_user=uc_get_user($userinfo['username']);
						$uc_uid_arr[]=$uc_user[0];
						}
						uc_user_delete($uc_uid_arr);
					} 
		if (!$db->query("Delete from ".table('members')." WHERE uid IN (".$sqlin.")")) return false;
		if (!$db->query("Delete from ".table('members_info')." WHERE uid IN (".$sqlin.")")) return false;
		return true;
		}
	return false;
}
function get_member_one($memberuid)
{
	global $db;
	$sql = "select * from ".table('members')." where uid=".intval($memberuid)." LIMIT 1";
	$val=$db->getone($sql);
	return $val;
}
function get_user($uid)
{
	global $db;
	$uid=intval($uid);
	$sql = "select * from ".table('members')." where uid = '{$uid}' LIMIT 1";
	return $db->getone($sql);
}
//��ȡ�����������־
function get_resumeaudit_one($resume_id){
	global $db;
	$sql = "select * from ".table('audit_reason')."  WHERE resume_id='".intval($resume_id)."' ORDER BY id DESC";
	return $db->getall($sql);
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
	$info['tagcn']=preg_replace("/\d+/", '',$info['tag']);
	$info['tagcn']=preg_replace('/\,/','',$info['tagcn']);
	$info['tagcn']=preg_replace('/\|/','&nbsp;&nbsp;&nbsp;',$info['tagcn']);
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
//��ȡ����������
function get_resume_work($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_work')." where pid='".$pid."' AND uid=".intval($uid)."" ;
	return $db->getall($sql);
}
//��ȡ����ѵ�����б�
function get_resume_training($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_training')." where pid='".intval($pid)."' AND  uid='".intval($uid)."' ";
	return $db->getall($sql);
}
//��ȡ����ְλ
function get_resume_jobs($pid)
{
	global $db;
	$pid=intval($pid);
	$sql = "select * from ".table('resume_jobs')." where pid='{$pid}'  LIMIT 20" ;
	return $db->getall($sql);
}
function reasonaudit_del($id)
{
	global $db;
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin)) return false;
	if (!$db->query("Delete from ".table('audit_reason')." WHERE id IN ({$sqlin})")) return false;
	return $db->affected_rows();
}
function export_resume($yid){
	global $db;
	$yid_str = implode(",", $yid);
	$oederbysql=" order BY refreshtime desc ";
	$wheresql = empty($wheresql)?" id in ({$yid_str}) ":" and id in ({$yid_str}) ";
	if (!empty($wheresql))
	{
	$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
	}
	$data = $db->getall("select * from ".table('resume').$wheresql);
	
	if(!empty($data)){
		$result = $data;
	}
	if(!empty($result)){
		foreach ($result as $key => $value) {
			$arr[$key]['num'] = $key;
			$arr[$key]['title'] = $value['title'];
			$arr[$key]['fullname'] = $value['fullname'];
			$arr[$key]['sex_cn'] = $value['sex_cn'];
			$arr[$key]['birthdate'] = $value['birthdate'];
			$arr[$key]['height'] = $value['height'];
			$arr[$key]['householdaddress'] = $value['householdaddress'];
			$arr[$key]['marriage_cn'] = $value['marriage_cn'];
			$arr[$key]['experience_cn'] = $value['experience_cn'];
			$arr[$key]['education_cn'] = $value['education_cn'];
			$arr[$key]['natrue_cn'] = $value['natrue_cn'];
			$arr[$key]['trade_cn'] = $value['trade_cn'];
			$arr[$key]['district_cn'] = $value['district_cn'];
			$arr[$key]['wage_cn'] = $value['wage_cn'];
			$arr[$key]['tag']=preg_replace("/\d+/", '',$value['tag']);
			$arr[$key]['tag']=preg_replace('/\,/','',$arr[$key]['tag']);
			$arr[$key]['tag']=preg_replace('/\|/','&nbsp;&nbsp;&nbsp;',$arr[$key]['tag']);
			$arr[$key]['school'] = "";
			$school = $db->getall("select * from ".table('resume_education')." where pid=".$value['id']." order by id desc");
			if(!empty($school)){
				foreach ($school as $key1 => $value1) {
					$arr[$key]['school'] .= $value1['start']."-".$value1['endtime']."�Ͷ���".$value1['school'].",��ѧרҵ��".$value1['speciality'].",ѧ����".$value1['education_cn'].";&nbsp;";
				}
			}
			$arr[$key]['work'] = "";
			$work = $db->getall("select * from ".table('resume_work')." where pid=".$value['id']." order by id desc");
			if(!empty($work)){
				foreach ($work as $key1 => $value1) {
					$arr[$key]['work'] .= $value1['start']."-".$value1['endtime']."��ְ��".$value1['companyname'].",��ְ��".$value1['jobs'].";&nbsp;";
				}
			}
			$arr[$key]['train'] = "";
			$train = $db->getall("select * from ".table('resume_training')." where pid=".$value['id']." order by id desc");
			if(!empty($train)){
				foreach ($train as $key1 => $value1) {
					$arr[$key]['train'] .= $value1['start']."-".$value1['endtime']."��".$value1['agency']."��ѵ".$value1['course']."�γ�;&nbsp;";
				}
			}
			$arr[$key]['telephone'] = $value['telephone'];
			$arr[$key]['email'] = $value['email'];
			$arr[$key]['qq'] = $value['qq'];
			$arr[$key]['address'] = $value['address'];
			$arr[$key]['website'] = $value['website'];
			$arr[$key]['recentjobs'] = $value['recentjobs'];
			$arr[$key]['intention_jobs'] = $value['intention_jobs'];
			$arr[$key]['specialty'] = str_replace("\n","",str_replace("\r", "", $value['specialty']));
			$arr[$key]['addtime'] = date("Y-m-d",$value['addtime']);
			$arr[$key]['refreshtime'] = date("Y-m-d",$value['refreshtime']);
			$arr[$key]['talent'] = $value['talent']==1?"��ͨ":"�߼�";
			$arr[$key]['complete_percent'] = $value['complete_percent'];
		}
		$top_str = "���\t��������\t����\t�Ա�\t��������\t���\t�������ڵ�\t����״��\t��������\tѧ��\t����ְλ����\t������ҵ\t����������\t����н��\t��ǩ\t��������\t��������\t��ѵ����\t�ֻ�\t����\tQQ\t��ַ\t������ҳ\t������¹���\t����ְλ\t�����س�\t���ʱ��\tˢ��ʱ��\t�����ȼ�\t������\t\n";
		create_excel($top_str,$arr);
		return true;
	}else{
		return false;
	}
	
}
function set_resume_entrust($resume_id){
	global $db;
	$resume = $db->getone("select audit,uid,fullname,addtime,entrust from ".table('resume')." where id=".$resume_id);
	if($resume["audit"]=="1" && $resume["entrust"]=="1"){
		$has = $db->getone("select 1 from ".table('resume_entrust')." where id=".$resume_id);
		if(!$has){
			$setsqlarr['id'] = $resume_id;
			$setsqlarr['uid'] = $resume['uid'];
			$setsqlarr['fullname'] = $resume['fullname'];
			$setsqlarr['resume_addtime'] = $resume['addtime'];
			inserttable(table('resume_entrust'),$setsqlarr);
			updatetable(table('resume'),array("entrust"=>"0")," id=".$resume_id." ");
		}
	}
	else
	{
		$db->query("delete from ".table('resume_entrust')." where id=".$resume_id);
	}
	return true;
}
?>