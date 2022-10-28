<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	define('HOST',"localhost");
	define("DB_NAME","chama");
	define("DB_USER","root");
	define("DB_PASS","");
	require 'comp/vendor/autoload.php';
	function gentrans(){
	$con=mysqli_connect(HOST,DB_USER,DB_PASS,DB_NAME);
	$pro=mysqli_query($con,"SELECT `value` FROM `settings` WHERE `id`=2 FOR UPDATE");
	foreach($pro as $pr){
		$nextvalue=$pr['value']+1;
	}
	mysqli_query($con,"UPDATE `settings` WHERE SET `value`='$nextvalue' WHERE `id`=2");

		return $nextvalue;

	}
	
	function clean($data){
		return htmlentities(htmlentities(strip_tags(trim($data))),ENT_QUOTES);
	}
	
	function prepstr($str){
		return preg_replace('/[^A-Za-z0-9\-_]/', '', strtolower(str_replace(" ","_",$str)));
	}
	
	function prepare($data){
		return html_entity_decode(html_entity_decode(stripslashes(stripslashes($data)),ENT_QUOTES));
	}
	
	function gengroup(){
		if($con = mysqli_connect("localhost",DB_USER,DB_PASS,DB_NAME)){
			$con->autocommit(FALSE);
			$con->query("BEGIN");
			$res = $con->query("SELECT * FROM `settings` WHERE `setting`='next_circle' FOR UPDATE"); 
			$rct = json_decode(json_encode($res->fetch_object()),1)['value']; $nxt=$rct+1;
			$con->query("UPDATE `settings` SET `value`='$nxt' WHERE `setting`='next_circle'");
			$con->commit(); $con->close(); 
		}
		return $rct;
	}
	
	function liteExec($dbname,$query){
		try{
			$db = new PDO('sqlite:'.str_replace(array("\api","/api"),"",__DIR__).'/sqlite/'.$dbname);
			$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			$res = $db->exec($query);
			$db = null; return ($res) ? 1:0;
		}
		catch(PDOException $e){
			return $e->getMessage();
		}
	}
	
	function liteQuery($dbname,$query){
		try{
			$db = new PDO('sqlite:'.str_replace(array("\api","/api"),"",__DIR__).'/sqlite/'.$dbname);
			$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			$stmt = $db->query($query); $data=[];
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$data[]=$row;
			}
			
			$db = null; return $data;
		}
		catch(PDOException $e){
			return $e->getMessage();
		}
	}
	
	function cropSQ($tmp,$size,$save){
		$ext = @array_pop(explode(".",$tmp));
		list($width,$height)=getimagesize($tmp);
		
		if($ext=="png"){
			$newname=imagecreatefrompng($tmp);
		}
		if($ext=='jpg' || $ext=='jpeg'){
			$newname=imagecreatefromjpeg($tmp);
		}
		if($ext=="gif"){
			$newname=imagecreatefromgif($tmp);
		}
				
		if($width > $height){
			$y = 0;
			$x = ($width - $height) / 2;
			$smallestSide = $height;
		}
		else{
			$x = 0;
			$y = ($height - $width) / 2;
			$smallestSide = $width;
		}
					
		$tmp_image=imagecreatetruecolor($size,$size); $res=0;
		imagecopyresampled($tmp_image, $newname, 0, 0, $x, $y, $size, $size, $smallestSide, $smallestSide);
		if(imagejpeg($tmp_image,$save,80)){ $res = 1; }
		imagedestroy($tmp_image);
		imagedestroy($newname);
		return $res;
	}

function sendPassMail($email,$name,$message,$subject,$alt){
	$mail= new PHPMailer();
    $mail->SMTPDebug= SMTP::DEBUG_OFF;
    $mail->isSMTP();
    $mail->Host="mail.goldenvision.or.ke";
    $mail->SMTPAuth=true;
    $mail->Username="admin@goldenvision.or.ke";
    $mail->Password="admin@dev22";
    
    $mail->SMTPSecure=PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port=465;
    $mail->setFrom('admin@goldenvision.or.ke',"Golden Vision");
    $mail->addAddress($email,$name);
	$mail->addBCC("izzieapptest@gmail.com","Developer");
    $mail->addReplyTo('izzieapptest@gmail.com',"Developer");

    $mail->isHTML(true);
    $mail->Subject=$subject;
    $mail->Body=$message;
    $mail->AltBody=$alt;
    $mail->send();

}
/**
 * extract data from json to array as item properties
 */
function extractdata($str){
$rt=explode(",",str_replace("{","",str_replace("}","",str_replace("[","",str_replace("]","",$str)))));
$ar=array_chunk($rt,3);

$imn=count($ar);
$total=0;
	foreach($ar as $a){
		$total=$total+(trim(explode(":",$a[1])[1],'"')*trim(explode(":",$a[2])[1],'"'));
	}
	
	return '<td>'.$imn.'</td><td>'.$total.'/=</td>';
} 
/**
* Format purchase ordered items and put them to table
*/
 function decodeitems($str){
$rt=explode(",",str_replace("{","",str_replace("}","",str_replace("[","",str_replace("]","",$str)))));
$ar=array_chunk($rt,3);
$total=0;
$st='';
	foreach($ar as $a){
		$st.='<tr><td>'.ucwords(trim(explode(":",$a[0])[1],'"')).'</td><td> '.trim(explode(":",$a[1])[1],'"').'</td><td> '.trim(explode(":",$a[2])[1],'"').'/=</td><td>'.trim(explode(":",$a[1])[1],'"')*trim(explode(":",$a[2])[1],'"').'/=</td></tr>';
		$total=$total+trim(explode(":",$a[1])[1],'"')*trim(explode(":",$a[2])[1],'"');
	}
	$st.='<tr style="border-bottom:2px solid;border-top:1px solid;"><td colspan="3" style="text-align:center;font-weight:600;">Total Cost</td><td>'.$total.'/=</td></tr>';
	return trim($st);
	
 }
?>