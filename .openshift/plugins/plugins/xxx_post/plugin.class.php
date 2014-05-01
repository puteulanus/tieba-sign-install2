<?php
if (! defined ( 'IN_KKFRAME' ))	exit ( 'Access Denied!' );
class plugin_xxx_post {
	var $description = '可以模仿客户端进行回帖（三倍经验yoooooooooooooo）';
	var $modules = array (
			array ('id' => 'index',	'type' => 'page','title' => '客户端回帖','file' => 'index.php')
	);
	var $version='0.2.3';
	public function page_footer_js() {
		echo '<script src="plugins/xxx_post/main.js"></script>';
	}
	public function on_install() {
		$query = DB::query ( 'SHOW TABLES' );
		$tables = array ();
		while ($table= DB::fetch($query)) $tables[]=implode ('', $table );
		if (!in_array ( 'xxx_post_posts', $tables )){
			DB::query ("CREATE TABLE IF NOT EXISTS `xxx_post_posts` ( `sid` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY, `uid` int(10) unsigned NOT NULL, `fid` int(10) unsigned NOT NULL, `tid` int(12) unsigned NOT NULL, `name` varchar(127) NOT NULL, `unicode_name` varchar(512) NOT NULL, `post_name` varchar(127) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8");
			DB::query ("CREATE TABLE IF NOT EXISTS `xxx_post_setting` ( `uid` int(10) unsigned NOT NULL PRIMARY KEY, `client_type` tinyint(1) NOT NULL DEFAULT '5', `frequency` tinyint(1) NOT NULL DEFAULT '2', `delay` tinyint(2) NOT NULL DEFAULT '1', `runtime` int(10) unsigned NOT NULL DEFAULT '0', `runtimes` int(5) unsigned NOT NULL DEFAULT '6') ENGINE=InnoDB DEFAULT CHARSET=utf8");
			DB::query ("CREATE TABLE IF NOT EXISTS `xxx_post_content` ( `cid` int(10) unsigned AUTO_INCREMENT PRIMARY KEY, `uid` int(10) unsigned NOT NULL, `content` varchar(1024) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8");
			DB::query ("CREATE TABLE IF NOT EXISTS `xxx_post_log` ( `sid` int(10) unsigned NOT NULL, `uid` int(10) unsigned NOT NULL, `date` int(11) NOT NULL DEFAULT '0', `status` tinyint(4) NOT NULL DEFAULT '0', `retry` tinyint(3) unsigned NOT NULL DEFAULT '0', UNIQUE KEY `sid` (`sid`,`date`), KEY `uid` (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8");
			DB::query ("REPLACE INTO `cron` (`id`, `enabled`, `nextrun`, `order`) VALUES ('xxx_post_daily', 1, 0, 101),('xxx_post', 1, 0, 103),('xxx_post_se', 1, 0, 105),('xxx_post_sxbk', 1, 0, 109)");
			saveSetting ( 'xxx_post_sxbk', '0' );
			saveSetting ( 'xxx_post_se', '21' );
			saveSetting ( 'xxx_post_first_end',15);
			showmessage ( '客户端回帖插件'.$this->version.'安装成功！');
		}
		$oldversion = '0.2.2_13';
		switch ($oldversion){
			case '0.2.2_13':
			default:
				showmessage ('客户端回帖插件已升级到'.$this->version.'！');
		}
	}
	public function on_uninstall() {
		DB::query ( "DROP TABLE xxx_post_content,xxx_post_log,xxx_post_posts,xxx_post_setting" );
		DB::query ( "DELETE FROM setting WHERE k='xxx_post'" );
		DB::query ( "DELETE FROM setting WHERE k='xxx_post_sxbk'" );
		DB::query ( "DELETE FROM setting WHERE k='xxx_post_se'" );
		DB::query ( "DELETE FROM cron WHERE id='xxx_post'" );
		DB::query ( "DELETE FROM cron WHERE id='xxx_post_daily'" );
		DB::query ( "DELETE FROM cron WHERE id='xxx_post_se'" );
		DB::query ( "DELETE FROM cron WHERE id='xxx_post_sxbk'" );
		CACHE::update ( 'plugins' );
		showmessage ( "数据库删除成功。" );
	}
	public function on_config() {
		if ($_POST) {
			$sxbkset=trim($_POST ['sxbkset']);
			$se_set=intval(trim($_POST['se_set']));
			$first_end=intval(trim($_POST['first_end']));
			if (! $sxbkset)	$sxbkset = 0;
			if($se_set<12) $se_set=12;
			else if ($se_set>22) $se_set=22;
			if($first_end<1) $first_end=1;
			else if ($first_end>22) $first_end=22;
			saveSetting('xxx_post_sxbk',$sxbkset);
			saveSetting('xxx_post_se',$se_set);
			saveSetting('xxx_post_first_end',$first_end);
			showmessage ( "设置保存成功" );
		} else {
			$sxbk=getSetting('xxx_post_sxbk');
			$se_set=getSetting('xxx_post_se');
			$first_end=getSetting('xxx_post_first_end');
			$sxbk = $sxbk ? 'checked="cheched"' : '';
			return <<<EOF
<P><label><input type="checkbox" name="sxbkset" value="1" $sxbk> 允许极限刷帖（此功能及其消耗服务器资源，而且会导致sign_retry任务无法执行，如果你是管理员，可以考虑禁用这个选项）</label></p>
<p>时间控制(24小时制):</p>
<p>在<input type="number" name="first_end" min="1" max="22" value="$first_end" style="outline:none;margin-left:4px;margin-right:4px"/>点之前结束第一次回帖</p>
<p>在<input type="number" name="se_set" min="12" max="22" style="outline:none;margin-left:4px;margin-right:4px" value="$se_set"/>点之后开始第二次回帖</p>
EOF;
		}
	}
}