<?php
session_start();
require 'functions.php';
$con = mysqli_connect("localhost",DB_USER,DB_PASS,DB_NAME);
if(isset($_GET['settings'])){
echo '<div class="container-fluid">
<div class="row no-gutters" style="justify-content:center;font-family:google-sans,Roboto;font-size:32px;"><h1>Management Panel</h1></div>
<div class="row mt-1 ">
<div class="card col-xs-11 col-sm-5 mx-sm-auto mx-lg-4 mx-auto" style="max-width:450px;min-width:300px;min-height:150px;padding:0px;">
    <div class="card-header" style="text-align:center;height:40px;"><h5> <i class="fas fa-user-shield" style="color:#4682b4;"></i> &nbsp; Employee Management</h5></div>
    <div class="card-body" style="font-size:14px;font-family:google-sans,Roboto,\'Open-Sans\',helvetica;padding:0px;">
    <div class="row no-gutters setopt" onclick="fetchpage(\'employee?fetch\')"><i class="fas fa-user-check" style="color:green;font-size:20px;"></i>&nbsp; Add Employee</div>
    <div class="row no-gutters setopt" onclick="popupload(\'managesettings?employees\')"><i class="fas fa-user-times" style="color:red;font-size:20px;"></i>&nbsp; Delete Employee</div>
    <div class="row no-gutters setopt" onclick="addemposition()"> <i class="fas fa-user-plus" style="font-size:20px;color:green;"></i>&nbsp; Add Employee Positions</div>
    <div class="row no-gutters setopt" onclick="popupload(\'managesettings?delemploypositions\')"><i class="fas fa-users-slash" style="color:#f27900;font-size:20px;"></i>&nbsp; Delete Employee Positions</div>
    <div class="row no-gutters setopt" onclick="loadpositions()"><i class="fas fa-user-lock" style="color:#4682b4;font-size:20px;"></i>&nbsp; Set permission</div>
    <div class="row no-gutters setopt" onclick="fetchpage(\'managesettings?updatepos\')"><i class="fas fa-user-tie" style="color:green;font-size:20px;"></i>&nbsp; Update employee Position </div>
    
    </div></div>
     <div class="card col-xs-11 col-sm-5 mx-sm-auto mx-lg-4 mx-auto" style="max-width:450px;min-width:300px;min-height:150px;padding:0px;">
    <div class="card-header" style="text-align:center;height:40px;"><h5><i class="fa fa-users-cog" style="color:#4682b4;"></i> &nbsp; Group Management</h5></div>
    <div class="card-body" style="font-size:14px;font-family:google-sans,Roboto,\'Open-Sans\',helvetica;padding:0px;">
     <div class="row no-gutters setopt" onclick="editgroups()"><i class="fa fa-pencil-alt" style="font-size:20px;color:green;"></i> &nbsp; Edit Group</div>
     <div class="row no-gutters setopt" onclick="deletegroup()"><i class="fa fa-times-circle" style="color:red;font-size:20px;"></i> &nbsp; Delete Group</div>
     <div class="row no-gutters setopt" onclick="deactivategroup()"><i class="fa fa-users-slash" style="color:#f27900;font-size:20px;"></i> &nbsp;Deactivate Group</div>
     <div class="row no-gutters setopt" onclick="popupload(\'members?addposition\')"><i class="fas fa-user-tie" style="color:green;font-size:20px;"></i>&nbsp; Add group Position </div>
     <div class="row no-gutters setopt" onclick="popupload(\'members?uploadmembers\')"><i class="fa fa-file-excel" style="color:green;font-size:20px;"></i>&nbsp; Add Users from excel</div>
     <!--<div class="row no-gutters setopt"><i class="fa fa-edit"></i> &nbsp; Edit member loan</div>-->
     </div></div>
<div class="card col-xs-11 col-sm-5 mx-sm-auto mx-lg-4 mx-auto" style="max-width:450px;min-width:300px;min-height:150px;padding:0px;">
    <div class="card-header" style="text-align:center;height:40px;"><h5><i class="fa fa-hand-holding-usd" style="color:#4682b4;"></i> &nbsp; Loan Management</h5></div>
    <div class="card-body" style="font-size:14px;font-family:google-sans,Roboto,\'Open-Sans\',helvetica;padding:0px;">
    <div class="row no-gutters setopt" onclick="addloan()"><i class="bi bi-plus" style="color:green;font-size:20px;"></i>&nbsp; Add new loan type</div>
    <div class="row no-gutters setopt" onclick="editloan()"><i class="fa fa-pencil-alt" style="font-size:20px;color:green;"></i> &nbsp; Edit loan type</div>
    <div class="row no-gutters setopt" onclick="deleteloan()"><i class="fa fa-times-circle" style="color:red;font-size:20px;"></i> &nbsp; Delete Loan type</div>
    <div class="row no-gutters setopt" onclick="externallender()"><i class="fa fa-university" style="color:#4682b4;font-size:20px;"></i> &nbsp; Add external lender</div>
    <!--<div class="row no-gutters setopt"><i class="fa fa-edit"></i> &nbsp; Edit member loan</div>-->

     </div></div>
<div class="card col-xs-11 col-sm-5 mx-sm-auto mx-lg-4 mx-auto" style="max-width:450px;min-width:300px;min-height:150px;padding:0px;"><div class="card-header" style="text-align:center;height:40px;"><h5><i class="fas fa-donate" style="color:#4682b4;"></i> &nbsp; Deposit Management</h5></div><div class="card-body" style="font-size:14px;font-family:google-sans,Roboto,\'Open-Sans\',helvetica;padding:0px;">
<div class="row no-gutters setopt" onclick="addsaving()"><i class="bi bi-plus" style="font-size:20px;color:green;"></i> &nbsp; Add deposit type</div>
<div class="row no-gutters setopt"><i class="fa fa-user-edit"></i> &nbsp; Edit deposit type</div>
<div class="row no-gutters setopt"><i class="fa fa-user-times"></i> &nbsp; Delete deposit type</div>
<div class="row no-gutters setopt" onclick="popupload(\'loan?charges\')"><i class="fa fa-coins" style="color:green;font-size:20px;"></i>&nbsp; Add Charges </div>
<!--<div class="row no-gutters setopt" onclick="popupload(\'loan?charges\')"><i class="fa fa-trash" style="color:red;font-size:20px;"></i>&nbsp; Deleted deposit list </div>-->
</div></div>
<div class="card col-xs-11 col-sm-5 mx-sm-auto mx-lg-4 mx-auto" style="max-width:450px;min-width:300px;min-height:150px;padding:0px;"><div class="card-header" style="text-align:center;height:40px;"><h5><i class="fas fa-gifts" style="color:#4682b4;"></i> &nbsp; Accounting Management</h5></div><div class="card-body" style="font-size:14px;font-family:google-sans,Roboto,\'Open-Sans\',helvetica;padding:0px;">
<div class="row no-gutters setopt" onclick="popupload(\'managesettings?accountsedit\')"> <i class="fa fa-pencil-alt" style="font-size:20px;color:green;"></i> &nbsp; Edit Account</div>
<div class="row no-gutters setopt" onclick="popupload(\'managesettings?accountsdelete\')"> <i class="fa fa-times-circle" style="color:red;font-size:20px;"></i> &nbsp; Delete Account</div>
<!--<div class="row no-gutters setopt" onclick="popupload(\'managesettings?accountslock\')"> <i class="bi bi-trash" style="color:red;font-size:20px;"></i> &nbsp; Suspend Product</div>-->
</div></div>
<div class="col-xs-11 col-sm-5 mx-sm-auto mx-lg-4 d-lg-none" style="max-width:450px;min-width:300px;min-height:150px;padding:0px;">
<!--<div class="card-header" style="text-align:center;height:40px;"><h5><i class="fas fa-gifts" style="color:#4682b4;"></i> &nbsp; Accounting</h5></div>--><div class="card-body" style="font-size:14px;font-family:google-sans,Roboto,\'Open-Sans\',helvetica;padding:0px;">
</div></div>
</div>
</div>';}
//
if(isset($_GET['addemployeepos'])){
    echo '<div class="col-8 mx-auto" style="style="max-width:300px;width:100%;">
    <div class="row no-gutters" style="text-align:center;"><h4>Add Employee position</h4></div>
    <form id="addposition" method="post">
    <p>Position name<br><input type="text" name="emppos" style="max-width:300px;width:100%;"></p>
    <p><div class="row no-gutters" style="justify-content:end;max-width:300px;width:100%;"><button class="btnn">Add</div></div></p>
    </form>
    </div>';}

if(isset($_GET['allemployee'])){
    echo '<div class="col-6 mx-auto" style="style="max-width:400px;width:100%;">
    <div class="row no-gutters" style="text-align:center;"><h4>Update employee Position</h4></div>
    <table class="table-striped">
    <tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:40px;"><td>Employee name</td><td>Position</td></tr>';
    $employees=mysqli_query($con,"SELECT * FROM `employeetb`");
    if($employees){

    }else{
        echo '<tr><td colspan="2" style="text-align:center;"></td></tr>';
    }
    echo '</table></div>';
}
if(isset($_GET['getloantype'])){
    echo '<div class="col-8 mx-auto">
    <div class="row no-gutters" style="justify-content:center"><h4>Update Loantype</h4></div>
   <form id="updateloantype" method="post">';
    $loanid=$_GET['getloantype'];
    $getloantype=mysqli_query($con,"SELECT * FROM `loantype` WHERE `id`='$loanid'");
    foreach($getloantype as $loantype){
        echo '<input type="hidden" value="'.$loanid.'" name="loantp"<p>Loan name<br>
        <input type="text" style="width:100%;max-width:300px;" name="loanname" value="'.$loantype['name'].'">
        </p>
            <p>Interest charge<br>
            <input type="text" style="width:100%;max-width:300px;" name="interest" value="'.$loantype['interest'].'">
            </p>
            <p>Interest rate period<br>
            <select style="width:100%;max-width:300px;" name="rate">';
            $selected=(strpos($loantype['rate'],"month")!==false)?"selected":"";
           echo '<option value="monthly" '.$selected.'>Per month</option>';

           $selected=(strpos($loantype['rate'],"annual")!==false)?"selected":"";
           echo '<option value="annual" selected>Per Annual</option></select>
            </p>
            <p>Overdue Charges<br>
            <input type="text" style="width:100%;max-width:300px;" value="'.$loantype['overduerate'].'" name="overdue"></p>';}
    
    echo '<p><div class="row no-gutters" style="justify-content:end;width:100%;max-width:300px;"><button class="btn btn-success">Update</button></div></p><br></form>
    </div>';
}
if(isset($_GET['updtemployee'])){
    $employ=$_GET['updtemployee'];
    echo '<div class="col-8 mx-auto" style="width:100%;max-width:300px;">
    <div class="row no-gutters" style="text-align:center;"><h4>Update Employee</h4></div>
    <form id="updateemp" method="post">
    <input type="hidden" value="'.$employ.'" name="cmember">';
    $select=mysqli_query($con,"SELECT * FROM `employeetb` WHERE `id`='$employ'");
    foreach($select as $sel){
        $upost=$sel['position'];
        echo '<p>Name<br>
        <input type="text" name="name" style="width:100%;max-width:300px;" value="'.$sel['name'].'">
        </p><p>Email<br>
        <input type="email" name="mail" style="width:100%;max-width:300px;" value="'.$sel['email'].'">
        </p><p>Contact<br>
        <input type="tel" name="phone" style="width:100%;max-width:300px;" value="'.$sel['phone'].'">
        </p>';
    }
    echo '<p>Select employee position<br><select name="nextpos" style="max-width:300px;width:100%;">';
    $allpos=mysqli_query($con,"SELECT * FROM `employee_positions`");
    if(mysqli_num_rows($allpos)>0){
        foreach($allpos as $pos){
            $sl=($pos['id']==$upost)?"selected":'';
            echo '<option value="'.$pos['id'].'" '.$sl.'>'.ucwords($pos['name']).'</option>';
        }
    }else{
        echo '<option>No position</option>';
    }
    
    echo '</select></p>
    <p><div class="row no-gutters" style="justify-content:right;"><button class="btnn">Update</button></div></p>
    </form>
    </div>';
    }
    if(isset($_GET['ldpositions'])){
        echo '<div class="col-9 mx-auto" style="max-width:800px;">
        <div class="row no-gutters" style="justify-content:center;text-align:center"><h4 style="">Employee Positions</h4></div>
        <div class="col-9 mx-auto table-responsive" style="width:100%;">
        <p><table class="table-striped" style="width:100%;">
        <tr style="background:#e6e6fa;width:100%;color:#191970;font-weight:bold;font-size:14px;font-family:cambria;line-height:30px;"><td>Id</td><td>Position</td><td>Manage</td></tr>';
    $positions=mysqli_query($con,"SELECT * FROM `employee_positions` ");
    if(mysqli_num_rows($positions)>0){
        foreach($positions as $position){
        echo '<tr><td>'.ucwords($position['id']).'</td><td>'.$position['name'].'</td><td style="cursor:pointer;" onclick="popupload(\'settings?currentperms='.$position['id'].'\')"><i class="fa fa-user-cog" style="font-size:20px;color: #4682b4;pointer:cursor"></i> Edit Permission</td></tr>'; }
    }else{
        echo '<tr></tr>';
    }
    echo '</table></p>
        </div>
        </div>    
        </div>';
    }
    if(isset($_GET['currentperms'])){
        $position=$_GET['currentperms'];
        echo '<div class="col-8 mx-auto" style="max-width:400px;width:100%;">
        <div class="row no-gutters" style="justify-content:center;"><h5>Position permissions</h5></div>
        <form id="perms" method="post"><input type="hidden" name="selpos" value="'.$position.'"><div class="row no-gutters">';
        $roles=mysqli_query($con,"SELECT `roles` FROM `employee_positions` WHERE `id`='$position'");
        foreach($roles as $role){
            
            $roleperms=explode(",",$role['roles']);
        }
        $permisions=mysqli_query($con,"SELECT * FROM `permisions`");
        foreach($permisions as $perm){
            $ch=(in_array($perm['id'],$roleperms))?"checked":'';
            
            echo '<div class="col-6" style="margin-bottom:5px;"><input type="checkbox" '.$ch.' name="inp'.$perm['id'].'" value="'.$perm['id'].'"> &nbsp;'.$perm['perms'].'</div>';
        }
        echo '</div><br><div class="row no-gutters" style="justify-content:end;"><button class="btnn">Update</div></form></div></div>';
    }

    if(isset($_GET['addemployee'])){
        echo '<div class="col-6 mx-auto" style="max-width:300px;"><div style="text-align:center;"><h4>Add Employee</h4></div>
        <form id="newemployee" method="post"">
        <p>Employee name<br>
        <input type="text" name="employname" style="width:100%;" required></p>
        <p>Phone Number<br><input type="tel" name="phone" style="width:100%;" required></p>
        <p>Employee email<br><input type="email" name="mail" style="width:100%;" required></p>
        <p>Employee Position<br><select name="position" style="max-width:300px;width:100%;">';
        $getallpos=mysqli_query($con,"SELECT `id`,`name` FROM `employee_positions`");
        foreach($getallpos as $pos){
        echo '<option value="'.$pos['id'].'">'.ucwords($pos['name']).'</option>';}
        echo '</select></p><br>
        <div class="row no-gutters" style="justify-content:end;"><button class="btnn">Add</button></div>
        </br></form>
        </div>';
        }
    if(isset($_GET['newlender'])){
        echo '<div class="col-6 mx-auto" style="max-width:300px;"><div style="text-align:center;"><h4>Add New lender</h4></div>
        <form id="newlender" method="post"">
        <p>Lender Name<br>
        <input type="text" name="lendername" style="width:100%;" required></p>
        <p>Lender type<br><select name="lendertype" style="max-width:300px;width:100%;">';
        echo '<option value="office">Office lending</option><option value="bank">Bank Loan</option><option value="sacco">SACCO Loan</option>';
        echo '</select></p><br>
        <div class="row no-gutters" style="justify-content:end;"><button class="btnn">Add</button></div>
        </br></form>
        </div>';
    }
   
?>
<script>
function deleteloan(){fetchpage("managesettings?allloans");}
function editloan(){popupload("managesettings?getloans");}
function addemposition(){popupload("settings?addemployeepos");}
function deactivategroup(){fetchpage('managesettings?deactgroup')}
function deletegroup(){fetchpage('managesettings?delegroups')}
function editgroups(){fetchpage("managesettings?editgroup")}
function updateemployee(id){popupload("managesettings?cemployee")}
function updateposition(id){popupload("settings?updtemployee="+id)}
function loadpositions(){popupload("settings?ldpositions")}
function externallender(){popupload('settings?newlender')}
function loandelete(id){
    let data="deleteloan="+id;
    let confirmed=confirm('Do you want to delete the selected loan type?');
    if(confirmed){
    $.ajax({method:"POST",url:"managesettings",data:data,
        beforeSend:()=>{progress("Processing request..")},
        complete:()=>{progress()}
        }).fail(
            ()=>{toast("No Connection: Sorry you lost internet connection");
                }).done(
                    (e)=>{
                if(e.trim()=="success"){
                    toast("Loan type deleted successfully!");deleteloan();
                }else{
                    toast("Action failed");}
                }
            )}
        }
function deleteemployee(id){
    let data="deleteemployee="+id;
    let confirmed=confirm('Do you want to delete the selected employee?');
    if(confirmed){
    $.ajax({method:"POST",url:"managesettings",data:data,beforeSend:()=>{progress("Processing request..")},complete:()=>{progress()}}).fail(()=>{toast("No Connection: Sorry you lost internet connection")}).done((e)=>{
        if(e.trim()=="success"){
            toast("Employee deleted successfully!");popupload("managesettings?employees")
        }else{
            toast("Action failed")
        }
    })}
}

$("#addposition").on("submit",(e)=>{
    e.preventDefault();
    let data=$("#addposition").serialize();
    $.ajax({method:"POST",url:"managesettings",data:data,
        beforeSend:()=>{progress("Adding position...");},
        complete:()=>{progress();}
        }).fail(()=>{closepop();toast("Connection Error:Internet Connection error!");}).done(
        (e)=>{
        if(e.trim()=="success"){
        _("addposition").reset();
        closepop();toast("Position added successfully");
        }else if(e.trim()=="found"){
                toast("The position is already Added");
            }
            else{
                toast("Failed to add position");
            }
        })
})
function grouptodeleted(id){
    let data="deletedgroup="+id;
    let confirmed=confirm('Do you want to deactivate the selected Group?');
    if(confirmed){
    $.ajax({method:"POST",url:"managesettings",data:data,
        beforeSend:()=>{progress("Processing request..")},
        complete:()=>{progress()}
        }).fail(
            ()=>{toast("No Connection: Sorry you lost internet connection");
                }).done(
                    (e)=>{
                if(e.trim()=="success"){
                    toast("Group deleted successfully!");deletegroup();
                }else{
                    toast("Action failed1");}
                }
            )}
}
$("#perms").on("submit",(e)=>{e.preventDefault();

    let roles=$("#perms").serializeArray();
    let role=[];
    for(let i=1;i<roles.length;i++){
        role.push(roles[i].value);
    }
    
    let data="cpos="+roles[0].value+"&perms="+role.join();
    $.ajax({method:"POST",url:"managesettings",data:data,
            beforeSend:()=>{progress("Updating permissions....");},
            complete:()=>{progress();}
            }).fail(()=>{closepop();toast("Connection Error:Internet Connection error!");}).done(
            (e)=>{
            if(e.trim()=="success"){
            _("perms").reset();
            closepop();toast("Position permisions updated!");
            location.reload(true);
            }
            else{
                    toast("Failed to update permissions");
                }
            })
    
})
$("#newemployee").on("submit",(e)=>{
     e.preventDefault();
    let data=$("#newemployee").serialize();
    $.ajax({method:"POST",url:"savemember",data:data,
        beforeSend:()=>{progress("Creating account...");},
        complete:()=>{progress();}
        }).fail((e,ht,error)=>{toast("Error:Internet Connection error! Try again later");}).done(
        (e)=>{
            console.log(e.trim());
            if(e.trim()=="success"){
            _("newemployee").reset();
        closepop();toast("Account created successfully!");fetchpage('employee?fetch');
        }else if(e.trim()=="found"){
            toast("The employee with the email is already in the system!");
        }else{
            toast("Sorry the process could not be compeleted!")
        }})
            
    })
    $("#newlender").on("submit",(e)=>{
     e.preventDefault();
    let data=$("#newlender").serialize();
    $.ajax({method:"POST",url:"savemember",data:data,
        beforeSend:()=>{progress("Adding New lender");},
        complete:()=>{progress();}
        }).fail((e,ht,error)=>{toast("Error:Internet Connection error! Try again later");}).done(
        (e)=>{
            console.log(e.trim());
            if(e.trim()=="success"){
            _("newlender").reset();
        closepop();toast("Request completed successfully!");
        }else if(e.trim()=="found"){
            toast("The lender name is already in the system!");
        }else{
            toast("Sorry the request could not be compeleted!")
        }})
            
    })
function deactivategp(id){
        let data="deactivategroup="+id;
        let confirmed=confirm('Do you want to deactivate the selected Group?');
        if(confirmed){
        $.ajax({method:"POST",url:"managesettings",data:data,
            beforeSend:()=>{progress("Processing request..")},
            complete:()=>{progress()}
            }).fail(
                ()=>{toast("Network Connection LOst: Sorry you lost internet connection");
                    }).done(
                        (e)=>{
                    if(e.trim()=="success"){
                        toast("Group deactivated successfully!");deactivategroup();
                    }else{
                        toast("Request failed");}
                    }
                )}
    }
function editloantype(id){popupload("settings?getloantype="+id)}
   $("#updateloantype").on("submit",(e)=>{
    e.preventDefault();
    let data=$("#updateloantype").serialize();
    $.ajax({method:"POST",url:"managesettings",data:data,
			beforeSend:()=>{progress("Updating loan details...");},
			complete:()=>{progress();}
			}).fail(()=>{closepop();toast("Connection Error:Internet Connection error!");}).done(
			(e)=>{
                console.log(e.trim())
			if(e.trim()=="success"){
			_("updateloantype").reset();
			closepop();toast("Loan updated successfully");
			}else{
					toast("Failed to update loan");
				}
			})
    
   })
   $("#updateemp").on("submit",(e)=>{
    e.preventDefault();
    let data=$("#updateemp").serialize();
    $.ajax({method:"POST",url:"managesettings",data:data,
			beforeSend:()=>{progress("Updating employ details...");},
			complete:()=>{progress();}
			}).fail(()=>{closepop();toast("Connection Error:Internet Connection error!");}).done(
			(e)=>{
			if(e.trim()=="success"){
			_("updateemp").reset();
			closepop();toast("Member updated successfully");fetchpage('managesettings?updatepos');
			}else{
					toast("Failed to update employee position");
				}
			})
    
   })
function nextactivegroup(total,perpage,current){
	$(".mtb1").load('managesettings?nextactivegroup='+current+"&limit="+perpage+"&count="+total);}
function nextdelgroup(total,perpage,current){
	$(".mtb1").load('managesettings?nextdel='+current+"&limit="+perpage+"&count="+total);}
    function nexteditgroup(total,perpage,current){
	$(".mtb1").load('managesettings?nextegroup='+current+"&limit="+perpage+"&count="+total);}

function editgroup(id){popupload("groups?egroup="+id)}
function saveaccount(e){
    e.preventDefault();
    let alldata=$("#editaccounts").serializeArray();
    let cstate=true;
        let data=$("#editaccounts").serialize();
    alldata.forEach((el,index)=>{
        if(alldata[index].value==""){
            cstate=false;
        }
    })
    if(cstate){
        console.log(data)
        $.ajax({
            method:"POST",
            url:"managesettings",
            data:data,
            beforeSend:()=>{progress("Updating Account");},
            complete:()=>{progress();}
            }).fail(()=>{closepop();toast("Connection Error:Internet Connection error!");}).done(
            (e)=>{
                console.log(e.trim())
            if(e.trim()=="success"){
            _("editaccounts").reset();
            closepop();toast("Account updated successfully");
            }else{
                    toast("Sorry,Failed to update account details. Try again later!");
                }
            })
    }
    else{
        toast("Fill all fieds before updating the account!")
    }
        
}
function deleteaccount(id){
    let data="deleteaccountid="+id;
    $.ajax({
            method:"POST",
            url:"managesettings",
            data:data,
            beforeSend:()=>{progress("Deleting account...");},
            complete:()=>{progress();}
            }).fail(()=>{closepop();toast("Connection Error:Internet Connection error!");}).done(
            (e)=>{
                console.log(e.trim())
            if(e.trim()=="success"){
            closepop();toast("Account deleted successfully");
            }else if(e.trim()=="fail"){
                    toast("Sorry,Failed to delete account. Try again later!");
                }
                else{
                    toast(e.trim());
                }
            })
}

</script>