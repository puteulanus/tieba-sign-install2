<?php
if(!defined('IN_KKFRAME')) exit('Access Denied!');
?>
<h2>高级管理面板</h2>
<p style="color:#757575;font-size:12px">当前插件版本：0.1.3 | 更新日期：2013-12-08 | Designed By <a href="http://tieba.baidu.com/home/main/?un=%D0%C7%CF%D2%D1%A9&fr=frs" target="_blank">@星弦雪</a></p>
<style>
#cronadvanceset .x_adt_statue_wrap{width:33.33%;}#cronadvanceset .x_adt_statue_wrap input{position:absolute;left:-9999px;}.slider-v2{position:relative;display:block;width:5em;height:2em;cursor:pointer;border-radius:1.5em;transition:350ms;background:linear-gradient(rgba(0,0,0,0.07),rgba(255,255,255,0)),#dddddd;box-shadow:0 0.07em 0.1em -0.1em rgba(0,0,0,0.4) inset,0 0.05em 0.08em -0.01em rgba(255,255,255,0.7);}.slider-v2::after{position:absolute;content:'';width:1.5em;height:1.5em;top:0.3em;left:0.5em;border-radius:50%;transition:250ms ease-in-out;background:linear-gradient(#f5f5f5 10%,#eeeeee);box-shadow:0 0.1em 0.15em -0.05em rgba(255,255,255,0.9) inset,0 0.2em 0.2em -0.12em rgba(0,0,0,0.5);}.slider-v2::before{position:absolute;content:'';width:3.6em;height:1.5em;top:0.28em;left:0.75em;border-radius:0.75em;transition:250ms ease-in-out;background:linear-gradient(rgba(0,0,0,0.07),rgba(255,255,255,0.1)),#d0d0d0;box-shadow:0 0.08em 0.15em -0.1em rgba(0,0,0,0.5) inset,0 0.05em 0.08em -0.01em rgba(255,255,255,0.7),0 0 0 0 rgba(68,204,102,0.7) inset;}input:checked+.slider-v2::before{box-shadow:0 0.08em 0.15em -0.1em rgba(0,0,0,0.5) inset,0 0.05em 0.08em -0.01em rgba(255,255,255,0.7),3em 0 0 0 rgba(68,204,102,0.7) inset;}input:checked+.slider-v2::after{left:3em;}#cronadvanceset td{padding:8px}
</style>
<h2>Cron管理</h2>
<p>这里显示了当前cron执行状况，可以查看不签到是因为cron在哪里卡住了，并可以对相关项进行一些操作。</p>
<table>
	<thead>
		<tr>
			<td style="width:10%">cron名称</td>
			<td style="width:10%">优先级</td>
			<td style="width:10%">状态</td>
			<td style="width:15%">下次执行时间</td>
			<td>描述</td>
			<td>操作</td>
		</tr>
	</thead>
	<tbody id="x_ad_cron"></tbody>
</table>
<br>
<p>下一次cron执行时间：<?php echo date('Y-n-j G:i:s',getSetting ( 'next_cron' ));?></p>
<p>
<a href="plugins/x_admincp/ajax.php?v=refreshcron" class="btn"	onclick="return msg_callback_action(this.href,x_load_crons)">一键重置cron并清理缓存</a>
<a href="plugins/x_admincp/ajax.php?v=refreshcroncache" class="btn"	onclick="return msg_callback_action(this.href,x_load_crons)">仅清理cron相关的缓存</a>
</p>
<br>
<h2>清理缓存</h2>
<p>
<a href="plugins/x_admincp/ajax.php?v=refreshcache" class="btn"	onclick="return msg_win_action(this.href)">一键清理全部缓存</a>
</p>
<br>
<h2>批量清理用户</h2>
<p>
<form method="post" action="plugins/x_admincp/ajax.php?v=delusers" id="x_adt_delusers" onsubmit="return post_win(this.action, this.id)">
<p>删除UID从<input type="text" name="startnum" style="width:100px;margin:0 5px 0 5px;">到<input type="text" name="endnum" style="width:100px;margin:0 5px 0 5px;">的用户。<input type="submit" value="确定"></p>
</form>

<form  method="post" action="plugins/x_admincp/ajax.php?v=delusersx" id="x_adt_delusersx" onsubmit="return post_win(this.action, this.id)">
<p>删除用户名为<input type="text" name="username" style="width:120px;margin:0 5px 0 5px;">的用户。<input type="submit" value="确定"></p>
</form>
