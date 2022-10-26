<?php
session_start();
require 'functions.php';
$con = mysqli_connect("localhost",DB_USER,DB_PASS,DB_NAME);

if(isset($_GET['fetch'])){
    echo '<div class="container-fluid" style="min-height:300px;">
    <div class="row no-gutters"><h4 style="font-size:23px;">Employee List</h4></div>
    <div class="row no-gutters">
    <div class="col-6 float-left" style="padding-left:10px;"><input type="search" style="width:150px;outline:none;" placeholder=" Employee"></div><div class="col-6 float-right" style="text-align:right;"><button class="btnn" onclick="popupload(\'settings.php?addemployee\')"><i class="fas fa-user-plus"></i> Employee</button></div></div>
    <div class="table-responsive">
    <table class="table-striped" style="min-width:600px;width:100%;margin-top:20px;">
    <tr style="line-height:30px;background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;"><td>Employee name</td><td>Phone Number</td><td>Email</td><td>Position</td></tr><tbody id="empbody">';
    $allemployees=mysqli_query($con,"SELECT * FROM `employeetb` WHERE `status`=1");
    foreach($allemployees as $employee){
        $pos=$employee['position'];
        $getpos=mysqli_query($con,"SELECT `name` FROM `employee_positions` WHERE `id`='$pos'");
        foreach($getpos AS $pos){
            $position=$pos['name'];
        }
    echo '<tr><td>'.ucwords($employee['name']).'</td><td>0'.$employee['phone'].'</td><td>'.$employee['email'].'</td><td>'.$position.'</td></tr>';
}
    echo '</tbody></table></div></div>';
}

?>