<?php
require_once '../../system/common.inc.php';
if(!is_admin($uid)) exit('Access Denied');
$data = array();
$data['msgx']=0;
switch($_GET['v']){
	case'refreshcroncache':
		saveSetting('next_cron', time());
		CACHE::update('setting');
		$data['msg']="清理cron缓存成功";
		break;
	case'refreshcron':
		$dailytime=strtotime(date('Y-m-d'));
		$commoncrontime=$dailytime+1800;
		$date=date ( 'Ymd', TIMESTAMP );
		DB::query ( "UPDATE cron SET enabled='1', nextrun='$commoncrontime'" );
		DB::query ( "UPDATE cron SET nextrun='$dailytime' where id='daily'" );
		DB::query ( "delete from sign_log where date='$date'" );
		$query = DB::query ( 'SHOW TABLES' );
		$tables = array ();
		while ($table= DB::fetch($query)) $tables[]=implode ('', $table );
		if(in_array('xxx_post_log', $tables)) DB::query ( "delete from xxx_post_log where date='$date'");
		if(in_array('x_meizi_log', $tables)) DB::query ( "delete from x_meizi_log where date='$date'");
		if(in_array('x_meizi_log_a', $tables)) DB::query ( "delete from x_meizi_log_a where date='$date'");
		if(in_array('x_meizi_log_b', $tables)) DB::query ( "delete from x_meizi_log_b where date='$date'");
		if(in_array('zw_sign_log', $tables)) DB::query ( "delete from zw_sign_log where date='$date'");
		if(in_array('zw_blockid_log', $tables)) DB::query ( "delete from zw_blockid_log where date='$date'");
		saveSetting('next_cron', $commoncrontime);
		CACHE::update('setting');
		$data['msg']="重置cron并清理缓存成功";
		break;
	case'refreshcache':
		DB::query ( "TRUNCATE cache" );
		$data['msg']="清理缓存成功";
		break;
	case'delusers':
		$startnum=intval($_POST['startnum']);
		$endnum=intval($_POST['endnum']);
		DB::query ( "DELETE FROM member WHERE uid>='$startnum' and uid<='$endnum'" );
		DB::query ( "DELETE FROM member_setting WHERE uid>='$startnum' and uid<='$endnum'" );
		DB::query ( "DELETE FROM my_tieba WHERE uid>='$startnum' and uid<='$endnum'" );
		DB::query ( "DELETE FROM sign_log WHERE uid>='$startnum' and uid<='$endnum'" );
		$data['msg']="批量删除用户成功，删除了uid为".$startnum.'到'.$endnum."的用户。";
		break;
	case 'delusersx':
		$username=trim($_POST['username']);
		$user = DB::result_first ( "select uid from member where username='$username'" );
		DB::query ( "DELETE FROM member WHERE uid='$user'" );
		DB::query ( "DELETE FROM member_setting WHERE uid='$user'" );
		DB::query ( "DELETE FROM my_tieba WHERE uid='$user'" );
		DB::query ( "DELETE FROM sign_log WHERE uid='$user'" );
		$data['msg']="批量删除用户成功，用户名为".$username."的用户。";
		break;
	case 'setcron':
		$id=$_POST['x_adt_id'];
		$order=intval($_POST['x_adt_order']);
		if($order<0) $order=0;
		elseif ($order>127) $order=127;
		$statue=intval((bool)$_POST['x_adt_statue']);
		$nextrun_h=intval($_POST['next_h']);
		$nextrun_m=intval($_POST['next_m']);
		if($nextrun_h<0) $nextrun_h=0;
		elseif ($nextrun_h>23) $$nextrun_h=23;
		if($nextrun_m<0) $nextrun_m=0;
		elseif ($nextrun_m>59) $$nextrun_m=59;
		$date=date('Y-m-d');
		$date=$date." $nextrun_h:$nextrun_m:00";
		$next=strtotime($date);
		DB::query ( "UPDATE  `cron` SET  `enabled` =  '$statue',`nextrun` =  '$next',`order` =  '$order' WHERE  `cron`.`id` =  '$id'" );
		$data['msgx']=1;
		$data['msg']="cron设置成功";
		break;
	case 'cronadvanceset':
		$id=$_GET['id'];
		$data ['cronadvanceset'] = DB::fetch_first("select * from cron where id='$id'");
		if($data ['cronadvanceset']['enabled']==1){
			$data ['cronadvanceset']['enabled']='checked="checked"';
		}else $data ['cronadvanceset']['enabled']='';
		$nextrun=$data ['cronadvanceset']['nextrun'];
		$data ['cronadvanceset']['nextrun_h']=date('G',$nextrun);
		$data ['cronadvanceset']['nextrun_m']=date('i',$nextrun);
		break;
	case 'showcrons':
		$query = DB::query ( "SELECT * FROM cron" );
		$nowcron = DB::fetch_first ( "SELECT * FROM cron WHERE enabled='1' AND nextrun<'".TIMESTAMP."' ORDER BY `order` LIMIT 0,1" );
		$data ['crons']=array();
		while ( $result = DB::fetch ( $query ) ) {
			$data ['crons'] [] = $result;
		}
		usort($data['crons'], "x_cron_sort");
		foreach ($data['crons'] as &$field){
			$field['nextrun']=date('Y-n-j G:i:s',$field['nextrun']);
			$field['description']=x_get_cron_description($field['id']);
			$field['statue']=$field['enabled']?'等待执行':'已完成';
			if($field['id']==$nowcron['id']) $field['statue']='正在执行';
		}
		break;
	case 'liketb':
		$liketb = $_POST['xxx_tools_liketb_tb'];
		
}
echo json_encode($data);

function x_cron_sort($a,$b){
	if($a['order']==$b['order']) return 0;
	return ($a['order']>$b['order']) ? 1 : -1;
}
function x_get_cron_description($id){
	switch ($id){
		case 'daily': return '将要签到的吧加入签到队列';
		case 'update_tieba': return '自动更新喜欢的吧';
		case 'sign': return '贴吧签到';
		case 'ext_sign': return '百科、文库签到';
		case 'mail': return '发送邮件';
		case 'sign_retry': return '对不能签到的贴吧再次尝试签到';
		case 'xxx_post_daily': return '将要发的帖子加入回帖队列';
		case 'xxx_post': return '第一次回帖';
		case 'xxx_post_se': return '第二次回帖';
		case 'xxx_post_sxbk': return '全天不间断回帖';
		case 'zw_blockid_daily': return '将要封禁的人加入封禁队列';
		case 'zw_blockid': return '执行封禁';
		case 'zw_sign_daily': return '将要签到的论坛加入队列';
		case 'zw_sign': return '执行论坛签到';
		case 'x_meizi_daily': return '将要投票的ID加入投票队列';
		case 'x_meizi_vote': return '执行投票';
		default :return '未知';
	}
}
?>