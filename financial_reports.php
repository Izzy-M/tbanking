<?php
    require 'functions.php';
    $con=mysqli_connect(HOST,DB_USER,DB_PASS,DB_NAME);
    if(isset($_GET['reports'])){
        echo '<div class="table-responsive"><div style="min-width:400px;display:flex;flex-direction:row;overflow-x:auto;width:100%;">
        <div class="ddowns accnt" style="margin-right:2px;"><div class="btnn1" id="gen">Accounts <i class="fas fa-chevron-down" style="font-size:11px;"></i></div>
        <div class="drop" id="accnt">
        <div class="drpitem" onclick="getcontent(\'financial_reports.php?chart\')">Chart of Accounts</div>
        <div class="drpitem" onclick="getcontent(\'reports?inventory\')">View Inventory</div>
        </div>
        </div>
        <div class="ddowns sttment" style="margin-right:2px;"><div class="btnn1" id="statement">Reports<i class="fas fa-chevron-down" style="font-size:11px;"></i></div>
        <div class="drop" id="sttment">
        <div class="drpitem" onclick="getcontent(\'reports?blsheet\')">Balance sheet</div>
        <div class="drpitem" onclick="getcontent(\'reports?journal\')">General Journal</div>
        <div class="drpitem" onclick="getcontent(\'reports?cashbook\')">Petty Cashbook</div>
        <div class="drpitem" onclick="getcontent(\'reports?incomestt\')">Income statement</div>
        <!--<div class="drpitem" onclick="getcontent(\'reports?chart\')">Cash Flow Statement</div>-->
        </div>
        </div>
        <div class="ddowns cust" style="margin-right:2px;"><div class="btnn1" id="gen">Customer <i class="fas fa-chevron-down" style="font-size:11px;"></i></div>
        <div class="drop" id="cust">
        <div class="drpitem" onclick="getcontent(\'reports?invoice\')">Generate invoice</div>
        <div class="drpitem" onclick="popupload(\'reports?saleorder\')">Create sale orders</div>
        <div class="drpitem" onclick="getcontent(\'reports?memo\')">Group fines collection</div>
        <div class="drpitem" onclick="popupload(\'reports?payments\')">Receive payments</div>
        </div>
        </div>
        <div class="ddowns vendor" style="margin-right:2px;"><div class="btnn1" id="gen">Vendors <i class="fas fa-chevron-down" style="font-size:11px;"></i></div>
        <div class="drop" id="vendor">
        <div class="drpitem" onclick="getcontent(\'reports?allvendors\')">Vendor List</div>
        <div class="drpitem" onclick="popupload(\'reports?newbill\')">Enter bills</div>
        <div class="drpitem" onclick="popupload(\'reports?paybill\')">Pay bills</div>
        <div class="drpitem" onclick="popupload(\'reports?purchase\')">New LPO</div>
        <div class="drpitem" onclick="getcontent(\'reports?unpaidbills\')">Unpaid Bills</div>
        <div class="drpitem" onclick="getcontent(\'reports?addproducts\')">Receive items</div>
        </div>
        </div>
        <div class="ddowns bank" style="margin-right:2px;"><div class="btnn1" id="gen">Banking <i class="fas fa-chevron-down" style="font-size:11px;"></i></div>
        <div class="drop" id="bank">
        <div class="drpitem" onclick="popupload(\'reports?withdrawac\')">Bank Withdraw</div>
        <div class="drpitem" onclick="popupload(\'reports?deposits\')">Bank deposits</div>
        <div class="drpitem" onclick="popupload(\'reports?bankaccounts\')">Manage bank A/Cs</div>
        </div>
        </div></div>
    
        <div class="cen" style="min-height:200px;height:auto;min-width:300px;width:98%;margin:0px auto;margin-top:10px;max-width:1240px;">
        <div style="width:98%;min-width:400px;min-height:100px;padding-top:5px;padding-right:10px;">
        <div class="row no-gutters justify-content-end mb-2"><button class="btn1" onclick="popupload(\'reports.php?newasset\')">Acquire asset</button> &nbsp; <!--<button class="btn1" onclick="popupload(\'reports.php?updateasset\')"> Update assets</button>--><button class="btn1" style="min-width:40p" onclick="popupload(\'reports?addcoa\')"> Add account</button></div>
        <div class="table-responsive justify-content-between" style="min-width:300px;display:flex;flex-direction:row;min-height:500px;">
        <div class="col-6 accordion" id="accordion" style="min-width:300px;"><ul>';
            $getaccounts=mysqli_query($con,"SELECT * FROM `accounttypes`");
            foreach($getaccounts as $accounttype){
                $root=$accounttype['id'];
                echo '<li style="list-style:none;"><span class="libox" style="color:#4682b4;font-size:18px;">'.strtoupper($accounttype['name']).'</span>';
                $branches=mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `category`='$root' AND `descendant`=1");
                    echo '<ul class="nested">';
                    foreach($branches as $branch){
                        if($branch['level']>0){
                            echo '<li style="list-style:none;"><span class="libox" style="color:#4682b4;font-size:18px;">'.$branch['name'].'</span><ul class="nested" style="list style:disc">';
                            $nodes=explode(",",$branch['nodes']);
                            for($i=0;$i<count($nodes);$i++){
                                $id=$nodes[$i];
                                $getsubs=mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `id`='$id' AND `descendant`=2");
                                foreach($getsubs AS $sub){
                                    echo '<li style="list-style:disc;">'.$sub['name'].'</li>';
                                }
                            }
                            echo '</ul></li>';
                        }
                        else{
                            echo '<li style="list-style:disc;">'.$branch['name'].'</li>';}
                    }
                    echo '</ul></li>';
                }
                echo '</ul></div></div></div>';
            }
    if(isset($_GET['chart'])){
        echo ' <div style="width:98%;min-width:400px;min-height:100px;padding-top:5px;padding-right:10px;">
        <div class="row no-gutters justify-content-end mb-2"><button class="btn1" onclick="popupload(\'reports.php?newasset\')">Acquire asset</button>&nbsp; <button class="btn1" style="min-width:40p" onclick="popupload(\'reports?addcoa\')"> Add account</button></div>
        <div class="table-responsive justify-content-between" style="min-width:300px;display:flex;flex-direction:row;min-height:600px;">
        <div class="col-6 accordion" id="accordion" style="min-width:300px;"><ul>';
        $getaccounts=mysqli_query($con,"SELECT * FROM `accounttypes`");
        foreach($getaccounts as $accounttype){
            $root=$accounttype['id'];
            echo '<li style="list-style:none;"><span class="libox" style="color:#4682b4;font-size:18px;">'.strtoupper($accounttype['name']).'</span>';
            $branches=mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `category`='$root' AND `descendant`=1");
                echo '<ul class="nested">';
                foreach($branches as $branch){
                    if($branch['level']>0){
                        echo '<li style="list-style:none;"><span class="libox" style="color:#4682b4;font-size:18px;">'.$branch['name'].'</span><ul class="nested" style="list style:disc">';
                        $nodes=explode(",",$branch['nodes']);
                        for($i=0;$i<count($nodes);$i++){
                            $id=$nodes[$i];
                            $getsubs=mysqli_query($con,"SELECT * FROM `chartsofaccount` WHERE `id`='$id' AND `descendant`=2");
                            foreach($getsubs AS $sub){
                                echo '<li style="list-style:disc;">'.$sub['name'].'</li>';
                            }
                        }
                        echo '</ul></li>';
                    }
                    else{echo '<li style="list-style:disc;">'.$branch['name'].'</li>';}
                }
                echo '</ul></li>';
            }
            echo '</ul></div></div></div>';
    }
mysqli_close($con);
?>
<script>
   $(window).ready(()=>{
     let toggler=document.getElementsByClassName('libox');
			for (var i = 0; i <toggler.length; i++){
			  toggler[i].addEventListener('click', function(){
				this.parentElement.querySelector('.nested').classList.toggle('liactive');
				this.classList.toggle('check-box');
			  });}
			})
function changeaccount(e){
   $('input:checkbox').prop("checked",false)
        $("#"+e).prop("checked",true) 
        if($("#"+e).is(":checked")){
        $(".branch").hide()
        
         $("#"+e+"area").show();
       
    }
}
function removecat(){
    $("#para").html('');
    $("#para").html('<div id="cicon"><div class="row no-gutters" style="justify-content:space-between"><div id="subid" style="background:green;color:white;min-width:40px;max-width:110px;height:35px;line-height:35px;font-size:12px;cursor:pointer;padding:0 5px;font-family:cambria;border-radius:5px;" onclick="addsubcategory()">Add subcategory</div><div style="width:20px;height:20px;cursor:pointer;" onclick="addcat()"><i class="bi bi-x" style="color:red;font-size:25px;"></i></div></div><p id="mores"></p>');
}
function addcat(){
    $("#para").html('');
    $("#subid").remove();
    $("#cicon").remove();
}
   $(".accnt").hover(()=>{$("#accnt").show()},()=>{$("#accnt").hide()})
   $(".bank").hover(()=>{$("#bank").show()},()=>{$("#bank").hide()})
   $(".vendor").hover(()=>{$("#vendor").show()},()=>{$("#vendor").hide()})
   $(".cust").hover(()=>{$("#cust").show()},()=>{$("#cust").hide()})
   $(".sttment").hover(()=>{$("#sttment").show()},()=>{$("#sttment").hide()})
   $("chartset").on("submit",(e)=>{
    e.preventDefault();
    alert("posted")
   })
   function getcontent(page){
    $(".drop").hide()
    $("#progdv").fadeIn(); 
			$(".loada").animate({ scrollTop:0 },500);
			
			var urp = (page.split("?").length>1) ? "&md=":"?md=";
			$(".cen").load(page+urp+$(window).width(),function(response,status,xhr){
				$("#progdv").fadeOut(); if(status=="error"){toast("Failed: Check Internet connection");}
			});
   }
   function addnewbank(e){
    e.preventDefault();
    let data=$("#nbaccount").serialize();
    let arrayd=$("#nbaccount").serializeArray();
    let currentstate=true;
        arrayd.forEach((element,index)=>{
            if(arrayd[index].value==""){
                currentstate=false;
    }})
    if(currentstate){
        $.ajax({method:"POST",url:"reports",data:data,beforeSend:(e)=>{progress("Sending request..")},complete:(e)=>{progress()}}).fail((e)=>{
            toast("Connection error! Internet Connection error occured, please try later!")
        }).done((e)=>{
            console.log(e.trim())
            if(e.trim()=="success"){
                toast("Request accepted!");
                closepop();
            }
            else{
                toast("Sorry, your request can not be processed at the moment!");
            }
        })
    }
    else{
    toast("Fill all fields before you submit");
    }
}
function newdeposit(e){
    e.preventDefault();
    let data=$("#deposit").serialize();
    let arrayd=$("#deposit").serializeArray()
    let currentState=true;
    arrayd.forEach((element,index)=>{
        if(arrayd[index].value==""){
            currentState=false;
        }
    })
    if(currentState){
    $.ajax({method:"POST",url:"reports",data:data,
        beforeSend:(e)=>{progress("Sending request..")},
        complete:(e)=>{progress()}
        }).fail((e)=>{
         toast("Connection error! Internet Connection error occured, please try later!")
        }).done((e)=>{
            console.log(e.trim())
            if(e.trim()=="success"){
                toast("Request accepted!");
                closepop();
            }
            if(e.trim()!='fail'){
                toast(e.trim());
            }
            else{
                toast("Sorry, your request can not be processed at the moment!");
            }
        })
    }else{
        toast("Fill all fields before submiting")
    }
}
function newwith(e){
    e.preventDefault();
    let data=$("#withdraw").serialize();
    let arrayd=$("#withdraw").serializeArray();
    let currentState=true;
    arrayd.forEach((element,index)=>{
        if(arrayd[index].value==""){
            currentState=false;
        }
    })
    if(currentState){
    $.ajax({method:"POST",url:"reports",data:data,
        beforeSend:(e)=>{progress("Sending request..")},
        complete:(e)=>{progress()}
        }).fail((e)=>{
         toast("Connection error! Internet Connection error occured, please try later!")
        }).done((e)=>{
            console.log(e.trim())
            if(e.trim()=="success"){
                toast("Request accepted!");
                closepop();
            }
            else{
                toast("Sorry, your request can not be processed at the moment!");
            }
        })
    }else{
        toast("Fill all fields before submitting")
        }
}
function nbill(e){
    e.preventDefault()
    let data=$("#nbill").serialize();
    let currentState=true;
    let arrayd=$("#nbill").serializeArray();
    arrayd.forEach((element,index)=>{if(arrayd[index].value==""){
        currentState=false;
    }})
    if(currentState){
        $.ajax({method:"POST",url:"reports",data:data,
            beforeSend:(e)=>{progress("Sending request..")},
            complete:(e)=>{progress()}
            }).fail((e)=>{
            toast("Connection error! Internet Connection error occured, please try later!")
            }).done((e)=>{
                console.log(e.trim())
                if(e.trim()=="success"){
                    toast("Bill record updated!");
                    blcontent()
                    closepop();
                }
                else{
                    toast("Sorry, your request can not be processed at the moment!");
                }
        })}
        else{
            toast("All fields are required");
        }
}
function paybill(e){
    e.preventDefault();
    let data=$("#pbill").serialize();
    let currentState=true;
    let arrayd=$("#pbill").serializeArray();
    arrayd.forEach((el,index)=>{
        if(arrayd[index].value==""){
            currentState=false;
        }
    })
    if(currentState){
        $.ajax({
            method:"POST",
            url:"reports",
            data:data,
            beforeSend:(e)=>{progress("Sending request..")},
            complete:(e)=>{progress()}
            }).fail((e)=>{
            toast("Connection error! Internet Connection error occured, please try later!")
            }).done((e)=>{
                console.log(e.trim())
                if(e.trim()=="success"){
                    toast("Bill payment accepted!");
                    blcontent()
                    closepop();
                }
                else if(e.trim()=='insufficient'){
                    toast('The credit account balance is insuffucient');
                }
                else if(e.trim()=='incorrect'){
                    toast('The amount for the bill is incorrect,try again!');
                }
                else{
                    toast("Sorry, your request can not be processed at the moment!");
                }
            })
        }else{
            toast("Fill all fields before submitting");
        }
}
function blcontent(){
    $(".blcont").load("reports?updatebills");
}
function addprod(){
    let ind= parseInt(document.getElementById("order").value);
    $(".more").append('<p><div id="or'+ind+'"><div class="row no-gutters" style="justify-content:end;">'+
    '<i class="bi-x-lg" style="float:right;color:#ff4500;cursor:pointer" onclick="rmid(\'or'+ind+'\')"></i></div>Select Item<br><select  id="prod'+ind+'" name="product"  style="width:100%;max-width:300px;"></select></p><p>Quantity<br><input type="number" name="qty" style="width:100%;max-width:300px;" required></p><p>Unit Prices<br><input type="number" name="price" style="width:100%;max-width:300px;" required></div></p>')
    document.getElementById("order").value=ind+1;
    $.get("reports?prods",(data)=>{$("#prod"+ind).html(data)});
}
function rmid(id){
    $("#"+id).remove();
}
function changebill(id){
    popupload('reports?cpaybill='+id)
}
function addinvoiceitem(){
    
    $(".addinvo").append('<p>Select Item<br><select name="product" style="width:100%;max-width:300px;"></select></p><p>Quantity<br><input type="number" name="qty" style="width:100%;max-width:300px;" required></p><p>Unit Prices<br><input type="number" name="price" style="width:100%;max-width:300px;" required></p>')

}
function addpo(e){
    e.preventDefault();
    let data=$("#purchase").serializeArray();
    let details='';
    data.forEach((elem,index)=>{if(elem.name=="product"){details=details+'["product":"'+elem.value+'","'+data[(index+1)].name+'":"'+data[(index+1)].value+'","'+data[(index+2)].name+'":"'+data[index+2].value+'"],'}})
    details.replace(/\,+$/,"");
    let all=data[0].name+"="+data[0].value+"&other="+details+"&"+data[data.length-1].name+"="+data[data.length-1].value;
    $.ajax({
        method:"POST",
        url:"reports",
        data:all,
        beforeSend:(e)=>{progress("Sending request..")},
        complete:(e)=>{progress()}
        }).fail((e)=>{
         toast("Connection error! Internet Connection error occured, please try later!")
        }).done((e)=>{
            console.log(e.trim())
            if(e.trim()=="success"){
                toast("Puchase Order created successfully!");
                blcontent()
                closepop();
            }
            else{
                toast("Sorry, your request can not be processed at the moment!");
            }
    })
}
function addvendor(e){
    e.preventDefault()
    let data=$("#vvendor").serialize();
    $.ajax({method:"POST",url:"reports",data:data,
        beforeSend:(e)=>{progress("Adding new vendor..")},
        complete:(e)=>{progress()}
        }).fail((e)=>{
         toast("Connection error! Internet Connection error occured, please try later!")
        }).done((e)=>{
            if(e.trim()=="success"){
                toast("Vendor added successfully!");
                closepop();
            }
            else{
                toast("Sorry, your request can not be processed at the moment!");
            }
    })

}
function updtvendor(e){
    e.preventDefault()
    let data=$("#updatevendor").serialize();
    
    $.ajax({method:"POST",url:"reports",data:data,
        beforeSend:(e)=>{progress("Updating vendor details..")},
        complete:(e)=>{progress()}
        }).fail((e)=>{
         toast("Connection error! Internet Connection error occured, please try later!")
        }).done((e)=>{
            if(e.trim()=="success"){
                toast("Vendor updated successfully!");
                closepop();
            }
            else{
                toast("Sorry, your request can not be processed at the moment!");
            }
    })
}
function addasset(e){
        e.preventDefault();
        let data=$("#newasset").serialize();
        $.ajax({method:"POST",
            url:"reports",
            data:data,
            beforeSend:()=>{progress("Adding new asset..")},
            complete:()=>{progress()}}).fail(()=>{toast("Connection Error: Internet Connection was lost!")}).done(
            (e)=>{
                console.log(e.trim())
                if(e.trim()=="success"){
                    toast("The asset was successfully added");
                    closepop()
                }
                else if(e.trim()=="fail"){
                     toast("Sorry, your request can not be processed at the moment!");
                 }
                 else{
                    toast(e.trim());
                 }
                }
            )
}
function deleteac(id){
    let data="accuntdel="+id;
}
function uploaddb(){
    
}
function confirmdelivery(id){
   let duedate= document.getElementById('duedate').value;
   if(duedate==''){
    document.getElementById('duedate').focus;
    document.getElementById('duedate').style.borderColor="red";
    setTimeout(()=>{document.getElementById('duedate').style.outline="none";document.getElementById('duedate').style.borderColor=""},3000)
    toast("Select Payment Due Date to confirm Sale Delivery")
   }
   else{
    let data="acceptpo="+id+"&duedate="+duedate;
    $.ajax({
        method:"POST",
        url:"reports",
        data:data,
        beforeSend:(()=>{progress("Updating inventory")}),
        complete:(()=>{progress()})}).fail(()=>{toast("Connection Error: Internet Connection was lost!")}).done(
        (e)=>{
            console.log(e.trim())
            if(e.trim()=="success"){
                toast("Items have been added to inventory");
                getcontent('reports?addproducts');
                closepop();
            window.open("delivery?orderid="+id,'_blank',noope);
            }
            else{
                toast(e.trim())
            }
        })
    }
}
function changechartaccount(id){
    popupload('reports?nextaccount='+id);
}
function customerorder(e){
    e.preventDefault();
    let data=$("#nsaleorder").serializeArray();
    console.log(data)
    let details='';
    data.forEach((elem,index)=>{
        if(elem.name=="product"){
            details=details+'["product":"'+elem.value+'","'+data[(index+1)].name+'":"'+data[(index+1)].value+'","'+data[(index+2)].name+'":"'+data[index+2].value+'"],'
        }
    })
    details.replace(/\,+$/,"");
    let alldata=data[0].name+"="+data[0].value+'&'+data[1].name+"="+data[1].value+'&'+data[2].name+"="+data[2].value+'&'+data[3].name+"="+data[4].value+'&'+data[4].name+"="+data[3].value+"&orderitems="+details+"&"+data[data.length-1].name+"="+data[data.length-1].value;
    let currentState=true;
    data.forEach((element,index)=>{if(data[index].value==""){currentState=false;}})
    console.log(alldata)
    if(currentState){$.ajax({
        method:"POST",
        url:"reports",
        data:alldata,
        beforeSend:(e)=>{progress("Sending request..")},
        complete:(e)=>{progress()}
        }).fail((e)=>{
         toast("Connection error! Internet Connection error occured, please try later!")
        }).done((e)=>{
            console.log(e.trim())
            if(e.trim()=="success"){
                toast("Puchase Order created successfully!");
                blcontent()
                closepop();
            }
            else{
                toast("Sorry, your request can not be processed at the moment!");
            }
    })}else{
        toast("Fill all the fields to proceed!");
    }
}
function generateinvo(id){
let data="invoicegen="+id;
$.ajax(
        {
        method:"POST",
        url:"reports",
        data:data,
        beforeSend:(()=>{progress("Generating invoice...")}),
        complete:(()=>{progress()})}).fail(()=>{toast("Connection Error: Internet Connection was lost!")}).done(
        (e)=>{
            console.log(e.trim())
            if(e.trim()=="success"){
                toast("Invoice was successfully added!");
                getcontent('reports?invoice')
                closepop();
                }
            }
        )

}
function changeassetcategory(id){
         popupload("reports?changeasset="+id);
        }
function delevendor(id){
let data="deletevendor="+id;
        $.ajax({
        method:"POST",
        url:"reports",
        data:data,
        beforeSend:(e)=>{progress("Sending request..")},
        complete:(e)=>{progress()}
        }).fail((e)=>{
        toast("Connection error! Internet Connection error occured, please try later!")
        }).done((e)=>{
            if(e.trim()=="success"){
                toast("Account created successfully!");
                getcontent('reports.php?allvendors');
                closepop();
            }
            else if(e.trim()=="fail"){
                toast("Sorry, your request can not be processed at the moment!");
            }
            else{
                toast(e.trim());
            }
        })
    } 
    
function newchartaccount(e){
    e.preventDefault();
    let data=$("#chartset").serialize();
    let currentState=true;
    let arrayd=$("#chartset").serializeArray();
    arrayd.forEach((element,index)=>{
        if(arrayd[index].value==""){
            currentState=false;
        }
    })

    if(currentState){
        $.ajax({
        method:"POST",
        url:"reports",
        data:data,
        beforeSend:(e)=>{progress("Sending request..")},
        complete:(e)=>{progress()}
        }).fail((e)=>{
        toast("Connection error! Internet Connection error occured, please try later!")
        }).done((e)=>{
            console.log(e.trim())
            console.log(currentState)
            if(e.trim()=="success"){
                toast("Account created successfully!");
                getcontent('financial_reports.php?chart');
                closepop();
            }
            else if(e.trim()=="fail"){
                toast("Sorry, your request can not be processed at the moment!");
            }
            else{
                toast(e.trim());
            }
        })
    }else{
        toast("Fill all the fields to continue ")
    }  
}
    function addsubcategory(){
         $("#cicon").html('');
         $("#cicon").append("<div class='row no-gutters' style=justify-content:end;'><div style='width:20px;height:20px;cursor:pointer;' onclick='addcat()'><i class='bi bi-x' style='color:red;font-size:25px;'></i></div></div><p>Add subcategory<br><input type='text' name='nsubcat' style='width:100%;max-width:300px;'></p>");
     
    }
    function changecustomer(type){
        if(type=="1"){
            $("#grpdet").css("display","block");
            $("#cust1").html('')
            $.get("reports?groupcustomers",(data,stt)=>{$("#blist").html(data)});
        }
        else{
            $("#grpdet").css("display","none");
            $("#grpdet").html('');
            $("#cust1").css("display","block");
            $("#cust1").html(' <p>Buyer<br><input type="text" name="buyer" style="width:100%;max-width:300px;"></p><p>Address<br><input type="text" name="address" style="width:100%;max-width:300px;"></p><p>Contact<br><input type="number" name="cont1" style="width:100%;max-width:300px;"></p>');
            
        }
    }
    function addpettycash(e){
        e.preventDefault();
        let data=$("#pettycash").serialize();
        let arrayd= $("#pettycash").serializeArray();
        let currentState=true;
        arrayd.forEach((element,index)=>{
            if(arrayd[index].value==""){
                currentState=false;
            }
        })
        if(currentState){
            $.ajax({
            method:"POST",
            url:"reports",
            data:data,
            beforeSend:(e)=>{progress("Sending request..")},
            complete:(e)=>{progress()}
            }).fail((e)=>{
            toast("Connection error! Internet Connection error occured, please try later!")
            }).done((e)=>{
                console.log(e.trim())
                console.log(currentState)
                if(e.trim()=="success"){
                    toast("Expense added successfully!");
                    getcontent('reports.php?cashbook');
                    closepop();
                }
                else if(e.trim()=="fail"){
                    toast("Sorry, your request can not be processed at the moment!");
                }
                else{
                    toast(e.trim());
                }
            })
        }
        else{
            toast("Fill in all fields before you proceed!");
        }
    }
    function dpettycash(e){
        e.preventDefault();
        let data=$("#dpettycash").serialize();
        let arrayd= $("#dpettycash").serializeArray();
        let currentState=true;
        arrayd.forEach((element,index)=>{
            if(arrayd[index].value==""){
                currentState=false;
            }
        })
        if(currentState){
            $.ajax({
            method:"POST",
            url:"reports",
            data:data,
            beforeSend:(e)=>{progress("Sending request..")},
            complete:(e)=>{progress()}
            }).fail((e)=>{
            toast("Connection error! Internet Connection error occured, please try later!")
            }).done((e)=>{
                console.log(e.trim())
                console.log(currentState)
                if(e.trim()=="success"){
                    toast("Expense added successfully!");
                    getcontent('reports.php?cashbook');
                    closepop();
                }
                else if(e.trim()=="insuficient"){
                   toast("Sorry, Your credit account has insufficient funds");
                }
                else{
                     toast("Sorry, your request can not be processed at the moment!");
                }
            })
        }
        else{
            toast("Fill in all fields before you proceed!");
        }
    }
function invoicepayment(e){
    e.preventDefault();
    let data=$("#paym").serialize();
    let arrm=$("#paym").serializeArray();
    let currentstate=true;
    arrm.forEach((elem,index)=>{
        if(arrm.value==''){
            currentstate=false;
        }
    })
    if(currentstate){
        $.ajax({
            method:"POST",
            url:"reports",
            data:data,
            beforeSend:(e)=>{progress("Processing request..")},
            complete:(e)=>{progress()}
            }).fail((e)=>{
            toast("Connection error! Internet Connection error occured, please try later!")
            }).done((e)=>{
                console.log(e.trim())
                if(e.trim()=="success"){
                    toast("Request successful!");
                    closepop();
                }
                else{
                     toast("Sorry, your request can not be processed at the moment!");
                }
            })
    }else{
        toast("Fill all fields before you proceed");
    }

}
function receivefines(e){
    e.preventDefault()
    let dt=$("#collections").serializeArray();
    let data='receivefines=';
    dt.forEach((elem,index)=>{
        if(dt[index].name=="fineid"){
        data+=dt[index].value+","
        }
    })
    data=data.replace(/,$/g,'')
    let len=dt.length;
    data=data+'&'+dt[len-1].name+"="+dt[len-1].value;
    console.log(data)
    $.ajax({
            method:"POST",
            url:"reports",
            data:data,
            beforeSend:(e)=>{progress("Processing request..")},
            complete:(e)=>{progress()}
                }).fail((e)=>{
                 toast("Connection error! Internet Connection error occured, please try later!")
             }).done((e)=>{
                console.log(e.trim())
                if(e.trim()=="success"){
                    toast("Request successful");
                    getcontent('reports?memo')
                }
                else{
                     toast("Sorry, your request can not be processed at the moment!");
                }
            })
    }
function loantype(id){
    let data="loantype="+id
    $(".progdv").fadeIn()
    $.get("reports?"+data,(dt)=>{$("#collectionid").html(dt);$(".progdv").fadeOut()})
}
function changejournal(date){
    let data='period='+date;
    $.ajax({method:'GET',data:data,url:'reports'}).fail(toast('Connection Error! Connection failed!')).done(
        ()=>{
            $("#jdata").load(e)
        }
    )
}
function bsprint(){
    window.open('balance',"_blank");
}
</script>