<?php
	session_start();
	$currentgrp=isset($_SESSION['grptable'])?$_SESSION['grptable']:'';
	require "functions.php";
	$con = mysqli_connect("localhost",DB_USER,DB_PASS,DB_NAME);
	function message($name,$email,$password,$pos){
		$dt='<div class="card" style="min-height:300px;width:100%;max-width:800px;background:#f0f0f0;margin:auto;padding-top:5px">
		<div style="width:96%;margin:0px auto;height:40px;text-align:center;border-radius:15px;"><h4 style="font-family:google-sans,Roboto,\'Open Sans\',Helvetica,Arial;font-size:18px;margin-top:5px;">Account Registration Details</h4></div>
		<div class="card" style="width:96%;background:;font-family:Roboto,\'Open Sans\',font-size:12px;min-height:200px;margin:0px auto;padding:5px;">
		<div class="card-body" style="padding:10px;background:white;"><p>Hello <span style="font-size:12px;font-weight:600">'.ucwords($name).'</span>,</p>
		<p>As Golden Vision '.$pos.', you are receiving this email with your account credentials to access the roles that will be assigned to.</p> <p style="text-align:center;">User Email:'.$email.'<br>Password:'.$password.'</p><p>You can access the system throught this link <a href="dev.goldenvision.or.ke">dev.goldenvision.or.ke</a>. In case you face any problems accessing your account, write  the sysetm adminstrator an email for help.Regards</p>
		<p>Isaiah Morara<br>
		System Adminstrator<br/>
		Email:<a href="mailto:admin@goldenvision.or.ke">admin@goldenvision.or.ke</a><br>
		Tel:+254797698098<br></p>
		</div></div></div>';
		return $dt;
	}

#add products
	if(isset($_POST['pname'])){
		$name = strtolower(clean($_POST['pname']));
		$amnt = clean($_POST['lamnt']);
		$dur = clean($_POST['loanable']);
		
		$chk = mysqli_query($con,"SELECT * FROM `products` WHERE `name`='$name'");
		if(mysqli_num_rows($chk)){ echo "Failed: Product ".prepare($name)." already exists"; }
		else{
			if(mysqli_query($con,"INSERT INTO `products` VALUES(NULL,'$name','$amnt','0','$dur')")){
				echo "success";
			}
			else{ echo "Failed to complete the request! Try again later"; }
		}
	}
#save group
	if(isset($_POST['gname'])){
		$name = strtolower(clean($_POST['gname']));
		$loc=clean($_POST['loc']);
		$accun=clean($_POST['acnt']);
		$grcode=clean($_POST['gcode']);
		$mail=clean($_POST['mail']);
		$branch=clean($_POST['branch']);
		$address=clean($_POST['address']);
		$contact=clean($_POST['contact']);
		
		$chk1 = mysqli_query($con,"SELECT *FROM `groups` WHERE `name`='$name'");
		$chk2 = mysqli_query($con,"SELECT *FROM `groups` WHERE `bankaccount`='$accun'");
		$chk3=mysqli_query($con,"SELECT * FROM `groups` WHERE `gid`='$grcode'");
		if(mysqli_num_rows($chk3)>0){ echo "Failed: Group group code ".$grcode." already exists"; }
		else if(mysqli_num_rows($chk1)){ echo "Failed: Group ".prepare($name)." already exists"; }
		elseif(mysqli_num_rows($chk2)){ echo "Failed: Group with A/C $accn already exists"; }
		else{
			if(mysqli_query($con,"INSERT INTO `groups`(`id`,`name`,`gid`,`bankaccount`,`bankbranch`,`contact`,`location`,`address`,`email`,`status`) VALUES(NULL,'$name','$grcode','$accun','$branch','$contact','$loc','$address','$mail',1)")){
				echo "success";
			}
			else{
				echo "Failed to complete the request! Try again later"; }
		}
	}
#add save type
	if(isset($_POST['sname'])){
			$sname=clean($_POST['sname']);
			$int=clean($_POST['int']);
			$select=mysqli_query($con,"SELECT `code` FROM `deposittypes` ORDER BY `id` DESC LIMIT 1");
			foreach($select as $sel){
				$seld=$sel['code']+1;
			}
			$insert=mysqli_query($con,"INSERT INTO `deposittypes`(`id`,`type`,`code`,`interest`) VALUES(NULL,'$sname','$seld','$int')");
			if($insert){echo 'success';}else{
				echo "fail".mysqli_error($con);
			}
		}
#add loan type
	if(isset($_POST['lname'])){
		$sname=clean($_POST['lname']);
		$int=clean($_POST['intr']);
		$period=clean($_POST['period']);
		$overduerate=clean($_POST['rperiod']);
		$select=mysqli_query($con,"SELECT `code` FROM `loantype` ORDER BY `id` DESC LIMIT 1");
		foreach($select as $sel){
			$seld=$sel['code']+1;
		}
		$insert=mysqli_query($con,"INSERT INTO `loantype`(`id`,`name`,`code`,`interest`,`rate`,`overduerate`) VALUES(NULL,'$sname','$seld','$int','$period','$overduerate')");
		if($insert){echo 'success';}else{
			echo "fail".mysqli_error($con).$sname.','.$seld.','.$int.','.$period.','.$overduerate;
			}
		}
	# save loan
	if(isset($_POST['prod'])){
		$prd = trim($_POST['prod']);
		$mid = trim($_POST['mid']);
		$amnt = clean($_POST['lamnt']);
		$pterms = clean($_POST['payterms']);
		$gur= trim($_POST['gurantor']);
		$lid = rand(10000000,999999999);
		$time=strtotime($_POST['dt']);
		$ctime=time();
		$loan='product';
		$getid=mysqli_query($con,"SELECT `id` FROM `loantype` WHERE `name` LIKE '%$loan%'");
		foreach($getid as $idd){
			$id=$idd['id'];
		}
		/*$interest=mysqli_query($con,"SELECT `interest`,`rate` FROM `loantype` WHERE `id`='$lntype'");
		foreach($interest as $int){
			if($int['rate']=="monthly"){
				$ipay=($int['interest']/100)*$period;
			}
			else{
					$ipay=($int['interest']/100)*$period/12;
				}
			}*/
		$deadline=strtotime(date('Y-m-d',strtotime(strtotime($time). '+'.$pterms. 'months')));
			
			if(mysqli_query($con,"INSERT INTO `loans`(`id`,`client`,`product`,`amount`,`history`,`period`,`loan`,`time`,`loantype`,`gurantor`,`deadline`) VALUES(NULL,'$mid','$prd','$amnt','$amnt','$pterms','$lid','$ctime','$id','$gur','$deadline')")){
				echo "success";
			}
			else{ echo "Failed to complete the request!"; }
		
	}
	
# save member
	if(isset($_POST['allkin'])){
		$cname = clean(strtolower($_POST['name']));
		$kinsdata =strtolower($_POST['allkin']);
		$fon = ltrim(ltrim(clean($_POST['fon']),"0"),"254");
		$dob = clean($_POST['dob']);
		$mno=clean($_POST['memberno']);
		$idno = clean($_POST['idno']);
		$grp = clean($_POST['grup']);
		$res=clean($_POST['residence']);
		$cont = ltrim(ltrim(clean($_POST['kcont']),"0"),"254");
		$tmp = $_FILES['photo']['tmp_name'];
		
		$ext = @strtolower(array_pop(explode(".",strtolower($_FILES['photo']['name']))));
		$get = array("png","jpg","jpeg","gif");
		
		$chk1 = mysqli_query($con,"SELECT *FROM `members` WHERE `phone`='$fon'");
		$chk2 = mysqli_query($con,"SELECT *FROM `members` WHERE `idno`='$idno'");
		$chk3=mysqli_query($con,"SELECT `id` FROM `members` ORDER BY `id` DESC LIMIT 1");
		foreach($chk3 as $chk){
			$ncode=$chk['id']+1;
		}
		
		if(!is_numeric($fon)){ echo "Phone number must be numeric!"; }
		elseif(strlen($fon)!=9){ echo "Invalid phone number!"; }
		elseif(!is_numeric($cont)){ echo "Next of Kin contact must be numeric!"; }
		elseif(strlen($cont)!=9){ echo "Invalid next of Kin contact!"; }
		elseif(!$grp){ echo "Failed: No group selected"; }
		elseif(!@getimagesize($tmp)){ echo "Invalid Image! Please select a valid photo"; }
		elseif(!in_array($ext,$get)){ echo "Failed: Image extension $ext is not supported"; }
		elseif(mysqli_num_rows($chk1)){ echo "Failed: Phone number 0$fon is already in the system"; }
		elseif(mysqli_num_rows($chk2)){ echo "Failed: Id number $idno is already in use"; }
		else{
			$img = "Prof_".date("Ymd_His").".$ext"; 
			$mgrp = $grp; $uid=gentrans(); $tym=time();
				
			if(move_uploaded_file($tmp,$img)){
				if(cropSQ($img,200,$img)){
					$pic = base64_encode(file_get_contents($img));
					if(mysqli_query($con,"INSERT INTO `members`(`id`,`uid`,`name`,`phone`,`idno`,`photo`,`dob`,`mgroup`,`time`,`nextkin`,`sysnum`,`residence`) VALUES(NULL,'$uid','$cname','$fon','$idno','$pic','$dob','$mgrp','$tym','$kinsdata','$mno','$res')")){
						unlink($img);
						echo "success";
					}
					else{echo mysqli_error($con);echo "Failed to save member! Try again later"; unlink($img); }
				}
				else{ echo "Failed to save photo"; }
			}
			else{ echo "Failed: Unknown Error occured"; }
		}
	}
#give loan
	if(isset($_POST['client'])){
		$client=$_POST['client'];
		$amount=clean($_POST['amount']);
		$lngrp=$_POST['loangrp'];
		$lntype=$_POST['loantype'];
		$gr=$_POST['gur'];
		$fee=clean($_POST['fee']);
		$collat=clean($_POST['collat']);
		$period=clean($_POST['repayplan']);
		$hst=$_POST['date'];
		$ctime=time();
		$loan=rand(10000000,99999999);
		$interest=mysqli_query($con,"SELECT `interest`,`rate` FROM `loantype` WHERE `id`='$lntype'");
		foreach($interest as $int){
			if($int['rate']=="monthly"){
				$ipay=($int['interest']/100)*$period;
			}
			else{
					$ipay=($int['interest']/100)*$period/12;
				}
			}
		$cgb=mysqli_query($con,"SELECT `cpool` FROM `groups` WHERE `id`='$lngrp'");
		foreach($cgb as $cb){$cpool=$cb['cpool'];}
		if($cpool>=$amount){
			$deadline=strtotime(date('Y-m-d',strtotime('+'.$period. 'months')));
			$lintrest=$ipay*$amount;
			$nm=$amount+$fee+$lintrest;
			$insert=mysqli_query($con,"INSERT INTO `loans`(`id`,`client`,`loan`,`time`,`amount`,`intrest`,`period`,`collateral`,`loantype`,`appfee`,`history`,`gurantor`,`deadline`) VALUES(NULL,'$client','$loan','$ctime','$amount','$lintrest','$period','$collat','$lntype','$fee','$nm','$gr','$deadline')");
			$applicationfee=mysqli_query($con,"INSERT INTO `applicationfee`(`id`,`client`,`loan`,`fee`) VALUES(NULL,'$client','$loan','$fee')");
			$res=$cpool-$amount;
			if($insert){
					echo "success";
					$up=mysqli_query($con,"UPDATE `groups` SET `cpool`='$res' WHERE `id`='$lngrp'");
				}
				else{
					echo "fail";
				}
			}
		else{
			echo "low";
		}
	}
#saving to member account
	if(isset($_POST['investor'])){
		$client=$_POST['investor'];
		$other=$_POST['other'];
		$group=$_POST['ingroup'];
		$time=time();
		$month=strtotime(date('M-Y'));
		$all= "{".$other."}";
		foreach(json_decode($all,1) as $key=>$value){
		if($value>0){
		$gss=mysqli_query($con,"SELECT `savings`,`cpool` FROM `groups` WHERE `id`='$group'");
		foreach($gss as $gs){
			$princ=$gs['savings'];$pol=$gs['cpool'];
		}
		$lprin=$princ+$value;$lpol=$pol+$value;
	
			$insert=mysqli_query($con,"INSERT INTO `savings`(`id`,`client`,`saving`,`time`,`month`,`type`) VALUES(NULL,'$client','$value','$time','$month','$key')");
			$upp=mysqli_query($con,"UPDATE `groups` SET `savings`='$lprin',`cpool`='$lpol' WHERE `id`='$group'");
			if(!$insert && !$upp){
				$res='fail';}else{$res="success";}
			}
			if($res!=="success"){
				break;
			}
		}
		if($res==="success"){
			echo "success";
		}
		else{
			echo "fail";
		}
		}
	
#pay loan
	if(isset($_POST['payee'])){
		$payee=$_POST['payee'];
		$grp=$_POST['lngr'];
		$amount=$_POST['amount'];
		$cloan=$_POST['cloan'];
		$time=time();
		$totals=mysqli_query($con,"SELECT `cpool` FROM `groups` WHERE `id`='$grp'");
		foreach($totals as $total){
			$cpool=$total['cpool'];
		}
		$select=mysqli_query($con,"SELECT `id`,`paid`,`history`,`loan` FROM `loans` WHERE `client`='$payee' AND `id`='$cloan' FOR UPDATE");
			foreach($select as $loan){
				$bl=$loan['history']-$loan['paid'];
				$final=$amount-$bl;
				$paid=$amount+$loan['paid'];
				$loanid=$loan['loan'];
				$cpool=$cpool+$amount;}
				$appfees=mysqli_query($con,"SELECT * FROM `applicationfee` WHERE `loan`='$loanid' AND `client`='$payee' FOR UPDATE");
				foreach($appfees as $appfee){$sumpaid=0;

					foreach(json_decode($appfee['history'],1) as $key=>$value){$sumpaid=$sumpaid+$value;};

					if($sumpaid<$appfee['fee']){
						$appbal=$appfee['fee']-$sumpaid;
						mysqli_query($con,"UPDATE `applicationfee` SET `history`=JSON_SET(`history`,'$.$time','$appbal') WHERE `loan`='$loanid' AND `client`='$payee'");
					}
				}
				 if($amount<=$bl){
					$update=mysqli_query($con,"UPDATE `loans` SET `paid`='$paid',`phistory`=JSON_INSERT(`phistory`,'$.$time','$amount') WHERE `client`='$payee' AND `id`='$cloan'");
					$gupdate=mysqli_query($con,"UPDATE `groups` SET `cpool`='$cpool' WHERE `id`='$grp'");
					if($update && $gupdate){
						echo "success";
					}
					else{
						echo "fail";
						}
				 }
				else{
					echo 'fail';
				}
			
	}
#group fines
	if(isset($_POST['groupid'])){
		$grp=$_POST['groupid'];
		$client=$_POST['userid'];
		$amount=clean($_POST['amount']);
		$charges=$_POST['charges'];
		$time=time();
		$insert=mysqli_query($con,"INSERT INTO `fines`(`id`,`client`,`mgroup`,`charges`,`amount`,`date`) VALUES(NULL,'$client','$grp','$charges','$amount','$time')");
		if($insert){echo 'success';}else{
			echo 'fail';
		}
	}
#update user position
	if(isset($_POST['cuserid'])){
		$sid=$_POST['cuserid'];
		$pos=$_POST['cpos'];
		$update=mysqli_query($con,"UPDATE `members` SET `pos`='$pos' WHERE `id`='$sid'");
		if($update){
			echo 'success';
		}
		else{
			echo 'fail';
		}
	}
#update group name
	if(isset($_POST['cgroup'])){
		$grp=clean($_POST['cgroup']);
		$name=clean($_POST['grpname']);
		$acnt=clean($_POST['grpac']);
		$grpcode=clean($_POST['grpcode']);
		$grploc=clean($_POST['grploc']);
		$mail=clean($_POST['mail']);
		$branch=clean($_POST['branch']);
		$address=clean($_POST['address']);
		$contact=clean($_POST['contact']);
		$qr=mysqli_query($con,"UPDATE `groups` SET `name`='$name',`bankaccount`='$acnt',`location`='$grploc',`gid`='$grpcode',`email`='$mail',`address`='$address',`bankbranch`='$branch',`contact`='$contact' WHERE `id`='$grp'");
		if($qr){
			echo 'success';
		}else{
			echo mysqli_error($con);
			echo 'fail';
		}
	}
#Add new charges	
	if(isset($_POST['cname'])){
		$cnm=clean($_POST['cname']);
		$amount=clean($_POST['camount']);
		$col=clean($_POST['collection']);
		$type=clean($_POST['ctype']);
		$check=mysqli_query($con,"SELECT * FROM `chargetypes` WHERE `name`='$cnm'");
		if(mysqli_num_rows($check)>0){
			echo"found";
		}else{
			$insert=mysqli_query($con,"INSERT INTO `chargetypes`(`id`,`name`,`amount`,`fixed`,`collection`)VALUES(NULL,'$cnm','$amount','$type','$col')");
			if($insert){
				echo 'success';
			}else{
				echo 'fail';
			}
		}
	}
#get external debt
	if(isset($_POST['dtgroup'])){
		$amount=$_POST['damount'];
		$grp=$_POST['tgrp'];
		$period=$_POST['period'];
		$dgrp=$_POST['dtgroup'];
		$time=time();
		$dbt='"['.$amount.','.$grp.','.$dgrp.']"';
		$update=mysqli_query($con,"UPDATE `groups` SET `debts`=JSON_INSERT(`debt`,'$.$time','$dbt') WHERE `id`=''$grp");
		if($update){
			echo 'success';
		}else{
		
			echo 'fail';
		}
	}
#withdraw funds
	if(isset($_POST['groupaccount'])){
		$account=$_POST['groupaccount'];
		$amount=clean($_POST['wamaount']);
		$member=$_POST['memberid'];
		$investment=$_POST['investment'];
		$time=time();
		$month=strtotime(date("Y_M",time()));
		$select=mysqli_query($con,"SELECT `cpool`,`savings` FROM `groups` WHERE `id`='$account' FOR UPDATE");
		foreach($select as $accsavings){
			$cpool=$accsavings['cpool'];$savingamount=$accsavings['savings'];
			if(($savingamount>=$amount) AND ($cpool>=$amount)){
			
				$balances= mysqli_query($con,"SELECT `saving`,`id` FROM `savings` WHERE `client`='$member' AND `id`='$investment' FOR UPDATE");
				foreach($balances as $balance){
					$save=$balance['saving'];
					$savid=$balance['id'];
				}
				if($save>=$amount){
					$bal=$save-$amount;
					$groupsaving=$savingamount-$amount;$grouppool=$cpool-$amount;
				$updatesavings=mysqli_query($con,"UPDATE `savings` SET `saving`='$bal' WHERE `id`='$investment'");
				if($updatesavings){
					$updategroupbal= mysqli_query($con,"UPDATE `groups` SET `cpool`='$grouppool',`savings`='$groupsaving' WHERE `id`='$account'");
					if($updategroupbal){
						$withdrawal=mysqli_query($con,"INSERT INTO `account_withdraws`(`id`,`groupid`,`client`,`amount`,`time`,`savingid`,`month`) VALUES(NULL,'$account','$member','$amount','$time','$savid','$month')");
						if($withdrawal){
							echo 'success';
						}else{
							mysqli_query($con,"UPDATE `savings` SET `saving`='$save' WHERE `client`='$member'");
							mysqli_query($con,"UPDATE `groups` SET `savings`='$savingamount',`cpool`='$cpool' WHERE `id`='$account'");
							echo 'fail';
						}
					}
					else{
						mysqli_query($con,"UPDATE `groups` SET `savings`='$savingamount',`cpool`='$cpool' WHERE `id`='$account'");
						echo "fail";
					}

				}else{
					echo 'fail';
				}}else{
					echo 'insufficient';
				}
			}else{
				echo 'insufficient';
			}
		}
		
	}
#delete member
	if(isset($_POST['delmember'])){
		$del=$_POST['delmember'];
		$delete=mysqli_query($con,"UPDATE `members` SET `status`=4 WHERE `id`='$del'");
		if($delete){
			echo "success";
		}else{
			
			echo "fail";
		}
	}
#delete  group
	if(isset($_POST['deletegroup'])){
		$group=$_POST['deletegroup'];
		$delegroup=mysqli_query($con,"UPDATE `groups` SET `status`=4 WHERE `id`='$group'");
		if($delegroup){
			$deletemember=mysqli_query($con,"UPDATE `members` SET `status`=4 WHERE `id`='$group'");
			if($deletemember){
				echo 'success';
			}else{
				echo mysqli_error($con);
				echo 'fail';
			}
		}
	}
#add position
	if(isset($_POST['pos'])){
		$pos=clean($_POST['pos']);
		$get=mysqli_query($con,"SELECT * FROM `grouppositions` WHERE `position` LIKE '%$pos%'");
		if(mysqli_num_rows($get)>0){echo "found";}else{
		$insert=mysqli_query($con,"INSERT INTO `grouppositions` VALUES(NULL,'$pos')");
		if($insert){
			echo "success";
		}else{
			echo 'fail';
		}
		}
	}
#update member detaild
	if(isset($_POST['updatee'])){
		$member=$_POST['updatee'];
		$username=clean($_POST['username']);
		$idnum=clean($_POST['idnum']);
		$phone=clean($_POST['phone']);
		$pos-clean($_POST['grouppos']);
		$res=clean($_POST['residence']);
		$kin=rtrim($_POST['kin'],",");
		$nxt='{"'.$kin.'}';
		$update=mysqli_query($con,"UPDATE `members` SET `name`='$username',`idno`='$idnum',`phone`='$phone',`nextkin`='$nxt',`residence`='$res', `pos`='$pos' WHERE id='$member'");
		if($update){
			echo "success";
		}else{
			echo "fail";
		}
	}
#external loan
	if(isset($_POST['tgrp'])){
		$grp=$_POST['tgrp'];
		$lender=$_POST['lenderid'];
		$amount=$_POST['damount'];
		$period=$_POST['period'];
		$time=time();
		$checks=mysqli_query($con,"SELECT `cpool` FROM `groups` WHERE `id`='$grp' FOR UPDATE");
		foreach($checks as $check){
			$currentamount=$check['cpool'];
				$newamount=$currentamount+$amount;
				$update=mysqli_query($con,"UPDATE `groups` SET `cpool`='$newamount' WHERE `id`='$grp'");
				if($update){
					$insert=mysqli_query($con,"INSERT INTO `debts`(`id`,`borrower`,`lender`,`amount`,`period`,`time`) VALUES(NULL,'$grp','$lender','$amount','$period','$time')");
					if($insert){
						echo 'success';
					}
					else{
						echo 'fail'.mysqli_error($con);
					}
				}else{
					echo 'fail'.mysqli_error($con);
				}
			}
	}
#record expenses
	if(isset($_POST['expense'])){
		$group=$_POST['expgroup'];
		$exp=$_POST['expense'];
		$amount=$_POST['damount'];
		$method=$_POST['payment'];
		$receiver=$_POST['receiver'];
		$time=time();
		$insert=mysqli_query($con,"INSERT INTO `expenses`(`id`,`groupid`,`expense`,`paymethod`,`receiver`,`paid`,`time`) VALUES(NULL,'$group','$exp','$method','$receiver','$amount','$time')");
		if($insert){echo 'success';}else{echo 'fail';}}
#office expense
	if(isset($_POST['offexpense'])){
		$group=$_POST['offpgroup'];
		$exp=$_POST['offexpense'];
		$amount=$_POST['offamount'];
		$time=time();
		$insert=mysqli_query($con,"INSERT INTO `officeexpense`(`id`,`grp`,`amount`,`payment`,`time`) VALUES(NULL,'$group','$amount','$exp','$time')");
		if($insert){echo 'success';}else{echo 'fail';echo mysqli_error($con);}}
#change deposit
	if(isset($_POST['gedit'])){
		$id=$_POST['gedit'];
		$amount=$_POST['eamount'];
		$type=$_POST['depotype'];
		
		$query=mysqli_query($con,"UPDATE `savings` SET `saving`='$amount',`type`='$type' WHERE `id`='$id'");
		if($query){
			echo "success";
		}else{
			echo "fail";
		}
	}
#delete deposit type	
	if(isset($_POST['deldepo'])){
		$id=$_POST['deldepo'];
		$query=mysqli_query($con,"DELETE FROM `savings` WHERE `id`='$id'");
		if($query){
			echo "success";
		}
		else{
			echo mysqli_error($con);echo "fail";
		}
	}
#get depotypes
	if(isset($_GET['depotypes'])){
		$select=mysqli_query($con,"SELECT * FROM `deposittypes`");
		if(mysqli_num_rows($select)>0){
			
			foreach($select as $rm){
			echo'<option value="'.$rm['id'].'">'.$rm['type'].'</option>';
			}
		}
	}
#search member from 
	if(isset($_POST['sqterm'])){
		$term=clean($_POST['sqterm']);
		$getall=mysqli_query($con,"SELECT * FROM `members` WHERE `name` LIKE '%$term%' OR `idno` LIKE'%$term%' OR `phone` LIKE '%$term%' OR `sysnum` LIKE '%$term%'");
		if(mysqli_num_rows($getall)>0){
		foreach($getall as $row){
			$mid=$row['id']; $name=ucwords(prepare($row['name'])); $uid=$row['uid']; $fon=$row['phone']; $idno=$row['idno']; $pic=strlen($row['photo'])>1?'data:image/jpg;base64,'.$row['photo']:'assets/img/user.png';
			$kinname=[];$kincont=[];
			if(strlen($row['nextkin'])>5){
				foreach(json_decode($row['nextkin'],1) as $kin=>$kindet){
					$kcont=explode(",",str_replace(']','',str_replace('[','',$kindet)));
					array_push($kinname,$kin);array_push($kincont,$kcont[0]);
				}
			}
			$kin=strlen(@$kinname[0])>2?ucwords(@$kinname[0]):"";
			$cont=strlen(@$kincont[0])>5?'0'.ltrim(ltrim(clean(@$kincont[0]),"0"),"254"):''; $grp=$row['mgroup'];$dob=$row['dob'];
			$loan = "<button class='btnn' style='padding:4px;min-width:60px' onclick=\"popupload('members?assign=$mid')\"><i class='bi-plus-lg'></i> Assign</button>";
				
			$qri = mysqli_query($con,"SELECT *FROM `loans` WHERE `client`='$mid'");
			$tloan=0;$tpaid=0;
				foreach($qri as $qris){
					$paid=$qris['paid']; $amnt=$qris['history'];
					if(($qris['product']<0) && ($paid<$amnt)){
						$loan=$loant = "<button class='btnn' style='padding:4px;min-width:60px' onclick=\"popupload('members?assign=$mid')\"><i class='bi-plus-lg'></i> Assign</button>";
					}
				$tpaid=$tpaid+$qris['paid'];$tloan=$tloan+$amnt=$qris['history'];
				$loan ="KES ".number_format($tloan)."<br><span style='color:grey;font-size:14px'>Paid: ".number_format($tpaid)."</span>";
			}
			$gid=$row['mgroup'];
			$groupn=mysqli_query($con,"SELECT `name` FROM `groups` WHERE `id`='$gid'");
			foreach($groupn as $cgrp){
				$grpname=ucwords($cgrp['name']);
			}
			
			echo "<tr valign='top' onclick='getuserdetails(".$mid.")' style='cursor:pointer;'><td style='width:50px'><img src='$pic' style='width:40px;border-radius:50%'></td><td>$name<br>
			<span style='color:grey;font-size:14px'>DOB: $dob</span></td><td>$idno<br><span style='color:grey;font-size:14px'>UID: $uid</span></td>
			<td>$grpname<br><span style='color:grey;font-size:14px'>0$fon</span></td><td>$kin<br><span style='color:grey;font-size:14px'>$cont</span></td><td>$loan</td></tr>";
			}
		}
		else{
			echo '<tr><td colspan="6" style="text-align:center;">Sorry, There is no a member matching the key provided</td></tr>';
		}
	}
#get all members
	if(isset($_GET['allmembers'])){
		$sql = mysqli_query($con,"SELECT *FROM `members` WHERE `status`=1 ORDER BY `name` ASC");
		foreach($sql as $row){
		$mid=$row['id']; $name=ucwords(prepare($row['name'])); $uid=$row['uid']; $fon=$row['phone']; $idno=$row['idno']; $pic=strlen($row['photo'])>1?'data:image/jpg;base64,'.$row['photo']:'assets/img/user.png';
			$kinname=[];$kincont=[];
			if(strlen($row['nextkin'])>5){
			foreach(json_decode($row['nextkin'],1) as $kin=>$kindet){
				$kcont=explode(",",str_replace(']','',str_replace('[','',$kindet)));
				array_push($kinname,$kin);array_push($kincont,$kcont[0]);
				}
			}
			$kin=strlen(@$kinname[0])>2?ucwords(@$kinname[0]):"";
			$cont=strlen(@$kincont[0])>5?'0'.ltrim(ltrim(clean(@$kincont[0]),"0"),"254"):''; $grp=$row['mgroup'];$dob=$row['dob'];
			$loan = "<button class='btnn' style='padding:4px;min-width:60px' onclick=\"popupload('members?assign=$mid')\"><i class='bi-plus-lg'></i> Assign</button>";
				
			$qri = mysqli_query($con,"SELECT *FROM `loans` WHERE `client`='$mid'");
			$tloan=0;$tpaid=0;
				foreach($qri as $qris){
					$paid=$qris['paid']; $amnt=$qris['history'];
					if(($qris['product']<0) && ($paid<$amt)){
						$loan=$loant = "<button class='btnn' style='padding:4px;min-width:60px' onclick=\"popupload('members?assign=$mid')\"><i class='bi-plus-lg'></i> Assign</button>";
					}
				$tpaid=$tpaid+$qris['paid'];$tloan=$tloan+$amnt=$qris['history'];
				$loan ="KES ".number_format($tloan)."<br><span style='color:grey;font-size:14px'>Paid: ".number_format($tpaid)."</span>";
			}
			$gid=$row['mgroup'];
			$groupn=mysqli_query($con,"SELECT `name` FROM `groups` WHERE `id`='$gid'");
			foreach($groupn as $cgrp){
				$grpname=ucwords($cgrp['name']);
			}
			echo "<tr valign='top' onclick='getuserdetails(".$mid.")' style='cursor:pointer;'><td style='width:50px'><img src='$pic' style='width:40px;border-radius:50%'></td><td>$name<br>
			<span style='color:grey;font-size:14px'>DOB: $dob</span></td><td>$idno<br><span style='color:grey;font-size:14px'>UID: $uid</span></td>
			<td>$grpname<br><span style='color:grey;font-size:14px'>0$fon</span></td><td>$kin<br><span style='color:grey;font-size:14px'>$cont</span></td><td>$loan</td></tr>";
			}
	}
#fetch user details;
	if(isset($_GET['userdetails'])){
		$user=$_GET['userdetails'];
		echo '<div class="col-8 mx-auto" style="max-width:300px;justify-content:center;border:none; font-family:cambria;">
		<table class="table-striped" style="width:100%;font-size:13px;">
		<div class="row no-gutters" style="text-align:center;font-size:18px;"><h5>User Information</h5></div>';
		$members=mysqli_query($con,"SELECT * FROM `members` WHERE `id`='$user'");
			foreach($members as $m){
			echo '<tr style="background:#f2f6fc;color:#191970;font-weight:bold;font-size:14px;font-family:cambria"><td colspan="2" >Member Details</td></tr>
			<p><div tr><td style="font-weight:500;">Member name</td>
			</td><td>'.ucwords($m['name']).'</td></tr>
			<tr><td style="font-weight:500;">ID Number</td>
			</td><td>'.$m['idno'].'</td></tr>
			<tr><td style="font-weight:500;">Contact</td>
			</td><td>0'.$m['phone'].'</td></tr>
			<tr><td style="font-weight:500;">Member No</td>
			</td><td>'.$m['sysnum'].'</td></tr><tr><td style="font-weight:500;">DOB</td>
			</td><td>'.$m['dob'].'</td></tr></p>';
				if(strlen($m['nextkin'])>2){
					echo '<tr style="background:#f2f6fc;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;margin-top:5px;"><td colspan="2">Next of Kin details</td></tr>';
					foreach(json_decode($m['nextkin'],1) as $kin=>$knv){
						$vls=explode(",",str_replace("[","",str_replace("]","",$knv)));
						$vl=(int)$vls[1]>0?@$vls[1]:0;
						echo '<tr><td>Name</td><td>'.ucwords($kin).'</div></div>
						<tr><td>Contact</td><td>'.$vls[0].'</div></div>
						<tr><td>Percentage</td><td>'.$vl.'%</td></tr>';
						}
				}
				echo '</table><br></div>';
				}
	}
#risk collection
	/*if(isset($_POST['riskamt'])){
		$risk=clean($_POST['riskamt']);
		$member=$_POST['member'];
		$time=time();
		$month=strtotime(date('M-Y'));
		$insert=mysqli_query($con,"INSERT INTO `riskfund`(`id`,`client`,`time`,`month`,`amount`) VALUES(NULL,'$member','$time','$month','$risk')");
		if($insert){
			echo "success";
		}
		else{
			echo "fail";echo mysqli_error($con);
		}
	}*/
#ssesion groups
	if(isset($_POST['setgroup'])){
		$table=$_POST['setgroup'];
		$_SESSION['grptable']=$table;
		if(isset($_SESSION['grptable']) && $_SESSION['grptable']==$table){
			echo "success";
		}else{echo "fail";}
		}
#employ login
	if(isset($_POST['currentuser'])){
		$user=clean($_POST['currentuser']);
		$pass=clean($_POST['password']);
		$select=mysqli_query($con,"SELECT `id`,`config` FROM `employeetb` WHERE `email`='$user'");
		if(mysqli_num_rows($select)>0){
			foreach($select as $sel){
				$hash=$sel['config'];
			}
			if(password_verify($pass,$hash)){

				echo "success";
				$_SESSION['csysuser']=$sel['id'];
			}else{
				echo $hash;
				echo "fail";
			}
		}else{
			echo "not found";
		}
	}
#add new employee
	if(isset($_POST['employname'])){
		$employee=clean($_POST['employname']);
		$phone=clean($_POST['phone']);
		$mail=clean($_POST['mail']);
		$position=$_POST['position'];
		$check= mysqli_query($con,"SELECT `name`,`email` FROM `employeetb` WHERE `email`='$mail'");
		if(mysqli_num_rows($check)<1){
			$select =mysqli_query($con,"SELECT * FROM `employeetb`");
			$count=mysqli_num_rows($select)+1;
			$employpos=mysqli_query($con,"SELECT `name` FROM `employee_positions` WHERE `id`='$position'");
			foreach($employpos as $emppos){
				$pos=$emppos['name'];
			}
			$symbol=array("@","&","#","!","&","*","%");
			$sy=$symbol[rand(0,6)];
			$psd=$pos.$sy.$count;
			$pass=password_hash($psd,PASSWORD_DEFAULT);
			$insert=mysqli_query($con,"INSERT INTO `employeetb`(`id`,`name`,`phone`,`email`,`position`,`config`,`status`) VALUES(NULL,'$employee','$phone','$mail','$position','$pass',1)");
			if($insert){
				$alt='Hello,'.$employee.'Goldev vision Account. Click here to login';
				sendPassMail($mail,$employee,message($employee,$mail,$psd,$pos),"Employee Account Creation",$alt);
				echo  "success";
			}else{
				mysqli_error($con);
				echo 'fail';
			}
				}
			else{
				echo "found";
		}
	}
#pay external debt
	if(isset($_POST['dbtid'])){
		$currentgroup=$_POST['currentgroup'];
		$debt=$_POST['dbtid'];
		$amountpaid=clean($_POST['dbtpay']);
		$time=time();
		$checks=mysqli_query($con,"SELECT * FROM `debts` WHERE `id`='$debt' FOR UPDATE");
		$groupaccount=mysqli_query($con,"SELECT `cpool` FROM `groups` WHERE `id`='$currentgroup' FOR UPDATE");
		foreach($groupaccount AS $account){$currentamount=$account['cpool'];}
		if(mysqli_num_rows($checks)===1){
			foreach($checks as $check){
			$adebt=$check['amount'];$paid=$check['paid'];
			}
			if($amountpaid<=$adebt){
				$tpaid=$paid+$amountpaid;
				$finalbalance=$currentamount+$amountpaid;
				$info='"'.$time.'":"'.$amountpaid.'"';
				$update=mysqli_query($con,"UPDATE `debts` SET `paid`='$tpaid',`payments`=JSON_INSERT(`payments`,'$.$time','$amountpaid') WHERE `id`='$debt'");
				$updategroup=mysqli_query($con,"UPDATE `groups` SET `cpool`='$finalbalance' WHERE `id`='$currentgroup'");
				if($update AND $updategroup){
					echo 'success';
				}else{
					mysqli_query($con,"UPDATE `groups` SET `cpool`='$currentamount' WHERE `id`='$currentgroup'");
					mysqli_query($con,"UPDATE `debts` SET `paid`='$paid',`payments`=JSON_REMOVE(`payments`,'$.$time')");
					echo 'fail';
				}
			}
			else{
				echo 'more';
			}

		}
		else{
			echo 'no debt';
		}
	}
#alldeposit API
	if(isset($_GET['alldepos'])){
		$alldepos=mysqli_query($con,"SELECT * FROM `deposittypes`");
		if(mysqli_num_rows($alldepos)>0){
			foreach($alldepos as $depo){
				echo '<option value='.$depo['id'].'>'.ucwords($depo['type']).'</option>';
			}
		}else{
			echo '<option>No Deposit types</option>';
		}
	}
#All loans API
	if(isset($_GET['getallloans'])){
		$cli=$_GET['getallloans'];
		$all=mysqli_query($con,"SELECT `ln`.`id`,`tp`.`name`,`ln`.`paid`,`ln`.`history` FROM `loans` as `ln` INNER JOIN `loantype` as `tp`  ON `ln`.`loantype`=`tp`.`id` WHERE `ln`.`paid`<`ln`.`history` AND `ln`.`client`=$cli");
		foreach($all as $ln){ 
			$res=$ln['history']-$ln['paid'];
			echo '<option value='.$ln['id'].'>'.$ln['name'].' Loan balance '.$res.'</option>';
		}

	}
#Daily Activity module
	if(isset($_POST['acmember'])){
		$member=$_POST['acmember'];
		$loans=$_POST['loans'];
		$deposits=$_POST['deposits'];
		$risk=$_POST['risk'];
		$date=$_POST['recdate'];
		$now=time();
		$depos=json_decode($deposits,1);
		$loan=json_decode($loans,1);
		$savs=0;
		$month=strtotime(date('Y-M'));
		$tdepo=0;
		$savs=$currentgroupsavs=0;
		$donedeposit=$doneloan=true;
		$banking=mysqli_query($con,"SELECT `gr`.`savings`,`gr`.`cpool` FROM `groups` As `gr` INNER JOIN `members` As `mb` ON `mb`.`mgroup`=`gr`.`id` FOR UPDATE");
		foreach($banking as $bank){
			$cpool=$bank['cpool'];$ttsaving=$bank['savings'];
		}

		foreach($depos as $dep=>$amt){
		
			if($amt>0){
				$insert=mysqli_query($con,"INSERT INTO `savings` VALUES (NULL,'$member','$amt','$dep','{}','$now','$month')");
				if(!$insert){
					$donedeposit=false;
				}
				else{
					$tdepo=$tdepo+$amt;
					$donedeposit=true;
				}
			}
		}
		$finalbal=0;
		if($donedeposit){
			
			$floans=$bals=$update=[];
		 	foreach($loan as $loid=>$bal){
				 $getloans=mysqli_query($con,"SELECT * FROM `loans` WHERE `id`='$loid' AND `client`='$member' FOR UPDATE");
		 		 foreach($getloans as $getloan){
		 		 	if($bal>0 && ($getloan['history']-$getloan['paid'])>=0){
		 		 		$ballc=$getloan['paid']+$bal;
						$vv='"'.$loid.'":"'.$ballc.'"';
						$bbl='"'.$loid.'":"'.$getloan['paid'].'"';
		 	 		 	array_push($update,$vv);
						array_push($floans,$bbl);
		 				$newup=mysqli_query($con,"UPDATE `loans` SET `paid`='$ballc',`phistory`=JSON_INSERT(`phistory`,'$.$now','$bal') WHERE `id`='$loid' AND `client`='$member'");
						if($newup){
							$finalbal=$finalbal+$bal;
							$doneloan=true;
						}
						else{
							$doneloan=false;
							mysqli_query($con,"DELETE `savings` WHERE `client`='$member' AND `time`='$now'");
							for($i=0;$i<count($update);$i++){
								$datadet=explode(':',$floans[$i]);
								$loanid=str_replace('"','',$datadet[0]);$currentbalance=str_replace('"','',$datadet[1]);
								mysqli_query($con,"UPDATE `loans` SET `paid`='$currentbalance', `phistpry'=JSON_REMOVE(`phistory`,'$.$now') WHERE `id`='$loanid'");
							}
							break;
						}
					}
		 		}
				
			}
			if($doneloan){
				if($risk>0){
				$risk=mysqli_query($con,"INSERT INTO `riskfund` VALUES(NULL,'$risk','$member','$now','$month')");
				if($risk){
					$newsav=$tdepo+$ttsaving;
					$newp=$tdepo+$finalbal+$cpool;
					mysqli_query($con,"UPDATE `groups` SET `savings`='$newsav',`cpool`='$newp' WHERE `id`='$currentgrp'");
					echo 'success';
				}
				else{
					mysqli_query($con,"DELETE `savings` WHERE `client`='$member' AND `time`='$now'");
							for($i=0;$i<$balances;$i++){
								$pid=$ballances[$i][0];$id=$balances[$i][1];
								mysqli_query($con,"UPDATE `loans` SET `paid`='$pid',`phistory`=`JSON_REMOVE(`phistory`,'$.$now') WHERE `id`='$id'");
							}
							mysqli_query($con,"DELETE `savings` WHERE `client`='$member' AND `time`='$now'");
							echo 'Sorry! You request can not be compeleted at the moment!';
						}
			}else{
					$newsav=$tdepo+$ttsaving;
					$newp=$tdepo+$finalbal+$cpool;
					mysqli_query($con,"UPDATE `groups` SET `savings`='$newsav',`cpool`='$newp' WHERE `id`='$currentgrp'");
					echo 'success';
				}
			}
			else{
			echo 'Sorry! You request can not be compeleted at the momen!';	
			}
		}
		else{
			mysqli_query($con,"DELETE `savings` WHERE `client`='$member' AND `time`='$now'");
		echo 'Sorry! You request can not be compeleted at the moment!';
		}	
	}
#Add new lender type
	if(isset($_POST['lendername'])){
		$name=clean(strtolower($_POST['lendername']));
		$type=clean($_POST['lendertype']);
		$find=mysqli_query($con,"SELECT * FROM `lenders` WHERE `name`='$name'");
		if(mysqli_num_rows($find)<1){
			$insert= mysqli_query($con,"INSERT INTO `lenders` VALUES(NULL,'$name','$type')");
			if($insert){
				echo 'success';
			}
			else{
				echo 'fail'.mysqli_error($con);
			}
		}
		else{
			echo 'found';
		}
	}
#get external loan categories
if(isset($_GET['category'])){
	$cattype=$_GET['category'];
	$getcats=mysqli_query($con,"SELECT `id`,`name` FROM `lenders` WHERE `category`='$cattype'");
	if(mysqli_num_rows($getcats)>0){
		foreach($getcats AS $gtcat){
			echo '<option value="'.$gtcat['id'].'">'.ucwords($gtcat['name']).'</option>';
		}
	}
	else{
		echo '<option>--No lender type --</option>';
	}
}
#search groups
if(isset($_GET['searchgroup'])){
	$groupnames=strtolower(clean($_GET['searchgroup']));
	$sql=mysqli_query($con,"SELECT * FROM `groups` WHERE `name` LIKE '%$groupnames%' ORDER BY `id`");
	if(mysqli_num_rows($sql)>0){
		$no=0;
		foreach($sql as $row){
			$rid=$row['id']; $name=prepare(ucwords($row['name'])); $gid=$row['gid']; $no++;
			$chk = mysqli_query($con,"SELECT *FROM `members` WHERE `status`=1 AND `mgroup`='$rid'"); $sum=mysqli_num_rows($chk);
			if($row['status']==1){
			echo "<tr style='cursor:pointer;height:30px;'><td onclick='opengroup($rid)'>$no</td><td onclick='opengroup($rid)'>$name</td><td onclick='opengroup($rid)'>$gid</td><td onclick='opengroup($rid)' >$sum</td><td style='cursor:pointer;color:green;' onclick='editgroup($rid)'>Active</td></tr>";}
			else if($row['status']==4){
				$data.="<tr style='cursor:not-allowed;height:30px;'><td>$no</td><td>$name</td><td>$gid</td><td>$sum</td><td style='color:red;'>Deleted</td></tr>";
			}
			else if($row['status']==3){
				echo"<tr style='cursor:not-allowed;height:30px;'><td>$no</td><td>$name</td><td>$gid</td><td>$sum</td><td style='color:#f8c753;'>Suspended</td></tr>";
			}
		}
	}
	else{
		echo '<tr><td colspan="5" style="text-align:center;">Sorry, No match was found!</td></tr>';
	}
}
#default group search fallback
if(isset($_GET['allgroups'])){
	$sql = mysqli_query($con,"SELECT * FROM `groups` ORDER BY `gid` ASC LIMIT 20");
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
}
mysqli_close($con);
?>