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
			$html="<div class=\"log_box\"><div class=\"log_tip\"><div>����ע�Ტ��¼����ܲ鿴��ҵ����ϵ��ʽ������<a href=\"".url_rewrite('QS_login',array('url'=>$_SERVER['HTTP_REFERER']),false,NULL,false)."\">[������¼]</a>����<a href=\"".$_CFG['main_domain']."user/user_reg.php\">[���ע��]</a></div></div></div>";
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
				$html="<div class=\"log_box\"><div class=\"log_tip\"><div>��û�з����������߼�����Ч������������ſ��Բ鿴��ϵ��ʽ��<a href=\"".get_member_url($_SESSION['utype'],true)."personal_resume.php?act=resume_list\">[�鿴�ҵļ���]</a></div></div></div>";
				}
			}
			else
			{
			$show=false;
			$html="<div class=\"log_box\"><div class=\"log_tip\"><div>����ע�Ტ��¼����ܲ鿴��ҵ����ϵ��ʽ������<a href=\"".url_rewrite('QS_login',array('url'=>$_SERVER['HTTP_REFERER']),false,NULL,false)."\">[������¼]</a>����<a href=\"".$_CFG['main_domain']."user/user_reg.php\">[���ע��]</a></div></div></div>";
			}
		}
		if ($show)
		{
		$sql = "select * from ".table('jobs_contact')." where pid='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_job']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$ul="<div class=\"contact_con\">";
			$contact=$val['contact_show']=='1'?"<p>�� ϵ �ˣ�<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=jobs_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p>":"<p>�� ϵ �ˣ���ҵ���ò����⹫��</p>";
			$telephone=$val['telephone_show']=='1'?"<p>�� ϵ �� ����<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=jobs_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p>":"<p>�� ϵ �� ������ҵ���ò����⹫��</p>";
			$email=$val['email_show']=='1'?"<p>��ϵ���䣺<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=jobs_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p>":"<p>��ϵ���䣺��ҵ���ò����⹫��</p>";
			$ull="<div class=\"tip\"><div>��ʾ����ӦƸ���������˵�λ���κ�������ӦƸ����ȡ���ö�����Υ�����ݣ�</div></div></div>";
			$html=$ul.$contact.$telephone.$email.$ull;
			}
			else
			{
			$ul="<div class=\"contact_con\">";
			$contact=$val['contact_show']=='1'?"<p>�� ϵ �ˣ�{$val['contact']}</p>":"<p>�� ϵ �ˣ���ҵ���ò����⹫��</p>";
			$telephone=$val['telephone_show']=='1'?"<p>��ϵ�绰��{$val['telephone']}</p>":"<p>��ϵ�绰����ҵ���ò����⹫��</p>";
			$email=$val['email_show']=='1'?"<p>��ϵ���䣺{$val['email']}</p>":"<p>��ϵ���䣺��ҵ���ò����⹫��</p>";
			$ull="<div class=\"tip\"><div>��ʾ����ӦƸ���������˵�λ���κ�������ӦƸ����ȡ���ö�����Υ�����ݣ�</div></div></div>";
			$html=$ul.$contact.$telephone.$email.$ull;
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
			$html="<p>������ <a href=\"".url_rewrite('QS_login',array('url'=>$_SERVER['HTTP_REFERER']),false,NULL,false)."\">[��¼]</a> ��鿴��ϵ��ʽ���������û���ʺţ����� <a href=\"".$_CFG['main_domain']."user/user_reg.php\">[���ע��]</a></p>";
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
				$html="<p>��û�з����������߼�����Ч������������ſ��Բ鿴��ϵ��ʽ��<a href=\"".get_member_url($_SESSION['utype'],true)."personal_resume.php?act=resume_list\">[�鿴�ҵļ���]</a></p>";
				}
			}
			else
			{
			$show=false;
			$html="<p>������ <a href=\"".url_rewrite('QS_login',array('url'=>$_SERVER['HTTP_REFERER']),false,NULL,false)."\">[��¼]</a> ��鿴��ϵ��ʽ���������û���ʺţ����� <a href=\"".$_CFG['main_domain']."user/user_reg.php\">[���ע��]</a></p>";
			}
		}
		if ($show)
		{
		$sql = "select contact,contact_show,telephone,telephone_show,email,email_show,address,address_show,website FROM ".table('company_profile')." where id='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_com']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$ul="<ul>";
			$contact=$val['contact_show']=='1'?"<li>�� ϵ �ˣ�<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=company_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>�� ϵ �ˣ���ҵ���ò����⹫��</li>";
			$telephone=$val['telephone_show']=='1'?"<li>�� ϵ �� ����<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=company_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>�� ϵ �� ������ҵ���ò����⹫��</li>";
			$email=$val['email_show']=='1'?"<li>��ϵ���䣺<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=company_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>��ϵ���䣺��ҵ���ò����⹫��</li>";
			$ull="</ul>";
			$html=$ul.$contact.$telephone.$email.$ull;
			}
			else
			{
			$ul="<ul>";
			$contact=$val['contact_show']=='1'?"<li>�� ϵ �ˣ�{$val['contact']}</li>":"<li>�� ϵ �ˣ���ҵ���ò����⹫��</li>";
			$telephone=$val['telephone_show']=='1'?"<li>��ϵ�绰��{$val['telephone']}</li>":"<li>��ϵ�绰����ҵ���ò����⹫��</li>";
			$email=$val['email_show']=='1'?"<li>��ϵ���䣺{$val['email']}</li>":"<li>��ϵ���䣺��ҵ���ò����⹫��</li>";
			$ull.="</ul>";
			$html=$ul.$contact.$telephone.$email.$ull;
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
				if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1')
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html="<div class=\"btnIsLogin\"><a class=\"download\" href=\"javascript:void(0);\">�鿴��ϵ��ʽ</a></div>";
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
					$html="<div class=\"btnIsLogin\"><a class=\"download\" href=\"javascript:void(0);\">�鿴��ϵ��ʽ</a></div>";
					}
				}
				else
				{
				$show=false;
				$html="<div class=\"btnIsLogin\"><a class=\"download\" href=\"javascript:void(0);\">�鿴��ϵ��ʽ</a></div>";
				}
			}
		}
		
		if ($show)
		{
			$val=$db->getone("select fullname,telephone,email from ".table('resume')." WHERE  id='{$id}'  LIMIT 1");
			
			if ($_CFG['contact_img_resume']=='2')
			{
			$token=md5($val['fullname'].$id.$val['telephone']);
			$html="<ul>";
			$html.="<li>�� ϵ �ˣ�<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=resume_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>";
			$html.="<li>��ϵ�绰��<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=resume_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>";
			$html.="<li>��ϵ���䣺<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=resume_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>";
			$html.="</ul>";
			}
			else
			{
			$html="<ul>";
			$html.="<li>�� ϵ �ˣ�".$val['fullname']."</li>";
			$html.="<li>��ϵ�绰��".$val['telephone']."</li>";
			$html.="<li>��ϵ���䣺".$val['email']."</li>";
			$html.="</ul>";
			}
			exit($html);
		}
		else
		{		
		exit($html);
		}
}
//3.4
elseif($act == 'course_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$show=false;
		if($_CFG['showcoursecontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showcoursecontact']=='1')
		{
		//��¼�󣬲鿴Ȩ��
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html="<p>�� <a href=\"".url_rewrite('QS_login',array('url'=>$_SERVER['HTTP_REFERER']),false,NULL,false)."\">[��¼]</a> ��鿴��ϵ��ʽ���������û���ʺţ����� <a href=\"".$_CFG['main_domain']."user/user_reg.php\">[���ע��]</a></p>";
			}
		}
		if ($show)
		{
		$sql = "select * from ".table('course_contact')." where pid='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_course']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$ul="<ul>";
			$contact=$val['contact_show']=='1'?"<li>�� ϵ �ˣ�<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=course_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>�� ϵ �ˣ��������ò����⹫��</li>";
			$telephone=$val['telephone_show']=='1'?"<li>�� ϵ �� ����<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=course_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>�� ϵ �� �����������ò����⹫��</li>";
			$email=$val['email_show']=='1'?"<li>��ϵ���䣺<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=course_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>��ϵ���䣺�������ò����⹫��</li>";
			$ull="</ul>";
			$html=$ul.$contact.$telephone.$email.$ull;
			}
			else
			{
			$ul="<ul>";
			$contact=$val['contact_show']=='1'?"<li>�� ϵ �ˣ�{$val['contact']}</li>":"<li>�� ϵ �ˣ��������ò����⹫��</li>";
			$telephone=$val['telephone_show']=='1'?"<li>��ϵ�绰��{$val['telephone']}</li>":"<li>��ϵ�绰���������ò����⹫��</li>";
			$email=$val['email_show']=='1'?"<li>��ϵ���䣺{$val['email']}</li>":"<li>��ϵ���䣺�������ò����⹫��</li>";
			$ull="</ul>";
			$html=$ul.$contact.$telephone.$email.$ull;
			}
		exit($html);
		}
		else
		{		
		exit($html);
		}
	}
}
elseif($act == 'train_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$show=false;
		if($_CFG['showcoursecontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showcoursecontact']=='1')
		{
		//��Ҫע��
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html="<p>�� <a href=\"".url_rewrite('QS_login',array('url'=>$_SERVER['HTTP_REFERER']),false,NULL,false)."\">[��¼]</a>  ��鿴��ϵ��ʽ�������û����û���ʺţ����� <a href=\"".$_CFG['main_domain']."user/user_reg.php\">[���ע��]</a></div>";
			}
		}
		if ($show)
		{
		$sql = "select contact,contact_show,telephone,telephone_show,email,email_show,address,address_show,website FROM ".table('train_profile')." where id='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_train']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$ul="<ul>";
			$contact=$val['contact_show']=='1'?"<li>�� ϵ �ˣ�<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=train_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>�� ϵ �ˣ��������ò����⹫��</li>";
			$telephone=$val['telephone_show']=='1'?"<li>�� ϵ �� ����<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=train_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>�� ϵ �� �����������ò����⹫��</li>";
			$email=$val['email_show']=='1'?"<li>��ϵ���䣺<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=train_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>��ϵ���䣺�������ò����⹫��</li>";
			$address=$val['address_show']=='1'?"<li>��ϵ��ַ��<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=train_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>��ϵ��ַ���������ò����⹫��</li>";
			$website.="<li>������ַ��<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=train_contact&type=5&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>";		
			$ull="</ul>";
			$html=$ul.$contact.$telephone.$email.$address.$website.$ull;
			}
			else
			{
			$ul="<ul>";
			$contact=$val['contact_show']=='1'?"<li>�� ϵ �ˣ�{$val['contact']}</li>":"<li>�� ϵ �ˣ��������ò����⹫��</li>";
			$telephone=$val['telephone_show']=='1'?"<li>��ϵ�绰��{$val['telephone']}</li>":"<li>��ϵ�绰���������ò����⹫��</li>";
			$email=$val['email_show']=='1'?"<li>��ϵ���䣺{$val['email']}</li>":"<li>��ϵ���䣺�������ò����⹫��</li>";
			$address=$val['address_show']=='1'?"<li>��ϵ��ַ��{$val['address']}</li>":"<li>��ϵ��ַ���������ò����⹫��</li>";
			$website="<li>������ַ��{$val['website']}</li>";
			$ull.="</ul>";
			$html=$ul.$contact.$telephone.$email.$address.$website.$ull;
			}
			exit($html);
		}
		else
		{		
		exit($html);
		}
	}
}
elseif($act == 'teacher_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$show=false;
		if($_CFG['showcoursecontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showcoursecontact']=='1')
		{
		//��Ҫע��
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html="<p>�� <a href=\"".url_rewrite('QS_login',array('url'=>$_SERVER['HTTP_REFERER']),false,NULL,false)."\">[��¼]</a> ��鿴��ϵ��ʽ�������û����û���ʺţ����� <a href=\"".$_CFG['main_domain']."user/user_reg.php\">[���ע��]</a></p>";
			}
		}
		if ($show)
		{
		$sql = "select telephone,telephone_show,email,email_show,address,address_show,qq,qq_show,website FROM ".table('train_teachers')." where id='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_train']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$ul="<ul>";
			$telephone=$val['telephone_show']=='1'?"<li>�� ϵ �� ����<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=teacher_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>�� ϵ �� ������ʦ���ò����⹫��</li>";
			$email=$val['email_show']=='1'?"<li>��ϵ���䣺<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=teacher_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>��ϵ���䣺��ʦ���ò����⹫��</li>";
			$address=$val['address_show']=='1'?"<li>��ϵ��ַ��<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=teacher_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>��ϵ��ַ����ʦ���ò����⹫��</li>";
			$ull="</ul>";
			$html=$ul.$contact.$telephone.$email.$address.$ull;
			}
			else
			{
			$ul="<ul>";
			$telephone=$val['telephone_show']=='1'?"<li>��ϵ�绰��{$val['telephone']}</li>":"<li>��ϵ�绰����ʦ���ò����⹫��</li>";
			$email=$val['email_show']=='1'?"<li>��ϵ���䣺{$val['email']}</li>":"<li>��ϵ���䣺��ʦ���ò����⹫��</li>";
			$address=$val['address_show']=='1'?"<li>��ϵ��ַ��{$val['address']}</li>":"<li>��ϵ��ַ����ʦ���ò����⹫��</li>";
			$ull.="</ul>";
			$html=$ul.$contact.$telephone.$email.$address.$ull;
			}
			exit($html);
		}
		else
		{		
		exit($html);
		}
	}
}
//�߼�ְλ
elseif($act == 'hunterjobs_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$show=false;
		if($_CFG['showhunterjobcontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showhunterjobcontact']=='1')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html="<p>���˻�Ա�� <a href=\"".url_rewrite('QS_login',array('url'=>$_SERVER['HTTP_REFERER']),false,NULL,false)."\">��¼</a>  �鿴��ϵ��ʽ����������Ǹ��˻�Ա������ <a href=\"".$_CFG['main_domain']."user/user_reg.php\">���ע��</a> ��Ϊ���˻�Ա��</p>";
			}
		}
		//�Ƿ񴴽��߼������˼���
		elseif($_CFG['showhunterjobcontact']=='2')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
				$val=$db->getone("select uid from ".table('manager_resume')." where uid='{$_SESSION['uid']}' and complete=1 and audit=1 LIMIT 1");
			 	if (!empty($val))
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html="<p>��û�з��������˼������߼�����Ч�����������˼�����ſ��Բ鿴��ϵ��ʽ��<a href=\"".get_member_url($_SESSION['utype'],true)."personal_managerresume.php?act=resume_list\">[�鿴�ҵľ����˼���]</a></p>";
				}
			}
			else
			{
			$show=false;
			$html="<p>���˻�Ա�� <a href=\"".url_rewrite('QS_login',array('url'=>$_SERVER['HTTP_REFERER']),false,NULL,false)."\">��¼</a>  �鿴��ϵ��ʽ����������Ǹ��˻�Ա������ <a href=\"".$_CFG['main_domain']."user/user_reg.php\">���ע��</a> ��Ϊ���˻�Ա��</p>";
			}
		}
		
		if ($show)
		{
		$sql = "select telephone,telephone_show,email,email_show,address,address_show,contact,contact_show,qq,qq_show from ".table('hunter_jobs')." where id='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_hunterjob']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$contact=$val['contact_show']=='1'?"<p>�� ϵ �ˣ�<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=hunterjobs_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p>":"<p>�� ϵ �ˣ���ҵ���ò����⹫��</p>";
			$telephone=$val['telephone_show']=='1'?"<p>�� ϵ �� ����<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=hunterjobs_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p>":"<p>�� ϵ �� ������ҵ���ò����⹫��</p>";
			$email=$val['email_show']=='1'?"<p>��ϵ���䣺<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=hunterjobs_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p>":"<p>��ϵ���䣺��ҵ���ò����⹫��</p>";
			$address=$val['address_show']=='1'?"<p>��ϵ��ַ��<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=hunterjobs_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p>":"<p>��ϵ��ַ����ҵ���ò����⹫��</p>";
			$qq=$val['qq_show']=='1'?"<p>��ϵQ Q�� <img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=hunterjobs_contact&type=5&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p>":"<p>��ϵQ Q����ҵ���ò����⹫�� </p>";
			$html=$contact.$telephone.$email.$address.$qq;
			}
			else
			{
			$contact=$val['contact_show']=='1'?"<p>�� ϵ �ˣ�{$val['contact']}</p>":"<p>�� ϵ �ˣ���ҵ���ò����⹫��</p>";
			$telephone=$val['telephone_show']=='1'?"<p>��ϵ�绰��{$val['telephone']}</p>":"<p>��ϵ�绰����ҵ���ò����⹫��</p>";
			$email=$val['email_show']=='1'?"<p>��ϵ���䣺{$val['email']}</p>":"<p>��ϵ���䣺��ҵ���ò����⹫��</p>";
			$address=$val['address_show']=='1'?"<p>��ϵ��ַ��{$val['address']}</p>":"<p>��ϵ��ַ����ҵ���ò����⹫��</p>";
			$qq=$val['qq_show']=='1'?"<p>��ϵQ Q��{$val['qq']}</p>":"<p>��ϵQ Q����ҵ���ò����⹫��</p>";
			$html=$contact.$telephone.$email.$address.$qq;
			}
		exit($html);
		}
		else
		{		
		exit($html);
		}
	}
		
}
elseif($act == 'hunter_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$show=false;
		if($_CFG['showhunterjobcontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showhunterjobcontact']=='1')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html="<div class=\"hunter_log\">���˻�Ա�� <a href=\"".url_rewrite('QS_login',array('url'=>$_SERVER['HTTP_REFERER']),false,NULL,false)."\">��¼</a>  �鿴��ϵ��ʽ����������Ǹ��˻�Ա������ <a href=\"".$_CFG['main_domain']."user/user_reg.php\">���ע��</a> ��Ϊ���˻�Ա��</div>";
			}
		}
		//�Ƿ񴴽��߼�����
		elseif($_CFG['showhunterjobcontact']=='2')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
				$val=$db->getone("select uid from ".table('resume')." where uid='{$_SESSION['uid']}' and complete=1 and audit=1 and talent=2 LIMIT 1");
			 	if (!empty($val))
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html="<p>��û�з����߼��������߼�����Ч�������߼�������ſ��Բ鿴��ϵ��ʽ��<a href=\"".get_member_url($_SESSION['utype'],true)."personal_managerresume.php?act=resume_list\">[�鿴�ҵľ����˼���]</a></p>";
				}
			}
			else
			{
			$show=false;
			$html="<p>���˻�Ա�� <a href=\"".url_rewrite('QS_login',array('url'=>$_SERVER['HTTP_REFERER']),false,NULL,false)."\">��¼</a>  �鿴��ϵ��ʽ����������Ǹ��˻�Ա������ <a href=\"".$_CFG['main_domain']."user/user_reg.php\">���ע��</a> ��Ϊ���˻�Ա��</p>";
			}
		}
		
		if ($show)
		{
		$sql = "select telephone,telephone_show,email,email_show,address,address_show,contact,contact_show,qq,qq_show from ".table('hunter_jobs')." where id='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_hunterjob']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$contact=$val['contact_show']=='1'?"<p>�� ϵ �ˣ�<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=hunterjobs_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p>":"<p>�� ϵ �ˣ���ҵ���ò����⹫��</p>";
			$telephone=$val['telephone_show']=='1'?"<p>�� ϵ �� ����<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=hunterjobs_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p>":"<p>�� ϵ �� ������ҵ���ò����⹫��</p>";
			$email=$val['email_show']=='1'?"<p>��ϵ���䣺<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=hunterjobs_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p>":"<p>��ϵ���䣺��ҵ���ò����⹫��</p>";
			$address=$val['address_show']=='1'?"<p>��ϵ��ַ��<img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=hunterjobs_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p>":"<p>��ϵ��ַ����ҵ���ò����⹫��</p>";
			$qq=$val['qq_show']=='1'?"<p>��ϵQ Q�� <img src=\"{$_CFG['main_domain']}plus/contact_img.php?act=hunterjobs_contact&type=5&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></p>":"<p>��ϵQ Q����ҵ���ò����⹫�� </p>";
			$html=$contact.$telephone.$email.$address.$qq;
			}
			else
			{
			$contact=$val['contact_show']=='1'?"<p>�� ϵ �ˣ�{$val['contact']}</p>":"<p>�� ϵ �ˣ���ҵ���ò����⹫��</p>";
			$telephone=$val['telephone_show']=='1'?"<p>��ϵ�绰��{$val['telephone']}</p>":"<p>��ϵ�绰����ҵ���ò����⹫��</p>";
			$email=$val['email_show']=='1'?"<p>��ϵ���䣺{$val['email']}</p>":"<p>��ϵ���䣺��ҵ���ò����⹫��</p>";
			$address=$val['address_show']=='1'?"<p>��ϵ��ַ��{$val['address']}</p>":"<p>��ϵ��ַ����ҵ���ò����⹫��</p>";
			$qq=$val['qq_show']=='1'?"<p>��ϵQ Q��{$val['qq']}</p>":"<p>��ϵQ Q����ҵ���ò����⹫��</p>";
			$html=$contact.$telephone.$email.$address.$qq;
			}
		exit($html);
		}
		else
		{		
		exit($html);
		}
	}
}
?>