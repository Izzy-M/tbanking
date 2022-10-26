<?php
require 'functions.php';
require 'comp/vendor/autoload.php';
$con=mysqli_connect(HOST,DB_USER,DB_PASS,DB_NAME);
//addpodition
if(isset($_POST['emppos'])){
    $position=clean($_POST['emppos']);
    $select=mysqli_query($con,"SELECT * FROM `employee_positions` WHERE `name`='$position'");
    if(mysqli_num_rows($select)>0){
        echo 'found';
    }
    else{
        $insert=mysqli_query($con,"INSERT INTO `employee_positions`(`id`,`name`) VALUES(NULL,'$position')");
        if($insert){
            echo "success";
        }else{
            echo 'fail';
        }
    }
}
//all employees
if(isset($_GET['employees'])){
    echo '<div class="col-8 mx-auto" style="width:100%;max-width:600px;"><div class="row no-gutters" style="justify-content:center;"><h4>Delete Employee<h4></div><table class="table-striped" style="width:100%;">
    <tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Employee name</td><td>Position</td><td>Delete</td></tr>';
    $employees=mysqli_query($con,"SELECT * FROM `employeetb` WHERE `status`=1");
    if(mysqli_num_rows($employees)>0){
        foreach($employees as $employee){
            $pos1=$employee['position'];
            $getpos=mysqli_query($con,"SELECT `name` FROM `employee_positions` WHERE `id`='$pos1'");
            foreach($getpos AS $pos){
                $position=$pos['name'];
            }
        echo '<tr><td style="padding-left:5px;">'.$employee['name'].'</td><td>'.$position.'</td><td style="cursor:pointer;" onclick="deleteemployee('.$employee['id'].')"><i class="bi bi-trash" style="color:red;font-size:20px;"></i></td></tr>';
        }
    }else{
        echo '<tr colspan=3>No Employ has benn added in the system</tr>';
    }
    
    
    echo '</table></div>';
}
//all loans
if(isset($_GET['allloans'])){
    echo '<div class="col-11 mx-auto" style="max-width:1240px;">
    <div class="row no-gutters" style="justify-content:center;text-align:center"><h4 style="">All Loans List</h4></div>
    <div class="col-12 table-responsive" style="width:100%;">

    <table class="table-striped" style="width:100%;">
    <tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:40px;"><td>Loan Name</td><td>Loan Interest</td><td>Loan Rate</td><td>Overdue Rate</td><td>Delete</td></tr>';
    $loantypes=mysqli_query($con,"SELECT * FROM `loantype` WHERE `status`=1");
    if(mysqli_num_rows($loantypes)>0){
        foreach($loantypes as $loantype){
            $rate=(strpos($loantype['overduerate'],'%')!==false)?$loantype['overduerate']:'Ksh '.$loantype['overduerate'];
        echo '<tr><td>'.ucwords($loantype['name']).'</td><td>'.$loantype['interest'].'%</td><td>'.$loantype['rate'].'</td><td>'.$rate.'</td><td style="cursor:pointer;color:red;" onclick="loandelete('.$loantype['id'].')"><i class="bi bi-trash" style="color:red;font-size:20px;"></i> &nbsp Delete</td></tr>';}
     }
    echo '</table></div></div></div>';}

//getloans to edit
if(isset($_GET['getloans'])){
        echo '<div class="col-11 mx-auto" style="max-width:1240px;">
        <div class="row no-gutters" style="justify-content:center;text-align:center"><h4 style="">Active Loans List</h4></div>
        <div class="col-12 table-responsive" style="width:100%;">
        <p><table class="table-striped" style="width:100%;">
        <tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Loan Name</td><td>Loan Interest</td><td>Action</td></tr>';
    $loantypes=mysqli_query($con,"SELECT * FROM `loantype` WHERE `status`=1");
    if(mysqli_num_rows($loantypes)>0){
        foreach($loantypes as $loantype){
        echo '<tr><td>'.ucwords($loantype['name']).'</td><td>'.$loantype['interest'].'%</td><td style="cursor:pointer;" onclick="editloantype('.$loantype['id'].')"><i class="bi bi-pencil" style="font-size:20px;"></i></td></tr>'; }
    }else{
        echo '<tr></tr>';
    }
    echo '</table></p>
        </div>
        </div>    
        </div>'; 
    }

//delete loan
if(isset($_POST['deleteloan'])){
    $loanid=$_POST['deleteloan'];
    $deleteloan=mysqli_query($con,"UPDATE `loantype` SET `status`=4 WHERE `id`='$loanid'");
    if($deleteloan){
        echo 'success';
        }else{
             echo 'fail';
     }
    }
#deactivate group
if(isset($_POST['deactivategroup'])){
    $groupdeact=$_POST['deactivategroup'];
    $deleteloan=mysqli_query($con,"UPDATE `groups` SET `status`=3 WHERE `id`='$groupdeact'");
    if($deleteloan){
        echo 'success';
        }else{
             echo 'fail';
     }
}
#delete group

if(isset($_POST['deletedgroup'])){
    $delgroup=$_POST['deletedgroup'];
    $deleteloan=mysqli_query($con,"UPDATE `groups` SET `status`=4 WHERE `id`='$delgroup'");
    if($deleteloan){
        echo 'success';
        }else{
             echo 'fail';
     }
}
#delete employ
if(isset($_POST['deleteemployee'])){
    $employeeid=$_POST['deleteemployee'];
    $deleteemp=mysqli_query($con,"UPDATE `employeetb` SET `status`=4 WHERE `id`='$employeeid'");
    if($deleteemp){
        echo 'success';
    }else{
        echo 'fail';
        }
    }

//update loan type
if(isset($_POST['loantp'])){
    $loaidid=$_POST['loantp'];
    $intrest=clean($_POST['interest']);
    $rate=clean($_POST['rate']);
    $overdue=clean($_POST['overdue']);
    $name=clean($_POST['loanname']);
    $update=mysqli_query($con,"UPDATE `loantype` SET `interest`='$intrest',`rate`='$rate',`overduerate`='$overdue',`name`='$name' WHERE `id`='$loaidid'");
    if($update){
        echo "success";
    }else{
        echo 'fail'.mysqli_error($con);
    }
}
if(isset($_POST['sgroup'])){
    $group=$_POST['sgroup'];
    $group=(int)$group;
    $pos=$_POST['pos'];
    $name=$_FILES['excelfile']['name'];
    $tmp=$_FILES['excelfile']['tmp_name'];
    $error=$_FILES['excelfile']['error'];
    $allowed=array("csv","xlsx","xls");
    $ext=strtolower(array_reverse(explode(".",$name))[0]);
    if(in_array($ext,$allowed)){
        $dest='docs';
        if(!is_dir($dest)){
            mkdir($dest,0777);
        }
        move_uploaded_file($tmp,$dest.'/'.$name);
        
        $sheet=\PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($dest.'/'.$name);
        $sheet->setReadDataOnly(true);
        $reader=$sheet->load($dest.'/'.$name);
        $data=$reader->getActiveSheet()->toArray();
        
        $status='';
            foreach($data as $row){
               
                 if($row[0]>0){
                    $time=time();
                    $uid=mysqli_query($con,"SELECT `value` FROM `settings` WHERE `id`=2 FOR UPDATE");
                    foreach($uid as $val){
                        $usid=$val['value']+1;
                    }
                    $idno=$row[3];
                    $membersid=mysqli_query($con,"SELECT * FROM `members` WHERE `idno`='$idno'");
                    $name=clean($row[1]);$residence=clean($row[5]);
                     if(strlen($row[3])>6 && mysqli_num_rows($membersid)<1){
                         $update=mysqli_query($con,"UPDATE `settings` SET `value`='$usid' WHERE `id`=2");
                        $insert=mysqli_query($con,"INSERT INTO `members`(`id`,`name`,`uid`,`sysnum`,`phone`,`idno`,`residence`,`time`,`pos`,`mgroup`,`status`) VALUES(NULL,'$name','$usid','$row[2]','$row[3]','$row[4]','$residence','$time','$pos','$group',1)");
                         if(!$insert){
                             $status="fail";break;
                    
                             }
                             else{
                            $status="success";
                            }
                        }
                    }
                
                  } echo $status;
                }
                else{
                echo "Invalid file type!";
             }
    }
if(isset($_GET['deactgroup'])){
    echo "<div class='table-responsive'><div style='min-width:550px;max-width:1240px;margin:0 auto'>
			<h3 style='font-size:23px;'>Active Groups
			<table style='width:100%;font-family:cambria;font-size:14px;' class='table-striped'><tbody class='mtb1'>";
			
			$perpage=20;
			$allmembers=mysqli_query($con,"SELECT * FROM `groups` WHERE `status`=1");
			$total=mysqli_num_rows($allmembers);
			$pages=ceil($total/$perpage);
			echo "<tr style='height:35px;background:white;'><td colspan='7' style='text-align:right;'>";
			for($i=0;$i<$pages;$i++){
				$clr=$i==0?"background:#4682b4;color:white;":'';
				echo '<button style="height:30px;width:25px;background:#f0f0f0;margin-right:5px;cursor:pointer;border:1px solid;'.$clr.'" onclick="nextactivegroup('.$pages.','.$perpage.','.$i.')">'.($i+1).'</button>';
			}
			echo "</td></tr><tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;'><td>Sn</td><td>Name</td><td>Group ID</td>
				<td>Members</td><td>Location</td><td>Deactivate</td></tr>";
			$trs=""; $no=0;
				$sql = mysqli_query($con,"SELECT *FROM `groups` WHERE `status`=1 ORDER BY `gid` ASC LIMIT $perpage");
				foreach($sql as $row){
					$rid=$row['id']; $name=prepare(ucwords($row['name'])); $gid=$row['gid']; $loc=$row['location'];$no++;
					$chk = mysqli_query($con,"SELECT *FROM `members` WHERE `status`=1 AND `mgroup`='$rid'"); $sum=mysqli_num_rows($chk);
					echo "<tr style='cursor:pointer;height:30px;'><td>$no</td><td>$name</td><td>$gid</td><td >$sum</td><td style='cursor:pointer;'>".$loc."</td><td onclick='deactivategp($rid)' style='color:#ffb507;'><i class='fa fa-gavel' style='font-size:18px;'></i> Deactivate</td></tr>";
					
					
				}

			echo "</tbody></table></div></div>";

}
if(isset($_GET['nextactivegroup'])){
    $cpage=$_GET['nextactivegroup'];
    $perpage=$_GET['limit'];
    $npage=$_GET['count'];
    $start=($cpage)*$perpage;
    $trs=""; $no=0;
    
    $start=($cpage)*$perpage;
    
    echo "<tr style='height:35px;background:white;'><td colspan='8' style='text-align:right;'>";
        for($i=0;$i<$npage;$i++){
            $clr=$i==$cpage?"background:#4682b4;color:white;":'';
            echo '<button style="height:30px;width:25px;background:#f0f0f0;margin-right:5px;cursor:pointer;border:1px solid;'.$clr.'" onclick="nextactivegroup('.$npage.','.$perpage.','.$i.')">'.($i+1).'</button>';
        }
        echo "</td></tr><tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;'><td>Sn</td><td>Name</td><td>Group ID</td>
            <td>Members</td><td>Location</td><td>Deactivate<td></tr>";
    $sql = mysqli_query($con,"SELECT *FROM `groups` WHERE `status`=1 ORDER BY `gid` ASC LIMIT $start,$perpage");
    foreach($sql as $row){
        $rid=$row['id']; $name=prepare(ucwords($row['name'])); $gid=$row['gid']; $loc=$row['location'];$no++;
        $chk = mysqli_query($con,"SELECT *FROM `members` WHERE `status`=1 AND `mgroup`='$rid'"); $sum=mysqli_num_rows($chk);
        echo "<tr style='cursor:pointer;height:30px;'><td>$no</td><td>$name</td><td>$gid</td><td >$sum</td><td style='cursor:pointer;'>".$loc."</td><td onclick='deactivategp($rid)' style='color:#ffb507;'><i class='fa fa-gavel' style='font-size:18px;'></i> Deactivate</td></tr>";
        
       
    }

}
if(isset($_GET['delegroups'])){
    echo "<div class='table-responsive'><div style='min-width:550px;max-width:1240px;margin:0 auto'>
			<h3 style='font-size:23px;'>All Groups
			<table style='width:100%;font-family:cambria;font-size:14px;' class='table-striped'><tbody class='mtb1'>";
			
			$perpage=20;
			$allmembers=mysqli_query($con,"SELECT * FROM `groups` WHERE `status`<4");
			$total=mysqli_num_rows($allmembers);
			$pages=ceil($total/$perpage);
			echo "<tr style='height:35px;background:white;'><td colspan='7' style='text-align:right;'>";
			for($i=0;$i<$pages;$i++){
				$clr=$i==0?"background:#4682b4;color:white;":'';
				echo '<button style="height:30px;width:25px;background:#f0f0f0;margin-right:5px;cursor:pointer;border:1px solid;'.$clr.'" onclick="nextdelgroup('.$pages.','.$perpage.','.$i.')">'.($i+1).'</button>';
			}
			echo "</td></tr><tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;'><td>Sn</td><td>Name</td><td>Group ID</td>
				<td>Members</td><td>Location</td><td>Delete</td></tr>";
			$trs=""; $no=0;
				$sql = mysqli_query($con,"SELECT *FROM `groups` WHERE `status`< 4 ORDER BY `gid` ASC LIMIT $perpage");
				foreach($sql as $row){
					$rid=$row['id']; $name=prepare(ucwords($row['name'])); $gid=$row['gid']; $loc=$row['location'];$no++;
					$chk = mysqli_query($con,"SELECT *FROM `members` WHERE `status`=1 AND `mgroup`='$rid'"); $sum=mysqli_num_rows($chk);
					echo "<tr style='cursor:pointer;height:30px;'><td>$no</td><td>$name</td><td>$gid</td><td >$sum</td><td style='cursor:pointer;'>".$loc."</td><td onclick='grouptodeleted($rid)' style='color:red;'><i class='bi bi-trash' style='font-size:18px;'></i> Delete</td></tr>";
					
				}

			echo "</tbody></table></div></div>";
}
if(isset($_GET['nextdel'])){
    $cpage=$_GET['nextdel'];
    $perpage=$_GET['limit'];
    $npage=$_GET['count'];
    $trs=""; $no=0;
    $start=($cpage)*$perpage;
    echo "<tr style='height:35px;background:white;'><td colspan='7' style='text-align:right;'>";
    for($i=0;$i<$npage;$i++){
        $clr=$i==0?"background:#4682b4;color:white;":'';
        echo '<button style="height:30px;width:25px;background:#f0f0f0;margin-right:5px;cursor:pointer;border:1px solid;'.$clr.'" onclick="nextdelgroup('.$npage.','.$perpage.','.$i.')">'.($i+1).'</button>';
    }
    echo "</td></tr><tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;'><td>Sn</td><td>Name</td><td>Group ID</td>
        <td>Members</td><td>Location</td><td>Delete</td></tr>";
    $trs=""; $no=0;
        $sql = mysqli_query($con,"SELECT *FROM `groups` WHERE `status`< 4 ORDER BY `gid` ASC LIMIT $start,$perpage");
        foreach($sql as $row){
            $rid=$row['id']; $name=prepare(ucwords($row['name'])); $gid=$row['gid']; $loc=$row['location'];$no++;
            $chk = mysqli_query($con,"SELECT *FROM `members` WHERE `status`=1 AND `mgroup`='$rid'"); $sum=mysqli_num_rows($chk);
            echo "<tr style='cursor:pointer;height:30px;'><td>$no</td><td>$name</td><td>$gid</td><td >$sum</td><td style='cursor:pointer;'>".$loc."</td><td onclick='grouptodeleted($rid)' style='color:red;'><i class='bi bi-trash' style='font-size:18px;'></i> Delete</td></tr>";}
}
if(isset($_GET['editgroup'])){
    echo "<div class='table-responsive'><div style='min-width:550px;max-width:1240px;margin:0 auto'>
    <h3 style='font-size:23px;'>Edit Groups
    <table style='width:100%;font-family:cambria;font-size:14px;' class='table-striped'><tbody class='mtb1'>";
    
    $perpage=20;
    $allmembers=mysqli_query($con,"SELECT * FROM `groups` WHERE `status`<4");
    $total=mysqli_num_rows($allmembers);
    $pages=ceil($total/$perpage);
    echo "<tr style='height:35px;background:white;'><td colspan='7' style='text-align:right;'>";
    for($i=0;$i<$pages;$i++){
        $clr=$i==0?"background:#4682b4;color:white;":'';
        echo '<button style="height:30px;width:25px;background:#f0f0f0;margin-right:5px;cursor:pointer;border:1px solid;'.$clr.'" onclick="nexteditgroup('.$pages.','.$perpage.','.$i.')">'.($i+1).'</button>';
    }
    echo "</td></tr><tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;'><td>Sn</td><td>Name</td><td>Group ID</td>
        <td>Email</td><td>Location</td><td>Edit</td></tr>";
    $trs=""; $no=0;
        $sql = mysqli_query($con,"SELECT *FROM `groups` WHERE `status`<4 ORDER BY `gid` ASC LIMIT $perpage");
        foreach($sql as $row){
            $rid=$row['id']; $name=prepare(ucwords($row['name']));$mail=$row['email']; $gid=$row['gid']; $loc=$row['location'];$no++;
            echo "<tr style='cursor:pointer;height:30px;'><td>$no</td><td>$name</td><td>$gid</td><td >$mail</td><td style='cursor:pointer;'>".$loc."</td><td onclick='editgroup($rid)' style='color:green;'><i class='i bi-pencil' style='font-size:18px;'></i> Edit</td></tr>";
            
            
        }

    echo "</tbody></table></div></div>";

}
if(isset($_GET['nextegroup'])){
    $cpage=$_GET['nextegroup'];
    $perpage=$_GET['limit'];
    $npage=$_GET['count'];
    $trs=""; $no=0;
    $start=($cpage)*$perpage;
    echo "<tr style='height:35px;background:white;'><td colspan='7' style='text-align:right;'>";
    for($i=0;$i<$npage;$i++){
        $clr=$i==$cpage?"background:#4682b4;color:white;":'';
        echo '<button style="height:30px;width:25px;background:#f0f0f0;margin-right:5px;cursor:pointer;border:1px solid;'.$clr.'" onclick="nexteditgroup('.$npage.','.$perpage.','.$i.')">'.($i+1).'</button>';
    }
    echo "</td></tr><tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;'><td>Sn</td><td>Name</td><td>Group ID</td>
        <td>Email</td><td>Location</td><td>Edit</td></tr>";
    $trs=""; $no=0;
        $sql = mysqli_query($con,"SELECT *FROM `groups` WHERE `status`<4 ORDER BY `gid` ASC LIMIT $start,$perpage");
        foreach($sql as $row){
            $rid=$row['id']; $name=prepare(ucwords($row['name']));$mail=$row['email']; $gid=$row['gid']; $loc=$row['location'];$no++;
            echo "<tr style='cursor:pointer;height:30px;'><td>$no</td><td>$name</td><td>$gid</td><td >$mail</td><td style='cursor:pointer;'>".$loc."</td><td onclick='editgroup($rid)' style='color:green;'><i class='i bi-pencil' style='font-size:18px;'></i> Edit</td></tr>";
            
            
        }

    echo "</tbody></table></div></div>";

}

if(isset($_GET['updatepos'])){
    echo '<div class="container-fluid" style="min-height:300px;">
    <div class="row no-gutters"><h4 style="font-size:23px;">Employee List</h4></div>
    <div class="row no-gutters">
    <div class="table-responsive">
    <table class="table-striped" style="min-width:600px;width:100%;margin-top:20px;">
    <tr style="line-height:30px;background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;"><td>Employee name</td><td>Email</td><td>Position</td><td>Action</td></tr><tbody id="empbody">';
    $allemployees=mysqli_query($con,"SELECT * FROM `employeetb` WHERE `status`<4");
    foreach($allemployees as $employee){
        $pos=$employee['position'];
        $getpos=mysqli_query($con,"SELECT `name` FROM `employee_positions` WHERE `id`='$pos'");
        foreach($getpos AS $pos){
            $position=$pos['name'];
        }
    echo '<tr><td>'.ucwords($employee['name']).'</td><td>'.$employee['email'].'</td><td>'.$position.'</td><td onclick="updateposition('.$employee['id'].')" style="cursor:pointer;color:#468b24;"><i class="fa fa-user-cog" aria-hidden="true" style="font-size:18px;color:"></i> &nbsp; Update</td></tr>';}
    echo '</tbody></table></div></div>';

}
if(isset($_POST['cmember'])){
        $cmember=$_POST['cmember'];
        $pos=$_POST['nextpos'];
        $name=clean($_POST['name']);
        $phone=clean($_POST['phone']);
        $mail=clean($_POST['mail']);
        $insert=mysqli_query($con,"UPDATE `employeetb` SET `position`='$pos',`email`='$mail',`phone`='$phone',`name`='$name' WHERE `id`='$cmember'");
        if($insert){echo "success";}else{
            echo "fail";
        }
}
if(isset($_POST['cpos'])){
    $cpos=$_POST['cpos'];
    $roles=$_POST['perms'];
    $update=mysqli_query($con,"UPDATE `employee_positions` SET `roles`='$roles' WHERE `id`='$cpos'");
    if($update){
        echo "success";
    }else{echo "fail";}
}
if(isset($_POST['chargesname'])){
    $name=clean($_POST['chargesname']);
    $amount=clean($_POST['chargesamount']);
    $type=$_POST['chargestype'];
    $check=mysqli_query($con,"SELECT `name` FROM `chargestype` WHERE `name` ='$name'");
    if(mysqli_num_rows($check)>0){
        echo "Sorry, the loan type is already in the system";
    }
    else{
        $insert=mysqli_query($con,"INSERT INTO `chargetypes`(`id`,`name`,`fixed`,`amount`) VALUE(NULL,'$name','$type','$amount')");
        if($insert){
            echo 'success';
        }else{
            echo 'fail';
        }
    }
    
}

if(isset($_GET['accountsedit'])){
echo '<div class="col-9 mx-auto" style="max-width:500px;">
<div class="row no-gutters" style="justify-content:center;"><h4>Edit charts of account </h4></div>
<table class="table-striped" style="width:100%;"><tr style="line-height:30px;background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;"><td>Account Name</td><td>Action</td></tr>';
$allaccounts=mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `level`=0");
if(mysqli_num_rows($allaccounts)>0){
    foreach($allaccounts AS $account){
        echo '<tr style="line-height:35px;"><td>'.ucwords($account['name']).'</td><td onclick=popupload(\'managesettings?getaccount='.$account['id'].'\') style="cursor:pointer;"><i class="fa fa-pencil-alt" style="color:green;font-size:20px;"></i></td></tr>';
    }
}else{
    echo '<tr><td colspan="3">No available accounts to edit</td></tr>';
}
echo'</table>
</div>';

}
if(isset($_GET['accountsdelete'])){
    echo '<div class="col-9 mx-auto" style="max-width:500px;">
    <div class="row no-gutters" style="justify-content:center;"><h4>Delete Account from charts </h4></div>
    <table class="table-striped" style="width:100%;"><tr style="line-height:30px;background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;"><td>Account Name</td><td>Action</td></tr>';
    $allaccounts=mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `level`=0");
    if(mysqli_num_rows($allaccounts)>0){
        foreach($allaccounts AS $account){
            echo '<tr style="line-height:35px;"><td >'.ucwords($account['name']).'</td><td onclick="deleteaccount(\''.$account['id'].'\')" style="cursor:pointer;"><i class="bi bi-trash" style="color:red;font-size:20px;" title="Delete"></i></td></tr>';
        }
    }else{
        echo '<tr><td colspan="3">No available accounts to Delete</td></tr>';
    }
    echo'</table>
    </div>';
    
    }
    if(isset($_GET['accountslock'])){
        echo '<div class="col-9 mx-auto" style="max-width:500px;">
        <div class="row no-gutters" style="justify-content:center;"><h4>Edit charts of account </h4></div>
        <table class="table-striped" style="width:100%;"><tr style="line-height:30px;background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;"><td>Account Name</td><td>Action</td></tr>';
        $allaccounts=mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `level`=0");
        if(mysqli_num_rows($allaccounts)>0){
            foreach($allaccounts AS $account){
                echo '<tr style="line-height:35px;"><td>'.ucwords($account['name']).'</td><td onclick="deleteaccount(\'getaccount='.$account['id'].'\') style="cursor:pointer;"><i class="fa fa-trash3" style="color:red;font-size:20px;"></i></td></tr>';
            }
        }else{
            echo '<tr><td colspan="3">No available accounts to edit</td></tr>';
        }
        echo'</table>
        </div>';
        
        }
    if(isset($_GET['getaccount'])){
        $account=$_GET['getaccount'];
        echo '<div class="col-8 mx-auto" style="max-width:300px;">
        <div class="row no-gutter" style="justify-content:center;"><h4>Update Account</h4></div>
        <form id="editaccounts" method="post" onsubmit="saveaccount(event)">
        <input type="hidden" value="'.$account.'" name="acid">';
        $accountdet=mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `id`='$account'");
        foreach($accountdet AS $act){
            $cat=$act['category'];
        echo '<p>Acount name <br> <input style="width:100%;max-width:300px;" type="text" name="editaccount" value="'.$act['name'].'"></p>
        <p>Account category<br><select  name="cat" style="width:100%;;max-width:300px;">';
        $parents=mysqli_query($con,"SELECT * FROM `accounttypes`");
        foreach($parents AS $parent){
            $selected=$parent['id']==$cat?"selected":"";
            echo '<option value="'.$parent['id'].'" '.$selected.'>'.$parent['name'].'</option>';
        }
        echo '</select></p>
        <p>Account code<br><input style="width:100%;max-width:300px;" type="number" name="code" value="'.$act['accountcode'].'"></p>
        <p>Description<br><textarea form="editaccounts" style="height:70px;width:100%;resize:none;outline:none;" name="desc" required>'.$act['description'].'</textarea></p>';
        }
        echo '<div class="row no-gutters" style="justify-content:end;"><button class="btnn" onclick="saveaccount(event)">Update</button></div><br></form></div>';
    }
/**
 * update account details
 **/
    if(isset($_POST['cat'])){
        $cat=$_POST['cat'];
        $account=clean($_POST['editaccount']);
        $code=clean($_POST['code']);
        $desc=clean($_POST['desc']);
        $acid=$_POST['acid'];
        $query=mysqli_query($con,"UPDATE `chartsofaccount` SET `description`='$desc', `name`='$account',`category`='$cat',`accountcode`='$code' WHERE `id`='$acid'");
        if($query){
            echo 'success';
        }
        else{
            echo 'fail'.mysqli_error($con);
        }

    }
    /*Delete Account*/
    if(isset($_POST['deleteaccountid'])){
        $acid=$_POST['deleteaccountid'];
        $selectbal=mysqli_query($con,"SELECT `balance` FROM `chartsofaccount` WHERE `id`='$acid'");
        foreach($selectbal AS $bal){
            if($bal['balance']==0){
                $delete=mysqli_query($con,"DELETE FROM `chartsofaccount` WHERE `id`='$acid'");
                if($delete){
                    echo 'success';
                }
                else{
                    echo 'fail';
                }
            }
            else{
                echo 'Account holding funds can not be deleted!';
            }
        }

    }
mysqli_close($con);
?>