<?php
/**
  * @Author Dawei Ma
  * @Date 2014-04-18
  * @Function 跑赢CPI注册页面
  */
require_once './databases.php';
$from_user = trim($_GET["from_user"],'\'');

echo '<!DOCTYPE HTML>';
echo '<html><head><meta charset = "UTF-8" />';
echo '<link rel="stylesheet" type="text/css" href="css/register.css" />';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '</head><body>';
echo '<div class="container">';
echo '<section id="content">';
echo '<form id="register_form" action="save_register_data.php"  method="post" novalidate onsubmit="return checkForm()">';
echo '<input type="hidden" id="from_user" name="from_user" value="'.$from_user.'"/>';
echo '<h1>跑赢CPI会员绑定</h1>';
echo '
<div>
<input type="text" title="您的输入包含了错误的微信号，微信号可在我-个人信息-微信号查看。" pattern="^[A-Za-z][\w\-]{5,19}$" placeholder="微信名" required="" id="weixin_name" name="weixin_name"/>
</div>
<div>
<input type="text" title="请输入正确的邮箱格式" pattern="^([0-9A-Za-z\-_\.]+)@([0-9a-z]+\.[a-z]{2,3}(\.[a-z]{2})?)$" placeholder="常用邮箱" required="" id="mail" name="mail"/>
</div>
<div>
<input type="submit" value="绑定" />
<input type="reset" value="重置" />
</div>
<script language="javascript">
function checkForm()
{
	var weixin_name_v = document.getElementById("weixin_name").value.trim();
	if(weixin_name_v!=""){
		var reg_wx = /^[A-Za-z][\w\-]{5,19}$/;
		if(!reg_wx.test(weixin_name_v)){
			alert("您的输入包含了错误的微信号，微信号可在我-个人信息-微信号查看");
			return false;
		}
	}else{
		alert("请输入微信号！");
		return false;
	}
	var mail_v = document.getElementById("mail").value.trim();
	if(mail_v!=""){
		var reg_m = /^([0-9A-Za-z\-_\.]+)@([0-9a-z]+\.[a-z]{2,3}(\.[a-z]{2})?)$/;
		if(!reg_m.test(mail_v)){
			alert("您输入了错误的邮箱格式，请重试！");
			return false;
		}
		return true;
	}else{
		alert("请输入常用邮箱！");
		return false;
	}
}
</script>
';
echo '</section></div></form>';
echo '</body></html>';
?>