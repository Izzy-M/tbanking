<?php
	session_start();
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
		$group=mysqli_query($con,"SELECT `mgroup` FROM `members` WHERE `id`='$mid'");
		foreach($group as $grpid){
			$groupid=$grpid['mgroup'];
		}
		echo "<div style='max-width:320px;margin:0 auto;'>
			<h3 style='text-align:center;color:#191970;font-size:23px;margin:0px'>Assign Loan</h3><br>
			<form method='post' id='lform' onsubmit=\"saveloan(event)\">
				<input type='hidden' name='mid' value='$mid'> 
				<p>Loan product<br><select name='prod' style='width:100%;max-width:300px;' onchange=\"popupload('members?assign=$mid&pid='+this.value)\">$opts</select></p>
				<p>Amount<br><input type='number' name='lamnt' style='width:100%;max-width:300px;' value='$amnt' required></p>
				<p>Loan gurantor <br><select style='width:100%;max-width:300px;' name='gurantor' required>";
				$allmembers=mysqli_query($con,"SELECT `id`,`name` FROM `members` WHERE `mgroup`='$groupid' AND NOT IN($mid)");
				if(mysqli_num_rows($allmembers)>0){
					foreach($allmembers as $member){echo '<option value="'.$member['id'].'">'.ucwords($member['name']).'</option>';}
					
				}else{
					echo '<option value="0">No Available member</option>';
				}
				echo"</select></p>
				<p>Date taken<br><input type='date' name='dt' max=".date('Y-m-d',time())."' value=".date('Y-m-d',time())." style='width:100%;max-width:300px;' required></p>
				<p>Repayment Period<br><input type='number' name='payterms' style='width:100%;max-width:300px;' required></p><br>
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
				<p>Member Number<br><input type='text' style='width:100%' name='memberno' required></p>
				<p><div class='row'><div class='col-6'>Date of Birth <br> <input type='date' style='width:100%;max-width:140px;' name='dob' max='".date('Y-m-d')."' required></div>
				<div class='col-5'>Member Group<br><select style='width:100%;max-width:120px;' name='grup'>$opts</select></div></div></p>
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
	#view members
	if(isset($_GET['fetch'])){
		$mgrp = trim($_GET['fetch']);
		$cond = ($mgrp>0) ? " WHERE `mgroup`='$mgrp' AND `status`=1":'WHERE `status`=1';
		$opts = "<option value='0'>-- Group --</option>";
		$qri = mysqli_query($con,"SELECT *FROM `groups` ORDER BY `name` ASC");
		while($row=mysqli_fetch_assoc($qri)){
			$grp = $row['id']; $gname=prepare(ucwords($row['name'])); $cnd = ($grp==$mgrp) ? "selected":""; $groups[$grp]=$gname;
			$opts.="<option value='$grp' $cnd>$gname</option>";
		}
		$search=($mgrp>0)?'<input type="search" onkeyup="searchperson(this.value)" style="width:120px;margin-left:20px;" placeholder="Search member..">':'';
		echo "<h3 style='font-size:23px;'>Members</h3>
		<div class='table-responsive' style='min-width:400px'>
		<table style='width:100%;min-width:600px' class='table-striped mtbl'><tbody id='mbs'>
			<caption style='caption-side:top'>
				<button class='btnn' style='padding:6px;font-size:15px;float:right' onclick=\"popupload('members?add')\"><i class='bi-person-plus'></i> Member</button>
				<select style='width:120px;padding:5px;font-size:15px' onchange=\"fetchpage('members?fetchm='this.value)\">$opts</select> $search
			</caption>";
			$allmembers=mysqli_query($con,"SELECT * FROM `members` $cond");
			$getall=mysqli_num_rows($allmembers);
			$perpage=20;
			if($getall>20){
			$quatoas=ceil($getall/$perpage);
			if($quatoas>1){
				echo '<tr><td colspan="6" style="padding:0px;background:white;"><div class="row no-gutters" style="justify-content:end;padding:0px;"><select onchange="getclist(this.value,'.$quatoas.','.$perpage.','.$mgrp.')" style="margin-right:10px;">';
			for($i=0;$i<$quatoas;$i++){
				$cl=($i==0)?"selected":'';
			echo '<option '.$cl.'" value="'.$i.'" >'.($i+1).'</option>';}echo '</select></td></tr>';
				}
			}
			echo "<tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria'><td colspan='2'>Member</td><td>Idno/UID</td>
			<td>Group/Phone</td><td>Next of Kin</td><td>Loan</td></tr>";
			
			$sql = mysqli_query($con,"SELECT *FROM `members` $cond ORDER BY `name` ASC LIMIT $perpage"); $total=mysqli_num_rows($sql);
			foreach($sql as $row){
				$mid=$row['id']; $name=ucwords(prepare($row['name'])); $uid=$row['uid']; $fon=$row['phone']; $idno=$row['idno'];$pic=strlen($row['photo'])>1?'data:image/jpg;base64,'.$row['photo']:'assets/img/user.png';
				$kinname=[];$kincont=[];
				if(strlen($row['nextkin'])>2){
				foreach(json_decode(@$row['nextkin'],1) as $kin=>$kindet){
					$kcont=explode(",",str_replace(']','',str_replace('[','',$kindet)));
					array_push($kinname,$kin);array_push($kincont,$kcont[0]);
				}}
				$kin=strlen(@$kinname[0])>2?ucwords(@$kinname[0]):"None";
				$cont=strlen(@$kincont[0])>5?'0'.ltrim(ltrim(clean(@$kincont[0]),"0"),"254"):''; $grp=$row['mgroup'];$dob=strlen($row['dob'])>6?$row['dob']:'--/--/--';
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
		echo "<h3 style='font-size:23px;'>Member Loans</h3>
		<table style='width:100%;min-width:500px' class='table-striped mtbl'>
			<tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria'><td colspan='2'>Member</td><td>Loan</td>
			<td>Product</td><td>Repayment</td><td>Status</td></tr>";
			$sql = mysqli_query($con,"SELECT `ln`.*,`mb`.`name`,`mb`.`photo`,`mb`.`phone` FROM `loans` AS `ln` INNER JOIN `members` AS `mb` ON `ln`.`client`=`mb`.`id` WHERE `ln`.`paid`<`ln`.`history` ORDER BY `ln`.`time` DESC"); $trs="";
			if(mysqli_num_rows($sql)>0){
		foreach($sql as $row){
			$lid=$row['loan']; $name=ucwords(prepare($row['name'])); 
			$prid=$row['product'];
			if($prid>0){
			$prodnams=mysqli_query($con,"SELECT `name` FROM `products` WHERE `id`='$prid'");
				foreach($prodnams AS $prad){
					$prod=$prad['name'];
				}
		}
			else{
				$prod="Financial loan";
			}
			$fon=$row['phone'];  $amnt=number_format($row['amount']); $dur=$row['period']; $bal=number_format($row['amount']-$row['paid']);
		
			$pic=strlen($row['photo'])>1?'data:image/jpg;base64,'.$row['photo']:'assets/img/user.png';
			$day=date("d-m-Y",$row['time']); $paid=number_format($row['paid']);
			
			echo "<tr valign='top'><td style='width:50px'><img src='$pic' style='width:40px;border-radius:50%'></td><td>$name<br>
			<span style='color:grey;font-size:14px'>0$fon</span></td><td>KES $amnt<br><span style='color:grey;font-size:14px'>$day</span></td>
			<td>$prod<br><span style='color:grey;font-size:14px'>For $dur Months</span></td><td>KES $paid<br>
			<span style='color:grey;font-size:14px'>Bal: $bal</span></td><td>Running</td></tr>";
		}
		
		}
		else{
			echo '<tr><td colspan="6" style="text-align:center;">No current loans</td></tr>';
		}

		echo "</table>";
	}

if(isset($_GET['addposition'])){
	echo "<div style='max-width:320px;margin:0 auto;'>
	<h3 style='text-align:center;color:#191970;font-size:23px;margin:0px;text-align:center;'>Add Group Position</h3><br>
	<form method='post' id='addpos'>
	
		<p>Add position<br>
		<input type='text' name='pos' style='width:100%;max-width:300px;' required></p>
		
		<p style='text-align:right;width:100%;'><button class='btnn'>Add</button></p><br>
	</form><br>
</div>";
}

if(isset($_GET['uploadmembers'])){
	echo '<div class="col-6 mx-auto" style="max-width:300px">
	<div class="row no-gutters" style="text-align:center;justify-content:center;"><h4>Add Members</h4></div>
	<form id="uploadm" method="post" enctype="multipart/form-data" onsubmit="uploadm(event)">
	<p>Select group<br><select name="sgroup" id="sgroup" style="max-width:300px;width:100%;">';
		$allgroups=mysqli_query($con,"SELECT * FROM `groups` WHERE `status`=1");
		if(mysqli_num_rows($allgroups)>0){
			foreach($allgroups as $group){
			echo '<option value='.$group['id'].'>'.ucwords($group['name']).'</option>';
			}
		}else{
			echo "<option>No groups</option>";
		}
	echo '</select></p>
	<p>Select Default position<br><select name="pos" id="pos" style="max-width:300px;width:100%;">';
		$allpos=mysqli_query($con,"SELECT * FROM `grouppositions`");
		if(mysqli_num_rows($allpos)>0){
			foreach($allpos as $pos){
			echo '<option value='.$pos['id'].'>'.ucwords($pos['position']).'</option>';
			}
		}else{
			echo "<option>No groups</option>";
		}
	echo '</select></p>
	<p>File to upload<br>
	<span style="font-size:10px;">NB:The arrangement of cells should be Member number, Name, System number, Phone No,ID,Residence</span></br>
	<input type="file" id="membersf" name="filemembers" accept=".csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" style="border:none;" required></p>
	<p>
	<div class="row no-gutters" style="width:100%;max-width:100%;justify-content:end;">
		<button class="btnn"onclick="uploadm(event)">Upload file</button>
	</div></p><br>
	</form>
	</div>';
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
					fetchpage("members?fetch"); closepop(); toast("Assigned successfully!");dashbd();
				}
				else{ alert(res); }
			});
		}
	}
	function getuserdetails(id){popupload("savemember?userdetails="+id);}
	function savemember(e){
		e.preventDefault();
		var img = document.getElementById("pic").files[0];
		if(confirm("Continue to save member details?")){
			var formdata=new FormData(document.getElementById("mform"));
			let t=$("#mform").serializeArray();
			let res=[];
			let details='{"';
			
			let total=0;
			t.forEach((element,index)=>{
				if(element.name=="kname"){
					total=total+parseInt(t[index+2].value);
					details=details+element.value+'":"['+t[(index+1)].value+","+t[index+2].value+']",'}
			})
			details=details.replace(/,\s*$/, "")
			details=details+"}"
			if(total<=100){
			var xhr=new XMLHttpRequest();
			xhr.upload.addEventListener("progress",profprogress,false);
			xhr.addEventListener("load",profdone,false);
			xhr.addEventListener("error",proferror,false);
			xhr.addEventListener("abort",profabort,false);
			formdata.append("photo",img);
			formdata.append("allkin",details);
			xhr.onload=function(){
				if(this.responseText.trim()=="success"){
					toast("Saved successfull"); fetchpage("members?fetch"); closepop();
				}
				else{ alert(this.responseText); }
			}
			xhr.open("post","savemember",true);
			xhr.send(formdata);
		}else{
				alert("Share Percentage is out of range!");
			}
			
		}
	}
	
	function profprogress(event){
		var percent=(event.loaded / event.total) * 100;
		progress("Uploading "+Math.round(percent)+"%");
		if(percent==100){
			progress("Cropping...please wait");
		}
	}	
	function profdone(event){progress();}

	function proferror(event){toast("Upload failed"); progress();
	}
	function searchperson(item){
			if(item.length>3){
				let data="sqterm="+item;
				$.ajax({method:"POST",url:"savemember",data:data}).fail(()=>{toast("")}).done((e)=>{
					$("#mbs").html(e)
				})
			}else{
				$("#mbs").load("savemember?allmembers");
			}
		}
	function profabort(event){toast("Upload aborted"); progress();}
	function refreshemployees(){
		fetchpage('employee?fetch');
	}
	$("#addpos").on("submit",(e)=>{
		e.preventDefault();
		let data=$("#addpos").serialize();
		$.ajax({method:"POST",url:"savemember",data:data,
			beforeSend:()=>{progress("Adding position...");},
			complete:()=>{progress();}
			}).fail(()=>{closepop();toast("Error:Internet Connection error!");}).done(
			(e)=>{
			if(e.trim()=="success"){
			_("addpos").reset();
			closepop();toast("Position added successfully");
			}else if(e.trim()=="found"){
					toast("The position is already Added");
				}
				else{
					toast("Failed to add position");
				}
			})
	})
	function addkin(){
		$("#ttle").text("Add Next Of Kin")
		$("#kin").append("<p>Next of Kin name<br><input type='text' name='kname' style='width:100%' required></p><p>Next of Kin Contact<br><input type='number' name='kcont' style='width:100%' required></p><p>Share percentage<br><input type='number' name='share' style='width:100%' required></p>")
	}
	function uploadm(e){
		e.preventDefault();
		let data=new FormData(document.getElementById("uploadm"))
		let request= new XMLHttpRequest();
		request.upload.addEventListener("progress",()=>{progress("Uploading file...")})
		request.addEventListener("loadstart",()=>{progress("Uploading file..")})
		request.addEventListener("load",()=>{"Uploading loading..."});
		request.addEventListener("loadend",()=>{progress()})
		request.addEventListener("error",()=>{toast("Connection Error:Connection error occured!")})
		request.addEventListener("abort",()=>{progress("Operation terminated")})
		data.append('pos',document.getElementById('pos').value)
		data.append("excelfile",document.getElementById("membersf").files[0]);
		data.append("sgroup",document.getElementById("sgroup").value)
		request.onload=function(){
				if(this.responseText.trim()=="success"){
					toast("All members in list Were added"); fetchpage("settings?settings"); closepop();
				}
				else{ toast("All members were not added. Check Your list for issues");fetchpage("settings?settings"); closepop();}
			}
			request.open("POST","managesettings",true);
			request.send(data)	
	}
	
</script>