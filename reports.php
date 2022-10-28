<?php
session_start();

require 'functions.php';
require 'mpdfs.php';
$con=mysqli_connect(HOST,DB_USER,DB_PASS,DB_NAME);
#add products
if(isset($_GET['addproducts'])){
echo '<div class="col-11 mx-auto" style="width:98%;">
<div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h5>Open Orders</h5></div>
<table style="width:99%;margin:0 auto;">
<tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Vendor</td><td>Item types</td><td>Total cost</td><td>Delivery date</td></tr>';
    $allpos=mysqli_query($con,"SELECT `po`.*,`v`.`name` FROM `purchase_orders`  AS `po` INNER JOIN `vendors` AS `v` ON `po`.`vendor`=`v`.`id` WHERE `po`.`status`=0");
    if(mysqli_num_rows($allpos)>0){
        foreach($allpos as $pos){
            
            echo '<tr class="orders" onclick="popupload(\'reports?addinvent='.$pos['id'].'\')"><td>'.ucwords($pos['name']).'</td>'.extractdata($pos['items']).'<td>'.$pos['delivery'].'</td>';
            
       echo '</tr>';
        }
    }else{
        echo '<tr style="line-height:25px;"><td colspan="6"> There is no open purchase orders</td></tr>';
    }
    echo '</table></div>';
}
if(isset($_GET['invoice'])){
    echo '<div class="col-11 mx-auto" style="width:98%;">
    <div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h5>Sale Orders</h5></div>
    <table style="width:99%;margin:0 auto;">
    <tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Customer</td><td>Item types</td><td>Total cost</td><td>Delivery date</td></tr>';
        $allpos=mysqli_query($con,"SELECT * FROM `sales_orders` WHERE `status`=0");
        if(mysqli_num_rows($allpos)>0){
            foreach($allpos as $pos){
                
                echo '<tr class="orders" onclick="popupload(\'reports?getinvoice='.$pos['id'].'\')"><td>'.ucwords($pos['customer']).'</td>'.extractdata($pos['items']).'<td>'.$pos['delivery'].'</td>';
                
           echo '</tr>';
            }
        }else{
            echo '<tr style="line-height:25px;"><td colspan="6"> There are no sale orders for invoicing</td></tr>';
        }
        echo '</table></div>';
}
if(isset($_GET['saleorder'])){
    echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
    <div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h4>New Sale Order</h4></div>
    <form id="nsaleorder" method="post" onsubmit="customerorder(event)">
        <p>Select customer type<br><select name="type" onchange="changecustomer(this.value)">
        <option value="1">Group</option>
        <option value="2">Other</option>
        </select></p>
        <p id="grpdet" style="display:block;">Buyer<br>
        <input type="text" list="blist" name="buyer" style="width:100%;max-width:300px;">
        <datalist id="blist">';
        $allgroup=mysqli_query($con,"SELECT * FROM `groups` WHERE `status`=1");
        foreach($allgroup AS $grop){
        echo '<option value="'.ucwords($grop['name']).'">';
        }
        echo '</datalist>
        </p>
        <div id="cust1" style="display:none;"></div>
        <p>Order reference<br><input type="text" name="ref" value="'.'SO'.$_SESSION['csysuser'].time().'" style="width:100%;max-width:300px;" readonly></p>
        <p>Item name<br><select name="product" style="width:100%;max-width:300px;">';
        $products=mysqli_query($con,"SELECT * FROM `products` WHERE `quantity`>0 AND `loanable`=1");
        foreach($products as $product){
            echo '<option value="'.$product['name'].'">'.$product['name'].'</option>';
        }
        echo '</select></p>
        <p>Quantity<br><input type="number" name="qty" style="width:100%;max-width:300px;" required></p>
        <p>Unit Price<br><input type="number" name="price" style="width:100%;max-width:300px;" required></p>
        <div class="more"></div>
        <p>Delivery date<br><input type="date" name="delivery" style="width:100%;max-width:300px;" value="'.date('Y-m-d',strtotime("tomorrow")).'" min="'.date('Y-m-d',strtotime("tomorrow")).'" required></p>
    <!--<div class="btn1" onclick="addprod()" style="padding:0 3px;color:white;background:green;"><i class="bi bi-plus" style="font-size:20px;cursor:pointer"></i> Add item</div>-->
        <p style="text-align:right"><button class="btnn" onclick="customerorder(event)">Confirm Order</button></p>
    </form>
    </div>';
}
if(isset($_GET['payments'])){
    echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
    <div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h5>Receive payments</h5></div>
    <form id="paym" method="post" onsubmit="invoicepayment(event)">
   <p>Select Order<br><select name="stdorder" style="width:100%;max-width:300px;">';
        $allpos=mysqli_query($con,"SELECT * FROM `sales_orders` WHERE `status`=2");
        if(mysqli_num_rows($allpos)>0){
            foreach($allpos as $pos){
                echo '<option value="'.$pos['id'].'">'.$pos['customer'].'</option>';
            }
        }else{echo '<option>No pending order</option>';}
            echo '</select></p>
            <p>Debit account<br><select style="max-width:300px;width:100%;" name="raccount">';
            $selectall=mysqli_query($con,"SELECT `ca`.* FROM `accounttypes`AS `at` INNER JOIN `chartsofaccount` AS `ca` ON `ca`.`category`=`at`.`id` WHERE `at`.`ftype`=1");
            foreach($selectall as $all){
                echo '<option value="'.$all['id'].'">'.$all['name'].'</option>';
            }
            echo '</select></p>
            <p>Amount received<br><input type="number" name="currentpay" style="width:100%;max-width:300px;" required></p>
            <p><button class="btnn" style="float:right;" onclick="invoicepayment(event)">Confirm</button></p><br><br>';
}
if(isset($_GET['purchase'])){
    echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
    <div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h4>Create New LPO</h4></div>
    <form id="purchase" method="post" onsubmit="addpo(event)">
        <p>Supplier<br><select name="supplier" style="width:100%;max-width:300px;">';
        $allsups=mysqli_query($con,"SELECT * FROM `vendors` WHERE `status`=1");
        if(mysqli_num_rows($allsups)>0){
            foreach($allsups as $sup){
            echo '<option value="'.$sup['id'].'">'.ucwords($sup['name']).'</option>';
            }
        }
        else{
            echo '<option>No vendor has benn added</option>';
        }
        echo '</select></p>
        <input type="hidden" id="order" value="0">
        <p>Select Item<br><select name="product" style="width:100%;max-width:300px;">';
        $products=mysqli_query($con,"SELECT * FROM `products`");
        if(mysqli_num_rows($products)){
            foreach($products as $product){
                echo '<option value="'.$product['name'].'">'.$product['name'].'</option>';
            }
        }
        else{
            echo '<option>No available products</option>';
        }
        echo '</select></p>
        <p>Quantity<br><input type="number" name="qty" style="width:100%;max-width:300px;" required></p>
        <p>Unit Price<br><input type="number" name="price" style="width:100%;max-width:300px;" required></p>
        <div class="more"></div>
        <p>Delivery date<br><input type="date" name="delivery" style="width:100%;max-width:300px;" value="'.date('Y-m-d',strtotime("tomorrow")).'" min="'.date('Y-m-d',strtotime("tomorrow")).'" required></p>
    <div class="btn1" onclick="addprod()" style="padding:0 3px;color:white;background:green;"><i class="bi bi-plus" style="font-size:20px;cursor:pointer"></i> Add item</div>
        <p style="text-align:right"><button class="btnn" onclick="addpo(event)">Make Order</button></p>
    </form>
    </div>';
}
if(isset($_GET['addinvent'])){
    $order=$_GET['addinvent'];
    echo '<div class="col-10 mx-auto" style="max-width:500px;width:100%;min-width:400px;">
    <div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h4>Goods Delivery Note</h4></div>';
    $allorders=mysqli_query($con,"SELECT `po`.*,`v`.`name`,`v`.`address`,`v`.`contact`,`v`.`email` FROM `purchase_orders` AS `po` INNER JOIN `vendors` AS `v` ON `v`.`id`=`po`.`vendor` WHERE `po`.`id`='$order'");
    foreach($allorders as $order){
    echo '<div class="row no-gutters" style="justify-content:space-between;font-size:13px;font-family:cambria;"><div class="col-6" style="min-width:200px;">
    Golden Vision Empowerment Program<br>
    P.O. Box 75- 10218 Kangari<br>
    Email:goldenvep@gmail.com/<br>
    info@goldenvision.or.ke<br>
    Phone:+254 716 639 658<br>
    +254 726 778 662<br></div><div class="col-6">
    P.Order Number: '.$order['poid'].'<br>
    Supplier:'.ucwords($order['name']).'<br>
    Email: '.$order['email'].'<br>
    Address: '.ucwords($order['address']).'<br>
    Contact: '.$order['contact'].'<br>
    Order On: '.date('M/d/Y ',$order['time']).'<br>
    </div><div style="min-height:200px;width:100%;margin-top:20px;">';
    echo '<table class="table-striped" style="width:99%;margin:0 auto;">
    <tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Item</td><td>Quantity</td><td>Unit Cost</td><td>Total cost</td>';
        echo decodeitems($order['items']);
        
        }
    echo '</table></div>
    <p><div class="col-12" style="text-align:right;"><Payment Due date<br><input type="date" id="duedate" min="'.date('Y-d-m',strtotime('today')).'" style="max-width:200px;width:100%"></div></p>
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
}
if(isset($_GET['getinvoice'])){
    $invoice=$_GET['getinvoice'];
    echo '<div class="col-10 mx-auto" style="max-width:500px;width:100%;min-width:400px;">
    <div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h4>Sale Invoice</h4></div>';
    $allorders=mysqli_query($con,"SELECT * FROM `sales_orders` WHERE `id`='$invoice'");
    foreach($allorders as $order){
    echo '<div class="row no-gutters" style="justify-content:space-between;font-size:13px;font-family:cambria;"><div class="col-6" style="min-width:200px;">
    Golden VEP<br>
    P.O. Box 75- 10218 Kangari<br>
    Email:goldenvep@gmail.com/<br>
    info@goldenvision.or.ke<br>
    Phone:+254 716 639 658<br>
    +254 726 778 662<br></div><div class="col-6">
    <div class="row no-gutters">To</div>
    LPO Ref: '.$order['refid'].'<br>
    Buyer:'.ucwords($order['customer']).'<br>
    Contact: '.$order['contact'].'<br>
    Order On: '.date('M/d/Y ',$order['date']).'<br>
    Address:'.$order['address'].'
    </div><div style="min-height:200px;width:100%;margin-top:20px;"><div class="row no-gutters"><h5>Invoice Description</h5></div><table class="table-striped" style="width:99%;margin:0 auto;">
    <tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Item</td><td>Quantity</td><td>Unit Cost</td><td>Total cost</td>';
        echo decodeitems($order['items']);
        
        }
    echo '</table>
    </div>
    <div style="width:100%;display:flex;flex-direction:row;justify-content:end;">
    <div style="max-width:%;">
    Sign:____________________ <br>
    Date:____________________<br>
    </div></div>
    <div style="width:100%;display:flex;flex-direction:row;margin-top:10px;margin-bottom:20px;justify-content:end;"><button class="btnn" onclick="generateinvo('.$order['id'].')" style="color:white;width:fit-content;background:red;float:right;">Generate</button></div><br>';
}
if(isset($_GET['memo'])){
    echo '<div class="col-12 card" style="min-height:150px;min-width:500px;">
   <div style="margin-top:10px">
   <div class="row no-gutters">
   <select style="max-width:200px;width:100%;" onchange="loantype(this.value)">';
    $chargestypes=mysqli_query($con,"SELECT * FROM `chargetypes`");
    if(mysqli_num_rows($chargestypes)>0){
        foreach($chargestypes as $chty){
        echo '<option value="'.$chty['id'].'">'.ucwords($chty['name']).'</option>';
        }
    }
    else{
        echo '<option>--No collection funds--</option>';
    }
    echo'</select></div><br/>
    <div class="" style="width:100%;font-family:cambria;min-width:500px;">
    <div class="row no-gutters" style="background:#e6e6fa;width:96%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;justify-content:space-around;"><div class="col-1">Id</div><div class="col-3">Member number</div><div class="col-2">Amount</div><div class="col-3">Date</div><div class="col-2">Action</div></div><div id="collectionid">';
    $chargetypes=mysqli_query($con,"SELECT * FROM `chargetypes` LIMIT 1");
    foreach($chargetypes as $charge){
        $type=$charge['id'];
    }
    $uncollected=mysqli_query($con,"SELECT `ch`.`name`,`fn`.*,`mb`.`sysnum` FROM `chargetypes`AS `ch` INNER JOIN `fines` AS `fn` ON `fn`.`charges`=`ch`.`id` INNER JOIN `members` AS `mb` ON `fn`.`client`=`mb`.`id` LEFT JOIN `collections` AS `cl` ON `cl`.`fineid`=`fn`.`id` WHERE `ch`.id='$type'AND `fn`.`status`=0 LIMIT 20");
    if(!mysqli_num_rows($uncollected)>1){
        echo '<div class="row no-gutters"><div style="text-align:center;">There is no uncollected funds in here, check again later!</div></div>';
    }
    else{ $i=0;
       echo '<form id="collections" method="post" onsubmit="receivefines(event)">';
        foreach($uncollected as $col){
            $i++;
            echo '<div class="row no-guters" style="line-height:30px;justify-content:space-around;"><div class="col-1" style="text-align:left;">'.$i.'</div><div class="col-3" style="text-align:left;">'.$col['sysnum'].'</div><div class="col-2" style="text-align:left;">'.$col['amount'].'/=</div><div class="col-3" style="text-align:left;">'.date('d/m/Y',$col['date']).'</div><div class="col-2" style="text-align:left;"><input  type="checkbox" name="fineid" value="'.$col['id'].'"></div></div>';
    }
    echo '<div class="row no-gutters" style="margin:20px 0;"><select name="debtacnt" style="width:100%;max-width:200px;">';
        $selectall=mysqli_query($con,"SELECT `ac`.* FROM `chartsofaccount` AS `ac` INNER JOIN `accounttypes` AS `at` ON `at`.`id`=`ac`.`category` WHERE `at`.`ftype`=1");
        if(mysqli_num_rows($selectall)<1){
            echo '<option>--No Account--</option>';
        }
        else{
            foreach($selectall as $all){
                echo '<option value="'.$all['id'].'">'.ucwords($all['name']).'</option>';
            }
        }
    echo'</select></div>
    <div class="row no-gutters" style="justify-content:right;margin:10px 0;margin-right:20px;"><button class="btnn" style="float:right;" onclick="receivefines(event)">Confirm</button></div></div>
   </form>';
}
    echo '</div></div>';
}
if(isset($_GET['loantype'])){
    $type=$_GET['loantype'];
    $chargetypes=mysqli_query($con,"SELECT * FROM `chargetypes` WHERE `id`='$type'");
    foreach($chargetypes as $charge){
        $type=$charge['id'];
    }
    $uncollected=mysqli_query($con,"SELECT `ch`.`name`,`fn`.*,`mb`.`sysnum` FROM `chargetypes`AS `ch` INNER JOIN `fines` AS `fn` ON `fn`.`charges`=`ch`.`id` INNER JOIN `members` AS `mb` ON `fn`.`client`=`mb`.`id` LEFT JOIN `collections` AS `cl` ON `cl`.`fineid`=`fn`.`id` WHERE `ch`.id='$type' AND `fn`.`status`=0 LIMIT 20");
    if(mysqli_num_rows($uncollected)<1){
        echo '<div class="row no-gutters"><div style="text-align:center;">There is no uncollected funds in here, check again later!</div></div>';
    }
    else{ $i=0;
       echo '<form id="collections" method="post" onsubmit="receivefines(event)">';
        foreach($uncollected as $col){
            $i++;
            echo '<div class="row no-guters" style="line-height:30px;justify-content:space-around;"><div class="col-1" style="text-align:left;">'.$i.'</div><div class="col-3" style="text-align:left;">'.$col['sysnum'].'</div><div class="col-2" style="text-align:left;">'.$col['amount'].'/=</div><div class="col-3" style="text-align:left;">'.date('d/m/Y',$col['date']).'</div><div class="col-2" style="text-align:left;"><input  type="checkbox" name="fineid" value="'.$col['id'].'"></div></div>';
        }
        echo '<div class="row no-gutters" style="margin:20px 0;"><select name="debtacnt" style="width:100%;max-width:200px;">';
        $selectall=mysqli_query($con,"SELECT `ac`.* FROM `chartsofaccount` AS `ac` INNER JOIN `accounttypes` AS `at` ON `at`.`id`=`ac`.`category` WHERE `at`.`ftype`=1");
        if(mysqli_num_rows($selectall)<1){
            echo '<option>--No Account--</option>';
        }
        else{
            foreach($selectall as $all){
                echo '<option value="'.$all['id'].'">'.ucwords($all['name']).'</option>';
            }
        }
    echo'</select></div>
    <div class="row no-gutters" style="justify-content:right;margin:10px 0;margin-right:20px;"><button class="btnn" style="float:right;" onclick="receivefines(event)">Confirm</button></div></div>
   </form>';
    }
}
if(isset($_GET['newvendor'])){
    echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
            <div class="row no-gutters" style="justify-content:center;font-size:23px;"><h5>Add New Vendor</h5></div>
            <form id="vvendor" method="post" onsubmit="addvendor(event)">
                <p>Vendor name<br><input type="text" name="nvendor" style="width:100%;max-width:300px;" required></p>
                <p>Contact<br><input type="number" name="vcont" style="width:100%;max-width:300px;" required></p>
                <p>Email<br><input type="email" name="vmail" style="width:100%;max-width:300px;" required></p>
                <p>Address<br><input type="text" name="vaddress" style="width:100%;max-width:300px;" required></p>
                <p style="text-align:right"><button class="btnn" onclick="addvendor(event)">Add</button></p>
            </form>
            </div>';
}
if(isset($_GET['receipts'])){
echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
<div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h5>Create new receipt</h5></div>
<form id="rproducts" method="post">
    <p>Product name<br><input type="text" name="pname" style="width:100%;max-width:300px;" required></p>
    <p>Quantity received<br><input type="number" name="lamnt" style="width:100%;max-width:300px;" required></p>
    <p>Buying price<br><input type="number" name="price" style="width:100%;max-width:300px;" required></p>
    <p>Date received<br><input type="date" name="price" style="width:100%;max-width:300px;" max="'.date('Y-m-d',time()).'" required></p>
    <p>Supplier<br><input type="text" name="price" style="width:100%;max-width:300px;" required></p>
    <p style="text-align:right"><button class="btnn">Add</button></p>
</form>
</div>';
}
if(isset($_GET['newbill'])){
    echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
<div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h4>Create New Bill</4></div>
<form id="nbill" method="post" onsubmit="nbill(event)">
    <p>Vendor<br><select name="vname" style="width:100%;max-width:300px;">';
    $vendors=mysqli_query($con,"SELECT `id`,`name` FROM `vendors` WHERE `status`=1");
    if(mysqli_num_rows($vendors)>0){
        foreach($vendors as $vendor){
            echo '<option value="'.$vendor['id'].'"> '.ucwords($vendor['name']).'</option>';
        }
    }
    else{
        echo '<option> -- No Registered Vendor -- </option>';
    }
    
    echo '</select></p>
    <p>Product<br><input type="text" name="items" style="width:100%;max-width:300px;" required></p>
    <p>Quantity<br><input type="number" name="qty" style="width:100%;max-width:300px;" required></p>
    <p>Unit Price<br><input type="number" name="uprice" style="width:100%;max-width:300px;" required></p>
    <p>Bill Id<br><input type="text" name="desc" style="width:100%;max-width:300px;" required value="BID'.$_SESSION['csysuser'].time().'"></p>
    <p>Debit Account<br><select name="debaccount" style="width:100%;max-width:300px;">';
    $liabilities=mysqli_query($con,"SELECT `ca`.`id`,`ca`.`name` FROM `accounttypes` AS `at` INNER JOIN `chartsofaccount` AS `ca` ON `ca`.`category`=`at`.`id` WHERE `at`.`name` LIKE '%liabilit%'");
    if(mysqli_num_rows($liabilities)>0){
        foreach($liabilities AS $liab){
            echo '<option value="'.$liab['id'].'">'.ucwords($liab['name']).'</option>';
        }
    }else{
        echo '<option> -- No Liability Account -- </option>';
    }
    echo '</select></p>
    <p>Due date<br><input type="date" name="ddate" min="'.date("Y-m-d",time()).'"  value="'.date("Y-m-d",time()).'" style="width:100%;max-width:300px;" required></p>
    <p style="text-align:right"><button class="btnn" onclick="nbill(event)">Add</button></p>
</form>
</div>';
}
if(isset($_GET['paybill'])){
    echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
    <div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h5>Pay Bill</h5></div>
    <form id="pbill" method="post" onsubmit="paybill(event)">
        
        <p>Bill code<br><select name="bills" style="width:100%;max-width:300px;" onchange="changebill(this.value)">';
        $getbills=mysqli_query($con,"SELECT `id`,`billid`,`balance` FROM `bills` WHERE `status`=0");
        if(mysqli_num_rows($getbills)>0){
             $bals=[];
            foreach($getbills as $bill){
               array_push($bals,$bill['balance']);
                echo '<option value="'.$bill['id'].'">'.$bill['billid'].'</option>';

            }
        }
        else{
            echo '<option>No unpaid bills</option>';
        }
        echo '</select></p>
        <p>Amount<br><input type="number" name="pamount" style="width:100%;max-width:300px;" max="'.$bals['0'].'" required></p>
        <p>Credit Account<br><select name="craccount" style="width:100%;max-width:300px;">';
        $paccounts=mysqli_query($con,"SELECT `ca`.`id`,`ca`.`name` FROM `chartsofaccount` AS `ca` INNER JOIN `accounttypes` AS `at` ON `ca`.`category`=`at`.`id` WHERE `ftype`=1 AND `level`=0");
        if(mysqli_num_rows($paccounts)>0){
            foreach($paccounts AS $pa){
        echo '<option value="'.$pa['id'].'">'.ucwords($pa['name']).'</option>';
            }
        }else{
            echo '<option>--No Available Account--</option>';
        }
        
        echo '</select></p>
        <p style="text-align:right"><button class="btnn" onclick="paybill(event)">Pay Bill</button></p>
    </form>
    </div>';
}
if(isset($_GET['getvbills'])){
    $vvend=$_GET['getvbills'];
    echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
    <div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h5>Pay Bill</h5></div>
    <form id="pbill" method="post" onsubmit="paybill(event)">
        
        <p>Bill code<br><select name="bills" style="width:100%;max-width:300px;" onchange="changebill(this.value)">';
        $getbills=mysqli_query($con,"SELECT `id`,`billid`,`balance` FROM `bills` WHERE `status`=0 AND `vendor`='$vvend'");
        if(mysqli_num_rows($getbills)>0){
             $bals=[];
            foreach($getbills as $bill){
               array_push($bals,$bill['balance']);
                echo '<option value="'.$bill['id'].'">'.$bill['billid'].'</option>';

            }
        }
        else{
            echo '<option>No unpaid bills</option>';
        }
        echo '</select></p>
        <p>Amount<br><input type="number" name="pamount" style="width:100%;max-width:300px;" max="'.$bals['0'].'" required></p>
        <p>Credit Account<br><select name="craccount" style="width:100%;max-width:300px;">';
        $paccounts=mysqli_query($con,"SELECT `ca`.`id`,`ca`.`name` FROM `chartsofaccount` AS `ca` INNER JOIN `accounttypes` AS `at` ON `ca`.`category`=`at`.`id` WHERE `ftype`=1 AND `level`=0");
        if(mysqli_num_rows($paccounts)>0){
            foreach($paccounts AS $pa){
        echo '<option value="'.$pa['id'].'">'.ucwords($pa['name']).'</option>';
            }
        }else{
            echo '<option>--No Available Account--</option>';
        }
        
        echo '</select></p>
        <p style="text-align:right"><button class="btnn" onclick="paybill(event)">Pay Bill</button></p>
    </form>
    </div>';

}
if(isset($_GET['journal'])){
    
echo '<div class="col-md-11 table-responsive">
<div class="row no-gutters" style="margin-left:10px;"><select style="min-width:60px;max-width:120px;">';
$periods=mysqli_query($con,"SELECT * FROM `transactions` GROUP BY `month`");
 foreach($periods as $period){
    $sel=$period['month']==strtotime('M-Y',time())?'selected':'';
    echo '<option value="'.$period['month'].'" '.$sel.'>'.date('M-Y', $period['month']).'</option>';
 }
echo '</select></div>
<table class="table-striped" style="width:98%;margin:0 auto;min-width:500px;max-width:1240px;">
<tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Date</td><td>Account</td><td>Description</td><td>Amount</td></tr>
<tbody id="jdata">';
$month=strtotime(date('Y-M',time()));
$alltransactions=mysqli_query($con,"SELECT * FROM `transactions` WHERE `month`='$month'");
if(mysqli_num_rows($alltransactions)){
    foreach($alltransactions as $trans){
        $ac=$trans['account'];
        if($ac>0){
            $account=mysqli_query($con,"SELECT `name` FROM `chartsofaccount` WHERE `id`='$ac'");

            foreach($account AS $act){
                echo '<tr><td>'.date('d/M/Y',$trans['date']).'</td><td>'.ucwords($act['name']).'</td><td>'.$trans['description'].'</td><td>'.$trans['amount'].'</td></tr>';
            }
        }else{
            echo '<tr><td>'.date('d/M/Y',$trans['date']).'</td><td>Uncategorized</td><td>'.$trans['description'].'</td><td>'.$trans['amount'].'</td></tr>';
        }
    }
}
else{
    echo '<tr><td colspan="4" style="">No available transactions for this period!!</td></tr>';
}


echo '</tbody>
</div>';
}
if(isset($_GET['cpaybill'])){
    $bid=$_GET['cpaybill'];
    echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
    <div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h5>Pay Bill</h5></div>
    <form id="pbill" method="post" onsubmit="paybill(event)">
        
        <p>Item received<br><select name="bills" style="width:100%;max-width:300px;" onchange="changebill(this.value)">';
        $getbills=mysqli_query($con,"SELECT `id`,`billid`,`balance` FROM `bills` WHERE `status`=0");
        if(mysqli_num_rows($getbills)>0){
             
            foreach($getbills as $bill){
                $selected=$bill['id']==$bid?"selected":"";
                $amount=$bill['id']==$bid?$bill['balance']:0;
               array_push($bals,$bill['balance']);
                echo '<option value="'.$bill['id'].'" '.$selected.'>'.$bill['billid'].'</option>';

            }
        }
        else{
            echo '<option>No unpaid bills</option>';
        }
        echo '</select></p>
        <p>Amount<br><input type="number" name="pamount" style="width:100%;max-width:300px;" max="'.$amount.'" required></p>
        <p>Credit Account<br><select name="craccount" style="width:100%;max-width:300px;">';
        $paccounts=mysqli_query($con,"SELECT `ca`.`id`,`ca`.`name` FROM `chartsofaccount` AS `ca` INNER JOIN `accounttypes` AS `at` ON `ca`.`category`=`at`.`id` WHERE `ftype`=1 AND `level`=0");
        if(mysqli_num_rows($paccounts)>0){
            foreach($paccounts AS $pa){
        echo '<option value="'.$pa['id'].'">'.ucwords($pa['name']).'</option>';
            }
        }else{
            echo '<option>--No Available Account--</option>';
        }
        
        echo '</select></p>
        <p style="text-align:right"><button class="btnn" onclick="paybill(event)">Pay Bill</button></p>
    </form>
    </div>';
}
if(isset($_GET['period'])){
    $month=$_GET['period'];
    $alltransactions=mysqli_query($con,"SELECT * FROM `transactions` WHERE `month`=$month");
    if(mysqli_num_rows($alltransactions)>0){
        foreach($alltransactions as $trans){
            $ac=$trans['account'];
            if($ac>0){
                $account=mysqli_query($con,"SELECT `name` FROM `chartsofaccount` WHERE `id`='$ac'");
                    foreach($account AS $act){
                        echo '<tr><td>'.date('d/M/Y',$trans['date']).'</td><td>'.ucwords($act['name']).'</td><td>'.$trans['description'].'</td><td>'.$trans['amount'].'</td></tr>';
                    }
                }else{
                    echo '<tr><td>'.date('d/M/Y',$trans['date']).'</td><td>Uncategorized</td><td>'.$trans['description'].'</td><td>'.$trans['amount'].'</td><td></tr>';
                }
            }
        }
        else{
            echo '<tr><td colspan="4" style="text-align:center;">No available transactions</td></tr>';
        }
}
if(isset($_GET['unpaidbills'])){
    if(isset($_GET['unpaidbills'])){
        echo '<h4>Pending Bills</h4>
        <div class="table-responsive">
        <table class="table-striped" style="width:98%;margin:0 auto;min-width:500px;">
        <tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Vendor</td><td>Item</td><td>Qty</td><td>Cost</td><td>Due date</td><td>Status</td></tr><tbody class="blcont">';
        $getbills=mysqli_query($con,"SELECT `bl`.*,`vn`.`name` FROM `bills` AS `bl` INNER JOIN `vendors` AS `vn` ON `bl`.`vendor`=`vn`.`id` WHERE `bl`.`status`=0");
        if(mysqli_num_rows($getbills)>0){
            foreach($getbills as $bill){
                $d=strtotime($bill['ddate'])-strtotime("today");
                $res=$d/(24*60*60);
                $stat=$res>=0?"<span style=''> Due in ".$res." day(s)</span>":"<span style='color:red;'> ".abs($res). " days ago</span>";
            echo '<tr style=""><td>'.ucwords($bill['name']).'</td><td>'.ucwords($bill['item']).'</td><td>'.$bill['qty'].'</td><td>'.$bill['balance'].'</td><td>'.$bill['ddate'].'</td><td>'. $stat.'</td></tr>';}
        }
        else{
            echo '<tr><td colspan="6" style="text-align:center;">No recorded bills </td></tr>';
        }
       echo '</tbody></table></div>
        </div>';
    }
}
if(isset($_GET['deposits'])){
    echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
    <div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h5>Make bank deposit</h5></div>
    <form id="deposit" method="post" onsubmmit="newdeposit(event)">
    <p>Credit Account <br> <select style="width:100%;max-width:300px;" name="chartaccount">';
    $incomes= mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `level`=0");
    if(mysqli_num_rows($incomes)>0){
    foreach($incomes as $income){
        echo '<option value="'.$income['id'].'">'.ucwords($income['name']).'</option>';
    }
}else{
    echo '<option>-- No funds source --</option>';
}
        echo '</select></p>
        <p>Debit Account<br><select name="bacnt">';
        $getbanks=mysqli_query($con,"SELECT * FROM `bank_accounts` WHERE `status`=1");
        if(mysqli_num_rows($getbanks)>0){
            foreach($getbanks as $bank){
                echo '<option value="'.$bank['id'].'">'.$bank['account'].'</option>';
            }
        }
        else{
            echo '<option>No available bank</option>';
        }
        echo '</select></p>
        <p>Deposit amount<br><input type="number" name="acamount" style="width:100%;max-width:300px;" required></p>
       
        <p style="text-align:right"><button class="btnn" onclick="newdeposit(event)">Add</button></p>
    </form>
    </div>';
}
if(isset($_GET['withdrawac'])){
    echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
    <div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h5>Bank Withdraw</h5></div>
    <form id="withdraw" method="post" onsubmmit="newwith(event)">
        <p>Withdraw To<br><select name="actype" style="max-width:300px;width:100%;">';
        $destinations= mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `level`=0");
        if(mysqli_num_rows($destinations)>0){
        foreach($destinations as $destination){
            echo '<option value="'.$destination['id'].'">'.ucwords($destination['name']).'</option>';
        }}
        else{
            echo '<option>-- No Destination Account --</option>';
        }
        echo '</select></p>
        <p>Account<br><select name="waccount">';
        $getbanks=mysqli_query($con,"SELECT * FROM `bank_accounts` WHERE `status`=1");
        if(mysqli_num_rows($getbanks)>0){
            foreach($getbanks as $bank){
                echo '<option value="'.$bank['id'].'">'.$bank['account'].'</option>';
            }
        }
        else{
            echo '<option>No available bank</option>';
        }
        echo '</select></p>
        <p>Withdraw Amount<br><input type="number" name="wamount" style="width:100%;max-width:300px;" required></p>
        <p style="text-align:right"><button class="btnn" onclick="newwith(event)">Add</button></p>
    </form>
    </div>';
}
if(isset($_GET['newaccount'])){
    echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
<div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h5>Add Bank Account</h5></div>
<form id="nbaccount" method="post" onsubmmit="addnewbank(event)">
    <p>Bank name<br><input type="text" name="bname" style="width:100%;max-width:300px;" required></p>
    <p>Account number<br><input type="text" name="acnumber" style="width:100%;max-width:300px;" required></p>
    <p style="text-align:right"><button class="btnn" onclick="addnewbank(event)">Add</button></p>
</form>
</div>';
}
if(isset($_GET['bankaccounts'])){
    echo '<div class="col-8 mx-auto" style="min-width:80%;margin:0px auto;max-width:300px;>
    <div style="font-size:23px;width:100%;text-align:center;"><h5>Manage Bank Accounts</h5></div>
    <div class="row no-gutters" style="justify-content:end;"><button class="btn1" style="color:white;background:green;" onclick="popupload(\'reports.php?newaccount\')"> Add Account</button></div><div class="table-responsive" style="min-width:280px;">
    <table class="table-striped mx-auto" style="width:100%;max-width:600px;margin-top:10px;">
    <tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Account</td><td colspan="2">Actions</td></tr>';
    $getaccounts=mysqli_query($con,"SELECT * FROM `bank_accounts` WHERE `status`=1");
    if(mysqli_num_rows($getaccounts)>0){
        foreach($getaccounts as $account){
            echo '<tr><td>'.$account['bank'].'</td><td onclick="deleteac('.$account['id'].')"><span style="color:red;cursor:pointer;"><i class="bi bi-trash"></i> &nbsp Delete </span></td><td onclick="uploadb('.$account['id'].')"> <span style="color:green;cursor:pointer;"><i class="bi bi-pencil"></i>  Edit<span></td></tr>';
        }
    }else{
        echo '<tr><td colspan="2">No account has been added!</td></tr>';
    }

    echo '</table></div>
    </div></div>';
}
if(isset($_GET['inventory'])){
echo '<div style="width:98%;min-width:400px;min-height:100px;padding-top:5px;padding-right:10px;">
<div class="row no-gutters">Inventory Overview</div>
<div class="col-12 mx-1"><table class="table-striped" style="width:100%;min-width:500px;max-width:1240px;"><tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td colspan="2">Item Name</td><td>Item Cost</td><td> Stock Quantity</td><td>Stock cost</td><tr>';
    $getallitems=mysqli_query($con,"SELECT * FROM `products`");
    if(mysqli_num_rows($getallitems)<1){
        echo '<tr><td colspan="4" text-align:center;>No product has been added to inventory</td></tr>';
    }
    else{
        $i=0;
        foreach($getallitems as $item){
            $i++;
         echo '<tr><td style="text-align:center;">'.$i.'</td><td>'.$item['name'].'</td><td>'.$item['amount'].'</td><td>'.$item['quantity'].'</td><td>'.$item['quantity']*$item['amount'].'/=</td></tr>';
        }
    }
echo '</table></div></div>';
}
if(isset($_GET['newasset'])){
echo '<div class="col-8 mx-auto" style="width:100%;max-width:300px;">
<div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h5>Add New Asset</h5></div>
<form id="newasset" method="post"onsubmit="newasset(event)">
    <p>Asset type<br><select name="account" style="width:100%;max-width:300px;" onchange="changeassetcategory(this.value)">';
    $assettypes= mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `category`=1");
    foreach($assettypes as $asset) {
        echo '<option value="'.$asset['id'].'">'.ucwords($asset['name']).'</option>';
    }
    echo '</select></p>';
    $lims=  mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `category`=1 LIMIT 1");
    $cat=[];
    foreach($lims as $lim){
    if(strlen($lim['nodes'])>1){$cat=explode(",",$lim['nodes']);}
    }
    if(count($cat)>1){echo '<p>Select subaccount<br><select name="subcatego">';
        for($i=0;$i<count($cat);$i++){
            $sbc=$cat[$i];
    
             $subcategory=mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `id`='$sbc'");
             foreach($subcategory as $categ){
                echo '<option value="'.$sbc.'">'.$categ['name'].'</option>';
                }
             }
             echo '</select></p>';
    }   
echo'</p><p>Asset name<br><input type="text" name="asset" style="width:100%;max-width:300px;" required></p>
<p>Asset Value<br><input type="number" name="assetvalue" style="width:100%;max-width:300px;" required></p>
<p style="text-align:right"><button class="btnn" onclick="addasset(event)">Add Asset</button></p>
</form>
</div>';
}
if(isset($_GET['changeasset'])){
    $id=clean($_GET['changeasset']);
        echo '<div class="col-8 mx-auto" style="width:100%;max-width:300px;">
        <div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h5>Add new assets</h5></div>
        <form id="newasset" method="post"onsubmit="newasset(event)">
            <p>Account<br><select name="account" style="width:100%;max-width:300px;" onchange="changeassetcategory(this.value)">';
            $assettypes= mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `id`=$id");
            foreach($assettypes as $asset) {
                if(is_string($asset['nodes'])>1){$cat=explode(",",$asset['nodes']);}else{$cat=[];}
                echo '<option value="'.$asset['id'].'">'.ucwords($asset['name']).'</option>';
            }
            echo '</select></p>';
            if(count($cat)>1){echo '<p>Select subaccount<br><select name="subcatego">';
                for($i=0;$i<count($cat);$i++){
                    $sbc=$cat[$i];
            
                     $subcategory=mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `id`='$sbc'");
                     foreach($subcategory as $categ){
                        echo '<option value="'.$sbc.'">'.$categ['name'].'</option>';
                        }
                     }
                     echo '</select></p>';
            }   
        echo'</p><p>Asset name<br><input type="text" name="asset" style="width:100%;max-width:300px;" required></p>
        <p>Asset Value<br><input type="number" name="assetvalue" style="width:100%;max-width:300px;" required></p>
        <p style="text-align:right"><button class="btnn" onclick="addasset(event)">Add Asset</button></p>
        </form>
        </div>';
}
if(isset($_GET['updateasset'])){
   echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
<div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h5>Update assets</h5></div>
<form id="assetupdate"><p>Select Asset<br><select name="uasset" style="width:100%;max-width:300px;">';
$allassets=mysqli_query($con,"SELECT * FROM `currentassets`");
foreach($allassets AS $asset){
    echo '<option>'.$asset['assetname'].'</option>';
}
  
    echo '</select></p>
    <p>Date received<br><input type="date" name="price" style="width:100%;max-width:300px;" max="'.date('Y-m-d',time()).'" required></p>
    <p>Current<br><input type="text" name="price" style="width:100%;max-width:300px;" required></p>
    <p style="text-align:right"><button class="btnn">Add</button></p>
</form>
</div>';
}

if(isset($_GET['updatebills'])){
    $getbills=mysqli_query($con,"SELECT * FROM `bills` WHERE `status`=0");
    if(mysqli_num_rows($getbills)>0){
        foreach($getbills as $bill){
            $d=strtotime($bill['ddate'])-strtotime("today");
            $res=$d/(24*60*60);
            $stat=$res>=0?"<span style=''> Due in ".$res." day(s)</span>":"<span style='color:red;'> ".abs($res). " days ago </span>";
        echo '<tr style=""><td>'.ucwords($bill['vendor']).'</td><td>'.ucwords($bill['item']).'</td><td>'.$bill['qty'].'</td><td>'.$bill['balance'].'</td><td>'.$bill['ddate'].'</td><td>'. $stat.'<td></tr>';}
    }else{
        echo '<tr><td colspan="5" style="text-align:center;">No recorded bills </td></tr>';
    }
}
if(isset($_GET['addcoa'])){
    echo '<div class="col-6 mx-auto" style="width:100%;max-width:300px;"><div class="row no-gutters" style="justify-content:center;"><h4>New Chart Account</4></div>
    <form method="post" id="chartset" onsubmit="newchartaccount(event)">
    <p>Select Account category<br><select name="category" style="max-width:300px;width:100%;" onchange="changechartaccount(this.value)">';
    $allacounts=mysqli_query($con,"SELECT * FROM `accounttypes`");
    foreach($allacounts as $account){
        echo '<option value="'.$account['id'].'">'.ucwords($account['name']).'</option>';
    }
    
    echo '</select></p>';
    $subaccounts=mysqli_query($con,"SELECT * FROM `chartsofaccount`  WHERE `category`=1 AND `level`>0");
   
    if(mysqli_num_rows($subaccounts)>0){
        echo '<div id="para"><p><div class="row no-gutters" style="justify-content:end;"><div style="width:20px;height:20px;cursor:pointer;" onclick="removecat()"><i class="bi bi-x" style="color:red;font-size:25px;"></i></div></div>Select Subcategory<br><select name="subcat" style="width:100%;max-width:300px;">';
        foreach($subaccounts AS $subac){
                        echo '<option value="'.$subac['id'].'">'.ucwords($subac['name']).'</option>';
                 }     
             
            echo '<select></p></div>';
        }
         else{
            echo '<p><div class="row no-gutters" style="justify-content:space-between" id="cicon"><div id="subid" style="background:green;color:white;min-width:40px;width:fit-content;height:35px;line-height:35px;font-size:12px;cursor:pointer;padding:0 5px;font-family:cambria;border-radius:5px;" onclick="addsubcategory()">Add Subcategory</div><div style="width:20px;height:20px;cursor:pointer;float:right" onclick="addcat()"><i class="bi bi-x" style="color:red;font-size:25px;"></i></div></div><p id="mores"></p>';
        }
        echo '<p>Account name<br><input type="text" name="acname" style="max-width:300px;width:100%;"></p>
    <p>Account Number<br><input type="number" name="account" style="max-width:300px;width:100%;" required></p>
    <p>Account Description<br><textarea name="descrip" style="height:70px;width:100%;resize:none;outline:none;"></textarea></p>
    <p><button class="btnn" style="min-width:60px;width:fit-content;background:green;color:white;float:right;" onclick="newchartaccount(event)">Add Account</button></p></form><br><br>
    </div></div>';
}
if(isset($_GET['nextaccount'])){
    $id=$_GET['nextaccount'];
    echo '<div class="col-6 mx-auto" style="width:100%;max-width:300px;"><div class="row no-gutters" style="justify-content:center;"><h4>New Chart Account</4></div>
    <form method="post" id="chartset" onsubmit="newchartaccount(event)">
    <p>Select Account <br><select name="category" style="max-width:300px;width:100%;" onchange="changechartaccount(this.value)">';
    $allacounts=mysqli_query($con,"SELECT * FROM `accounttypes`");
    foreach($allacounts as $account){
        $current=$account['id']==$id? 'selected':'';
        echo '<option value="'.$account['id'].'" '.$current.'>'.ucwords($account['name']).'</option>';
    }
    
    echo '</select></p>';
    $subaccounts=mysqli_query($con,"SELECT * FROM `chartsofaccount`  WHERE `category`='$id' AND `level`>0");
   
    if(mysqli_num_rows($subaccounts)>0){
        echo '<div id="para"><p><div class="row no-gutters" style="justify-content:end;"><div style="width:20px;height:20px;cursor:pointer;" onclick="removecat()"><i class="bi bi-x" style="color:red;font-size:25px;"></i></div></div>Select Subcategory<br><select name="subcat" style="width:100%;max-width:300px;">';
        foreach($subaccounts AS $subac){
           
                        echo '<option value="'.$subac['id'].'">'.ucwords($subac['name']).'</option>';
                     }
            echo '<select></p></div>';
        }
            else{
            echo '<div class="row no-gutters" style="justify-content:space-between" id="cicon"><div id="subid" style="background:green;color:white;min-width:40px;width:fit-content;height:35px;line-height:35px;font-size:12px;cursor:pointer;padding:0 5px;font-family:cambria;border-radius:5px;" onclick="addsubcategory()">Add Subcategory</div><div style="width:20px;height:20px;cursor:pointer;" onclick="addcat()"><i class="bi bi-x" style="color:red;font-size:25px;"></i></div></div><p id="mores"></p>';
        }

    echo '<p>Account name<br><input type="text" name="acname" style="max-width:300px;width:100%;"></p><p>Account Number<br><input type="number" name="account" style="max-width:300px;width:100%;" required></p>
    <p>Account Description<br><textarea name="descrip" style="height:70px;width:100%;resize:none;outline:none;"></textarea></p>
    <p><button class="btnn" style="min-width:60px;width:fit-content;background:green;color:white;float:right;" onclick="newchartaccount(event)">Add Account</button></p></form><br><br>
    </div></div>';
}
#cashbook
if(isset($_GET['cashbook'])){
    echo '<div class="col-12 card" style="min-height:150px;min-width:500px;">
    <div class="row no-gutters" style="margin:10px 0;justify-content:space-between;">
    <div><select style="width:150px;" onchange=""><option>--'.date('M-Y',strtotime('today')).'--</option></select></div>
    <div style="margin-right:20px;"><button style="cursor:pointer;border:1px solid;padding:2px 5px;width:fit-content;" onclick="popupload(\'reports.php?credittcashbook\')">Credit A/C</button>&nbsp;<button style="cursor:pointer;border:1px solid;padding:2px 5px;width:fit-content;"onclick="popupload(\'reports?debitcashbook\')">Debit A/C</button></div></div>
    <table class="table-reponsive" style="width:100%;max-height:95%;min-width:500px;"><tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Id</td><td>Date</td><td>Description</td><td>Amount</td></tr>';
    $month=strtotime(date('Y-M',time()));$total=0;
    $expenses=mysqli_query($con,"SELECT * FROM `cliabilities` WHERE `month`='$month'");
    if(mysqli_num_rows($expenses)>0){
    $i=0;
        foreach($expenses as $col){
            $i++;
            $total=$total+$col['amount'];
            echo '<tr><td>'.$i.'</td><td>'.date('d/m/Y',$col['date']).'</td><td>'.$col['description'].'</td><td>'.$col['amount'].'/=</td></tr>';
        }
    }
    else{
        echo '<tr><td colspan="5">There is no record for this period</td></tr>';
    }
    echo '<tr style="border-top:2px solid;font-weight:bold;"><td colspan="3"> Total</td><td>'.$total.'/=</td></tr></table>
    </div>';
}
#Add cashbook expense 
if(isset($_GET['creditcashbook'])){
    echo '<div class="col-8 mx-auto" style="max-width:300px;">
    <div class="row no-gutters" style="justify-content:center;"><h4>Add Expense</h4></div>
    <form method="post" id="pettycash" onsubmit="addpettycash(event)">
    <p>Date<br><input type="date" name="ddt" value="'.date('Y-m-d',strtotime("today")).'" style="width:100%;max-width:300px;outline:none;"></p>
    <p>Description<br><textarea style="height:70px;width:100%;max-width:300px;outline:none;resize:none;" required name="description"></textarea></p>
    <p>Credit Account<br><select style="width:100%;max-width:300px;" name="accnt">';
    $pettyaccount=mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `name` LIKE '%petty%'");
    if(mysqli_num_rows($pettyaccount)>0){
        foreach($pettyaccount as $petty){
            echo '<option value="'.$petty['id'].'">'.ucwords($petty['name']).'</option>';
        }
    }
    else{
        echo '<option>--Account has Not been added--</option>';
    }
    echo'</select></p>
    <p>Amount<br><input type="number" min="1" name="amount" style="width:100%;max-width:300px;"></p>
    <div class="row no-gutters" style="justify-content:end;"><button class="btnn" onclick="addpettycash(event)">Add</button></div><br>
    </form></div>';

}
if(isset($_GET['debitcashbook'])){
    echo '<div class="col-8 mx-auto" style="max-width:300px;">
    <div class="row no-gutters" style="justify-content:center;"><h4>Debit Petty Cash</h4></div>
    <form method="post" id="dpettycash" onsubmit="dpettycash(event)">
    <p>Credit Account<br><select style="width:100%;max-width:300px;" name="ptaccnt">';
    $allacounts=mysqli_query($con,"SELECT `ca`.* FROM `accounttypes` AS `ac` INNER JOIN `chartsofaccount` AS `ca` ON `ca`.`category`=`ac`.`id` WHERE `ac`.`ftype`=1");
    if(mysqli_num_rows($allacounts)>0){
        foreach($allacounts as $nmact){
            echo '<option value="'.$nmact['id'].'">'.ucwords($nmact['name']).'</option>';
        }
    }
    else{
        echo '<option>--Account has Not been added--</option>';
    }
    echo'</select></p>
    <p>Amount<br><input type="number" min="1" name="ptamount" style="width:100%;max-width:300px;"></p>
    <div class="row no-gutters" style="justify-content:end;"><button class="btnn" onclick="dpettycash(event)">Add</button></div><br>
    </form></div>';
}
#get all vendors
if(isset($_GET['allvendors'])){
    echo '<div class="col-12 table-responsive" style="">
    <div style="justify-content:end;width:95%;height:30px;max-width:1240px;margin:10px 0;"><button style="border:1px solid;cursor:pointer;float:right;margin:10" onclick="popupload(\'reports?newvendor\')"> Add Vendor</button></div><table class="table-striped" style="width:95%;max-width:1240px;"><tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Id</td><td>Name</td><td>Contact</td><td>Address</td><td>Edit</td><td>Delete</td></tr>';
        $allvendors=mysqli_query($con,"SELECT * FROM `vendors` WHERE `status`=1");
        $i=0; 
        if(mysqli_num_rows($allvendors)>0){
            foreach($allvendors as $vendor){
                $i++;
               
             echo '<tr style="cursor:pointer;"><td>'.$i.'</td><td onclick="popupload(\'reports?getvbills='.$vendor['id'].'\')">'.ucwords($vendor['name']).'</td><td onclick="popupload(\'reports?getvbills='.$vendor['id'].'\')">'.$vendor['contact'].'</td><td onclick="popupload(\'reports?getvbills='.$vendor['id'].'\')">'.ucwords($vendor['address']).'</td><td onclick="popupload(\'reports?editvendor='.$vendor['id'].'\')"><i class="bi-pencil" style="color:green;font-size:20px;cursor:pointer;"></i></td><td><i class="bi-trash" style="color:red;font-size:20px;cursor:pointer;" onclick="delevendor('.$vendor['id'].')"></i></td></tr>';
            }
        }else{
            echo '<tr><td colspan="6">You have no added any vendor to your list</td></tr>';
        }

    echo '</table></div>';
}
#update vendor
if(isset($_GET['editvendor'])){
    $vendor=$_GET['editvendor'];
        echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
                <div class="row no-gutters" style="justify-content:center;font-size:23px;"><h5>Update Vendor Details</h5></div>

                <form id="updatevendor" method="post" onsubmit="updtvendor(event)">
                <input type="hidden" value="'.$vendor.'" name="upid"';
                $getvendor=mysqli_query($con,"SELECT * FROM `vendors` WHERE `id`='$vendor'");
                foreach($getvendor as $cvendor){
                    echo '<p>Vendor name<br><input type="text" name="upvendor"  value="'.ucwords($cvendor['name']).'"style="width:100%;max-width:300px;" required></p>
                    <p>Contact<br><input type="number" name="upcont" style="width:100%;max-width:300px;" value="'.$cvendor['contact'].'" required></p>
                    <p>Email<br><input type="email" name="upmail" style="width:100%;max-width:300px;" value="'.$cvendor['email'].'" required></p>
                    <p>Address<br><input type="text" name="upaddress" style="width:100%;max-width:300px;" value="'.ucwords($cvendor['address']).'" required></p>
                    <p style="text-align:right"><button class="btnn" onclick="updtvendor(event)">Update</button></p>';
                }
                echo '</form>
                </div>';
    
}
#get items
if(isset($_GET['prods'])){
    $products='';
    $allitems=mysqli_query($con,"SELECT * FROM `products`");
    foreach($allitems as $item){
        $products.='<option value="'.$item['name'].'">'.$item['name'].'</option>';
    }
    echo $products;
}
#balancesheet
if(isset($_GET['blsheet'])){
    echo '<div class="table-responsive"><div class="card" style="min-width:600px">
    <table class="table-striped mx-auto" style="width:100%;min-width:600px;max-width:1200px;">
        <tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:20px;">
            <td colspan="2" style="text-align:center;font-weight:bold;">
            <div style="height:60px;width:80px;margin:0px auto;margin-top:10px;margin-bottom:-10px;"><img src="assets/img/logo.png" style="height:60px;width:80px;"/></div><br>
            Golden Vision Empowerment Organization<br>
                Balance Sheet as at '.date('d-m-Y',time()).'<br></td>
        </tr>
    <tr><td colspan="2" style="text-align:center;font-weight:bold;">Assets</td></tr>';
    $allassets=mysqli_query($con,"SELECT `ca`.* FROM `accounttypes` AS `at` INNER JOIN `chartsofaccount` AS `ca` ON `at`.`id`=`ca`.`category` WHERE `at`.`name` LIKE '%asset%'");
    $totalasset=0;
    foreach($allassets as $asset){
        $totalasset+=$asset['balance'];
        echo '<tr><td>'.$asset['name'].'</td><td>'.$asset['balance'].'</td></tr>';
    }
    $allrevenues=mysqli_query($con,"SELECT `ca`.* FROM `accounttypes` AS `at` INNER JOIN `chartsofaccount` AS `ca` ON `at`.`id`=`ca`.`category` WHERE `at`.`name` LIKE '%revenue%'");
    
    foreach($allrevenues as $revenue){
        $totalasset+=$revenue['balance'];
        echo '<tr><td>'.$revenue['name'].'</td><td>'.$revenue['balance'].'</td></tr>';
    }
    echo '<tr style="font-weight:bold;"><td style="text-align:center;border-top:1px solid;border-bottom:1px solid;">Total</td><td style="border-top:1px solid;border-bottom:1px solid;">'.$totalasset.'</td></tr>
    <tr><td colspan="2" style="text-align:center;font-weight:bold;">Liabilities And Shareholders` Equity</td></tr>';
    $allliabil=mysqli_query($con,"SELECT `ca`.* FROM `accounttypes` AS `at` INNER JOIN `chartsofaccount` AS `ca` ON `at`.`id`=`ca`.`category` WHERE `at`.`name` LIKE '%liabilit%'");
    $totalliab=0;
    foreach($allliabil as $liabil){
        $totalliab=+$liabil['balance'];
        echo '<tr><td>'.$liabil['name'].'</td><td>'.$liabil['balance'].'</td></tr>';
    }
    $allequity=mysqli_query($con,"SELECT `ca`.* FROM `accounttypes` AS `at` INNER JOIN `chartsofaccount` AS `ca` ON `at`.`id`=`ca`.`category` WHERE `at`.`name` LIKE '%equit%'");
    foreach($allequity as $equity){
        $totalliab+=$equity['balance'];
        echo '<tr><td>'.$equity['name'].'</td><td>'.$equity['balance'].'</td></tr>';
    }
    $allexpenses=mysqli_query($con,"SELECT `ca`.* FROM `accounttypes` AS `at` INNER JOIN `chartsofaccount` AS `ca` ON `at`.`id`=`ca`.`category` WHERE `at`.`name` LIKE '%expense%'");
    
    foreach($allexpenses as $expense){
        $totalliab+=$expense['balance'];
        echo '<tr><td>'.$expense['name'].'</td><td>'.$expense['balance'].'</td></tr>';
    }
   
    echo '<tr style="font-weight:bold;"><td style="text-align:center;border-top:1px solid;border-bottom:1px solid;">Total</td><td style="border-top:1px solid;border-bottom:1px solid;">'.$totalliab.'</td></tr><tr style="line-height:30px;padding:5px;"><td colspan="2" style="text-align:right;"></td></tr>';
    echo '</table>
    <p><div class="row no-gutters" style="text-align:right;justify-content:right;margin-right:20px;"><button style="min-width:70px;width:fit-content;height:40px;color:white;background:#2f79bd;border:1px solid;border-radius:5px;float:right;" onclick="bsprint()"><i class="fa fa-print" style="color:red;"></i> Print</button></div></p><br>
    </div></div>';
}
#income statement
if(isset($_GET['incomestt'])){
    echo '<div class="table-responsive"><div class="card" style="min-width:500px">
    <table class="table-striped mx-auto" style="width:100%;min-width:500px;max-width:900px;"><tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td colspan="2" style="text-align:center;font-weight:bold;">Income statement as at '.date('d-m-Y',time()).'</td></tr></table>';
}
#Add bank account
if(isset($_POST['bname'])){
    $name=clean($_POST['bname']);
    $account=clean($_POST['acnumber']);
    $check=mysqli_query($con,"SELECT * FROM `bank_accounts` WHERE `account`='$account'");
    if(mysqli_num_rows($check)<1){
    $insert=mysqli_query($con,"INSERT INTO `bank_accounts` (`id`,`bank`,`account`) VALUES(NULL,'$name','$account')");
    if($insert){
        echo 'success';//Admin!1
    }
    else{
        echo 'fail';
    }
    }
    else{
        echo 'fail';
    }
}

#deposit to banks
if(isset($_POST['bacnt'])){
    $act=clean($_POST['bacnt']);
    $acamount=clean($_POST['acamount']);
    $account=$_POST['chartaccount'];
    $time=time();
    $select=mysqli_query($con,"SELECT * FROM `bank_accounts` WHERE `id`='$act'");
    foreach($select as $se){
        $sel=$se['total'];$selname=$se['bank'];
    }
    $charts=mysqli_query($con,"SELECT `balance` FROM `chartsofaccount` WHERE `id`='$account' FOR UPDATE");
    foreach($charts as $tbal){
        $ttbal=$tbal['balance'];
    }
    $rembalance=$ttbal-$acamount;
    $amount=$sel+$acamount;
    if($rembalance>=0){
        mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$rembalance' WHERE `id`='$account'");
        $new= mysqli_query($con,"INSERT INTO `bankhistory`(id,`account`,`amount`,`type`,`time`) VALUES(NULL,'$act','$acamount',1,$time)");
        if($new){
            $insert= mysqli_query($con,"UPDATE `bank_accounts` SET `total`='$amount' WHERE `id`='$act'");
            if($insert){
                $trid='BD'.$_SESSION['csysuser'].time();
                $month=strtotime(date('Y-M',time()));
                mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$time','$trid','Bank deposit $selname','$acamount','$account')");
                $insert=mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$time','$month','$trid','Account credit','$acamount','0')");
                echo 'success';
            }else{
                mysqli_query($con,"DELETE FROM `bankhistory` WHERE `account`='$acamount' AND `time`='$time'");
                echo 'fail';
            }
        }else{
            echo 'fail';
        }  
    }
    else{
        echo 'Insufficient account balance';
    }
   
}
if(isset($_POST['receivefines'])){
    $dt=$_POST['receivefines'];
    $account=$_POST['debtacnt'];
    $nearr=explode(",",$dt);
    $month=strtotime(date('Y-M',time()));
    $state=true;
    $total=0;
    for($i=0;$i<count($nearr);$i++){
        $fid=$nearr[$i];
        $accounts=mysqli_query($con,"SELECT *  FROM `chartsofaccount` WHERE `id`='$account' FOR UPDATE");
        foreach($accounts AS $acc){
            $oldbalance=$acc['balance'];$newid=$acc['id'];
        }
        $selectfine=mysqli_query($con,"SELECT `amount`,`client`,`charges` FROM `fines` WHERE `id`='$fid' FOR UPDATE");
        foreach($selectfine as $fine){
            $fnid=$fine['charges'];
            $allnam=mysqli_query($con,"SELECT `name` FROM `chargetypes` WHERE `id`='$fnid'");
            foreach($allnam as $nm){
                $chname=$nm['name'];
            }
            $filcol=$fine['amount'];
        }
        $newbal=$oldbalance+$filcol;
        $total=$total+$filcol;
        $update=mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$newbal' WHERE `id`='$newid'");
        $finset=mysqli_query($con,"UPDATE `fines` SET `status`=1 WHERE `id`='$fid'");
        if($update && $finset){
            $stat=true;
        }else{
            mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$oldbalance' WHERE `id`='$newid'");
            mysqli_query($con,"UPDATE `fines` SET `status`=0 WHERE `id`='$fid'");
            $stat=false;
            break;
        }
    }
    if($stat){
        $time=time();
        $trid='CH'.$_SESSION['csysuser'].$time;
        mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$time','$month','$trid','$newid','Collection of $chname','$total')");
        echo 'success';
    }
    else{
        echo 'fail';
    }
}
#withdraw from bank
if(isset($_POST['waccount'])){
    $account=$_POST['waccount'];
    $amount=clean($_POST['wamount']);
    $time=time();
    $select=mysqli_query($con,"SELECT `total` FROM `bank_accounts`  WHERE `id`='$account'");
    foreach($select as $sel){
        $se=$sel['total'];
    }
    if($se>=$amount){
        $newal=$amount-$se;
        $new= mysqli_query($con,"INSERT INTO `bankhistory`(id,`account`,`amount`,`type`,`time`) VALUES(NULL,'$account','$amount',2,$time)");
        if($new){
            $insert= mysqli_query($con,"UPDATE `bank_accounts` SET `total`='$newbal' WHERE `id`='$account'");
            if($insert){
                echo 'success';
            }else{
                mysqli_query($con,"DELETE FROM `bankhistory` WHERE `account`='$account' AND `time`='$time'");
                echo 'fail';
            }
        }else{
            echo 'fail';
        }
    }
    else{
        echo 'fail';
    }
}

#add bill
if(isset($_POST['vname'])){
    $vname=clean($_POST['vname']);
    $product=clean(($_POST['items']));
    $ddate=clean($_POST['ddate']);
    $price=clean($_POST['uprice']);
    $quantity=clean($_POST['qty']);
    $desc=clean($_POST['desc']);
    $account=clean($_POST['debaccount']);
    $time=time();
    $balance=$price*$quantity;
    $insert=mysqli_query($con,"INSERT INTO `bills`(`id`,`vendor`,`qty`,`unit_price`,`balance`,`ddate`,`item`,`billid`) VALUES(NULL,'$vname','$quantity','$price','$balance','$ddate','$product','$desc')");

    if($insert){
        $updatecharts=mysqli_query($con,"SELECT `balance` FROM `chartsofaccount` WHERE `id`='$account' FOR UPDATE");
        foreach($updatecharts as $update){
            $newamt=$update['balance'];
        }
        $final=$newamt+$balance;
        $insert= mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$final' WHERE `id`='$account'");
        if($insert){
            $month=strtotime(date('Y-M',time()));
            $updt=mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$time','$month','$desc','$account','Unsettled $quantity $product bill','$balance')");
        
        if($updt){
            echo "success";
        }
        
        else{mysqli_query($con,"DELETE FROM `bills` WHERE `billid`='$desc'");
            echo 'fail';
        }
        }
    }else{
        mysqli_query($con,"DELETE `bills` WHERE `billid`='$desc'");
        echo 'fail';
    }

}
#pay bills
if(isset($_POST['bills'])){
    $bill=$_POST['bills'];
    $amount=clean($_POST['pamount']);
    $account=$_POST['craccount']; 
    $time=time();
    $newupdates=mysqli_query($con,"SELECT `balance` FROM `chartsofaccount` WHERE `id`='$account' FOR UPDATE");
    foreach($newupdates as $nwup){
        $finalbal=$nwup['balance'];
    }
    $rem=$finalbal-$amount;
    if($rem>=0){
        $select=mysqli_query($con,"SELECT `billid`,`balance`,`paid` FROM `bills` WHERE `id`='$bill'");
        foreach($select as $bll){
            $bamt=$bll['balance'];$paid=$bll['paid'];$desc=$bll['billid'];
        }

        if($bamt>=$amount){
            $bal=$bamt-$amount;
            $paidm=$paid+$amount;
            if($bal===0){
                if(mysqli_query($con,"UPDATE `bills` SET `balance`='$bal',`status`=1,`paid`='$paidm' WHERE `id`='$bill'")){
                    if(mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$rem' WHERE `id`='$account'")){
                        $month=strtotime(date('Y-M',time()));
                        $updt=mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$time','$month','$desc','$account','Payment of $desc bill','$amount')");
                        if($updt){
                     echo "success";}
                        }
                        else{
                            mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$bamt' WHERE `id`='$account'");
                            echo 'fail';
                        }
                    }
                    else{
                     echo 'fail';
                }
            }
            else{
                if(mysqli_query($con,"UPDATE `bills` SET `balance`='$bal',`paid`='$paidm' WHERE `id`='$bill'")){
                    if(mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$rem' WHERE `id`='$account'")){
                        $month=strtotime(date('Y-M',time()));
                        $updt=mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$time','$month','$desc','$account','Payment of $desc bill','$amount')");
                        if($updt){
                        echo 'success';
                        }
                        else{
                            mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$bamt' WHERE `id`='$account'");
                            echo 'fail';
                        }
                    }
                }
                else{
                    echo 'fail';
                }
            }

            }else{
                echo "incorrect";
            }
        }
        else{
            echo 'insufficient';
        }
}
#make purchase order
if(isset($_POST['supplier'])){
    $supplier=clean($_POST['supplier']);
    $data="{".rtrim($_POST['other'],",")."}";
    $delivery=clean($_POST['delivery']);$time=time();
    $poid="PO".$_SESSION['csysuser'].$time;
    $insert= mysqli_query($con,"INSERT INTO `purchase_orders`(`id`,`vendor`,`time`,`items`,`delivery`,`poid`) VALUES(NULL,'$supplier','$time','$data','$delivery','$poid')");
    if($insert){
        echo "success";
    }else{
        echo "fail";
    }
}
#record new order
if(isset($_POST['buyer'])){
    $type=$_POST['type'];
    $name=clean($_POST['buyer']);
    $orderid=$_POST['ref'];
    if($type=="1"){
        $all=mysqli_query($con,"SELECT `contact`,`address` FROM `groups` WHERE `name`='$name'");
        foreach($all as $me){
            $address=$me['address'];
            $contact=$me['contact'];
        }
    }
    if($type=="2"){
    $contact=clean($_POST['cont1']);
    $address=clean($_POST['address']);
}
    $data="{".rtrim($_POST['orderitems'],",")."}";
    $delivery=clean($_POST['delivery']);$time=time();
    $it=json_decode(str_replace("[",'',str_replace("]","",$data)),1);
    $t=array_chunk($it,3);
    $total=0;
    for($i=0;$i<count($t);$i++){
        $total=$total+($t[$i][2]*$t[$i][1]);
        }

    $insert= mysqli_query($con,"INSERT INTO `sales_orders`(`id`,`refid`,`customer`,`address`,`contact`,`items`,`delivery`,`totalprice`,`date`) VALUES(NULL,'$orderid','$name','$address','$contact','$data','$delivery','$total','$time')");
    if($insert){
        
        echo "success";
    }else{
        echo "fail".mysqli_error($con);
    }
}
#add newvendor
if(isset($_POST['nvendor'])){
    $vendor=clean($_POST['nvendor']);
    $contact=clean($_POST['vcont']);
    $mail=clean($_POST['vmail']);
    $address=clean($_POST['vaddress']);
    $insert=mysqli_query($con,"INSERT INTO `vendors`(`id`,`name`,`address`,`contact`,`email`) VALUES(NULL,'$vendor','$address','$contact','$mail')");
    if($insert){

        echo 'success';
    }else{
        echo 'fail'.mysqli_error($con);
    }
}
#add Assets
if(isset($_POST['asset'])){
    $asset=clean($_POST['asset']);
    $value=clean($_POST['assetvalue']);
    $date=time();
    $assetid='AS'.$_SESSION['csysuser'].time();
    $account=isset($_POST['subcatego'])? $_POST['subcatego']:$_POST['account'];
   $check=mysqli_query($con,"SELECT *  FROM `currentassets` WHERE `assetname`='$asset'");
   if(mysqli_num_rows($check)>0){
    echo 'Sorry, the asset is already in the system!';
   }
   else{
        $getvalue=mysqli_query($con,"SELECT `balance` FROM `chartsofaccount` WHERE `id`='$account' FOR UPDATE");
        foreach($getvalue as $gvalue){
            $acvalue=$gvalue['balance'];
        }
        $finalbalance=$acvalue+$value;
       $insert=mysqli_query($con,"INSERT INTO `currentassets`(`id`,`assetname`,`value`) VALUE(NULL,'$asset','$value')");
       if($insert){
        $update=mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$finalbalance' WHERE `id`='$account' AND `level`=0");
            if($update){
                $insert=mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$date','$month','$assetid','New $asset as asset acquired','$value','$account')");
                mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$date','$assetid','New $asset as asset acquired','$value','$account')");
                
                echo 'success'; 
            }
            else{
                mysqli_query($con,"DELETE FROM `currentassets` WHERE `assetname`='$asset'");
                echo 'fail';
            }
            
        }else{
            echo 'fail';
        }
    }
}
#accept purchase order
if(isset($_POST['acceptpo'])){
$po=$_POST['acceptpo'];
$due=clean($_POST['duedate']);
$time=time();
    $selectall =mysqli_query($con,"SELECT * FROM `purchase_orders` WHERE `id`='$po' FOR UPDATE");
    foreach($selectall as $select){
            $serilitem=explode(",",str_replace("{",'',str_replace("}",'',str_replace("[",'',str_replace("]",'',str_replace('"','',$select['items']))))));   
            $items=array_chunk($serilitem,3);
        }
        $ms='';
        for($i=0;$i<count($items);$i++){
            $product=explode(":",$items[$i][0])[1]; $quantity=explode(":",$items[$i][1])[1];$price=explode(":",$items[$i][2])[1];
            $updateinventory=mysqli_query($con,"INSERT INTO `inventory`(`id`,`product`,`quantity`,`price`,`delivery`) VALUES(NULL,'$product','$quantity','$price','$time')");
            if(!$updateinventory){
                $ms='fail';
                break;
            }
            else{
                $inventory= mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `name`='Inventory Asset' FOR UPDATE");
                foreach($inventory as $inv){
                    $crid=$inv['id'];$credbal=$inv['balance'];
                }
                $chrtacs=mysqli_query($con,"SELECT `balance`,`id` FROM `chartsofaccount` WHERE `name`='accounts payable' FOR UPDATE");
                foreach($chrtacs as $ch){
                    $chid=$ch['id'];$chbal=$ch['balance'];
                    }
                $getstock=mysqli_query($con,"SELECT `quantity` FROM `products` WHERE `name`='$product' FOR UPDATE");
                foreach($getstock AS $stock){
                    $stk=$stock['quantity'];
                }
                $total=$stk+$quantity;
                if(mysqli_query($con,"UPDATE `products` SET `quantity`='$total' WHERE `name`='$product'")){
                    $tbal=$price*$quantity;
                    $credbalance=$credbal-$tbal;
                    $vnd=$select['vendor'];
                    $cbillid='BID'.$_SESSION['csysuser'].time();
                    $ins=mysqli_query($con,"INSERT INTO `bills`(`id`,`vendor`,`billid`,`item`,`qty`,`unit_price`,`balance`,`ddate`,`status`) VALUES(NULL,'$vnd','$cbillid','$product','$quantity','$price','$tbal','$due',0)");
                    $nchartbalance=$chbal+$tbal;
                    $newcredbal=$credbal+$tbal;
                    $upch=mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$nchartbalance' WHERE `id`='$chid'");
                    $inventoryas=mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$nchartbalance' WHERE `id`='$crid'");
                    $trid='PO'.$_SESSION['csysuser'].time();
                    $insert=mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$time','$month','$trid','Add $quantity $product to payable','$tbal','$chid')");
                    $month=strtotime(date('Y-M',time()));
                    mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$time','$month','$trid','Add $quantity $product to inventory','$credbal','$crid')");
                    
                    if($ins && $insert && $upch){
                         $ms="success";
                    }
                    else{
                        mysqli_query($con,"DELETE FROM `bills` WHERE `status`=0 AND `item`='$product' AND `balance`='$tbal'");
                        mysqli_query($con,"UPDATE `balance`='$chbal' WHERE `id`='$chid'");
                        mysqli_query($con,"UPDATE `balance`='$credbal' WHERE `id`='$crid'");
                        mysqli_query($con,"DELETE FROM `transactions` WHERE `transactionid`='$trid'");
                        echo 'failup';
                    }
               }
            }
        }
        if($ms=="success"){
            $finish=mysqli_query($con,"UPDATE `purchase_orders` SET `status`=1 WHERE `id`='$po'");
            //$updateaccounts=mysqli_query($con,"INSERT INTO `cliabilities`(`id`,`name`,`amount`,`recepient`,`status`,`type`) VALUES(NULL,``)");
           if($finish){
            echo 'success';
           }
           else{
                echo 'faill';
           }
        }
        else{
        echo $ms;}
}
#print invoices
if(isset($_POST['invoicegen'])){
    $saleid=$_POST['invoicegen'];
    $date=time();
    $trid='IN'.$_SESSION['csysuser'].time();
    
   $select =mysqli_query($con,"SELECT * FROM `sales_orders` WHERE `id`='$saleid'");
   foreach($select as $sel){
        $items=$sel['items'];   
   }
   $itms=explode(',',str_replace('{','',str_replace('}','',str_replace('[','',str_replace(']','',$items)))));
    $itemarray=array_chunk($itms,3,',');
    for($i=0;$i<count($itemarray);$i++){
        $updateinventory=mysqli_query($con,"SELECT `balance`,`id` FROM `chartsofaccount` WHERE `name`='Inventory Asset' FOR UPDATE");
    foreach($updateinventory as $inv){
        $invbal=$inv['balance'];$invid=$inv['id'];
    }
    $account=mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `name` LIKE '%receivable%' FOR UPDATE");
    foreach($account as $acc){
        $acid=$acc['id'];$cbal=$acc['balance'];
    } 
        $name=trim(explode(":",$itemarray[$i][0])[1],'"');$qty=trim(explode(":",$itemarray[$i][1])[1],'"');$price=trim(explode(":",$itemarray[$i][2])[1],'"');
        $tpr=$qty*$price;
        $inventoryp=mysqli_query($con,"SELECT * FROM `products` WHERE `name`='$name' FOR UPDATE");
        foreach($inventoryp as $invp){
            $oldqty=$invp['quantity'];
        }
        $newqty=$oldqty-$qty;
        $fnal=$invbal-$tpr;
        if($newqty>=0){
        $newinv=mysqli_query($con,"UPDATE `products` SET `quantity`='$newqty' WHERE `name`='$name'");
        if($newinv){
            $month=strtotime(date('Y-M',time()));
        $insert=mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$date','$month','$trid','Items supply of $qty $name','$tpr','$acid')");

        if($insert){
            mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$date','$month','$trid','Items sale of $qty $name','$tpr','$invid'");
            $bl=$cbal+$tpr;
            $update= mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$bl' WHERE `id`='$acid'");
            $update2=mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$fnal' WHERE `id`='$invid'");
            if($update && $update2){
                mysqli_query($con,"UPDATE `sales_orders` SET `status`=2 WHERE `id`='$saleid'");
                echo 'success';
            }
            else{
                mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$cbal' WHERE `id`='$acid'");
                mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$invbal' WHERE `id`='$invid'");
                mysqli_query($con,"DELETE FROM `transactions` WHERE `date`='$date");
                echo 'fail'.mysqli_error($con);
            }
        }
    }
    else{
        echo 'fail'.mysqli_error($con);
    }
}else{
    echo 'fail';
}
    }
}
if(isset($_POST['acname'])){
    $name=clean($_POST['acname']);
    $category=clean($_POST['category']);
    $code= clean($_POST['account']);
    $desc=clean($_POST['descrip']);
    $sub=isset($_POST['subcat'])?true:false;
    $newsub=isset($_POST['nsubcat'])?true:false;
    //category=1&subcat=12&acname=morara&account=345&descrip=
    if($sub){
        $id=$_POST['subcat'];
        $currentbranch=mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `id`='$id' FOR UPDATE");
        foreach($currentbranch as $cbranch){
            $bal=$cbranch['balance'];$nodes=$cbranch['nodes'];$des=$cbranch['descendant'];
        }
        $newnode=mysqli_query($con,"INSERT INTO `chartsofaccount`(`id`,`category`,`name`,`descendant`,`accountcode`,`balance`,`level`)VALUES(NULL,'$category','$name',2,'$code','$bal',0)");
        if($newnode){
            $sel=mysqli_query($con,"SELECT `id` FROM `chartsofaccount` WHERE `name`='$name'");
            foreach($sel as $se){
                $nwd=$se['id'];
            }
            
            $nwn=rtrim(trim($nodes.','.$nwd,','),',');
            $update=mysqli_query($con,"UPDATE `chartsofaccount` SET `descendant`=1,`balance`=0,`nodes`='$nwn',`level`=1 WHERE `id`='$id'");
            if($update){echo 'success';}else{echo 'fail'.mysqli_error($con);}
        }else{echo 'fail'.mysqli_error($con);}
    }
    else{
        if($newsub){
            $nebs=$_POST['nsubcat'];
            $createsubcat= mysqli_query($con,"INSERT INTO `chartsofaccount`(`id`,`category`,`level`,`name`,`descendant`,`balance`) VALUES(NULL,'$category',1,'$nebs',1,0)");
            if($createsubcat){
                $insert= mysqli_query($con,"INSERT INTO `chartsofaccount`(`id`,`category`,`name`,`level`,`description`,`descendant`,`accountcode`,`balance`) VALUES(NULL,'$category','$name',0,'$desc',2,'$code',0)");
                if($insert){
                    $sel=mysqli_query($con,"SELECT `id` FROM `chartsofaccount` WHERE `name`='$name'");
                    foreach($sel as $se){
                        $nwd=$se['id'];
                    }
                    
                    $update=mysqli_query($con,"UPDATE `chartsofaccount` SET `descendant`=1,`balance`=0,`nodes`='$nwd',`level`=1 WHERE `name`='$nebs'");
                    if($update){echo 'success';}else{echo 'fail';}
                }else{
                    echo 'fail'.mysqli_error($con);
                }
            }else{echo 'fail'.mysqli_error($con);}
        }
        else{
         $insert=mysqli_query($con,"INSERT INTO `chartsofaccount`(`id`,`category`,`level`,`name`,`descendant`,`accountcode`,`balance`) VALUES(NULL,'$category',0,'$name',1,'$code',0)");
            if($insert){
                echo 'success';
            }else{
                echo'fail';
            }
        }
    }     
}
#delete vendor
if(isset($_POST['deletevendor'])){
    $id=$_POST['deletevendor'];
    $query=mysqli_query($con,"UPDATE `vendors` SET `status`=0 WHERE `id`='$id'");
    if($query){
        echo 'success';
    }else{
        echo 'fail';
    }
}
#update vendor
if(isset($_POST['upid'])){
    $uid=$_POST['upid'];
    $vname=clean($_POST['upvendor']);
    $contact=clean($_POST['upcont']);
    $mail=clean($_POST['upmail']);
    $address=clean($_POST['upaddress']);
    $update= mysqli_query($con,"UPDATE `vendors` SET `name`='$vname',`address`='$address',`contact`='$contact',`email`='$mail' WHERE `id`='$uid'");
    if($update){
        echo "success";
    }else{
        echo 'fail';
    }

}
#groups as customer
if(isset($_GET['groupcustomers'])){
    $allgroups=mysqli_query($con,"SELECT * FROM `groups` WHERE `status`=1");
    foreach ($allgroups as $group) {
        echo '<option value="'.$group['name'].'">';
    }
}
#add expense to pettycash
if(isset($_POST['ddt'])){
$date=strtotime($_POST['ddt']);
$descr=clean($_POST['description']);
$amount=clean($_POST['amount']);
$account=$_POST['accnt'];
$time=time();
$month=strtotime(date('M-Y',$time));
$trd='PTC'.$_SESSION['csysuser'].$time;
$balances=mysqli_query($con,"SELECT `balance` FROM `chartsofaccount` WHERE `id`='$account' FOR UPDATE");
 foreach($balances as $balance){
    $bal=$balance['balance'];
 }
 $res=$bal-$amount;
 if($res>=0){
    $insert=mysqli_query($con,"INSERT INTO `cliabilities`(`id`,`pid`,`description`,`month`,`date`,`amount`) VALUES(NULL,'$trd','$descr','$month','$date','$amount')");
    if($insert){
        if(mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$res' WHERE `id`='$account'")){
            $month=strtotime(date('Y-M',time()));
           if(mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$time','$month','$trd','$account','$descr','$amount')")){
             echo 'success';
           }
           else{
            echo 'fail'.mysqli_error($con);
           }
        }
        else{
            mysqli_query($con,"DELETE FROM `cliabilities` WHERE `pid`='$trd'");
            echo 'fail';
        }
    }
    else{
        echo 'fail';
    }
    
 }
 else{
    echo 'insufficient';
 }
}
if(isset($_POST['ptamount'])){
    $account=$_POST['ptaccnt'];
    $amount=$_POST['ptamount'];
    $time=time();
    $trd='PTC'.$_SESSION['csysuser'].$time;
    $balances=mysqli_query($con,"SELECT `balance` FROM `chartsofaccount` WHERE `id`='$account' FOR UPDATE");
    foreach($balances as $balance){
        $bal=$balance['balance'];
    }
    $res=$bal-$amount;
    if($res>=0){
        $oldbalance=mysqli_query($con,"SELECT `balance`,`id` FROM `chartsofaccount` WHERE `name` LIKE '%petty%' LIMIT 1 FOR UPDATE ");
        foreach($oldbalance AS $oldbl){
            $oldbal=$oldbl['balance'];$pid=$oldbl['id'];
        }
        $fnalbal=$oldbal+$amount;
        $newbal=mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$fnalbal' WHERE `id`='$pid'");
        if($newbal){
            $update=mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$res' WHERE `id`='$account'");
            if($update){
                $month=strtotime(date('Y-M',time()));
                mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$time','$month','$trd','$account','Debit Petty Cash Account','$amount')");
                mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$time','$month','$trd','$pid','Credit to Petty Cash Account','$amount')");
                echo 'success';
            }
            else{
                mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$oldbal' WHERE `id`='$pid'");
                echo 'fail';
            }
        }
        else{echo 'fail';}
    }
    else{
        echo 'insufficient';
    }
}
if(isset($_POST['stdorder'])){
    $amount=clean($_POST['currentpay']);
    $payee=$_POST['stdorder'];
    $account=$_POST['raccount'];
    $time=time();
    $select=mysqli_query($con,"SELECT `balance`,`id` FROM `chartsofaccount` WHERE `name` LIKE '%receivable%' LIMIT 1 FOR UPDATE");
    foreach($select as $sel){
        $pid=$sel['id'];$recbal=$sel['balance'];
    }
    $newreceivable=$recbal-$amount;
    $update=mysqli_query($con,"SELECT `balance`,`id` FROM `chartsofaccount` WHERE `id`='$account' FOR UPDATE");
    foreach($update as $upd){
        $fbal=$upd['balance'];
    }
    $dedbal=$fbal+$amount;
    $saleorder=mysqli_query($con,"SELECT `paid`,`totalprice`,`refid` FROM `sales_orders` WHERE `id`='$payee' FOR UPDATE");
    foreach($saleorder as $sale){
        $paid=$sale['paid'];$total=$sale['totalprice'];$unique=$sale['refid'];
    }
    $totalpaid=$paid+$amount;
    if($totalpaid<=$total){
        $saleupdate=mysqli_query($con,"UPDATE `sales_orders` SET `paid`='$totalpaid' WHERE `id`='$payee'");
        $updatereceivable=mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$newreceivable' WHERE `id`='$pid'");
        $updatedebit=mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$dedbal' WHERE `id`='$account'");
        if($saleupdate && $updatereceivable && $updatedebit){
            $month=strtotime(date('Y-M',time()));
            mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$time','$month','$unique','$account','debit of order $unique','$amount')");
            mysqli_query($con,"INSERT INTO `transactions` VALUES(NULL,'$time','$month','$unique','$pid','Credit of order $unique','$amount')");
            echo 'success';
        }
        else{
            mysqli_query($con,"UPDATE `sales_orders` SET `paid`='$paid' WHERE `id`='$payee'");
            mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$recbal' WHERE `id`='$pid'");
            mysqli_query($con,"UPDATE `chartsofaccount` SET `balance`='$fbal' WHERE `id`='$account'");
            echo 'fail';
        }
    }
    else{
        echo 'excess';
    }
}
mysqli_close($con);

?>
