<?php
/**
  * @Author Dawei Ma
  * @Date 2014-04-13
  * @Function 跑赢CPI[销魂宝]风险测评页面
  */
require_once './databases.php';
$from_user = trim($_GET["from_user"],'\'');
/*
<SCRIPT language="JavaScript">      
function QueryGET(TheName){      
	var urlt = window.location.href.split("?");      
	var gets = urlt[1].split("&");      
	for(var i=0;i<gets.length;i++){      
	  var get = gets[i].split("=");      
	   if(get[0] == TheName){      
	   var TheValue = get[1];      
	   break;      
	   }      
	}      
	return TheValue;      
}
var f_u_button = document.getElementById("from_user");
f_u_button.value = QueryGET("from_user");
</SCRIPT>
*/
echo '<!DOCTYPE HTML>';
echo '<html><head><meta charset = "UTF-8" />';
echo '<link rel="stylesheet" type="text/css" href="css/survey.css" />';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
// echo '
// <style>
//         html, body
//         {
//             height: 100%;
//             width: 100%;
//             margin: 0;
//             padding: 0;
//         }
//         .form-horizontal .control-group
//         {
//             margin-bottom: 16px;
//         }
//         .wrap
//         {
//             height: 100%;
//             display: -webkit-box;
//             -webkit-box-align: center;
//             -webkit-box-pack: center;
//             overflow: auto;
//         }
//         .main
//         {
//             width: 450px;
//         }
//         .btn
//         {
//         	width: 100px;
//         	line-height: 30px;
//         	font-weight: bold;
//         	margin: 0 35px 20px 0;
//         }
//         .content{
//         	font-size: 18px;
//         }
//         .div_bt{
//         	text-align:center;
//         	vertical-align:middle;
//         	padding-top:6px;
//         }
//         h1 {
//         	font-size: 32px;
//         }
//         h3 {
//         	font-size: 20px;
//         }
//     </style>
// ';
echo '</head><body>';
echo '<div class="wrap">';
echo '<form id="survey_form" action="save_survey_data.php"  method="post" onsubmit="return checkForm()">';
echo '<input type="hidden" id="from_user" name="from_user" value="'.$from_user.'"/>';
echo '<h1>跑赢CPI[销魂宝]风险测评</h1>';
$q_select_sql="SELECT * FROM question";
$q_select_res=_select_data($q_select_sql);
$i = 0;
while ($q_rows=mysql_fetch_assoc($q_select_res)) {
	$i++;
	echo '<h3>';
	echo $q_rows["question_id"];
	echo '、';
	echo $q_rows["content"];
	echo '</h3>';
	$select_sql="SELECT * FROM options WHERE question_id='$i'";
	$select_res=_select_data($select_sql);
	while ($rows=mysql_fetch_assoc($select_res)) {
		echo '<div class="content">';
		echo '<input type="radio" id="'.$rows["question_id"].$rows["choice"].'" name="'
		.$rows["question_id"].'"value="'.$rows["choice"].'">';
		echo '<label for="'.$rows["question_id"].$rows["choice"].'">';
		echo $rows["choice"].'、'.$rows["option_text"];
		echo '</label></div>';	
	}
}
echo '<div class="div_bt"><button class="btn" type="submit" id="ok">提 交</button><button class="btn" type="reset">重 置</button></div></form></div>';
echo '
<script language="javascript">
function checkForm()
{  
var flag = true;
var q_flag = false;
for (var i=0; i <'.$i.'; i++) { 
	q_flag = false;
	var allRadio = document.getElementsByName(""+i);
	for(var j = 0;j < allRadio.length;j++){
   		if(allRadio[j].checked){
   			q_flag = true;
   			break;
   		}
	}
	if(q_flag){
		continue;
	}
}
if (!(flag&&q_flag)){
	alert("有问题还没有被回答，请检查！");
	flag = false;
}
return flag;
}
</script>';
echo '</body></html>';
?>