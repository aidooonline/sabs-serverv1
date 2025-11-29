<div class="col-xl-3 col-md-6" style="z-index:1;background-color:purple">
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
                    <input id="search" class="search" type="search" placeholder=" &#x1F50D Search Customer" />

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
    
</div>

 
<script type="text/javascript">
<?php echo $__env->make('layouts.datascripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

function showelement(){
    //showsearchrow();
    $( "#searchoptionsdiv" ).toggle();
}

function searchuser(){
   
    showhidediv('loadingdiv');
    let searchval = $('#search').val(); 
    let searchoption = $('input[name="selectedsearch"]:checked').val();
    
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
                 alert(" Can't search because: " + JSON.stringify(error));

                // A function to be called if the request fails.					
            },
            beforeSend: function () {

                // A function to be called if before the request is made.
            },
            success: function (response){
                
               // $('#searchoptionsdiv').hide();
                //for holding single row.
                let rowsplit = response.split('*****');

                //for holding all search rows.
                let rowsplits ='';

                
                for(i=0;i<rowsplit.length-1;i++){
                    let myresponsesplit = rowsplit[i].split('___');
               // id,first_name,surname,account_number,account_type,occupation,residential_address,customer_picture
               // accountid,accountnumber,customerpicture,occupation,residentialaddress,accounttypes
             rowsplits +=  showsearchrow(myresponsesplit[0],myresponsesplit[3],myresponsesplit[7],myresponsesplit[5],myresponsesplit[6],myresponsesplit[4],myresponsesplit[1],myresponsesplit[2],myresponsesplit[8]);
                }
                $('#modalbody').html(rowsplits);
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
      let acc = document.getElementsByClassName("accordion");
    let i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function () {
            this.classList.toggle("active");
            let panel = this.nextElementSibling;
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        });
    }
}

  

function showsearchrow(accountid,accountnumber,customerpicture,occupation,residentialaddress,accounttypes,firstname,surname,phonenumber){
   let result =`<button onclick="insertloancustomer('`+ accountid + `','`+ accountnumber + `','`+ customerpicture + `','`+ firstname + `','`+ surname + `','`+ phonenumber + `')" id="accountbtnpanel_` + accountid +`" class="accordion card listdiv" style="background:#fff;width:99%;padding-left:0;padding-right:0;margin-left:0;margin-right:0;height:auto !important;">
 
<table>
    <tr>
        <td width="23%">
            <div> <img style="position:relative;float:left;margin-right:10px;" class="rounded-circle profilepic" src="<?php echo e(env('NOBS_IMAGES')); ?>/useraccounts/profileimage.png" profilevalue="` + customerpicture+ `"></div>
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
 `;
return result;
 


}

function insertloancustomer(accountid,accountnumber,customerpicture,firstname,lastname,phonenumber){
    //insertloancustomer('`+ accountid + `','`+ accountnumber + `','`+ customerpicture + `','`+ firstname + `','`+ lastname + `','`+ phonenumber + `')" 
    //id="accountbtnpanel_` + accountid +`
    
 if(customerpicture == ''){
    $('#customerimage').attr('src',"<?php echo e(env('NOBS_IMAGES')); ?>useraccounts/profileimage.png");
 }else{
    $('#customerimage').attr('src',customerpicture);
 }

 
 $('#first_name').val(firstname);
 $('#last_name').val(lastname);
 $('#phone_number').val(phonenumber);
 $('#account_number').val(accountnumber);
 $('#customer_account_id').val(accountid);
 $('#addcustomertoloan').modal('hide');
   
}


function getprofilepic(theimage){
    try {
                let myimager = theimage.split('___')[3];
                let myimagesplit = myimager.split('users')[1].split('.')[0];
                let preparedimage = myimagesplit + '.jpg';
                let percentagesplitter = preparedimage.split('%2F');
                let firstreplace = '%252F';

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
<?php /**PATH /home/banqgego/public_html/nobs001/resources/views/layouts/searchloancustomer.blade.php ENDPATH**/ ?>