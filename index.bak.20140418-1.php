<?php
/**
  * @Author Dawei Ma
  * @Date 2014-04-11
  * @Function 跑赢CPI微信自动回复后台，提供精选文章、豆瓣专栏、卖艺系列、精选投资组合、账户管理功能
  */
require_once './databases.php';
//define your token
define("TOKEN", "6z7nm9dq");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
		$this->responseMsg();
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
          	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            switch($RX_TYPE)
            {
                case "text":
                    $resultStr = $this->handleText($postObj);
                    break;
                case "event":
                    $resultStr = $this->handleEvent($postObj);
                    break;
                default:
                    $resultStr = "Unknow msg type: ".$RX_TYPE;
                    break;
            }
            echo $resultStr;           
        }else {
        	echo "";
        	exit;
        }
    }

    public function handleText($postObj)
    {
        $fromUsername = $postObj->FromUserName;
        $keyword = trim($postObj->Content);
        $msg_type = $postObj->MsgType;
        $time = time(); 
        $nowtime = date("Y-m-d G:i:s");
        //0-5分钟之内没输入特定数字
        $user_flag = 0;
        $father_msg = '';
        if(!empty( $keyword )){
                $msgType = "text";
                //判断在3分钟之内是否输入特定数字
                $select_sql="SELECT * FROM message WHERE from_user='$fromUsername' order by create_time desc limit 1";
                $select_res=_select_data($select_sql);
                $rows=mysql_fetch_assoc($select_res);
                if($rows[id] <> ''){
                    //判断是否在3分钟之内
                    $day=floor((strtotime($nowtime)-strtotime($rows[create_time]))/86400);
                    $hour=floor((strtotime($nowtime)-strtotime($rows[create_time]))%86400/3600);
                    $minute=floor((strtotime($nowtime)-strtotime($rows[create_time]))%86400%3600/60);
                    $second=floor((strtotime($nowtime)-strtotime($rows[create_time]))%86400%3600%60);
                    if($day==0&&$hour==0&&$minute<=3){
                        if($rows[content] == '5' || $rows[father_msg] == '5'){
                            $father_msg = '5';
                            $contentStr = $this->userManager($fromUsername,$keyword);
                            if($contentStr=='exit'){
                                $user_flag = 1;
                                $father_msg = '';
                            }
                            if($contentStr=='0'){
                                $survey_url="http://jferic.com/weixin/survey.php?from_user='$fromUsername'";
                                $record=array(
                                        'title' =>'跑赢CPI客户风险测评[内测中]',
                                        'description' =>'此测评仅作客户投资参考',
                                        'picUrl' => 'http://jferic.com/weixin/img/survey.jpg',
                                        'url' =>$survey_url
                                );
                                $resultStr = $this->responseNews($postObj,$record);
                            }else if($contentStr <> 'exit'){
                                $resultStr = $this->responseText($postObj,$contentStr);
                            }
                            
                        }else{
                            $user_flag = 1;
                        }
                    }else{
                        $user_flag = 1;
                    }
                }
                if(($keyword == '1') && $user_flag){
                    $record[0]=array(
                        'title' =>'那些非余额宝们',
                        'description' =>'许多人在思考一个问题：有没有预期收益比余额宝高但风险不高的理财工具？',
                        'picUrl' => 'http://mmbiz.qpic.cn/mmbiz/cHasHFSHJic5Ny9rRlia3VRhCAYbxy2FHcCN52qce03KNlZ6SfXKHYYa3Dyu3LEINBI9ZqmmuXIeSE9JaPnEoPNQ/0',
                        'url' =>'http://mp.weixin.qq.com/s?__biz=MjM5MDg2MTA2MQ==&mid=200090986&idx=1&sn=aa3eec8fca6d578f4eef2377867e816a#rd'
                    );
                    $record[1]=array(
                        'title' =>'优选偏股型基金池子',
                        'description' =>'近期，我在国内所有成立满5年以上的偏股型基金中，根据长期、中期业绩排名，长期、中期业绩稳定性，基金经理过往业绩、基金规模大小、基金风格等因素，构建了一个优选基金池。',
                        'picUrl' => 'http://mmbiz.qpic.cn/mmbiz/cHasHFSHJic5Ny9rRlia3VRhCAYbxy2FHcYhyiaN8icktQI9RG0at0k8r7oXpHibfqQ4ypGPXricicuic3d6fQia5Aj00dA/0',
                        'url' =>'http://mp.weixin.qq.com/s?__biz=MjM5MDg2MTA2MQ==&mid=200090986&idx=3&sn=e7c9d85fa9f323f8cb0201efc6618a5a#rd'
                    );
                    $record[2]=array(
                        'title' =>'那池子怎么样了？',
                        'description' =>'池子发布于去年11月15日，我想有必要回顾一下他的表现。',
                        'picUrl' => 'http://mmbiz.qpic.cn/mmbiz/cHasHFSHJic4ODYDFtvuJHI6FGA0U1XWxiap17Gud3Mkib1GRulg6BauRjTs9dl3yXBOFwdPsg6RkQGDW6xsGw2Zg/0',
                        'url' =>'http://mp.weixin.qq.com/s?__biz=MjM5MDg2MTA2MQ==&mid=200018945&idx=1&sn=de65972d6b191629b677be495d32169a#rd'
                    );
                    $resultStr = $this->response_multiNews($postObj,$record);
                }elseif (($keyword == '2') && $user_flag) {
                    $record=array(
                                        'title' =>'跑赢CPI豆瓣专栏',
                                        'description' =>'本专栏将努力以故事性的叙述方式，将专业的金融知识讲解变得生动有趣，让读者在趣味性中理解常见的金融投资工具，学习投资理财的方法和技巧，掌握理财规划的核心要领，从而实现财务的自由、自主、自在。',
                                        'picUrl' => 'https://s.doubanio.com/view/ark_column_cover/retina/public/6839.jpg?v=1388043644.0',
                                        'url' => 'http://read.douban.com/column/6839/'
                                );
                    $resultStr = $this->responseNews($postObj,$record);
                }elseif (($keyword == '3') && $user_flag) {
                    $record[0]=array(
                        'title' =>'【卖艺系列】“卖艺”完美收官',
                        'description' =>'截止4月11日收盘，“卖艺”品种11南钢债债券净价89.95元，净价收益加上期间利息收益，总收益率达11.3%，年化收益率51.6%。',
                        'picUrl' => 'http://mmbiz.qpic.cn/mmbiz/cHasHFSHJic4UxHDJjUHicRYOkJ4w2YojibHPWDk5IKicDeryLhx9wk7vOicib2Nv3wajuAA2dRkogRKu4Ksygd3YsvQ/0',
                        'url' =>'http://mp.weixin.qq.com/s?__biz=MjM5MDg2MTA2MQ==&mid=200118204&idx=1&sn=6034bf72507b8dedad9535f45bc28748#rd'
                    );
                    $record[1]=array(
                        'title' =>'“卖艺”大丰收，债市机会值得挖掘',
                        'description' =>'如果按照1月23日收盘价格买入该债券，截止今天3月31日，期间收益率为5%，年化收益率为26%。',
                        'picUrl' => 'http://mmbiz.qpic.cn/mmbiz/cHasHFSHJic657CJgwn0ZFjdN8Ric2WOLNwZYSVgdakX2wE6Awub2nclRibny6qNLXu1qST3BJ8iaSbpZXFtJsCbeg/0',
                        'url' =>'http://mp.weixin.qq.com/s?__biz=MjM5MDg2MTA2MQ==&mid=200090990&idx=3&sn=96ee4fe18c476e0cfea41d92319432fc#rd'
                    );
                    $record[2]=array(
                        'title' =>'“卖艺”回顾',
                        'description' =>'1月22日发布的“卖艺”，引来不少童鞋围观。眨眼间已经过去一个月，我觉得有必要回顾一下这个债券的表现',
                        'picUrl' => 'http://mmbiz.qpic.cn/mmbiz/cHasHFSHJic6icVpcEKPxvuMAf3MNpibpqdf8mu9TFQvArQTKtzqUwYNmF2RyvoiczVrXwm0m0KliblSbYFKePWXdmQ/0',
                        'url' =>'http://mp.weixin.qq.com/s?__biz=MjM5MDg2MTA2MQ==&mid=200035658&idx=1&sn=23c6809adc38616eec1d53889784032e#rd'
                    );
                    $record[3]=array(
                        'title' =>'卖艺（长期有效）',
                        'description' =>'市场情绪失控的时候，就是机会来的时候。天上其实是会掉馅饼的，只是看你能否发现罢了。',
                        'picUrl' => 'http://mmbiz.qpic.cn/mmbiz/cHasHFSHJic657CJgwn0ZFjdN8Ric2WOLNosfwovUJ2p2yZuNDPxou1N7XU1NNDkiboTj7pBiahhxuFtZt3nspQDsQ/0',
                        'url' =>'http://mp.weixin.qq.com/s?__biz=MjM5MDg2MTA2MQ==&mid=200090990&idx=2&sn=bb532389888ffdef84e1d72a44e7bff9#rd'
                    );
                    $resultStr = $this->response_multiNews($postObj,$record);
                }elseif (($keyword == '4') && $user_flag) {
                    $record[0]=array(
                        'title' =>'【业绩回顾】“ 稳健宝”20140407~20140413周报',
                        'description' =>'稳健宝本周上涨1.122%，4月1日发布至今累计上涨1.75%，年化回报45.34%。',
                        'picUrl' => 'http://mmbiz.qpic.cn/mmbiz/cHasHFSHJic4UxHDJjUHicRYOkJ4w2Yojib5SOsetXtMtZgibibefJaxwicicqpkUJmiaCHss2S0dyfv02YFE92uD1VEUQ/0',
                        'url' =>'http://mp.weixin.qq.com/s?__biz=MjM5MDg2MTA2MQ==&mid=200118208&idx=1&sn=1d3286bab4290f5e6a5547c27b10822a#rd'
                    );
                    $record[1]=array(
                        'title' =>'【业绩回顾】“ 稳健宝”20140401~20140407周报',
                        'description' =>'2014年4月1日，我们正式发布稳健宝，从此宝宝的江湖中，多了一位特点鲜明的宝宝。',
                        'picUrl' => 'http://mmbiz.qpic.cn/mmbiz/cHasHFSHJic4UxHDJjUHicRYOkJ4w2Yojib5SOsetXtMtZgibibefJaxwicicqpkUJmiaCHss2S0dyfv02YFE92uD1VEUQ/0',
                        'url' =>'http://mp.weixin.qq.com/s?__biz=MjM5MDg2MTA2MQ==&mid=200118210&idx=1&sn=aa140e9e55c6ad4becdf62aa1a3ec461#rd'
                    );
                    $record[2]=array(
                        'title' =>'“稳健宝”正式发布',
                        'description' =>'认真的，今天我们正式发布跑赢CPI微信公众号“稳健宝”模拟投资组合。',
                        'picUrl' => 'http://mmbiz.qpic.cn/mmbiz/cHasHFSHJic50DTCpndq4z3OyrX3yrzX62ysrMibsob1iaebczso2StxqbwvXia8iaMJJMrATib9WuLZGcNteAiasJ4Gg/0',
                        'url' =>'http://mp.weixin.qq.com/s?__biz=MjM5MDg2MTA2MQ==&mid=200097432&idx=1&sn=9e943aafa10572495a4663967d92e696#rd'
                    );
                    $resultStr = $this->response_multiNews($postObj,$record);
                }elseif (($keyword == '5') && $user_flag) {
                    $contentStr = sprintf("3分钟内不操作自动退出会员服务\n- 回复[绑定+微信号+邮箱]绑定帐户，可享受邮箱推送投资标的文章[注：中间是两个+号]\n- 回复[查询]查询帐户信息\n- 回复[解绑]解绑账户\n- 回复[测评]进行风险测评-内测中\n- 回复[风险]查看风险测评结果分析\n- 回复[退出]退出会员服务");
                    $resultStr = $this->responseText($postObj,$contentStr);
                }else if($user_flag){
                    $contentStr = sprintf("功能列表[回复数字]\n1 精选文章\n2 豆瓣专栏\n3 卖艺系列\n4 精选投资组合\n5 会员服务");
                    $resultStr = $this->responseText($postObj,$contentStr);
                }
                echo $resultStr;
                //保存用户发送的每一条消息，用来获取用户发送指令的上下文
                $insert_sql="INSERT INTO message(from_user, msg_type, content, create_time, father_msg) VALUES('$fromUsername','$msg_type','$keyword','$nowtime','$father_msg')";
                $res = _insert_data($insert_sql);
            }else{
                echo "Input something...";
            }
    }

    public function userManager($fromUsername,$keyword){
        $keyword = trim($keyword,'[');
        $keyword = trim($keyword,']');
        if(strstr($keyword,"+")){
            $keywords = explode("+",$keyword);
        }elseif(strstr($keyword," ")){
            $keywords = explode(" ",$keyword);
        }elseif (strstr($keyword,"＋")) {
            $keywords = explode("＋",$keyword);
        }else{
            $keywords[0] = $keyword;
        }
        $nowtime = date("Y-m-d G:i:s");
        //判断是否已经绑定
        $select_sql="SELECT id from user WHERE from_user= '$fromUsername'";
        $res=_select_data($select_sql);
        $rows=mysql_fetch_array($res, MYSQL_ASSOC);
        if($rows[id] <> ''){
            $user_flag='y';          
        }
        switch (trim($keywords[0])) {
            case '绑定':
                if($user_flag <> 'y'){
                    if(trim($keywords[1]) <> '' && trim($keywords[2]) <> ''){
                        $keywords[1] = trim($keywords[1]);
                        $keywords[2] = trim($keywords[2]);
                        $email_pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
                        $weixin_pattern = "/^[A-Za-z][\w\-]{5,19}$/";
                        if (preg_match($weixin_pattern,$keywords[1])) {
                            if (preg_match($email_pattern,$keywords[2])) {
                                $insert_sql="INSERT INTO user(from_user, weixin_name, mail, create_time) VALUES('$fromUsername','$keywords[1]','$keywords[2]','$nowtime')";
                                $res = _insert_data($insert_sql);
                                if($res == 1){
                                    $contentStr = "绑定成功";
                                }elseif($res == 0){
                                    $contentStr = "绑定失败";
                                }
                            }else{
                                $contentStr = "您的输入包含了错误的邮箱格式，请检查！";
                            }
                        }else{
                            $contentStr = "您的输入包含了错误的微信号，微信号可在我-个人信息-微信号查看。";
                        }
                    }else{
                        $contentStr = "您的输入格式错误，回复[绑定+微信号+邮箱]绑定帐户。[注：微信号，不是微信昵称，可在我-个人信息-微信号查看，不用输入中括号，中间是两个半角英文的+号]";
                    }
                }else{
                    $contentStr = "您账户已绑定";
                }
                break;
            case '查询':
                $select_sql="SELECT * FROM user WHERE from_user= '$fromUsername'";
                $select_res=_select_data($select_sql);
                $rows=mysql_fetch_assoc($select_res);
                if($rows[id] <> ''){
                    $contentStr="微信ID:$rows[weixin_name]\n"."邮箱：$rows[mail]\n"."绑定时间：$rows[create_time]";
                }else{
                    $contentStr="您还未绑定账户，请先绑定，谢谢！";
                }
                break;
            case '解绑':
                $delete_sql="DELETE FROM user WHERE from_user= '$fromUsername'";
                $res = _delete_data($delete_sql);
                if($res == 1){
                    $contentStr = "解绑成功";
                }else{
                    $contentStr = "您还未绑定账户，请先绑定，谢谢！";
                }
                break;
            case '测评':
                $select_sql="SELECT * FROM user WHERE from_user= '$fromUsername'";
                $select_res=_select_data($select_sql);
                $rows=mysql_fetch_assoc($select_res);
                if($rows[id] <> ''){
                    $contentStr = '0';
                }else{
                    $contentStr="您还未绑定账户，请先绑定，谢谢！";
                }
                break;
            case '风险':
                $select_sql="SELECT * FROM user WHERE from_user= '$fromUsername'";
                $select_res=_select_data($select_sql);
                $rows=mysql_fetch_assoc($select_res);
                if($rows[id] <> ''){
                    if($rows[fx_score]<=20 && $rows[fx_score]>0){
                         $contentStr = "您的测评结果为保守型。以下是对您的分析描述：\n您的风险承担能力水平比较低，您关注资产的安全性远超于资产的收益性，所以低风险、高流动性的投资品种比较适合您，这类投资的收益相对偏低。";
                    }elseif ($rows[fx_score]>20 && $rows[fx_score]<=40){
                        $contentStr = "您的测评结果为稳健型。以下是对您的分析描述：\n您有比较有限的风险承受能力，对投资收益比较敏感，期望通过长期且持续的投资获得高于平均水平的回报。所以中低等级风险收益的投资品种比较适合您，适当回避风险的同时保证收益。";
                    }elseif ($rows[fx_score]>40 && $rows[fx_score]<=60) {
                        $contentStr = "您的测评结果为平衡型。以下是对您的分析描述：\n您有一定的风险承受能力，对投资收益比较敏感，期望通过长期且持续的投资获得高于平均水平的回报，通常更注重长期限内的平均收益。所以中等风险收益的投资品种比较适合您，回避风险的同时有一定的收益保证。";
                    }elseif ($rows[fx_score]>60 && $rows[fx_score]<=80) {
                        $contentStr = "您的测评结果为成长型。以下是对您的分析描述：\n您有中高的风险承受能力，愿意承担可预见的投资风险去获取更多的收益。所以中高等级的风险收益投资品种比较适合您，以一定的可预见风险换取超额收益。";
                    }elseif ($rows[fx_score]>80 && $rows[fx_score]<=100) {
                        $contentStr = "您的测评结果为进取型。以下是对您的分析描述：\n您有较高的风险承受能力，是富有冒险精神的积极型选手。在投资收益波动的情况下，仍然保持积极进取的投资理念。短期内投资收益的下跌被您视为加注投资的利好机会。您适合从事灵活、风险与报酬都比较高的投资，不过要注意不要因一时的高报酬获利而将全部资金投入高风险操作，务必做好风险管理与资金调配工作。";
                    }elseif ($rows[fx_score]==0) {
                        $contentStr = "您还没做风险测评，请回复[测评]开始做题吧，谢谢！";
                    }else {
                        $contentStr = "系统可能出错了，请在微信公众号留言给跑赢CPI运营人员，谢谢！[ERROR_CODE=3]";
                    }
                }else{
                    $contentStr="您还未绑定账户，请先绑定，谢谢！";
                }
                break;
            case '退出':
                $contentStr = 'exit';
                break;
            default:
                $contentStr = sprintf("3分钟内不操作自动退出会员服务\n- 回复[绑定+微信号+邮箱]绑定帐户，可享受邮箱推送投资标的文章[注：中间是两个+号]\n- 回复[查询]查询帐户信息\n- 回复[解绑]解绑账户\n- 回复[测评]进行风险测评-内测中\n- 回复[风险]查看风险测评结果分析\n- 回复[退出]退出会员服务");
                break;
        }
        return $contentStr;
    }

    public function responseNews($object,$newsContent){
        $newsTplHead = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>1</ArticleCount>
                <Articles>";
        $newsTplBody = "<item>
                <Title><![CDATA[%s]]></Title> 
                <Description><![CDATA[%s]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>";
        $newsTplFoot = "</Articles>
                <FuncFlag>0</FuncFlag>
                </xml>";
        $header = sprintf($newsTplHead, $object->FromUserName, $object->ToUserName, time());
        $title = $newsContent['title'];
        $desc = $newsContent['description'];
        $picUrl = $newsContent['picUrl'];
        $url = $newsContent['url'];
        $body = sprintf($newsTplBody, $title, $desc, $picUrl, $url);
        $FuncFlag = 0;
        $footer = sprintf($newsTplFoot, $FuncFlag);
        return $header.$body.$footer;
    }

    public function response_multiNews($object,$newsContent){
        $newsTplHead = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>%s</ArticleCount>
                <Articles>";
        $newsTplBody = "<item>
                <Title><![CDATA[%s]]></Title> 
                <Description><![CDATA[%s]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>";
        $newsTplFoot = "</Articles>
                <FuncFlag>0</FuncFlag>
                </xml>";
        $bodyCount = count($newsContent);
        $bodyCount = $bodyCount < 10 ? $bodyCount : 10;
        $header = sprintf($newsTplHead, $object->FromUserName, $object->ToUserName, time(), $bodyCount);
        foreach($newsContent as $key => $value){
            $body .= sprintf($newsTplBody, $value['title'], $value['description'], $value['picUrl'], $value['url']);
        }
        $FuncFlag = 0;
        $footer = sprintf($newsTplFoot, $FuncFlag);
        return $header.$body.$footer;
    }

    public function handleEvent($object)
    {
        $contentStr = "";
        switch ($object->Event)
        {
            case "subscribe":
                $contentStr = "感谢您关注跑赢CPI\n\n以全市场的投资视角，努力成为投资价值的发现者\n
以稳健的投资风格，追求低风险前提下的中高收益\n\n回复以下数字获取文章\n1 精选文章\n2 豆瓣专栏\n3 卖艺系列\n4 精选投资组合\n5 会员服务";
                break;
            default :
                $contentStr = "Unknow Event: ".$object->Event;
                break;
        }
        $resultStr = $this->responseText($object, $contentStr);
        return $resultStr;
    }
    
    public function responseText($object, $content, $flag=0)
    {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>%d</FuncFlag>
                    </xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
        return $resultStr;
    }
		
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>
