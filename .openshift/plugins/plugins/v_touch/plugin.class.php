<?php
if (! defined ( 'IN_KKFRAME' ))
	exit ( 'Access Denied!' );
class plugin_v_touch {
	var $description = '这是一个点触验证码插件，安装后需要设置才能正常使用';
	var $modules = array ();
	public static function on_config() {
		if ($_POST) {
			saveSetting ( 'v_c_publicKey', $_POST ['v_c_publicKey'] );
			saveSetting ( 'v_c_privateKey', $_POST ['v_c_privateKey'] );
			showmessage ( "设置保存成功" );
		} else {
			$publicKey = getSetting ( 'v_c_publicKey' );
			$privateKey = getSetting ( 'v_c_privateKey' );
			return <<<EOF
<p>请前往<a href="http://www.touclick.com/" target="_blank">www.touclick.com</a>申请账号，并把获取到的公钥和私钥依次填到这里</p>
<p>输入您获取到的公钥：</p>
<input type="text" name="v_c_publicKey" id="v_c_publicKey" style="width:100%" value="{$publicKey}"/>
<p>输入您获取到的私钥：</p>
<input type="text" name="v_c_privateKey" id="v_c_privateKey" style="width:100%" value="{$privateKey}"/>
EOF;
		}
	}
	public static function on_load() {
		if ($_GET ['action'] != 'register' || ! $_POST)
			return;
		if (! $_POST ['check_key'] || ! $_POST ['check_address'])
			showmessage ( '请先输入点触验证码', 'member.php?action=register' );
		else {
			$publicKey = getSetting ( 'v_c_publicKey' );
			$privateKey = getSetting ( 'v_c_privateKey' );
			$check_key = $_POST ['check_key'];
			$check_address = $_POST ['check_address'];
			require_once 'touclick.class.php';
			$touclick = new touclick ();
			$res = $touclick->touclickCheck ( $publicKey, $privateKey, $check_key, $check_address );
			if (! $res) {
				showmessage ( '验证码二次验证未通过，请重来一次', 'member.php' );
			}
		}
	}
	public static function member_footer() {
		$publicKey = getSetting ( 'v_c_publicKey' );
		echo <<<EOF
<script type='text/javascript' charset='utf-8' src='http://js.touclick.com/js.touclick?b={$publicKey}&pf=api&v=v2-2'></script>
<script type="text/javascript">
var is_checked = false; 
function tou_submit()
{
	if (is_checked === true)
	{
		return true;
	}
	else
	{
		window.TouClick.Start({
			website_key: '{$publicKey}',
			position_code: 0,
			args: { 'this_form': this},
			captcha_style: { },
			onSuccess: function (args, check_obj)
			{
				is_checked = true;
				var this_form = args.this_form;
				var hidden_input_key = document.createElement('input');
				hidden_input_key.name = 'check_key';
				hidden_input_key.value = check_obj.check_key;
				hidden_input_key.type = 'hidden';
				this_form.appendChild(hidden_input_key);
				var hidden_input_address = document.createElement('input');
				hidden_input_address.name = 'check_address';
				hidden_input_address.value = check_obj.check_address;
				hidden_input_address.type = 'hidden';
				this_form.appendChild(hidden_input_address);
				this_form.submit();
			},
			onError: function (args){}
		});
		return false;
	}
}
document.getElementsByTagName('form').item(1).onsubmit = function(){return tou_submit.call(this);}
</script>

EOF;
	}
}