<?php
 /*
 * 74cms ajax����
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../../include/plus.common.inc.php');
require_once(dirname(__FILE__).'/../../include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_company.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
if($act == 'ajaxjobslist'){
	$jobslisthtml="";
	$district = intval($_GET['district'])==0?"":intval($_GET['district']);
	$sdistrict = intval($_GET['sdistrict'])==0?"":intval($_GET['sdistrict']);
	$trade = intval($_GET['trade'])==0?"":intval($_GET['trade']);
	$topclass = intval($_GET['topclass'])==0?"":intval($_GET['topclass']);
	$category = intval($_GET['category'])==0?"":intval($_GET['category']);
	$subclass = intval($_GET['subclass'])==0?"":intval($_GET['subclass']);
	$recommend = intval($_GET['recommend'])==0?"":intval($_GET['recommend']);
	$emergency = intval($_GET['emergency'])==0?"":intval($_GET['emergency']);
	$wage = intval($_GET['wage'])==0?"":intval($_GET['wage']);
	$key = empty($_GET['key'])?"":$_GET['key'];
	$jobstable=table('jobs_search_stickrtime');
	if ($district<>'')
	{
		$wheresql.=" AND `district` = ".$district;
		if ($sdistrict<>'')
		{
			$wheresql.=" AND `sdistrict` = ".$sdistrict;
		}
	}
	if ($trade<>'')
	{
		$wheresql.=" AND `trade` = ".$trade;
	}
	if ($topclass<>'')
	{
		$wheresql.=" AND `topclass` = ".$topclass;
		if ($category<>'')
		{
			$wheresql.=" AND `category` = ".$category;
			if ($subclass<>'')
			{
				$wheresql.=" AND `subclass` = ".$subclass;
			}
		}
	}
	if ($wage<>'')
	{
		$wheresql.=" AND `wage` = ".$wage;
	}
	if ($recommend<>'')
	{
		$wheresql.=" AND `recommend` = ".$recommend;
	}
	if ($emergency<>'')
	{
		$wheresql.=" AND `emergency` = ".$emergency;
	}
	if (!empty($key))
	{
		$key=trim($key);
		$akey=explode(' ',$key);
		if (count($akey)>1)
		{
		$akey=array_filter($akey);
		$akey=array_slice($akey,0,2);
		$akey=array_map("fulltextpad",$akey);
		$ykey='+'.implode(' +',$akey);
		$mode=' IN BOOLEAN MODE';
		}
		else
		{
		$ykey=fulltextpad($key);
		$mode=' ';
		}
		$wheresql.=" AND  MATCH (`key`) AGAINST ('{$ykey}'{$mode}) ";
		$orderbysql="";
		$jobstable=table('jobs_search_key');
	}
	if (!empty($wheresql))
	{
	$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
	}
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']); 


	$idresult = $db->query("SELECT id FROM {$jobstable} ".$wheresql." ORDER BY `refreshtime` DESC LIMIT {$offset},{$rows}");
	while($row = $db->fetch_array($idresult))
	{
	$id[]=$row['id'];
	}
	if (!empty($id))
	{
		$wheresql=" WHERE id IN (".implode(',',$id).") ";
		$jobs = $db->getall("SELECT * FROM ".table('jobs').$wheresql." ORDER BY `refreshtime` DESC ");
		foreach ($jobs as $key => $value) {
			$jobs[$key]['url'] = wap_url_rewrite("wap-jobs-show",array("id"=>$value['id']));
		}	
	}
	else
	{
		$jobs=array();
	}
	// $jobslistarray=$db->getall("select * from ".table("jobs").$wheresql." ORDER BY `refreshtime` DESC LIMIT {$offset},{$rows}");
	if (!empty($jobs) && $offset<=100)
	{
		foreach($jobs as $li)
		{
			$url = wap_url_rewrite("wap-jobs-show",array("id"=>$li["id"]));
			$jobslisthtml.='<div class="list" id="li-'.$li["jobs_id"].'" url="'.$url.'">
	  <div class="t1"><span><a href="'.$url.'">'.$li["jobs_name"].'</a></span><br />
'.$li["companyname"].'</div>
	  <div class="t2">'.$li["district_cn"].'<br />'.$li["wage_cn"].'</div>
	  <div class="t3"><img src="images/14.png"  border="0"/></div>
	  <div class="clear"></div>
	</div>';
	}
		exit($jobslisthtml);
	}
	else
	{
		exit('-1');
	}
}
elseif($act == 'ajaxnewslist'){
	$newslisthtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']); 
	$newslistarray=$db->getall("select * from ".table('article')." ORDER BY `id` DESC LIMIT {$offset},{$rows}");
	if (!empty($newslistarray) && $offset<=100)
	{
		foreach($newslistarray as $li)
		{
			$url = "wap-news-show.php?id=$li[id]";
			$newslisthtml.='<div class="news_list" id="li-'.$offset.'" url="'.$url.'">
	  <div class="news_left"><h4 class="news_tit">'.$li["title"].'</h4></div>
	  <div class="news_right"><img src="images/14.png" border="0" /></div>
	  <div class="clear"></div>
	</div>';
		}
		exit($newslisthtml);
	}
	else
	{
		exit('-1');
	}
}
elseif($act == 'ajaxcomjobslist'){
	$jobslisthtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']); 
	$companyid=intval($_GET['companyid']); 
	$jobslistarray=$db->getall("select * from ".table('jobs')." WHERE `company_id`={$companyid} ORDER BY `refreshtime` DESC LIMIT {$offset},{$rows}");
	if (!empty($jobslistarray) && $offset<=100)
	{
		foreach($jobslistarray as $li)
		{
			$url = wap_url_rewrite("wap-jobs-show",array("id"=>$li["id"]));
			$jobslisthtml.='<div class="list" url="'.$url.'" id="li-'.$offset.'">
	<h3><a href="'.$url.'">'.$li["jobs_name"].'</a></h3>
	<h5>'.$li["wage_cn"].' '.$li["district_cn"].' </h5>   
	</div>';
	}
		exit($jobslisthtml);
	}
	else
	{
		exit('-1');
	}
}
elseif($act == 'ajaxresumelist'){
	$resumelisthtml="";
	$district = intval($_GET['district'])==0?"":intval($_GET['district']);
	$sdistrict = intval($_GET['sdistrict'])==0?"":intval($_GET['sdistrict']);
	$experience = intval($_GET['experience'])==0?"":intval($_GET['experience']);
	$education = intval($_GET['education'])==0?"":intval($_GET['education']);
	$topclass = intval($_GET['topclass'])==0?"":intval($_GET['topclass']);
	$category = intval($_GET['category'])==0?"":intval($_GET['category']);
	$subclass = intval($_GET['subclass'])==0?"":intval($_GET['subclass']);
	$talent = intval($_GET['talent'])==0?"":intval($_GET['talent']);
	$key = empty($_GET['key'])?"":$_GET['key'];
	$jobstable=table('resume_search_rtime');
	if ($talent<>'')
	{
		$wheresql.=" AND `talent`=".$talent." ";
	}
	if ($district<>'')
	{
		$wheresql.=" AND `district`=".$district." ";
	}
	if ($sdistrict<>'')
	{
		$wheresql.=" AND `sdistrict`=".$sdistrict." ";
	}
	if ($experience<>'')
	{
		$wheresql.=" AND `experience`=".$experience." ";
	}
	if ($education<>'')
	{
		$wheresql.=" AND `education`=".$education." ";
	}
	if ($topclass<>'' || $category<>'' || $subclass<>'')
	{
		if ($topclass<>'')
		{
			$joinwheresql.=" AND  topclass=".$topclass;
		}
		if ($category<>'')
		{
			$joinwheresql.=" AND  category=".$category;
		}
		if ($subclass<>'')
		{
			$joinwheresql.=" AND  subclass=".$subclass;
		}
		if (!empty($joinwheresql))
		{
		$joinwheresql=" WHERE ".ltrim(ltrim($joinwheresql),'AND');
		}
		$joinsql="  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_jobs')." {$joinwheresql} ) AS j ON  r.id=j.pid ";
	}
	$orderbysql=" ORDER BY `refreshtime` desc";
	if (!empty($key))
	{
		$key=trim($key);
		$akey=explode(' ',$key);
		if (count($akey)>1)
		{
		$akey=array_filter($akey);
		$akey=array_slice($akey,0,2);
		$akey=array_map("fulltextpad",$akey);
		$ykey='+'.implode(' +',$akey);
		$mode=' IN BOOLEAN MODE';
		}
		else
		{
		$ykey=fulltextpad($key);
		$mode=' ';
		}
		$wheresql.=" AND  MATCH (`key`) AGAINST ('{$ykey}'{$mode}) ";
		$orderbysql="";
		$jobstable=table('resume_search_key');
	}
	if (!empty($wheresql))
	{
	$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
	}
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']); 
	$idresult = $db->query("SELECT id FROM {$jobstable} as r ".$joinsql.$wheresql.$orderbysql." limit $offset,$rows ");
	while($row = $db->fetch_array($idresult))
	{
	$id[]=$row['id'];
	}
	if (!empty($id))
	{
		$wheresql=" WHERE id IN (".implode(',',$id).") ";
		$resume = $db->getall("SELECT * FROM ".table('resume').$wheresql.$orderbysql);	
		foreach ($resume as $key => $value) {
			$resume[$key]['url'] = wap_url_rewrite("wap-resume-show",array("id"=>$value["id"]));
		}
		
	}
	else
	{
		$resume=array();
	}
	if (!empty($resume) && $offset<=100)
	{
		foreach($resume as $li)
		{
			$url = wap_url_rewrite("wap-resume-show",array("id"=>$li["id"]));
			$resumelisthtml.='<div class="list" id="li-'.$offset.'" url="'.$url.'">
	  <span><a href="'.$url.'">'.$li["fullname"].'('.$li["sex_cn"].')</a></span><br />
ѧ����'.$li["education_cn"].'  �������飺'.$li["experience_cn"].'<br />
'.$li["intention_jobs"].'
	</div>';
	}
		exit($resumelisthtml);
	}
	else
	{
		exit('-1');
	}
}
elseif($act == 'jobs_contact')
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
			$html='<div class="job_show_box telbox"><h2>��ϵ��ʽ</h2>';
  			$html.='<div class="nolog_txt">���˻�Ա��<a href="wap_login.php">[��¼]</a>��鿴��ϵ��ʽ<br />û���ʺţ�<a href="wap_user_reg.php">[���ע��]</a></div></div>';
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
				$html='<div class="job_show_box telbox"><h2>��ϵ��ʽ</h2>';
				$html.='<div class="nolog_txt">��û�з����������߼�����Ч������������ſ��Բ鿴��ϵ��ʽ��</div></div>';
				}
			}
			else
			{
			$show=false;
			$html='<div class="job_show_box telbox"><h2>��ϵ��ʽ</h2>';
  			$html.='<div class="nolog_txt">���˻�Ա��<a href="wap_login.php">[��¼]</a>��鿴��ϵ��ʽ<br />û���ʺţ�<a href="wap_user_reg.php">[���ע��]</a></div></div>';
			}
		}
		if ($show)
		{
		$sql = "select * from ".table('jobs_contact')." where pid='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			// if ($_CFG['contact_img_job']=='2')
			// {
			// $token=md5($val['contact'].$id.$val['telephone']);
			// $contact=$val['contact_show']=='1'?"��ϵ�ˣ�<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/><br />":"��ϵ�ˣ���ҵ���ò����⹫��<br />";
			// $telephone=$val['telephone_show']=='1'?"��ϵ�绰��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/><br />":"��ϵ�绰����ҵ���ò����⹫��<br />";
			// $address=$val['address_show']=='1'?"��ϵ��ַ��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/><br />":"��ϵ��ַ����ҵ���ò����⹫��<br />";
			// $html='<div class="title"><h2>��ϵ��ʽ</h2></div><div class="txt telbox">';
			// $html.=$contact.$telephone.$address;
			// $html.='</div><div class="telimg"><a href="wtai://wp/mc;'.$val["telephone"].'"><img src="images/23.jpg"  border="0"/></a></div>';
			// }
			// else
			// {
			$contact=$val['contact_show']=='1'?"<div class='txt_box'>��ϵ�ˣ�{$val['contact']}<br />":"��ϵ�ˣ���ҵ���ò����⹫��<br />";
			$telephone=$val['telephone_show']=='1'?"��ϵ�绰��{$val['telephone']}<br />":"��ϵ�绰����ҵ���ò����⹫��<br />";
			$address=$val['address_show']=='1'?"��ϵ��ַ��{$val['address']}<br />":"��ϵ��ַ����ҵ���ò����⹫��<br /></div>";
			$tel=$val["telephone_show"]=='1'?$val['telephone']:"";
			$html='<div class="job_show_box telbox"><h2>��ϵ��ʽ</h2>';
			$html.=$contact.$telephone.$address;
			$html.='<div class="telimg"><a href="wtai://wp/mc;'.$tel.'"><img src="./images/23.jpg" alt="" /></a></div></div>';
			// }
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
			$html='<div class="title"><h2>��ϵ��ʽ</h2></div><div class="txt">';
			$html.='���˻�Ա��<a href="wap_login.php">[��¼]</a>��鿴��ϵ��ʽ<br />û���ʺţ�<a href="wap_user_reg.php">[���ע��]</a>';
			$html.='</div><div class="telimg"></div>';
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
				$html='<div class="title"><h2>��ϵ��ʽ</h2></div><div class="txt">';
				$html.="��û�з����������߼�����Ч������������ſ��Բ鿴��ϵ��ʽ��<a href=\"".get_member_url($_SESSION['utype'],true)."personal_resume.php?act=resume_list\">[�鿴�ҵļ���]</a>";
				$html.='</div><div class="telimg"></div>';
				}
			}
			else
			{
			$show=false;
			$html='<div class="title"><h2>��ϵ��ʽ</h2></div><div class="txt">';
			$html.='���˻�Ա��<a href="wap_login.php">[��¼]</a>��鿴��ϵ��ʽ<br />û���ʺţ�<a href="wap_user_reg.php">[���ע��]</a>';
			$html.='</div><div class="telimg"></div>';
			}
		}
		if ($show)
		{
		$sql = "select contact,contact_show,telephone,telephone_show,email,email_show,address,address_show,website FROM ".table('company_profile')." where id='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_com']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$contact=$val['contact_show']=='1'?"��ϵ�ˣ�<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/><br />":"��ϵ�ˣ���ҵ���ò����⹫��<br />";
			$telephone=$val['telephone_show']=='1'?"��ϵ�绰��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/><br />":"��ϵ�绰����ҵ���ò����⹫��<br />";
			$address=$val['address_show']=='1'?"��ϵ��ַ��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/><br />":"��ϵ��ַ����ҵ���ò����⹫��<br />";
			$html='<div class="title"><h2>��ϵ��ʽ</h2></div><div class="txt">';
			$html.=$contact.$telephone.$address;
			$html.='</div><div class="telimg"><a href="wtai://wp/mc;'.$val["telephone"].'"><img src="images/23.jpg"  border="0"/></a></div>';
			}
			else
			{
			$contact=$val['contact_show']=='1'?"��ϵ�ˣ�{$val['contact']}<br />":"��ϵ�ˣ���ҵ���ò����⹫��<br />";
			$telephone=$val['telephone_show']=='1'?"��ϵ�绰��{$val['telephone']}<br />":"��ϵ�绰����ҵ���ò����⹫��<br />";
			$address=$val['address_show']=='1'?"��ϵ��ַ��{$val['address']}<br />":"��ϵ��ַ����ҵ���ò����⹫��<br />";
			$html='<div class="title"><h2>��ϵ��ʽ</h2></div><div class="txt">';
			$html.=$contact.$telephone.$address;
			$html.='</div><div class="telimg"><a href="wtai://wp/mc;'.$val["telephone"].'"><img src="images/23.jpg"  border="0"/></a></div>';
			}
			exit($html);
		}
		else
		{		
		exit($html);
		}
	}	
}
//������ϵ��ʽ
elseif($act == 'resume_contact')
{   
	$id=intval($_GET['id']);
		$show=false;
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
			exit("");
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
				$html='<div class="title"><h2>��ϵ��ʽ</h2></div><div class="txt">';
				$html="<div align=\"center\"><img src=\"{$_CFG['site_template']}images/44.gif\"  border=\"0\" id=\"download\"/></div>";
				$html.="<div align=\"center\"><span class=\"add_resume_pool\">[��ӵ��˲ſ�]</span><br/><br/></div>";
				$html.='</div><div class="telimg"></div>';
				}
			}
			else
			{
			$show=false;
			$html='<div class="title"><h2>��ϵ��ʽ</h2></div><div class="txt">';
			$html.='��ҵ��Ա��<a href="wap_login.php">[��¼]</a>��鿴��ϵ��ʽ<br />û���ʺţ�<a href="wap_user_reg.php">[���ע��]</a>';
			$html.='</div><div class="telimg"></div>';
			}
		}
		if ($show)
		{
			$tb1=$db->getone("select fullname,telephone,email,qq,address,website from ".table('resume')." WHERE  id='{$id}'  LIMIT 1");
			$tb2=$db->getone("select fullname,telephone,email,qq,address,website from ".table('resume_tmp')." WHERE  id='{$id}'  LIMIT 1");		
			$val=!empty($tb1)?$tb1:$tb2;
			if ($_CFG['contact_img_resume']=='2')
			{
			$token=md5($val['fullname'].$id.$val['telephone']);
			$html='<div class="title"><h2>��ϵ��ʽ</h2></div><div class="txt">';
			$html.="�� ϵ �ˣ�<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/><br />";
			$html.="��ϵ�绰��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/><br />";
			$html.="��ϵ��ַ��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=5&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/><br />";
			$html.="<div align=\"center\"><br/><img src=\"{$_CFG['site_template']}images/64.gif\"  border=\"0\" id=\"invited\"/></div>";
			$html.="<div align=\"center\"><span class=\"add_resume_pool\">[��ӵ��˲ſ�]</span><br/><br/></div>";
			$html.='</div><div class="telimg"><a href="wtai://wp/mc;'.$val["telephone"].'"><img src="images/23.jpg"  border="0"/></a></div>';
			}
			else
			{
			$html='<div class="title"><h2>��ϵ��ʽ</h2></div><div class="txt">';
			$html.="�� ϵ �ˣ�".$val['fullname']."<br />";
			$html.="��ϵ�绰��".$val['telephone']."<br />";
			$html.="��ϵ��ַ��".$val['address']."<br />";
			$html.="<div align=\"center\"><br/><img src=\"{$_CFG['site_template']}images/64.gif\"  border=\"0\" id=\"invited\"/></div>";
			$html.="<div align=\"center\"><span class=\"add_resume_pool\">[��ӵ��˲ſ�]</span><br/><br/></div>";
			$html.='</div><div class="telimg"><a href="wtai://wp/mc;'.$val["telephone"].'"><img src="images/23.jpg"  border="0"/></a></div>';
			}
			exit($html);
		exit($html);
		}
		else
		{		
		exit($html);
		}
}
// ajax ��ȡ��������
elseif($act=="ajax_interview_list")
{
	$interviewhtml="";
	//�õ�ҳ�洫��������ʾ����
	$rows=intval($_GET['rows']);
	//�õ�ҳ�洫������  ���һ����¼��didֵ
	$offset=intval($_GET['offset']); 
	$interviewarray=$db->getall("select * from ".table('company_interview')." where  resume_uid=$_SESSION[uid] order by interview_addtime desc  LIMIT {$offset},{$rows}");
	if (!empty($interviewarray) && $offset<=100)
	{
		foreach($interviewarray as $list)
		{
			$interviewhtml.='<div class="interview_list_content" did="'.$list['did'].'"><div class="list_centent_left"><h4>'.$list['company_name'].'</h4><div class="l_bottom"><div class="l_bottom_l">����������&nbsp;&nbsp;"'.$list['jobs_name'].'"</div><div class="l_bottom_r">'.date('Y-m-d',$list['addtime']).'</div><div class="clear"></div></div></div><div class="list_centent_right"><img src="../images/36.gif" alt="" /></div><div class="clear"></div></div>';
		}
		exit($interviewhtml);
	}
	else
	{
		exit('-1');
	}
}
// ajax ��ȡ�ղ�ְλ
elseif($act=="ajax_favorites_list")
{
	$favoriteshtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']);
	$favoritesarry=$db->getall("select f.*,j.companyname,j.wage_cn from ".table("personal_favorites")." as f left join ".table("jobs")." as j on f.jobs_id=j.id where f.personal_uid=$_SESSION[uid] order by f.favoritesa_ddtime desc limit $offset,$rows");
	if (!empty($favoritesarry) && $offset<=100)
	{
		foreach($favoritesarry as $list)
		{
			$favoriteshtml.='<div class="list_centent" url="../wap-jobs-show.php?id='.$list['jobs_id'].'"><div class="jobs_t1"><span><a >'.$list['jobs_name'].'</a></span><br />'.$list['companyname'].'</div><div class="jobs_t2">'.date('Y-m-d',$list['addtime']).'<br />'.$list['wage_cn'].'</div><div class="jobs_t3"><img src="../images/14.jpg" alt="" /></div><div class="clear"></div></div>';
		}
		exit($favoriteshtml);
	}
	else
	{
		exit('-1');
	}
}
// ajax ��ȡ ����ְλ
elseif($act=="ajax_apply_list")
{
	$favoriteshtml="";
	$rows=intval($_GET['rows']);
	$offset=intval($_GET['offset']);
	$favoritesarry=$db->getall("select a.*,j.wage_cn from ".table("personal_jobs_apply")." as a left join ".table("jobs")." as j on a.jobs_id=j.id where a.personal_uid=$_SESSION[uid]  order by a.apply_addtime desc limit $offset,$rows");
	if (!empty($favoritesarry) && $offset<=100)
	{
		foreach($favoritesarry as $list)
		{
			$favoriteshtml.='<div class="list_centent" url="../wap-jobs-show.php?id='.$list['jobs_id'].'"><div class="jobs_t1"><span><a >'.$list['jobs_name'].'</a></span><br />'.$list['company_name'].'</div><div class="jobs_t2">'.date('Y-m-d',$list['apply_addtime']).'<br />'.$list['wage_cn'].'</div><div class="jobs_t3"><img src="../images/14.jpg" alt="" /></div><div class="clear"></div></div>';
		}
		exit($favoriteshtml);
	}
	else
	{
		exit('-1');
	}
}
// ��������
elseif ($act == 'invited_add')
{

	$smarty->cache = false;
	$resume=resume_one($_POST["resume_id"]);
	$jobs=jobs_one($_POST["jobs_id"]);
	if($_SESSION['utype']!=1){
		exit("��ҵ��Ա���¼����������");
	}

	if (check_interview($_POST["resume_id"],$_POST["jobs_id"],$_SESSION['uid']))
	{
	exit("repeat");
	}
	$addarr['resume_id']=$resume['id'];
	$addarr['resume_addtime']=$resume['addtime'];
	if ($resume['display_name']=="2")
	{
	$addarr['resume_name']="N".str_pad($resume['id'],7,"0",STR_PAD_LEFT);	
	}
	elseif ($resume['display_name']=="3")
	{
	$addarr['resume_name']=cut_str($resume['fullname'],1,0,"**");
	}
	else
	{
	$addarr['resume_name']=$resume['fullname'];
	}
	$addarr['resume_uid']=$resume['uid'];
	$addarr['company_id']=$jobs['company_id'];
	$addarr['company_addtime']=$jobs['company_addtime'];
	$addarr['company_name']=$jobs['companyname'];
	$addarr['company_uid']=$_SESSION['uid'];
	$addarr['jobs_id']=$jobs['id'];
	$addarr['jobs_name']=$jobs['jobs_name'];
	$addarr['jobs_addtime']=$jobs['addtime'];	

	$addarr['personal_look']= 1;
	$addarr['interview_addtime']=time();
	$user=get_user_info($resume['uid']);
	$resume_user=get_user_info($resume['uid']);
	if ($_CFG['operation_mode']=="2")
	{
		inserttable(table('company_interview'),$addarr);
		if ($resume['talent']=='2')
		{
			action_user_setmeal($_SESSION['uid'],"interview_senior");
			$setmeal=get_user_setmeal($_SESSION['uid']);
			write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"������ {$resume_user['username']} ���ԣ�����������߼��˲� {$setmeal['interview_senior']} ��",2,1007,"����߼��˲�����","1","{$setmeal['interview_senior']}");
			write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"������ {$resume_user['username']} ����");
		}
		else
		{				 
			action_user_setmeal($_SESSION['uid'],"interview_ordinary");
			$setmeal=get_user_setmeal($_SESSION['uid']);
			write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"������ {$resume_user['username']} ���ԣ�������������ͨ�˲� {$setmeal['interview_ordinary']} ��",2,1006,"������ͨ�˲�����","1","{$setmeal['interview_ordinary']}");
			write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"������ {$resume_user['username']} ����");				
		}			
	}		 
	elseif($_CFG['operation_mode']=="1")
	{
		$mypoints=get_user_points($_SESSION['uid']);
		$points_rule=get_cache('points_rule');
		$points=$resume['talent']=='2'?$points_rule['interview_invite_advanced']['value']:$points_rule['interview_invite']['value'];
		$ptype=$resumeshow['talent']=='2'?$points_rule['interview_invite_advanced']['type']:$points_rule['interview_invite']['type'];
		if  ($mypoints<$points)
		{
			exit("���Ļ��ֲ���");
		}
		inserttable(table('company_interview'),$addarr);
		if ($points>0)
		{
			report_deal($_SESSION['uid'],$ptype,$points);
			$user_points=get_user_points($_SESSION['uid']);
			$operator=$ptype=="1"?"+":"-";
			if($resume['talent']=='2'){
				write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"���� {$resume_user['username']} ����({$operator}{$points}),(ʣ��:{$user_points})",1,1007,"����߼��˲�����","{$operator}{$points}","{$user_points}");
			}else{
				write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"���� {$resume_user['username']} ����({$operator}{$points}),(ʣ��:{$user_points})",1,1006,"������ͨ�˲�����","{$operator}{$points}","{$user_points}");
			}
			write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"���� {$resume_user['username']} ����");
		}		
	}
	$mailconfig=get_cache('mailconfig');
	$sms=get_cache('sms_config');
	if ($mailconfig['set_invite']=="1" && $resume['email_notify']=='1' && $resume_user['email_audit']=="1")
	{
		dfopen("{$_CFG['website_dir']}plus/asyn_mail.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_invite&companyname={$jobs['companyname']}&email={$resume_user['email']}");				
	}
	//sms
	if ($sms['open']=="1"  && $sms['set_invite']=="1"  && $resume_user['mobile_audit']=="1")
	{
		dfopen("{$_CFG['website_dir']}plus/asyn_sms.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_invite&companyname={$jobs['companyname']}&mobile={$resume_user['mobile']}");		
	}
	//վ����
	if($pms_notice=='1'){
		$user=$db->getone("select username from ".table('members')." where uid ={$resume['uid']} limit 1");
		$jobs_url=url_rewrite('QS_jobsshow',array('id'=>$jobs['id']),true,$jobs['subsite_id']);
		$company_url=url_rewrite('QS_companyshow',array('id'=>$jobs['company_id']),false);
		$message=$jobs['companyname']."�������μӹ�˾���ԣ�����ְλ��<a href=\"{$jobs_url}\" target=\"_blank\"> {$jobs['jobs_name']} </a>��<a href=\"{$company_url}\" target=\"_blank\">����鿴��˾����</a>";
		write_pmsnotice($resume['uid'],$user['username'],$message);
	}
	exit("ok");
	

}
//��ȡְλ���߼���������UID
function get_uid($aid,$type='jobs')
{
    global $db;
	if($type=='resume')
	{
	    $table=table('resume');
	}
	else
	{
	    $table=table('jobs');
	}
	$row=$db->getone("Select uid From {$table} Where id={$aid} LIMIT 1");
	return $row['uid'];
}
?>