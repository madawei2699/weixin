<?php
/**
  * @Author Dawei Ma
  * @Date 2014-04-18
  * @Function 跑赢CPI客户绑定保存数据功能
  */
require_once './databases.php';
echo '<!DOCTYPE HTML>';
echo '<html><head><meta charset = "UTF-8" />';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '
<style>
        html, body
        {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .wrap
        {
        	width: 450px;
            height: 100%;
            display: -webkit-box;
            -webkit-box-align: center;
            -webkit-box-pack: center;
            overflow: auto;
        }
        .btn
        {
        	width: 100px;
        	line-height: 30px;
        	font-weight: bold;
        	margin: 0 35px 20px 0;
        }
        .content{
        	font-size: 18px;
        	width: 450px;
        }
        .div_bt{
        	text-align:center;
        	vertical-align:middle;
        	padding-top:6px;
        }
        h1 {
        	font-size: 32px;
        }
        h3 {
        	font-size: 20px;
        }
    </style>
';
echo '</head><body>';
echo '<div class="">';
echo '<h1>跑赢CPI会员绑定结果</h1>';
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // all strings should be escaped
    // and it should be done after connecting to DB
    $nowtime = date("Y-m-d G:i:s");
    $from_user = mysql_real_escape_string($_POST['from_user']);
    if($from_user <> ''){
    //解绑用户
    $delete_sql="DELETE FROM user WHERE from_user= '$from_user'";
    $res = _delete_data($delete_sql);

    $weixin_name = mysql_real_escape_string($_POST['weixin_name']);
    $mail = mysql_real_escape_string($_POST['mail']);

    //绑定用户
    $insert_sql="INSERT INTO user(from_user, weixin_name, mail, create_time) VALUES('$from_user','$weixin_name','$mail','$nowtime')";
    $res = _insert_data($insert_sql);
    echo '<div class="content">';
    if($res == 1){
        echo '<p>绑定成功</p>';
    }elseif($res == 0){
        echo '<p>绑定失败</p>';
    }
    echo '</div>';  
    }
}
echo '</div></body></html>';
?>
