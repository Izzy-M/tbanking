<?php
session_start();
if(isset($_SESSION['csysuser'])){
  echo '<script>location.replace("main.php")</script>';
}
?>
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
    <body>
      <div style="height:100%;width:100%;background:#f2f6fc;">
        <div id="progdv">
          <img src="assets/img/loading.gif" style="height:130px">
        </div>
        <div id="toast">
          <div id="notif"></div>
        </div>
        <div id="progr">
          <div id="progt"></div>
        </div>
        <div class="wrap"></div>
        <div class="overlay"></div>
        
        <div class="card col-sm-10 col-xsm-9 d-flex flex-xs-column flex-sm-row justify-content-center mx-auto" style="max-height:350px;height:auto;max-width:600px;min-width:300px;background:white;position:absolute;right:0;left:0;top:90px;bottom:0;margin:0 auto;">
          <div class="col-4 d-none d-sm-block" style="width:150px;;padding:0;text-align:center;"><img src="assets/img/logo.png" style="width:100px;height:90px;margin-top:50%;">
          <div class="row no-gutters" style="font-family:Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;font-weight:600;font-style:italic;font-size:16px;">Golden Vision Empowerment Program</div>
        </div>
          <div class="col-8 log" style="max-height:300px;max-width:300px;width:100%;margin:0 auto;padding:0px;padding-top:10px;">
            <div style="justify-content:center;background:#f0f0f0;border-radius:15px;display:none;z-index:999;position:absolute;width:90%;max-width:300px;margin:auto;min-height:30px;height:auto;text-align:center;" id="logmessage">
            </div>
            <div class="row no-gutters" style="font-family:Cambria, Cochin, Georgia, Times, 'Times New Roman';font-weight:bold;font-size:18px;">ACCOUNT LOGIN</div>
            <form id="login" method="post">
            <p>
              <div class="row no-gutters" style="font-family:'Signika negative',tahoma;font-size:16px;">Email</div>
              <div class="row no-gutters">
                <input type="email" style="width:100%;" name="currentuser" autocomplete="off" required autofocus autocompelete="off">
              </div>
            </p>
            <p>
              <div class="row no-gutters" style="font-family:'Signika negative',tahoma;font-size:16px;">Password</div>
              <div class="row no-gutters">
              <input type="password" style="width:100%;" name="password" autocompelete="off" required>
              </div>
            </p>
            <p><div class="row no-gutters" style="justify-content:start;max-width:300px;">
              <button class="btnn" style="width:100%;max-width:280px;">Login</button>
              <br>
            </div>
            <br>
            </form>
          </div>
        </div>
      </div>
      <script async id="__bs_script__" src="https://localhost:12738/browser-sync/browser-sync-client.js?v=2.27.5"></script>

    </body>
</html>
<script>

$("#login").on("submit",(e)=>{
			e.preventDefault();
			let data=$("#login").serialize();
			$.ajax({
				method:"POST",url:"savemember",data:data,
			beforeSend:()=>{$("#progdv").fadeIn()},
				complete:()=>{$("#progdv").fadeOut();}}).fail(()=>{toast("A connection error occured!")}
				).done(
					(e)=>{
						if(e.trim()=="success"){
						window.location.replace("main");
						}
						else if(e.trim()=="not found"){
							toast("The user account does not exist");
						}
						else{
							toast("Sorry, Wrong credentials!")
						}
					})
			})
      function _(el){
        return document.getElementById(el);
      }
      function toast(v){
			rest(); $("#toast").fadeIn(); _("notif").innerHTML=v;
			tmo=setTimeout(function(){
				$("#toast").fadeOut();
			},5000);
		}
    function progress(v){
			if(v){
				$("#progr").fadeIn(); _("progt").innerHTML=v;
			}
			else{
				$("#progr").fadeOut(); _("progt").innerHTML="";
			}
		}
    var tmo;
    function rest(){
			clearTimeout(tmo);
		}
</script>