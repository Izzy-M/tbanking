
<?php
require_once 'comp/vendor/autoload.php';
require_once 'functions.php';

$con = mysqli_connect("localhost",DB_USER,DB_PASS,DB_NAME);

//$order=clean($_GET['orderid']);

$mpdf= new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
$data='<div style="max-width:500px;width:95%;margin:0 auto;">
    <div style="font-size:23px;color:;width:100%;text-align:center;"><h4> Goods Delivery Note</h4></div>
    <div style="text-align:right;font-size:16px;color:;width:100%;text-align:right;"><h4> Goods Delivery Note</h4></div>';
    $allorders=mysqli_query($con,"SELECT `po`.*,`v`.`name`,`v`.`address`,`v`.`contact`,`v`.`email` FROM `purchase_orders` AS `po` INNER JOIN `vendors` AS `v` ON `v`.`id`=`po`.`vendor` WHERE `po`.`id`=1");
    foreach($allorders as $order){
    $data.='<div style="justify-content:space-between;font-size:13px;font-family:cambria;"><div style="min-width:200px;width:48%;margin 0px auto;float:left;">
    Golden VEP<br>
    P.O. Box 75- 10218 Kangari<br>
    Email:goldenvep@gmail.com/<br>
    info@goldenvision.or.ke<br>
    Phone:+254 716 639 658<br>
    +254 726 778 662<br></div><div style="width:48%;margin:0 auto;float:right;">
    LPO Number: '.$order['poid'].'<br>
    Supplier:'.ucwords($order['name']).'<br>
    Email: '.$order['email'].'<br>
    Address: '.ucwords($order['address']).'<br>
    Contact: '.$order['contact'].'<br>
    Order On: '.date('M/d/Y ',$order['time']).'<br>
    </div><div style="min-height:200px;width:100%;margin-top:20px;">';
    $data.='<table class="table-striped" style="width:99%;margin:0 auto;margin-top:10px">
    <tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Item</td><td>Quantity</td><td>Unit Cost</td><td>Total cost</td>'.decodeitems($order['items']);
        }
    $data.='</table></div>

    <div style="width:100%;display:flex;flex-direction:row;">
    <div style="width:50%;">
    Sign: ____________________<br>
    Date:_____________________<br>
    </div><div style="width:50%;">
    Sign:____________________ <br>
    Date:____________________<br>
    </div></div>
    
    <div style="width:100%;display:flex;flex-direction:row;margin-top:10px;margin-bottom:20px;">
    <button class="btnn" style="color:white;width:fit-content;background:green;float:right;" onclick="confirmdelivery('.$order['id'].')">Receive</button>
    </div></div>';
    $mpdf->text_input_as_HTML=true;
    $mpdf->WriteHTML($data);
   

    $mpdf->Output('Delivery'.$order['poid'].'pdf','D');
    ?>