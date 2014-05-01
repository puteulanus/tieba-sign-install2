<?php
require_once '../../system/common.inc.php';
require_once './core.php';
if (!$uid)	exit ( 'Access Denied' );
$data = array ();
$data ['msgx'] = 1;
switch ($_GET ['v']) {
	case 'delsid' :
		$_sid = intval ( $_GET ['sid'] );
		DB::query ( "DELETE FROM xxx_post_posts WHERE sid='{$_sid}'" );
		$data ['msg'] = "删除成功";
		break;
	case 'del-all-tid' :
		DB::query ( "DELETE FROM xxx_post_posts WHERE uid='{$uid}'" );
		$data ['msg'] = "删除成功";
		break;
	case 'delcont' :
		$cid = intval ( $_GET ['cid'] );
		DB::query ( "DELETE FROM xxx_post_content WHERE cid='{$cid}'" );
		$data ['msg'] = "删除成功";
		break;
	case 'del-all-cont' :
		DB::query ( "DELETE FROM xxx_post_content WHERE uid='{$uid}'" );
		$data ['msg'] = "删除成功";
		break;
	case 'set-content' :
		$contx = $_POST ['post_content'];
		if (! $contx) {
			$data ['msg'] = "设置失败，请输入字符串";
		} else {
			DB::insert ( 'xxx_post_content', array (
					'uid' => $uid,
					'content' => $contx 
			) );
			$data ['msg'] = "设置成功";
		}
		break;
	case 'set-cont-plus' :
		$contplus = $_POST ['x_p_contant'];
		if (! trim ( $contplus )) {
			$data ['msg'] = "设置失败，请输入字符串";
		} else {
			$cp_array = explode ( "\n", trim ( $contplus ) );
			foreach ( $cp_array as $contx ) {
				if (! trim ( $contx ))
					continue;
				DB::insert ( 'xxx_post_content', array (
						'uid' => $uid,
						'content' => $contx 
				) );
			}
			$data ['msg'] = "设置成功";
		}
		break;
	case 'set-settings' :
		$client_type = intval($_POST ['x_p_client_type']);
		$frequency = intval($_POST ['x_p_frequency']);
		$runtimes = intval($_POST ['x_p_runtimes']);
		$delay = intval($_POST ['x_p_delay']);
		if ($delay < 0)	$delay = 0;
		else if ($delay > 15)  $delay = 15;
		DB::query ( "replace into xxx_post_setting (uid,client_type,frequency,delay,runtimes) values($uid,$client_type,$frequency,$delay,$runtimes)" );
		$data ['msg'] = "设置成功";
		break;
	case 'post-settings' :
		$query = DB::query ( "SELECT * FROM xxx_post_posts WHERE uid='$uid'" );
		while ( $result = DB::fetch ( $query ) ) {
			$data ['tiebas'] [] = $result;
		}
		$query = DB::query ( "SELECT * FROM xxx_post_content WHERE uid='$uid'" );
		while ( $result = DB::fetch ( $query ) ) {
			$data ['contents'] [] = $result;
		}
		$data ['count1'] = count ( $data ['tiebas'] );
		$data ['count2'] = count ( $data ['contents'] );
		break;
	case 'post-adv-settings' :
		$query = DB::query ( "SELECT * FROM xxx_post_setting WHERE uid='$uid'" );
		while ( $result = DB::fetch ( $query ) ) {
			$data ['settings'] = $result;
		}
		if (! $data ['settings'] ['client_type']) {
			DB::query ( "insert into xxx_post_setting set uid=$uid");
			$data ['settings'] ['client_type'] = 5;
			$data ['settings'] ['frequency'] = 2;
			$data ['settings'] ['delay'] = 1;
			$data ['settings'] ['runtimes'] = 6;
		}
		break;
	case 'add-tieba' :
		$tieba = $_POST ['xxx_post_add_tieba'];
		$ch = curl_init ('http://tieba.baidu.com/f?kw='.urlencode(iconv("utf-8", "gbk", $tieba)).'&fr=index');
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$contents = curl_exec ( $ch );
		curl_close ( $ch );
		$fid = 0;
		preg_match('/"forum_id"\s?:\s?(?<fid>\d+)/', $contents, $fids);
		$fid = $fids ['fid'];
		if ($fid == 0) {
			$data ['msg'] = "添加失败，请检查贴吧名称并重试";
			$data ['msgx'] = 0;
			break;
		}
		preg_match ( '/fname="(.+?)"/', $contents, $fnames );
		$unicode_name = urlencode($fnames [1]);
		$fname = iconv("gbk", "utf-8", $fnames [1]);
		DB::insert ( 'xxx_post_posts', array (
			'uid' => $uid,
			'fid' => $fid,
			'tid' => 0,
			'name' => $fname,
			'unicode_name' => $unicode_name,
			'post_name' =>'随机'
		) );
		$data ['msg'] = "添加成功";
		break;
	case 'get-tid' :
		$tieurl = $_POST ['xxx_post_tid'];
		preg_match ( '/tieba\.baidu\.com\/p\/(?<tid>\d+)/', $tieurl, $tids );
		$tid=$tids ['tid'];
		$ch = curl_init ('http://tieba.baidu.com/p/'.$tid);
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$contents = curl_exec ( $ch );
		curl_close ( $ch );
		$fid = 0;
		preg_match ( '/"forum_id"\s?:\s?(?<fid>\d+)/', $contents, $fids );
		$fid =$fids ['fid'];
		if ($fid == 0) {
			$data ['msg'] = "添加失败，请检查帖子地址并重试";
			$data ['msgx'] = 0;
			break;
		}
		preg_match ( '/fname="(.+?)"/', $contents, $fnames );
		$unicode_name = urlencode($fnames [1]);
		$fname = iconv("gbk", "utf-8", $fnames [1]);
		preg_match ( '/title:"(.*?)"/', $contents, $post_names );
		$post_name = iconv("gbk", "utf-8", $post_names [1]);
		DB::insert ( 'xxx_post_posts', array (
				'uid' => $uid,
				'fid' => $fid,
				'tid' => $tid,
				'name' => $fname,
				'unicode_name' => $unicode_name,
				'post_name' => $post_name 
		) );
		$data ['msg'] = "添加成功";
		break;
	case 'test_post' :
		$tieba = DB::fetch_first ( "SELECT * FROM xxx_post_posts WHERE uid='$uid' ORDER BY RAND() LIMIT 0,1" );
		if (! $tieba) showmessage ('没有添加帖子，请先添加！');
		$x_content = DB::fetch_first ( "SELECT content FROM xxx_post_content WHERE uid='$uid' ORDER BY RAND() LIMIT 0,1" );
		list ( $status, $result ) = client_rppost ( $uid, $tieba, $x_content ['content'] );
		$status = $status == 2 ? '发帖成功' : '发帖失败';
		showmessage ( "<p>测试帖子：【{$tieba[name]}吧】{$tieba[post_name]}</p><p>测试结果：{$status}</p><p>详细信息：{$result}</p>" );
		break;
	case 'post-log' :
		$date = date ( 'Ymd' );
		$data ['date'] = date ( 'Y-m-d' );
	case 'post-history' :
		if ($_GET ['v'] == 'post-history') {
			$date = intval ( $_GET ['date'] );
			$data ['date'] = substr ( $date, 0, 4 ) . '-' . substr ( $date, 4, 2 ) . '-' . substr ( $date, 6, 2 );
		}
		$data ['log'] = array ();
		$query = DB::query ( "SELECT * FROM xxx_post_log l LEFT JOIN xxx_post_posts t ON t.sid=l.sid WHERE l.uid='$uid' AND l.date='$date'" );
		while ( $result = DB::fetch ( $query ) ) {
			if (! $result ['sid']) continue;
			$data ['log'] [] = $result;
		}
		$data ['count'] = count ( $data ['log'] );
		$data ['before_date'] = DB::result_first ( "SELECT date FROM xxx_post_log WHERE uid='{$uid}' AND date<'{$date}' ORDER BY date DESC LIMIT 0,1" );
		$data ['after_date'] = DB::result_first ( "SELECT date FROM xxx_post_log WHERE uid='{$uid}' AND date>'{$date}' ORDER BY date ASC LIMIT 0,1" );
		break;
}
echo json_encode ( $data );
?>