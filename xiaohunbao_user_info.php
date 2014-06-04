<!-- 
   @Author Dawei Ma
   @Date 2014-05-21
   @Function 跑赢CPI[销魂宝]客户统计表 -->
<!DOCTYPE HTML>
<html>
<head>
<meta charset = "UTF-8" />
<title>跑赢CPI[销魂宝]客户统计表</title>
</head>
<body>
<h1>跑赢CPI[销魂宝]客户统计表</h1>
<?php
require_once './databases.php';
class Table{
    var $table_array = array();
    var $headers = array();
    var $cols;
    function Table( $headers ){
        $this->headers = $headers;
        $this->cols = count ( $headers );
    }

    function addRow( $row ){
        if ( count ($row) != $this->cols )
            return false;
        array_push($this->table_array, $row);
        return true;
    }

    function addRowAssocArray( $row_assoc ) {
        if ( count ($row_assoc) != $this->cols )
            return false;
        $row = array();
        foreach ( $this->headers as $header ) {
            if ( ! isset( $row_assoc[$header] ))
                $row_assoc[$header] = " ";
            $row[] = $row_assoc[$header];
        }
        array_push($this->table_array, $row) ;
    }

    function output() {
         print "<pre>";
         foreach ( $this->headers as $header )
             print "<B>$header</B>  ";
         print "\n";
         foreach ( $this->table_array as $y ) {
             foreach ( $y as $xcell )
                 print "$xcell  ";
             print "\n";
         }
         print "</pre>";
     }
}

class HTMLTable extends Table{
     var $bgcolor;
     var $cellpadding = "2";
     function HTMLTable( $headers, $bg="#ffffff" ){
         Table::Table($headers);
         $this->bgcolor=$bg;
     }
     function setCellpadding( $padding ){
         $this->cellpadding = $padding;
     }
     function output(){
         print "<table cellpadding=\"$this->cellpadding\" border=1 style=border-collapse:collapse>";
         foreach ( $this->headers as $header )
             print "<td bgcolor=\"$this->bgcolor\"><b>$header</b></td>";
         foreach ( $this->table_array as $row=>$cells ) {
             print "<tr>";
             foreach ( $cells as $cell )
                 print "<td bgcolor=\"$this->bgcolor\">$cell</td>";
             print "</tr>";
         }
         print "</table>";
     }
}

$q_select_sql="SELECT * FROM user WHERE fx_score<0 order by fx_score desc";
$q_select_res=_select_data($q_select_sql);
$i=0;
$test = new HTMLTable( array("序号","微信ID","邮箱","是否要","投资经验","投资态度","投资金额","期望收益","投资期限","索要时间","分数"));
$test->setCellpadding( 7 );
while ($q_rows=mysql_fetch_assoc($q_select_res)) {
    $row=array();
    $i++;
    $select_sql="SELECT * FROM message WHERE from_user='$from_user' and content='我要销魂宝'";
    $select_res=_select_data($select_sql);
    $rows=mysql_fetch_assoc($select_res);
    if($rows[id] <> ''){
        $isOK="是";
    }else{
        $isOK="否";
    }
    array_push($row, $i);
    $wexin_id=$q_rows["weixin_name"];
    array_push($row, $wexin_id);
    $email=$q_rows["mail"];
    array_push($row, $email);
    array_push($row, $isOK);
    $from_user=$q_rows["from_user"];
    $select_sql="SELECT option_text FROM answer,options WHERE from_user='$from_user' and answer.question_id=options.question_id and answer.choice=options.choice order by answer.question_id asc";
    $select_res=_select_data($select_sql);
    $q_array= array();
    while ($rows=mysql_fetch_assoc($select_res)) {
        array_push($row, $rows[option_text]);
    }
    $select_sql="SELECT * FROM message WHERE from_user='$from_user' and content='销魂宝快到碗里来' order by create_time desc limit 1";
    $select_res=_select_data($select_sql);
    $rows=mysql_fetch_assoc($select_res);
    $create_time=$rows["create_time"];
    array_push($row, $create_time);
    array_push($row, $q_rows["fx_score"]);
    $test->addRow($row);
}
$test->output();
?>
</body>
</html>