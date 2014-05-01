<?php
if (! defined ( 'IN_KKFRAME' )) 	exit ( 'Access Denied!' );
?>
<h2>客户端回帖</h2><p style="color:#757575;font-size:12px">当前插件版本：0.2.3 | 更新日期：14-04-04 | Designed By <a href="http://tieba.baidu.com/home/main?un=%D0%C7%CF%D2%D1%A9&fr=index" target="_blank">@星弦雪</a></p>
<p>使用该插件需做好每日被永封的准备，因发帖插件导致的账号被封、被屏蔽，请使用者自行承担后果
	<a id="see_jiefen">【点此查看轻松解封方法】</a>
</p>

<style type="text/css">
.x_tab{border:2px solid #2c3e50;border-radius:8px;margin-bottom:20px;width:99%;box-shadow:0 0 5px rgba(0,0,0,0.5);}
.x_tab_title{background-color:#2c3e50;}
.x_tab_title ul{width:100%;height:40px}
.x_tab_title li{float:left;font-size:1.2em;height:40px;border-top-left-radius:8px;border-top-right-radius:8px}
.x_tab_title li:hover{background:-webkit-linear-gradient(top,#2c3e50,#ffffff)}
.x_tab_title li a{padding:10px 15px 10px 15px;display:block;color:white}
.x_tab_title_selected,.x_tab_title_selected:hover{background:white!important}
.x_tab_title_selected a{color:#2c3e50!important;cursor:default!important}
.x_tab_title a:hover{text-decoration:none!important;cursor:pointer}
.x_tab_content{padding:15px;}
table.x_table thead tr{background-color:#dedede;}
@media (max-width: 382px){
	.x_tab_title ul{height:50px}
	.x_tab_title li{width:33%;height:50px}
	.x_tab_title li a{padding:5px 15px 5px 15px}
}
</style>


<div class="x_tab">
<div class="x_tab_title">
<ul>
	<li class="x_tab_title_selected"><a>回帖设置</a></li><li><a>高级设置</a></li><li><a>回帖记录</a></li>
</ul>
</div>
<div class="x_tab_content">
<p class="x_tab_content_title">添加需要回的帖子：</p>
<table class="x_table">
	<thead><tr><td style="width:20px">序号</td><td>贴吧</td><td>贴子</td><td style="width: 20%">操作</td></tr></thead>
	<tbody id="xxx_post_show">
		<tr><td colspan="4"><img src="./style/loading.gif">载入中请稍后</td></tr>
	</tbody>
</table>
<p>
	<a class="btn" id="xxx_post_add_tid">添加贴子</a>
	<a class="btn" id="x_p_add_tb"	style="margin-left: 5px">添加贴吧</a>
	<a class="btn" id="x_p_del_tid"	style="margin-left: 5px">全部删除</a>
</p>
<p class="x_tab_content_title">添加回帖内容：(回帖时随机使用其中之一，不添加的话会使用系统内置的)</p>
<table class="x_table">
	<thead><tr><td style="width: 20px">序号</td><td>回帖内容</td><td style="width: 20%">操作</td></tr></thead>
	<tbody id="xxx_post_contents"><tr><td colspan="4"><img src="./style/loading.gif">载入中请稍后</td></tr></tbody>
</table>
<p>
	<a class="btn" id="xxx_post_add_content">添加内容</a>
	<a class="btn" id="x_p_add_con"	style="margin-left: 5px">批量添加</a>
	<a class="btn" id="x_p_del_con"	style="margin-left: 5px">全部删除</a>
</p>
</div>

<div class="x_tab_content">
<form method="post" id="xxx_post_settings"
	action="plugins/xxx_post/ajax.php?v=set-settings"
	onsubmit="return post_win(this.action, this.id)">
	<p>
		客户端类型：
	<select name="x_p_client_type" id="x_p_client_type" disabled>
	  <option value="1">iphone</option>
	  <option value="2">android</option>
	  <option value="3">WindowsPhone</option>
	  <option value="4">windows8</option>
	  <option value="5">随机</option>
	</select>
	</p>
	<p>回帖频率：
	<select name="x_p_frequency" id="x_p_frequency" disabled>
	  <option value="2">每天回一次</option>
	  <option value="1">早晚各回一次</option>
<?php if (getSetting ( 'xxx_post_sxbk' ) == 1) echo '<option value="4">极限刷帖</option>'; ?>
	</select>
	，<span id="x_p_runtimes_hide">每次回
	<input type="number" name="x_p_runtimes" id="x_p_runtimes" min="1" max="999" disabled>
	贴，</span>发出一贴后等待
	<input type="number" name="x_p_delay" id="x_p_delay" min="0" max="15" disabled>
	分钟再发下一帖
	<input type="submit" value="保存设置">
	</p>
</form>
	<br><br>
	<p>随机选取一个帖子，进行一次回帖测试，检查你的设置有没有问题
	<a href="plugins/xxx_post/ajax.php?v=test_post" class="btn"	onclick="return msg_win_action(this.href)">测试回帖</a>
	</p>
</div>

<div class="x_tab_content">
<h2 id="x_p_post_log_tite">当天的回帖记录</h2>
<p>如果帖子已从回帖列表删除，则不会在这里显示</p>
<p id="x_p_pager_text"></p>
<table class="x_table">
	<thead><tr><td style="width: 20px">序号</td><td>贴吧</td><td>贴子</td><td style="width: 20px">成功</td><td style="width: 20px">失败</td></tr></thead>
	<tbody id="x_p_log_tab"><tr><td colspan="5">载入中请稍后</td></tr></tbody>
</table>

</div></div>