<?php
/*
 * 74cms ��ҵ��Ա����ajax������
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/company_common.php');
if($act=="company_profile_save_succeed"){
	$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_companyprofile_save_succeed_box.htm";
	$contents=file_get_contents($tpl);
	exit($contents);
}
elseif($act=="user_email"){
	$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_authenticate_email_box.htm";
	$contents=file_get_contents($tpl);
	$_SESSION['send_email_key']=mt_rand(100000, 999999);
	$contents=str_replace('{#$email#}',$user["email"],$contents);
	$contents=str_replace('{#$send_email_key#}',$_SESSION['send_email_key'],$contents);
	$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
	exit($contents);
}	
elseif($act=="user_mobile"){
	$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_authenticate_mobile_box.htm";
	$contents=file_get_contents($tpl);
	$_SESSION['send_mobile_key']=mt_rand(100000, 999999);
	$contents=str_replace('{#$mobile#}',$user["mobile"],$contents);
	$contents=str_replace('{#$send_mobile_key#}',$_SESSION['send_mobile_key'],$contents);
	$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
	exit($contents);
}
elseif($act=="old_mobile"){
	$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_authenticate_old_mobile_box.htm";
	$contents=file_get_contents($tpl);
	$_SESSION['send_mobile_key']=mt_rand(100000, 999999);
	$user["hid_mobile"] = substr($user["mobile"],0,3)."*****".substr($user["mobile"],7,4);
	$contents=str_replace('{#$mobile#}',$user["mobile"],$contents);
	$contents=str_replace('{#$hid_mobile#}',$user["hid_mobile"],$contents);
	$contents=str_replace('{#$send_mobile_key#}',$_SESSION['send_mobile_key'],$contents);
	$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
	exit($contents);
}
elseif($act=="edit_mobile"){
	$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_authenticate_edit_mobile_box.htm";
	$contents=file_get_contents($tpl);
	$_SESSION['send_mobile_key']=mt_rand(100000, 999999);
	$contents=str_replace('{#$send_mobile_key#}',$_SESSION['send_mobile_key'],$contents);
	$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
	exit($contents);
}
elseif($act=="set_promotion"){
	$catid = intval($_GET['catid'])?intval($_GET['catid']):exit("��������");
	$jobid = intval($_GET['jobid'])?intval($_GET['jobid']):exit("��������");
	$uid = intval($_SESSION['uid'])?intval($_SESSION['uid']):exit("��������");
	$jobinfo = get_jobs_one($jobid);
	$promotion = get_promotion_category_one($catid);


	if ($_CFG['operation_mode']=='2')
	{
		$setmeal=get_user_setmeal($uid);//��ȡ��Ա�ײ�
		if($setmeal['endtime']<time() && $setmeal['endtime']<>'0'){
			$end=1;//�ж��ײ��Ƿ���
			$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_promotion_end.htm";
		}else{
			$data=get_setmeal_promotion($uid,$catid);//��ȡ��Աĳ���ƹ��ʣ�����������������ƣ�������
			$operation_mode=2;
			$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_setmeal_promotion.htm";
		}
	}
	elseif($_CFG['operation_mode']=='1')
	{
		$points = get_user_points($uid);
		$operation_mode=1;
		$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_points_promotion.htm";
	}
	elseif($_CFG['operation_mode']=='3')
	{
		$setmeal=get_user_setmeal($_SESSION['uid']);//��ȡ��Ա�ײ�
		if($setmeal['endtime']<time() && $setmeal['endtime']<>'0'){
			if($_CFG['setmeal_to_points']!=1){
				$end=1;//�ж��ײ��Ƿ���
				$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_promotion_end.htm";
			}else{
				$operation_mode=1;
				$points = get_user_points($uid);
				$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_points_promotion.htm";
			}
		}else{
			$data=get_setmeal_promotion($uid,$catid);//��ȡ��Աĳ���ƹ��ʣ�����������������ƣ�������
			if($data['num']<1){
				if($_CFG['setmeal_to_points']==1){
					$operation_mode=1;
					$points = get_user_points($uid);
					$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_points_promotion.htm";
				}else{
					$operation_mode=2;
					$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_setmeal_promotion.htm";
				}
			}else{
				$operation_mode=2;
				$tpl='../../templates/'.$_CFG['template_dir']."member_company/ajax_set_setmeal_promotion.htm";
			}
		}
	}
	$contents=file_get_contents($tpl);
	if($end!=1){
		if($catid=="4"){
			$color = get_color();
			$color_list = '<tr>
				<td height="50">ѡ����ɫ��</td>
				<td>
					<div style="position:relateve;">
	             	 	<div id="val_menu" class="input_text_70_bg">��ѡ��</div>	
	             	 	<div class="menu" id="menu1">
		              		<ul style="width:88px;">';
			foreach ($color as $key => $value) {
				$color_list.='<li id="'.$value["id"].'" title="'.$value["value"].'" style="background-color:'.$value["value"].'">&nbsp;</li>';
			}
			$color_list.='</ul>
		              	</div>
		            </div>
		            <input type="hidden" name="val" value="" id="val">
				</td>
				<td></td>
			</tr>';
			$contents=str_replace('{#$color_list#}',$color_list,$contents);
		}else{
			$contents=str_replace('{#$color_list#}',"",$contents);
		}
		$contents=str_replace('{#$jobid#}',$jobid,$contents);
		$contents=str_replace('{#$catid#}',$catid,$contents);
		$contents=str_replace('{#$jobs_name#}',$jobinfo['jobs_name'],$contents);
		$contents=str_replace('{#$promotion_name#}',$promotion['cat_name'],$contents);
		$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
		if($operation_mode==1){
			if($promotion['cat_minday']=="0"){
				$promotion['cat_minday'] = "������";
			}
			if($promotion['cat_maxday']=="0"){
				$promotion['cat_maxday'] = "������";
			}
			if($promotion['cat_points']=="0"){
				$promotion['cat_points'] = "���";
			}
			$contents=str_replace('{#$user_points#}',$points,$contents);
			$contents=str_replace('{#$points_perday#}',$promotion['cat_points'],$contents);
			$contents=str_replace('{#$points_quantifier#}',$_CFG['points_quantifier'],$contents);
			$contents=str_replace('{#$points_byname#}',$_CFG['points_byname'],$contents);
			$contents=str_replace('{#$cat_minday#}',$promotion['cat_minday'],$contents);
			$contents=str_replace('{#$cat_maxday#}',$promotion['cat_maxday'],$contents);
		}elseif($operation_mode==2){
			$contents=str_replace('{#$days#}',$data['days'],$contents);
			$contents=str_replace('{#$setmeal_name#}',$setmeal['setmeal_name'],$contents);
			$contents=str_replace('{#$num#}',$data['num'],$contents);
			$contents=str_replace('{#$pro_name#}',$data['name'],$contents);
			$contents=str_replace('{#$cat_minday#}',$promotion['cat_minday'],$contents);
			$contents=str_replace('{#$cat_maxday#}',$promotion['cat_maxday'],$contents);
		}
	}
	exit($contents);
}
elseif($act=="promotion_add_save"){
	$catid = intval($_POST['catid'])?intval($_POST['catid']):exit("-1");
	$jobid = intval($_POST['jobid'])?intval($_POST['jobid']):exit("-1");
	$days = intval($_POST['days'])?intval($_POST['days']):exit("-1");
	$uid = intval($_SESSION['uid'])?intval($_SESSION['uid']):exit("-1");

	if($catid==4){
		$val = intval($_POST['val'])?intval($_POST['val']):exit("-1");
		$color = get_color_one($val);
		$val_code = $color['value'];
	}else{
		$val_code = "";
	}
	$jobs=get_jobs_one($jobid,$uid);
	if($jobs['deadline']<time()){
		exit("��ְλ�ѵ��ڣ��������ڣ�");
	}
	if ($jobid>0 && $days>0)
	{
		$pro_cat=get_promotion_category_one($catid);
		if($_CFG['operation_mode']=='3'){
			$setmeal=get_setmeal_promotion($uid,$catid);//��ȡ��Ա�ײ�
			$num=$setmeal['num'];
			if(($setmeal['endtime']<time() && $setmeal['endtime']<>'0') || $num<=0){
				if($_CFG['setmeal_to_points']==1){
					if ($pro_cat['cat_points']>0)
					{
						$points=$pro_cat['cat_points']*$days;
						$user_points=get_user_points($uid);
						if ($points>$user_points)
						{
							exit("���".$_CFG['points_byname']."�������д˴β��������ȳ�ֵ��");
						}else{
							$_CFG['operation_mode']=1;
						}
					}else{
						$_CFG['operation_mode']=2;
					}
				}else{
					exit("����ײ��ѵ��ڻ��ײ���ʣ��{$pro_cat['cat_name']}�������뾡�쿪ͨ���ײ�");
				}
			}else{
				$_CFG['operation_mode']=2;
			}
		}elseif($_CFG['operation_mode']=='1'){
			if ($pro_cat['cat_points']>0)
			{
				$points=$pro_cat['cat_points']*$days;
				$user_points=get_user_points($uid);
				if ($points>$user_points)
				{
				exit("���".$_CFG['points_byname']."�������д˴β��������ȳ�ֵ��");
				}
			}
		}elseif($_CFG['operation_mode']=='2'){
			$setmeal=get_setmeal_promotion($uid,$catid);//��ȡ��Ա�ײ�
			$num=$setmeal['num'];
			if(($setmeal['endtime']<time() && $setmeal['endtime']<>'0') || $num<=0){
				exit("����ײ��ѵ��ڻ��ײ���ʣ��{$pro_cat['cat_name']}�������뾡�쿪ͨ���ײ�");
			}
		}
		$info=get_promotion_one($jobid,$uid,$catid);
		if (!empty($info))
		{
		exit("��ְλ�����ƹ��У���ѡ������ְλ����������");
		}
		$setsqlarr['cp_available']=1;
		$setsqlarr['cp_promotionid']=$catid;
		$setsqlarr['cp_uid']=$uid;
		$setsqlarr['cp_jobid']=$jobid;
		$setsqlarr['cp_days']=$days;
		$setsqlarr['cp_starttime']=time();
		$setsqlarr['cp_endtime']=strtotime("{$days} day");
		$setsqlarr['cp_val']=$val_code;
		if ($setsqlarr['cp_promotionid']=="4" && empty($setsqlarr['cp_val']))
		{
		exit("��ѡ����ɫ��");
		}
			if (inserttable(table('promotion'),$setsqlarr))
			{
				set_job_promotion($jobid,$setsqlarr['cp_promotionid'],$val_code);
				$jobs=get_jobs_one($jobid,$uid);
				if ($_CFG['operation_mode']=='1' && $pro_cat['cat_points']>0)
				{
					report_deal($uid,2,$points);
					$user_points=get_user_points($uid);
					write_memberslog($uid,1,9001,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$days} �죬(-{$points})��(ʣ��:{$user_points})",1,1018,"{$pro_cat['cat_name']}","-{$points}","{$user_points}");
				}elseif($_CFG['operation_mode']=='2'){
					$user_pname=trim($_POST['pro_name']);
					action_user_setmeal($uid,$user_pname); //�����ײ�����Ӧ�ƹ㷽ʽ������
					$setmeal=get_user_setmeal($uid);//��ȡ��Ա�ײ�
					write_memberslog($uid,1,9002,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$days} �죬�ײ���ʣ��{$pro_cat['cat_name']}������{$setmeal[$user_pname]}����",2,1018,"{$pro_cat['cat_name']}","-{$days}","{$setmeal[$user_pname]}");//9002���ײͲ���
				}
				write_memberslog($uid,1,3004,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$days} �졣");
				if ($_POST['golist'])
				{
				exit("1");
				}
				else
				{
				exit("1");
				}
			}
	}
	else
	{
	exit("-1");
	}
}
unset($smarty);
?>