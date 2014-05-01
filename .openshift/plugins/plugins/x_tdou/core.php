<?php
if (! defined ( 'IN_KKFRAME' ))	exit ();
function x_tdou_time($uid){
	$formdata=array(
		'ie'=>'utf-8',
		'tbs'=>get_tbs($uid),
		'fr'=>'frs'
	);
	$ch=curl_init('http://tieba.baidu.com/tbscore/timebeat');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formdata));
	curl_setopt($ch, CURLOPT_COOKIE, get_cookie($uid));
	$re=curl_exec($ch);
	curl_close($ch);
	$re=json_decode($re,true);
	$retime=$re['data']['time_stat'];
	if($retime['interval_begin_time']+$retime['time_len']<$retime['now_time']&&$retime['time_has_score']=='true'){
		$formdata=array(
				'ie'=>'utf-8',
				'tbs'=>get_tbs($uid),
		);
		$ch=curl_init('http://tieba.baidu.com/tbscore/fetchtg');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formdata));
		curl_setopt($ch, CURLOPT_COOKIE, get_cookie($uid));
		$re=curl_exec($ch);
		curl_close($ch);
		$re=json_decode($re,true);
	}
	$gift_info=$re['data']['gift_info'][0];
	if($gift_info){
		$get_re=x_tdou_get($gift_info['gift_key'],$gift_info['gift_type'],$uid);
		$score=$get_re['data']['gift_got']['gift_score'];
		if(!$score) $score=0;
		return array($gift_info['gift_type'],$score);
	}else if(!$re['data']['time_stat']['time_has_score']){
		return array(3,0);
	}else{
		return array(4,0);
	}
}

function x_tdou_get($gift_key,$type,$uid){
	if($type==1) $type='time';
	elseif ($type==2) $type='rand';
	$formdata=array(
			'ie'=>'utf-8',
			'type'=>$type,
			'tbs'=>get_tbs($uid),
			'gift_key'=>$gift_key
	);
	$ch=curl_init('http://tieba.baidu.com/tbscore/opengift');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formdata));
	curl_setopt($ch, CURLOPT_COOKIE, get_cookie($uid));
	$re=curl_exec($ch);
	curl_close($ch);
	$re=json_decode($re,true);
	return $re;
}
?>