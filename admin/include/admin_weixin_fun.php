<?php
 /*
 * 74cms �������� ����΢�Ų˵� ���ݵ��ú���
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
function get_weixin_menu()
{
	global $db;
	$menu_list = array();
	$sql = "select * from ".table('weixin_menu')." where parentid=0 order BY menu_order desc,id asc";
	$parent_menu = $db->getall($sql);
	foreach($parent_menu as $p){
		$menu_list[$p['id']] = $p;
		$sub_menu = $db->getall("select * from ".table('weixin_menu')." where parentid=".$p['id']);
		foreach ($sub_menu as $key => $value) {
			$menu_list[$p['id']]['child_menu'][] = $value;
		}
	}
	return $menu_list;
}
function get_weixin_menu_one($id)
{
	global $db;
	$sql = "select * from ".table('weixin_menu')." WHERE id=".intval($id)." LIMIT 1";
	return $db->getone($sql);
}
function get_parent_menu()
{
	global $db;
	$sql = "select * from ".table('weixin_menu')." WHERE parentid=0";
	return $db->getall($sql);
}
function del_menu($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('weixin_menu')." WHERE id IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
		if (!$db->query("Delete from ".table('weixin_menu')." WHERE parentid IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
function get_binding_list()
{
	global $db;
	$sql = "select * from ".table('members')." where weixin_openid!='' order BY bindingtime desc";
	$binding_list = $db->getall($sql);
	return $binding_list;
}
function del_binding($uid)
{
	global $db;
	if(!is_array($uid)) $uid=array($uid);
	$return=0;
	$sqlin=implode(",",$uid);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update ".table('members')." set weixin_openid='',bindingtime=0 WHERE uid IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
?>