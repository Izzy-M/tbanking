<?php
	require "functions.php";
	$con = mysqli_connect("localhost",DB_USER,DB_PASS,DB_NAME);
    $now=time();
    $loanbalance= mysqli_query($con,"SELECT `id`,`paid`,`history`,`loan`,`amount`,`loantype`,`deadline`,`overdue` FROM `loans` WHERE `paid`<`history` AND `deadline`<'$now' FOR UPDATE");
    foreach($loanbalance as $loanb){
       
    }