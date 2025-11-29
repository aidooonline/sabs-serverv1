function dataapi(accountnumber,targetid) {
    
    showhidediv('loadingdiv');
    $.ajax({
        url: "<?php echo e(env('NOBS_IMAGES')); ?>index.php",
        method: "POST",      // The HTTP method to use for the request
        dataType: "html",   // The type of data that you're exerciseecting back 	
        data: {                             // Data to be sent to the server.
            accountnumber: accountnumber
        },
        error: function () {

            // A function to be called if the request fails.					
        },
        beforeSend: function () {

            // A function to be called if before the request is made.
        },
        success: function (response) {
              
            getbtn_acbalance(targetid.id,response,accountnumber);
            showhidediv('loadingdiv');

            // A function to be called if the request succeeds.
        },
        complete: function (response) {

            // A function to be called when the request finishes
        }
    });
}

function getbtn_acbalance(targetid,response,acnum){
 
        
    //split the id;
    var splitter = targetid.split('_')[1];



    var preparedsplitter = 'tdbalanceid_' + splitter;

    //prepare balance data
   /* var totalwithdrawal = '<div style="width:100%;">Ttl Withdrawals : ' + response.split('___')[0] + ' </div>';
    var totaldeposit  =' <div style="width:100%;">Ttl Deposits : '+ response.split('___')[1] + ' </div>';*/
    var totalbalance  = '<div style="width:100%;"><a id="acnumba_' + acnum + '" href="<?php echo e(route('accounts.transactiondetails')); ?>/' + acnum +'"   style="color:#ad9349;">GH¢ ' + response.split('___')[2] + ' </a></div>';
//alert(response.split('___')[2]);
    $('#' + preparedsplitter).html(totalbalance);

}

function movetopage(accountnumber,that){
    window.location.href = "<?php echo e(route('accounts.transactiondetails')); ?>/" + accountnumber;
}


function movetodepositpage(accountnumber,that,pbn){
    window.location.href = "<?php echo e(route('accounts.deposit')); ?>/" + accountnumber + "/<?php echo e(Auth::user()->created_by_user); ?>" + "/" + pbn;
}



function movetorefundpage(accountnumber,that,pbn){
    window.location.href = "<?php echo e(route('accounts.refund')); ?>/" + accountnumber + "/<?php echo e(Auth::user()->created_by_user); ?>" + "/" + pbn;
}

function movetowithdrawalpage(accountnumber,that,pbn){
    window.location.href = "<?php echo e(route('accounts.withdraw')); ?>/" + accountnumber + "/<?php echo e(Auth::user()->created_by_user); ?>" + "/" + pbn;
}



//For deposit
//first check whether everything is alight.

function checkdepositfields(){
    let amount = $('#amount').val();

    
if (isNaN(amount)){
   return false;
}else{
    if(amount == '' || amount <= 0){
        return false;
    }else{
        return true;
    }
}

}



function makedepositjvs(to,msg){
    let __id__ = uuidv4();
    let account_number = $('#account_number').html().trim();
    let account_type = $('#accounttype').html().trim();
    let amount = $('#amount').val();
    let agentname = "<?php echo e(\Auth::user()->name); ?>";
    let users = "<?php echo e(\Auth::user()->created_by_user); ?>";
    
    
    showhidediv('loadingdiv');

    if(checkdepositfields()){
        $.ajax({
            url: "<?php echo e(env('NOBS_IMAGES')); ?>deposit.php",
            method: "POST",      // The HTTP method to use for the request
            dataType: "html",   // The type of data that you're exerciseecting back 	
            data: {  
                __id__:__id__,
                account_number:account_number,
                account_type:account_type,
                amount:amount,
                agentname:agentname, 
                users:users,
                insertuseraccount:'insertuseraccount'
            },
            error: function () {
                showhidediv('loadingdiv');
                // A function to be called if the request fails.	
                alert('kindly check your internet. it seems deposit taking too long.');				
            },
            beforeSend: function () {
    
                // A function to be called if before the request is made.
            },
            success: function (response) {
               
    if(response == 'ERROR'){
          alert('there was a problem connecting to server. Kindly re-connect again.');
          showhidediv('loadingdiv');
    }else{
        sndmsg(to,msg);
        alert(response);

       
        location.reload();
       
 
          showhidediv('loadingdiv');
       

    }
                // A function to be called if the request succeeds.
            },
            timeout:4000,// set timeout to 4 seconds
            complete: function (response) {
    
                // A function to be called when the request finishes
            }
        });
    }else{
        alert('Please enter valid amount');
        showhidediv('loadingdiv');
    }
    

}



function makerefundjvs(to,msg){
    let __id__ = uuidv4();
    let account_number = $('#account_number').html().trim();
    let account_type = $('#accounttype').html().trim();
    let amount = $('#amount').val();
    let agentname = "<?php echo e(\Auth::user()->name); ?>";
    let users = "<?php echo e(\Auth::user()->created_by_user); ?>";
    
    
    showhidediv('loadingdiv');

    if(checkdepositfields()){
        $.ajax({
            url: "<?php echo e(env('NOBS_IMAGES')); ?>refund.php",
            method: "POST",      // The HTTP method to use for the request
            dataType: "html",   // The type of data that you're exerciseecting back 	
            data: {  
                __id__:__id__,
                account_number:account_number,
                account_type:account_type,
                amount:amount,
                agentname:agentname, 
                users:users,
                insertuseraccount:'insertuseraccount'
            },
            error: function () {
                showhidediv('loadingdiv');
                // A function to be called if the request fails.	
                alert('kindly check your internet. it seems deposit taking too long.');				
            },
            beforeSend: function () {
    
                // A function to be called if before the request is made.
            },
            success: function (response) {
               
    if(response == 'ERROR'){
          alert('there was a problem connecting to server. Kindly re-connect again.');
          showhidediv('loadingdiv');
    }else{
        sndmsg(to,msg);
        alert(response);

       
        location.reload();
       
 
          showhidediv('loadingdiv');
       

    }
                // A function to be called if the request succeeds.
            },
            timeout:4000,// set timeout to 4 seconds
            complete: function (response) {
    
                // A function to be called when the request finishes
            }
        });
    }else{
        alert('Please enter valid amount');
        showhidediv('loadingdiv');
    }
    

}


function makewithdrawaljvs(to,msg){


    if($('#confirmationcode').val() == getconfirmcode()){
        
        
        let __id__ = uuidv4();
        let account_number = $('#account_number').html().trim();
        let account_type = $('#accounttype').html().trim();
        let amount = $('#amount').val();
        let agentname = "<?php echo e(\Auth::user()->name); ?>";
        let users = "<?php echo e(\Auth::user()->created_by_user); ?>";
        
        
        showhidediv('loadingdiv');
    
        if(checkdepositfields()){
            $.ajax({
                url: "<?php echo e(env('NOBS_IMAGES')); ?>withdraw.php",
                method: "POST",      // The HTTP method to use for the request
                dataType: "html",   // The type of data that you're exerciseecting back 	
                data: {  
                    __id__:__id__,
                    account_number:account_number,
                    account_type:account_type,
                    amount:amount,
                    agentname:agentname, 
                    users:users,
                    insertuseraccount:'insertuseraccount'
                },
                error: function () {
                    showhidediv('loadingdiv');
                    // A function to be called if the request fails.	
                    alert('kindly check your internet. it seems deposit taking too long.');				
                },
                beforeSend: function () {
        
                    // A function to be called if before the request is made.
                },
                success: function (response) {
                   
        if(response == 'ERROR'){
              alert('there was a problem connecting to server. Kindly re-connect again.');
              showhidediv('loadingdiv');
        }else{
            sndmsg(to,msg);
            alert(response);
    
           
            location.reload();
           
     
              showhidediv('loadingdiv');
           
    
        }
                    // A function to be called if the request succeeds.
                },
                timeout:4000,// set timeout to 4 seconds
                complete: function (response) {
        
                    // A function to be called when the request finishes
                }
            });
        }else{
            alert('Please enter valid amount');
            showhidediv('loadingdiv');
        }
         

    }else{
        alert('Your code appears to be wrong. Kindly check again or generate again.');
    }
    
    

}<?php /**PATH /Applications/MAMP/htdocs/nobsbackend/resources/views/layouts/datascripts.blade.php ENDPATH**/ ?>