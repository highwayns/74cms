<?php
 /*
 * 74cms �ٱ�
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'app';
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if((empty($_SESSION['uid']) || empty($_SESSION['username']) || empty($_SESSION['utype'])) &&  $_COOKIE['QS']['username'] && $_COOKIE['QS']['password'] && $_COOKIE['QS']['uid'])
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	if(check_cookie($_COOKIE['QS']['uid'],$_COOKIE['QS']['username'],$_COOKIE['QS']['password']))
	{
	update_user_info($_COOKIE['QS']['uid'],false,false);
	header("Location:".get_member_url($_SESSION['utype']));
	}
	else
	{
	unset($_SESSION['uid'],$_SESSION['username'],$_SESSION['utype'],$_SESSION['uqqid'],$_SESSION['activate_username'],$_SESSION['activate_email'],$_SESSION["openid"]);
	setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[username]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[password]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	}
}
if ($_SESSION['uid']=='' || $_SESSION['username']=='')
{
	$captcha=get_cache('captcha');
	$smarty->assign('verify_userlogin',$captcha['verify_userlogin']);
	$smarty->display('plus/ajax_login.htm');
	exit();
}
if ($_SESSION['utype']!='2')
{
	exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		    <tr>
				<td width="20" align="right"></td>
				<td>
					�����Ǹ��˻�Ա�ſ��Ծٱ�ְλ��Ϣ��
				</td>
		    </tr>
		</table>');
}
require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
$user=get_user_info($_SESSION['uid']);
if ($user['status']=="2") 
{
	exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
		    <tr>
				<td width="20" align="right"></td>
				<td>
					�����˺Ŵ�����ͣ״̬������ϵ����Ա��Ϊ��������в�����
				</td>
		    </tr>
		</table>');
}
if ($act=="report")
{		
		$id=isset($_GET['jobs_id'])?$_GET['jobs_id']:exit("id ��ʧ");
		$jobs=app_get_jobs($id);
		if (empty($jobs))
		{
			exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
			    <tr>
					<td width="20" align="right"></td>
					<td>
						�ٱ���Ϣʧ�ܣ�
					</td>
			    </tr>
			</table>');
		}
		if (check_jobs_report($_SESSION['uid'],intval($_GET['jobs_id'])))
		{
			exit('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall">
			    <tr>
					<td width="20" align="right"></td>
					<td>
						���Ѿ��ٱ�����ְλ��
					</td>
			    </tr>
			</table>');
		}
?>
<script type="text/javascript">
$(".but80").hover(function(){$(this).addClass("but80_hover")},function(){$(this).removeClass("but80_hover")});
//���������������

//��֤
$("#ajax_report").click(function() {
	var content=$("#content").val();
	if (content=="")
	{
	alert("����������");
	}
	else
	{
		$("#report").hide();
		$("#waiting").show();
		
		$.post("<?php echo $_CFG['site_dir'] ?>user/user_report.php", { "jobs_id": $("#jobs_id").val(),"jobs_name": $("#jobs_name").val(),"content": $("#content").val(),"jobs_addtime":$("#jobs_addtime").val(),"act":"app_save"},

	 	function (data,textStatus)
	 	 {
			if (data=="ok")
			{
				$("#report").hide();
				$("#waiting").hide();
				$("#app_ok").show();
					$("#app_ok .closed").click(function(){
					});
			}
			else
			{
				$("#report").show();
				$("#waiting").hide();
				$("#app_ok").hide();
				alert("�ٱ�ʧ�ܣ�"+data);
			}
	 	 });
	}
});
function DialogClose()
{
	$("#FloatBg").hide();
	$("#FloatBox").hide();
}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall" id="report">
	<input type="hidden" id="jobs_id" value="<?php echo intval($_GET['jobs_id']);?>">
	<input type="hidden" id="jobs_name" value="<?php echo trim($_GET['jobs_name']);?>">
	<input type="hidden" id="jobs_addtime" value="<?php echo trim($_GET['jobs_addtime']);?>">
    <tr>
		<td width="120" align="right">Ͷ�ߵ�ְλ��</td>
		<td>
			<?php echo trim($_GET['jobs_name']);?>
		</td>
    </tr>
    <tr>
		<td align="right">���������</td>
		<td>
			<textarea name="content" id="content"  style="width:300px; height:60px; line-height:180%; font-size:12px;"></textarea>
		</td>
    </tr>
    <tr>
		<td></td>
		<td>
			<input type="button" name="Submit"  id="ajax_report" class="but130lan" value="�ύ" />
		</td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="5" cellpadding="0" id="waiting"  style="display:none">
  <tr>
    <td align="center" height="60"><img src="<?php echo  $_CFG['site_template']?>images/30.gif"  border="0"/></td>
  </tr>
  <tr>
    <td align="center" >���Ժ�...</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableall" id="app_ok" style="display:none">
    <tr>
		<td width="140" align="right"><img height="100" src="<?php echo  $_CFG['site_template']?>images/14.gif" /></td>
		<td>
			<strong style="font-size:14px ; color:#0066CC;margin-left:20px">�ٱ��ɹ�������Ա�����洦��!</strong>
		</td>
    </tr>
</table>
<?php
}

elseif ($act=="app_save")
{
	$setsqlarr['content']=trim($_POST['content'])?trim($_POST['content']):exit("������");
	$setsqlarr['jobs_id']=$_POST['jobs_id']?intval($_POST['jobs_id']):exit("������");
	$setsqlarr['jobs_name']=trim($_POST['jobs_name'])?trim($_POST['jobs_name']):exit("������");
	$setsqlarr['jobs_addtime']=intval($_POST['jobs_addtime']);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['addtime']=time();
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$setsqlarr['content']=utf8_to_gbk($setsqlarr['content']);
	$setsqlarr['jobs_name']=utf8_to_gbk($setsqlarr['jobs_name']);
	}
	$jobsarr=app_get_jobs($setsqlarr['jobs_id']);
	if (empty($jobsarr))
	{
	exit("ְλ��ʧ");
	}
	else
	{
		$insert_id = inserttable(table('report'),$setsqlarr,1);
	}
	if($insert_id)
	 {
	 exit("ok");
	 }
}

?>
