<?php
/**
  * @Author Dawei Ma
  * @Date 2014-05-30
  * @Function 跑赢CPI菜单生成
  */
$access_token = "I5KCEkQQmptheI1sCZTP7ZQoGeCAG2k9BTat4j-Zp8R32gatLqmsp09-ZxSxkAda_Oe9Ly-tfHgPRVGQO3EUOw";

$jsonmenu = '{
      "button":[
      {
            "type":"click",
            "name":"精选文章",
            "key":"m_jxwz"
       },
       {
           "name":"会员服务",
           "sub_button":[
            {
               "type":"click",
               "name":"注册会员",
               "key":"m_zchy"
            },
            {
               "type":"click",
               "name":"查询信息",
               "key":"m_cxxx"
            },
            {
                "type":"click",
                "name":"删除帐号",
                "key":"m_sczh"
            }]

       }]
 }';


$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
$result = https_request($url, $jsonmenu);
var_dump($result);

function https_request($url,$data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

?>