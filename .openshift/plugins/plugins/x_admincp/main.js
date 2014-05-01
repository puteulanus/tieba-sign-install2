$("#menu_x_admincp-index").click(function (){x_load_crons();});
function x_load_crons(){
	showloading();
	$.getJSON("plugins/x_admincp/ajax.php?v=showcrons", function(result){
		x_show_crons(result);
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取cron表内容').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}
function x_show_crons(result){
	$('#x_ad_cron').html('');
	$.each(result.crons, function(i, field){
		$("#x_ad_cron").append("<tr><td>"+field.id+"</td><td>"+field.order+"</td><td>"+field.statue+"</td><td>"+field.nextrun+"</td><td>"+field.description+"</td><td><a href=\"javascript:;\" onclick=\"return advanceset('"+field.id+"')\">高级设置</a></td></tr>");
		
	});
}
function advanceset(id){
	showloading();
	$.getJSON("plugins/x_admincp/ajax.php?v=cronadvanceset&id="+id, function(result){
		createWindow().setTitle("修改cron参数").setContent('<form method="get" action="plugins/x_admincp/ajax.php?v=setcron" id="cronadvanceset" onsubmit="return post_win(this.action, this.id,x_load_crons)"><input type="hidden" name="x_adt_id" value="'+id+'"><table><tr><td>Cron ID:</td><td>'+id+'</tr><tr><td>优先级(0-127):</td><td><input type="number" min="0" max="127" name="x_adt_order" style="width:120px" value="'+result.cronadvanceset.order+'"></td></tr><tr><td>状态:</td><td><div class="x_adt_statue_wrap"><input type="checkbox" name="x_adt_statue" id="x_adt_statue" value="1"'+result.cronadvanceset.enabled+'><label class="slider-v2" for="x_adt_statue"></label></div></td></tr><tr><td>下次执行时间:</td><td><input type="number" name="next_h" style="width:50px;margin:0 4px 0 0" min="0" max="23" value="'+result.cronadvanceset.nextrun_h+'"/>时<input type="number" name="next_m" style="width:50px;margin:0 4px 0 4px" min="0" max="59" value="'+result.cronadvanceset.nextrun_m+'"/>分</td></tr></table></form>').addButton("确定", function(){ $('#cronadvanceset').submit(); }).addCloseButton("取消").append();
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取指定内容').addButton('确定', function(){}).append(); }).always(function(){ hideloading(); });
}
