<?php
/**
 * Created by PhpStorm.
 * User: mlyang
 * Date: 2017/5/14
 * Time: 20:01
 */

//date("Y-m-d");
$host="112.74.124.145:3306";
$user="group5";
$password="group5";
$database="stockG5";
$conn=new mysqli($host, $user, $password,$database);

$query0="select * from indexIndustry where industry= ";

//$indus=array("专用机械","中成药","互联网","仓储物流","供气供热","元器件","乳制品","保险","全国地产","公路","其他商业","其他建材","农业综合","农药化肥","农用机械"
//,"出版业","化学制药","化工原料","化工机械","化纤","区域地产","医疗保健","半导体","商贸代理","商品城","啤酒"
//,"园区开发","塑料","多元金融","家居用品","家用电器","小金属","工程机械","广告包装","建筑施工","影视音像","房产服务","批发业"
//,"摩托车","文教休闲","新型电力");

$indus = array();
$q="SELECT * FROM indexIndustry  group by industry";
$r = $conn->query($q);
if ($r->num_rows > 0) {
    while ($r1 = $r->fetch_assoc()) {
        array_push($indus,$r1['industry']);
    }
}
else{
    echo "Error: " . $q . "<br>" . $conn->error;
    exit;
}
//print_r($indus);
for($i=0;$i<count($indus);$i++) {
    $query=$query0."'".$indus[$i]."'";
    $companies=0;
    $sumprice=0;
    $sumchange=0;
    $volume=0;
    $amount=0;

    $result = $conn->query($query);
    if ($result->num_rows > 0) {
// 输出每行数据
        while ($row = $result->fetch_assoc()) {
            $code = $row['code'];
            $name = $row['name'];
            $stock_query="select * from stockList where code= '".$code."'";
            $result1 = $conn->query($stock_query);
            if ($result1->num_rows < 0){
                echo "Error: " . $stock_query . "<br>" . $conn->error;
                exit;
            }
            else{
                $row1 = $result1->fetch_assoc();
                $date=date("Y-m-d");
                $companies=$result->num_rows;
                $sumprice+=$row1['trade'];
                $sumchange+=$row1['changepercent'];
                $volume+=$row1['volume'];
                $amount+=$row1['amount'];
            }
        }
    }
    else{
        echo "Error: " . $query . "<br>" . $conn->error;
        exit;
    }
    $avgprice=$sumprice/$companies;
    $avgchange=$sumchange/$companies;
    if($avgchange>=0) {
        $update = "update industryHistData set date= '".$date."',companies=".$companies.",avgprice=".$avgprice."
        ,avgchange=".$avgchange.",avgp_change=0,volume=".$volume.",amount=".$amount."
        where industry='".$indus[$i]."' ";
    }
    else{
        $update = "update industryHistData set date= ".$date.",companies=".$companies.",avgprice=".$avgprice."
        ,avgchange=0,avgp_change=".$avgchange.",volume=".$volume.",amount=".$amount."
        where industry='".$indus[$i]."' ";
    }
    $result_update = $conn->query($update);
    if($result_update!=TRUE){
        echo "Error: " . $update . "<br>" . $conn->error;
        exit;
    }
}