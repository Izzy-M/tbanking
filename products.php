<?php
	
	require "functions.php";
	$con = mysqli_connect("localhost",DB_USER,DB_PASS,DB_NAME);
	
	# view products
	if(isset($_GET['fetch'])){
		$trs=""; $no=0;
		$sql = mysqli_query($con,"SELECT *FROM `products`  WHERE `loanable` =1 ORDER BY `name` ASC");
		while($row=mysqli_fetch_assoc($sql)){
			$rid=$row['id']; $name=prepare(ucwords($row['name'])); $amnt=number_format($row['amount']); $dys=$row['quantity']>1 ? $row['quantity']." items" :$row['quantity']." item"; $no++;
			$chk = mysqli_query($con,"SELECT *FROM `loans` WHERE `product`='$rid'"); $sum=mysqli_num_rows($chk);
			
			$trs.="<tr><td>$no</td><td>$name</td><td>KES $amnt</td><td>$dys </td><td>$sum</td></tr>";
		}
		
		echo "<div style='max-width:1240px;margin:0 auto'>
			<h3 style='font-size:23px;'>Loan Products
			<button class='btnn' style='padding:6px;font-size:15px;float:right' onclick=\"popupload('products.php?add')\"><i class='bi-plus-lg'></i> Product</button></h3>
			<table style='width:100%' class='table-striped mtbl'>
				<tr style='background:#e6e6fa;color:#191970;font-weight:bold;font-size:14px;font-family:cambria'><td colspan='2'>Product</td><td>Amount</td><td>Quantity</td>
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
				<p>Product Loanable<br><select name='loanable' style='width:100%'><option value='0'>Not loanable</option><option value='1'>Loanable</option></select>
				<p style='text-align:right'><button class='btnn'>Save</button></p><br>
			</form><br>
		</div><br>";
	}

	mysqli_close($con);
?>

<script>

	function saveproduct(e){
		e.preventDefault();
		if(confirm("Add loan product?")){
			var data=$("#pform").serialize();
			$.ajax({
				method:"post",url:"savemember",data:data,
				beforeSend:function(){ progress("Processing...please wait"); },
				complete:function(){progress();}
			}).fail(function(){
				toast("Failed: Check internet Connection");
			}).done(function(res){
				console.log(e.trim())
				if(res.trim().split(":")[0]=="success"){
					fetchpage("products.php?fetch"); closepop(); toast("Added successfully!");
				}
				else{ alert(res); }
			});
		}
	}
	
</script>
