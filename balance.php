<?php
require_once 'comp/vendor/autoload.php';
require_once 'functions.php';

$con = mysqli_connect("localhost",DB_USER,DB_PASS,DB_NAME);
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
$mpdf->Output('balancesheet_'.date('M_Y_d'),'I');

?>