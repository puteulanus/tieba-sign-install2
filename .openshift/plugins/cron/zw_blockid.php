<?php
if (! defined ( 'IN_KKFRAME' )) exit ();
require_once ROOT . './plugins/zw_blockid/zw_blockid.php';
$date = date ( 'Ymd', TIMESTAMP + 900 );
$count = DB::result_first ( "SELECT COUNT(*) FROM `zw_blockid_log` WHERE (status=0 AND retry<5 AND date='{$date}')" );
saveSetting('blockcount',$count);
if ($count) {
	while ( $num ++ < 25 ) {
		$offset = rand ( 1, $count ) - 1;
		$block_info = DB::fetch_first ( "SELECT * FROM `zw_blockid_log` WHERE (status=0 AND retry<5 AND date='{$date}') LIMIT {$offset},1" );
		if (! $block_info)
			break;
		$result = zw_blockid ( $block_info ['fid'], $block_info ['blockid'], 1, $block_info ['uid'] );
		if ($result['errno'] == 0) {
			DB::query ( "UPDATE zw_blockid_log SET status=1 WHERE id='{$block_info['id']}'" );
		} else {
			DB::query ( "UPDATE zw_blockid_log SET retry=retry+1 WHERE id='{$block_info['id']}'" );
		}
		if (! defined ( 'SIGN_LOOP' ))
			break;
	}
} else {
	define ( 'CRON_FINISHED', true );
}