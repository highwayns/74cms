<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/plus.common.inc.php');
define("TOKEN", $_CFG['weixin_apptoken']);
define("APPID", $_CFG['weixin_appid']);
define("APPSECRET", $_CFG['weixin_appsecret']);
define("ROOT",$_CFG['site_domain']);
define("FIRST_PIC",$_CFG['weixin_first_pic']);
define("DEFAULT_PIC",$_CFG['weixin_default_pic']);
define("WAP_DOMAIN",rtrim($_CFG['wap_domain'],"/")."/");
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
class wechatCallbackapiTest extends mysql
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature())
		{
        	exit($echoStr);
        }
    }
    public function responseMsg()
    {
		// if(!$this->checkSignature())
		// {
  //       	exit();
  //       }
		$postStr = addslashes($GLOBALS["HTTP_RAW_POST_DATA"]);
		if (!empty($postStr))
		{
			// libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
			$keyword = utf8_to_gbk($keyword);
			$keyword = addslashes($keyword);
            $time = time();
			$event = trim($postObj->Event);
			
			if ($event === "subscribe")
			{
				$word= "�ظ�j���ؽ�����Ƹ���ظ�n����������Ƹ�������Գ�������ְλ�����硰��ơ���ϵͳ���᷵����Ҫ�ҵ���Ϣ������Ŭ�����������Ի��ķ���ƽ̨��лл��ע��";
				$this->exit_word_message($word,$fromUsername,$toUsername,$time);			
			}	
			$default_pic=ROOT."/data/images/".DEFAULT_PIC;
			$first_pic=ROOT."/data/images/".FIRST_PIC;

			if($event === "CLICK"){
				if($_CFG['weixin_apiopen']=='0')
				{
					$word="��վ΢�Žӿ��Ѿ��ر�";
					$this->exit_word_message($word,$fromUsername,$toUsername,$time);
				}
				if($postObj->EventKey=="binding"){
					$usinfo = $this->get_user_info($fromUsername);
					if(!empty($usinfo)){
						$word="���Ѿ��󶨹���!";
					}else{
						$word="�����������˺�����.
����:����/123456";
					}
					$this->exit_word_message($word,$fromUsername,$toUsername,$time);
				}
				elseif($postObj->EventKey=="apply_jobs"){
					$usinfo = $this->get_user_info($fromUsername);
					if(!empty($usinfo)){
						$word="";
						$apply_info = array();
						$uid = $usinfo['uid'];
						$apply_obj = $this->query("select * from ".table('personal_jobs_apply')." where personal_uid=".$uid);
						while($row = $this->fetch_array($apply_obj)){
							$jobs_url = WAP_DOMAIN."wap-jobs-show.php?id=".$row['jobs_id'];
							$look = intval($row['personal_look'])==1?"δ�鿴":"�Ѳ鿴";
							$word.="��".date('Y-m-d',$row['apply_addtime'])."����".$look."��\n<a href='".$jobs_url."'>".$row['jobs_name']."</a>\n".$row['company_name']."\n--------------------------\n";
						}
						if(empty($word)){
							$word = "û���ҵ���Ӧ����Ϣ!";
						}
					}else{
						$word = "���Ȱ��˺�!";
					}
					$this->exit_word_message($word,$fromUsername,$toUsername,$time);
				}
				elseif($postObj->EventKey=="resume_refresh"){
					$usinfo = $this->get_user_info($fromUsername);
					if(!empty($usinfo)){
						$uid = $usinfo['uid'];
						$time = time();
						$this->query("update ".table('resume')." set refreshtime=".$time." where uid=".$uid);
						$this->query("update ".table('resume_search_key')." set refreshtime=".$time." where uid=".$uid);
						$this->query("update ".table('resume_search_rtime')." set refreshtime=".$time." where uid=".$uid);
						$word = "ˢ�³ɹ�!";
					}else{
						$word = "���Ȱ��˺�!";
					}
					$this->exit_word_message($word,$fromUsername,$toUsername,$time);
				}
				elseif($postObj->EventKey=="interview"){
					$usinfo = $this->get_user_info($fromUsername);
					if(!empty($usinfo)){
						$word="";
						$interview_info = array();
						$uid = $usinfo['uid'];
						$interview_obj = $this->query("select * from ".table('company_interview')." where resume_uid=".$uid);
						while($row = $this->fetch_array($interview_obj)){
							$jobs_url = WAP_DOMAIN."wap-jobs-show.php?id=".$row['jobs_id'];
							$company_url = WAP_DOMAIN."wap-company-show.php?id=".$row['company_id'];
							$word.="��".date('Y-m-d',$row['interview_addtime'])."��\n<a href='".$company_url."'>".$row['company_name']."</a>����������<a href='".$jobs_url."'>".$row['jobs_name']."</a>\n--------------------------\n";
						}
						if(empty($word)){
							$word = "û���ҵ���Ӧ����Ϣ!";
						}
					}else{
						$word = "���Ȱ��˺�!";
					}
					$this->exit_word_message($word,$fromUsername,$toUsername,$time);
				}
				else{
					switch ($postObj->EventKey)
					{
						case "newjobs":
							$type=1;
							$jobstable=table('jobs_search_rtime');	
							break;
						case "emergencyjobs":
							$type=1;
							$jobstable=table('jobs_search_rtime');
							$wheresql=" where `emergency`=1 ";	
							break;
						case "recommendjobs":
							$type=1;
							$jobstable=table('jobs_search_rtime');
							$wheresql=" where `recommend`=1 ";	
							break;
						case "resume":
							$type=2;
							$jobstable=table('resume_search_rtime');	
							break;
						default:
							$type=1;
							$jobstable=table('jobs_search_rtime');	
							break;
					}
					$limit=" LIMIT 5";
					$orderbysql=" ORDER BY refreshtime DESC";
					$word='';
					$list = $id = array();
					$idresult = $this->query("SELECT id FROM {$jobstable} ".$wheresql.$orderbysql.$limit);
					while($row = $this->fetch_array($idresult))
					{
					$id[]=$row['id'];
					}
					if (!empty($id))
					{
						$wheresql=" WHERE id IN (".implode(',',$id).") ";
						if($type==1){
							$result = $this->query("SELECT * FROM ".table('jobs').$wheresql.$orderbysql);
						}elseif($type==2){
							$result = $this->query("SELECT * FROM ".table('resume').$wheresql.$orderbysql);
						}
						
						$count=mysql_num_rows($result);
						$i=1;
						$strmiddle="";
						$strbegin="<xml>
								 <ToUserName><![CDATA[".$fromUsername."]]></ToUserName>
								 <FromUserName><![CDATA[".$toUsername."]]></FromUserName>
								 <CreateTime>".$time."</CreateTime>
								 <MsgType><![CDATA[news]]></MsgType>
								 <ArticleCount>".$count."</ArticleCount>
								 <Articles>";	
						while($row = $this->fetch_array($result))
						{
							if($i==1){
		                        $picurl=$first_pic;	
							}else{
								$picurl=$default_pic;	
							}
							$i++;
							if($type==1){
								$jobs_name=gbk_to_utf8($row['jobs_name']);				
							    $companyname=gbk_to_utf8($row['companyname']);
							    $title=$jobs_name."--".$companyname;
							    $url=WAP_DOMAIN."wap-jobs-show.php?id=".$row['id'];
							}elseif($type==2){
								$fullname=gbk_to_utf8($row['fullname'])."(".gbk_to_utf8($row['sex_cn']).")";				
							    $intention_jobs=gbk_to_utf8($row['intention_jobs']);
							    $title=$fullname."--".$intention_jobs;
							    $url=WAP_DOMAIN."wap-resume-show.php?id=".$row['id'];
							}
							$strmiddle.="<item>
										 <Title><![CDATA[".$title."]]></Title> 
										 <Description><![CDATA[".$con."]]></Description>
										 <PicUrl><![CDATA[".$picurl."]]></PicUrl>	
										 <Url><![CDATA[".$url."]]></Url>
										 </item>";
						}
						$strend = "</Articles>
								 <FuncFlag>1</FuncFlag>
								 </xml>";
						$word = $strbegin.$strmiddle.$strend;
					}
					if(empty($word))
					{
						$word="û���ҵ���Ӧ����Ϣ";
						$this->exit_word_message($word,$fromUsername,$toUsername,$time);	
					}
					else
					{
						exit($word);
					}
				}
					
			}
	       	if (!empty($keyword))
			{
			
				if($_CFG['weixin_apiopen']=='0')
				{
						$word="��վ΢�Žӿ��Ѿ��ر�";
						$this->exit_word_message($word,$fromUsername,$toUsername,$time);	
				}
				if(strstr($keyword,"/")){
					global $QS_pwdhash;
					$key_arr = explode("/",$keyword);
					$username = $key_arr[0];
					$password = $key_arr[1];
					$usinfo = array();
					$usinfo_obj = $this->query("select * from ".table('members')." where username = '{$username}' LIMIT 1");
					while($row = $this->fetch_array($usinfo_obj)){
						$usinfo = $row;
					}
					if(!empty($usinfo)){
						$success = false;
						$pwd_hash=$usinfo['pwd_hash'];
						$usname=$usinfo['username'];
						$pwd=md5(md5($password).$pwd_hash.$QS_pwdhash);
						if ($usinfo['password']==$pwd)
						{
							$this->query("update ".table('members')." set `weixin_openid`='".$fromUsername."',bindingtime=".time()." where uid=".$usinfo['uid']);
							$success = true;
						}
						else
						{
							$success = false;
						}
						if($success){
							$word="�󶨳ɹ�!";
						}else{
							$word="�û������������!";
						}
					}else{
						$word="�û������������!";
					}
					
					$this->exit_word_message($word,$fromUsername,$toUsername,$time);	
				}else{
					$limit=" LIMIT 5";
					$orderbysql=" ORDER BY refreshtime DESC";
					if($keyword=="n")
					{
						$jobstable=table('jobs_search_rtime');			 
					}
					else if($keyword=="j")
					{
						$jobstable=table('jobs_search_rtime');
						$wheresql=" where `emergency`=1 ";	
					}
					else
					{
					$jobstable=table('jobs_search_key');
					$wheresql.=" where likekey LIKE '%{$keyword}%' ";
					}
					$word='';
					$list = $id = array();
					$idresult = $this->query("SELECT id FROM {$jobstable} ".$wheresql.$orderbysql.$limit);
					while($row = $this->fetch_array($idresult))
					{
					$id[]=$row['id'];
					}
					if (!empty($id))
					{
						$wheresql=" WHERE id IN (".implode(',',$id).") ";
						$result = $this->query("SELECT * FROM ".table('jobs').$wheresql.$orderbysql);
						$count=mysql_num_rows($result);
						$i=1;
						$strmiddle="";
						$strbegin="<xml>
								 <ToUserName><![CDATA[".$fromUsername."]]></ToUserName>
								 <FromUserName><![CDATA[".$toUsername."]]></FromUserName>
								 <CreateTime>".$time."</CreateTime>
								 <MsgType><![CDATA[news]]></MsgType>
								 <ArticleCount>".$count."</ArticleCount>
								 <Articles>";	
						while($row = $this->fetch_array($result))
						{
							$jobs_name=gbk_to_utf8($row['jobs_name']);				
						    $companyname=gbk_to_utf8($row['companyname']);
						    $title=$jobs_name."--".$companyname;
						    $url=ROOT."/wap/wap-jobs-show.php?id=".$row['id'];

						    if($i==1){
		                        $picurl=$first_pic;	
							}else{
								$picurl=$default_pic;	
							}
							$i++;
							$strmiddle.="<item>
										 <Title><![CDATA[".$title."]]></Title> 
										 <Description><![CDATA[".$con."]]></Description>
										 <PicUrl><![CDATA[".$picurl."]]></PicUrl>	
										 <Url><![CDATA[".$url."]]></Url>
										 </item>";
						}
						$strend = "</Articles>
								 <FuncFlag>1</FuncFlag>
								 </xml>";
						$word = $strbegin.$strmiddle.$strend;
					}
					if(empty($word))
					{
						$word="û���ҵ������ؼ��� {$keyword} ����Ϣ�����������ؼ���";
						$this->exit_word_message($word,$fromUsername,$toUsername,$time);	
					}
					else
					{
						exit($word);
					}	
				}
				 
			}
			else 
			{
			exit("");
			}
    	}
	}	
	private function checkSignature()
	{
	    $signature = $_GET["signature"];
	    $timestamp = $_GET["timestamp"];
	    $nonce = $_GET["nonce"];        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );		
		if($tmpStr == $signature )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	private function get_user_info($fromUsername){
		$usinfo = array();
		$usinfo_obj = $this->query("select * from ".table('members')." where weixin_openid='".$fromUsername."' limit 1");
		while($row = $this->fetch_array($usinfo_obj)){
			$usinfo = $row;
		}
		return $usinfo;
	}
	private function exit_word_message($word,$fromUsername,$toUsername,$time){
		$word = gbk_to_utf8($word);
		$text="<xml>
		<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>
		<FromUserName><![CDATA[".$toUsername."]]></FromUserName>
		<CreateTime>".$time."</CreateTime>
		<MsgType><![CDATA[text]]></MsgType>
		<Content><![CDATA[".$word."]]></Content>
		</xml> ";
		exit($text);
	}
}
//
$wechatObj = new wechatCallbackapiTest($dbhost,$dbuser,$dbpass,$dbname);
if(isset($_REQUEST['echostr']))
			 $wechatObj->valid();
elseif(isset($_REQUEST['signature']))
{			  
	$wechatObj->responseMsg();
}
		
?>