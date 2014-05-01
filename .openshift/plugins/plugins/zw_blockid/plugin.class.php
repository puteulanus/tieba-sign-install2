<?php
if (! defined ('IN_KKFRAME')) exit ('Access Denied!');

class plugin_zw_blockid extends Plugin {
	var $name = 'zw_blockid';
	var $description = '本插件可以给网站用户提供循环封禁用户功能';
	var $modules = array (
		array ('id' => 'index',
			'type' => 'page',
			'title' => '循环封禁',
			'file' => 'zw_blockid.inc.php'
			),
		array('type' => 'cron',
			'cron' => array('id' => 'zw_blockid_daily', 'order' => 101),
			),
		array('type' => 'cron',
			'cron' => array('id' => 'zw_blockid', 'order' => 102),
			),
		array('type' => 'cron',
			'cron' => array('id' => 'zw_blockid_mail', 'order' => 103),
			),
		);
	var $version = '1.2.2';

	function install() {
		runquery("CREATE TABLE `zw_blockid_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `blockid` varchar(20) NOT NULL,
  `tieba` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `zw_blockid_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `fid` int(8) NOT NULL,
  `tieba` varchar(200) NOT NULL,
  `blockid` varchar(100) NOT NULL,
  `date` int(11) NOT NULL DEFAULT '20131201',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `retry` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
	}

	function uninstall() {
		runquery("
DROP TABLE `zw_blockid_list`;
DROP TABLE `zw_blockid_log`;
DELETE FROM `plugin_var` WHERE `pluginid`='zw_blockid';
");
	}

	function page_footer_js() {
		echo '<script src="plugins/zw_blockid/zw_blockid.js"></script>';
	}

	function on_upgrade($nowversion) {
		if ($nowversion == '0') {
			DB :: query("DELETE FROM  `setting` WHERE  `k` LIKE  'zw_blockid%';");
			showmessage('循环封禁插件已经成功升级到 <b>1.2.0</b>！更新内容：<br>1.修复 SQL注入漏洞<br>2.新增 查看30天记录<br>3.新增 邮件报告功能<br>4.新增 批量添加封禁ID功能<br>5.优化 插件代码逻辑<br>6.支持 新版(1.14.2.6)插件系统并可后台禁用cron');
		}
		if ($nowversion == '1.2.0') {
			return '1.2.1';
		}


	}
}
