<?php
	
	require "functions.php";
	$con = mysqli_connect("localhost",DB_USER,DB_PASS,DB_NAME);
	
	# assign loan
	if(isset($_GET['assign'])){
		$mid = trim($_GET['assign']); 
		$pid = (isset($_GET['pid'])) ? trim($_GET['pid']):0;
		
		$opts="<option value='0'> Select Product</option>";
		$sql = mysqli_query($con,"SELECT *FROM `products` ORDER BY `name` ASC");
		while($row=mysqli_fetch_assoc($sql)){
			$rid=$row['id']; $name=prepare(ucwords($row['name'])); $cnd=($rid==$pid) ? "selected":""; $prods[$rid]=$row;
			$opts.="<option value='$rid' $cnd>$name</option>";
		}
		
		$amnt=($pid) ? $prods[$pid]['amount']:0;
		$dur=($pid) ? $prods[$pid]['duration']:0; $lis="";
		
		foreach(array(1=>"Daily",7=>"Weekly",28=>"Monthly") as $key=>$txt){
			$lis.="<option value='$key'>$txt</option>";
		}
		
		echo "<div style='max-width:320px;margin:0 auto;'>
			<h3 style='text-align:center;color:#191970;font-size:23px;margin:0px'>Assign Loan</h3><br>
			<form method='post' id='lform' onsubmit=\"saveloan(event)\">
				<input type='hidden' name='mid' value='$mid'> <input type='hidden' name='dur' value='$dur'>
				<p>Loan product<br><select name='prod' style='width:100%' onchange=\"popupload('members?assign=$mid&pid='+this.value)\">$opts</select></p>
				<p>Amount<br><input type='number' name='lamnt' style='width:100%' value='$amnt' required></p>
				<p>Loan repayment starts on <br><input type='date' style='width:100%' name='start' value='".date('Y-m-d')."' min='".date('Y-m-d')."' required></p>
				<p>Payment Intervals<br><select name='payterms' style='width:100%' required>$lis</select></p><br>
				<p style='text-align:right'><button class='btnn'>Save</button></p><br>
			</form><br>
		</div>";
	}
	
	# add member
	if(isset($_GET['add'])){
		$opts ="";
		$qri = mysqli_query($con,"SELECT *FROM `groups` ORDER BY `name` ASC");
		while($row=mysqli_fetch_assoc($qri)){
			$gname = prepare(ucwords($row['name'])); $gid=$row['id'];
			$opts.="<option value='$gid'>$gname</option>";
		}
	
		$opts = ($opts) ? $opts:"<option value='0'>-- None --</option>";
		echo "<div style='max-width:320px;margin:0 auto;'>
			<h3 style='text-align:center;color:#191970;font-size:23px;margin:0px'>Add New Member</h3><br>
			<form method='post' id='mform' onsubmit=\"savemember(event)\">
				<p>Full name<br><input type='text' name='name' style='width:100%' required></p>
				<p>Phone number<br><input type='number' name='fon' style='width:100%' required></p>
				<p>ID Number<br><input type='number' style='width:100%' name='idno' required></p>
				<p>Date of Birth <span style='float:right'>Member Group</span><br> <input type='date' style='width:55%' name='dob' max='".date('Y-m-d')."' required>
				<select style='width:43%;float:right;padding:5px' name='grup'>$opts</select></p>
				<p style='margin-bottom:15px'>Photo<br><input type='file' name='pic' id='pic' style='width:100%;box-shadow:0px;border:0px' accept='image/*' required></p>
				<p>Next of Kin name<br><input type='text' name='kname' style='width:100%' required></p>
				<p>Next of Kin Contact<br><input type='number' name='kcont' style='width:100%' required></p><br>
				<p style='text-align:right'><button class='btnn'>Save</button></p><br>
			</form><br>
		</div>";
	}
	
	# view members
	if(isset($_GET['fetch'])){
		$mgrp = trim($_GET['fetch']);
		$cond = ($mgrp) ? "`mgroup`='$mgrp'":1;
		
		$opts = "<option value='0'>-- Group --</option>";
		$qri = mysqli_query($con,"SELECT *FROM `groups` ORDER BY `name` ASC");
		while($row=mysqli_fetch_assoc($qri)){
			$grp = $row['id']; $gname=prepare(ucwords($row['name'])); $cnd = ($grp==$mgrp) ? "selected":""; $groups[$grp]=$gname;
			$opts.="<option value='$grp' $cnd>$gname</option>";
		}
		
		$sql = mysqli_query($con,"SELECT *FROM `members` WHERE $cond ORDER BY `name` ASC"); $trs="";
		while($row=mysqli_fetch_assoc($sql)){
			$mid=$row['id']; $name=ucwords(prepare($row['name'])); $uid=$row['uid']; $fon=$row['phone']; $idno=$row['idno']; $pic=$row['photo'];
			$kin=ucwords(prepare($row['kin_name'])); $cont=$row['kin_contact']; $dob=$row['dob']; $gid=$row['mgroup']; 
			$loan = "<button class='btnn' style='padding:4px;min-width:60px' onclick=\"popupload('members?assign=$mid')\"><i class='bi-plus-lg'></i> Assign</button>";
			
			$qri = mysqli_query($con,"SELECT *FROM `loans` WHERE `client`='$mid'");
			if(mysqli_num_rows($qri)){
				$info = mysqli_fetch_assoc($qri); $paid=$info['paid']; $amnt=$info['amount'];
				$loan = ($amnt>$paid) ? "KES ".number_format($amnt)."<br><span style='color:grey;font-size:14px'>Paid: ".number_format($paid)."</span>":$loan;
			}
			
			$trs.="<tr valign='top'><td style='width:50px'><img src='data:image/jpg;base64,$pic' style='width:40px;border-radius:50%'></td><td>$name<br>
			<span style='color:grey;font-size:14px'>DOB: $dob</span></td><td>$idno<br><span style='color:grey;font-size:14px'>UID: $uid</span></td>
			<td>$groups[$gid]<br><span style='color:grey;font-size:14px'>0$fon</span></td><td>$kin<br><span style='color:grey;font-size:14px'>0$cont</span></td><td>$loan</td></tr>";
		}
	
		echo "<h3 style='font-size:23px;'>Members</h3>
		<table style='width:100%;min-width:500px' class='table-striped mtbl'>
			<caption style='caption-side:top'>
				<button class='btnn' style='padding:6px;font-size:15px;float:right' onclick=\"popupload('members?add')\"><i class='bi-person-plus'></i> Member</button>
				<select style='width:120px;padding:5px;font-size:15px' onchange=\"fetchpage('members?fetch='+this.value)\">$opts</select>
			</caption>
			<tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria'><td colspan='2'>Member</td><td>Idno/UID</td>
			<td>Group/Phone</td><td>Next of Kin</td><td>Loan</td></tr> $trs 
		</table>";
	}
	
	# view loans
	if(isset($_GET['loans'])){
		$ltp = trim($_GET['loans']);
		$cond = ($ltp) ? "AND `mgroup`='$mgrp'":"";
		
		$sql = mysqli_query($con,"SELECT *FROM `members`");
		while($row=mysqli_fetch_assoc($sql)){
			$names[$row['id']]=$row;
		}
		
		$sql = mysqli_query($con,"SELECT *FROM `products`");
		while($row=mysqli_fetch_assoc($sql)){
			$prods[$row['id']]=$row['name'];
		}
		
		$sql = mysqli_query($con,"SELECT *FROM `loans` WHERE `status`='1' $cond ORDER BY `time` DESC"); $trs="";
		while($row=mysqli_fetch_assoc($sql)){
			$lid=$row['loan']; $name=ucwords(prepare($names[$row['client']]['name'])); $prod=ucwords(prepare($prods[$row['product']])); 
			$fon=$names[$row['client']]['phone'];  $amnt=number_format($row['amount']); $dur=$row['period']; $bal=number_format($row['amount']-$row['paid']);
			$pic=$names[$row['client']]['photo']; $day=date("d-m-Y",$row['time']); $paid=number_format($row['paid']);
			
			$trs.="<tr valign='top'><td style='width:50px'><img src='data:image/jpg;base64,$pic' style='width:40px;border-radius:50%'></td><td>$name<br>
			<span style='color:grey;font-size:14px'>0$fon</span></td><td>KES $amnt<br><span style='color:grey;font-size:14px'>$day</span></td>
			<td>$prod<br><span style='color:grey;font-size:14px'>For $dur days</span></td><td>KES $paid<br>
			<span style='color:grey;font-size:14px'>Bal: $bal</span></td><td>Running</td></tr>";
		}
	
		echo "<h3 style='font-size:23px;'>Member Loans</h3>
		<table style='width:100%;min-width:500px' class='table-striped mtbl'>
			<tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria'><td colspan='2'>Member</td><td>Loan</td>
			<td>Product</td><td>Repayment</td><td>Status</td></tr> $trs 
		</table>";
	}
	
	
	mysqli_close($con);
?>

<script>
	
	function saveloan(e){
		e.preventDefault();
		if(confirm("Assign Loan to member?")){
			var data=$("#lform").serialize();
			$.ajax({
				method:"post",url:"savemember",data:data,
				beforeSend:function(){ progress("Processing...please wait"); },
				complete:function(){progress();}
			}).fail(function(){
				toast("Failed: Check internet Connection");
			}).done(function(res){
				if(res.trim().split(":")[0]=="success"){
					fetchpage("members?fetch"); closepop(); toast("Assigned successfully!");
				}
				else{ alert(res); }
			});
		}
	}
	
	function savemember(e){
		e.preventDefault();
		var img = document.getElementById("pic").files[0];
		if(confirm("Continue to save member details?")){
			var formdata=new FormData(document.getElementById("mform"));
			var xhr=new XMLHttpRequest();
			xhr.upload.addEventListener("progress",profprogress,false);
			xhr.addEventListener("load",profdone,false);
			xhr.addEventListener("error",proferror,false);
			xhr.addEventListener("abort",profabort,false);
			formdata.append("photo",img);
			xhr.onload=function(){
				if(this.responseText.trim()=="success"){
					toast("Saved successfull"); fetchpage("members?fetch"); closepop();
				}
				else{ alert(this.responseText); }
			}
			xhr.open("post","savemember",true);
			xhr.send(formdata);
		}
	}
	
	function profprogress(event){
		var percent=(event.loaded / event.total) * 100;
		progress("Uploading "+Math.round(percent)+"%");
		if(percent==100){
			progress("Cropping...please wait");
		}
	}
		
	function profdone(event){ progress(); }

	function proferror(event){
		toast("Upload failed"); progress();
	}

	function profabort(event){
		toast("Upload aborted"); progress();
	}

</script>