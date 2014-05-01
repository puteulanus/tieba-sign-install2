<?php
require_once '../../system/common.inc.php';
if (!is_admin($uid)) exit('Access Denied');

function getMailContent($format, $username, $authcode, $deathtime) {
	global $siteurl;
	$content = $format;
	$authlink = substr($siteurl, 0, -20) . 'member.php?action=verify&username=' . urlencode($username) . '&authcode=' . $authcode;
	// 检查是否有特定标记并进行替换
	if (strstr($content, '{username}')) $content = str_replace('{username}', $username, $content);
	if (strstr($content, '{deathtime}')) $content = str_replace('{deathtime}', $deathtime, $content);
	if (strstr($content, '{authlink}')) $content = str_replace('{authlink}', $authlink, $content);
	if (strstr($content, '{sendtime}')) $content = str_replace('{sendtime}', date('Y年m月d日 H:m:s'), $content);
	return $content;
}

$data = array();
$data['msgx'] = 0;
$mailauth = new plugin_zw_mailauth();
$setting = json_decode($mailauth -> getSetting('setting'), true);
switch ($_GET['action']) {
	case 'getsetting':
		$query = DB :: query("SELECT * FROM `zw_mailauth_list`;");
		while ($result = DB :: fetch ($query)) {
			$result['regtime'] = date("Y年m月d日 H:m:s", $result['regtime']);
			$data ['list'] [] = $result;
		}
		$data ['count'] = count($data ['list']);
		$data ['setting'] = json_decode($mailauth -> getSetting('setting'), true);
		break;
	case 'savesetting':
		$mailaddrepeat = $_POST['mailaddrepeat'] == 1?1:0;
		$mailauth -> saveSetting('setting', json_encode(array('deathtime' => $_POST['deathtime'],
					'title' => $_POST['title'],
					'format' => $_POST['format'],
					'mailaddrepeat' => $mailaddrepeat,
					'abledomain' => $_POST['abledomain'],
					'unabledomain' => $_POST['unabledomain'],
					'unableaddress' => $_POST['unableaddress'],
					)));
		$data['msg'] = '保存成功！';
		break;
	case 'clear':
		$deltime = time() - $setting['deathtime'] * 60;
		DB :: query("DELETE FROM `zw_mailauth_list` WHERE `regtime`<{$deltime}");
		$data['msg'] = "清除成功！";
		break;
	case 'alldel':
		DB :: query('TRUNCATE TABLE `zw_mailauth_list`');
		$data['msg'] = '已经全部删除！';
		break;
	case 'allpass':
		$query = DB :: query("SELECT * FROM `zw_mailauth_list`;");
		while ($result = DB :: fetch ($query)) {
			$list [] = $result;
		}
		DB :: query('TRUNCATE TABLE `zw_mailauth_list`');
		for($i = 0;$i < count($list);$i++) {
			$uid = DB :: insert('member', array('username' => $list[$i]['username'],
					'password' => $list[$i]['password'],
					'email' => $list[$i]['email'],
					));
			DB :: insert('member_setting', array('uid' => $uid));
			CACHE :: update('username');
			CACHE :: save('user_setting_' . $uid, '');
		}
		$data['msg'] = '已经全部通过！';
		break;
	case 'allresend':
		$query = DB :: query("SELECT * FROM `zw_mailauth_list`");
		while ($result = DB :: fetch ($query)) {
			$list [] = $result;
		}
		for($i = 0;$i < count($list);$i++) {
			$content = getMailContent($setting['format'], $list[$i]['username'], $list[$i]['authcode'], $list[$i]['deathtime']);
			DB :: insert('mail_queue', array('to' => $list[$i]['email'],
					'subject' => $setting['title'],
					'content' => $content
					));
		}
		DB :: query("UPDATE `zw_mailauth_list` SET `regtime`=" . time());
		saveSetting('mail_queue', 1);
		$data['msg'] = '已经全部加入到邮件队列中，稍后将自动发送！';
		break;
	case 'resend':
		$result = DB :: fetch_first("SELECT * FROM `zw_mailauth_list` WHERE `id`=" . intval($_GET['id']));
		$content = getMailContent($setting['format'], $result['username'], $result['authcode'], $setting['deathtime']);
		DB :: query("UPDATE `zw_mailauth_list` SET `regtime`=" . time() . " WHERE `id`=" . intval($_GET['id']));
		DB :: insert('mail_queue', array('to' => $result['email'],
				'subject' => $setting['title'],
				'content' => $content,
				));
		saveSetting('mail_queue', 1);
		$data['msg'] = "新的验证邮件已经加入到队列中，稍后将自动发送！";
		break;
	case 'pass':
		$result = DB :: fetch_first("SELECT * FROM `zw_mailauth_list` WHERE `id`=" . intval($_GET['id']));
		$uid = DB :: insert('member', array('username' => $result['username'],
				'password' => $result['password'],
				'email' => $result['email'],
				));
		DB :: insert('member_setting', array('uid' => $uid));
		CACHE :: update('username');
		CACHE :: save('user_setting_' . $uid, '');
		DB :: query("DELETE FROM `zw_mailauth_list` WHERE id=" . intval($_GET['id']));
		$data['msg'] = '已经通过帐号的邮箱验证！';
		break;
	case 'del':
		DB :: query("DELETE FROM `zw_mailauth_list` WHERE id=" . intval($_GET['id']));
		$data['msg'] = '成功删除该记录！';
		break;
	default:
		$data['msg'] = '没有指定Action！！';
}
echo json_encode ($data);
