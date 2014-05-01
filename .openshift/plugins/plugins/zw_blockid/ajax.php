<?php
require_once '../../system/common.inc.php';

if (! $uid) exit ('Access Denied');
require_once ('zw_blockid.php');

$data = array ();
$data ['msgx'] = 1;
switch ($_GET ['action']) {
	case 'add-id' :
		$tieba = daddslashes($_POST ['tb_name']);
		$user_name = $_POST ['user_name'];
		$tb_name = mb_convert_encoding($_POST ['tb_name'], 'gb2312', 'utf-8');
		$url = 'http://tieba.baidu.com/f?kw=' . urlencode ($tb_name);
		$contents = fetch_url($url, 0, '', get_cookie ($uid));
		$fid = 0;
		preg_match ('/"forum_id"?:?(?<fid>\d+)/', $contents, $fids);
		$fid = $fids ['fid'];
		if ($fid == 0) {
			$data ['msg'] = "添加失败，无法获取该贴吧的FID";
			$data ['msgx'] = 0;
			break;
		} 
		DB :: insert ('zw_blockid_list', array ('uid' => $uid,
				'fid' => $fid,
				'blockid' => daddslashes($user_name),
				'tieba' => $tieba,
				));
		$data ['msg'] = "添加成功！贴吧FID为：{$fid}，被封禁用户为{$user_name}";
		break;
	case 'add-id-batch' :
		$tieba = $_POST ['tb_name'];
		$user_name = explode ("\n", $_POST ['user_name']);
		for($i = 0;$i < count($user_name);$i++) {
			$user_name[$i] = trim($user_name[$i]);
		} 
		$user_name = array_filter($user_name);
		if (!is_array($user_name)) {
			$data ['msg'] = "添加失败：格式错误，多个ID请用换行分隔！";
			break;
		} 
		$tb_name = mb_convert_encoding ($tieba, 'gb2312', 'utf-8');
		$url = 'http://tieba.baidu.com/f?kw=' . urlencode ($tb_name);
		$contents = fetch_url($url, 0, '', get_cookie ($uid));
		$fid = 0;
		preg_match ('/"forum_id"?:?(?<fid>\d+)/', $contents, $fids);
		$fid = $fids ['fid'];
		if ($fid == 0) {
			$data ['msg'] = "添加失败，无法获取该贴吧的FID";
			$data ['msgx'] = 0;
			break;
		} 
		$count = 0;
		foreach($user_name as $id) {
			if (DB :: insert ('zw_blockid_list', array ('uid' => $uid,
						'fid' => $fid,
						'blockid' => daddslashes($id),
							'tieba' => daddslashes($tieba),
							), true)) $count++;
		} 
		$data ['msg'] = "成功添加了{$count}个ID！所在贴吧为{$tieba}，该贴吧FID为：{$fid}";
		break;
	case 'get-list' :
		$data ['list'] = array ();
		$data ['log'] = array ();
		$query = DB :: query ("SELECT * FROM zw_blockid_list WHERE uid={$uid}");
		while ($result = DB :: fetch ($query)) {
			$data ['list'] [] = $result;
		} 
		$data ['today'] = date('Ymd');
		$zw_blockid = new plugin_zw_blockid();
		$sendmail_uid = array_filter(explode (',', $zw_blockid -> getSetting('sendmail_uid')));
		$data['sendmail'] = in_array($uid, $sendmail_uid)?1:0;
		break;
	case 'get-log':
		$date = intval($_GET['date']);
		$data ['log'] = array ();
		$data ['today'] = date('Ymd');
		$data ['date'] = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2);
		$data ['log_success_status'] = 0;
		$query = DB :: query ("SELECT * FROM zw_blockid_log WHERE uid={$uid} AND date={$date}");
		while ($result = DB :: fetch ($query)) {
			if ($result['status'] == 1) $data ['log_success_status']++;
			$data ['log'] [] = $result;
		} 
		$data['log_count'] = count($data ['log']);
		$data['before_date'] = DB :: result_first("SELECT date FROM zw_blockid_log WHERE uid={$uid} AND date<{$date} ORDER BY date DESC LIMIT 0,1");
		$data['after_date'] = DB :: result_first("SELECT date FROM zw_blockid_log WHERE uid={$uid} AND date>{$date} ORDER BY date LIMIT 0,1");
		break;
	case 'del-blockid' :
		$no = intval($_GET ['no']);
		DB :: query ("DELETE FROM zw_blockid_list WHERE id={$no} AND uid={$uid}");
		$data ['msg'] = "删除成功！";
		break;
	case 'do-blockid':
		$username = urldecode($_GET['blockid']);
		$tieba = urldecode($_GET['tieba']);
		$re = zw_blockid ($_GET['fid'], $username, 1, $uid);
		$id = intval($_GET['id']);
		if ($re['errno'] == -1) {
			$data ['msg'] = "JSON解析失败！";
		} elseif ($re['errno'] == 1) {
			$data ['msg'] = "封禁成功！封禁账号：{$username}，FID为{$_GET['fid']}";
			DB :: query ("UPDATE zw_blockid_log SET status=1 WHERE id={$id} AND uid={$uid}");
		} else {
			$data ['msg'] = "封禁失败！返回信息：{$re['errmsg']}，封禁账号：{$username}，所在贴吧：{$tieba}，FID为{$_GET['fid']}";
		} 
		break;
	case 'del-all' :
		DB :: query ("DELETE FROM zw_blockid_list WHERE uid='{$uid}'");
		$data ['msg'] = "删除成功！";
		break;
	case 'test-blockid' :
		$query = DB :: query ("SELECT * FROM zw_blockid_list WHERE uid='{$uid}'");
		while ($result = DB :: fetch ($query)) {
			$blockid_list [] = $result;
		} 
		if (! $blockid_list) {
			$data ['msgx'] = 0;
			$data ['msg'] = "没有封禁信息，请先添加！";
			break;
		} 
		$rand = rand (0, count ($blockid_list) - 1);
		$test_blockid = $blockid_list [$rand];
		$re = zw_blockid ($test_blockid ['fid'], $test_blockid ['blockid'], 1, $uid);
		if ($re['errno'] == -1) {
			$data ['msg'] = "JSON解析失败！";
		} elseif ($re['errno'] == 1) {
			$data ['msg'] = "封禁成功！封禁账号：{$test_blockid['blockid']}，所在贴吧：{$test_blockid['tieba']}，FID为{$test_blockid['fid']}";
		} else {
			$data ['msg'] = "封禁失败！返回信息：{$re['errmsg']}，封禁账号：{$test_blockid['blockid']}，所在贴吧：{$test_blockid['tieba']}，FID为{$test_blockid['fid']}";
		} 
		break;
	case 'setting':
		$zw_blockid = new plugin_zw_blockid();
		if (intval($_POST['zw_blockid-report']) == 1) {
			$sendmail_uid = array_filter(explode (',', $zw_blockid -> getSetting('sendmail_uid')));
			if (!in_array($uid, $sendmail_uid)) $sendmail_uid[] = $uid;
			$zw_blockid -> saveSetting('sendmail_uid', implode(',', $sendmail_uid));
			$data ['msg'] = "成功开启邮件报告！";
		} else {
			$sendmail_uid = array_filter(explode (',', $zw_blockid -> getSetting('sendmail_uid')));
			if (in_array($uid, $sendmail_uid)) {
				for($i = 0;$i < count($sendmail_uid);$i++) {
					if ($sendmail_uid[$i] == $uid) unset($sendmail_uid[$i]);
				} 
				$zw_blockid -> saveSetting('sendmail_uid', implode(',', $sendmail_uid));
			} 
			$data ['msg'] = "成功关闭邮件报告！";
		} 
		break;
	default :
		$data ['msg'] = "没有指定action！";
		break;
} 
echo json_encode ($data);
