<?php
require_once 'comp/vendor/autoload.php';
require_once 'functions.php';

function ddecodeitems($str){
    $rt=explode(",",str_replace("{","",str_replace("}","",str_replace("[","",str_replace("]","",$str)))));
    $ar=array_chunk($rt,3);
    $total=0;
    $st='';
        foreach($ar as $a){
            $st.='<tr><td>'.ucwords(trim(explode(":",$a[0])[1],'"')).'</td><td> '.trim(explode(":",$a[1])[1],'"').'</td><td> '.trim(explode(":",$a[2])[1],'"').'/=</td><td>'.trim(explode(":",$a[1])[1],'"')*trim(explode(":",$a[2])[1],'"').'/=</td></tr>';
            $total=$total+trim(explode(":",$a[1])[1],'"')*trim(explode(":",$a[2])[1],'"');
        }
        $st.='<tr style="border-bottom:2px solid;border-top:1px solid;"><td colspan="3" style="text-align:center;font-weight:600;">Total Cost</td><td>'.$total.'/=</td></tr>';
        return trim($st);
        
     }
$con = mysqli_connect("localhost",DB_USER,DB_PASS,DB_NAME);
if(isset($_GET['balancesheet'])){
$mpdf= new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
$data='<table style="width:90%;margin:0 auto">
<tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:20px;">
    <td colspan="2" style="text-align:center;font-weight:bold;">
    <div style="height:60px;width:80px;">
    <img src="assets/img/logo.png" style="height:60px;width:80px;"/></div><br>
    Golden Vision Empowerment Organization<br>
        Balance Sheet as at '.date('d-m-Y',time()).'<br></td></tr>
<tr><td colspan="2" style="text-align:center;font-weight:bold;">Assets</td></tr>';
$allassets=mysqli_query($con,"SELECT `ca`.* FROM `accounttypes` AS `at` INNER JOIN `chartsofaccount` AS `ca` ON `at`.`id`=`ca`.`category` WHERE `at`.`name` LIKE '%asset%'");
$totalasset=0;
foreach($allassets as $asset){
$totalasset+=$asset['balance'];
$data.='<tr><td>'.$asset['name'].'</td><td>'.$asset['balance'].'</td></tr>';
}
$allrevenues=mysqli_query($con,"SELECT `ca`.* FROM `accounttypes` AS `at` INNER JOIN `chartsofaccount` AS `ca` ON `at`.`id`=`ca`.`category` WHERE `at`.`name` LIKE '%revenue%'");

foreach($allrevenues as $revenue){
$totalasset+=$revenue['balance'];
$data.='<tr><td>'.$revenue['name'].'</td><td>'.$revenue['balance'].'</td></tr>';
}
$data.='<tr><td style="text-align:center;border-top:1px solid;border-bottom:1px solid;font-weight:bold;">Total</td><td style="border-top:1px solid;border-bottom:1px solid;font-weight:bold;">'.$totalasset.'</td></tr>
<tr><td colspan="2" style="text-align:center;font-weight:bold;">Liabilities And Shareholders` Equity</td></tr>';
$allliabil=mysqli_query($con,"SELECT `ca`.* FROM `accounttypes` AS `at` INNER JOIN `chartsofaccount` AS `ca` ON `at`.`id`=`ca`.`category` WHERE `at`.`name` LIKE '%liabilit%'");
$totalliab=0;
foreach($allliabil as $liabil){
$totalliab=+$liabil['balance'];
$data.='<tr><td>'.$liabil['name'].'</td><td>'.$liabil['balance'].'</td></tr>';
}
$allequity=mysqli_query($con,"SELECT `ca`.* FROM `accounttypes` AS `at` INNER JOIN `chartsofaccount` AS `ca` ON `at`.`id`=`ca`.`category` WHERE `at`.`name` LIKE '%equit%'");
foreach($allequity as $equity){
$totalliab+=$equity['balance'];
$data.='<tr><td>'.$equity['name'].'</td><td>'.$equity['balance'].'</td></tr>';
}
$allexpenses=mysqli_query($con,"SELECT `ca`.* FROM `accounttypes` AS `at` INNER JOIN `chartsofaccount` AS `ca` ON `at`.`id`=`ca`.`category` WHERE `at`.`name` LIKE '%expense%'");

foreach($allexpenses as $expense){
$totalliab+=$expense['balance'];
$data.='<tr><td>'.$expense['name'].'</td><td>'.$expense['balance'].'</td></tr>';
}

$data.='<tr style=""><td style="text-align:center;border-top:1px solid;border-bottom:1px solid;font-weight:bold;">Total</td><td style="border-top:1px solid;border-bottom:1px solid;font-weight:bold;">'.$totalliab.'</td></tr><tr style="line-height:30px;padding:5px;"><td colspan="2" style="text-align:right;"></td></tr>';
$data.='</table>';
$mpdf->text_input_as_HTML=true;
$mpdf->WriteHTML($data);
$mpdf->Output('balancesheet_'.date('M_Y_d'),'D');
}
if(isset($_GET['orderid'])){
$style=file_get_contents('docs/bootstrap.min.css');
$mpdf= new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
$data='<div style="max-width:800px;width:95%;margin:0 auto;">
<div style="font-size:23px;color:;width:100%;text-align:center;"><h4> Goods Delivery Note</h4></div>
<div style="height:80px;width:96%;background:#191970;text-align:center;"> <img src="assets/img/logo.png" style="height:60px;width:80px;"/></div>';
    $allorders=mysqli_query($con,"SELECT `po`.*,`v`.`name`,`v`.`address`,`v`.`contact`,`v`.`email` FROM `purchase_orders` AS `po` INNER JOIN `vendors` AS `v` ON `v`.`id`=`po`.`vendor` WHERE `po`.`id`=1");
    foreach($allorders as $order){
        $mm=strlen($order['email'])>4?$order['email']:'-----';
    $data.='<div style="display:flex;flex-direction:row;font-size:13px;justify-content:space-between;font-family:cambria;width:100%;">
    <div style="min-width:300px;width:48%;float:left;">
    <span style="">Golden Vision Empowerment Program</span><br>
    Address: P.O. Box 75- 10218 Kangari<br>
    Email: goldenvep@gmail.com</br>
    Email2: info@goldenvision.or.ke<br>
    Phone: +254 716 639 658<br>
    Phone2: +254 726 778 662<br></div><div style="min-width:300px;width:48%;;float:right;">
    LPO Number: '.$order['poid'].'<br>
    Supplier:'.ucwords($order['name']).'<br>
    Email: '.$mm.'<br>
    Address: '.ucwords($order['address']).'<br>
    Contact: '.$order['contact'].'<br>
    Order On: '.date('M/d/Y ',$order['time']).'<br>
    </div></div><div style="min-height:300px;width:100%;margin-top:20px;">';
    $data.='<table class="table-striped" style="width:99%;margin:0 auto;margin-top:10px;min-height:100px;height:1">
    <tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Item</td><td>Quantity</td><td>Unit Cost</td><td>Total cost</td>'.ddecodeitems($order['items']);
        }
    $data.='</table></div>
    <div style="width:100%;display:flex;flex-direction:row;margin-top:20px;">
    <div style="width:50%;float:left;">
    Sign: ____________________________<br>
    Date:_____________________________<br>
    </div><div style="width:50%;float:right;">
    Sign:______________________________ <br>
    Date:______________________________<br>
    </div></div>
    <div style="width:100%;display:flex;flex-direction:row;margin-top:10px;margin-bottom:20px;">
    <button class="btnn" style="color:white;width:fit-content;background:green;float:right;" onclick="confirmdelivery('.$order['id'].')">Receive</button>
    </div></div>';
    //$mpdf->WriteHTML($style,\Mpdf\HTMLParserMode::HEADER_CSS);
    $mpdf->WriteHTML($data,\Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->text_input_as_HTML=true;
    $mpdf->Output($order['poid'],'D');
    }
    ?>
?>