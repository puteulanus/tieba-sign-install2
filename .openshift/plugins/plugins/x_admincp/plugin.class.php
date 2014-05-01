<?php
if(!defined('IN_KKFRAME')) exit('Access Denied!');
class plugin_x_admincp{
	var $description = '高级管理面板，集成了cron管理、账号批量管理等功能';
	var $modules = array(
		array('id' => 'index', 'type' => 'page', 'title' => '高级管理面板', 'file' => 'index.inc.php','admin'=>'1'),
	);
	function page_footer_js(){
		echo <<<EOF
<script src="plugins/x_admincp/main.js"></script>
EOF;
}

}