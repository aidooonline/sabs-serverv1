

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
        <span class="text-muted mb-1">{{__('Welcome, ')}}</span>
        <span class="h3 font-weight-bold mb-0 text-light">{{\Auth::user()->username}}</span>
    </div>
</div>

 
<script type="text/javascript">

 

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
            url: "{{env('NOBS_IMAGES')}}search_customers.php",
            method: "POST",      // The HTTP method to use for the request
            dataType: "html",   // The type of data that you're exerciseecting back 	
            data: {                             // Data to be sent to the server.
                search: searchval,
                searchmode: searchoption
            },
            error: function () {

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
                var rowsplits ='';

                
                for(i=0;i<rowsplit.length-1;i++){
                    var myresponsesplit = rowsplit[i].split('___');
               // id,first_name,surname,account_number,account_type,occupation,residential_address,customer_picture
               // accountid,accountnumber,customerpicture,occupation,residentialaddress,accounttypes
             rowsplits +=  showsearchrow(myresponsesplit[0],myresponsesplit[3],myresponsesplit[7],myresponsesplit[5],myresponsesplit[6],myresponsesplit[4],myresponsesplit[1],myresponsesplit[2],myresponsesplit[8]);
                }
                $('#mainsearchdiv').html(rowsplits);
                replaceimages();
                showhidediv('loadingdiv');
            },
            complete: function (response) {

                // A function to be called when the request finishes
            }
        });


    }else{
alert('enter search data');
    }


}

 


function showsearchrow(accountid,accountnumber,customerpicture,occupation,residentialaddress,accounttypes,firstname,surname,phonenumber){
   var result =`<button id="accountbtnpanel_` + accountid +`" onclick="movetoloanpage('` + accountnumber +`',this,` + phonenumber + `);" class="accordion card listdiv" style="background:#fff;width:99%;padding-left:0;padding-right:0;margin-left:0;margin-right:0;">

<table>
    <tr>
        <td width="23%">
            <div> <img style="position:relative;float:left;margin-right:10px;" class="rounded-circle profilepic" src="{{env('NOBS_IMAGES')}}/useraccounts/profileimage.png" profilevalue="` + customerpicture+ `"></div>
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
<div class="panel listdiv2" style="margin-top:0 !important;">
 
<table class="table table-panel">
   
    <tbody>
        <tr>
            <td class="mintd" style="width:40%;padding:1px 1px;">Balance:</td>
            <td class="mintd" style="padding:1px 1px;" id="tdbalanceid_` + accountid + `"></td>
          </tr>
        <tr>
            <td class="mintd" >Account Number:</td>
            <td class="mintd"><a href="{{ route('accounts.transactiondetails') }}/` + accountnumber +`" >` + accountnumber +`</a></td>
        </tr>

          <tr>
            <td class="mintd">Account Types:</td>
            <td class="mintd">` + accounttypes + `</td>
          </tr>
 
    </tbody>
  </table>
</div>`;
return result;
 


}
@include('layouts.datascripts')

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
                return "{{env('NOBS_IMAGES')}}useraccounts/users" + firstreplace;
            }
            catch (err) {
               return "{{env('NOBS_IMAGES')}}useraccounts/profileimage.png";
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

