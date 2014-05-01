<?php

// 设定数据库信息
$db_host = getenv('OPENSHIFT_MYSQL_DB_HOST');// 数据库地址
$db_port = intval(getenv('OPENSHIFT_MYSQL_DB_PORT'));// 数据库端口
$db_username = getenv('OPENSHIFT_MYSQL_DB_USERNAME');// 数据库用户名
$db_password = getenv('OPENSHIFT_MYSQL_DB_PASSWORD');// 数据库密码
$db_name = getenv('OPENSHIFT_APP_NAME');// 数据库名
$db_pconnect = True; // 保持数据库连接

// 数据库初始化
$function = $db_pconnect ? 'mysql_connect' : 'mysql_pconnect';
$link = mysql_connect("{$db_host}:{$db_port}", $db_username, $db_password);
$selected = mysql_select_db($db_name, $link);
if(!$selected){
	// 尝试新建
	mysql_query("CREATE DATABASE `{$db_name}`", $link);
	$selected = mysql_select_db($db_name, $link);
	}
mysql_query("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary");
$syskey = random(32);

// 生成随机密码
$systempd = (string)generate_password();
writepd($systempd);// 写入pd.txt文件中备用

// 设定管理员信息
$username = addslashes('default');// 管理员共户名
$password = md5($syskey.md5($systempd).$syskey);// 管理员密码
$email = addslashes('ex@mple.com');// 管理员邮箱
$install_script = file_get_contents(dirname(__FILE__).'/install.sql');
preg_match('/version ([0-9a-z.]+)/i', $install_script, $match);
$version = trim($match[1]);
$err = runquery($install_script, $link);
mysql_query("INSERT INTO member SET username='{$username}', password='{$password}', email='{$email}'");
$uid = mysql_insert_id($link);
mysql_query("INSERT INTO member_setting SET uid='{$uid}', cookie=''");
saveSetting('block_register', 1);
saveSetting('jquery_mode', 2);
saveSetting('admin_uid', $uid);
saveSetting('SYS_KEY', $syskey);

// 设定配置文件
$_config = array(
	'version' => $version,
	'db' => array(
	'server' => $db_host,
		'port' => $db_port,
		'username' => $db_username,
		'password' => $db_password,
		'name' => $db_name,
		'pconnect' => $db_pconnect,
		),
	);
$content = '<?php'.PHP_EOL.'/* Auto-generated config file */'.PHP_EOL.'$_config = ';
$content .= var_export($_config, true).';'.PHP_EOL.'?>';
file_put_contents('config.inc.php', $content);

// 自定函数
function runquery($sql, $link){
	$sql = str_replace("\r", "\n", $sql);
	foreach(explode(";\n", trim($sql)) as $query) {
		$query = trim($query);
		if(!$query) continue;
		$ret = mysql_query($query, $link);
		if(!$ret) return mysql_error();
	}
}

function saveSetting($k, $v){
	global $link;
	$v = addslashes($v);
	mysql_query("REPLACE INTO setting SET v='{$v}', k='{$k}'", $link);
}

function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}

function generate_password($length = 8) {
    // 密码字符集
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $password = '';
    for ( $i = 0; $i < $length; $i++ )
    {
        $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }
    return $password;
}

function writepd($syspd)
{
	$open=fopen('pd.txt','a' );
	fwrite($open,$syspd);
	fclose($open);
} 
?>