<?php
	session_start();
	require "functions.php";
	if(!isset($_SESSION['csysuser'])){echo '<script>location.replace("index.php")</script>';}
	$con = mysqli_connect("localhost",DB_USER,DB_PASS,DB_NAME);
	$trs=""; $sid='1';
	if(isset($_SESSION['grptable'])){
	$ccgrp=$_SESSION['grptable'];}
	#load permissions here
	if(isset($_SESSION['csysuser'])){
		$id=$_SESSION['csysuser'];
		$permissions=[];
		
		$roles=mysqli_query($con,"SELECT `roles` FROM `employeetb` as `et` INNER JOIN `employee_positions` as `ep` ON `et`.`position`=`ep`.`id` WHERE `et`.`id`='$id'");
		foreach($roles as $role){
			$perms=explode(",",$role['roles']);
		}
		for($i=0;$i<count($perms);$i++){
			$pid=$perms[$i];
			
			$selectperm=mysqli_query($con,"SELECT `perms` FROM `permisions` WHERE `id`='$pid'");
			foreach($selectperm as $permname){
			array_push($permissions,strtolower($permname['perms']));
			}
		}
	}

	$opts = "<option value='0'>-- Group --</option>";
	$qri = mysqli_query($con,"SELECT *FROM `groups` ORDER BY `name` ASC");
	while($row=mysqli_fetch_assoc($qri)){
		$grp = $row['id']; $gname=prepare(ucwords($row['name'])); $cnd = ($grp==@$mgrp) ? "selected":""; $groups[$grp]=$gname;
		$opts.="<option value='$grp' $cnd>$gname</option>";
	}?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GVEP SYSTEM</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <meta charset="utf-8">
  <meta name="viewport"content="width=device-width,initial-scale=1.0,user-scalable=no,user-scalable=0">
  <meta name="theme-color" content="#489ED7"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
  <link rel="shortcut icon" href="assets/img/favicon.ico">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css?family=Signika Negative&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Acme&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/client.css?<?php echo rand(1234,4321); ?>">
  <script src="assets/js/jquery.js"></script>
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato&family=Noto+Serif:ital@1&family=Open+Sans:wght@400;500&family=Playfair+Display:ital,wght@1,600&family=Roboto&family=Roboto+Flex:opsz,wght@8..144,200&family=Signika+Negative&family=Signika:wght@500&display=swap" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<?php echo '<input type="hidden" value="" id="temp">
<div id="progdv"><img src="assets/img/loading.gif" style="height:130px"></div>
<div id="toast"><div id="notif"></div></div>
<div id="progr"><div id="progt"></div></div>
<div class="wrap"></div><div class="overlay"></div>

<div class="popup" style="font-family:cambria">
  <h3 style="padding:10px;font-size:22px;color:#191970">
  <i class="bi-x-lg" style="float:right;color:#ff4500;cursor:pointer" title="Close" onclick="closepop()"></i></h3>
  <div class="popdiv" style="padding:10px"></div>
</div>
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item" style="cursor:pointer;">
        <div class="nav-link" data-widget="pushmenu" role="button"><i class="fas fa-bars"></i></div>
      </li>
     
    </ul>
  </nav>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="brand-link">
      <img src="assets/img/logo.png" alt="GVEP Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-heavy">GVEP SYSTEM</span>
    </div>

    <div class="sidebar">
      
      <div class="form-inline">
        
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
			<li class="nav-item" style="cursor:pointer;" onclick="dashbd()">
            <div class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </div>
          </li>';
		  if(in_array("view groups",$permissions)){echo '<li class="nav-item" style="cursor:pointer;" onclick="fetchpage(\'groups.php?fetch\')">
            <div class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p> Groups</p>
            </div>
          </li>';}
          if(in_array("view members",$permissions)){echo '<li class="nav-item" style="cursor:pointer;" onclick="fetchpage(\'members.php?fetch\')">
            <div class="nav-link">
              <i class="nav-icon bi-people"></i>
              <p> Members</p>
            </div>
          </li>';}
		  if(in_array("view products",$permissions)){echo '<li class="nav-item" style="cursor:pointer;" onclick="fetchpage(\'products.php?fetch\')">
              <div class="nav-link">
                <i class="nav-icon bi-list-stars"></i>
                <p>Products</p>
              </div>
            </li>';}
            if(in_array("view loans",$permissions)){ echo '<li class="nav-item" style="cursor:pointer;" onclick="fetchpage(\'members.php?loans\')">
              <div class="nav-link" >
                <i class=" nav-icon bi-list-stars"></i>
                <p> Loans</p>
              </div>
            </li>';}
			if(in_array('add employee',$permissions)){echo '<li class="nav-item" style="cursor:pointer;" onclick="fetchpage(\'employee.php?fetch\')">
            <div class="nav-link">
              <i class="nav-icon fas fa-user-friends"></i>
              <p>Employee</p>
            </div>
          </li>';}else{echo'';};
		  if(in_array("access control panel",$permissions)){echo '<li class="nav-item" style="cursor:pointer;" onclick="fetchpage(\'settings.php?settings\')">
            <div class="nav-link">
              <i class="nav-icon fas fa-cogs"></i>
              <p>Admin panel</p>
            </div>
          </li>';}else{echo '';}
		 if(in_array("accountant",$permissions)){
			echo '<li class="nav-item" style="cursor:pointer;" onclick="fetchpage(\'financial_reports.php?reports\')">
            <div class="nav-link">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>Accountant</p>
            </div>
          </li>';}
		  if(isset($_SESSION['csysuser'])){echo '<div class="nav-item">
		  <div style="cursor:pointer;text-decoration:none;">
            <div class="nav-link" onclick="logout()">
              <i class="nav-icon fas fa-power-off" style="color:red;"></i>
              <p>Log Out</p>
            </div>
        </div>';}
		echo '<br><br>
      </nav>
    </div>
  </aside>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-12 mainactivity">
          <h3 style="font-size:23px;">Members </h3>
		  <div class="table-responsive">
			<table style="width:100%;min-width:600px;font-family:cambria;font-size:14px;" class="table-striped mtbl">
				<caption style="caption-side:top">
					<button class="btnn" style="padding:6px;font-size:15px;float:right" onclick="popupload(\'members.php?add\')"><i class="bi-person-plus"></i> Member</button>
						<select style="width:125px;padding:5px;font-size:15px" onchange="fetchpage(\'members.php?fetch=\'+this.value)">'.$opts.'</select>
							<input type="search" onkeyup="searchperson(this.value)" style="width:120px;margin-left:20px;outline:none;" placeholder="Search member.."></caption><tbody id="mbs">';
							$allmembers=mysqli_query($con,"SELECT * FROM `members` WHERE `status`=1");
							$getall=mysqli_num_rows($allmembers);
							$perpage=20;
							if($getall>$perpage){
							$pages=ceil($getall/$perpage);
							if($pages>1){
								echo '<tr><td colspan="6" style="background:#f0f0f0;"><div class="row no-gutters" style="justify-content:end;"><select class="sel" onchange="getclist(this.value,'.$pages.','.$perpage.')">';
							for($i=0;$i<$pages;$i++){
								$cl=($i==0)?"selected":'';
							echo '<option value="'.$i.'">'.($i+1).'</option>';}echo '</select></td></tr>';}
						}
					echo '<tr style="background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria"><td colspan="2">Member</td><td>Idno/UID</td>
						<td>Group/Phone</td><td>Next of Kin</td><td>Loan</td></tr>';
						
						$sql = mysqli_query($con,"SELECT *FROM `members` WHERE `status`=1 ORDER BY `name` ASC LIMIT $perpage"); $total=mysqli_num_rows($sql);
							foreach($sql as $row){
								$mid=$row['id']; $name=ucwords(prepare($row['name'])); $uid=$row['uid']; $fon=$row['phone']; $idno=$row['idno'];
								$pic=strlen($row['photo'])>1?'data:image/jpg;base64,'.$row['photo']:'assets/img/user.png';
								$kinname=[];$kincont=[];
								if(strlen($row['nextkin'])>2){
								foreach(json_decode(@$row['nextkin'],1) as $kin=>$kindet){
									$kcont=explode(",",str_replace(']','',str_replace('[','',$kindet)));
									array_push($kinname,$kin);array_push($kincont,$kcont[0]);
								}}
								$kin=strlen(@$kinname[0])>2?ucwords(@$kinname[0]):"None";
								$cont=strlen(@$kincont[0])>5?'0'.ltrim(ltrim(clean(@$kincont[0]),"0"),"254"):''; $grp=$row['mgroup'];$dob=strlen($row['dob'])>6?$row['dob']:'--/--/---';
								$loan = "<button class='btnn' style='padding:4px;min-width:60px' onclick=\"popupload('members.php?assign=$mid')\"><i class='bi-plus-lg'></i> Assign</button>";
									
								$qri = mysqli_query($con,"SELECT *FROM `loans` WHERE `client`='$mid'");
								$tloan=0;$tpaid=0;
									foreach($qri as $qris){
										$paid=$qris['paid']; $amnt=$qris['history'];
										if(($qris['product']<1) && ($paid<$amnt)){
											$loan=$loant = "<button class='btnn' style='padding:4px;min-width:60px' onclick=\"popupload('members.php?assign=$mid')\"><i class='bi-plus-lg'></i> Assign</button>";
										}else{
									$tpaid=$tpaid+$qris['paid'];$tloan=$tloan+$amnt=$qris['history'];
									$loan="KES ".number_format($tloan)."<br><span style='color:grey;font-size:14px'>Paid: ".number_format($tpaid)."</span>";}
									}
								echo "<tr valign='top' style='cursor:pointer;' ><td style='width:50px'>
								<img src='$pic' style='width:40px;border-radius:50%' onclick='getuserdetails(".$mid.")'></td><td onclick='getuserdetails(".$mid.")'>$name<br><span style='color:grey;font-size:14px'>DOB: $dob</span></td onclick='getuserdetails(".$mid.")'><td onclick='getuserdetails(".$mid.")'>$idno<br><span style='color:grey;font-size:14px'>UID: $uid</span></td>
								<td onclick='getuserdetails(".$mid.")'>$groups[$grp]<br><span style='color:grey;font-size:14px'>0$fon</span></td><td onclick='getuserdetails(".$mid.")'>$kin<br><span style='color:grey;font-size:14px'>$cont</span></td><td>$loan</td></tr>";
							}
							
						echo '</tbody></table>
						</div>
					</div>
				</div>
			</div>
			</div>
        </div>
      </div>
    </section>
  </div>
  <footer class="main-footer">
 
    
  </footer>

  <aside class="control-sidebar control-sidebar-dark">
  </aside>
</div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/sparklines/sparkline.js"></script>
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="dist/js/adminlte.js"></script>
<script async id="__bs_script__" src="https://localhost:12738/browser-sync/browser-sync-client.js?v=2.27.5"></script>
</body>
</html>';
	mysqli_close($con);
	?>
	
	<script>

	$(window).resize(()=>{
			if($(window).width()<715){$(".shd").show();$(".hd").hide();//navbar("hide");
			}else{$(".shd").hide();$(".hd").show();}}
			
			)
		$(document).ready(()=>{
			if($(window).width()<715){$(".shd").show();$(".hd").hide();//navbar("hide");
			}else{$(".shd").hide();$(".hd").show();closemenulist()}
		})
		function dashbd(){

			fetchpage("loan.php?homepg");
		}
		function popupload(url){
			closemenulist()
			if($(window).width()<715){$(".shd").show();$(".hd").hide();//navbar("hide");
			}else{$(".shd").hide();$(".hd").show();closemenulist()}
			if($(window).width()<601){//navbar("hide");
			}else{}
			$(".popdiv").html("<br><br><center><img src='assets/img/waiting.gif'></center>");
			$(".popup").fadeIn(); $(".overlay").fadeIn(); 
			$(".popdiv").load(url+"&sid=<?php echo $sid; ?>");
			
		}
		function logout(){
			location.replace("logout.php");
		}
		function getmenulist(){
			$(".overlay").fadeIn();
			$(".menulist").fadeIn()
		}
		function closemenulist(){
			$(".overlay").fadeOut();
			$(".menulist").fadeOut()
		}
		function addloan(){
			closemenulist()
			popupload("loan.php?addloan");
		}
		function addsaving(){
			closemenulist()
			popupload("loan.php?addsave");
		}
		function closepop(){
			$(".popup").fadeOut(); $(".previewimg").fadeOut(); $(".overlay").fadeOut();
		}
		function searchperson(item){
			if(item.length>3){
				let data="sqterm="+item;
				$.ajax({method:"POST",url:"savemember.php",data:data}).fail().done((e)=>{
					$("#mbs").html(e)
				})
			}else{
				$("#mbs").load("savemember.php?allmembers");
			}
		}
		function getuserdetails(id){
			popupload("savemember.php?userdetails="+id);
		}
		function fetchpage(page){
			dismissmenu()
			if($(window).width()<601){ //navbar("hide");
			}
			$("#progdv").fadeIn(); 
			$(".loada").animate({ scrollTop:0 },500);
			
			var urp = (page.split("?").length>1) ? "&md=":"?md=";
			$(".mainactivity").load(page+urp+$(window).width(),function(response,status,xhr){
				$("#progdv").fadeOut(); if(status=="error"){toast("Failed: Check Internet connection");}
			});
		}
		
		function toast(v){
			rest(); $("#toast").fadeIn(); _("notif").innerHTML=v;
			tmo=setTimeout(function(){
				$("#toast").fadeOut();
			},5000);
		}
		
		var tmo;
		function rest(){
			clearTimeout(tmo);
		}
		
		function progress(v){
			if(v){
				$("#progr").fadeIn(); _("progt").innerHTML=v;
			}
			else{
				$("#progr").fadeOut(); _("progt").innerHTML="";
			}
		}
		
		function valid(id,v){
			var exp=/^[0-9.]+$/;
			if(!v.match(exp)){ document.getElementById(id).value=v.slice(0,-1); }
		}
		
		function _(el){
			return document.getElementById(el);
		}
	
		
      function currentgroup(){
	let x=document.cookie;
		let ckarr=x.split(";");
		for(let i=0;i<ckarr.length;i++){
			if(ckarr[i].includes('cgroup')){
				let pr=ckarr[i].split('=');
				return pr[1];}
			}
		}
function getclist(id,qty,ppage){
	$("#progdv").fadeIn(); 
	$(".mtbl").load('loan.php?numberpage='+id+'&total='+qty+'&ppage='+ppage);$("#progdv").fadeOut(); }
function getmoreclist(id,qty,ppage,group){
	$("#progdv").fadeIn(); 
	$(".mtbl").load('loan.php?nextpagegroup='+id+'&total='+qty+'&ppage='+ppage+'&group='+group);$("#progdv").fadeOut(); }
function getgolden(){
	moredismiss()
	$(".grpview").load("groups.php?goldenkid="+grp)
}
function riskfund(grp){
	moredismiss()
	popupload("groups.php?riskfund="+grp)
}
function extdebts(grp){
	moredismiss()
	popupload("groups.php?takedebt="+grp)
}
function fundsin(grp){
	moredismiss()
	$(".grpview").load("groups.php?fundsin="+grp)
}
function getgroupsavings(grp){
	moredismiss()
	$(".grpview").load("groups.php?grpsavings="+grp)
}
function getfundsout(grp){
	moredismiss()
	$(".grpview").load("groups.php?fundsout="+grp)
}
function home(grp){
	moredismiss()
	fetchpage("groups.php?viewgroup="+grp)
}
function managegroup(grp){
	moredismiss()
	$(".grpview").load("groups.php?managemembers="+grp)}
function externaldebts(grp){
	moredismiss()
	$(".grpview").load("groups.php?getextdebt="+grp)
}
function getgolden(grp){
	moredismiss()
	$(".grpview").load("groups.php?goldenkid="+grp)
}
function getxmass(grp){
	moredismiss()
	$(".grpview").load("groups.php?xmassout="+grp)
}
function addexpenses(grp){
	moredismiss()
	popupload("groups.php?newexpense="+grp)
}
function officeexp(grp){
	moredismiss()
	popupload("groups.php?offexpense="+grp)
}
function managedeposits(grp){
	moredismiss()
	$(".grpview").load("groups.php?authenticate="+grp)
}
function dismissmenu(){
	if($(window).width()<=768){
		$('[data-widget="pushmenu"]').PushMenu("collapse")
	}
}
function getmorepos(){
	if($(".content-header").width()>=700){
		$(".more").css("left","750px")
	}else{
		$(".more").css("left",($(".content-header").width()-150)+'px')
	}
}

	</script>
