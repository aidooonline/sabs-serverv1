

<div class="col-xl-3 col-md-6" style="position:fixed;top:0 !important;left:0;z-index:1;background-color:purple">
    <div class="card-stats">
        <div class="card-body">
         
            <div class="row">

                <div class="col-auto">
                    <div class="icon bg-gradient-secondary text-white rounded-circle icon-shape">
 
                        <a href="#"
                            onclick="event.preventDefault();showelement();" style="min-width:30px;min-height:30px;">
                            <i class="fas fa-chevron-circle-down text-purple"></i>
                             
                        </a>
                        
                    </div>
                </div>

                <div class="col">
                    <input id="search" class="search" type="search" placeholder=" &#x1F50D Search Customers" />

                </div>
                <div class="col-auto">
                    <div class="icon bg-gradient-secondary text-white rounded-circle icon-shape">


                        <a href="#"
                            onclick="event.preventDefault();searchuser();" style="min-width:30px;min-height:30px;">

                            <i class="fas fa-search text-purple"></i>
                        </a>
                        
                    </div>
                </div>
            </div>



            <div id="searchoptionsdiv" style="display:none;" class="row">
                
                <div id="searchoptions"  class="col">

                      <div>
                        <input type="radio" id="searchbyname" name="selectedsearch" value="searchbyname"
                               checked>
                        <label for="searchbyname">By Name</label>
                      </div>
                      
                      <div>
                        <input type="radio" id="searchbyaccountnumber" name="selectedsearch" value="searchbyaccountnumber">
                        <label for="searchbyaccountnumber">By Account Number</label>
                      </div>
                          
                </div>
                
            </div>
           
            
        </div>
        
    </div>
    <div class="col">
        <span class="text-muted mb-1"><?php echo e(__('Welcome, ')); ?></span>
        <span class="h3 font-weight-bold mb-0 text-light"><?php echo e(\Auth::user()->username); ?></span>
    </div>
</div>

 
<script type="text/javascript">
<?php echo $__env->make('layouts.datascripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

function showelement(){
    //showsearchrow();
    $( "#searchoptionsdiv" ).toggle();
}

function searchuser(){
   
    showhidediv('loadingdiv');
    var searchval = $('#search').val(); 
    var searchoption = $('input[name="selectedsearch"]:checked').val();
    
    if (searchval !== "") {
  
    
    $.ajax({
            url: "<?php echo e(env('NOBS_IMAGES')); ?>search_customers.php",
            method: "POST",      // The HTTP method to use for the request
            dataType: "html",   // The type of data that you're exerciseecting back 	
            data: {                             // Data to be sent to the server.
                search: searchval,
                searchmode: searchoption
            },
            error: function (response,error) {
                 alert(" Can't do because: " + JSON.stringify(error));

                // A function to be called if the request fails.					
            },
            beforeSend: function () {

                // A function to be called if before the request is made.
            },
            success: function (response){
                
                $('#searchoptionsdiv').hide();
                //for holding single row.
                var rowsplit = response.split('*****');

                //for holding all search rows.
                var rowsplits ='<h4 class="card-title" style="margin-top:20px;">Search Customers: <span class="text-warning">'+  searchval  + '</span></h4>';

                
                for(i=0;i<rowsplit.length-1;i++){
                    var myresponsesplit = rowsplit[i].split('___');
               // id,first_name,surname,account_number,account_type,occupation,residential_address,customer_picture
               // accountid,accountnumber,customerpicture,occupation,residentialaddress,accounttypes
             rowsplits +=  showsearchrow(myresponsesplit[0],myresponsesplit[3],myresponsesplit[7],myresponsesplit[5],myresponsesplit[6],myresponsesplit[4],myresponsesplit[1],myresponsesplit[2],myresponsesplit[8]);
                }
                $('#mainsearchdiv').html(rowsplits);
                replaceimages();
               
                showhidediv('loadingdiv');
                 callaccordionfn();
            },
            complete: function (response) {

                // A function to be called when the request finishes
            }
        });


    }else{
alert('enter search data');
    }


}

function callaccordionfn(){ 
      var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function () {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        });
    }
}

  

 
 


function showsearchrow(accountid,accountnumber,customerpicture,occupation,residentialaddress,accounttypes,firstname,surname,phonenumber){
   var result =`<button id="accountbtnpanel_` + accountid +`" class="accordion card listdiv" style="background:#fff;width:99%;padding-left:0;padding-right:0;margin-left:0;margin-right:0;height:auto !important;">
 
<table>
    <tr>
        <td width="23%">
            <div> <img style="position:relative;float:left;margin-right:10px;" class="rounded-circle" src="<?php echo e(env('NOBS_IMAGES')); ?>images/user_avatar/avatar_` + accountid + `.jpg?lastmod=<?php echo date("m/d/Y h:i:s a", time())?>"></div>
        </td>
        <td width="77%" style="text-align:left; ">
            <div style="text-align:left;padding-top:1px !important;">
                <h6 class="account_name" style="padding-top:1px !important;">
                    `+ firstname + ' ' + surname + `</h6>
                <h6 style="color:#724c78;text-align:left !important;">` + accountnumber + ' ' + occupation + ' ' + residentialaddress +`</h6>
                <span class="text-success">` + phonenumber +`</span> 
            </div>
        </td>
    </tr>
</table>

</button>

<div class="panel listdiv2" style="margin-top: 0px !important; padding-left: 0px !important; padding-right: 0px !important; max-height: 0;overflow: hidden;transition: max-height 0.2s ease-out;">
        
    <table style="vertical-align: center;text-align:center;width:99%;margin-bottom:0 !important;">
        
        <tbody><tr> 
         <td class="btn btn-light"><a href="javascript:movetodepositpage('` + accountnumber + `',this,'`+phonenumber+`');" class="btn btn-xs  rounded">
              <img src="<?php echo e(env('NOBS_IMAGES')); ?>icons/depositicon2.png" style="height:23px;width:auto;" class="fa" aria-hidden="true">
              <br>
              <span class="smallspan">Deposit</span>
               </a></td>
               
                <td class="btn btn-light"><a href="javascript:movetowithdrawalpage('` + accountnumber + `',this,'`+phonenumber+`');" class="btn btn-xs  rounded">
              <img src="<?php echo e(env('NOBS_IMAGES')); ?>icons/withdrawicon2.png" style="height:23px;width:auto;" class="fa" aria-hidden="true">
              <br>
              <span class="smallspan">Withdraw</span>
               </a></td>

               <td class="btn btn-light"><a href="javascript:movetowithdrawalpage('` + accountnumber + `',this,'`+phonenumber+`');" class="btn btn-xs  rounded">
              <img src="<?php echo e(env('NOBS_IMAGES')); ?>icons/withdrawicon2.png" style="height:23px;width:auto;" class="fa" aria-hidden="true">
              <br>
              <span class="smallspan">Loans</span>
               </a></td>
               
                  <td class="btn btn-light"><a href="javascript:movetorefundpage('` + accountnumber + `',this,'`+phonenumber+`');" class="btn btn-xs  rounded">
              <img src="<?php echo e(env('NOBS_IMAGES')); ?>icons/reversal2.png" style="height:23px;width:auto;" class="fa" aria-hidden="true">
               <br>
              <span class="smallspan">Reverse</span>
               </a></td>
        
        
        <td class="btn btn-light"><a href="<?php echo e(env('BASE_URL')); ?>accounts/transactiondetails/` + accountnumber + `/" class="btn btn-xs  rounded"><i class="fa fa-eye" aria-hidden="true"></i>
         <br>
              <span class="smallspan">View</span>
        </a></td>
        
         <td class="btn btn-light"><a href="#" onclick="sendDataToReactNativeApp('` + accountid +`');" class="btn btn-xs  rounded"><i class="fa fa-camera" aria-hidden="true"></i>
         <br>
              <span class="smallspan">Camera</span>
        </a></td>
            <td class="btn btn-light">
             <?php if(\Auth::user()->type == 'Agents'): ?>
                
                
                <?php else: ?>
            
            <a href="<?php echo e(env('BASE_URL')); ?>accounts/` + accountid + `/edit" class="btn btn-xs  rounded"><i class="fa fa-pencil" aria-hidden="true"></i> 
             <br>
              <span class="smallspan">Edit</span>
            </a>
            <?php endif; ?>
            </td>
             

         <td style="display:none;"><a href="tel:0248641328" class="btn btn-xs rounded"><i class="fa fa-phone" aria-hidden="true"></i>  </a></td>
         <td style="display:none;"><a href="https://wa.me/0248641328" class="btn btn-xs  rounded"><i class="fa fa-whatsapp" aria-hidden="true"></i> </a></td>
         <td style="display:none;"><a href="sms://0248641328" class="btn btn-xs rounded"><i class="fa fa-sms" aria-hidden="true"></i> </a></td>
        
         <td style="display:none;"><a href="mailto:sboadi2077@gmail.com" class="btn btn-xs rounded"><i class="fa fa-envelope" aria-hidden="true"></i> </a></td>
         
     
        </tr>
        
        
     </tbody></table>
</div>
 `;
return result;
 


}


function getprofilepic(theimage){
    try {
                var myimager = theimage.split('___')[3];
                var myimagesplit = myimager.split('users')[1].split('.')[0];
                var preparedimage = myimagesplit + '.jpg';
                var percentagesplitter = preparedimage.split('%2F');
                var firstreplace = '%252F';

                for (i = 0; i < percentagesplitter.length; i++) {
                    if (i > 0) {
                        if (i == percentagesplitter.length - 1) {
                            firstreplace += percentagesplitter[i];
                        } else {
                            firstreplace += percentagesplitter[i] + '%252F';
                        }

                    }
                }
                return "<?php echo e(env('NOBS_IMAGES')); ?>useraccounts/users" + firstreplace;
            }
            catch (err) {
               return "<?php echo e(env('NOBS_IMAGES')); ?>useraccounts/profileimage.png";
            }
}



                 const sendDataToReactNativeApp = async (id) => {
                 
                window.ReactNativeWebView.postMessage('tocamera_____' + id);
              };
                   
                      

</script>

<style>
#searchoptions input[type='radio']{
color:#ffffff !important;
font-size:14px !important;
}

#searchoptionsdiv{
    padding-left:43px;
}

#searchoptions{
   
     
    text-align: left; 
}


    </style>
<?php /**PATH /home/banqgego/public_html/nobs001/resources/views/layouts/search.blade.php ENDPATH**/ ?>