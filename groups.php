<?php
session_start();
if(isset($_SESSION['grptable'])){
$ccgrp=$_SESSION['grptable'];
}
	require "functions.php";
	$con = mysqli_connect("localhost",DB_USER,DB_PASS,DB_NAME);
	#Edit group
	if(isset($_GET['egroup'])){
		$grp=clean($_GET['egroup']);
		echo '<div class="col-8 mx-auto" style="max-width:300px;">
		<div class="row no-gutters" style="justify-content:center;"><h3 style="text-align:center;color:#191970;font-size:23px;margin:0px">Edit Group</h3></div>
		<form method="post" id="egroup">';

		$group=mysqli_query($con,"SELECT * FROM `groups` WHERE `id`='$grp'");
		foreach($group as $gr){
		echo '<input type="hidden" value="'.$gr['id'].'" name="cgroup">
		<p><div class="row no-gutters">Group name</div>
		<div class="row no-gutters"><input type="text" style="width:100%;max-width:300px;" name="grpname" value="'.$gr['name'].'"></div></p>
		<p><div class="row no-gutters">Group code</div>
		<div class="row no-gutters"><input type="text" style="width:100%;max-width:300px;" name="grpcode" value="'.$gr['gid'].'"></div></p>
		<p><div class="row no-gutters">Group e-mail</div>
		<div class="row no-gutters"><input type="email" style="width:100%;max-width:300px;" name="mail" value="'.$gr['email'].'"></div></p>
		<p><div class="row no-gutters">Group address</div>
		<div class="row no-gutters"><input type="text" style="width:100%;max-width:300px;" name="address" value="'.$gr['address'].'"></div></p>
		<p><div class="row no-gutters">Group contact</div>
		<div class="row no-gutters"><input type="tel" style="width:100%;max-width:300px;" name="contact" value="'.$gr['contact'].'"></div></p>
		<p><div class="row no-gutters">Location</div>
		<div class="row no-gutters"><input type="text" style="width:100%;max-width:300px;" name="grploc" value="'.$gr['location'].'"></div></p>
		<p><div class="row no-gutters">Account No:</div>
		<div class="row no-gutters"><input type="text" style="width:100%;max-width:300px;" name="grpac" value="'.$gr['bankaccount'].'"></div></p>
		<p><div class="row no-gutters">Bank branch</div>
		<div class="row no-gutters"><input type="text" style="width:100%;max-width:300px;" name="branch" value="'.$gr['bankbranch'].'"></div></p>';
		}
		echo '<div class="row no-gutters" style="justify-content:end;"><button class="btn btn-success">Update</button></div></form></div>';
	}
	# view groups
	if(isset($_GET['fetch'])){
		echo "<div style='max-width:1240px;margin:0 auto'>
			<h3 style='font-size:23px;'>Member Groups</h3>
			<div class='row no-gutters' style='justify-content:right;'><button class='btnn' style='padding:6px;font-size:15px;float:right;' onclick=\"popupload('groups?add')\"><i class='bi-person-plus'></i> Group</button></div>
			<div class='row no-gutters' style='justify-content:space-between;margin-top:10px;margin-bottom:20px;'><div style='min-width:40px;max-width:100px;flot:left;'><input type='search' placeholder='Search group' name='groupsearch' style='min-width:100px;max-width:250px;font-size:16px;' onkeyup='searchgroupname(this.value)'></div><div style='float:right;'id='pgnation'>";
			$perpage=20;
			$allmembers=mysqli_query($con,"SELECT * FROM `groups`");
			$total=mysqli_num_rows($allmembers);
			$pages=ceil($total/$perpage);
			echo "<select onchange='nextgroup($pages,$perpage,this.value)'>";
			for($i=0;$i<$pages;$i++){
				$clr=$i==0?"selected":'';
				echo '<option '.$clr.'" value="'.$i.'">'.($i+1).'</option>';
			}
			echo "</select></div></div>
			<table style='width:100%;font-family:cambria;font-size:14px;' class='table-striped'>
			<tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;'><td>SN</td><td>Name</td><td>Group ID</td>
				<td>Members</td><td>Status</td></tr><tbody class='mtb1'>";
			$trs=""; $no=0;
				$sql = mysqli_query($con,"SELECT *FROM `groups` ORDER BY `gid` ASC LIMIT $perpage");
				foreach($sql as $row){
					$rid=$row['id']; $name=prepare(ucwords($row['name'])); $gid=$row['gid']; $no++;
					$chk = mysqli_query($con,"SELECT *FROM `members` WHERE `status`=1 AND `mgroup`='$rid'"); $sum=mysqli_num_rows($chk);
					if($row['status']==1){
					echo "<tr style='cursor:pointer;height:30px;'><td onclick='opengroup($rid)'>$no</td><td onclick='opengroup($rid)'>$name</td><td onclick='opengroup($rid)'>$gid</td><td onclick='opengroup($rid)' >$sum</td><td style='cursor:pointer;color:green;' onclick='editgroup($rid)'>Active</td></tr>";}
					else if($row['status']==4){
						echo "<tr style='cursor:not-allowed;height:30px;'><td>$no</td><td>$name</td><td>$gid</td><td>$sum</td><td style='color:red;'>Deleted</td></tr>";
					}
					else if($row['status']==3){
						echo "<tr style='cursor:not-allowed;height:30px;'><td>$no</td><td>$name</td><td>$gid</td><td>$sum</td><td style='color:#f8c753;'>Suspended</td></tr>";
					}
				}

			echo "</tbody></table></div></div>";
	}
	#load all groups
	if(isset($_GET['loadgroups'])){
		$perpage=20;
		$trs=""; $no=0;
		$sql = mysqli_query($con,"SELECT * FROM `groups` ORDER BY `name` ASC LIMIT $perpage");
		echo "<tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria'><td colspan='2'>Name</td><td>Group ID</td>
		<td>Members</td><td>Action</td></tr>";
		foreach($sql as $row){
			$rid=$row['id']; $name=prepare(ucwords($row['name'])); $gid=$row['gid']; $no++;
			$chk = mysqli_query($con,"SELECT *FROM `members` WHERE `status`=1 AND `mgroup`='$rid'"); $sum=mysqli_num_rows($chk);
			if($row['status']==1){
				echo "<tr style='cursor:pointer;'><td onclick='opengroup($rid)'>$no</td><td onclick='opengroup($rid)'>$name</td><td onclick='opengroup($rid)'>$gid</td><td onclick='opengroup($rid)' >$sum</td><td onclick='editgroup($rid) style='cursor:pointer;text-align:center;color:green;'>Active</td></tr>";}
			else{
				echo "<tr style='cursor:pointer;'><td>$no</td><td>$name</td><td>$gid</td><td  >$sum</td><td style='cursor:pointer;text-align:center;color:red;'>Deleted</td></tr>";
				}
			}
		echo '<tr><td colspan=2 style="text-align:right;"></td></tr>';
	}
	# add group 
	if(isset($_GET['add'])){
		echo "<div style='max-width:300px;margin:0 auto;'>
			<h3 style='text-align:center;color:#191970;font-size:23px;margin:0px'>Add Group</h3><br><br>
			<form method='post' id='gform' onsubmit=\"savegroup(event)\">
				<p>Group name<br><input type='text' name='gname' style='width:100%' required></p>
				<p>Group code<br><input type='text' name='gcode' style='width:100%' required></p>
				<p>Group Email<br><input type='email' name='mail' style='width:100%' required></p>
				<p>Group Address<br><input type='text' name='address' style='width:100%' required></p>
				<p>Group Contact<br><input type='tel' name='contact' style='width:100%' required></p>
				<p>Group Location<br><input type='text' name='loc' style='width:100%' required></p>
				<p>Group A/C<br><input type='text' name='acnt' style='width:100%' required></p>
				<p>Bank Branch<br><input type='text' name='branch' style='width:100%' required></p>
				<br>
				<p style='text-align:right'><button class='btnn'>Create</button></p><br>
			</form><br>
		</div><br>";
	}
	#view manage group
	if(isset($_GET['viewgroup'])){
		$grp=$_GET['viewgroup'];
		$selectgrp=mysqli_query($con,"SELECT `name` FROM `groups` WHERE `id`='$grp' AND `status`=1");
		foreach($selectgrp as $grps){
			echo '<div class="row no-gutters" style="height:40px;width:100%;justify-content:center;"><h4>'.ucwords(str_replace('group','',strtolower($grps['name']))).' Group </h4></div>';
			}
		echo '<div class="table-responsive style="margin-top:-15px;max-width:1200px;height:40px;"><div class="operations" style="margin-bottom:10px;overflow-x:auto;flex-wrap:nowrap;display:flex;flex-direction:row;overflow-x:auto;overflow-y:unset;min-width:875px;scrollbar-width:none;-ms-overflow-style:none;font-size:16px;">';
		if(isset($ccgrp)){
			echo '<div class="btnn1" onclick="getgroupops('.$ccgrp.')">Members</div>
			<div class="btnn1" onclick="groupcharges('.$ccgrp.')">Fines and Charges</div>
			<!--<div class="btnn1" onclick="investmoney('.$ccgrp.')">Deposit</div>-->
			<div class="btnn1" onclick="giveloan('.$ccgrp.')">Loan</div>
			<div class="btnn1" onclick="addexpenses('.$ccgrp.')">Add Expense</div>
			<div class="btnn1" onclick="summary('.$ccgrp.')">Summary</div>
			<div class="btnn1" onclick="officeexp('.$ccgrp.')">Office Expense</div>
			<div class="btnn1" onclick="extdebts('.$ccgrp.')">Take Debts</div>
			<div class="btnn1" onclick="gethistory('.$ccgrp.')">More options<i class="fas fa-chevron-down"></i></div>
			<div class="card more" style="width:150px;min-height:100px;z-index:10;margin-top:20px;top:50px;position:absolute;display:none;z-index:9999">
			<div id="options" style="width:100%;padding-left:10px;margin-top:5px;margin-bottom:5px;color:#235a81;cursor:pointer;" onclick="getgroupsavings('.$ccgrp.')">Group Savings</div>
			<div id="options" style="width:100%;padding-left:10px;margin-top:5px;margin-bottom:5px;color:#235a81;cursor:pointer;" onclick="fundsin('.$ccgrp.')">Funds In</div>
			<div id="options" style="width:100%;padding-left:10px;margin-top:5px;margin-bottom:5px;color:#235a81;cursor:pointer;" onclick="getfundsout('.$ccgrp.')">Funds Out</div>
			<div id="options" style="width:100%;padding-left:10px;margin-top:5px;margin-bottom:5px;color:#235a81;cursor:pointer;" onclick="withdraw('.$ccgrp.')">Cash Withdraw</div>
			<div id="options" style="width:100%;padding-left:10px;margin-top:5px;margin-bottom:5px;color:#235a81;cursor:pointer;" onclick="groupexpe('.$ccgrp.')">Group Expenses</div>
			<div id="options" style="width:100%;padding-left:10px;margin-top:5px;margin-bottom:5px;color:#235a81;cursor:pointer;" onclick="getgolden('.$ccgrp.')">GKid Funds Out</div>
			<div id="options" style="width:100%;padding-left:10px;margin-top:5px;margin-bottom:5px;color:#235a81;cursor:pointer;" onclick="externaldebts('.$ccgrp.')">External Debts</div>
			<div id="options" style="width:100%;padding-left:10px;margin-top:5px;margin-bottom:5px;color:#235a81;cursor:pointer;" onclick="getxmass('.$ccgrp.')">Xmass Funds Out</div>
			<div id="options" style="width:100%;padding-left:10px;margin-top:5px;margin-bottom:5px;color:#235a81;cursor:pointer;" onclick="paydebts('.$ccgrp.')">Pay Group Debts </div>
			<div id="options" style="margin-bottom:5px;width:100%;padding-left:10px;margin-top:5px;margin-bottom:5px;color:#235a81;cursor:pointer;" onclick="managegroup('.$ccgrp.')">Manage Members</div><br></div></div></div><div id="content">';
		}
			echo '<div class="row no-gutters" style="display:flex;flex-direction:row;flex-wrap:nowrap;">
		<div class="col-12 grpview" style="min-height:400px;width:98%;margin:0 10px;">
		<div class="table-responsive"><div class="table-responsive" style="min-width:550px;">	
			<table class="table-striped" style="width:100%;font-size:12px;">
			<tr><td><h5>Cash In</h5></td><td><h5>Cash Out</h5></td></tr><tr><td style="width:50%;padding:0 5px;vertical-align:top;">
			<div class="row no-gutters" style="justify-content:center;font-weight:600;">Fines and Charges</div>
			<table style="width:100%;max-width:900px;min-width:200px;"><tr><th>Member</th><th>Type</th><th>Amount</th></tr>';
			$first=strtotime("first day of last month");
			$last=strtolower("last day of last month");
			$cashin=0;
				$fines=mysqli_query($con,"SELECT `fin`.`amount`,`chty`.`name`,`memb`.`membernumber`,`memb`.`name` as `mna` FROM `fines` AS `fin` INNER JOIN `members` AS `memb` ON `fin`.`client`=`memb`.`id` INNER JOIN `chargetypes` AS `chty` ON `fin`.`charges`=`chty`.`id` WHERE `memb`.`mgroup`='$ccgrp'");
				$totalfines=0;
				foreach($fines as $fine){
					echo '<tr><td>'.$fine['mna'].'</td><td>'.$fine['name'].'</td><td style="width:80px;">'.$fine['amount'].'/=</td></tr>';
						$totalfines=$totalfines+$fine['amount'];
					
					}
				$cashin=$cashin+$totalfines;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$totalfines.'/= </td></tr></table><br>
			<div class="row no-gutters" style="justify-content:center;font-weight:600;">Loan Repayment</div>
			<table style="width:100%;max-width:900px;min-width:200px;"><tr style="font-weight:bold;"><th>Name</th><th>Loan</th><th>Amount</th></tr>';
			$repayment=mysqli_query($con,"SELECT `membernumber`,`paid`,`appfee`,`phistory`,`loan`,`overdue` FROM `members` AS `mb` INNER JOIN `loans` AS `ln` ON `mb`.`id`=`ln`.`client` WHERE `mgroup`='$ccgrp' AND `paid`>0")
			;$loans=0;$app=0;
			foreach($repayment as $repay){
				$application=[];
				$paid=$repay['paid']-$repay['overdue'];
				$tv=0;
				foreach(json_decode($repay['phistory'],1) as $k=>$v){
					$tv=$tv+$v;
					array_push($application,array($repay['membernumber']=>$repay['appfee']));
				if($k<$last){
				$app=$app+$repay['appfee'];$loans=$loans+$repay['paid'];
					echo '<tr><td>'.$repay['membernumber'].'</td><td>'.$repay['loan'].'</td><td style="width:80px;">'.($loans-$app).'</td></tr>';
					}
				}
				$overdue=($tv-($repay['paid']-$repay['appfee'])-$repay['overdue']);
				
			}
			$loans=$loans-$app;
			$cashin=$cashin+$loans;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$loans.'/=</td></tr></table><br>
			
			<div class="row no-gutters" style="font-weight:600;justify-content:center;font-weight:600;">Loan Application Fee</div>
			<table style="width:100%;max-width:900px;min-width:200px"><tr style="font-weight:bold;"><td colspan="2">Member name</td><td>Amount</td></tr>';
			$repayment=mysqli_query($con,"SELECT `ap`.`history`,`m`.`name`,`ap`.`loan`FROM `groups` AS `gr` INNER JOIN `members` AS `m` ON `m`.`mgroup`=`gr`.`id` INNER JOIN `applicationfee` AS `ap` ON `ap`.`client`=`m`.`id` WHERE `gr`.`id`='$ccgrp'");
			$fee=0;
			foreach($repayment as $repay){
				foreach(json_decode($repay['history'],1) as $k=>$v){
				$fee=$fee+$v;
					echo '<tr><td>'.$repay['name'].'</td><td>'.$repay['loan'].'</td><td style="width:80px;">'.($v).'</td></tr>';
				}
			}
			$cashin=$cashin+$fee;
			echo '<tr style="font-weight:600;background:white;border-top:2px solid;border-bottom:1px solid;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$fee.'/=</td></tr></table><div class="row no-gutters" style="justify-content:center;font-weight:600;">Group Savings</div><table style="width:100%;min-width:200px;max-width:900px;"><tr><th>Name</th><th>Type</th><th>Amount</th></tr>';
			$savs=mysqli_query($con,"SELECT SUM(`sav`.`saving`) AS `saving`,`mb`.`name`,`dep`.`type` FROM `savings` AS `sav` INNER JOIN `members` AS `mb` ON `mb`.`id`=`sav`.`client` INNER JOIN `deposittypes` AS `dep` ON `sav`.`type`=`dep`.`id` WHERE `mgroup`='$ccgrp' AND `sav`.`saving`>0 GROUP BY `dep`.`type`,`mb`.`name`");
			$savn=0;
			if(mysqli_num_rows($savs)>0){
			foreach($savs as $sav){
				$savn=$savn+$sav['saving'];
				echo '<tr><td>'.ucwords($sav['name']).'</td><td>'.$sav['type'].'</td><td style="width:80px;">'.$sav['saving'].'</td></tr>';}
			}
			$cashin= $cashin+$savn;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;">'.$savn.'/=</td></tr></table>';
			echo '<div class="row no-gutters" style="justify-content:center;font-weight:600;">External Debts</div><table style="width:100%;min-width:200px;max-width:900px;"><tr><th>Lenders</th><th>Amount</th></tr>';
			$debts=mysqli_query($con,"SELECT `dbt`.`amount`,`dbt`.`time`,`lg`.`name`,`dbt`.`paid` FROM `debts` AS `dbt` INNER JOIN `lenders` AS `lg` ON `dbt`.`lender`=`lg`.`id` WHERE `dbt`.`borrower`='$ccgrp' AND `dbt`.`paid`<`dbt`.`amount`");
			$amount=0;
			foreach($debts as $debt){
				echo '<tr><td>'.ucwords($debt['name']).'</td><td>'.($debt['amount']-$debt['paid']).'</td></tr>';
				$amount=$amount+$debt['amount'];
			}
			$cashin=$cashin+$amount;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td style="text-align:center;">Subtotal</td><td style="text-align:end;" >'.$amount.'/=</td></tr></table>
			</div>
			</td><td style="display:flex;flex-direction:column;padding:0 5px">
			<div class="row no-gutters" style="justify-content:center;font-weight:600">Loans Borrowed</div><table style="min-width:200px;max-width:900px"><tr><th>Name</th><th>loan</th><th>Amount</th><tr>';
			$cashout=0;
			$repayment=mysqli_query($con,"SELECT `ln`.`phistory`,`ln`.`amount`,`lty`.`name`,`mb`.`name` AS `mname` FROM `members` AS `mb` INNER JOIN `loans` AS `ln` ON `ln`.`client`=`mb`.`id` INNER JOIN `loantype` AS `lty` ON `ln`.`loantype`=`lty`.`id` WHERE `mb`.`mgroup`='$ccgrp' AND `ln`.`paid`>0");
			$loans=0;
				foreach($repayment as $repay){
					foreach(json_decode($repay['phistory'],1) as $k=>$v){
					if($k>=$first && $k<=$last){
						echo '<tr><td>'.$repay['mname'].'</td><td>'.$repay['name'].'</td><td style="width:80px;">'.$repay['amount'].'</td></tr>';
					$loans=$loans+$v;}
					}

				}
			$cashout=$cashout+$loans;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;">'.$loans.'/=</td></table>
			<div class="row no-gutters" style="justify-content:center;font-weight:600;">External Debt paid</div><table style="min-width:200px;max-width:900px;"><tr><th>Group</th><th>date</th><th>Amount</th></tr>';
				$debtspaid=mysqli_query($con,"SELECT `gr`.`name`,`db`.* FROM `debts` AS `db` INNER JOIN `groups` AS `gr` ON `db`.`borrower`=`gr`.`id` WHERE `lender`='$ccgrp' AND `paid`>0");
				$paid=0;
				foreach($debtspaid as $debt){
					foreach(json_decode($debt['payments'],1) as $key=>$value){
						if($key>=$first){
							echo '<tr><td>'.$debt['name'].'</td><td>'.date("d/m/Y",$key).'</td><td>'.$value.'</td><tr>';
							$paid=$paid+$value;
						}
					}
				}
				$cashout=$cashout+$paid;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$paid.'/=</td></table>
			<div class="row no-gutters" style="justify-content:center;font-weight:600;margin-top:5px;">Group Expenditure</div><table style="min-width:200px;max-width:900px;"><tr><th>Expense</th><th>Paid To</th><th>Amount</th></tr>';
				$expenses=mysqli_query($con,"SELECT `paid`,`expense`,`receiver` FROM `expenses` WHERE `groupid`='$ccgrp' AND `time`>$first");
				$exp=0;
				foreach($expenses as $expense){
					echo '<tr><td>'.$expense['expense'].'</td><td>'.$expense['receiver'].'</td><td style="width:80px;">'.$expense['paid'].'</td></tr>';
					$exp=$exp+$expense['paid'];
				}
				$cashout=$cashout+$exp;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$exp.'/=</td></tr></table><table style="min-width:200px;max-width:900px;"><tr><th>Expense</th><th>Date</th><th>Amount</th></tr><div class="row no-gutters" style="font-weight:600;justify-content:center;">Office Expense</div>';
				$office=mysqli_query($con,"SELECT `payment`,`time`,`amount` FROM `officeexpense` WHERE `grp`='$ccgrp' AND `time`>$first");
				$ofexp=0;
				foreach($office as $ofc){
					echo '<tr><td>'.$ofc['payment'].'</td><td>'.date("d/m/Y",$ofc['time']).'</td><td style="width:80px;">'.$ofc['amount'].'</td><tr>';
					$ofexp=$ofexp+$ofc['amount'];
				}
				$cashout=$cashout+$ofexp;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$ofexp.'/=</td></tr></table>
			<div class="row no-gutters" style="justify-content:center;font-weight:600;">Member Withdraws</div><table class="table-stripped"><tr><th>Name</th><th>Date</th><th style="width:80px;">Amount</th></tr>';
			$withdraws=mysqli_query($con,"SELECT `as`.`amount`,`mb`.`name`,`as`.`time` FROM `account_withdraws` AS `as` INNER JOIN `members` AS `mb` ON `mb`.`id`=`as`.`client` WHERE `mb`.`mgroup`='$ccgrp' AND `as`.`time`>='$first'");
			$amt=0;
			foreach($withdraws as $withd){
				$amt=$amt+$withd['amount'];
				echo '<tr><td>'.$withd['name'].'</td><td>'.date('d/m/Y',$withd['time']).'</td><td style="width:80px;">'.$withd['amount'].'/=</td></tr>';
			}
			$cashout=$cashout+$amt;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;">
				<td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$amt.'/=</td></tr></table></td></tr>
			<tr style="line-height:35px;background:white;font-size:16px;font-weight:600;">
				<td><div class="row no-gutters"><div class="col-8" style="text-align:right;">Total</div>
			<div class="col-4" style="text-align:right;">'.$cashin.'/=</div></td><td>
			<div class="row no-gutters"><div class="col-8" style="text-align:right;">Total</div>
			<div class="col-4" style="text-align:right;">'.$cashout.'/=</div></td></tr></table></div>
			<div class="col-12"><div class="row no-gutters"><span style="font-weight:600;">Group Accumulatives</span></div>';
			$normalloans=mysqli_query($con,"SELECT SUM(`ln`.`paid`) AS `paid`, SUM (ln.`history`) AS `history` FROM `loans` as `ln` INNER JOIN `members` AS `mb` ON `ln`.`client`=`mb`.`id` INNER JOIN `loantype` as `lt` ON `ln`.`loantype`=`lt`.`id` WHERE `mb`.`mgroup`='$ccgrp' AND `ln`.`name` LIKE '%normal%'");
			$normal=0;
			if($normalloans){
				foreach($normalloans as $normals){
					$normal=$normal+($normals['history']-$normals['paid']);
				}
			}
			$advanceloans=mysqli_query($con,"SELECT SUM(`ln`.`paid`) AS `paid`, SUM (ln.`history`) AS `history` FROM `loans` as `ln` INNER JOIN `members` AS `mb` ON `ln`.`client`=`mb`.`id` INNER JOIN `loantype` as `lt` ON `ln`.`loantype`=`lt`.`id` WHERE `mb`.`mgroup`='$ccgrp' AND `ln`.`name` LIKE '%advance%'");
			$advance=0;
			if($advanceloans){
				foreach($advanceloans as $advances){
					$advance=$advance+($advances['history']-$advances['paid']);
				}
			}
			$investimentloans=mysqli_query($con,"SELECT SUM(`ln`.`paid`) AS `paid`, SUM (ln.`history`) AS `history` FROM `loans` as `ln` INNER JOIN `members` AS `mb` ON `ln`.`client`=`mb`.`id` INNER JOIN `loantype` as `lt` ON `ln`.`loantype`=`lt`.`id` WHERE `mb`.`mgroup`='$ccgrp' AND `ln`.`name` LIKE '%investiment%'");
			$investiment=0;
			if($investimentloans){
				foreach($investimentloans as $investiments){
					$investiment=$investiment+($investiments['history']-$investiments['paid']);
				}
			}

			$banking=mysqli_query($con,"SELECT `cpool` FROM `groups` WHERE `id`='$ccgrp'");
			$bank=0;
			foreach($banking as $banks){
				$bank=$banks['cpool'];
			}
			$riskfund=mysqli_query($con,"SELECT SUM(`amount`) As `tt` FROM `riskfund` AS `rs` INNER JOIN `members` AS `mb` ON `mb`.`id`=`rs`.`client` WHERE `mb`.`mgroup`='$ccgrp'");
			$ttrisk=0;
			foreach($riskfund as $risk){
				$ttrisk=$risk['tt'];
			}
			$exdebt=mysqli_query($con,"SELECT SUM(`amount`) AS `amount`, SUM(`paid`) As `paid` FROM `debts` WHERE `borrower`='$ccgrp'");
			$exdb=0;
			foreach($exdebt as $debt){
				$exdb=$debt['amount']-$debt['paid'];
			}
			$less=$ttrisk+$exdb;
			$trf1= ($normal+$advance+$investiment+$bank)-$less;
			$collections=mysqli_query($con,"SELECT `ln`.`amount` AS `tt`,`lt`.`name` FROM `loans` as `ln` INNER JOIN `members` AS `mb` ON `ln`.`client`=`mb`.`id` INNER JOIN `loantype` as `lt` ON `ln`.`loantype`=`lt`.`id` WHERE `mb`.`mgroup`='$ccgrp' GROUP BY `lt`.`name`");
			//var_dump($collections);
			$ttsavings=mysqli_query($con,"SELECT `savings` FROM `groups` WHERE `id`='$ccgrp'");
			foreach($ttsavings as $ttsav){
				$totalsavings=$ttsav['savings'];
			}
			$normalsavings=mysqli_query($con,"SELECT SUM(`sv`.`saving`) as `total` FROM `savings` AS `sv` INNER JOIN `members` as `mb` ON `sv`.`client`=`mb`.`id` INNER JOIN `deposittypes` as `dp` ON `dp`.`id`=`sv`.`type` WHERE `mb`.`mgroup`='$ccgrp' AND `dp`.`type` LIKE '%normal%'");
			$normsave=0;
			foreach($normalsavings as $normalsv){
				$normsave=$normsave+$normalsv['total'];
			}
			$investsavings=mysqli_query($con,"SELECT SUM(`sv`.`saving`) as `total` FROM `savings` AS `sv` INNER JOIN `members` as `mb` ON `sv`.`client`=`mb`.`id` INNER JOIN `deposittypes` as `dp` ON `dp`.`id`=`sv`.`type` WHERE `mb`.`mgroup`='$ccgrp' AND `dp`.`type` LIKE '%normal%'");
			$invesave=0;
			foreach($investsavings as $investsv){
				$invesave=$invesave+$investsv['total'];
			}
			$gf1=$trf1-($invesave+$normsave);
			$christmassavings=mysqli_query($con,"SELECT SUM(`sv`.`saving`) as `total` FROM `savings` AS `sv` INNER JOIN `members` as `mb` ON `sv`.`client`=`mb`.`id` INNER JOIN `deposittypes` as `dp` ON `dp`.`id`=`sv`.`type` WHERE `mb`.`mgroup`='$ccgrp' AND `dp`.`type` LIKE '%christmass%'");
			$chrissave=0;
			foreach($christmassavings as $christsv){
				$chrissave=$chrissave+$christsv['total'];
			}
			$goldensavings=mysqli_query($con,"SELECT SUM(`sv`.`saving`) as `total` FROM `savings` AS `sv` INNER JOIN `members` as `mb` ON `sv`.`client`=`mb`.`id` INNER JOIN `deposittypes` as `dp` ON `dp`.`id`=`sv`.`type` WHERE `mb`.`mgroup`='$ccgrp' AND `dp`.`type` LIKE '%christmass%'");
			$goldensave=0;
			foreach($goldensavings as $cgoldensv){
				$goldensave=$goldensave+$cgoldensv['total'];
			}
			$trf2=$trf1+$chrissave+$goldensave;
			echo '<div class="row no-gutters"><div class="col-col-md-6 col-sm-10 col-lg-5" style="justify-content:space-between;display:flex;flec-direction:row;"><span style="font-weight:600;">TRF1</span> <span style="font-weight:600;">'.$trf1.' KSH</span></div></div>
			<div class="row no-gutters"><div class="col-col-md-6 col-sm-10 col-lg-5" style="justify-content:space-between;display:flex;flec-direction:row;"><span style="font-weight:600;">Total Savings</span> <span style="font-weight:600;">'.$totalsavings.' KSH</span></div></div>
			<div class="row no-gutters"><div class="col-col-md-6 col-sm-10 col-lg-5" style="justify-content:space-between;display:flex;flec-direction:row;"><span style="font-weight:600;">GF1</span> <span style="font-weight:600;">'.$gf1.' KSH</span></div></div>
			<div  class="row no-gutters"><div class="col-col-md-6 col-sm-10 col-lg-5" style="justify-content:space-between;display:flex;flec-direction:row;"><span style="font-weight:600;">TRF2</span> <span style="font-weight:600;">'.$trf2.' KSH</span></div></div>
			<div class="row no-gutters"><div class="col-col-md-6 col-sm-10 col-lg-5" style="justify-content:space-between;display:flex;flec-direction:row;"><span style="font-weight:600;">GF2</span> <span style="font-weight:600;">0 KSH</span></div></div>
			</div></div>';

		 echo '</div>';
	}
	#fetch all members
	if(isset($_GET['groupopts'])){
		$grp=$_GET['groupopts'];
		echo '</div><div class="row no-gutters" style="justify-content:right;margin:0 20px;"><button class="btnn" style="float:right;" onclick="newgroupmember()"><i class="bi-person-plus"></i> Add Member</button></div><div class="row no-gutters" style="display:flex;flex-direction:row;flex-wrap:nowrap;">
		';
		$allmembers=mysqli_query($con,"SELECT `mb`.*,`gp`.`position` FROM `members` AS `mb` INNER JOIN `grouppositions` AS `gp` ON `mb`.`pos`=`gp`.`id` WHERE `mb`.`mgroup`='$ccgrp' AND `mb`.`status`=1");
			if(mysqli_num_rows($allmembers)>0){
		echo '<table class="table-striped" style="width:100%;min-width:530px;margin:0px auto;" id="cgrouptable">
		<tr style="background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;">
		<td style="padding-left:5px;">Member</td><td>Member Number</td><td style="padding-left:5px;">Total Savings</td>
		<td style="padding-left:5px;">Total Loan</td><td>Position</td><td style="text-align:center;">Activity</td></tr>';
		
		foreach($allmembers as $all){
			$cli=$all['id'];
			$clipos=$all['pos'];
			$loans=mysqli_query($con,"SELECT `history`,`paid` FROM `loans` WHERE `client`='$cli'");$amt=0;
			foreach($loans as $loan){
				$amt=$amt+($loan['history']-$loan['paid']);
			}
				$sv=mysqli_query($con,"SELECT `saving` FROM `savings` WHERE `client`='$cli'");$sav=0;
			foreach($sv as $s){
				$sav=$sav+$s['saving'];
			}
			$optio='<button style="border:1px solid green;background:green;color:white;border-radius:1px;min-width:50px;margin:5px;" onclick="newactivity('.$all['id'].')">Select</button>';
			echo '<tr style="margin:5px;padding:5px;"><td>'.ucwords($all['name']).'</td><td>'.$all['membernumber'].'</td><td style="cursor:pointer;" onclick="displaySavings('.$all['id'].')">'.$sav.'</td><td style="padding:2px;cursor:pointer;" onclick="getloans('.$all['id'].')">'.$amt.'</td><td style="cursor:pointer;" onclick="changepos('.$grp.','.$all['id'].')">'.ucwords($all['position']).'</td><td style="text-align:center;">'.$optio.'</td></tr>';
		
			 }
	 
	  echo '<tr style="background:#ffffff;line-height:30px;"><th colspan="4" style="padding:0px 20px;"><div class="row no-gutters"><div></th></tr></table></div></div></div>';
		}
		else{
		echo '<div class="row no-gutters" style="height:auto;border:none;width:95%;margin-top:2%;align-items:left;justify-content:left;color:#ff8800;">Oops! It seems like there is no member who has been registerd in this group!</div>';
		}
	
	}
	#invest money
	if(isset($_GET['investmoney'])){
		$group=$_GET['investmoney'];
		echo '<div style="text-align:center;"><h3 style="text-align:center;color:#191970;font-size:23px;margin:0px">Deposit Money</h3></div>
		<div class="col-8 mx-auto" style="max-width:300px;">
		<form method="post" id="invest">
		<input type="hidden" name="ingroup" value="'.$group.'">
		<p><div class="row no-gutters">Select Member</div>
		<div class="row no-gutters"><select style="max-width:300px;width:100%;" name="investor">';
		$membs=mysqli_query($con,"SELECT `id`,`membernumber` FROM `members` WHERE `status`=1 AND `mgroup`='$group'");
		if(mysqli_num_rows($membs)>0){
		foreach($membs as $memb){
		echo '<option value="'.$memb['id'].'">'.$memb['membernumber'].'</option>';}
		}
		else{echo '<option value="0">Not available</option>';}
		
		echo '</select></div></p>
		<p><div class="row no-gutters">Saving Type</div>
		<div class="row no-gutters"><select style="max-width:300px;width:100%;" name="type">';
		$deptyps= mysqli_query($con,"SELECT `id`,`type` FROM `deposittypes`");
		if(mysqli_num_rows($deptyps)>0){
		foreach($deptyps as $det){
			echo '<option value="'.$det['id'].'">'.$det['type'].'</option>';
		}
		}
		else{
			echo '<option value="0">No Availabe deposit type</option>';
		}
		echo'</select></div></p>
		<p><div class="row no-gutters">Deposit Amount</div>
		<div class="row no-gutters"><input type="number" style="width:100%;max-width:300px;" name="amount" required></div></p>
		<p><div class="row no-gutters">Date deposited</div>
		<div class="row no-gutters"><input type="date" style="width:100%;max-width:300px;" max="'.date('Y-m-d',time()).'"name="date" value="'.date('Y-m-d',time()).'" required></div></p>
		<div class="row no-gutters" style="justify-content:end;margin-top:10px;"><button class="btn btn-success">Save</div><br>
		</form></div>';
	}
	#lend money
	if(isset($_GET['loanmoney'])){
		$grp=$_GET['loanmoney'];
		echo '<div style="text-align:center;"><h3 style="text-align:center;color:#191970;font-size:23px;margin:0px">Request Loan</h3></div>
		<div class="col-8 mx-auto" style="max-width:300px;">
		<form id="loanreq" method="POST">
		<input type="hidden" name="loangrp" value="'.$grp.'">
		<p><div class="row no-gutters">Loan beneficiary</div>
		<div class="row no-gutters"><select style="max-width:300px;width:100%;" name="client" onchange="changemember('.$grp.',this.value)">';
		$getmember=mysqli_query($con,"SELECT `name`,`id`,`membernumber` FROM `members` WHERE `status`=1 AND `mgroup`='$grp'");
		$sar=[];
		if(mysqli_num_rows($getmember)>0){
			foreach($getmember as $cmb){
				array_push($sar,$cmb['id']);
				echo '<option value="'.$cmb['id'].'">'.ucwords($cmb['membernumber'].' '.$cmb['name']).'</option>';
			}
		}else{
			echo '<option>--No available member--option>';
		}
		
		echo '</select></div></p><p><div class="row no-gutters">Loan amount</div>
		<div class="row no-gutters"><br><input type="number" style="width:100%;max-width:300px;" required name="amount"></div></p>
		
		<p><div class="row no-gutters">Loan Type</div>
		<div class="row no-gutters"><select style="width:100%;max-width:300px;" name="loantype">';
		$lnt=mysqli_query($con,"SELECT * FROM `loantype` WHERE `status`=1");
		if(mysqli_num_rows($lnt)>0){
			foreach($lnt as $ltype){
				echo '<option value="'.$ltype['id'].'">'.ucwords($ltype['name']).'</option>';
			}
		}
		else{
			echo '<option>-- No loans added--</option>';
		}
		
		echo '</select></div></p>
		<p><div class="row no-gutters">Gurantor</div>
		<div class="row no gutters"><select style="width:100%;max-width:300px;" name="gur">';
		$members=mysqli_query($con,"SELECT `name`,`id`,`membernumber` FROM `members` WHERE `status`=1 AND `mgroup`='$grp'");
		
		if(mysqli_num_rows($members)>0){
		foreach($members as $member){
			if($member['id']!=$sar[0]){
		echo '<option value="'.$member['id'].'">'.ucwords($member['membernumber'].' '.$member['name']).'</option>';}
			}
		}else{
			$b="disabled";
			echo '<option>-- No Available Member --</option>';
		}
		echo '</select></div></p><p><div class="row no-gutters">Date of issue</div><div class="row no-gutters"><input type="date" style="width:100%;outline:none;" max="'.date('Y-m-d',time()).'"name="date" value="'.date('Y-m-d',time()).'" required></div></p><p><div class="row no-gutters">Payment period(in months)</div><div class="row no-gutters"><input type="number" min="1" style="max-width:300px;width:100%;" name="repayplan"></div></p><p><div class="row no-gutters">Loan Application Fee</div>
		<div class="row no-gutters"><input type="number" style="width:100%;max-width:300px" name="fee"></div><p>
		<p><div class="row no-gutters">Collateral</div>
		<div class="row no-gutters"><input type="text" style="width:100%;max-width:300px;" name="collat"></div><p><br>
		<div class="row no-gutters" style="justify-content:end;margin-top:10px;"><button class="btn btn-success" '.$b.'>Request</div><br>
		</form>
		</div>';
	}
	#change member
	if(isset($_GET['giveloanto'])){
		$cmemb=$_GET['giveloanto'];
		$grp=$_GET['cgroup'];
		echo '<div style="text-align:center;"><h3 style="text-align:center;color:#191970;font-size:23px;margin:0px">Request Loan</h3></div>
		<div class="col-8 mx-auto" style="max-width:300px;">
		<form id="loanreq" method="POST">
		<input type="hidden" name="loangrp" value="'.$grp.'">
		<p><div class="row no-gutters">Loan beneficiary</div>
		<div class="row no-gutters"><select style="max-width:300px;width:100%;" name="client">';
		
		$getmember=mysqli_query($con,"SELECT `name`,`id`,`membernumber` FROM `members` WHERE `status`=1 AND `mgroup`='$grp' AND `id`='$cmemb'");
		if(mysqli_num_rows($getmember)>0){
			
			foreach($getmember as $cmb){
				$sel=$cmb==$cmemb?"selected":'';
				$csel=$cmb[0];
				echo '<option value="'.$cmb['id'].'" >'.ucwords($cmb['membernumber'].' '.$cmb['name']).'</option>';
			}
		}else{
			echo '<option value="0">No available member</option>';
		}
		echo '</select></div></p>
		
		<p><div class="row no-gutters">Loan amount</div>
		<div class="row no-gutters"><br><input type="number" style="width:100%;max-width:300px;" required name="amount"></div></p>
		<p><div class="row no-gutters">Loan Type</div>
		<div class="row no-gutters"><select style="max-width:300px;width:100%;" name="loantype">';
		$lnt=mysqli_query($con,"SELECT * FROM `loantype` WHERE `status`=1");
		if(mysqli_num_rows($lnt)>0){
			foreach($lnt as $ltype){
				echo '<option value="'.$ltype['id'].'">'.ucwords($ltype['name']).'</option>';
			}
		}
		else{
			echo '<option>No loans is available</option>';
		}
		
		echo '</select></div></p>
		<p><div class="row no-gutters">Gurantor'.$csel.'</div>
		<div class="row no gutters"><select style="max-width:300px;width:100%;" name="gur">';
		$members=mysqli_query($con,"SELECT `name`,`id`,`membernumber` FROM `members` WHERE `status`=1 AND `mgroup`='$grp' AND `id` NOT IN ('$cmemb')");
		foreach($members as $member){
			if($member['id']!==$lender){
				echo '<option value="'.$member['id'].'">'.ucwords($member['membernumber'].''.$member['name']).'</option>';
			}
		}
		echo '</select></div></p><p><div class="row no-gutters">Date of issue </div>
		<div class="row no-gutters"><input type="date" style="width:100%;outline:none;" max="'.date('Y-m-d',time()).'"name="date" value="'.date('Y-m-d',time()).'" required></div></p><p><div class="row no-gutters">Repayment period(in months)</div><div class="row no-gutters"><input type="number" min="1" style="max-width:300px;width:100%;" name="repayplan"></div></p>
		<p><div class="row no-gutters">Loan Application Fee</div>
		<div class="row no-gutters"><input type="number" style="max-width:300px;width:100%;" name="fee"></div><p>
		<p><div class="row no-gutters">Collateral</div>
		<div class="row no-gutters"><input type="text" style="max-width:300px;width:100%;" name="collat"></div><p><br>
		<div class="row no-gutters" style="justify-content:end;margin-top:10px;"><button class="btn btn-success">Request</div><br>
		</form>
		</div>';
		
	}
	#pay loan
	if(isset($_GET['loanpay'])){
		$payee=$_GET['loanpay'];
		$grp=$_GET['grp'];
		echo '<div class="col-8 mx-auto" style="max-width:300px;">
		<div class="row no-gutters" style="justify-content:center;"><h3 style="text-align:center;color:#191970;font-size:23px;margin:0px;">Pay Loan</h3></div>
		<form id="payloan" method="POST">
		<input type="hidden" name="payee" value="'.$payee.'">
		<input type="hidden" name="lngr" value="'.$grp.'">
		<div class="row no-gutters">Payment Amount</div>
		<div class="row no-gutters"><input type="number" style="width:100%;max-width:300px;" name="amount" min="5" required></div><br>
		<div class="row no-gutters">Loans</div>
		<div class="row no-gutters"><select style="max-width:300px;width:100%;" name="cloan">';
		$getloans=mysqli_query($con,"SELECT `id`,`loantype`,`loan`,`paid`,`history` FROM `loans` WHERE `client`='$payee'");
			if(mysqli_num_rows($getloans)>0){
			foreach($getloans as $loans){
				$balance=$loans['history']-$loans['paid'];
				if($balance>0){
				$id=$loans['loantype'];
					$loantps=mysqli_query($con,"SELECT * FROM `loantype` WHERE `id`='$id'");
					foreach($loantps as $loan){
						$nm=$loan['name'];
						}
					echo '<option value="'.$loans['id'].'">'.$nm.' Loan Balance &nbsp; &nbsp;'.$balance.'</option>';
					}
				 }
		}
		else{echo '<option value="0">No Available Loan</option>';}
		
		echo '</select></div><br>
		<div class="row no-gutters" style="margin-top:10px;justify-content:end;"><button class="btn btn-success"'.$brn.'>Pay Loan</button></div><br>
		</form>
		</div>';
	}
	//collect fines
	if(isset($_GET['charges'])){
		$group=$_GET['charges'];
		echo '<div class="col-8 mx-auto" style="max-width:300px;">
		<div class="row no-gutters" style="justify-content:center;"><h4 style="text-align:center;color:#191970;font-size:23px;margin:0px;">Charges and Fines</h4></div>
		<form id="charges" method="POST">
		<input type="hidden" name="groupid" value="'.$group.'">
		<p><div class="row no-gutters">Select Member</div>
		<div class="row no-gutters"><select style="max-width:300px;width:100%;" name="userid">';
		$members=mysqli_query($con,"SELECT `id`,`name` FROM `members` WHERE `status`=1 AND `mgroup`='$group'");
		foreach($members as $member){
			if($member['id']!==$lender){
				echo '<option value="'.$member['id'].'">'.ucwords($member['name']).'</option>';
			}
		}
		echo '</select></div></p><p><div class="row no-gutters">Select charges</div>
		<div class="row no-gutters"><select style="max-width:300px;width:100%;" onchange="changefine(this.value,'.$group.')" name="charges">';
		$charges=mysqli_query($con,"SELECT `id`,`name`,`fixed` FROM `chargetypes`");
		if(mysqli_num_rows($charges)>0){
		foreach($charges as $fine){
				echo '<option value="'.$fine['id'].'">'.ucwords($fine['name']).'</option>';
			}
		}
		else{
			echo '<option value="0">No charges are Available</option>';
		}
		
		echo '</select></div></p>';
		
		echo '<p><div class="row no-gutters">Fine Amount</div>';
		$chargeslim=mysqli_query($con,"SELECT `amount`,`name`,`fixed` FROM `chargetypes` LIMIT 1");
		foreach($chargeslim as $lim){
		echo'<div class="row no-gutters"><input type="number" style="width:100%;max-width:300px;" id="typ" name="amount"  min="'.$lim['amount'].'" required></div></p><br>';
		}
		echo '<div class="row no-gutters" style="justify-content:end;"><button class="btn btn-success">Update</button></div><br>
		</form>
		</div>';
	}
	#changefine
	if(isset($_GET['changefine'])){
	$group=$_GET['group'];
	$fin=clean($_GET['changefine']);
		echo '<div class="col-8 mx-auto" style="max-width:300px;">
		<div class="row no-gutters" style="justify-content:center;"><h4 style="text-align:center;color:#191970;font-size:23px;margin:0px;">Charges and Fines</h4></div>
		<form id="charges" method="POST">
		<input type="hidden" name="groupid" value="'.$group.'">
		<p><div class="row no-gutters">Select Member</div>
		<div class="row no-gutters"><select style="max-width:300px;width:100%;" name="userid">';
		$members=mysqli_query($con,"SELECT `id`,`name` FROM `members` WHERE `status`=1 AND `mgroup`='$group'");
		foreach($members as $member){
			if($member['id']!==$lender){
				echo '<option value="'.$member['id'].'">'.ucwords($member['name']).'</option>';
			}
		}
		echo '</select></div></p><p><div class="row no-gutters">Select charges</div>
		<div class="row no-gutters"><select style="max-width:300px;width:100%;" onchange="changfine(this.value,'.$group.')" name="charges">';
		$charges=mysqli_query($con,"SELECT `id`,`name`,`fixed` FROM `chargetypes`");
		if(mysqli_num_rows($charges)>0){
		foreach($charges as $fine){
				echo '<option value="'.$fine['id'].'">'.ucwords($fine['name']).'</option>';
			}
		}
		else{
			echo '<option value="0">No charges are Available</option>';
		}
		
		echo '</select></div></p>';
		
		echo '<p><div class="row no-gutters">Fine Amount<br></div>';
		$chargeslim=mysqli_query($con,"SELECT `amount`,`name`,`fixed` FROM `chargetypes` WHERE `id`='$fin'");
		foreach($chargeslim as $lim){
		echo'<div class="row no-gutters"><input type="number" style="width:100%;max-width:300px;" id="typ" name="amount"  min="'.$lim['amount'].'" required></div></p><br>';
		}
		echo '<div class="row no-gutters" style="justify-content:end;"><button class="btn btn-success">Update</button></div><br>
		</form>
		</div>';
	}
	#get individual loans
	if(isset($_GET['memberloans'])){
		$loanee=$_GET['memberloans'];
		$member=mysqli_query($con,"SELECT `name` FROM `members` WHERE `id`='$loanee'");
		foreach($member as $mb){
			$cuser=$mb['name'];
		}
		echo '<div class="col-10 mx-auto">
		<div class="row no-gutters" style="justify-content:center;"><h5>'.ucwords($cuser).' Loan Details</h5></div>';
		$today=strtotime("today");
		$get=mysqli_query($con,"SELECT `amount`,`history`,`paid`,`loantype`,`time` FROM `loans` WHERE `client`='$loanee' AND `history`>`paid`");
			if($get){
				echo '<table style="width:100%;max-width:600px;" class="table-striped"><th>Loan Amount</th><th>Loan Type</th><th>Day</th>';
				$total=0;
				foreach($get as $gt){
				$bl=$gt['history']-$gt['paid'];
				$id=$gt['loantype'];
				$total=$total+$bl;
				if($bl>0){
				$selloan=mysqli_query($con,"SELECT `id`,`name` FROM `loantype` WHERE `id`='$id'");
				foreach($selloan as $la){
				echo '<tr style="line-height:30px;"><td>'.$bl.'</td><td>'.$la['name'].'</td><td>'.@date("d/m/Y",$gt['time']).'</td></tr>';}
				}
			}
			echo '<tr style="line-height:30px;"><td colspan="2" style="text-align:right;"><span style="font-weight:bold;">Total Loan:</span></td><td style="text-align:center;">'.$total.'KSH.</td></tr>';}
			else{
				echo '<tr><td colspan="3">No loan records</td></tr>';
			}
			echo '</table>';
	
		echo '<div class="row no-gutters"></div>
		</div>';
	}
	
	#get individual savings
	if(isset($_GET['getsavings'])){
		$memberid=$_GET['getsavings'];
		$member=mysqli_query($con,"SELECT `name` FROM `members` WHERE `status`=1 AND `id`='$memberid'");
		foreach($member as $mb){
			$cuser=$mb['name'];
		}
		echo '<div class="col-10 mx-auto">
		<div class="row no-gutters" style="justify-content:center;"><h4>'.ucwords($cuser).' Saving Details</h4></div>';
		$get=mysqli_query($con,"SELECT `saving`,`time`,`type` FROM `savings` WHERE `client`='$memberid' AND  `saving` >0");
		
			echo '<table style="width:100%;max-width:600px;" class="table-striped"><th>Savings Amount</th><th>Saving Type</th><th>Day</th>';
			
			if($get){
				$svs=0;
			foreach($get as $gt){
				$id=$gt['type'];
				$svs=$svs+$gt['saving'];
				$savtypes=mysqli_query($con,"SELECT `type` FROM `deposittypes` WHERE `id`='$id'");
				foreach($savtypes as $svt){
				echo '<tr style="line-height:30px;"><td>'.$gt['saving'].'</td><td>'.ucwords($svt['type']).'</td><td>'.@date("d/m/Y",$gt['time']).'</td></tr>';}
			}
			echo '<tr style="line-height:30px;"><td colspan="2" style="text-align:right;"><span style="font-weight:bold;">Total Savings:</span></td><td style="text-align:center;">'.$svs.'KSH.</td></tr>';
		}else{
			echo '<tr><td colspan="3">Saving details are not available!</td></tr>';
		}
		echo '</table>
		<div class="row no-gutters" style="justify-content:right;margin-top:20px;"><div style="float:right;background:;border:1px solid;line-height:20px;padding:10px 5px;min-width:60px;border-radius:10px;cursor:pointer;"><i class="fa fa-print" style="font-size:20px;color:;"></i> &nbsp; <pan style="font-weight:600;">Print</span></div></div></div>';
	}
	#change member position
	if(isset($_GET['changepos'])){
		$id=$_GET['changepos'];
		$grp=$_GET['grp'];
		echo '<div class="col-8 mx-auto" style="max-width:300px">
		<form id="membership" method="post">
		<input type="hidden" name="cuserid" value="'.$id.'">';
		$get=mysqli_query($con,"SELECT * FROM `members` WHERE `status`=1 AND `mgroup`='$grp' AND `id`='$id'");
		foreach($get as $gmb){
		echo '<div class="row no-gutters" style="justify-content:center;"><h4>Update member post</h4></div></p>
		<p>Member name<br>'.ucwords($gmb['name']).'</p>
		<p>Member Position<br><select style="max-width:300px;width:100%;" name="cpos">';
		$roles=mysqli_query($con,"SELECT * FROM `grouppositions`");
		if(mysqli_num_rows($roles)>0){
			foreach($roles as $role){
			$cpos=$gmb['pos']==$role['id']?'selected':'';
			
		echo '<option value='.$role['id'].' '.$cpos.'>'.$role['position'].'</option>';}
		}

		}
		echo '</select></p><br><div class="row no-gutters" style="justify-content:end;"><button class="btn btn-success">Update</button></div><br></form></div>';
	}
	if(isset($_GET['currentgroup'])){
		$grp=$_GET['currentgroup'];
		echo '<tr style="background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;">
			<td style="padding-left:5px;">Member Name</td><td style="padding-left:5px;">Total Savings</td>
			<td style="padding-left:5px;">Total Loan</td><td>Position</td><td style="text-align:center;">Activity</td></tr>';
			$sel=mysqli_query($con,"SELECT *FROM `members` WHERE `status`=1 AND `mgroup`='$grp'");
			foreach($sel as $all){
				$cli=$all['id'];
				$clpos=$all['pos'];
				$roles=mysqli_query($con,"SELECT * FROM `grouppositions` WHERE `id`='$clpos'");
				foreach($roles as $role){
					$res=$role['position'];
				}
				$loans=mysqli_query($con,"SELECT `history`,`paid` FROM `loans` WHERE `client`='$cli'");$amt=0;
				foreach($loans as $loan){$amt=$amt+($loan['history']-$loan['paid']);}
				$option='<button style="border:1px solid green;background:green;color:white;border-radius:1px;min-width:50px;margin:5px;" onclick="newactivity('.$all['id'].')">Select</button>';
				$sv=mysqli_query($con,"SELECT `saving` FROM `savings` WHERE `client`='$cli'");$sav=0;
				foreach($sv as $s){$sav=$sav+$s['saving'];}
				echo '<tr style="margin:5px;padding:5px;"><td>'.ucwords($all['name']).'</td><td style="cursor:pointer;" onclick="displaySavings('.$all['id'].')">'.$sav.'</td><td style="padding:2px;cursor:pointer;" onclick="getloans('.$all['id'].')">'.$amt.'</td><td style="cursor:pointer;" onclick="changepos('.$grp.','.$all['id'].')">'.$res.'</td><td style="text-align:center;">'.$option.'<td></tr>';
			
			
		 }
		  echo '<tr style="line-height:30px;"><td colspan="4"></td></tr><tr style="background:#ffffff;line-height:30px;"><th colspan="4" style="padding:0px 20px;"><div class="row no-gutters"><div></th></tr>';
		
	}
#take debt
	if(isset($_GET['takedebt'])){
		$grp=$_GET['takedebt'];
		echo '<div class="col-8 mx-auto" style="max-width:300px;">
		<div class="row no-gutters" style="justify-content:center;"><h3 style="text-align:center;color:#191970;font-size:23px;margin:0px">External Debts</h3></div>
		<form id="exdt" method="post">
		<input type="hidden" name="tgrp" value="'.$grp.'">
		<p><div class="row no-gutters">Lender type</div>
		<div class="row no-gutters"><select style="max-width:300px;width:100%;" onchange="lendercategory(this.value)">';
		$fcy=[];
		$fingroups=mysqli_query($con,"SELECT `category` FROM `lenders` GROUP BY `category`");
		if(mysqli_num_rows($fingroups)>0){
			foreach($fingroups AS $fngrps){
				array_push($fcy,$fngrps['category']);
				echo '<option value="'.$fngrps['category'].'">'.ucwords($fngrps['category']).'</option>';
			}

		}
		else{
			echo '<option>-- Not Available --</option>';
		}
		echo '</select></div></p>
		<p><div class="row no-gutters">Lender group</div>
		<div class="row no-gutters"><select name="lenderid" id="lenderid" style="max-width:300px;width:100%;">';
		$categories= mysqli_query($con,"SELECT `id`,`name` FROM `lenders` WHERE `category`='$fcy[0]'");
		if(mysqli_num_rows($categories)>0){
			foreach($categories AS $cat){
				echo '<option value="'.$cat['id'].'">'.ucwords($cat['name']).'</option>';
			}
		}
		else{
			echo '<option>-- No category --</option>';
		}
		echo '</select></div></p>
		<p><div class="row no-gutters">Debt amount</div>
		<div class="row no-gutters"><input type="number" name="damount" style="width:100%;"></div></p>
		<p><div class="row no-gutters">Period</div>
		<div class="row no-gutters"><input type="number" name="period" style="width:100%;"></div></p><br>
		<p><div class="row no-gutters" style="justify-content:end;"><button class="btn btn-success">Add
		</button></div></p></form>
		</div>
		</div>';
	}
	##Funds in
	if(isset($_GET['fundsin'])){
		$fund=$_GET['fundsin'];
		$gdetails=mysqli_query($con,"SELECT `name` FROM `groups` WHERE `id`='$fund'");
		foreach($gdetails as $gdtl){$name=$gdtl['name'];}
	echo '<div style="width:95%;margin:0 auto;">
	<div class="row no-gutters" style="justify-content:center;background:#f0f0f0;line-height:40px;font-weight:600;">'.ucwords($name).' Funds In History</div>
	<div class="row no-gutters"><table class="table-striped" style="width:100%;">
	<tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Activity</td><td>Member</td><td>Day</td><td>Amount</td></tr>';
	$members=mysqli_query($con,"SELECT `id`,`name` FROM `members` WHERE `status`=1 AND `mgroup`='$fund'");
	$total=0;
	foreach($members as $member){
		
		$cli=$member['id'];$cliname=$member['name'];
		$savings=mysqli_query($con,"SELECT `saving`,`time`,`type` FROM `savings` WHERE `client`='$cli'");
		foreach($savings as $saving){
			$total=$total+$saving['saving'];
				$type=$saving['type'];
				$get=mysqli_query($con,"SELECT `type` FROM `deposittypes` WHERE `id`='$type'");
				foreach($get as $g){
					$ty=$g['type'];
				}
				echo '<tr><td>'.$ty.'</td><td>'.ucwords($cliname).'</td><td>'.date('d/m/Y',$saving['time']).'</td><td style="text-align:end;">'.$saving['saving'].'/=</td></tr>';
			}
		$risks=mysqli_query($con,"SELECT `amount`,`time` FROM `riskfund` WHERE `client`='$cli'");
		$riskamount=0;
		foreach($risks as $risk){
			$total=$total+$risk['amount'];
			echo '<tr><td>Risk fund</td><td>'.ucwords($cliname).'</td><td>'.date('d/m/Y',$risk['time']).'</td><td style="text-align:right;">'.$risk['amount'].'/=</td></tr>';
		}
		$lrepays=mysqli_query($con,"SELECT `paid`,`time` FROM `loans` WHERE `client`='$cli'");
		foreach($lrepays as $repays){
			$amt=$repays['paid'];
				$total=$total+$amt;
				if($repays['paid']>0){
					echo '<tr><td>Loan payment</td><td>'.ucwords($cliname).'</td><td>'.date('d/m/Y',$repays['time']).'</td><td style="text-align:right;">'.$amt.'/=</td></tr>';
				}
			}	
		}
	$fines=mysqli_query($con,"SELECT `charges`,`amount`,`client`,`date` FROM `fines` WHERE `mgroup`='$fund'");
			if($fines){foreach($fines as $fine){
			$client=$fine['client'];
			$date=$fine['date'];
			$charge=$fine['charges'];
				$total=$total+$fine['amount'];
				$charges=mysqli_query($con,"SELECT `name` FROM `chargetypes` WHERE `id`='$charge'");
				foreach($charges as $chr){
					$chn=$chr['name'];
				}
				$names=mysqli_query($con,"SELECT `name` FROM `members` WHERE `id`='$client'");
				foreach($names as $name){
				echo '<tr><td>'.ucwords($chn).' Payment</td><td>'.ucwords($name['name']).'</td><td>'.date('d/m/Y',$date).'</td><td style="text-align:right;">'
				.$fine['amount'].'/=</td></tr>';}
			
		}
	}
	
	
	echo '<tr style="font-weight:600;"><td colspan="3" style="text-align:center;">Total</td><td style="text-align:right;">'.$total.'/=</td></tr></table></div></div>';
	}
	if(isset($_GET['grpsavings'])){
		$grp=$_GET['grpsavings'];
		$select=mysqli_query($con,"SELECT `name` FROM `groups` WHERE `id`='$grp'");
		foreach($select as $gr){
			$name=$gr['name'];}
		echo '<div class="row no-gutters" style=":center;background:#f0f0f0;line-height:40px;font-weight:600;">'.ucwords($name).' Group Savings</div>
	<div class="row no-gutters"><table class="table-striped" style="width:100%;">
	<tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Type</td><td>Member</td><td>Day</td><td>Amount</td></tr>';
	$individual=mysqli_query($con,"SELECT `id`,`name` FROM `members` WHERE `status`=1 AND `mgroup`='$grp'");
	$t=0;
	foreach($individual as $ind){
		$member=$ind['id'];
		$members=mysqli_query($con,"SELECT `saving`,`time`,`type` FROM `savings` WHERE `client`='$member'");
		foreach($members as $saving){
			$t=$t+$saving['saving'];
				$dt=$saving['type'];
				$invtype=mysqli_query($con,"SELECT `type` FROM `deposittypes` WHERE `id`='$dt'");
				foreach($invtype as $type){
					echo '<tr><td>'.$type['type'].'</td><td>'.ucwords($ind['name']).'</td><td>'.date('d/m/Y',$saving['time']).'</td><td>'.$saving['saving'].'</td></tr>';
				}
				
			}
			
		}
	echo '<tr style="font-weight:600;"><td></td><td style="text-align:center;" colspan="2">Total Savings</td><td>'.$t.'KSH</td></tr>';

	echo '<table></div>';
	}
	#fund out
	if(isset($_GET['fundsout'])){
		$grp=$_GET['fundsout'];
		$grps=mysqli_query($con,"SELECT `name` FROM `groups` WHERE `id`='$grp'");
		foreach($grps as $cgrp){$name=$cgrp['name'];}
		echo '<div style="width:98%;margin:0px auto;"><div class="row no-gutters" style="justify-content:center;background:#f0f0f0;line-height:40px;font-weight:600;">'.ucwords($name).' Funds Out History</div>
		<div class="row no-gutters"><table class="table-striped" style="width:100%;">
		<tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Activity type</td><td>Member/Payee</td><td>Day</td><td>Amount</td></tr>';
		$members=mysqli_query($con,"SELECT `id`,`name` FROM `members` WHERE `status`=1 AND `mgroup`='$grp'");
		$total=0;
		foreach($members as $member){
			$cli=$member['id'];$cliname=$member['name'];
			$lrepays=mysqli_query($con,"SELECT `amount`,`loantype`,`time` FROM `loans` WHERE `client`='$cli'");
			
		foreach($lrepays as $repays){
			$bl=$repays['amount'];
			$total=$total+$bl;
			$lnt=$repays['loantype'];
					$type=mysqli_query($con,"SELECT `name` FROM `loantype` WHERE `id`='$lnt'");
					foreach($type as $name){

				echo '<tr><td>'.$name['name'].' Loan</td><td>'.ucwords($cliname).'</td><td>'.date('d/m/Y',$repays['time']).'</td><td>'.$bl.'</td></tr>';}
			
					}
			
			$expenses=mysqli_query($con,"SELECT * FROM `expenses` WHERE `groupid`='$grp'");
			if(mysqli_num_rows($expenses)>0){
				foreach($expenses as $expense){
					$total=$total+$expense['paid'];
					echo '<tr><td>Group expense('.$expense['expense'].')</td><td>'.$expense['receiver'].'</td><td>'.date('d/m/Y',$expense['time']).'</td><td>'.$expense['paid'].'</td></tr>';
				}
			}
		}
			echo '<tr style="font-weight:600;"><td></td><td style="text-align:center;" colspan="2">Total Funds out</td><td>'.$total.'</td>';
		echo'</table><div></div>';
	}
	if(isset($_GET['managemembers'])){
		$grp=$_GET['managemembers'];
		echo '<div class="col-11 mx-auto" style="width:100%;">
		<div class="row no-gutters" style="justify-content:center;font-weight:600;"><h4>Manage Group Members</h4></div>
		<table class="table-striped" style="width:100%;">
		<tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;padding-left:5px"><td>Name</td><td>ID</td><td>Contact</td><td>State</td></tr>';
		$members=mysqli_query($con,"SELECT * FROM `members` WHERE `mgroup`='$grp'");
		foreach($members as $qry){
			$stat=$qry['status']==1?'<td onclick="deletemember('.$qry['id'].','.$grp.')"><i class="bi bi-trash" style="cursor:pointer;color:red;font-size:20px;"></i></td>':'<td style="background:;color:red;font-weight:600;">Deleted<td>';
			echo '<tr style="line-height:30px;cursor:pointer;"><td onclick="updatemember('.$qry['id'].')">'.ucwords($qry['name']).'</td><td onclick="updatemember('.$qry['id'].')">'.$qry['idno'].'</td><td onclick="updatemember('.$qry['id'].')">0'.$qry['phone'].'</td>'.$stat.'</tr>';
		}
		echo '</table></div>
		
		</div>'; 
	}
	if(isset($_GET['updateuser'])){
		$updateuser=$_GET['updateuser'];
		$select=mysqli_query($con,"SELECT * FROM `members` WHERE `id`='$updateuser'");
		foreach($select as $m){
		echo '<div class="col-8 mx-auto" style="max-width:300px;">
		<form id="upmember" method="post">
		<input type="hidden" name="updatee" value="'.$updateuser.'">
		<div class="row no-gutters" style="justify-content:center;"><h3 style="text-align:center;color:#191970;font-size:23px;margin:0px">Update Member Details</h3></div>
		<p><div class="row no-gutters">Member name</div>
		<div class="row no-gutters"><input style="width:100%;"type="text" name="username" 
		value="'.ucwords($m['name']).'"></div></p>
		<p><div class="row no-gutters">ID Number</div>
		<div class="row no-gutters"><input style="width:100%;max-width:300px;" name="idnum" type="number" 
		value="'.$m['idno'].'"></div></p>
		<p><div class="row no-gutters">Member Number</div>
		<div class="row no-gutters"><input style="width:100%;max-width:300px;" name="sysnumber" min="0" type="number" 
		value="'.$m['membernumber'].'"></div></p>
		<p><div class="row no-gutters">Group Role</div>
		<div class="row no-gutters"><select style="width:100%;max-width:300px;" name="grouppos">';
			$positions=mysqli_query($con,"SELECT * FROM `grouppositions`");
			if(mysqli_num_rows($positions)>0){
				foreach($positions as $position){
					$select=$position['id']==$m['pos']? 'selected':'';
				echo '<option value="'.$position['id'].'" '.$select.'>'.ucwords($position['position']).'</option>';
				}
			}
			else{
				echo '<option>-- No positions-- </option>';
			}
		
		echo '</select></div></p>
		<p><div class="row no-gutters">Contact</div>
		<div class="row no-gutters"><input style="width:100%;max-width:300px;" type="number" name="phone" 
		value="'.$m['phone'].'"></div></p>
		<p><div class="row no-gutters">Place of residence</div>
		<div class="row no-gutters"><input style="width:100%;max-width:300px;" type="text" name="residence" 
		value="'.$m['residence'].'"></div></p>';
		$id=0;
		if(strlen($m['nextkin'])>2){
		foreach(json_decode($m['nextkin'],1) as $kin=>$knv){
			$vls=explode(",",str_replace("[","",str_replace("]","",$knv)));
			echo '<p><div class="row no-gutters">Next of Kin Name</div>
			<div class="row no-gutters"><input style="width:100%;"type="text" name="name'.$id.'" 
			value="'.ucwords($kin).'"></div></p>
			<p><div class="row no-gutters">Kin Contact</div>
			<div class="row no-gutters"><input style="width:100%;"type="number" name="cont'.$id.'" 
			value="'.$vls[0].'" minlength="9" maxlength="12"></div></p>
			<p><div class="row no-gutters">Share Percentage</div>
			<div class="row no-gutters"><input style="width:100%;"type="number" name="perc'.$id.'" 
			min="0" max="100" value="'.$vls[1].'"></div></p>';
			$id++;
		}
	}
		echo '<div id="newh"></div>
		<div class="row no-gutters" style=padding-left:10px;">
		<i class="bi bi-plus-circle-fill" style="font-size:26px;color:green;cursor:pointer;"
		 onclick="addnkin()";></i></div>
		<p><div class="row no-gutters" style="justify-content:end;"><button class="btn btn-success">
		Update</button></div></p></form></div></div>';
		}
	}
	#external loans
	if(isset($_GET['getextdebt'])){
		$grp=$_GET['getextdebt'];
		$grps=mysqli_query($con,"SELECT `name` FROM `groups` WHERE `id`='$grp'");
		foreach($grps as $cgrp){$name=$cgrp['name'];}
		echo '<div style="width:98%;margin:0px auto;"><div class="row no-gutters" style="justify-content:center;background:#f0f0f0;line-height:40px;font-weight:600;">'.ucwords(str_replace('group','',strtolower($name))).' Group External History</div>
		<div class="row no-gutters"><table class="table-striped" style="width:100%;">
		<tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Lender Group</td><td>Period(Mths)</td><td>Day</td><td>Amount</td></tr>';
		$debts=mysqli_query($con,"SELECT `ln`.`name`,`db`.`time`,`db`.`amount`,`db`.`period` FROM `debts` AS `db` INNER JOIN `lenders` AS `ln` ON `ln`.`id`=`db`.`lender` WHERE `db`.`borrower`='$grp'");
		$total=0;
		foreach($debts as $debt){
			$total=$total+$debt['amount'];
				echo '<tr><td>'.ucwords($debt['name']).' </td><td>'.ucwords($debt['period']).'</td><td>'.date('d/m/Y',$debt['time']).'</td><td>'.$debt['amount'].'/=</td></tr>';
					}
			echo '<tr style="font-weight:600;"><td></td><td style="text-align:center;" colspan="2">Total External</td><td>'.$total.'/=</td></tr>';
		echo'</table><div></div>';
		
	}
	#add group expense
	if(isset($_GET['newexpense'])){
		$grp=$_GET['newexpense'];
		echo '<div class="col-8 mx-auto" style="max-width:300px;">
		<div class="row no-gutters" style="justify-content:center;"><h3 style="text-align:center;color:#191970;font-size:23px;margin:0px">Pay Expenses</h3></div>
		<form id="rexpe" method="post">
		<input type="hidden" name="expgroup" value="'.$grp.'">
		<p><div class="row no-gutters">Expense</div>
		<div class="row no-gutters"><input type="text"name="expense" required style="width:100%;"></div></p>
		<p><div class="row no-gutters">Amount spent</div>
		<div class="row no-gutters"><input type="number" name="damount" required style="width:100%;"></div></p>
		<p><div class="row no-gutters">Payment Method</div>
		<div class="row no-gutters"><input type="text" name="payment" required style="width:100%;"></div></p>
		<p><div class="row no-gutters">Paid To:</div>
		<div class="row no-gutters"><input type="text" style="width:100%;max-width:300px;" name="receiver"></div></p>
		<br>
		<div class="row no-gutters" style="justify-content:end;"><button class="btn btn-success">Confirm
		</button></div></form></div></div>';
	}
	#risk fund
	if(isset($_GET['riskfund'])){
		$risk=$_GET["riskfund"];
		echo '<div class="col-8 mx-auto" style="max-width:300px;">
		<div class="row no-gutters" style="justify-content:center;"><h3 style="text-align:center;color:#191970;font-size:23px;margin:0px">Collect Risk Fund</h3></div>
		<form id="riskf" method="post"> 
		<p><div class="row no-gutters">Select Member</div>	
		<div class="row no-gutters"><select style="max-width:300px;" name="member" required>';
		$members=mysqli_query($con,"SELECT `id`,`name` FROM `members` WHERE `status`=1 AND `mgroup`='$risk'");
		foreach($members as $member){
			echo '<option value="'.$member['id'].'">'.ucwords($member['name']).'</option>';
		}
		echo '</select></div></p>
		<p><div class="row no-gutters">Amount</div>
		<div class="row no-gutters"><input type="number" name="riskamt" style="width:100%;"></div></p>
		<div class="row no-gutters" style="justify-content:end;">
		<button class="btn btn-success">Collect</button></div><br>
		</form>	
		</div>';
	}
	#office expense
	if(isset($_GET['offexpense'])){
		$grp=$_GET['offexpense'];
		echo '<div class="col-8 mx-auto" style="max-width:300px;">
		<div class="row no-gutters" style="justify-content:center;"><h3 style="text-align:center;color:#191970;font-size:23px;margin:0px">Record Office Expense</h3></div>
		<form id="offexpe" method="post">
		<input type="hidden" name="offpgroup" value="'.$grp.'">
		<p><div class="row no-gutters">Expense</div>
		<div class="row no-gutters"><input type="text" name="offexpense" required style="width:100%;"></div></p>
		<p><div class="row no-gutters">Amount spent</div>
		<div class="row no-gutters"><input type="number" name="offamount" required style="width:100%;"></div></p>
		
		<div class="row no-gutters" style="justify-content:end;"><button class="btn btn-success">Confirm
		</button></div></form></div></div>';
	}
	#group expenses
	if(isset($_GET['expenses'])){
		$grp=$_GET['expenses'];
		$grps=mysqli_query($con,"SELECT `name` FROM `groups` WHERE `id`='$grp'");
		foreach($grps as $cgrp){$name=$cgrp['name'];}
		echo '<div style="width:98%;margin:0px auto;"><div class="row no-gutters" style="justify-content:center;background:#f0f0f0;line-height:40px;font-weight:600;">'.ucwords($name).' Group Expenses</div>
		<div class="row no-gutters"><table class="table-striped" style="width:100%;">
		<tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Expense</td><td>Paid To</td><td>Payment method</td><td>Day</td><td>Amount Paid</td></tr>';
		$expenses=mysqli_query($con,"SELECT * FROM `expenses` WHERE `groupid`='$grp'");
		$total=0;
		if(mysqli_num_rows($expenses)>0){
		foreach($expenses as $expense){
			$bl=$expense['paid'];
			$total=$total+$bl;

				echo '<tr><td>'.$expense['expense'].'</td><td>'.$expense['receiver'].'</td><td>'.$expense['paymethod'].'</td><td>'.date('d/m/Y',$expense['time']).'</td><td>'.$expense['paid'].'</td></tr>';}
			echo '<tr style="font-weight:600;"><td></td><td style="text-align:center;" colspan="3">Total Expenses</td><td>'.$total.'Ksh</td></tr>';}else{
				echo '<tr style="font-weight:600;"><td style="text-align:center;" colspan="5">No recorded Expenses!</td><td></td></tr>';
			}
		echo'</table><div></div>';
	}
	##golden kidfunds out
	if(isset($_GET['goldenkid'])){
		$grp=$_GET['goldenkid'];
		$grps=mysqli_query($con,"SELECT `name` FROM `groups` WHERE `id`='$grp'");
		foreach($grps as $cgrp){$name=$cgrp['name'];}
		echo '<div style="width:98%;margin:0px auto;"><div class="row no-gutters" style="justify-content:center;background:#f0f0f0;line-height:40px;font-weight:600;">'.ucwords($name).' Group Golden Kid Out funds</div>
		<div class="row no-gutters"><table class="table-striped" style="width:100%;">
		<tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Group Member</td><td>Type</td><td>Day</td><td>Amount</td></tr>';
		$loantype=mysqli_query($con,"SELECT `id` FROM `loantype` WHERE `name` LIKE '%golden%'");
		foreach($loantype as $lp){$lnty=$lp['id'];}
		$loans=mysqli_query($con,"SELECT * FROM `loans` WHERE `loantype`='$lnty'");
		$total=0;
		if(mysqli_num_rows($loans)>0){
		foreach($loans as $gkid){
			$bl=$gkid['amount'];
			$total=$total+$bl;
			$uid=$gkid['client'];
				$users=mysqli_query($con,"SELECT `name` FROM `members` WHERE `id`='$uid'");
		
				foreach($users as $uname){
				echo '<tr><td>'.ucwords($uname['name']).'</td><td>Golden Loan</td><td>'.date('d/m/Y',$gkid['time']).'</td><td>'.$gkid['amount'].'</td></tr>';}
				}
					
			echo '<tr style="font-weight:600;"><td style="text-align:center;" colspan="3">Total Funds Out</td><td>'.$total.'Ksh</td></tr>';}else{
				echo '<tr style="font-weight:600;"><td style="text-align:center;" colspan="4">No Funds out in Golden kid!</td><td></td></tr>';
			}
		echo'</table><div></div>';

	}
	#chrismass funds out
	if(isset($_GET['xmassout'])){
		$grp=$_GET['xmassout'];
		$grps=mysqli_query($con,"SELECT `name` FROM `groups` WHERE `id`='$grp'");
		foreach($grps as $cgrp){$name=$cgrp['name'];}
		echo '<div style="width:98%;margin:0px auto;"><div class="row no-gutters" style="justify-content:center;background:#f0f0f0;line-height:40px;font-weight:600;">'.ucwords($name).' Group Christmas Funds Out</div>
		<div class="row no-gutters"><table class="table-striped" style="width:100%;">
		<tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Group Member</td><td>Type</td><td>Day</td><td>Amount</td></tr>';
		$loantype=mysqli_query($con,"SELECT `id` FROM `loantype` WHERE `name` LIKE '%hristmas%'");
		foreach($loantype as $lp){$lnty=$lp['id'];}
		$loans=mysqli_query($con,"SELECT * FROM `loans` WHERE `loantype`='$lnty'");
		$total=0;
		if(mysqli_num_rows($loans)>0){
		foreach($loans as $gkid){
			$bl=$gkid['amount'];
			$total=$total+$bl;
			$uid=$gkid['client'];
				$users=mysqli_query($con,"SELECT `name` FROM `members` WHERE `id`='$uid'");
		
				foreach($users as $uname){
				echo '<tr><td>'.ucwords($uname['name']).'</td><td>Chisristmas Loan</td><td>'.date('d/m/Y',$gkid['time']).'</td><td>'.$gkid['amount'].'</td></tr>';}
				}
					
			echo '<tr style="font-weight:600;"><td style="text-align:center;" colspan="3">Total Funds out</td><td>'.$total.'Ksh</td></tr>';}else{
				echo '<tr style="font-weight:600;"><td style="text-align:center;" colspan="4">No Funds out in Christmas Funds!</td><td></td></tr>';
			}
		echo'</table><div></div>';
	}
	#account withdraw
	if(isset($_GET['withdrawal'])){
		$grp=$_GET['withdrawal'];
		echo '<div class="col-8 mx-auto" style="max-width:300px;">
		<div class="row no-gutters" style="justify-content:center;"><h4 style="text-align:center;color:#191970;font-size:23px;margin:0px">Member Withdraw</h4></div>
		<form id="withdr" method="post">
		<input type="hidden" name="groupaccount" value="'.$grp.'">
		<p><div class="row no-gutters">Withdraw</div>
		<div class="row no-gutters"><input type="number" name="wamaount" required style="width:100%;"></div></p>
		<p><div class="row no-gutters">Member</div>
		<div class="row no-gutters"><select name="memberid" style="max-width:300px;width:100%;" onchange="changerecepient(this.value,'.$grp.')">';
		$fuser=[];
		$users=mysqli_query($con,"SELECT `name`,`id` FROM `members` WHERE `mgroup`='$grp' AND `status`=1 ORDER BY `id` ASC");
		if(mysqli_num_rows($users)>0){
			foreach($users as $user){
				array_push($fuser,$user['id']);
				echo '<option value="'.$user['id'].'">'.ucwords($user['name']).'</option>';
			}
		}
		
		echo'</select></div></p>
		<p><div class="row no-gutters">Investiment type</div>
		<div class="row no-gutters"><select name="investment" style="max-width:300px;width:100%;">';
		$cu=$fuser[0];
		$savins=mysqli_query($con,"SELECT `saving`,`type`,`id` FROM `savings` WHERE `client`='$cu' AND `saving`>0");
		if(mysqli_num_rows($savins)>0){
			foreach($savins as $sav){
				$type=$sav['type'];
				$saname=mysqli_query($con,"SELECT `type` FROM `deposittypes` WHERE `id`='$type'");
				foreach($saname as $snm){
				echo '<option value="'.$sav['id'].'">'.$snm['type'].' '.$sav['saving'].'</option>';
				}
			}
		}else{
			echo '<option>No Savings<option>';
		}
		echo '</select></div></p><p>
		<div class="row no-gutters" style="justify-content:end;"><button class="btn btn-success">Withdraw
		</button></div></p><br></form></div></div>';
	}
	if(isset($_GET['changerec'])){
		$grp=$_GET['group'];
		$member=$_GET['changerec'];
		echo '<div class="col-8 mx-auto" style="max-width:300px;">
		<div class="row no-gutters" style="justify-content:center;"><h4 style="text-align:center;color:#191970;font-size:23px;margin:0px">Member Withdraw</h4></div>
		<form id="withdr" method="post">
		<input type="hidden" name="groupaccount" value="'.$grp.'">
		<p><div class="row no-gutters">Withdraw</div>
		<div class="row no-gutters"><input type="number" name="wamaount" required style="width:100%;"></div></p>
		<p><div class="row no-gutters">Member</div>
		<div class="row no-gutters"><select name="memberid" style="max-width:300px;width:100%;" onchange="changerecepient(this.value,'.$grp.')">';
		
		$users=mysqli_query($con,"SELECT `name`,`id` FROM `members` WHERE `mgroup`='$grp' AND `status`=1 ORDER BY `id` ASC");
		if(mysqli_num_rows($users)>0){
			foreach($users as $user){
				$sel=($user['id']===$member)?"selected":'';
				echo '<option value="'.$user['id'].'" '.$sel.' >'.ucwords($user['name']).'</option>';
			}
		}
		
		echo'</select></div></p>
		<p><div class="row no-gutters">Investiment type</div>
		<div class="row no-gutters"><select name="investment" style="max-width:300px;width:100%;">';
		$cu=$fuser[0];
		$savins=mysqli_query($con,"SELECT `saving`,`type`,`id` FROM `savings` WHERE `client`='$member' AND `saving`>0");
		if(mysqli_num_rows($savins)>0){
			foreach($savins as $sav){
				$type=$sav['type'];
				$saname=mysqli_query($con,"SELECT `type` FROM `deposittypes` WHERE `id`='$type'");
				foreach($saname as $snm){
				echo '<option value="'.$sav['id'].'">'.$snm['type'].' '.$sav['saving'].'</option>';
				}
			}
		}else{
			echo '<option>No Savings<option>';
		}
		echo '</select></div></p><p>
		<div class="row no-gutters" style="justify-content:end;"><button class="btn btn-success">Withdraw
		</button></div></p><br></form></div></div>';
	}

	if(isset($_GET['authenticate'])){
		$grp=$_GET['authenticate'];
		echo '<!--<div class="col-6 mx-auto" style="max-width:300px;">
		<div class="row no-gutters" style="justify-content:center;"><h3 style="text-align:center;color:#191970;font-size:23px;margin:0px">Edit Deposits</h3></div>
		<form id="auth">
		<input type="hidden" name="gedit" value="'.$grp.'">
		<p><div class="row no-gutters">User</div>
		<div class="row no-gutters"><input type="text" name="aduser" style="width:100%;max-width:300px;" required></div></p>
		<p><div class="row no-gutters">Password</div>
		<div class="row no-gutters"><input type="password" style="width:100%;max-width:300px;" name="pass" required></div></p>
		<div class="row no-gutters" style="justify-content:end;">
		<button class="btn btn-success">Confirm</button></div><br>
		</form>
		</div>-->';
		$select=mysqli_query($con,"SELECT `name` FROM `groups` WHERE `id`='$grp'");
		foreach($select as $gr){
			$name=$gr['name'];}
		echo '<div class="row no-gutters" style="justify-content:center;background:#f0f0f0;line-height:40px;font-weight:600;">'.ucwords($name).' Group Savings</div>
	<div class="row no-gutters"><table class="table-striped" style="width:100%;">
	<tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Type</td><td>Deposited By</td><td>Amount</td><td>Delete</td></tr>';
	$individual=mysqli_query($con,"SELECT * FROM `members` WHERE `status`=1 AND `mgroup`='$grp'");
	$t=0;
	foreach($individual as $ind){
		$member=$ind['id'];
		$members=mysqli_query($con,"SELECT `id`,`saving`,`time`,`type` FROM `savings` WHERE `client`='$member'");
		foreach($members as $saving){
			$t=$t+$saving['saving'];
				$dt=$saving['type'];
				$invtype=mysqli_query($con,"SELECT `type` FROM `deposittypes` WHERE `id`='$dt'");
				foreach($invtype as $type){
					echo '<tr style="cursor:pointer;"><td onclick="editdeta('.$saving['id'].')">'.$type['type'].'</td><td onclick="editdeta('.$saving['id'].')">'.ucwords($ind['name']).'</td><td onclick="editdeta('.$saving['id'].')">'.$saving['saving'].'</td><td onclick="deldepo('.$saving['id'].')"><i class="bi bi-trash" style="color:red;font-size:20px;"></i></td></tr>';
				}
				
			}
			
		}

	echo '<table></div>';}
	##edit post
	if(isset($_GET['eddeposit'])){
		$id=$_GET['eddeposit'];
		$select=mysqli_query($con,"SELECT * FROM `savings` WHERE `id`='$id'");
		foreach($select as $sel){
		echo '<div class="col-6 mx-auto" style="max-width:300px;">
		<div class="row no-gutters" style="justify-content:center;"><h3 style="text-align:center;color:#191970;font-size:23px;margin:0px">Edit Deposit</h3></div>
		<form id="eddeposit">
		<input type="hidden" name="gedit" value="'.$id.'">
		<p><div class="row no-gutters">Amount</div>
		<div class="row no-gutters"><input type="text" name="eamount" style="width:100%;max-width:300px;" required value="'.$sel['saving'].'"></div></p>
		<p><div class="row no-gutters">Deposit type</div>
		<div class="row no-gutters"><select style="max-width:300px;width:100%;" name="depotype">';
		$depos=mysqli_query($con,"SELECT `id`,`type` FROM `deposittypes`");
			foreach($depos as $depo){
				$selected=$depo['type']==$sel['type']?"selected":'';
				echo '<option value="'.$depo['id'].'">'.$depo['type'].'</option>';
			}
		echo '</select></div></p>';
		}
		echo '<div class="row no-gutters" style="justify-content:end;">
		<button class="btn btn-success">Confirm</button></div><br>
		</form></div>';}
		#activity
		if(isset($_GET['summary'])){
			$grp=$_GET['summary'];
			$first=strtotime("today");
			$last=strtolower("last day of this month");
			$month=strtotime(date('M-Y',time()));
			echo '<div class="card mx-auto" style="min-width:290px;width:95%;min-height:400px;">
			<div class="card-header" style="text-align:center;"><h4>Group Monthly Performance</h4></div>
			<div class="card-body" style="padding:10px;overflow-x:auto;">
			<div class="row no-gutters" style="padding-left:20px;">
			<select style="width:150px;" onchange="membersummary('.$grp.',this.value)">';
			echo '<option>--Group--</option>';
			$cashin=0;
			$allmembers=mysqli_query($con,"SELECT `name`,`id` FROM `members` WHERE `mgroup`='$grp' AND `status`=1");
				foreach($allmembers as $member){
					echo '<option value="'.$member['id'].'">'.ucwords($member['name']).'</option>';
				}
			echo '</option></select></div>
			<hr/>
			<div class="table-responsive" style="min-width:550px;">	
			<table class="table-striped" style="width:100%;font-size:12px;">
			<tr><td><h5>Cash In</h5></td><td><h5>Cash Out</h5></td></tr><tr><td style="width:50%;padding:0 5px;vertical-align:top;">
			<div class="row no-gutters" style="justify-content:center;font-weight:600;">Fines and Charges</div>
			<table style="width:100%;max-width:900px;min-width:200px;"><tr><th>Name</th><th>type</th><th>Amount</th></tr>';
			
				$fines=mysqli_query($con,"SELECT `fin`.`amount`,`chty`.`name`,`memb`.`name` AS `mname` FROM `fines` AS `fin` INNER JOIN `members` AS `memb` ON `fin`.`client`=`memb`.`id` INNER JOIN `chargetypes` AS `chty` ON `fin`.`charges`=`chty`.`id` WHERE `memb`.`mgroup`='$grp' AND `fin`.`date`>='$first'");
				$totalfines=0;
				foreach($fines as $fine){
					echo '<tr><td>'.$fine['mname'].'</td><td>'.$fine['name'].'</td><td style="width:80px;">'.$fine['amount'].'</td></tr>';
						$totalfines=$totalfines+$fine['amount'];
					
					}
				$cashin=$cashin+$totalfines;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$totalfines.'/= </td></tr></table><br>
			<div class="row no-gutters" style="justify-content:center;font-weight:600;">Loan Repayment</div>
			<table style="width:100%;max-width:900px;min-width:200px;"><tr><th>Name</th><th>Loan</th><th>Amount</th></tr>';
			$repayment=mysqli_query($con,"SELECT `membernumber`,`paid`,`appfee`,`phistory`,`loan`,`overdue` FROM `members` AS `mb` INNER JOIN `loans` AS `ln` ON `mb`.`id`=`ln`.`client` WHERE `mgroup`='$grp' AND `paid`>0")
			;$loans=0;$app=0;
			foreach($repayment as $repay){
				$application=[];
				$paid=$repay['paid']-$repay['overdue'];
				$tv=0;
				foreach(json_decode($repay['phistory'],1) as $k=>$v){
					$tv=$tv+$v;
					array_push($application,array($repay['membernumber']=>$repay['appfee']));
				if($k>=$first){
				$app=$app+$repay['appfee'];$loans=$loans+$repay['paid'];
					echo '<tr><td>'.$repay['membernumber'].'</td><td>'.$repay['loan'].'</td><td style="width:80px;">'.($loans-$app).'</td></tr>';
					}
				}
				$overdue=($tv-($repay['paid']-$repay['appfee'])-$repay['overdue']);
				
			}
			$loans=$loans-$app;
			$cashin=$cashin+$loans;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$loans.'/=</td></tr></table><br>
			
			<div class="row no-gutters" style="font-weight:600;justify-content:center;font-weight:600;">Loan application fee</div>
			<table style="width:100%;max-width:900px;min-width:200px">';
			$repayment=mysqli_query($con,"SELECT `ap`.`history`,`name`,`ap`.`loan`FROM `groups` AS `gr` INNER JOIN `members` AS `m` ON `m`.`mgroup`=`gr`.`id` INNER JOIN `applicationfee` AS `ap` ON `ap`.`client`=`m`.`id` WHERE `gr`.`id`='2'");
			$fee=0;
			foreach($repayment as $repay){
				foreach(json_decode($repay['history'],1) as $k=>$v){
				if($k>=$first){
				$fee=$fee+$v;
					echo '<tr><td>'.$repay['name'].'</td><td>'.$repay['loan'].'</td><td style="width:80px;">'.($v).'</td></tr>';
					}
				}
			}
			$cashin= $cashin+$fee;
			echo '<tr style="font-weight:600;background:white;border-top:2px solid;border-bottom:1px solid;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$fee.'/=</td></tr></table><div class="row no-gutters" style="justify-content:center;font-weight:600;">Group Savings</div><table style="width:100%;min-width:200px;max-width:900px;"><tr><th>Name</th><th>Type</th><th>Amount</th></tr>';
			$savs=mysqli_query($con,"SELECT `saving`,`mb`.`name`,`dep`.`type` FROM `savings` AS `sav` INNER JOIN `members` AS `mb` ON `mb`.`id`=`sav`.`client` INNER JOIN `deposittypes` AS `dep` ON `sav`.`type`=`dep`.`id` WHERE `mgroup`='$grp' AND `sav`.`time`>='$first'");
			$savn=0;
			if(mysqli_num_rows($savs)>0){
			foreach($savs as $sav){
				$savn=$savn+$sav['saving'];
				echo '<tr><td>'.$sav['name'].'</td><td>'.$sav['type'].'</td><td style="width:80px;">'.$sav['saving'].'</td></tr>';}
			}
			$cashin= $cashin+$savn;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;">'.$savn.'/=</td></tr></table>';
			echo '<div class="row no-gutters" style="justify-content:center;font-weight:600;">External Debts</div><table style="width:100%;min-width:200px;max-width:900px;"><tr><th>Lenders</th><th>Amount</th></tr>';
			$debts=mysqli_query($con,"SELECT `dbt`.`amount`,`dbt`.`time`,`lg`.`name` FROM `debts` AS `dbt` INNER JOIN `groups` AS `lg` ON `dbt`.`lender`=`lg`.`id` WHERE `lender`='$grp' AND `paid`>0");
			$amount=0;
			foreach($debts as $debt){
				foreach(json_decode($debt,1) as $k=>$v){
					if($k>=$first){
					echo '<tr><td>'.$debt['name'].'</td><td>'.$debt['amount'].'</td></tr>';
					}
				$amount=$amount+$debt['amount'];
				}
			}
			$cashin=$cashin+$amount;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td style="text-align:center;">Subtotal</td><td style="text-align:end;" >'.$amount.'/=</td></tr></table>
			</div>
			</td><td style="display:flex;flex-direction:column;padding:0 5px">
			<div class="row no-gutters" style="justify-content:center;font-weight:600">Loans Borrowed</div><table style="min-width:200px;max-width:900px"><tr><th>Name</th><th>loan</th><th>Amount</th><tr>';
			$cashout=0;
			$repayment=mysqli_query($con,"SELECT `ln`.`phistory`,`ln`.`amount`,`lty`.`name`,`mb`.`name` AS `mname` FROM `members` AS `mb` INNER JOIN `loans` AS `ln` ON `ln`.`client`=`mb`.`id` INNER JOIN `loantype` AS `lty` ON `ln`.`loantype`=`lty`.`id` WHERE `mb`.`mgroup`='$grp' AND `ln`.`paid`>0");
			$loans=0;
				foreach($repayment as $repay){
					foreach(json_decode($repay['phistory'],1) as $k=>$v){
					if($k>=$first){
						echo '<tr><td>'.$repay['mname'].'</td><td>'.$repay['name'].'</td><td style="width:80px;">'.$repay['amount'].'</td></tr>';
					$loans=$loans+$v;}
					}

				}
			$cashout=$cashout+$loans;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;">'.$loans.'/=</td></table>
			<div class="row no-gutters" style="justify-content:center;font-weight:600;">External Debt paid</div><table style="min-width:200px;max-width:900px;"><tr><th>Group</th><th>date</th><th>Amount</th></tr>';
				$debtspaid=mysqli_query($con,"SELECT `gr`.`name`,`db`.* FROM `debts` AS `db` INNER JOIN `groups` AS `gr` ON `db`.`borrower`=`gr`.`id` WHERE `lender`='$grp' AND `paid`>0");
				$paid=0;
				foreach($debtspaid as $debt){
					foreach(json_decode($debt['payments'],1) as $key=>$value){
						if($key>=$first){
							echo '<tr><td>'.$debt['name'].'</td><td>'.date("d/m/Y",$key).'</td><td>'.$value.'</td><tr>';
							$paid=$paid+$value;
						}
					}
				}
				$cashout=$cashout+$paid;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$paid.'/=</td></table>
			<div class="row no-gutters" style="justify-content:center;font-weight:600;margin-top:5px;">Group Expenditure</div><table style="min-width:200px;max-width:900px;"><tr><th>Expense</th><th>Paid To</th><th>Amount</th></tr>';
				$expenses=mysqli_query($con,"SELECT `paid`,`expense`,`receiver` FROM `expenses` WHERE `groupid`='$grp' AND `time`>$first");
				$exp=0;
				foreach($expenses as $expense){
					echo '<tr><td>'.$expense['expense'].'</td><td>'.$expense['receiver'].'</td><td style="width:80px;">'.$expense['paid'].'</td></tr>';
					$exp=$exp+$expense['paid'];
				}
				$cashout=$cashout+$exp;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$exp.'/=</td></tr></table><table style="min-width:200px;max-width:900px;"><tr><th>Expense</th><th>Date</th><th>Amount</th></tr><div class="row no-gutters" style="font-weight:600;justify-content:center;">Office Expense</div>';
				$office=mysqli_query($con,"SELECT `payment`,`time`,`amount` FROM `officeexpense` WHERE `grp`='$grp' AND `time`>$first");
				$ofexp=0;
				foreach($office as $ofc){
					echo '<tr><td>'.$ofc['payment'].'</td><td>'.date("d/m/Y",$ofc['time']).'</td><td style="width:80px;">'.$ofc['amount'].'</td><tr>';
					$ofexp=$ofexp+$ofc['amount'];
				}
				$cashout=$cashout+$ofexp;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$ofexp.'/=</td></tr></table>
			<div class="row no-gutters" style="justify-content:center;font-weight:600;">Member Withdraws</div><table class="table-stripped"><tr><th>Name</th><th>Date</th><th style="width:80px;">Amount</th></tr>';
			$withdraws=mysqli_query($con,"SELECT `as`.`amount`,`mb`.`name`,`as`.`time` FROM `account_withdraws` AS `as` INNER JOIN `members` AS `mb` ON `mb`.`id`=`as`.`client` WHERE `mb`.`mgroup`='$grp' AND `as`.`time`>='$first'");
			$amt=0;
			foreach($withdraws as $withd){
				$amt=$amt+$withd['amount'];
				echo '<tr><td>'.$withd['name'].'</td><td>'.date('d/m/Y',$withd['time']).'</td><td style="width:80px;">'.$withd['amount'].'/=</td></tr>';
			}
			$cashout=$cashout+$amt;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$amt.'/=</td></tr></table></td></tr><tr style="line-height:35px;background:white;font-size:16px;font-weight:600;"><td><div class="row no-gutters"><div class="col-8" style="text-align:right;">Total</div><div class="col-4" style="text-align:right;">'.$cashin.'/=</div></td><td><div class="row no-gutters"><div class="col-8" style="text-align:right;">Total</div><div class="col-4" style="text-align:right;">'.$cashout.'/=</div</td></tr></table></div></div></div>';
		}
		#member activity
		if(isset($_GET['mactivity'])){
			$grp=$_GET['mgroup'];$cme=$_GET['mactivity'];
			$name=mysqli_query($con,"SELECT `name`,`id` FROM `members` WHERE `mgroup`='$grp'");
			foreach($name as $member){
				if($cme==$member['id']){
					$cname=ucwords($member['name']);
				}
			}
			echo '<div class="card mx-auto" style="min-width:290px;width:95%;min-height:400px;">
			<div class="card-header" style="text-align:center;"><h4>'.$cname.' Activities Sumarry</h4></div>
			<div class="card-body" style="padding:20px;">
			<div class="row no-gutters" style="padding-left:20px;">
			<select style="width:150px;" onchange="membersummary('.$grp.',this.value)">';
			$allmembers=mysqli_query($con,"SELECT `name`,`id` FROM `members` WHERE `mgroup`='$grp' AND `status`=1");
				foreach($allmembers as $member){
					$sl=$member['id']==$cme?"selected":'';
					echo '<option value="'.$member['id'].'" '.$sl.'>'.ucwords($member['name']).'</option>';
				}
			echo '</option></select></div>
			<hr/>

			<div class="row">
			<div class="table-responsive" style="min-width:550px;">	
			<table class="table-striped" style="width:100%;font-size:12px;">
			<tr><td><h5>Cash In</h5></td><td><h5>Cash Out</h5></td></tr><tr><td style="width:50%;padding:0 5px;vertical-align:top;">
			<div class="row no-gutters" style="justify-content:center;font-weight:600;">Fines and Charges</div>
			<table style="width:100%;max-width:900px;min-width:200px;"><tr><th>date</th><th>type</th><th style="text-align:right;">Amount</th></tr>';
				$first= strtotime('first day ot this month');
				$cashin=0;
				$month=strtotime(date('M-Y',time()));
				$fines=mysqli_query($con,"SELECT `fn`.`amount`,`ch`.`name`,`fn`.`date` FROM `fines` AS `fn` INNER JOIN `chargetypes` AS `ch` ON `ch`.`id`=`fn`.`charges` WHERE `fn`.`client`='$cme' AND `fn`.`date`>'$first'");
				$totalfines=0;
				foreach($fines as $fine){
					$totalfines=$totalfines+$fine['amount'];
					echo '<tr><td>'.date('d/m/Y',$fine['date']).'</td><td>'.$fine['name'].'</td><td style="text-align:right;">'.$fine['amount'].'/=</td></tr>';
				}

				$cashin=+$totalfines;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$totalfines.'/= </td></tr></table><br>
			<div class="row no-gutters" style="justify-content:center;font-weight:600;">Loan Repayment</div>
			<table style="width:100%;max-width:900px;min-width:200px;"><tr><th>date</th><th>Loan</th><th style="text-align:right";>Amount</th></tr>';
			$repayment=mysqli_query($con,"SELECT `ln`.`paid`,`ln`.`appfee`,`ln`.`phistory`,`ln`.`loan`,`lt`.`name` FROM `loans` AS `ln` INNER JOIN `loantype` AS `lt` ON `ln`.`loantype`=`lt`.`id` WHERE `ln`.`client`='$cme' AND `paid`>0");$loans=0;
			foreach($repayment as $repay){
				$tloan=0;
				foreach(json_decode($repay['phistory'],1) as $key=>$value){
					$tloan=$tloan+$value;
					if($key>$first){
						echo '<tr><td>'.date('d/m/Y',$key).'</td><td>'.$repay['name'].'</td><td style="text-align:right;">'.$value.'</td></tr>';
					}
				}
				$bal=$tloan-$repay['appfee'];
				$loans=$loans+$bal;
			}
			$cashin=$cashin+$loans;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$loans.'/=</td></tr></table><br>
			
			<div class="row no-gutters" style="font-weight:600;justify-content:center;font-weight:600;">Loan application fee</div><table style="width:100%;min-width:200px;max-width:900px;"><tr><th>Date</th><th style="text-align:right;">Amount</th></tr>';
			$allapps=mysqli_query($con,"SELECT *  FROM `applicationfee` WHERE `client`='$cme'");
			$appfee=0;
			foreach($allapps as $appf){
				foreach(json_decode($appf['history'],1) AS $key=>$value){
					if($key>$first){
					$appfee=+$value;
				echo '<tr><td>'.date('d/m/Y',$key).'</td><td style="text-align:right;">'.$value.'</td><tr>';
					}
				}
				
			}
			$cashin= $cashin+$appfee;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td style="text-align:center;">Subtotal</td><td style="text-align:end;">'.$appfee.'/=</td></tr></table>
			<div class="row no-gutters" style="justify-content:center;font-weight:600;">Individual Savings</div><table style="width:100%;min-width:200px;max-width:900px;"><tr><th>Date</th><th>Type</th><th style="text-align:right;">Amount</th></tr>';
			$allsavings=mysqli_query($con,"SELECT `sav`.`saving`,`sav`.`time`,`dep`.`type` FROM `savings` AS `sav` INNER JOIN `deposittypes` AS `dep` ON `sav`.`type`=`dep`.`id` WHERE `sav`.`client`='$cme' AND `sav`.`month`='$month' AND `sav`.`saving`>0");
			$savn=0;
			foreach($allsavings as $savs){
				$savn=+$savs['saving'];
				echo '<tr><td>'.date('d/m/Y',$savs['time']).'</td><td>'.$savs['type'].'</td><td style="text-align:right;">'.$savs['saving'].'</td></tr>';
			}
			$cashin= $cashin+$savn;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;">'.$savn.'/=</td></tr></table>
			</td><td style="display:flex;flex-direction:column;padding:0 5px">
			<div class="row no-gutters" style="justify-content:center;font-weight:600">Loans Borrowed</div><table style="min-width:200px;max-width:900px"><tr><th>Name</th><th>loan</th><th style="text-align:right;">Amount</th><tr>';
			$cashout=0;
			$repayment=mysqli_query($con,"SELECT `ln`.`phistory`,`ln`.`amount`,`lty`.`name`,`mb`.`membernumber` FROM `members` AS `mb` INNER JOIN `loans` AS `ln` ON `ln`.`client`=`mb`.`id` INNER JOIN `loantype` AS `lty` ON `ln`.`loantype`=`lty`.`id` WHERE `ln`.`client`='$cme' AND `ln`.`paid`>0");
			$loans=0;
			
			$cashout=$cashout+$loans;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;">'.$loans.'/=</td></table><table style="min-width:200px;max-width:900px;"><tr><th>Expense</th><th>Date</th><th>Amount</th></tr><div class="row no-gutters" style="font-weight:600;justify-content:center;">Office Expense</div>';
				$office=mysqli_query($con,"SELECT `payment`,`time`,`amount` FROM `officeexpense` WHERE `grp`='$grp' AND `time`>$first");
				$ofexp=0;
				
				$cashout=$cashout+$ofexp;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td colspan="2" style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$ofexp.'/=</td></tr></table>
			<div class="row no-gutters" style="justify-content:center;font-weight:600;">Member Withdraws</div><table class="table-stripped"><tr><th>Date</th><th>Amount</th></tr>';
			$withdraws=mysqli_query($con,"SELECT `amount`,`time` FROM `account_withdraws` WHERE  `time`>='$first' AND `client`='$cme'");
			$amt=0;
			foreach($withdraws as $withd){
				echo '<tr><td>'.date('d/m/Y',$withd['time']).'</td><td style="width:80px;">'.$withd['amount'].'/=</td></tr>';
			}
			$cashout=$cashout+$amt;
			echo '<tr style="border-top:2px solid;border-bottom:1px solid;font-weight:600;background:white;"><td style="text-align:center;">Subtotal</td><td style="text-align:end;margin-left:10px;">'.$amt.'/=</td></tr></table></td></tr><tr style="line-height:35px;background:white;font-size:16px;font-weight:600;"><td><div class="row no-gutters"><div class="col-8" style="text-align:right;">Total</div><div class="col-4" style="text-align:right;">'.$cashin.'/=</div></td><td><div class="row no-gutters"><div class="col-8" style="text-align:right;">Total</div><div class="col-4" style="text-align:right;">'.$cashout.'/=</div</td></tr></table></div>

			</div></div>';
		}
	if(isset($_GET['nextgroup'])){
		$cpage=$_GET['nextgroup'];$perpage=$_GET['limit'];$npage=$_GET['count'];$start=($cpage)*$perpage;
		$final=($npage+1)*$perpage;$trs=""; $no=0;$start=($cpage)*$perpage;
	
		$sql = mysqli_query($con,"SELECT *FROM `groups` ORDER BY `gid` ASC LIMIT $start,$perpage");
		foreach($sql as $row){
			$rid=$row['id']; $name=prepare(ucwords($row['name'])); $gid=$row['gid']; $no++;
			$chk = mysqli_query($con,"SELECT *FROM `members` WHERE `status`=1 AND `mgroup`='$rid'"); $sum=mysqli_num_rows($chk);
			if($row['status']==1){
			echo "<tr style='cursor:pointer;height:30px;'><td onclick='opengroup($rid)'>$no</td><td onclick='opengroup($rid)'>$name</td><td onclick='opengroup($rid)'>$gid</td><td onclick='opengroup($rid)' >$sum</td><td style='cursor:pointer;color:green;' onclick='editgroup($rid)'>Active</td></tr>";}
			else if($row['status']==4){
				echo "<tr style='cursor:not-allowed;height:30px;'><td>$no</td><td>$name</td><td>$gid</td><td>$sum</td><td style='color:red;'>Deleted</td></tr>";
			}
			else if($row['status']==3){
				echo "<tr style='cursor:not-allowed;height:30px;'><td>$no</td><td>$name</td><td>$gid</td><td>$sum</td><td style='color:f8c753;'>Suspended</td></tr>";
			}
		}echo $cpage,$perpage,$npage;
	}
	if(isset($_GET['debtgroup'])){
		$bgrp=$_GET['debtgroup'];
		echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
		<div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h4>Group Debt Payment</h4></div>
		<form id="paydebt" method="post">
		<input type="hidden" name="currentgroup" value="'.$bgrp.'">
		<p>Lender group<br><select name="dbtid" onchange="changedbtid(this.value,'.$bgrp.')">';
		$debts=mysqli_query($con,"SELECT `dbt`.`id`,`dbt`.`amount`,`ln`.`name` FROM `debts` AS `dbt` INNER JOIN `lenders` AS `ln` ON `dbt`.`lender`=`ln`.`id` WHERE `dbt`.`borrower`='$bgrp' AND `paid`<`amount`");
		if(mysqli_num_rows($debts)<1){
			echo '<option>-- No unpaid debt --</option>';
		}
		else{
			$ar=[];
			foreach($debts as $debt){
				array_push($ar,$debt['amount']);
				echo '<option value='.$debt['id'].' >'.ucwords($debt['name']).'</option>';
			}
		}
		
		echo '</select></p><p>Amount paid<br><input type="number" name="dbtpay" style="width:100%;max-width:300px;" min="0" max="'.$ar[0].'" value="'.$ar[0].'"></p>
		<p><div class="row no-gutters" style="justify-content:end;"><button class="btnn">Pay Debt</div></div></p><br>
		</form>
		</div>';
	}

	if(isset($_GET['changedbt'])){
		$bgrp=$_GET['dbtgroup'];
		$id=$_GET['changedbt'];
		echo '<div class="col-8 mx-auto" style="max-width:300px;width:100%;">
		<div class="row no-gutters" style="justify-content:center;font-size:23px;color:;"><h4>Group Debt Payment</h4></div>
		<form id="paydebt" method="post">
		<input type="hidden" name="currentgroup" value="'.$bgrp.'">
		<p>Lender group<br><select name="dbtid" onchange="changedbtid(this.value,'.$bgrp.')">';
		$debts=mysqli_query($con,"SELECT `dbt`.`id`,`dbt`.`amount`,`ln`.`name` FROM `debts` AS `dbt` INNER JOIN `lender` AS `ln` ON `dbt`.`lender`=`ln`.`id` WHERE `dbt`.`borrower`='$bgrp' AND `paid`<`amount`");
		if(mysqli_num_rows($debts)<1){
			echo '<option>-- No unpaid debt --</option>';
		}
		else{
			foreach($debts as $debt){
				$selected=$debt['id']==$id?"selected":'';
				$max=$debt['id']==$id?$debt['amount']:'';
				echo '<option value='.$debt['id'].' '.$selected.'>'.ucwords($debt['name']).'</option>';
			}
		}
		echo '</select></p><p>Amount paid<br><input type="number" name="dbtpay" style="width:100%;max-width:300px;" min="0" max="'.$max.'" value="'.$max.'"></p>
		<p><div class="row no-gutters" style="justify-content:end;"><button class="btnn">Pay debt</div></div></p><br>
		</form>
		</div>';
	}
	if(isset($_GET['newactivity'])){
		$memb=clean($_GET['newactivity']);
		echo '<div style="text-align:center;"><h3 style="text-align:center;color:#191970;font-size:23px;margin:0px">Member Activity</h3></div>
		<div class="col-8 mx-auto" style="max-width:300px;">
		<form method="post" id="compactivity">
		<input type="hidden" name="acmemb" value="'.$memb.'">
		<div id="membnewact">
		<p>
		<div class="row no-gutters">Saving Type</div>
		<div class="row no-gutters"><select style="max-width:300px;width:100%;" name="depoid">';
		$deptyps= mysqli_query($con,"SELECT `id`,`type` FROM `deposittypes`");
		if(mysqli_num_rows($deptyps)>0){
		foreach($deptyps as $det){
			echo '<option value="'.$det['id'].'">'.$det['type'].'</option>';
		}
		}
		else{
			echo '<option value="0">No Availabe deposit type</option>';
		}
		echo'</select></p></div>
		<p><div class="row no-gutters">Deposit Amount</div>
		<div class="row no-gutters"><input type="number" style="width:100%;max-width:300px;" value="0" name="depamount" required></div></div></p>
		<div class="row no-gutters" style="justify-content:end;"><div style="border: 1px solid;background:;cursor:pointer;padding:5px 10px;" onclick="addnewdeposit()">New Deposit </div></div>
		<div id="loanarea"><div class="row no-gutters">Pay loan</div>
		<p><div class="row no-gutters"><select style="max-width:300px;width:100%;" name="loanid">';
		$getloans=mysqli_query($con,"SELECT `id`,`loantype`,`loan`,`paid`,`history` FROM `loans` WHERE `client`='$memb'");
			if(mysqli_num_rows($getloans)>0){
			foreach($getloans as $loans){
				$balance=$loans['history']-$loans['paid'];
				if($balance>0){
				$id=$loans['loantype'];
					$loantps=mysqli_query($con,"SELECT * FROM `loantype` WHERE `id`='$id'");
					foreach($loantps as $loan){
						$nm=$loan['name'];
						}
					echo '<option value="'.$loans['id'].'">'.$nm.' Loan Balance &nbsp; &nbsp;'.$balance.'</option>';
					}
				 }
				 echo '</select></div></p><p><div class="row no-gutters">Pay Amount</div>
				 <div class="row no-gutters"><input type="number" value="0" style="width:100%;max-width:300px;" name="amount" required></div></p>';
		}
		else{echo '<option value="0">No Available Loan</option>';}
		
		
		echo'</select></div></p>';
		
		if(mysqli_num_rows($getloans)>1){
			echo '<div class="row no-gutters" style="justify-content:end;"><div style="border:1px solid;cursor:pointer;padding:5px 10px;" onclick="addnewloan('.$memb.')">Add Payment</div>';
		}
		echo '</div>
		<p>
		Risk Fund<br>
		<input type="number" name="riskfund" value="0" style="max-width:300px;width:100%;">
		</p>
		<p><div class="row no-gutters">Date</div>
		<div class="row no-gutters"><input type="date" style="width:100%;max-width:300px;" max="'.date('Y-m-d',time()).'"name="date" value="'.date('Y-m-d',time()).'" required></div></p>
		<div class="row no-gutters" style="justify-content:end;margin-top:10px;"><button class="btn btn-success">Complete</div><br>
		</form></div>';
	}
	if(isset($_GET['groupmembernew'])){
		echo "<div style='max-width:320px;margin:0 auto;'>
		<h3 style='text-align:center;color:#191970;font-size:23px;margin:0px'>Add New Member</h3><br>
		<form method='post' id='mform' onsubmit=\"savemember(event)\">
			<p>Full name<br><input type='text' name='name' style='width:100%' required></p>
			<p>Phone number<br><input type='number' name='fon' style='width:100%' required></p>
			<p>ID Number<br><input type='number' style='width:100%' name='idno' required></p>
			<p>Member Number<br><input type='text' style='width:100%' name='memberno' required></p>
			<p><div class='row'><div class='col-6'>Date of Birth <br> <input type='date' style='width:100%;max-width:140px;' name='dob' max='".date('Y-m-d')."' required></div>
			<div class='col-5'>Member Group<br><select style='width:100%;max-width:120px;' name='grup'>";
			$currentgroup=mysqli_query($con,"SELECT * FROM `groups` WHERE `id`='$ccgrp'");
		foreach($currentgroup as $current){
			$selected=$ccgrp==$current['id']?'selected':'';
			echo '<option value='.$current['id'].' '.$selected.'>'.ucwords($current['name']).'</option>';
		}
			echo "</select></div></div></p>
			<p style='margin-bottom:15px'>Photo<br><input type='file' name='pic' id='pic' style='width:100%;box-shadow:0px;border:0px' accept='image/*' required></p>
			<p>Member Role<br><select name='mrole' style='width:100%;max-width:280px;' required>";
			$roles=mysqli_query($con,"SELECT `id`,`position` FROM `grouppositions`");
			if(mysqli_num_rows($roles)>0){
				foreach($roles as $role){
				echo '<option value='.$role['id'].'>'.$role['position'].'</option>';
				}
			}else{
				echo '<option value="0">No available role</option>';}
			echo "</select></p>
			<p>Place of residence<br><input type='text' name='residence' style='width:100%' required></p>
			<p>Next of Kin name<br><input type='text' name='kname' style='width:100%' required></p>
			<p>Next of Kin Contact<br><input type='tel' name='kcont' style='width:100%' required></p>
			<p>Share percentage<br><input type='number' name='share' style='width:100%' required></p>
			<p><div class='row no-gutters' id='ttle' style='font-weight:bold;padding-left:20px;'></div></p>
			<p><div id='kin'></div></p>
			<div class='row no-gutters' style=padding-left:10px;'><i class='bi bi-plus-circle-fill' style='font-size:26px;color:green;' onclick='addkin()';></i></div>
			<p style='text-align:right'><button class='btnn'>Save</button></p><br>
		</form><br>
	</div>";
	}
	mysqli_close($con);
?>


<script>
	function newactivity(id){
		moredismiss()
		popupload('groups.php?newactivity='+id)
	}
	function savegroup(e){
		e.preventDefault();
		if(confirm("Add new group?")){
			var data=$("#gform").serialize();
			$.ajax({
				method:"post",url:"savemember",data:data,
				beforeSend:function(){ progress("Processing...please wait"); },
				complete:function(){progress();}
			}).fail(function(){
				toast("Failed: Check internet Connection");
			}).done(function(res){
				if(res.trim().split(":")[0]=="success"){
					fetchpage("groups?fetch"); closepop(); toast("Added successfully!");
				}
				else{ alert(res); }
			});
		}
	}
function editgroup(id){
	popupload("groups?egroup="+id)
}
function opengroup(id){
	$(".popdiv").html("<br><br><center><img src='assets/img/waiting.gif'></center>");
	document.cookie="cgroup="+id+";expires=''; path=/";
	let data="setgroup="+id;
	$.ajax({method:"POST",url:"savemember",data:data}).fail(
		()=>{toast("An error occured opening group")}).done((e)=>{
			if(e.trim()=="success"){
				fetchpage('groups?viewgroup='+id)
				$(".operations").load("groups?operations")
			}else{
				toast("An Error occured! Please try again later!")
			}
		})
	
}
function getgroupops(id){
	$(".popdiv").html("<br><br><center><img src='assets/img/waiting.gif'></center>");
				$("#content").load('groups?groupopts='+id)
	}
function giveloan(grp){
	moredismiss()
	popupload("groups?loanmoney="+grp)
	}
function investmoney(id){
	moredismiss()
	popupload("groups?investmoney="+id)
}
function changerecepient(id,grp){
	popupload('groups?changerec='+id+'&group='+grp)
}
function tbload(){
	let x=document.cookie;
		let ckarr=x.split(";");
		for(let i=0;i<ckarr.length;i++){
			if(ckarr[i].includes('cgroup')){
				let pr=ckarr[i].split('=');
				$("#cgrouptable").load("groups?currentgroup="+pr[1]);
			}
		}
}
function ldtbl(){
	$(".mtbl").load("groups?loadgroups");
}
$("#egroup").on("submit",(e)=>{
	e.preventDefault()
	let data=$("#egroup").serialize();
	
	$.ajax({method:"POST",url:"savemember",data:data,
		beforeSend:()=>{progress("Updating detaild..")},
		complete:()=>{progress();}
	}).fail(()=>{toast("Failed to update group details");}).done((e)=>{
		ldtbl()
		let res=e.trim();
		if(res=="success"){
			toast("Updated group details");
			closepop()
		}else{
			toast("Failed to update");
			closepop()
		}
		
		}
	)
})
$("#loanreq").on("submit",(e)=>{
	e.preventDefault();
	let data=$("#loanreq").serialize();
	$.ajax({data:data,method:"POST",url:"savemember",
	beforeSend:()=>{progress("sending data");},
	complete:()=>{progress();}}).fail(
	(e)=>{toast("Failed requesting loan");}).done((e)=>{
		console.log(e.trim())
		tbload();
		if(e.trim()=="success"){closepop();
			getgroupops(currentgroup)
			toast("Loan Application was successfull!")
		}else if(e.trim()=="low"){
			toast("The A/C balance is not sufficient to complete the request");
		}else{
		toast("Failed to request loan!");
	}})
})
$("#paydebt").on("submit",(e)=>{
	e.preventDefault();
let data=$("#paydebt").serialize();
$.ajax({method:"POST",data:data,url:"savemember",
		beforeSend:()=>{progress("Processing request..")},
		complete:()=>{progress()}
	}).fail(()=>{
		toast("Internet Connection Error! Please try again later")
	}).done((e)=>{
		console.log(e.trim());
	if(e.trim()=="success"){
		toast("Request compeleted successfully");
		closepop();
	}
	else if(e.trim()=='more'){
		toast("Sorry, account overdraft payment is not allowed!")
	}
	else{
		toast("The ")
	}
})
})
function changedbtid(id,grp){
	popupload('groups?changedbt='+id+'&dbtgroup='+grp);
}
$("#invest").on("submit",(e)=>{
	e.preventDefault();
	let data=$("#invest").serializeArray();
	let info=[];
	for(let i=2;i<data.length;i++){
		if(data[i].name=="type"){
			info.push('"'+data[i].value+'":"'+data[i+1].value+'"');
		}
	}
	
	let sdata=data[0].name+"="+data[0].value+"&"+data[1].name+"="+data[1].value+"&other="+info;
	
	$.ajax({
		data:sdata,
		method:"POST",
		url:"savemember",
		beforeSend:()=>{progress("Processing request..");},
		complete:()=>{progress();}
		}).fail(
		(e)=>{toast("Failed to complete request");
		}).done((e)=>{
			console.log(e.trim())
			tbload();
			if(e.trim()=="success"){
				closepop()
				toast("success");}
			});
	})

function loanpay(grp,id){
	popupload("groups?loanpay="+id+"&grp="+grp)
}
$("#payloan").on("submit",(e)=>{
	e.preventDefault();
	let data=$("#payloan").serialize();
	
	$.ajax({method:"POST",url:"savemember",data:data,
	beforeSend:()=>{progress("Updating loan payment....")},
	complete:()=>{progress()}}).fail((e)=>{
		closepop()
	toast("Error:Internet Connection error!");}
	).done((e)=>{
		tbload();
		if(e.trim()=="success"){closepop();toast("Update success!");}})
})
function groupcharges(value){
	moredismiss()
	popupload("groups?charges="+value);
}
function groupexpe(grp){
	moredismiss()
	$(".grpview").load("groups?expenses="+grp)
}
function paydebts(group){
	moredismiss();
	popupload("groups?debtgroup="+group);
}
function changefine(val,group){
	popupload('groups?changefine='+val+'&group='+group);
}
$("#charges").on("submit",(e)=>{
	e.preventDefault();
	let data=$("#charges").serialize();
	$.ajax({method:"POST",url:"savemember",data:data,
	beforeSend:()=>{progress("Sending data...");},
	complete:()=>{progress();}
	}).fail(()=>{closepop();toast("Error:Internet Connection error!");}).done(
	(e)=>{
		if(e.trim()=="success"){
		closepop();toast("Update success!");
	}})
	})

$("#membership").on("submit",(e)=>{
	e.preventDefault();
	let data=$("#membership").serialize();
	$.ajax({method:"POST",url:"savemember",data:data,
	beforeSend:()=>{},
	complete:()=>{}
	}).fail(()=>{toast("Connection Error")}).done((e)=>{

		if(e.trim()=="success"){
			tbload();
			toast("Updated Successfully")
			closepop();
			
		}else{
			tbload();
			toast("Failed to update")
			closepop();
		}
	})
	
})
$("#exdt").on("submit",(e)=>{
	e.preventDefault();
	let data=$("#exdt").serialize()
	$.ajax({method:"POST",url:"savemember",data:data,
	beforeSend:()=>{},
	complete:()=>{}
	}).fail(
			()=>{toast("An Error occured Updating debt")}
		).done((e)=>{
			let dt=e.trim();
			console.log(dt)
			if(dt=="success"){
				closepop();
				toast("Request compeleted successfully!")
			}
			else{
				toast("Request failed!");
			}
		})
})
$("#rexpe").on("submit",(e)=>{
	e.preventDefault()
	let data=$("#rexpe").serialize();
	$.ajax({method:"POST",url:"savemember",data:data,
	beforeSend:()=>{},
	compelete:()=>{}}).fail(()=>{toast("Unable to send current request!")}).done((e)=>{
		if(e.trim()=="success"){
			closepop();toast("Expenses updated!");
		}
		else{
			closepop();toast("Failed to update expenses");}
	})
})
$("#offexpe").on("submit",(e)=>{
	e.preventDefault()
	let data=$("#offexpe").serialize();
	$.ajax({method:"POST",url:"savemember",data:data,
	beforeSend:()=>{},
	compelete:()=>{}}).fail(()=>{
		toast("Unable to send current request!")}).done((e)=>{
		if(e.trim()=="success"){
			closepop();toast("Expenses updated!");
		}
		else{
			closepop();toast("Failed to update expenses");}
	})
})
$("#withdr").on("submit",(e)=>{
	e.preventDefault();
	let data= $("#withdr").serialize();
	$.ajax({method:"POST",url:"savemember",data:data,
		beforeSend:()=>{progress("Requesting member withdrawal")},
		complete:()=>{progress()}
	}).fail(()=>{toast("The system encountered a connection problem!")}).done(
		(e)=>{
			console.log(e.trim());
			if(e.trim()=="success"){
				toast("Transaction accepted");
				closepop();
				opengroup(currentgroup())
			}else if(e.trim()=='insufficient'){
				toast("Sorry the available account balance is not sufficient!");
			}
			else{
				toast("An error occured processing request");
			}
		}
	)
	
})
$("#riskf").on("submit",(e)=>{
	e.preventDefault();
	let data=$("#riskf").serialize();
	$.ajax({method:"POST",url:"savemember",data:data,
		beforeSend:()=>{progress("Updating fund fees")},
		complete:()=>{progress()}
	}).fail(()=>{toast("The system encountered a connection problem!")}).done(
		(e)=>{
			if(e.trim()=="success"){
				toast("Updated successfully");
				closepop();
			}
			else{
				toast("An error occured updating fund risk");
			}
		}
	)
})
function getloans(id){
	moredismiss()
	popupload("groups?memberloans="+id)
}
function displaySavings(id){
	moredismiss()
	popupload("groups?getsavings="+id);
}
function changemember(grp,value){
	moredismiss()
	popupload("groups?giveloanto="+value+"&cgroup="+grp);
}
function changepos(grp,val){
	moredismiss()
	popupload("groups?changepos="+val+"&grp="+grp);
}

function updatemember(id){
	popupload("groups?updateuser="+id)
}
function deletemember(id,grp){
	let data="delmember="+id;
	$.ajax({method:"POST",url:"savemember",data:data,
		beforeSend:()=>{progress("Deleting Member from group")},
		complete:()=>{progress()}
	}).fail(()=>{toast("Request can not be compeleted!")}).done((e)=>{
		$(".grpview").load("groups?managemembers="+grp)
		if(e.trim()=="success"){
		toast("One Member was deleted successfully!")}
		else{
			toast("Operation Failed!")
		}
	})
}
function managegroup(grp){
	moredismiss()
	$(".grpview").load("groups?managemembers="+grp)
}
function withdraw(grp){
	moredismiss()
	popupload("groups?withdrawal="+grp)
}
$("#upmember").on("submit",(e)=>{
	e.preventDefault();	
	let info=$("#upmember").serializeArray();
	let data='';
	for(let i=0;i<5;i++){
		data=data+info[i].name+"="+info[i].value+"&";
	}
	data=data.replace(/&\s*$/, "")
	let more='';
	let total=0;
	for(let j=4;j<info.length;j++){
		
		if(info[j].name.includes("name")){
			
			more=more+info[j].value+'":"['+info[j+1].value+','+info[j+2].value+']","';
			total=total+parseInt(info[j+2].value);
		}

	}
	if(total>100){
		alert("Sharing percentage is out of range!");
	}else{
	dt=more.replace(/\"\s*$/,"");
	dt="&kin="+dt;
	data=data+dt;
	$.ajax({method:"POST",url:"savemember",data:data,
	beforeSend:()=>{progress("Updating member..");},
	complete:()=>{progress()}}).fail(()=>{toast("An error occured updating member!");
		closepop();}).done((e)=>{
		if(e.trim()=="success"){
			closepop();
			toast("Member Updated");
		}
		
	})}
	
})
$("#eddeposit").on("submit",(e)=>{
	e.preventDefault();
	let data=$("#eddeposit").serialize();
	$.ajax({method:"POST",
		url:"savemember",
		data:data,beforeSend:()=>{progress("Sending request..")},
		complete:()=>{
			progress();}}).fail(
			()=>{toast("Sorry, your request could not be sent!")}
		).done((e)=>{
		managedeposits(currentgroup())
		if(e.trim()=="success"){
			closepop();toast("Updated successfully");
		}else{
			toast("Request failed!");
		}
	})
})
function editdeta(id){
	moredismiss()
	popupload("groups?eddeposit="+id)
}
function summary(grp){
	moredismiss()
	$("#content").load("groups?summary="+grp)
}
function deldepo(id){
	let data="deldepo="+id;
	$.ajax({method:"POST",url:"savemember",data:data}).fail(()=>{toast("An error occured deleting item")}).done((e)=>{
		if(e.trim()=="success"){managedeposits(currentgroup());toast("Deposit deleted successfully")}
		else{
			toast("Operation failed!");
		}
	})
}
function gettypes(cid){
	$.get("savemember?depotypes",(data)=>{document.getElementById(cid).innerHTML=data})
}
function deletegroup(id){
	let data="deletegroup="+id;
	$.ajax({method:"POST",data:data,url:"savemember",
		beforeSend:()=>{progress("Processing request")},
		complete:()=>{progress()}}).fail(
			(e)=>{toast("Connection Error: A connection error occured")
			}).done((e)=>{
		if(e.trim()=="success"){
			toast("The group  was successfully deleted");
			dashbd();
		}else{
			toast("Sorry, the operation can not be compeleted at the moment!");
		}
	})
}

function addnkin(){
	$("#newh").append('<p><div class="row no-gutters">Next of Kin Name</div><div class="row no-gutters">'
	+'<input style="width:100%;max-width:300px;" type="text" name="name"></div></p><p><div class="row no-gutters">Contact</div>'+
	'<div class="row no-gutters"><input style="width:100%;max-width:300px;" type="number" minlength="9" maxlenght="12" name="cont"></div></p><p><div class="row no-gutters">Share percentage</div><div class="row no-gutters"><input  style="width:100%;max-width:300px;" type="number" min="0" max="100" name="perc"></div></p>');
}
function currentgroup(){
	let x=document.cookie;
		let ckarr=x.split(";");
		for(let i=0;i<ckarr.length;i++){
			if(ckarr[i].includes('cgroup')){
				let pr=ckarr[i].split('=');
				return pr[1];
			}
		}
}
function nextgroup(total,perpage,current){
	$("#progdv").fadeIn(); 
	$(".mtb1").load('groups?nextgroup='+current+"&limit="+perpage+"&count="+total);$("#progdv").fadeOut(); 
}
function membersummary(grp,id){
$("#content").load("groups?mactivity="+id+"&mgroup="+grp)
}
function getmorepos(){
	if($(".content-header").width()>=700){
		$(".more").css("left","600px")
	}else{
		$(".more").css("left",($(".content-header").width()-150)+'px')
	}
}
function gethistory(){
	$(".more").fadeIn();
	getmorepos()
}
function moredismiss(){
	$(".more").hide();
}
function changecharges(id){
	
	$(".type").load('savemember?chargetp='+id);
}
$(window).on('resize',(e)=>{getmorepos()})
function getalldepos(id){
$.get('savemember.php?alldepos',(data)=>{document.getElementById('depotypes'+id).innerHTML=data});
}
function getmemberloans(id,mbn){
$.get('savemember.php?getallloans='+mbn,(data,status)=>{document.getElementById('loantypes'+id).innerHTML=data });
}
function addnewloan(mb){
	let d=Math.random();
	getmemberloans(d,mb)
	$("#loanarea").append('<div id="loa'+d+'"><p><div style="width:100%;display:flex;flex-direction:row;justify-content:space-between;"><div>Pay loan</div> <div style="cursor:pointer;height:20px;width:30px;" onclick="remloan('+d+')"> <i class="bi bi-x-lg" style="color:red;size:20px;"></i></div></div><br><select name="loanid" id="loantypes'+d+'"></select></p><p>Amount<br/><input type="number" value="0" required style="width:100%;max-width:300px;" name="depamount"></p></div>');
}
function addnewdeposit(){
	let d=Math.random()
$("#membnewact").append('<p><div id="dep'+d+'"><div style="width:100%;display:flex;flex-direction:row;justify-content:space-between;"><div>Add deposit</div> <div style="cursor:pointer;height:20px;width:30px;" onclick="remdepo('+d+')"><i class="bi bi-x-lg" style="color:red;size:20px;"></i></div></div><select name="depoid" id="depotypes'+d+'"></select></p><p>Amount<br/><input type="number" value="0" required style="width:100%;max-width:300px;" name="amount"></div></p>');
getalldepos(d);}
function remdepo(d){
	document.getElementById("dep"+d).remove();
}
function remloan(d){
	document.getElementById("loa"+d).remove();
}
$("#compactivity").submit((e)=>{
	e.preventDefault();
let narr=$("#compactivity").serializeArray();
let len=narr.length;
	let deposits='';loans='';
	narr.forEach((element,index)=>{
		if(element.name=='depoid'){
			deposits+='"'+element.value+'":"'+narr[index+1].value+'",';
		}
		else if(element.name=='loanid'){
			loans+='"'+element.value+'":"'+narr[index+1].value+'",';
		}
	})
	let loan=loans.replace(/(^,)|(,$)/g, '');
	let deposit=deposits.replace(/(^,)|(,$)/g, '');
	let data='acmember='+narr[0].value+'&loans={'+loan+'}&deposits={'+deposit+'}&risk='+narr[len-2].value+'&recdate='+narr[len-1].value;
	$.ajax({method:'POST',data:data,url:'savemember'}).fail(()=>{toast("Connection Error! Check your internet connection and try again!")}).done(
		(e)=>{
			console.log(e.trim())
			if(e.trim()=="success"){
				getgroupops(currentgroup)
				closepop();
				toast("Request compeleted successfully!");
			}
			else{
				toast("Sorry, Your request could not be completed!")
			}
		}
	)
})
function lendercategory(name){
	let req='category='+name;
    $.ajax({method:'GET',data:req,url:'savemember'}).fail(()=>{
		
		toast("Connection Error! Check your internet connection and try again!")}
	).done(
		(e)=>{
			$("#lenderid").html(e.trim())
		}
	)
}
function searchgroupname(str){
	if(str.length>=3){
		$("#pgnation").hide();
		$.get('savemember?searchgroup='+str,(data,textStatus)=>{$(".mtb1").html(data)}
				)
	}
	else{
		$("#pgnation").show();
		$.ajax('savemember?allgroups',(data,txt)=>{$(".mtb1").html(data);console.log(data,txt,st)});
	}
}
function newgroupmember(){
	popupload('groups?groupmembernew');
}
</script> 