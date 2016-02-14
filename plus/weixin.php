<?php
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/plus.common.inc.php');
define("TOKEN", $_CFG['weixin_apptoken']);
define("APPID", $_CFG['weixin_appid']);
define("APPSECRET", $_CFG['weixin_appsecret']);
define("ROOT",$_CFG['site_domain']);
define("FIRST_PIC",$_CFG['weixin_first_pic']);
define("DEFAULT_PIC",$_CFG['weixin_default_pic']);
define("SITE_NAME",$_CFG['site_name']);
define("WAP_DOMAIN",rtrim($_CFG['wap_domain'],"/")."/");
define("APIOPEN", $_CFG['weixin_apiopen']);
define("PHP_VERSION",PHP_VERSION);
define("EncodingAESKey",$_CFG['weixin_encoding_aes_key']);
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/weixinencodingaes/wxBizMsgCrypt.php');

class wechatCallbackapiTest extends mysql
{
	public $timestamp;
	public $nonce;
	public $msg_signature;
	public $encrypt_type;
	public $content;

	//��֤ǩ��
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature())
		{
        	exit($echoStr);
        }
    }
    //��Ӧ��Ϣ
    public function responseMsg()
    {
		if(!$this->checkSignature())
		{
        	exit();
        };
        $this->timestamp  = $_GET['timestamp'];
		$this->nonce = $_GET["nonce"];
		$this->msg_signature  = $_GET['msg_signature'];
		$this->encrypt_type = (isset($_GET['encrypt_type']) && ($_GET['encrypt_type'] == 'aes')) ? "aes" : "raw";

		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if (!empty($postStr)){
			//����
			if ($this->encrypt_type == 'aes'){
				$pc = new WXBizMsgCrypt(TOKEN, EncodingAESKey, APPID);                
				$decryptMsg = "";  //���ܺ������
				$errCode = $pc->DecryptMsg($this->msg_signature, $this->timestamp, $this->nonce, $postStr, $decryptMsg);
				$postStr = $decryptMsg;
			}
			if($this->check_php_version("5.2.11")){
				libxml_disable_entity_loader(true);
			}
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $rxType = trim($postObj->MsgType);
             
            //��Ϣ���ͷ���
            switch ($rxType)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
                default:
                    $result = "unknown msg type: ".$rxType;
                    break;
            }
            //����
			if ($this->encrypt_type == 'aes'){
				$encryptMsg = ''; //���ܺ������
				$errCode = $pc->encryptMsg($result, $this->timeStamp, $this->nonce, $encryptMsg);
				$result = $encryptMsg;
			}
            echo $result;
        }else {
            echo "";
            exit;
        }
	}	
	//�����¼���Ϣ
    private function receiveEvent($object)
    {
        switch ($object->Event)
        {
            case "subscribe":
                $this->content = "�ظ�j���ؽ�����Ƹ���ظ�n����������Ƹ�������Գ�������ְλ�����硰��ơ���ϵͳ���᷵����Ҫ�ҵ���Ϣ������Ŭ�����������Ի��ķ���ƽ̨��лл��ע��";
                break;
            case "SCAN":
                $this->actionScan($object);
                break;
            case "CLICK":
            	$this->check_weixin_open($object);
                switch ($object->EventKey)
                {
                    case "binding"://��
                    	$this->clickBinding($object);
						break;
					case "apply_jobs"://����ְλ�б�
						$this->clickApplyJobs($object);
						break;
					case "resume_refresh"://ˢ�¼���
						$this->clickResumeRefresh($object);
						break;
					case "interview"://���������б�
						$this->clickInterview($object);
						break;
                    default://������ʽ����
                        $this->clickSearch($object);
                        break;
                }
                break;
                case "unsubscribe":
            		$fromUsername = addslashes($object->FromUserName);
                $sql = "update ".table('members')." set weixin_openid='' where weixin_openid='".$fromUsername."'";
		  $this->query($sql);
                break;
            default:
                $this->content = "�ظ�j���ؽ�����Ƹ���ظ�n����������Ƹ�������Գ�������ְλ�����硰��ơ���ϵͳ���᷵����Ҫ�ҵ���Ϣ������Ŭ�����������Ի��ķ���ƽ̨��лл��ע��";
                break;
        }
        if(is_array($this->content)){
            if (isset($this->content[0])){
                $result = $this->transmitNews($object, $this->content);
            }
        }else{
            $result = $this->transmitText($object, $this->content);
        }

        return $result;
    }
    //�����ı���Ϣ
    private function receiveText($object)
    {
    	$this->check_weixin_open($object);
        $keyword = trim($object->Content);
        $keyword = utf8_to_gbk($keyword);
		$keyword = addslashes($keyword);
       
        //�Զ��ظ�ģʽ
        $this->enterSearch($object,$keyword);
        if(is_array($this->content)){
            if (isset($this->content[0]['PicUrl'])){
                $result = $this->transmitNews($object, $this->content);
            }
        }else{
            $result = $this->transmitText($object, $this->content);
        }
        return $result;
    }

    //�ظ��ı���Ϣ
    private function transmitText($object, $content)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
		$content = gbk_to_utf8($content);
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //�ظ�ͼ����Ϣ
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($newsArray as $item){
        	$item = array_map("gbk_to_utf8", $item);
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }
    //��֤ǩ��
	private function checkSignature()
	{
	    $signature = $_GET["signature"];
	    $timestamp = $_GET["timestamp"];
	    $nonce = $_GET["nonce"];        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
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
		$fromUsername = addslashes($fromUsername);
		$usinfo_obj = $this->query("select * from ".table('members')." where weixin_openid='".$fromUsername."' limit 1");
		while($row = $this->fetch_array($usinfo_obj)){
			$usinfo = $row;
		}
		return $usinfo;
	}
	private function check_php_version($version) {
		 $php_version = explode('-',phpversion());
		 // strnatcasecmp( $php_version[0], $version ) 0��ʾ���ڣ�1��ʾ���ڣ�-1��ʾС��
		 $is_pass = strnatcasecmp($php_version[0],$version)>=0?true:false;
		 return $is_pass;
	}
    //�����վ΢�Žӿ��Ƿ���
	private function check_weixin_open($object){
		if(APIOPEN=='0')
		{
			$this->content="��վ΢�Žӿ��Ѿ��ر�";
			$this->transmitText($object,$this->content);
		}
	}
    //���¼�
	private function clickBinding($object){
		$usinfo = $this->get_user_info($object->FromUserName);
		if(!empty($usinfo)){
			$this->content="���Ѿ��󶨹���!";
		}else{
			$this->content="<a href='".WAP_DOMAIN."wap-binding.php?from=".$object->FromUserName."'>���Ȱ��ʺ�</a>";
		}
	}
    //��ȡ������ְλ�б�
	private function clickApplyJobs($object){
		$usinfo = $this->get_user_info($object->FromUserName);
		if(!empty($usinfo)){
			$uid = $usinfo['uid'];
			$apply_obj = $this->query("select * from ".table('personal_jobs_apply')." where personal_uid=".$uid);
			while($row = $this->fetch_array($apply_obj)){
				$jobs_url = WAP_DOMAIN."wap-jobs-show.php?id=".$row['jobs_id']."&from=".$object->FromUserName;
				$look = intval($row['personal_look'])==1?"δ�鿴":"�Ѳ鿴";
				$this->content.="��".date('Y-m-d',$row['apply_addtime'])."����".$look."��\n<a href='".$jobs_url."'>".$row['jobs_name']."</a>\n".$row['company_name']."\n--------------------------\n";
			}
			if(empty($this->content)){
				$this->content = "û���ҵ���Ӧ����Ϣ!";
			}
		}else{
			$this->content="<a href='".WAP_DOMAIN."wap-binding.php?from=".$object->FromUserName."'>���Ȱ��ʺ�</a>";
		}
	}
    //ˢ�¼���
	private function clickResumeRefresh($object){
		$usinfo = $this->get_user_info($object->FromUserName);
		if(!empty($usinfo)){
			$uid = $usinfo['uid'];
			$time = time();
			$this->query("update ".table('resume')." set refreshtime=".$time." where uid=".$uid);
			$this->query("update ".table('resume_search_key')." set refreshtime=".$time." where uid=".$uid);
			$this->query("update ".table('resume_search_rtime')." set refreshtime=".$time." where uid=".$uid);
			$this->content = "ˢ�³ɹ�!";
		}else{
			$this->content="<a href='".WAP_DOMAIN."wap-binding.php?from=".$object->FromUserName."'>���Ȱ��ʺ�</a>";
		}
	}
    //��ȡ���������б�
	private function clickInterview($object){
		$usinfo = $this->get_user_info($object->FromUserName);
		if(!empty($usinfo)){
			$uid = $usinfo['uid'];
			$interview_obj = $this->query("select * from ".table('company_interview')." where resume_uid=".$uid);
			while($row = $this->fetch_array($interview_obj)){
				$jobs_url = WAP_DOMAIN."wap-jobs-show.php?id=".$row['jobs_id']."&from=".$object->FromUserName;
				$company_url = WAP_DOMAIN."wap-company-show.php?id=".$row['company_id']."&from=".$object->FromUserName;
				$this->content.="��".date('Y-m-d',$row['interview_addtime'])."��\n<a href='".$company_url."'>".$row['company_name']."</a>����������<a href='".$jobs_url."'>".$row['jobs_name']."</a>\n--------------------------\n";
			}
			if(empty($this->content)){
				$this->content = "û���ҵ���Ӧ����Ϣ!";
			}
		}else{
			$this->content="<a href='".WAP_DOMAIN."wap-binding.php?from=".$object->FromUserName."'>���Ȱ��ʺ�</a>";
		}
	}
    //���������������
	private function clickSearch($object){
		$default_pic=ROOT."/data/images/".DEFAULT_PIC;
		$first_pic=ROOT."/data/images/".FIRST_PIC;
		switch ($object->EventKey)
		{
			case "newjobs"://����ְλ
				$type=1;
				$jobstable=table('jobs_search_rtime');	
				break;
			case "emergencyjobs"://������Ƹ
				$type=1;
				$jobstable=table('jobs_search_rtime');
				$wheresql=" where `emergency`=1 ";	
				break;
			case "recommendjobs"://�Ƽ�ְλ
				$type=1;
				$jobstable=table('jobs_search_rtime');
				$wheresql=" where `recommend`=1 ";	
				break;
			case "resume"://���¼���
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
			$i=1;
			while($row = $this->fetch_array($result))
			{
				if($i==1){
                    $picurl=$first_pic;	
				}else{
					$picurl=$default_pic;	
				}
				$i++;
				if($type==1){
					$jobs_name=$row['jobs_name'];				
				    $companyname=$row['companyname'];
				    $title=$jobs_name."--".$companyname;
				    $url=WAP_DOMAIN."wap-jobs-show.php?id=".$row['id']."&from=".$object->FromUserName;
				}elseif($type==2){
					if($row['display_name']=="2")
					{
						$fullname="N".str_pad($row['id'],7,"0",STR_PAD_LEFT)."(".$row['sex_cn'].")";
					}
					elseif($row['display_name']=="3")
					{
						if($row['sex']==1){
						$fullname=cut_str($row['fullname'],1,0,"����");
						}elseif($row['sex'] == 2){
						$fullname=cut_str($row['fullname'],1,0,"Ůʿ");
						}
					}
					else
					{
						$fullname=$row['fullname']."(".$row['sex_cn'].")";	
					}			
				    $intention_jobs=$row['intention_jobs'];
				    $title=$fullname."--".$intention_jobs;
				    $url=WAP_DOMAIN."wap-resume-show.php?id=".$row['id']."&from=".$object->FromUserName;
				}
				$this->content[] = array("Title"=>$title, "Description"=>$con, "PicUrl"=>$picurl, "Url" =>$url);	
			}
		}
		if(empty($this->content))
		{
			$this->content="û���ҵ���Ӧ����Ϣ";
		}
	}
    //ɨ���¼�
	private function actionScan($object){
		$event_key = $object->EventKey;
		if($event_key<=10000000)
		{
			$usinfo = $this->get_user_info($object->FromUserName);
			if(!empty($usinfo)){
				$this->content = "<a href='".WAP_DOMAIN."wap_login.php?act=weixin_login&openid=".$object->FromUserName."&uid=".$usinfo['uid']."&event_key=".$event_key."'>���������¼".SITE_NAME."��ҳ</a>";
			}else{
				$this->content="<a href='".WAP_DOMAIN."wap-binding.php?from=".$object->FromUserName."'>���Ȱ��ʺ�</a>";
			}
		}
		elseif($event_key>10000000 && $event_key<=20000000)
		{
			$this->content = "��ѡ���Աע������.<a href='".WAP_DOMAIN."wap_login.php?act=weixin_reg&openid=".$object->FromUserName."&event_key=".$event_key."&utype=1'>��ҵ��Ա</a>��<a href='".WAP_DOMAIN."wap_login.php?act=weixin_reg&openid=".$object->FromUserName."&event_key=".$event_key."&utype=2'>���˻�Ա</a>";
		}
		elseif($event_key>20000000 && $event_key<=30000000)
		{
			$usinfo = $this->get_user_info($object->FromUserName);
			if($usinfo){
				$this->content="���ã������˺�(".$usinfo['username'].")�ѳɹ����ð�ȫ��¼��<a href='".WAP_DOMAIN."wap-binding.php?act=change_binding&from=".$object->FromUserName."'>�л���</a>";
			}else{
				$fp = @fopen(QISHI_ROOT_PATH . 'data/weixin/'.($event_key%10).'/'.$event_key.'.txt', 'wb+');
				@fwrite($fp, $object->FromUserName);
				@fclose($fp);
				$this->content="�󶨳ɹ�";
			}
		}
	}
	
    //����ؼ�������ְλ
	private function enterSearch($object,$keyword){
		$default_pic=ROOT."/data/images/".DEFAULT_PIC;
		$first_pic=ROOT."/data/images/".FIRST_PIC;
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
			$i=1;
			while($row = $this->fetch_array($result))
			{
				$jobs_name=$row['jobs_name'];				
			    $companyname=$row['companyname'];
			    $title=$jobs_name."--".$companyname;
			    $url=WAP_DOMAIN."wap-jobs-show.php?id=".$row['id']."&from=".$object->FromUserName;
			    if($i==1){
                    $picurl=$first_pic;	
				}else{
					$picurl=$default_pic;	
				}
				$i++;
				$this->content[] = array("Title"=>$title, "Description"=>$con, "PicUrl"=>$picurl, "Url" =>$url);	
			}
		}
		if(empty($this->content))
		{
			$this->content="û���ҵ������ؼ��� {$keyword} ����Ϣ�����������ؼ���";
		}
	}
}

$wechatObj = new wechatCallbackapiTest($dbhost,$dbuser,$dbpass,$dbname);
if (!isset($_REQUEST['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}		
?>