<?php
if(!defined('IN_KKFRAME')) exit();
$date = date('Ymd', TIMESTAMP+900);
DB::query("ALTER TABLE xxx_post_log CHANGE `date` `date` INT NOT NULL DEFAULT '{$date}'");
DB::query("INSERT IGNORE INTO xxx_post_log (sid, uid) SELECT sid, uid FROM xxx_post_posts");
$delete_date = date('Ymd', TIMESTAMP - 86400*10);
DB::query("DELETE FROM xxx_post_log WHERE date<'$delete_date'");
$setime=getSetting('xxx_post_se');
$dailytime=strtotime(date('Y-m-d'));
$nxrun =$dailytime+$setime*3600;
DB::query("update cron set nextrun='$nxrun' where id='xxx_post_se'");
define('CRON_FINISHED', true);