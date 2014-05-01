<?php
function zw_blockid($fid, $id, $day, $douid) {
	$blockid_api = "http://tieba.baidu.com/pmc/blockid";
	$formdata = array('user_name[]' => $id,
		'day' => $day,
		'fid' => $fid,
		'tbs' => get_tbs($douid),
		'ie' => 'gbk',
		'reason' => "抱歉，你的发贴操作或发表贴子的内容违反了本吧的吧规，已经被封禁，封禁期间不能在本吧继续发言。"
		);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $blockid_api);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_COOKIE, get_cookie($douid));
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formdata));
	$re = @json_decode(curl_exec($ch), true);
	curl_close($ch);
	if (empty($re)) {
		return array('errno' => -1, 'errmsg' => '未知错误！');
	} else {
		return $re;
	} 
} 