<?php
require_once '../../system/common.inc.php';
if (!is_admin($uid)) exit('Access Denied');
$data = array();
$data['msgx'] = 0;
$zw_custompage = new plugin_zw_custompage();
switch ($_GET['action']) {
	case 'getsetting':
		$query = DB :: query("SELECT * FROM `zw_custompage_pages`");
		while ($result = DB :: fetch ($query)) {
			$result['title'] = strip_tags(trim($result['title']));
			$result['content'] = strip_tags(trim($result['content']));
			$result['content'] = cutstr($result['content'], 50, '...');
			$data ['pages'] [] = $result;
		}
		$data ['count'] = count($data ['pages']);
		$setting = json_decode($zw_custompage -> getSetting('setting'), true);
		$data ['setting'] = $setting ? $setting : array("page_switch" => 1, "footer_js_switch" => 1, "footer_text_switch" => 1, "bg_switch" => 0, "page_footer_js" => "", "page_footer_text" => "", "bg_images" => ""
			);
		break;
	case 'savesetting':
		$zw_custompage -> saveSetting('setting', json_encode(array('page_switch' => $_POST['page_switch'] == 1?1:0,
					'footer_js_switch' => $_POST['footer_js_switch'] == 1?1:0,
					'footer_text_switch' => $_POST['footer_text_switch'] == 1?1:0,
					'bg_switch' => $_POST['bg_switch'] == 1?1:0,
					'page_footer_js' => trim($_POST['page_footer_js']),
					'bg_images' => trim($_POST['bg_images']),
					'page_footer_text' => trim($_POST['page_footer_text']),
					)));
		$data['msg'] = '保存成功！';
		break;
	case 'addpage':
		DB :: insert('zw_custompage_pages', array('title' => daddslashes(trim($_POST['page_title'])),
				'content' => daddslashes(trim($_POST['page_content'])),
				'pswitch' => $_POST['this_page_switch'] == 1 ? 1 : 0,
				));
		$data['msg'] = '添加成功！';
		break;
	case 'delall':
		DB :: query('TRUNCATE TABLE zw_custompage_pages;');
		$data['msg'] = '已经全部删除！';
		break;
	case 'allable':
		DB :: query('UPDATE `zw_custompage_pages` SET  `pswitch` = 1 WHERE `pswitch` = 0');
		$data['msg'] = '已经全部启用！';
		break;
	case 'allunable':
		DB :: query('UPDATE `zw_custompage_pages` SET  `pswitch` = 0 WHERE `pswitch` = 1');
		$data['msg'] = '已经全部关闭！';
		break;
	case 'turnedtoother':
		DB :: query("UPDATE `zw_custompage_pages`  SET pswitch=1-pswitch");
		$data['msg'] = '已经反向开启/关闭所有页面！';
		break;
	case 'setpage':
		DB :: query("UPDATE `zw_custompage_pages` SET  `title` =   '" . daddslashes(trim($_POST['page_title'])) . "',`content`  =  '" . daddslashes(trim($_POST['page_content'])) . "',`pswitch` =" . ($_POST['this_page_switch'] == 1 ? 1 : 0) . " WHERE id=" . intval($_GET['id']));
		$data['msg'] = '保存成功！';
		break;
	case 'getpage':
		$result = DB :: fetch_first("SELECT * FROM `zw_custompage_pages` WHERE id=" . intval($_GET['id']));
		$data ['this_page'] = $result;
		break;
	case 'delpage':
		DB :: query("DELETE FROM `zw_custompage_pages` WHERE id=" . intval($_GET['id']));
		$data['msg'] = '删除成功！';
		break;
	default:
		$data['msg'] = '没有指定Action！！';
}
echo json_encode ($data);
