<?php
if (! defined ( 'IN_KKFRAME' ))
	exit ( 'Access Denied!' );
echo '<h2>T豆获取记录</h2><p style="color:#757575;font-size:12px">当前插件版本：0.1.8 | 更新日期：13-12-12 | Designed By <a href="http://tieba.baidu.com/home/main?un=%D0%C7%CF%D2%D1%A9&fr=index" target="_blank">@星弦雪</a></p>';
?>
<p>这个插件可以帮助你自动领取T豆在线奖励以及自动砸蛋</p>
<table>
	<thead><tr><td style="width:20px">日期</td><td>T豆数量</td></tr></thead>
	<tbody id="x_tdou_log">
		<tr><td colspan="2"><img src="./style/loading.gif">载入中请稍后</td></tr>
	</tbody>
</table>
<a href="plugins/x_tdou/ajax.php?v=test" class="btn" onclick="return msg_win_action(this.href)">测试</a>
