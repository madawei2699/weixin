<?php
/**
  * @Author Dawei Ma
  * @Date 2014-04-13
  * @Function 跑赢CPI风险测评保存数据功能
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
echo '<h1>跑赢CPI[销魂宝]风险测评结果</h1>';
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // all strings should be escaped
    // and it should be done after connecting to DB
    $nowtime = date("Y-m-d G:i:s");
    $from_user = mysql_real_escape_string($_POST['from_user']);
	$score = 0;
	//删除用户原来的答案
	$delete_sql="DELETE FROM answer WHERE from_user= '$from_user'";
    $d_res = _delete_data($delete_sql);

    $q_select_sql="SELECT * FROM question";
	$q_select_res=_select_data($q_select_sql);

	while ($q_rows=mysql_fetch_assoc($q_select_res)) {
		$question_id = $q_rows["question_id"];
		$choice = mysql_real_escape_string($_POST[''.$question_id]);
		//查询用户选项分数
		$select_sql="SELECT * FROM options WHERE question_id = '$question_id' and choice = '$choice'";
        $select_res=_select_data($select_sql);
        $rows=mysql_fetch_assoc($select_res);
        $score += $rows["score"];
		$insert_sql="INSERT INTO answer(from_user, question_id, choice, create_time) VALUES('$from_user','$question_id','$choice','$nowtime')";
        $res = _insert_data($insert_sql);
	}
	//更新用户信息-风险测评分数
	$update_sql="UPDATE user SET fx_score = '$score' WHERE from_user = '$from_user'";
	$update_res=_update_data($update_sql);
	if($update_res == 1 || $update_res == 2){
		echo '<div class="content">';
		// if($score<=20){
		// 	echo '<p>您的测评结果为保守型。以下是对您的分析描述：</p>';
		// 	echo '<p>您的风险承担能力水平比较低，您关注资产的安全性远超于资产的收益性，所以低风险、高流动性的投资品种比较适合您，这类投资的收益相对偏低。</p>';
		// }elseif ($score>20 && $score<=40){
		// 	echo '<p>您的测评结果为稳健型。以下是对您的分析描述：</p>';
		// 	echo '<p>您有比较有限的风险承受能力，对投资收益比较敏感，期望通过长期且持续的投资获得高于平均水平的回报。所以中低等级风险收益的投资品种比较适合您，适当回避风险的同时保证收益。</p>';
		// }elseif ($score>40 && $score<=60) {
		// 	echo '<p>您的测评结果为平衡型。以下是对您的分析描述：</p>';
		// 	echo '<p>您有一定的风险承受能力，对投资收益比较敏感，期望通过长期且持续的投资获得高于平均水平的回报，通常更注重长期限内的平均收益。所以中等风险收益的投资品种比较适合您，回避风险的同时有一定的收益保证。</p>';
		// }elseif ($score>60 && $score<=80) {
		// 	echo '<p>您的测评结果为成长型。以下是对您的分析描述：</p>';
		// 	echo '<p>您有中高的风险承受能力，愿意承担可预见的投资风险去获取更多的收益。所以中高等级的风险收益投资品种比较适合您，以一定的可预见风险换取超额收益。</p>';
		// }elseif ($score>80 && $score<=100) {
		// 	echo '<p>您的测评结果为进取型。以下是对您的分析描述：</p>';
		// 	echo '<p>您有较高的风险承受能力，是富有冒险精神的积极型选手。在投资收益波动的情况下，仍然保持积极进取的投资理念。短期内投资收益的下跌被您视为加注投资的利好机会。您适合从事灵活、风险与报酬都比较高的投资，不过要注意不要因一时的高报酬获利而将全部资金投入高风险操作，务必做好风险管理与资金调配工作。</p>';
		// }else {
		// 	echo '<p>系统可能出错了，请在微信公众号留言给跑赢CPI运营人员，谢谢！[ERROR_CODE=1]</p>';
		// }
        echo '<p>我们已经收录您的信息，会尽快联系您！</p>';
		echo '</div>';
	}else{
		echo '<p>系统可能出错了，请在微信公众号留言给跑赢CPI运营人员，谢谢！[ERROR_CODE=2]</p>';
	}
}
echo '</div></body></html>';
?>
