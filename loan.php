<?php
	
	require "functions.php";
	$con = mysqli_connect("localhost",DB_USER,DB_PASS,DB_NAME);
	if(isset($_SESSION['grptable'])){
		$ccgrp=$_SESSION['grptable'];}
		$opts = "<option value='0'>-- Group --</option>";
		$qri = mysqli_query($con,"SELECT *FROM `groups` ORDER BY `name` ASC");
		while($row=mysqli_fetch_assoc($qri)){
			$grp = $row['id']; $gname=prepare(ucwords($row['name'])); $cnd = ($grp==@$mgrp) ? "selected":""; $groups[$grp]=$gname;
			$opts.="<option value='$grp' $cnd>$gname</option>";
		}
	
	# save product
	if(isset($_POST['pname'])){
		$name = strtolower(clean($_POST['pname']));
		$amnt = clean($_POST['lamnt']);
		$dur = clean($_POST['dur']);
		
		$chk = mysqli_query($con,"SELECT *FROM `products` WHERE `name`='$name'");
		if(mysqli_num_rows($chk)){ echo "Failed: Product ".prepare($name)." already exists"; }
		else{
			if(mysqli_query($con,"INSERT INTO `products` VALUES(NULL,'$name','$amnt','$dur')")){
				echo "success";
			}
			else{ echo "Failed to complete the request! Try again later"; }
		}
		
		mysqli_close($con); exit();
	}
	# view products
	if(isset($_GET['fetch'])){
		$trs=""; $no=0;
		$sql = mysqli_query($con,"SELECT *FROM `products` ORDER BY `name` ASC");
		while($row=mysqli_fetch_assoc($sql)){
			$rid=$row['id']; $name=prepare(ucwords($row['name'])); $amnt=number_format($row['amount']); $dys=$row['duration']; $no++;
			$chk = mysqli_query($con,"SELECT *FROM `loans` WHERE `product`='$rid'"); $sum=mysqli_num_rows($chk);
			
			$trs.="<tr><td>$no</td><td>$name</td><td>KES $amnt</td><td>$dys days</td><td>$sum</td></tr>";
		}
		
		echo "<div style='max-width:1240px;margin:0 auto'>
			<h3 style='font-size:23px;'>Loan Products
			<button class='btnn' style='padding:6px;font-size:15px;float:right' onclick=\"popupload('products?add')\"><i class='bi-plus-lg'></i> Product</button></h3>
			<table style='width:100%' class='table-striped mtbl'>
				<tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria'><td colspan='2'>Product</td><td>Amount</td><td>Duration</td>
				<td>Assigned Loans</td></tr> $trs 
			</table>
		</div>";
	}
	
	# add product 
	if(isset($_GET['add'])){
		echo "<div style='max-width:300px;margin:0 auto;'>
			<h3 style='text-align:center;color:#191970;font-size:23px;margin:0px'>Add Loan Product</h3><br><br>
			<form method='post' id='pform' onsubmit=\"saveproduct(event)\">
				<p>Product name<br><input type='text' name='pname' style='width:100%' required></p>
				<p>Maximum Amount<br><input type='number' name='lamnt' style='width:100%' required></p>
				<p>Payment duration (days)<br><input type='number' name='dur' style='width:100%' required></p><br>
				<p style='text-align:right'><button class='btnn'>Save</button></p><br>
			</form><br>
		</div><br>";
	}
	#add loan
	if(isset($_GET['addloan'])){
	echo "<div style='max-width:300px;margin:0 auto;'>
			<h3 style='text-align:center;color:#191970;font-size:23px;margin:0px'>Add Loan Type</h3><br><br>
			<form method='post' id='addloan' onsubmit=\"addnewloan(event)\">
				<p>Loan name<br><input type='text' name='lname' style='width:100%;max-width:300px;' required></p>
				<p>Interest rate(%)<br><input type='number' name='intr' style='width:100%;max-width:300px;' required></p>
				<p>Overdue charges(use % for percentile)<br><input type='text' name='rperiod' style='width:100%;max-width:300px;' required></p>
				<p>Interest Period(%)<br><select name='period' style='width:100%;max-width:300px;'>
				<option value='monthly'>Per Month</option>
				<option value='annually'>Per Annum</option>
				</select></p>
				<br>
				<p style='text-align:right'><button class='btnn'>Update</button></p><br>
			</form><br>
		</div><br>";}
		
		#add saving
		if(isset($_GET['addsave'])){
			echo "<div style='max-width:300px;margin:0 auto;'>
			<h3 style='text-align:center;color:#191970;font-size:23px;margin:0px'>Add Saving Type</h3><br><br>
			<form method='post' id='addsave' onsubmit=\"addsave(event)\">
				<p>Product name<br><input type='text' name='sname' style='width:100%' required></p>
				<p>Interest(%)<br><input type='number' name='int' min='1' style='width:100%' required></p><br>
				<p style='text-align:right'><button class='btnn'>Update</button></p><br>
			</form><br>
		</div><br>";
		}
	#add fine and charges
	if(isset($_GET['charges'])){
		echo "<div style='max-width:300px;margin:0 auto;'>
			<h3 style='text-align:center;color:#191970;font-size:23px;margin:0px'>Add Charges</h3><br><br>
			<form method='post' id='charges' onsubmit=\"charges(event)\">
				<p>Charges name<br><input type='text' name='cname' style='width:100%;max-width:300px;' required></p>
				<p>Select type<br>
				<select name='ctype' style='width:100%;max-width:300px;'>
				<option value='1'>Fixed amount</option>
				<option value='0'>Random</option>
				</select></p>
				<p>Amount <br>
				<input type='number' min='0' style='width:100%;max-width:300px;' name='camount' required></p>
				<p>Collection<br>
				<select style='width:100%;max-width:300px;' name='collection'>
				<option value='1'> Group</option>
				<option value='2'>Company</option>
				</select></p>
				<p style='text-align:right'><button class='btnn'>Add</button></p><br>
			</form><br>
		</div><br>";
	}
	if(isset($_GET['homepg'])){
		echo '<h3 style="font-size:23px;">Members </h3>
		<div class="table-responsive">
		  <table style="width:100%;min-width:600px;font-family:cambria;font-size:14px;" class="table-striped mtbl">
			  <caption style="caption-side:top">
				  <button class="btnn" style="padding:6px;font-size:15px;float:right" onclick="popupload(\'members?add\')"><i class="bi-person-plus"></i> Member</button>
					  <select style="width:125px;padding:5px;font-size:15px" onchange="fetchpage(\'members?fetch=\'+this.value)">'.$opts.'</select>
						  <input type="search" onkeyup="searchperson(this.value)" style="width:120px;margin-left:20px;" placeholder="Search member.."></caption><tbody id="mbs">';
						  $allmembers=mysqli_query($con,"SELECT * FROM `members` WHERE `status`=1");
						  $getall=mysqli_num_rows($allmembers);
						  $perpage=20;
						  if($getall>$perpage){
						  $pages=ceil($getall/$perpage);
						  if($pages>1){
							  echo '<tr><td colspan="6" style="background:#f0f0f0;padding:0px"><div class="row no-gutters" style="justify-content:end;padding:0px;"><select onchange="getclist(this.value,'.$pages.','.$perpage.')" style="margin-right:15px;">';
						  for($i=0;$i<$pages;$i++){
							  $cl=($i==0)?"selected":'';
						  echo '<option '.$cl.' value="'.$i.'">'.($i+1).'</option>';}echo '</select></td></tr>';}
					  }
				  echo '<tr style="background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria"><td colspan="2">Member</td><td>Idno/UID</td>
					  <td>Group/Phone</td><td>Next of Kin</td><td>Loan</td></tr>';
					  
					  $sql = mysqli_query($con,"SELECT *FROM `members` WHERE `status`=1 ORDER BY `name` ASC LIMIT $perpage"); $total=mysqli_num_rows($sql);
						  foreach($sql as $row){
							  $mid=$row['id']; $name=ucwords(prepare($row['name'])); $uid=$row['uid']; $fon=$row['phone']; $idno=$row['idno']; $pic=strlen($row['photo'])>1?'data:image/jpg;base64,'.$row['photo']:'assets/img/user.png';
							  $kinname=[];$kincont=[];
							  if(strlen($row['nextkin'])>2){
							  foreach(json_decode(@$row['nextkin'],1) as $kin=>$kindet){
								  $kcont=explode(",",str_replace(']','',str_replace('[','',$kindet)));
								  array_push($kinname,$kin);array_push($kincont,$kcont[0]);
							  }}
							  $kin=strlen(@$kinname[0])>2?ucwords(@$kinname[0]):"";
							  $cont=strlen(@$kincont[0])>5?'0'.ltrim(ltrim(clean(@$kincont[0]),"0"),"254"):''; $grp=$row['mgroup'];$dob=$row['dob'];
							  $loan = "<button class='btnn' style='padding:4px;min-width:60px' onclick=\"popupload('members?assign=$mid')\"><i class='bi-plus-lg'></i> Assign</button>";
								  
							  $qri = mysqli_query($con,"SELECT *FROM `loans` WHERE `client`='$mid'");
							  $tloan=0;$tpaid=0;
								  foreach($qri as $qris){
									  $paid=$qris['paid']; $amnt=$qris['history'];
									  if(($qris['product']<1) && ($paid<$amnt)){
										  $loan=$loant = "<button class='btnn' style='padding:4px;min-width:60px' onclick=\"popupload('members?assign=$mid')\"><i class='bi-plus-lg'></i> Assign</button>";
									  }else{
								  $tpaid=$tpaid+$qris['paid'];$tloan=$tloan+$amnt=$qris['history'];
								  $loan="KES ".number_format($tloan)."<br><span style='color:grey;font-size:14px'>Paid: ".number_format($tpaid)."</span>";}
								  }
							  echo "<tr valign='top' style='cursor:pointer;' ><td style='width:50px'>
							  <img src='$pic' style='width:40px;border-radius:50%' onclick='getuserdetails(".$mid.")'></td><td onclick='getuserdetails(".$mid.")'>$name<br><span style='color:grey;font-size:14px'>DOB: $dob</span></td onclick='getuserdetails(".$mid.")'><td onclick='getuserdetails(".$mid.")'>$idno<br><span style='color:grey;font-size:14px'>UID: $uid</span></td>
							  <td onclick='getuserdetails(".$mid.")'>$groups[$grp]<br><span style='color:grey;font-size:14px'>0$fon</span></td><td onclick='getuserdetails(".$mid.")'>$kin<br><span style='color:grey;font-size:14px'>$cont</span></td><td>$loan</td></tr>";
						  }
						  
					  echo '</tbody></table>
					  </div>';
	}
	if(isset($_GET['numberpage'])){
		echo '<caption style="caption-side:top">
			<button class="btnn" style="padding:6px;font-size:15px;float:right" onclick="popupload(\'members?add\')"><i class="bi-person-plus"></i> Member</button>
				<select style="width:125px;padding:5px;font-size:15px" onchange="fetchpage(\'members?fetch=\'+this.value)">'.$opts.'</select>
					<input type="search" onkeyup="searchperson(this.value)" style="width:120px;margin-left:20px;" placeholder="Search member.."></caption><tbody id="mbs">';
				$npage=$_GET['numberpage'];$total=$_GET['total'];$ppg=$_GET['ppage'];
				$start=$npage*$ppg;
				echo '<tr><td colspan="6" style="background:#f0f0f0;padding:0px;"><div class="row no-gutters" style="justify-content:end;padding:0px;"><select onchange="getclist(this.value,'.$total.','.$ppg.')" style="margin-right:15px;">';
				for($i=0;$i<$total;$i++){
					$cl=($i==$npage)?"selected":'';
				echo '<option '.$cl.' value="'.$i.'">'.($i+1).'</option>';
			}
				echo '</select></div></td></tr><tr style="background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria">
				<td colspan="2">Member</td><td>Idno/UID</td>
			<td>Group/Phone</td><td>Next of Kin</td><td>Loan</td></tr>';
		$sql = mysqli_query($con,"SELECT *FROM `members` WHERE `status`=1 ORDER BY `name` ASC LIMIT $start,$ppg");
		 $total=mysqli_num_rows($sql);
			foreach($sql as $row){
				$mid=$row['id']; $name=ucwords(prepare($row['name'])); $uid=$row['uid']; $fon=$row['phone']; $idno=$row['idno']; $pic=strlen($row['photo'])>1?'data:image/jpg;base64,'.$row['photo']:'assets/img/user.png';
				$kinname=[];$kincont=[];
				if(strlen($row['nextkin'])>5){
				foreach(json_decode(@$row['nextkin'],1) as $kin=>$kindet){
					$kcont=explode(",",str_replace(']','',str_replace('[','',$kindet)));
					array_push($kinname,$kin);array_push($kincont,$kcont[0]);
				}}
				$kin=strlen(@$kinname[0])>2?ucwords(@$kinname[0]):"";
				$cont=strlen(@$kincont[0])>5?'0'.ltrim(ltrim(clean(@$kincont[0]),"0"),"254"):''; $grp=$row['mgroup'];$dob=$row['dob'];
				$loan = "<button class='btnn' style='padding:4px;min-width:60px' onclick=\"popupload('members?assign=$mid')\"><i class='bi-plus-lg'></i> Assign</button>";
					
				$qri = mysqli_query($con,"SELECT *FROM `loans` WHERE `client`='$mid'");
				$tloan=0;$tpaid=0;
				foreach($qri as $qris){
					$paid=$qris['paid']; $amnt=$qris['history'];
					if(($qris['product']<1) && ($paid<$amnt)){
						$loan=$loant = "<button class='btnn' style='padding:4px;min-width:60px' onclick=\"popupload('members?assign=$mid')\"><i class='bi-plus-lg'></i> Assign</button>";
					}else{
				$tpaid=$tpaid+$qris['paid'];$tloan=$tloan+$amnt=$qris['history'];
				$loan ="KES ".number_format($tloan)."<br><span style='color:grey;font-size:14px'>Paid: ".number_format($tpaid)."</span>";}
				$loan=$tpaid==$tloan?$loan= "<button class='btnn' style='padding:4px;min-width:60px' onclick=\"popupload('members?assign=$mid')\"><i class='bi-plus-lg'></i> Assign</button>":$loan;
			}
				echo "<tr valign='top' style='cursor:pointer;' onclick='getuserdetails(".$mid.")'><td style='width:50px'><img src='$pic' style='width:40px;border-radius:50%'></td><td>$name<br>
				<span style='color:grey;font-size:14px'>DOB: $dob</span></td><td>$idno<br><span style='color:grey;font-size:14px'>UID: $uid</span></td>
				<td>$groups[$grp]<br><span style='color:grey;font-size:14px'>0$fon</span></td><td>$kin<br><span style='color:grey;font-size:14px'>$cont</span></td><td>$loan</td></tr>";
			}
			
		}
		if(isset($_GET['nextpagegroup'])){
			$mgrp = $_GET['group'];
			$page=trim($_GET['nextpagegroup']);
			$ppg=$_GET['ppage'];
			$count=$_GET['total'];
			$opts = "<option value='0'>-- Group --</option>";
			$qri = mysqli_query($con,"SELECT *FROM `groups` ORDER BY `name` ASC");
			while($row=mysqli_fetch_assoc($qri)){
				$grp = $row['id']; $gname=prepare(ucwords($row['name'])); $cnd = ($grp==$mgrp) ? "selected":""; $groups[$grp]=$gname;
				$opts.="<option value='$grp' $cnd>$gname</option>";
			}
			echo '<div class="table-responsive" style="min-width:400px">
			<table style="width:100%;min-width:600px" class="table-striped mtbl"><tbody id="mbs"><caption style="caption-side:top">
			<button class="btnn" style="padding:6px;font-size:15px;float:right" onclick="popupload(\'members?add\')"><i class="bi-person-plus"></i> Member</button>
				<select style="width:125px;padding:5px;font-size:15px" onchange="fetchpage(\'members?fetch=\'+this.value)">'.$opts.'</select></caption><tbody id="mbs">';
				$start=$page*$ppg;
				if($count>1){
					echo '<tr><td colspan="6" style="padding:0px;background:white;"><div class="row no-gutters" style="justify-content:end;padding:0px;">';
				for($i=0;$i<$count;$i++){
					$cl=($i==$page)?"background:#4682b2;color:white;":'';
				echo '<div style="height:30px;width:25px;background:#f0f0f0;border:1px solid;margin:0 5px;text-align:center;cursor:pointer;padding:0px;'.$cl.'" onclick="getmoreclist('.$i.','.$count.','.$ppg.','.$mgrp.')">'.($i+1).'</div>';}echo '</div></td></tr>';}
				echo "<tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria'><td colspan='2'>Member</td><td>Idno/UID</td>
				<td>Group/Phone</td><td>Next of Kin</td><td>Loan</td></tr>";
				
				$sql = mysqli_query($con,"SELECT *FROM `members` WHERE `status`=1 AND `mgroup`='$mgrp' ORDER BY `name` ASC LIMIT $start,$ppg"); $total=mysqli_num_rows($sql);
				foreach($sql as $row){
					$mid=$row['id']; $name=ucwords(prepare($row['name'])); $uid=$row['uid']; $fon=$row['phone']; $idno=$row['idno']; $pic=strlen($row['photo'])>1?'data:image/jpg;base64,'.$row['photo']:'assets/img/user.png';
					$kinname=[];$kincont=[];
					if(strlen($row['nextkin'])>2){
					foreach(json_decode(@$row['nextkin'],1) as $kin=>$kindet){
						$kcont=explode(",",str_replace(']','',str_replace('[','',$kindet)));
						array_push($kinname,$kin);array_push($kincont,$kcont[0]);
					}}
					$kin=strlen(@$kinname[0])>2?ucwords(@$kinname[0]):"";
					$cont=strlen(@$kincont[0])>5?'0'.ltrim(ltrim(clean(@$kincont[0]),"0"),"254"):''; $grp=$row['mgroup'];$dob=$row['dob'];
					$loan = "<button class='btnn' style='padding:4px;min-width:60px' onclick=\"popupload('members?assign=$mid')\"><i class='bi-plus-lg'></i> Assign</button>";
						
					$qri = mysqli_query($con,"SELECT *FROM `loans` WHERE `client`='$mid'");
					$tloan=0;$tpaid=0;
						foreach($qri as $qris){
							$paid=$qris['paid']; $amnt=$qris['history'];
							if(($qris['product']<1) && ($paid<$amnt)){
								$loan=$loant = "<button class='btnn' style='padding:4px;min-width:60px' onclick=\"popupload('members?assign=$mid')\"><i class='bi-plus-lg'></i> Assign</button>";
							}else{
						$tpaid=$tpaid+$qris['paid'];$tloan=$tloan+$amnt=$qris['history'];
						$loan="KES ".number_format($tloan)."<br><span style='color:grey;font-size:14px'>Paid: ".number_format($tpaid)."</span>";}
						}
					echo "<tr valign='top' style='cursor:pointer;' ><td style='width:50px'><img src='$pic' style='width:40px;border-radius:50%' onclick='getuserdetails(".$mid.")'></td><td onclick='getuserdetails(".$mid.")'>$name<br><span style='color:grey;font-size:14px'>DOB: $dob</span></td onclick='getuserdetails(".$mid.")'><td onclick='getuserdetails(".$mid.")'>$idno<br><span style='color:grey;font-size:14px'>UID: $uid</span></td>
					<td onclick='getuserdetails(".$mid.")'>$groups[$grp]<br><span style='color:grey;font-size:14px'>0$fon</span></td><td onclick='getuserdetails(".$mid.")'>$kin<br><span style='color:grey;font-size:14px'>$cont</span></td><td>$loan</td></tr>";
				}
			echo "</tbody></table></div>";
		}
	mysqli_close($con);
?>

<script>

	function saveproduct(e){
		e.preventDefault();
		if(confirm("Add loan product?")){
			let data=$("#pform").serialize();
			$.ajax({
				method:"post",url:"products",data:data,
				beforeSend:function(){ progress("Processing...please wait"); },
				complete:function(){progress();}
			}).fail(function(){
				toast("Failed: Check internet Connection");
			}).done(function(res){
				if(res.trim().split(":")[0]=="success"){
					fetchpage("products?fetch"); closepop(); toast("Added successfully!");
				}
				else{ alert(res); }
			});
		}
	}
	function addsave(e){e.preventDefault();
		let data=$("#addsave").serialize();
		$.ajax({method:"POST",url:"savemember",data:data,
			beforeSend:()=>{progress("Processing request")},
		complete:()=>{progress()}}).fail(()=>{toast("Failed to process request!")}).done((e)=>{
				console.log(e.trim())
			if(e.trim()=="success"){
				closepop();toast("Product update success");
			}
			else{
				toast("Product update failed");closepop()
			}
		})
	}
	function addnewloan(e){
		e.preventDefault();
		let data=$("#addloan").serialize();
		$.ajax({method:"POST",url:"savemember",data:data,beforeSend:()=>{progress("Processing request")},
		complete:()=>{progress()}}).fail(()=>{toast("Failed to process request!")}).done((e)=>{
			console.log(e.trim())
			if(e.trim()=="success"){
				closepop();toast("Product update success");
			}
			else{
				toast("Product update failed");closepop();
			}
		})
	}
	function charges(e){
		e.preventDefault();
		let data=$("#charges").serialize();
		console.log(data);
		$.ajax({method:"POST",url:"savemember",data:data,beforeSend:()=>{progress("Processing request")},
		complete:()=>{progress()}}).fail(()=>{toast("Failed to process request!")}).done((e)=>{
			console.log(e.trim())
			if(e.trim()=="success"){
				closepop();toast("Charges were addes successfully!");
			}else if(e.trim()=="found"){
				toast("The charges are already in the system");closepop();
			}
			else{
				toast("Failed to add charges!");closepop();
			}
		})
	}
	
</script>
