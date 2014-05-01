<?php
if(!defined('IN_KKFRAME')) exit();
require_once ROOT.'./plugins/x_tdou/core.php';
$now=TIMESTAMP;
$date = date('Ymd', TIMESTAMP);
$count = DB::result_first("SELECT COUNT(*) FROM `x_tdou_log` WHERE retry<5 AND date='{$date}'");
if($count){
	$count = DB::result_first("SELECT COUNT(*) FROM `x_tdou_log` WHERE nextrun<$now and retry<5 AND date='{$date}'");
	if(!$count){
		$r = DB::result_first ( "SELECT nextrun FROM x_tdou_log WHERE nextrun>$now and retry<5 AND date='{$date}' ORDER BY nextrun ASC LIMIT 0,1" );
		if(!$r) $r=TIMESTAMP+300;
		DB::query("update cron set nextrun=$r where id='x_tdou_get'");
	}else{
		$num = 0;
		while($num++ < 25){
			$offset = rand(1, $count) - 1;
			$uid = DB::result_first("SELECT uid FROM `x_tdou_log` WHERE retry<5 and nextrun<$now AND date='{$date}' LIMIT {$offset},1");
			if(!$uid) break;
			list($statue, $score) = x_tdou_time($uid);
			$nextrun=TIMESTAMP+300;
			switch ($statue){
				case 1:
					DB::query("UPDATE x_tdou_log SET nextrun=$nextrun,num=num+$score WHERE uid='$uid' AND date='$date'");
					break;
				case 2:
					DB::query("UPDATE x_tdou_log SET nextrun=$nextrun,num=num+$score WHERE uid='$uid' AND date='$date'");
					break;
				case 3:
					DB::query("UPDATE x_tdou_log SET nextrun=$nextrun,retry=retry+1 WHERE uid='$uid' AND date='$date'");
					break;
				case 4:
					DB::query("UPDATE x_tdou_log SET nextrun=$nextrun WHERE uid='$uid' AND date='$date'");
					break;
			}
			if(!defined('SIGN_LOOP')) break;
			$count--;
		}
	}
}else{
	define('CRON_FINISHED', true);
}
