<?php
require_once '../../system/common.inc.php';
require_once './core.php';
if (!$uid)	exit ( 'Access Denied' );
$data = array ();
$data ['msgx'] = 1;
switch ($_GET ['v']) {
	case 'show_log':
		$query = DB::query ( "select * from x_tdou_log where uid='$uid' order by date desc" );
		while ( $result = DB::fetch ( $query ) ) {
			$data ['logs'] [] = $result;
		}
		$data['count']=count($data ['logs']);
		break;
	case 'test' :
		list($statue, $score) = x_tdou_time($uid);
		$date=date('Ymd', TIMESTAMP);
		switch ($statue){
			case '1':
				DB::query("UPDATE x_tdou_log SET num=num+$score WHERE uid='$uid' AND date='$date'");
				showmessage ( "领取在线奖励,获得{$score}个T豆");
				break;
			case '2':
				DB::query("UPDATE x_tdou_log SET num=num+$score WHERE uid='$uid' AND date='$date'");
				showmessage ( "开彩蛋,获得{$score}个T豆");
				break;
			case '3':
				showmessage ( "今天的在线奖励已经领完啦⊙ω⊙");
				break;
			case '4':
				showmessage ( "暂时没有T豆可以领取⊙ω⊙");
				break;
			default:
				showmessage ( "未知错误⊙ω⊙");
		}
		break;
}
echo json_encode ( $data );
?>