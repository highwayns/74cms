<?php
 /*
 * 74cms ajax ��ϵ��ʽ
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/plus.common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : ''; 

if($act == 'jobs_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$jobs_one=$db->getone("select * from ".table("jobs")." where id=$id ");
		$jobs_tmp=$db->getone("select * from ".table("jobs_tmp")." where id=$id ");
		$jobs = empty($jobs_one)?$jobs_tmp:$jobs_one;
		$show=false;
		if($_CFG['showjobcontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showjobcontact']=='1')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html='<div class="no-login">
									<span class="border-item topleft"></span>
									<span class="border-item topright"></span>
									<span class="border-item bottomleft"></span>
									<span class="border-item bottomright"></span>
									<p>������ע�Ტ��¼����ܲ鿴��ҵ����ϵ��ʽ������<a href="javascript:void(0);" class="ajax_user_login" style="color:#ff7400">[������¼]</a>����<a href="'.$_CFG['site_dir'].'user/user_reg.php" style="color:#ff7400">[���ע��]</a></p>
								</div>';
			}
		}
		elseif($_CFG['showjobcontact']=='2')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
				$val=$db->getone("select uid from ".table('resume')." where uid='{$_SESSION['uid']}' LIMIT 1");
			 	if (!empty($val))
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html='<div class="no-login">
									<span class="border-item topleft"></span>
									<span class="border-item topright"></span>
									<span class="border-item bottomleft"></span>
									<span class="border-item bottomright"></span>
									<p>��û�з����������߼�����Ч������������ſ��Բ鿴��ϵ��ʽ��<a href="'.get_member_url($_SESSION['utype'],true).'personal_resume.php?act=resume_list">[�鿴�ҵļ���]</a></p>
								</div>';
				}
			}
			else
			{
			$show=false;
			$html='<div class="no-login">
									<span class="border-item topleft"></span>
									<span class="border-item topright"></span>
									<span class="border-item bottomleft"></span>
									<span class="border-item bottomright"></span>
									<p>������ע�Ტ��¼����ܲ鿴��ҵ����ϵ��ʽ������<a href="javascript:void(0);" class="ajax_user_login" style="color:#ff7400">[������¼]</a>����<a href="'.$_CFG['site_dir'].'user/user_reg.php" style="color:#ff7400">[���ע��]</a></p>
								</div>';
			}
		}
		if($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1' && $show==false)
		{
			if($jobs['uid']==$_SESSION['uid'])
			{
				$show=true;
			}
			else
			{
				$show=false;
			}
		}
		if ($show)
		{
		$sql = "select * from ".table('jobs_contact')." where pid='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_job']=='2')
			{
			$hashstr=$_GET['hashstr'];
			$token=md5($val['contact'].$id.$val['telephone']);
			$contact=$val['contact_show']=='1'?"<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"��ҵ���ò����⹫��";
			$telephone=$val['telephone_show']=='1'?"<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=2&id={$id}&token={$token}&hashstr={$hashstr}\"  border=\"0\" align=\"absmiddle\"/><a style=\"color:#017fcf\" id=\"tel_show_pic\" href=\"javascript:;\" >[�鿴]</a> <span  id=\"show_detail\" style='color:#666;display:none'>[��ϵ��ʱ��˵������&nbsp;".$_CFG['site_name']."&nbsp;�Ͽ�����]</span>":"��ҵ���ò����⹫��";
			$email=$val['email_show']=='1'?"<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/>":"��ҵ���ò����⹫��";

			$html='<div class="c-contact">
									<div class="contact-item clearfix">
										<div class="contact-type f-left">�� ϵ �ˣ�</div>
										<div class="contact-content f-left">'.$contact.'</div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ�绰��</div>
										<div class="contact-content f-left">'.$telephone.'</div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ���䣺</div>
										<div class="contact-content f-left">'.$email.'</div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��˾��ַ��</div>
										<div class="contact-content f-left">'.$val['address'].'</div>
									</div>
								</div>';
			}
			else
			{
			$contact=$val['contact_show']=='1'?"{$val['contact']}":"��ҵ���ò����⹫��";
			$telephone=$val['telephone_show']=='1'?"{$val['telephone']}":"��ҵ���ò����⹫��</li>";
			$email=$val['email_show']=='1'?"{$val['email']}":"��ҵ���ò����⹫��</li>";
						$html='<div class="c-contact">
									<div class="contact-item clearfix">
										<div class="contact-type f-left">�� ϵ �ˣ�</div>
										<div class="contact-content f-left">'.$contact.'</div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ�绰��</div>
										<div class="contact-content f-left"><span>'.$telephone.'</span></div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ���䣺</div>
										<div class="contact-content f-left"><span>'.$email.'</span></div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��˾��ַ��</div>
										<div class="contact-content f-left">'.$val['address'].'</div>
									</div>
								</div>';
			}
		exit($html);
		}
		else
		{		
		exit($html);
		}
	}
		
}
elseif($act == 'company_contact')
{
	$id=intval($_GET['id']);
	$company_profile = $db->getone("SELECT uid FROM ".table('company_profile')." WHERE id=$id");
	if ($id>0)
	{
		$show=false;
		if($_CFG['showjobcontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showjobcontact']=='1')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html='<div class="no-login">
									<span class="border-item topleft"></span>
									<span class="border-item topright"></span>
									<span class="border-item bottomleft"></span>
									<span class="border-item bottomright"></span>
									<p>������ע�Ტ��¼����ܲ鿴��ҵ����ϵ��ʽ������<a href="javascript:void(0);" class="ajax_user_login" style="color:#ff7400">[������¼]</a>����<a href="'.$_CFG['site_dir'].'user/user_reg.php" style="color:#ff7400">[���ע��]</a></p>
								</div>';
			}
		}
		elseif($_CFG['showjobcontact']=='2')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
				$val=$db->getone("select uid from ".table('resume')." where uid='{$_SESSION['uid']}' LIMIT 1");
			 	if (!empty($val))
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html='<div class="no-login">
									<span class="border-item topleft"></span>
									<span class="border-item topright"></span>
									<span class="border-item bottomleft"></span>
									<span class="border-item bottomright"></span>
									<p>��û�з����������߼�����Ч������������ſ��Բ鿴��ϵ��ʽ��<a href="'.get_member_url($_SESSION['utype'],true).'personal_resume.php?act=resume_list">[�鿴�ҵļ���]</a></p>
								</div>';
				}
			}
			else
			{
			$show=false;
			$html='<div class="no-login">
									<span class="border-item topleft"></span>
									<span class="border-item topright"></span>
									<span class="border-item bottomleft"></span>
									<span class="border-item bottomright"></span>
									<p>������ע�Ტ��¼����ܲ鿴��ҵ����ϵ��ʽ������<a href="javascript:void(0);" class="ajax_user_login" style="color:#ff7400">[������¼]</a>����<a href="'.$_CFG['site_dir'].'user/user_reg.php" style="color:#ff7400">[���ע��]</a></p>
								</div>';
			}
		}
		if($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1' && $show==false)
		{
			if($company_profile['uid']==$_SESSION['uid'])
			{
				$show=true;
			}
			else
			{
				$show=false;
			}
		}
		if ($show)
		{
		$sql = "select contact,contact_show,telephone,telephone_show,email,email_show,address,address_show,website FROM ".table('company_profile')." where id='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_com']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$contact=$val['contact_show']=='1'?"<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"��ҵ���ò����⹫��";
			$telephone=$val['telephone_show']=='1'?"<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/>":"��ҵ���ò����⹫��";
			$email=$val['email_show']=='1'?"<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/>":"��ҵ���ò����⹫��";

			$html='<div class="c-contact">
									<div class="contact-item clearfix">
										<div class="contact-type f-left">�� ϵ �ˣ�</div>
										<div class="contact-content f-left">'.$contact.'</div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ�绰��</div>
										<div class="contact-content f-left">'.$telephone.'</div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ���䣺</div>
										<div class="contact-content f-left">'.$email.'</div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��˾��ַ��</div>
										<div class="contact-content f-left">'.$val['address'].'</div>
									</div>
								</div>';
			}
			else
			{
			$contact=$val['contact_show']=='1'?"{$val['contact']}":"��ҵ���ò����⹫��";
			$telephone=$val['telephone_show']=='1'?"{$val['telephone']}":"��ҵ���ò����⹫��</li>";
			$email=$val['email_show']=='1'?"{$val['email']}":"��ҵ���ò����⹫��</li>";
						$html='<div class="c-contact">
									<div class="contact-item clearfix">
										<div class="contact-type f-left">�� ϵ �ˣ�</div>
										<div class="contact-content f-left">'.$contact.'</div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ�绰��</div>
										<div class="contact-content f-left"><span>'.$telephone.'</span></div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��ϵ���䣺</div>
										<div class="contact-content f-left"><span>'.$email.'</span></div>
									</div>
									<div class="contact-item clearfix">
										<div class="contact-type f-left">��˾��ַ��</div>
										<div class="contact-content f-left">'.$val['address'].'</div>
									</div>
								</div>';
			}
			exit($html);
		}
		else
		{		
		exit($html);
		}
	}
}
elseif($act == 'resume_contact')
{
		$id=intval($_GET['id']);
		$show=false;
		$resume_sql="select * from ".table("resume")." where id=$id ";
		$resume_one=$db->getone($resume_sql);
		if($_SESSION["utype"]==2 && $_SESSION["uid"]==$resume_one["uid"]){
			$show=true;
		}
		if($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1' && $_CFG['showapplycontact']=='1'){
			$has = $db->getone("select 1 from ".table('personal_jobs_apply')." where company_uid=".intval($_SESSION['uid'])." and resume_id=".$id);
			if($has){
				$show = true;
			}
		}
		if($show==false){
			if($_CFG['showresumecontact']=='0')
			{
			$show=true;
			}
			elseif($_CFG['showresumecontact']=='1')
			{
				if ($_SESSION['uid'] && $_SESSION['username'] && ($_SESSION['utype']=='1' || $_SESSION['utype']=='3'))
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html="<div class=\"contact-box\"><input type=\"button\" value=\"�鿴��ϵ��ʽ\" class=\"contact-btn download\" /></div>";
				}
			}
			elseif($_CFG['showresumecontact']=='2')
			{
				if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1')
				{
					$sql = "select did from ".table('company_down_resume')." WHERE company_uid = {$_SESSION['uid']} AND resume_id='{$id}' LIMIT 1";
					$info=$db->getone($sql);
					if (!empty($info))
					{
					$show=true;
					}
					else
					{
					$show=false;
					$html="<div class=\"contact-box\"><input type=\"button\" value=\"�鿴��ϵ��ʽ\" class=\"contact-btn download\" /></div>";
					}
				}
				else
				{
				$show=false;
				$html="<div class=\"contact-box\"><input type=\"button\" value=\"�鿴��ϵ��ʽ\" class=\"contact-btn download\" /></div>";
				}
			}
		}
		if ($show)
		{
			$val=$db->getone("select fullname,telephone,email,word_resume from ".table('resume')." WHERE  id='{$id}'  LIMIT 1");
			/*
				���������״̬
			*/
			if($_SESSION['uid'] && $_SESSION['utype']==1)
			{
				$resume_state=$db->getone("select resume_state from ".table("company_label_resume")." where resume_id=$id and uid=$_SESSION[uid]");
				switch ($resume_state['resume_state']) {
					case 1:
						$state_htm="<input type=\"button\" value=\"��δ��ͨ\" class=\"interview-state\" state=\"4\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"3\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state selected\" state=\"1\" resume_id=\"{$id}\"/><input type=\"button\" value=\"������\" class=\"interview-state\" state=\"2\" resume_id=\"{$id}\"/>";
						break;
					case 2:
						$state_htm="<input type=\"button\" value=\"��δ��ͨ\" class=\"interview-state\" state=\"4\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"3\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"1\" resume_id=\"{$id}\"/><input type=\"button\" value=\"������\" class=\"interview-state selected\" state=\"2\" resume_id=\"{$id}\"/>";
						break;
					case 3:
						$state_htm="<input type=\"button\" value=\"��δ��ͨ\" class=\"interview-state\" state=\"4\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state selected\" state=\"3\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"1\" resume_id=\"{$id}\"/><input type=\"button\" value=\"������\" class=\"interview-state\" state=\"2\" resume_id=\"{$id}\"/>";
						break;
					case 4:
						$state_htm="<input type=\"button\" value=\"��δ��ͨ\" class=\"interview-state selected\" state=\"4\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"3\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"1\" resume_id=\"{$id}\"/><input type=\"button\" value=\"������\" class=\"interview-state\" state=\"2\" resume_id=\"{$id}\"/>";
						break;
					default:
						$state_htm="<input type=\"button\" value=\"��δ��ͨ\" class=\"interview-state\" state=\"4\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"3\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"1\" resume_id=\"{$id}\"/><input type=\"button\" value=\"������\" class=\"interview-state\" state=\"2\" resume_id=\"{$id}\"/>";
						break;
				}
			}
			else
			{
				$state_htm="<input type=\"button\" value=\"��δ��ͨ\" class=\"interview-state\" state=\"4\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"3\" resume_id=\"{$id}\"/><input type=\"button\" value=\"����\" class=\"interview-state\" state=\"1\" resume_id=\"{$id}\"/><input type=\"button\" value=\"������\" class=\"interview-state\" state=\"2\" resume_id=\"{$id}\"/>";
			}
			// word ����
			if($val['word_resume'])
			{
				$word_resume="<a class=\"word_resume\" href=\"".$_CFG['site_dir']."data/word/".$val['word_resume']."\"><img src=\"".$_CFG['site_template']."/images/word_resume.png\"> word����</a>";
			}
			if ($_CFG['contact_img_resume']=='2')
			{
			$token=md5($val['fullname'].$id.$val['telephone']);
			$html="<div class=\"contact-interview\">";
			$html.="<div class=\"contact-text\">��ϵ��ʽ��<span><img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/><em>|</em><img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/>".$word_resume."</span></div>";
			$html.="<div class=\"interview-block\">";
			$html.="<input id=\"invited\" type=\"button\" value=\"������������\" class=\"contact-btn\" resume_id=\"{$id}\" />".$state_htm;
			$html.="</div>"; 
			$html.="</div>";
			}
			else
			{
			$html="<div class=\"contact-interview\">";
			$html.="<div class=\"contact-text\">��ϵ��ʽ��<span>".$val['telephone']."<em>|</em>".$val['email'].$word_resume."</span></div>";
			$html.="<div class=\"interview-block\">";
			$html.="<input id=\"invited\" type=\"button\" value=\"������������\" class=\"contact-btn\" resume_id=\"{$id}\" />".$state_htm;
			$html.="</div>"; 
			$html.="</div>";
			}
			exit($html);
		}
		else
		{		
		exit($html);
		}
}
?>