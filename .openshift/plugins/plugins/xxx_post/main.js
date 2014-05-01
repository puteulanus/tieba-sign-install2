$("#menu_xxx_post-index").click(function (){
	if($(".x_tab_title_selected").index()==1) load_post_adv_set();
	else if($(".x_tab_title_selected").index()==2) load_post_log();
	else if($(".x_tab_title_selected").index()==0) load_post_set();
});
$('#x_p_frequency').change(function(){
	if($('#x_p_frequency').val()==4) $("#x_p_runtimes_hide").fadeOut("slow");
	else $("#x_p_runtimes_hide").fadeIn("slow");
});
$("#xxx_post_add_tid").click(function(){
	createWindow().setTitle("添加帖子").setContent('<p>你可以指定帖子进行回复</p><p>请输入帖子的地址:</p><p>例如:http://tieba.baidu.com/p/2692275116</p><form method="get" action="plugins/xxx_post/ajax.php?v=get-tid" id="xxx_post_tid_form" onsubmit="return xxx_post_win(this.action, this.id)"><input type="text" id="xxx_post_tid" name="xxx_post_tid" style="width:90%"/></form>').addButton("确定", function(){ $('#xxx_post_tid_form').submit(); }).addCloseButton("取消").append();
	});
$("#x_p_add_tb").click(function(){
	createWindow().setTitle("添加帖吧").setContent('<p>你可以只指定贴吧，并从该贴吧首页随机选择帖子进行回复</p><p>请输入帖吧的名字（不要带“吧”字）:</p><p>例如:要添加chrome吧，请输入chrome</p><form method="get" action="plugins/xxx_post/ajax.php?v=add-tieba" id="xxx_post_add_tb_form" onsubmit="return xxx_post_win(this.action, this.id)"><input type="text" id="xxx_post_add_tieba" name="xxx_post_add_tieba" style="width:90%"/></form>').addButton("确定", function(){ $('#xxx_post_add_tb_form').submit(); }).addCloseButton("取消").append();
	});
$("#xxx_post_add_content").click(function(){
	createWindow().setTitle("添加回帖内容").setContent('<p>请输入要回复的内容（最多1000字符）:</p><form method="get" action="plugins/xxx_post/ajax.php?v=set-content" id="xxx_post_content_form" onsubmit="return xxx_post_win(this.action, this.id)"><textarea name="post_content" id="post_content" rows="5" style="width: 95%"></textarea></form>').addButton("确定", function(){ $('#xxx_post_content_form').submit(); }).addCloseButton("取消").append();
	});
$("#x_p_add_con").click(function(){
	createWindow().setTitle("批量添加内容").setContent('<p>请输入要回复的内容（每行算一条）:</p><form method="get" action="plugins/xxx_post/ajax.php?v=set-cont-plus" id="x_p_cont_form" onsubmit="return xxx_post_win(this.action, this.id)"><textarea name="x_p_contant" id="x_p_contant" rows="8" style="width: 95%"></textarea></form>').addButton("确定", function(){ $('#x_p_cont_form').submit(); }).addCloseButton("取消").append();
	});
$("#x_p_del_con").click(function(){
	createWindow().setTitle("批量删除").setContent('你确定要删除全部回复内容吗？').addButton("确定", function(){xxx_msg_win_action('plugins/xxx_post/ajax.php?v=del-all-cont');}).addCloseButton("取消").append();
});	
$("#x_p_del_tid").click(function(){
	createWindow().setTitle("批量删除").setContent('你确定要删除全部贴子吗？').addButton("确定", function(){xxx_msg_win_action('plugins/xxx_post/ajax.php?v=del-all-tid');}).addCloseButton("取消").append();
});	
$("#see_jiefen").click(function(){
createWindow().setTitle("贴吧解封方法").setContent('<p>其实解封很简单的= =（作者表示已经被永封过无数次）</p><p>如果被度受永封的话：</p><p>1.绑定手机秒解</p><p>2.申请人工解封的话，只要你不是丧心病狂地每分钟一贴，一般都可以通过</p><p>如果被吧务封禁的话，只好找吧务承认错误并表示永不再犯= =（不过在官方水楼里刷的话应该吧务不会插手）</p>').addCloseButton("确定").append();
});
$(".x_tab .x_tab_content").each(function(i){
	$(this).addClass("x_tab_content_"+i);
	if(i!=0) $(this).hide();
});
$(".x_tab .x_tab_title li a").click(function(){
	if($(this).parent().hasClass("x_tab_title_selected")) return 0;
	else{
		$(".x_tab .x_tab_content_"+$(this).parent().siblings().filter(".x_tab_title_selected").index()).slideUp();
		$(this).parent().siblings().filter(".x_tab_title_selected").removeClass("x_tab_title_selected");
		$(".x_tab .x_tab_content_"+$(this).parent().index()).slideDown();
		$(this).parent().addClass("x_tab_title_selected");
		if($(this).parent().index()==1) load_post_adv_set();
		else if($(this).parent().index()==2) load_post_log();
		else if($(this).parent().index()==0) load_post_set();
	}
});
function load_post_set(){
	showloading();
	$.getJSON("plugins/xxx_post/ajax.php?v=post-settings", function(result){
		show_post_set(result);
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取设置').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}
function show_post_set(result){
	$('#xxx_post_show').html('');
	$('#xxx_post_contents').html('');
	if(result.count1){
		$.each(result.tiebas, function(i, field){
		$("#xxx_post_show").append("<tr><td>"+(i+1)+"</td><td><a href=\"http://tieba.baidu.com/f?kw="+field.unicode_name+"\" target=\"_blank\">"+field.name+"</a></td><td><a href=\"http://tieba.baidu.com/p/"+field.tid+"\" target=\"_blank\">"+field.post_name+"</a></td><td><a href=\"javascript:;\" onclick=\"return delsid('"+field.sid+"')\">删除</a></td></tr>");
	});}else{
		$('#xxx_post_show').html('<tr><td colspan="4">暂无记录</td></tr>');
	}
	if(result.count2){
		$.each(result.contents, function(i, field){
		$("#xxx_post_contents").append("<tr><td>"+(i+1)+"</td><td>"+field.content+"</td><td><a href=\"javascript:;\" onclick=\"return delcont('"+field.cid+"')\">删除</a></td></tr>");
	});}else{
		$('#xxx_post_contents').html('<tr><td colspan="3">暂无记录</td></tr>');
	}
}
function load_post_adv_set(){
	showloading();
	$.getJSON("plugins/xxx_post/ajax.php?v=post-adv-settings", function(result){
		$('#x_p_client_type').val(result.settings.client_type).removeAttr('disabled');
		$('#x_p_frequency').val(result.settings.frequency).removeAttr('disabled');
		$('#x_p_delay').val(result.settings.delay).removeAttr('disabled');
		$('#x_p_runtimes').val(result.settings.runtimes).removeAttr('disabled');
		if(result.settings.frequency==4) $("#x_p_runtimes_hide").hide();
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取设置').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}
function load_post_log(){
	showloading();
	$.getJSON("plugins/xxx_post/ajax.php?v=post-log", function(result){
		show_post_log(result);
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取回帖报告').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}
function load_post_history(date){
	showloading();
	$.getJSON("plugins/xxx_post/ajax.php?v=post-history&date="+date, function(result){
		show_post_log(result);
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取签到报告').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}
function show_post_log(result){
	if(!result || result.count == 0){
		$('#x_p_log_tab').html('<tr><td colspan="5">暂无记录</td></tr>');
		return;
	}
	$('#x_p_log_tab').html('');
	$('#x_p_post_log_tite').html(result.date+" 回帖记录");
	$.each(result.log, function(i, field){
		$("#x_p_log_tab").append("<tr><td>"+(i+1)+"</td><td><a href=\"http://tieba.baidu.com/f?kw="+field.unicode_name+"\" target=\"_blank\">"+field.name+"</a></td><td><a href=\"http://tieba.baidu.com/p/"+field.tid+"\" target=\"_blank\">"+field.post_name+"</a></td><td>"+field.status+"</td><td>"+field.retry+"</td></tr>");
	});
	var pager_text = '';
	if(result.before_date) pager_text += '<a class="btn" onclick="return load_post_history('+result.before_date+')">&laquo; 前一天</a>';
	pager_text += '<a class="btn" onclick="load_post_log()">今天</a>';
	if(result.after_date) pager_text += '<a class="btn" onclick="return load_post_history('+result.after_date+')">后一天 &raquo;</a>';
	$('#x_p_pager_text').html(pager_text);
}

function xxx_post_win(link, formid){
	link += link.indexOf('?') < 0 ? '?' : '&';
	link += "format=json";
	showloading();
	$.post(link, $('#'+formid).serialize(), function(result){
		createWindow().setTitle('系统消息').setContent(result.msg).addButton('确定', function(){ if(result.msgx==1) load_post_set(); }).append();
	}, 'json').fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法解析返回结果').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
	return false;
}
function xxx_msg_win_action(link){
	link += link.indexOf('?') < 0 ? '?' : '&';
	link += "format=json";
	showloading();
	$.getJSON(link, function(result){
		createWindow().setTitle('系统消息').setContent(result.msg).addButton('确定', function(){ load_post_set(); }).append();
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法解析返回结果').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
	return false;
}
function delsid(sid){
	createWindow().setTitle('删除帖子').setContent('确认要删除这个帖子的自动回复吗？').addButton('确定', function(){ xxx_msg_win_action("plugins/xxx_post/ajax.php?v=delsid&sid="+sid); }).addCloseButton('取消').append();
	return false;
}
function delcont(cid){
	createWindow().setTitle('删除帖子').setContent('确认要删除这个回复内容吗？').addButton('确定', function(){ xxx_msg_win_action("plugins/xxx_post/ajax.php?v=delcont&cid="+cid); }).addCloseButton('取消').append();
	return false;
}
