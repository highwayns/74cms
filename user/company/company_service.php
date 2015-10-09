<?php
/*
 * 74cms ��ҵ��Ա����
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ��������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã��������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"service");
if ($act=='account')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$i_type=trim($_GET['i_type']);
	$settr=intval($_GET['settr']);
	if($_CFG['operation_mode']=="1"){
		$wheresql=" WHERE log_uid='{$_SESSION['uid']}' AND log_type=9001 AND log_mode=1";
	}elseif($_CFG['operation_mode']=="2"){
		$wheresql=" WHERE log_uid='{$_SESSION['uid']}' AND log_type=9002 AND log_mode=2 ";
	}else{
		$wheresql1=" WHERE log_uid='{$_SESSION['uid']}' AND log_type=9001 AND log_mode=1";
		$wheresql2=" WHERE log_uid='{$_SESSION['uid']}' AND log_type=9002 AND log_mode=2 ";
	}
	
	if($_CFG['operation_mode']=="3"){
		if($settr>0)
		{
		$settr_val=strtotime("-".$settr." day");
		$wheresql1.=" AND log_addtime>".$settr_val;
		$wheresql2.=" AND log_addtime>".$settr_val;
		}
		$perpage=15;
		$total_sql1="SELECT COUNT(*) AS num FROM ".table('members_log').$wheresql1;
		$total_sql2="SELECT COUNT(*) AS num FROM ".table('members_log').$wheresql2;
		$total_val1=$db->get_total($total_sql1);
		$total_val2=$db->get_total($total_sql2);
		$page1 = new page(array('total'=>$total_val1, 'perpage'=>$perpage));
		$page2 = new page(array('total'=>$total_val2, 'perpage'=>$perpage));
		$offset1=($page1->nowindex-1)*$perpage;
		$offset2=($page2->nowindex-1)*$perpage;
		$smarty->assign('report',get_user_report($offset1, $perpage,$wheresql1));
		$smarty->assign('setmeal_report',get_user_report($offset2, $perpage,$wheresql2));
		$smarty->assign('page1',$page1->show(3));
		$smarty->assign('page2',$page2->show(3));
	}else{
		if($settr>0)
		{
		$settr_val=strtotime("-".$settr." day");
		$wheresql.=" AND log_addtime>".$settr_val;
		}
		$perpage=15;
		$total_sql="SELECT COUNT(*) AS num FROM ".table('members_log').$wheresql;
		$total_val=$db->get_total($total_sql);
		$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
		$offset=($page->nowindex-1)*$perpage;
		$smarty->assign('report',get_user_report($offset, $perpage,$wheresql));
		$smarty->assign('page',$page->show(3));
	}
	
	$setmeal = get_user_setmeal($_SESSION['uid']);
	if ($setmeal['endtime']>0){
		$setmeal_endtime=sub_day($setmeal['endtime'],time());
	}else{
		$setmeal_endtime="������";
	}
	
	$smarty->assign('title','�ҵ��˻� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->assign('setmeal',$setmeal);
	$smarty->assign('points_rule',get_points_rule());
	$smarty->assign('setmeal_rule',get_members_setmeal_rule($setmeal['setmeal_id']));
	$smarty->assign('setmeal_endtime',$setmeal_endtime);
	if($_CFG['operation_mode']=="1"){
		$smarty->display('member_company/company_my_account.htm');
	}elseif($_CFG['operation_mode']=="2"){
		$smarty->display('member_company/company_my_account_package.htm');
	}else{
		$smarty->display('member_company/company_my_account_complex.htm');
	}
	
}
elseif ($act=='order_list')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$is_paid=trim($_GET['is_paid']);
	$wheresql=" WHERE uid='".$_SESSION['uid']."' ";
	if($is_paid<>'' && is_numeric($is_paid))
	{
	$wheresql.=" AND is_paid='".intval($is_paid)."' ";
	}
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('order').$wheresql;
	$page = new page(array('total'=>$db->get_total($total_sql), 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('title','��ֵ��¼ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('is_paid',$is_paid);
	$smarty->assign('payment',get_order_all($offset, $perpage,$wheresql));
	if ($total_val>$perpage)
	{
	$smarty->assign('page',$page->show(3));
	}
	$smarty->display('member_company/company_order_list.htm');
}
elseif ($act=='order_add')
{
	$smarty->assign('title','���߳�ֵ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('payment',get_payment());
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->display('member_company/company_order_add.htm');
}
elseif ($act=='order_add_save')
{
		if (empty($company_profile['companyname']))
		{
		$link[0]['text'] = "��д��ҵ����";
		$link[0]['href'] = 'company_info.php?act=company_profile';
		showmsg("������д������ҵ���ϣ�",1,$link);
		}
	$myorder=get_user_order($_SESSION['uid'],1);
	$order_num=count($myorder);
	if ($order_num>=5)
	{
	$link[0]['text'] = "�����鿴";
	$link[0]['href'] = '?act=order_list&is_paid=1';
	showmsg("δ�����Ķ������ܳ��� 5 �������ȴ������ٴ����룡",1,$link,true,8);
	}
	$amount=(trim($_POST['amount'])).(intval($_POST['amount']))?trim($_POST['amount']):showmsg('����д��ֵ��',1);
	($amount<$_CFG['payment_min'])?showmsg("���ʳ�ֵ�������� ".$_CFG['payment_min']." Ԫ��",1):'';
	$payment_name=empty($_POST['payment_name'])?showmsg("��ѡ�񸶿ʽ��",1):$_POST['payment_name'];
	$paymenttpye=get_payment_info($payment_name);
	if (empty($paymenttpye)) showmsg("֧����ʽ����",0);
	$fee=number_format(($amount/100)*$paymenttpye['fee'],1,'.','');//������
	$order['oid']= strtoupper(substr($paymenttpye['typename'],0,1))."-".date('ymd',time())."-".date('His',time());//������
	$order['v_url']=$_CFG['main_domain']."include/payment/respond_".$paymenttpye['typename'].".php";
	$order['v_amount']=$amount+$fee; 
	$points=$amount*$_CFG['payment_rate'];
	$order_id=add_order($_SESSION['uid'],$order['oid'],$amount,$payment_name,"��ֵ����:".$points,$timestamp,$points,'',1);
		if ($order_id)
			{
			header("location:?act=payment&order_id=".$order_id);
			}
			else
			{
			showmsg("���Ӷ���ʧ�ܣ�",0);
			}
}
elseif ($act=='payment')
{
	$setmeal = get_user_setmeal($_SESSION['uid']);
	if ($setmeal['endtime']>0){
		$setmeal_endtime=sub_day($setmeal['endtime'],time());
	}else{
		$setmeal_endtime="������";
	}
	$smarty->assign('user_setmeal',$setmeal);
	$smarty->assign('setmeal_endtime',$setmeal_endtime);
	$smarty->assign('payment',get_payment());
	$order_id=intval($_GET['order_id']);
	$myorder=get_order_one($_SESSION['uid'],$order_id);
	$payment=get_payment_info($myorder['payment_name']);
	if (empty($payment)) showmsg("֧����ʽ����",0);
	$fee=number_format(($amount/100)*$payment['fee'],1,'.','');//������
	$order['oid']=$myorder['oid'];//������
	$order['v_url']=$_CFG['main_domain']."include/payment/respond_".$payment['typename'].".php";
	$order['v_amount']=$myorder['amount']+$fee;
	if ($myorder['payment_name']!='remittance')//�����Ƿ�����֧����
	{
		require_once(QISHI_ROOT_PATH."include/payment/".$payment['typename'].".php");
		$payment_form=get_code($order,$payment);
		if (empty($payment_form)) showmsg("����֧����������",0);
	}
	$smarty->assign('points',get_user_points($_SESSION['uid']));
	$smarty->assign('title','���� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('fee',$fee);
	$smarty->assign('amount',$myorder['amount']);
	$smarty->assign('oid',$order['oid']);
	$smarty->assign('byname',$payment);
	$smarty->assign('payment_form',$payment_form);
	$smarty->display('member_company/company_order_pay.htm');
}
elseif ($act=='order_del')
{
	$link[0]['text'] = "������һҳ";
	$link[0]['href'] = '?act=order_list';
	$id=intval($_GET['id']);
	del_order($_SESSION['uid'],$id)?showmsg('ȡ���ɹ���',2,$link):showmsg('ȡ��ʧ�ܣ�',1);
}
elseif ($act=='setmeal_list')
{
	$setmeal = get_user_setmeal($_SESSION['uid']);
	if ($setmeal['endtime']>0){
		$setmeal_endtime=sub_day($setmeal['endtime'],time());
	}else{
		$setmeal_endtime="������";
	}
	$smarty->assign('user_setmeal',$setmeal);
	$smarty->assign('setmeal_endtime',$setmeal_endtime);
	$smarty->assign('title','�����б� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('setmeal',get_setmeal());
	$smarty->display('member_company/company_setmeal_list.htm');
}
elseif ($act=='setmeal_order_add')
{
	$setmealid = intval($_GET['setmealid'])?intval($_GET['setmealid']):showmsg("��ѡ������ײͣ�",1);
	$setmeal = get_user_setmeal($_SESSION['uid']);
	if ($setmeal['endtime']>0){
		$setmeal_endtime=sub_day($setmeal['endtime'],time());
	}else{
		$setmeal_endtime="������";
	}
	$smarty->assign('user_setmeal',$setmeal);
	$smarty->assign('setmeal_endtime',$setmeal_endtime);
	$smarty->assign('title','������� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('setmeal',get_setmeal_one($setmealid));
	$smarty->assign('payment',get_payment());
	$smarty->display('member_company/company_order_add_setmeal.htm');
}
elseif ($act=='setmeal_order_add_save')
{
		if (empty($company_profile['companyname']))
		{
		$link[0]['text'] = "��д��ҵ����";
		$link[0]['href'] = 'company_info.php?act=company_profile';
		showmsg("������д������ҵ���ϣ�",1,$link);
		}
	$myorder=get_user_order($_SESSION['uid'],1);
	$order_num=count($myorder);
	if ($order_num>=5)
	{
	$link[0]['text'] = "�����鿴";
	$link[0]['href'] = '?act=order_list&is_paid=1';
	showmsg("δ�����Ķ������ܳ��� 5 �������ȴ������ٴ����룡",1,$link,true,8);
	}
	$setmeal=get_setmeal_one($_POST['setmealid']);
	if ($setmeal && $setmeal['apply']=="1")
	{
		$payment_name=empty($_POST['payment_name'])?showmsg("��ѡ�񸶿ʽ��",1):$_POST['payment_name'];
		$paymenttpye=get_payment_info($payment_name);
		if (empty($paymenttpye)) showmsg("֧����ʽ����",0);
		$fee=number_format(($setmeal['expense']/100)*$paymenttpye['fee'],1,'.','');//������
		$order['oid']= strtoupper(substr($paymenttpye['typename'],0,1))."-".date('ymd',time())."-".date('His',time());//������
		$order['v_url']=$_CFG['main_domain']."include/payment/respond_".$paymenttpye['typename'].".php";
		$order['v_amount']=$setmeal['expense']+$fee;//���
		$order_id=add_order($_SESSION['uid'],$order['oid'],$setmeal['expense'],$payment_name,"��ͨ����:".$setmeal['setmeal_name'],$timestamp,"",$setmeal['id'],1);
			if ($order_id)
			{
				if ($order['v_amount']==0)//0Ԫ�ײ�
				{
					if (order_paid($order['oid']))
					{
						$link[0]['text'] = "�鿴����";
						$link[0]['href'] = '?act=order_list';
						$link[1]['text'] = "��Ա������ҳ";
						$link[1]['href'] = 'company_index.php?act=';
						showmsg("�����ɹ���ϵͳ��Ϊ����ͨ�˷���",2,$link);	
					}
				}
				header("Location:?act=payment&order_id=".$order_id."");//����ҳ��
			}
			else
			{
			showmsg("���Ӷ���ʧ�ܣ�",0);
			}
	}
	else
	{
	showmsg("���Ӷ���ʧ�ܣ�",0);
	}
}
elseif ($act=='feedback')
{
	$smarty->assign('title','�û����� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('feedback',get_feedback($_SESSION['uid']));
	$smarty->display('member_company/company_feedback.htm');
}
elseif ($act=='feedback_save')
{
	$get_feedback=get_feedback($_SESSION['uid']);
	if (count($get_feedback)>=5) 
	{
	showmsg('������Ϣ���ܳ���5����',1);
	exit();
	}
	$setsqlarr['infotype']=intval($_POST['infotype']);
	$setsqlarr['feedback']=trim($_POST['feedback'])?trim($_POST['feedback']):showmsg("����д���ݣ�",1);
	$setsqlarr['uid']=$_SESSION['uid'];
	$setsqlarr['usertype']=$_SESSION['utype'];
	$setsqlarr['username']=$_SESSION['username'];
	$setsqlarr['addtime']=$timestamp;
	write_memberslog($_SESSION['uid'],1,7001,$_SESSION['username'],"�����˷�����Ϣ");
	!inserttable(table('feedback'),$setsqlarr)?showmsg("����ʧ�ܣ�",0):showmsg("���ӳɹ�����ȴ�����Ա�ظ���",2);
}
elseif ($act=='del_feedback')
{
	$id=intval($_GET['id']);
	del_feedback($id,$_SESSION['uid'])?showmsg('ɾ���ɹ���',2):showmsg('ɾ��ʧ�ܣ�',1);
}
unset($smarty);
?>