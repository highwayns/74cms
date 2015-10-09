<?php
/*
 * 74cms ��ҵ��Ա����
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"jobs");
if ($act=='jobs')
{
	$jobtype=intval($_GET['jobtype']);
	$wheresql=" WHERE uid='{$_SESSION['uid']}' ";
	$orderby=" order by refreshtime desc";
	switch($jobtype){
		case 1:
			$tabletype="all";
			break;
		case 2:
			$tabletype="jobs_tmp";
			$wheresql.=" AND display=2";
			break;
		case 3:
			$tabletype="jobs_tmp";
			$wheresql.=" AND audit=2 ";
			break;
		case 4:
			$tabletype="jobs_tmp";
			$wheresql.=" AND audit=3 ";
			break;
		case 5:
			$tabletype="jobs_tmp";
			$wheresql.=" AND deadline<".time()." ";
			break;
		default:
			$tabletype="jobs";
			break;
	}
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	if ($tabletype=="all")
	{
	$total_sql="SELECT COUNT(*) AS num FROM ".table('jobs').$wheresql." UNION ALL  SELECT COUNT(*) AS num FROM ".table('jobs_tmp').$wheresql;
	}
	else
	{
	$total_sql="SELECT COUNT(*) AS num FROM ".table($tabletype).$wheresql;
	}
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$offset=($page->nowindex-1)*$perpage;
	$smarty->assign('title','ְλ���� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('audit',$audit);
	if ($tabletype=="all")
	{
	$sql="SELECT * FROM ".table('jobs').$wheresql." UNION ALL SELECT * FROM ".table('jobs_tmp').$wheresql.$orderby;
	}
	else
	{
	$sql="SELECT * FROM ".table($tabletype).$wheresql.$orderby;
	}
	$total[0]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs')." WHERE uid='{$_SESSION['uid']}'");
	$total[1]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE uid='{$_SESSION['uid']}'");
	$total[2]=$total[0]+$total[1];
	$smarty->assign('total',$total);
	$smarty->assign('setmeal',get_user_setmeal($_SESSION['uid']));
	$smarty->assign('jobs',get_jobs($offset,$perpage,$sql,true));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('points_rule',get_cache('points_rule'));
	$smarty->display('member_company/company_jobs.htm');
}
elseif ($act=='addjobs')
{
		$smarty->assign('user',$user);
		if ($company_profile['companyname'])
		{
			$_SESSION['addrand']=rand(1000,5000);
			$smarty->assign('addrand',$_SESSION['addrand']);
			$smarty->assign('title','����ְλ - ��ҵ��Ա���� - '.$_CFG['site_name']);
			$smarty->assign('company_profile',$company_profile);
			if ($_CFG['operation_mode']=="3")
			{
				$setmeal=get_user_setmeal($_SESSION['uid']);
				if (($setmeal['endtime']>time() || $setmeal['endtime']=="0") &&  $setmeal['jobs_ordinary']>0)
				{
				$smarty->assign('setmeal',$setmeal);
				$smarty->assign('add_mode',2);
				}
				elseif($_CFG['setmeal_to_points']=="1")
				{
				$smarty->assign('points_total',get_user_points($_SESSION['uid']));
				$smarty->assign('points',get_cache('points_rule'));
				$smarty->assign('add_mode',1);
				}
				else
				{
				$smarty->assign('setmeal',$setmeal);
				$smarty->assign('add_mode',2);
				}
				
			}
			elseif ($_CFG['operation_mode']=="2")
			{
				$setmeal=get_user_setmeal($_SESSION['uid']);
				$smarty->assign('setmeal',$setmeal);
				$smarty->assign('add_mode',2);
			}
			elseif ($_CFG['operation_mode']=="1")
			{
				$smarty->assign('points_total',get_user_points($_SESSION['uid']));
				$smarty->assign('points',get_cache('points_rule'));
				$smarty->assign('add_mode',1);
			}
			$captcha=get_cache('captcha');
			$smarty->assign('verify_addjob',$captcha['verify_addjob']);
			$smarty->display('member_company/company_addjobs.htm');
		}
		else
		{
		$link[0]['text'] = "������ҵ����";
		$link[0]['href'] = 'company_info.php?act=company_profile';
		showmsg("Ϊ�˴ﵽ���õ���ƸЧ������������������ҵ���ϣ�",1,$link);
		}
}
elseif ($act=='addjobs_save')
{
	$captcha=get_cache('captcha');
	$postcaptcha = trim($_POST['postcaptcha']);
	if($captcha['verify_addjob']=='1' && empty($postcaptcha))
	{
		showmsg("����д��֤��",1);
 	}
	if ($captcha['verify_addjob']=='1' && strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
	{
		showmsg("��֤�����",1);
	}
	$add_mode=trim($_POST['add_mode']);
	$days=intval($_POST['days']);
	if ($days<$_CFG['company_add_days_min'])
	{
	showmsg("��Чʱ������Ϊ ".$_CFG['company_add_days_min']." �죡",1);
	}
	if ($add_mode=='1')
	{
					$points_rule=get_cache('points_rule');
					$user_points=get_user_points($_SESSION['uid']);
					$total=0;
					if ($points_rule['jobs_add']['type']=="2" && $points_rule['jobs_add']['value']>0)
					{
					$total=$points_rule['jobs_add']['value'];
					}
					if ($points_rule['jobs_daily']['type']=="2" && $points_rule['jobs_daily']['value']>0)
					{
					$total=$total+($days*$points_rule['jobs_daily']['value']);
					}
					if ($total>$user_points)
					{
					$link[0]['text'] = "������ֵ";
					$link[0]['href'] = 'company_service.php?act=order_add';
					$link[1]['text'] = "��Ա������ҳ";
					$link[1]['href'] = 'company_index.php?act=';
					showmsg("���".$_CFG['points_byname']."���㣬���ֵ���ٷ�����",0,$link);
					}
					$setsqlarr['setmeal_deadline']=0;
	}
	elseif ($add_mode=='2')
	{
					$link[0]['text'] = "������ͨ����";
					$link[0]['href'] = 'company_service.php?act=setmeal_list';
					$link[1]['text'] = "��Ա������ҳ";
					$link[1]['href'] = 'company_index.php?act=';
				$setmeal=get_user_setmeal($_SESSION['uid']);
				if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
				{					
					showmsg("���ķ����Ѿ����ڣ������¿�ͨ",1,$link);
				}
				if ($setmeal['jobs_ordinary']<=0)
				{
					showmsg("��ǰ������ְλ�Ѿ�������������ƣ������������ײͣ�",1,$link);
				}
				$setsqlarr['setmeal_deadline']=$setmeal['endtime'];
				$setsqlarr['setmeal_id']=$setmeal['setmeal_id'];
				$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
	}
	
	$addrand=intval($_POST['addrand']);
	if($_SESSION['addrand']==$addrand){
	unset($_SESSION['addrand']);
	$setsqlarr['add_mode']=intval($add_mode);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['companyname']=$company_profile['companyname'];
	$setsqlarr['company_id']=$company_profile['id'];
	$setsqlarr['company_addtime']=$company_profile['addtime'];
	$setsqlarr['company_audit']=$company_profile['audit'];
	$setsqlarr['jobs_name']=!empty($_POST['jobs_name'])?trim($_POST['jobs_name']):showmsg('��û����дְλ���ƣ�',1);
	check_word($_CFG['filter'],$_POST['jobs_name'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['nature']=intval($_POST['nature']);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['topclass']=intval($_POST['topclass']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):showmsg('��ѡ��ְλ���',1);
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):showmsg('��ѡ����������',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['wage']=intval($_POST['wage'])?intval($_POST['wage']):showmsg('��ѡ��н�ʴ�����',1);		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['negotiable']=intval($_POST['negotiable']);
	$setsqlarr['tag']=trim($_POST['tag']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['education']=intval($_POST['education'])?intval($_POST['education']):showmsg('��ѡ��ѧ��Ҫ��',1);		
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):showmsg('��ѡ�������飡',1);		
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['graduate']=intval($_POST['graduate']);
	$setsqlarr['age']=trim($_POST['minage'])."-".trim($_POST['maxage']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('��û����дְλ������',1);
	check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['trade']=$company_profile['trade'];
	$setsqlarr['trade_cn']=$company_profile['trade_cn'];
	$setsqlarr['scale']=$company_profile['scale'];
	$setsqlarr['scale_cn']=$company_profile['scale_cn'];
	$setsqlarr['street']=$company_profile['street'];
	$setsqlarr['street_cn']=$company_profile['street_cn'];
	$setsqlarr['addtime']=$timestamp;
	$setsqlarr['deadline']=strtotime("".intval($_POST['days'])." day");
	$setsqlarr['refreshtime']=$timestamp;
	$setsqlarr['key']=$setsqlarr['jobs_name'].$company_profile['companyname'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$company_profile['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$setsqlarr['tpl']=$company_profile['tpl'];
	$setsqlarr['map_x']=$company_profile['map_x'];
	$setsqlarr['map_y']=$company_profile['map_y'];
	if ($company_profile['audit']=="1")
	{
	$setsqlarr['audit']=intval($_CFG['audit_verifycom_addjob']);
	}
	else
	{
	$setsqlarr['audit']=intval($_CFG['audit_unexaminedcom_addjob']);
	}
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):showmsg('��û����д��ϵ�ˣ�',1);
	check_word($_CFG['filter'],$_POST['contact'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):showmsg('��û����д��ϵ�绰��',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['address']=!empty($_POST['address'])?trim($_POST['address']):showmsg('��û����д��ϵ��ַ��',1);
	check_word($_CFG['filter'],$_POST['address'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['email']=!empty($_POST['email'])?trim($_POST['email']):showmsg('��û����д��ϵ���䣡',1);
	check_word($_CFG['filter'],$_POST['email'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['notify']=intval($_POST['notify']);
	
	$setsqlarr_contact['contact_show']=intval($_POST['contact_show']);
	$setsqlarr_contact['email_show']=intval($_POST['email_show']);
	$setsqlarr_contact['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr_contact['address_show']=intval($_POST['address_show']);
	
	//���ְλ��Ϣ
	$pid=inserttable(table('jobs'),$setsqlarr,true);
	if(empty($pid)){
		showmsg("���ʧ�ܣ�",0);
	}
	//�����ϵ��ʽ
	$setsqlarr_contact['pid']=$pid;
	!inserttable(table('jobs_contact'),$setsqlarr_contact)?showmsg("���ʧ�ܣ�",0):'';
	if ($add_mode=='1')
	{
		if ($points_rule['jobs_add']['value']>0)
		{
		report_deal($_SESSION['uid'],$points_rule['jobs_add']['type'],$points_rule['jobs_add']['value']);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['jobs_add']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"������ְλ��<strong>{$setsqlarr['jobs_name']}</strong>��({$operator}{$points_rule['jobs_add']['value']})��(ʣ��:{$user_points})",1,1001,"����ְλ","{$operator}{$points_rule['jobs_add']['value']}","{$user_points}");
		}
		if (intval($_POST['days'])>0 && $points_rule['jobs_daily']['value']>0)
		{
		$points_day=intval($_POST['days'])*$points_rule['jobs_daily']['value'];
		report_deal($_SESSION['uid'],$points_rule['jobs_daily']['type'],$points_day);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['jobs_daily']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"����ְλ:<strong>{$_POST['jobs_name']}</strong>����Ч��Ϊ{$_POST['days']}�죬({$operator}{$points_day})��(ʣ��:{$user_points})",1,1001,"����ְλ","{$operator}{$points_day}","{$user_points}");
		}
	}
	elseif ($add_mode=='2')
	{
		action_user_setmeal($_SESSION['uid'],"jobs_ordinary");
		$setmeal=get_user_setmeal($_SESSION['uid']);
		write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"������ְͨλ:<strong>{$_POST['jobs_name']}</strong>�������Է�����ְͨλ:<strong>{$setmeal['jobs_ordinary']}</strong>��",2,1001,"����ְλ","1","{$setmeal['jobs_ordinary']}");
	}
	$searchtab['id']=$pid;
	$searchtab['uid']=$setsqlarr['uid'];
	$searchtab['subsite_id']=$setsqlarr['subsite_id'];
	$searchtab['recommend']=$setsqlarr['recommend'];
	$searchtab['emergency']=$setsqlarr['emergency'];
	$searchtab['nature']=$setsqlarr['nature'];
	$searchtab['sex']=$setsqlarr['sex'];
	$searchtab['topclass']=$setsqlarr['topclass'];
	$searchtab['category']=$setsqlarr['category'];
	$searchtab['subclass']=$setsqlarr['subclass'];
	$searchtab['trade']=$setsqlarr['trade'];
	$searchtab['district']=$setsqlarr['district'];
	$searchtab['sdistrict']=$setsqlarr['sdistrict'];	
	$searchtab['street']=$company_profile['street'];
	$searchtab['education']=$setsqlarr['education'];
	$searchtab['experience']=$setsqlarr['experience'];
	$searchtab['wage']=$setsqlarr['wage'];
	$searchtab['refreshtime']=$setsqlarr['refreshtime'];
	$searchtab['scale']=$setsqlarr['scale'];	
	//
	inserttable(table('jobs_search_wage'),$searchtab);
	inserttable(table('jobs_search_scale'),$searchtab);
	//
	$searchtab['map_x']=$setsqlarr['map_x'];
	$searchtab['map_y']=$setsqlarr['map_y'];
	inserttable(table('jobs_search_rtime'),$searchtab);
	unset($searchtab['map_x'],$searchtab['map_y']);
	//
	$searchtab['stick']=$setsqlarr['stick'];
	inserttable(table('jobs_search_stickrtime'),$searchtab);
	unset($searchtab['stick']);
	//
	$searchtab['click']=$setsqlarr['click'];
	inserttable(table('jobs_search_hot'),$searchtab);
	unset($searchtab['click']);
	//
	$searchtab['key']=$setsqlarr['key'];
	$searchtab['likekey']=$setsqlarr['jobs_name'].','.$setsqlarr['companyname'];
	$searchtab['map_x']=$setsqlarr['map_x'];
	$searchtab['map_y']=$setsqlarr['map_y'];
	inserttable(table('jobs_search_key'),$searchtab);
	unset($searchtab);
	//
		$tag=explode('|',$setsqlarr['tag']);
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
		$tagsql['id']=$pid;
		$tagsql['uid']=$setsqlarr['uid'];
		$tagsql['subsite_id']=$setsqlarr['subsite_id'];
		$tagsql['topclass']=$setsqlarr['topclass'];
		$tagsql['category']=$setsqlarr['category'];
		$tagsql['subclass']=$setsqlarr['subclass'];
		$tagsql['district']=$setsqlarr['district'];
		$tagsql['sdistrict']=$setsqlarr['sdistrict'];	
		inserttable(table('jobs_search_tag'),$tagsql);
	distribution_jobs($pid,$_SESSION['uid']);
	write_memberslog($_SESSION['uid'],1,2001,$_SESSION['username'],"������ְλ��{$setsqlarr['jobs_name']}");
	}
	header("location:?act=addjobs_save_succeed&jobsid=".$pid);
}
elseif($act=='addjobs_save_succeed'){
	$uid = intval($_SESSION['uid']);
	$jobs=get_jobs_one(intval($_GET['jobsid']),$uid);
	$jobs['jobs_url'] = url_rewrite("QS_jobsshow",array('id'=>$jobs['id']),true,$jobs['subsite_id']);
	$smarty->assign('jobs',$jobs);
	$smarty->assign('concern_id',get_concern_id($uid));
	$smarty->assign('title','����ְλ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_company/company_addjobs_succeed.htm');
}
elseif ($act=='del_jobs_templates')
{
	$yid =!empty($_POST['y_id'])?$_POST['y_id']:$_GET['y_id'];
	if (empty($yid))
	{
	showmsg("��û��ѡ��ģ�壡",1);
	}
	if($n=del_templates($yid,$_SESSION['uid']))
	{
		showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
	showmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif ($act=='jobs_perform')
{
	global $_CFG;
	$yid =!empty($_POST['y_id'])?$_POST['y_id']:$_GET['y_id'];
    	$jobs_num=count($yid);
	if (empty($yid))
	{
	showmsg("��û��ѡ��ְλ��",1);
	}
	
	$refresh=!empty($_POST['refresh'])?$_POST['refresh']:$_GET['refresh'];
	$delete=!empty($_POST['delete'])?$_POST['delete']:$_GET['delete'];
    	if ($refresh)
	{
		if($jobs_num==1){
			if(is_array($yid)){
				$yid = $yid[0];
			}
			$jobs_info = $db->getone("select * from ".table('jobs')." where id=".$yid);
			if(empty($jobs_info)){
				$jobs_info = $db->getone("select * from ".table('jobs_tmp')." where id=".$yid);
			}
			if($jobs_info['deadline']<time()){
				showmsg("��ְλ�ѵ��ڣ��������ڣ�",1);
			}
		}
		//����ģʽ
		if($_CFG['operation_mode']=='1'){
			//����ˢ��ʱ��
			//�һ�ε�ˢ��ʱ��
			$refrestime=get_last_refresh_date($_SESSION['uid'],"1001");
			$duringtime=time()-$refrestime['max(addtime)'];
			$space = $_CFG['com_pointsmode_refresh_space']*60;
			$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001");
			if($_CFG['com_pointsmode_refresh_time']!=0&&($refresh_time['count(*)']>=$_CFG['com_pointsmode_refresh_time']))
			{
			showmsg("ÿ�����ֻ��ˢ��".$_CFG['com_pointsmode_refresh_time']."��,�������ѳ������ˢ�´������ƣ�",2);	
			}
			elseif($duringtime<=$space){
			showmsg($_CFG['com_pointsmode_refresh_space']."�����ڲ����ظ�ˢ��ְλ��",2);
			}
			else 
			{
				$points_rule=get_cache('points_rule');
				if($points_rule['jobs_refresh']['value']>0)
				{
					$user_points=get_user_points($_SESSION['uid']);
					$total_point=$jobs_num*$points_rule['jobs_refresh']['value'];
					if ($total_point>$user_points && $points_rule['jobs_refresh']['type']=="2")
					{
							$link[0]['text'] = "������һҳ";
							$link[0]['href'] = 'javascript:history.go(-1)';
							$link[1]['text'] = "������ֵ";
							$link[1]['href'] = 'company_service.php?act=order_add';
					showmsg("����".$_CFG['points_byname']."���㣬���ȳ�ֵ��",0,$link);
					}
					report_deal($_SESSION['uid'],$points_rule['jobs_refresh']['type'],$total_point);
					$user_points=get_user_points($_SESSION['uid']);
					$operator=$points_rule['jobs_refresh']['type']=="1"?"+":"-";
					write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"ˢ����{$jobs_num}��ְλ��({$operator}{$total_point})��(ʣ��:{$user_points})",1,1003,"ˢ��ְλ","{$operator}{$total_point}","{$user_points}");
				}
			}
		}	
		//�ײ�ģʽ
		elseif($_CFG['operation_mode']=='2') 
		{
			//����ˢ��ʱ��
			//�һ�ε�ˢ��ʱ��
			$link[0]['text'] = "������ͨ����";
			$link[0]['href'] = 'company_service.php?act=setmeal_list';
			$link[1]['text'] = "��Ա������ҳ";
			$link[1]['href'] = 'company_index.php?act=';
			$setmeal=get_user_setmeal($_SESSION['uid']);
			if (empty($setmeal))
			{					
				showmsg("����û�п�ͨ�����뿪ͨ",1,$link);
			}
			elseif ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
			{					
				showmsg("���ķ����Ѿ����ڣ������¿�ͨ",1,$link);
			}
			else
			{
				$refrestime=get_last_refresh_date($_SESSION['uid'],"1001");
				$duringtime=time()-$refrestime['max(addtime)'];
				$space = $setmeal['refresh_jobs_space']*60;
				$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001");
				if($setmeal['refresh_jobs_time']!=0&&($refresh_time['count(*)']>=$setmeal['refresh_jobs_time']))
				{
				showmsg("ÿ�����ֻ��ˢ��".$setmeal['refresh_jobs_time']."��,�������ѳ������ˢ�´������ƣ�",2);
				}
				elseif($duringtime<=$space){
				showmsg($setmeal['refresh_jobs_space']."�����ڲ����ظ�ˢ��ְλ��",2);	
				}
			}
		}
		//���ģʽ
		elseif($_CFG['operation_mode']=='3') 
		{
			//����ˢ��ʱ��
			//�һ�ε�ˢ��ʱ��
			$setmeal=get_user_setmeal($_SESSION['uid']);
			
			$refrestime=get_last_refresh_date($_SESSION['uid'],"1001");
			$duringtime=time()-$refrestime['max(addtime)'];
			$space = $setmeal['refresh_jobs_space']*60;
			$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001");
			if($setmeal['refresh_jobs_time']!=0&&($refresh_time['count(*)']>=$setmeal['refresh_jobs_time']))
			{
			showmsg("ÿ�����ֻ��ˢ��".$setmeal['refresh_jobs_time']."��,�������ѳ������ˢ�´������ƣ�",2);
			}
			elseif($duringtime<=$space){
			showmsg($setmeal['refresh_jobs_space']."�����ڲ����ظ�ˢ��ְλ��",2);	
			}
			else
			{
				$points_rule=get_cache('points_rule');
				if($points_rule['jobs_refresh']['value']>0)
				{
					$user_points=get_user_points($_SESSION['uid']);
					$total_point=$jobs_num*$points_rule['jobs_refresh']['value'];
					if ($total_point>$user_points && $points_rule['jobs_refresh']['type']=="2")
					{
							$link[0]['text'] = "������һҳ";
							$link[0]['href'] = 'javascript:history.go(-1)';
							$link[1]['text'] = "������ֵ";
							$link[1]['href'] = 'company_service.php?act=order_add';
					showmsg("����".$_CFG['points_byname']."���㣬���ȳ�ֵ��",0,$link);
					}
					report_deal($_SESSION['uid'],$points_rule['jobs_refresh']['type'],$total_point);
					$user_points=get_user_points($_SESSION['uid']);
					$operator=$points_rule['jobs_refresh']['type']=="1"?"+":"-";
					write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"ˢ����{$jobs_num}��ְλ��({$operator}{$total_point})��(ʣ��:{$user_points})",1,1003,"ˢ��ְλ","{$operator}{$total_point}","{$user_points}");
				}
			}
		}
		
		refresh_jobs($yid,$_SESSION['uid']);
		write_memberslog($_SESSION['uid'],1,2004,$_SESSION['username'],"ˢ��ְλ");	
		write_refresh_log($_SESSION['uid'],1001);			
		showmsg("ˢ��ְλ�ɹ���",2);
	}
	elseif ($delete)
	{
		if($n=del_jobs($yid,$_SESSION['uid']))
		{
			showmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
		}
		else
		{
			showmsg("ɾ��ʧ�ܣ�",2);
		}
	}
	elseif (!empty($_REQUEST['display1']))
	{
	activate_jobs($yid,1,$_SESSION['uid']);
	showmsg("���óɹ���",2);
	}
	elseif (!empty($_REQUEST['display2']))
	{
	activate_jobs($yid,2,$_SESSION['uid']);
	showmsg("���óɹ���",2);
	}
}
elseif ($act=='editjobs')
{
	$jobs=get_jobs_one(intval($_GET['id']),$_SESSION['uid']);
	if (empty($jobs)) showmsg("��������",1);
	if($jobs['age']){
		$jobs_age = explode("-", $jobs['age']);
		$jobs['minage'] = $jobs_age[0];
		$jobs['maxage'] = $jobs_age[1];
	}
	$smarty->assign('user',$user);
	$smarty->assign('title','�޸�ְλ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('points_total',get_user_points($_SESSION['uid']));
	$smarty->assign('points',get_cache('points_rule'));
	$smarty->assign('jobs',$jobs);
	$smarty->display('member_company/company_editjobs.htm');
}
elseif ($act=='editjobs_save')
{
	$id=intval($_POST['id']);
	$add_mode=trim($_POST['add_mode']);
	$days=intval($_POST['days']);
	if ($add_mode=='1')
	{
					$points_rule=get_cache('points_rule');
					$user_points=get_user_points($_SESSION['uid']);
					$total=0;
					if($points_rule['jobs_edit']['type']=="2" && $points_rule['jobs_edit']['value']>0)
					{
					$total=$points_rule['jobs_edit']['value'];
					}
					if($points_rule['jobs_daily']['type']=="2" && $points_rule['jobs_daily']['value']>0)
					{
					$total=$total+($days*$points_rule['jobs_daily']['value']);
					}
					if ($total>$user_points)
					{
					$link[0]['text'] = "������һҳ";
					$link[0]['href'] = 'javascript:history.go(-1)';
					$link[1]['text'] = "������ֵ";
					$link[1]['href'] = 'company_service.php?act=order_add';
					showmsg("���".$_CFG['points_byname']."���㣬���ֵ���ٷ�����",0,$link);
					}
	}
	elseif ($add_mode=='2')
	{
					$link[0]['text'] = "������ͨ����";
					$link[0]['href'] = 'company_service.php?act=setmeal_list';
					$link[1]['text'] = "��Ա������ҳ";
					$link[1]['href'] = 'company_index.php?act=';
				$setmeal=get_user_setmeal($_SESSION['uid']);
				if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
				{					
					showmsg("�����ײ��Ѿ����ڣ������¿�ͨ",1,$link);
				}
	}

	$setsqlarr['jobs_name']=!empty($_POST['jobs_name'])?trim($_POST['jobs_name']):showmsg('��û����дְλ���ƣ�',1);
	check_word($_CFG['filter'],$_POST['jobs_name'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['nature']=intval($_POST['nature']);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['topclass']=trim($_POST['topclass']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):showmsg('��ѡ��ְλ���',1);
	$setsqlarr['subclass']=trim($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):showmsg('��ѡ����������',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['wage']=intval($_POST['wage'])?intval($_POST['wage']):showmsg('��ѡ��н�ʴ�����',1);		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['negotiable']=intval($_POST['negotiable']);
	$setsqlarr['tag']=trim($_POST['tag']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['education']=intval($_POST['education'])?intval($_POST['education']):showmsg('��ѡ��ѧ��Ҫ��',1);		
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience'])?intval($_POST['experience']):showmsg('��ѡ�������飡',1);		
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['graduate']=intval($_POST['graduate']);
	$setsqlarr['age']=trim($_POST['minage'])."-".trim($_POST['maxage']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('��û����дְλ������',1);
	check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
	if ($add_mode=='1'){
		$setsqlarr['setmeal_deadline']=0;
		$setsqlarr['add_mode']=1;
	}elseif($add_mode=='2'){
		$setmeal=get_user_setmeal($_SESSION['uid']);
		$setsqlarr['setmeal_deadline']=$setmeal['endtime'];
		$setsqlarr['setmeal_id']=$setmeal['setmeal_id'];
		$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
		$setsqlarr['add_mode']=2;
	}
	if ($days>0)
	{
		if (intval($_POST['olddeadline'])>=time())
		{
			 $setsqlarr['deadline']=intval($_POST['olddeadline'])+($days*(60*60*24));
		}
		else
		{
			 $setsqlarr['deadline']=strtotime("{$days} day");
		}
	}
	$setsqlarr['key']=$setsqlarr['jobs_name'].$company_profile['companyname'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$company_profile['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	if ($company_profile['audit']=="1")
	{
	$_CFG['audit_verifycom_editjob']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_verifycom_editjob']):'';
	}
	else
	{
	$_CFG['audit_unexaminedcom_editjob']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_unexaminedcom_editjob']):'';
	}
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):showmsg('��û����д��ϵ�ˣ�',1);
	check_word($_CFG['filter'],$_POST['contact'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):showmsg('��û����д��ϵ�绰��',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['address']=!empty($_POST['address'])?trim($_POST['address']):showmsg('��û����д��ϵ��ַ��',1);
	check_word($_CFG['filter'],$_POST['address'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['email']=!empty($_POST['email'])?trim($_POST['email']):showmsg('��û����д��ϵ���䣡',1);
	check_word($_CFG['filter'],$_POST['email'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['notify']=trim($_POST['notify']);
	
  	$setsqlarr_contact['contact_show']=intval($_POST['contact_show']);
	$setsqlarr_contact['email_show']=intval($_POST['email_show']);
	$setsqlarr_contact['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr_contact['address_show']=intval($_POST['address_show']);
		
	if (!updatetable(table('jobs'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) showmsg("����ʧ�ܣ�",0);
	if (!updatetable(table('jobs_tmp'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) showmsg("����ʧ�ܣ�",0);
	if (!updatetable(table('jobs_contact'), $setsqlarr_contact," pid='{$id}' ")){
		showmsg("����ʧ�ܣ�",0);
	}
	if ($add_mode=='1')
	{
		if ($points_rule['jobs_edit']['value']>0)
		{
		report_deal($_SESSION['uid'],$points_rule['jobs_edit']['type'],$points_rule['jobs_edit']['value']);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['jobs_edit']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"�޸�ְλ��<strong>{$setsqlarr['jobs_name']}</strong>��({$operator}{$points_rule['jobs_edit']['value']})��(ʣ��:{$user_points})",1,1002,"�޸���Ƹ��Ϣ","{$operator}{$points_rule['jobs_edit']['value']}","{$user_points}");
		}
		if ($days>0 && $points_rule['jobs_daily']['value']>0)
		{
		$points_day=intval($_POST['days'])*$points_rule['jobs_daily']['value'];
		report_deal($_SESSION['uid'],$points_rule['jobs_daily']['type'],$points_day);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['jobs_daily']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"�ӳ�ְλ({$_POST['jobs_name']})��Ч��Ϊ{$_POST['days']}�죬({$operator}{$points_day})��(ʣ��:{$user_points})",1,1002,"�޸���Ƹ��Ϣ","{$operator}{$points_day}","{$user_points}");
		}
	}	 
	$link[0]['text'] = "ְλ�б�";
	$link[0]['href'] = '?act=jobs';
	$link[1]['text'] = "�鿴�޸Ľ��";
	$link[1]['href'] = "?act=editjobs&id={$id}";
	$link[2]['text'] = "��Ա������ҳ";
	$link[2]['href'] = "company_index.php";
	//
	$searchtab['nature']=$setsqlarr['nature'];
	$searchtab['sex']=$setsqlarr['sex'];
	$searchtab['topclass']=$setsqlarr['topclass'];
	$searchtab['category']=$setsqlarr['category'];
	$searchtab['subclass']=$setsqlarr['subclass'];
	$searchtab['district']=$setsqlarr['district'];
	$searchtab['sdistrict']=$setsqlarr['sdistrict'];
	$searchtab['education']=$setsqlarr['education'];
	$searchtab['experience']=$setsqlarr['experience'];
	$searchtab['wage']=$setsqlarr['wage'];
	//
	updatetable(table('jobs_search_wage'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	updatetable(table('jobs_search_rtime'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	updatetable(table('jobs_search_stickrtime'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	updatetable(table('jobs_search_hot'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	updatetable(table('jobs_search_scale'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}'");
	$searchtab['key']=$setsqlarr['key'];
	$searchtab['likekey']=$setsqlarr['jobs_name'].','.$company_profile['companyname'];
	updatetable(table('jobs_search_key'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	unset($searchtab);
		$tag=explode('|',$setsqlarr['tag']);
		$tagindex=1;
		foreach($tag as $v)
		{
		$vid=explode(',',$v);
		$tagsql['tag'.$tagindex]=intval($vid[0]);
		$tagindex++;
		}
		$tagsql['id']=$id;
		$tagsql['uid']=$_SESSION['uid'];
		$tagsql['topclass']=$setsqlarr['topclass'];
		$tagsql['category']=$setsqlarr['category'];
		$tagsql['subclass']=$setsqlarr['subclass'];
		$tagsql['district']=$setsqlarr['district'];
		$tagsql['sdistrict']=$setsqlarr['sdistrict'];
	updatetable(table('jobs_search_tag'),$tagsql," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	distribution_jobs($id,$_SESSION['uid']);
	write_memberslog($_SESSION['uid'],$_SESSION['utype'],2002,$_SESSION['username'],"�޸���ְλ��{$setsqlarr['jobs_name']}��ְλID��{$id}");
	showmsg("�޸ĳɹ���",2,$link);
}
elseif($act == "ajax_save_jobs_templates"){
	foreach ($_POST as $key => $value) {
		$_POST[$key] = utf8_to_gbk($value);
	}
	$setsqlarr['title']=!empty($_POST['jobs_name'])?trim($_POST['jobs_name'])."��ģ��":exit('-1');
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):exit('-1');
	$setsqlarr['nature']=intval($_POST['nature']);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['topclass']=intval($_POST['topclass']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):exit('-1');
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):exit('-1');
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['education']=intval($_POST['education']);		
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience']);		
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['wage']=intval($_POST['wage']);		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['tag']=trim($_POST['tag']);		
	$setsqlarr['graduate']=intval($_POST['graduate']);
	$setsqlarr['negotiable']=intval($_POST['negotiable']);
	$setsqlarr['minage']=intval($_POST['minage']);
	$setsqlarr['maxage']=intval($_POST['maxage']);
	$setsqlarr['addtime']=time();
	$pid=inserttable(table('jobs_templates'),$setsqlarr,true);
	if($pid>0){
		exit("1");
	}else{
		exit("0");
	}
}
elseif($act == 'copy_templates'){
	$id = intval($_GET['id']);
	if($id<1){
		exit("-1");
	}
	$templates = get_jobs_templates_one($id);
	if(!empty($templates)){
		foreach ($templates as $key => $value) {
			$templates[$key] = gbk_to_utf8($value);
		}
		exit(json_encode($templates));
	}else{
		exit("-1");
	}
}
elseif($act == "get_content_by_jobs_cat"){
	$id = intval($_GET['id']);
	if($id>0){
		$content = get_content_by_jobs_cat($id);
		if(!empty($content)){
			exit($content);
		}else{
			exit("-1");
		}
	}else{
		exit("-1");
	}
}
elseif ($act=='add_templates')
{
	$_SESSION['addrand']=rand(1000,5000);
	$smarty->assign('addrand',$_SESSION['addrand']);
	$smarty->assign('title','����ְλģ�� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_company/company_add_templates.htm');	
}
elseif($act == "add_templates_save"){
	$addrand=intval($_POST['addrand']);
	if($_SESSION['addrand']==$addrand){
	unset($_SESSION['addrand']);
	$setsqlarr['title']=!empty($_POST['title'])?trim($_POST['title']):showmsg('����дģ�����ƣ�',1);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('����дְλ������',1);
	$setsqlarr['nature']=intval($_POST['nature']);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['topclass']=intval($_POST['topclass']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):showmsg('��ѡ��ְλ���',1);
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):showmsg('��ѡ����������',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['education']=intval($_POST['education']);		
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience']);		
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['wage']=intval($_POST['wage']);		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['tag']=trim($_POST['tag']);		
	$setsqlarr['graduate']=intval($_POST['graduate']);
	$setsqlarr['negotiable']=intval($_POST['negotiable']);
	$setsqlarr['minage']=intval($_POST['minage']);
	$setsqlarr['maxage']=intval($_POST['maxage']);
	$setsqlarr['addtime']=time();
	$pid=inserttable(table('jobs_templates'),$setsqlarr,true);
	$link[0]['text'] = "ְλģ���б�";
	$link[0]['href'] = 'company_jobs.php?act=jobs_templates';
	$link[1]['text'] = "��������ְλģ��";
	$link[1]['href'] = 'company_jobs.php?act=add_templates';
	empty($pid)?showmsg("���ʧ�ܣ�",0):showmsg("��ӳɹ���",2,$link);
	}
}
elseif ($act=='edit_templates')
{
	$id = intval($_GET['id']);
	if($id<1){
		showmsg("��ѡ��ְλģ�壡",1);
	}
	$templates = get_jobs_templates_one($id);
	$_SESSION['addrand']=rand(1000,5000);
	$smarty->assign('addrand',$_SESSION['addrand']);
	$smarty->assign('templates',$templates);
	$smarty->assign('title','�޸�ְλģ�� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_company/company_edit_templates.htm');	
}
elseif($act == "edit_templates_save"){
	$id = intval($_POST['id']);
	if($id<1){
		showmsg("��ѡ��ְλģ�壡",1);
	}
	$addrand=intval($_POST['addrand']);
	if($_SESSION['addrand']==$addrand){
	unset($_SESSION['addrand']);
	$setsqlarr['title']=!empty($_POST['title'])?trim($_POST['title']):showmsg('����дģ�����ƣ�',1);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('����дְλ������',1);
	$setsqlarr['nature']=intval($_POST['nature']);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['topclass']=intval($_POST['topclass']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):showmsg('��ѡ��ְλ���',1);
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):showmsg('��ѡ����������',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['education']=intval($_POST['education']);		
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience']);		
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['wage']=intval($_POST['wage']);		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['tag']=trim($_POST['tag']);	
	$setsqlarr['graduate']=intval($_POST['graduate']);
	$setsqlarr['negotiable']=intval($_POST['negotiable']);
	$setsqlarr['minage']=intval($_POST['minage']);
	$setsqlarr['maxage']=intval($_POST['maxage']);
	$setsqlarr['addtime']=time();
	if (!updatetable(table('jobs_templates'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) showmsg("����ʧ�ܣ�",0);
	$link[0]['text'] = "ְλģ���б�";
	$link[0]['href'] = 'company_jobs.php?act=jobs_templates';
	$link[1]['text'] = "�鿴�޸Ľ��";
	$link[1]['href'] = 'company_jobs.php?act=edit_templates&id='.$id;
	showmsg("�޸ĳɹ���",2,$link);
	}
}
 unset($smarty);
?>