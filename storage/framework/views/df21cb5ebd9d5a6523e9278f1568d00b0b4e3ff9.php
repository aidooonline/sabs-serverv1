<?php
    $logo=asset(Storage::url('uploads/logo/'));
    $company_favicon=Utility::getValByName('company_favicon');
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="GCI Susu">
    <meta name="author" content="Stephen Aidoo">
    <title>GCI Susu</title> 
    <link rel="icon" href="<?php echo e($logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')); ?>" type="image" sizes="16x16">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <script src="https://kit.fontawesome.com/e77011a8a3.js" crossorigin="anonymous"></script>
    
    
   <!-- <link rel="stylesheet" href="<?php echo e(asset('assets/libs/fullcalendar/dist/fullcalendar.min.css')); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/animate.css/animate.min.css')); ?>" id="stylesheet">-->
    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/select2/dist/css/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/site.css')); ?>" id="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ac.css')); ?>" id="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/jquery.dataTables.min.css')); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('css/site-'.Auth::user()->mode.'.css')); ?>" id="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css')); ?>" id="stylesheet')}}">
    <link id="themecss" rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/js/all.min.css')); ?>"/>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/jquery-1.11.1.min.js')); ?>"></script>
    <!--<link href="<?php echo e(asset('assets/flag-icon-css-master/css/flag-icon.css')); ?>" rel="stylesheet">-->
    <?php echo $__env->yieldPushContent('css-page'); ?>
      <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900%7CMontserrat:300,400,500,600,700,800,900" rel="stylesheet">
     
    <link rel="stylesheet" href="<?php echo e(asset('assets/nobsdocs/fonts/feather/style.min.css')); ?>"> 
    
    <link rel="stylesheet" href="<?php echo e(asset('assets/nobsdocs/fonts/simple-line-icons/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/nobsdocs/fonts/font-awesome/css/font-awesome.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/nobsdocs/vendors/css/perfect-scrollbar.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/nobsdocs/vendors/css/prism.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/nobsdocs/css/app.css')); ?>"> 
      <link rel="stylesheet" href="<?php echo e(asset('assets/nobsdocs/vendors/css/dropzone.min.css')); ?>"> 
   
  
 
    
    <style type="text/css">

#mainsearchdiv{
    padding-top:40px;
      margin-right:1%;
      margin-left:1%;
  width:98%;
    position:relative;
       
}


.card-title{
    color:#ffffff !important;
    margin-left:40px;
}

#searchoptionsdiv label{
    color:#fff !important;
}

.mintd{
    width:25% !important;
}

.listdiv{
    height:auto !important;
}
.listdiv2, .listdiv2 table,listdiv2 table td{
    background-color:#f4e9f7 !important;
}

.smallspan{
    
    font-size:9px; 
    color:#878787 !important;
    text-align:center ;
}

.profile-tab-list li a{
     color:#444 !important;
     padding:5px 10px !important; 
}

.profile-tab-list li{
     padding:5px 10px !important; 
    
}

        </style>
  
<script type="text/javascript">
    $(document).ready(function(){
        $('#upload').change(function(){
            
    var input = this;
    var url = $(this).val();
    var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
    if (input.files && input.files[0]&& (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) 
     {
        var reader = new FileReader();

        reader.onload = function (e) {
           $('#img').attr('src', e.target.result);
        }
       reader.readAsDataURL(input.files[0]);
    }
    else
    {
      $('#img').attr('src', '/assets/no_preview.png');
    }
  });
    });


    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
            reader.onload = function (e) {
                $('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }


function copyelement(id) {
  /* Get the text field */
  var copyText = document.getElementById(id);

  /* Select the text field */
  copyText.select();
  copyText.setSelectionRange(0, 99999); /* For mobile devices */

   /* Copy the text inside the text field */
  navigator.clipboard.writeText(copyText.value);

   if (typeof(Storage) !== "undefined") {
  // Code for localStorage/sessionStorage.
} else {
  // Sorry! No Web Storage support..
}
}


function BtPrint(prn){
  pprintdata(prn);
}

let theprintabledata ='';

function initiateprint(){
   
        pprintdata(theprintabledata);
   
}




 let printerdata ='';
              
 const printDataToReactNativeApp = async () => {
    window.ReactNativeWebView.postMessage('toprint_____' + printerdata);
 };
              
function pprintdata(mypprintdata){
   
    printerdata = mypprintdata;
    printDataToReactNativeApp();
                  
}
              

//onclick="BtPrint(document.getElementById('pre_print').innerText)"




function printdeposit(microfinancename,transname,acc_no,customer_name,amount,balance,currency){
    BtPrint(printtransaction(microfinancename,transname,acc_no,customer_name,amount,balance,currency));
}

function printwithdrawal(microfinancename,transname,acc_no,customer_name,amount,balance,currency){
    BtPrint(printtransaction(microfinancename,transname,acc_no,customer_name,amount,balance,currency));
}

function printloan_repayment(microfinancename,transname,acc_no,customer_name,amount,balance,currency){
   
    BtPrint(printtransaction(microfinancename,transname,acc_no,customer_name,amount,balance,currency));
}



function printtransaction(name_of_system,transaction_name,account_no,customer_name,amount,balance,currency){
return `
--------------------------------
     `+name_of_system+`
-------------------------------- 
TRANSACTION: `+transaction_name+`

ACCOUNT NO.:`+account_no+` 

NAME:`+customer_name+`

AMOUNT:`+amount+`
********************************
BALANCE `+currency+` `+balance+`
--------------------------------
`;
}


function asktoprintreceipt(){
    //if(prompt())
}


 

function returndate(){
    let options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    let todaydate =   formatDate_today();
    todaydate =  todaydate.toLocaleDateString("en-UK", options);
    return todaydate;
}

function formatDate_today() {
let date1 = new Date();
  return date1.getFullYear() + '-' +
    (date1.getMonth() < 9 ? '0' : '') + (date1.getMonth()+1) + '-' +
    (date1.getDate() < 10 ? '0' : '') + date1.getDate();
}




function sndmsg(to,msg,toprint=false) {
    
   
            
            //check for printing;
            if(toprint==true){
               // initiateprint(msg);
               let thetitle = 'GCI SUSU TRANSACTION<br/><br/><hr>';
               theprintabledata = thetitle + msg + '<br/><br/>' + 'Agent: ' + '<?php echo e(\Auth::user()->name); ?>' + '<br/><br/> Office No. 0559303660 <br/><br/>';
                 $('#printreceipt').modal('show');
            }
            
       // 
        
 showhidediv('loadingdiv');
    // https://smsc.hubtel.com/v1/messages/send?clientsecret=fjiomwsc&clientid=fijhmesy&from=GCIsusu&to=233542148020&content=This+Is+A+Test+Message
        //curl -i -X GET \
  //'https://devp-sms03726-api.hubtel.com/v1/messages/send?clientid=string&clientsecret=string&from=string&to=string&content=string
  let myurl = "https://smsc.hubtel.com/v1/messages/send?clientsecret=fjiomwsc&clientid=fijhmesy&from=GCIsusu&to=" + to + "&content=" + msg;
    showhidediv('loadingdiv');
    $.ajax({
      url: "https://apps.mnotify.net/smsapi?key=NOc1wAUzzMMSdxtHWQvOiWb2w&to=" + to + "&msg="+ msg +"&sender_id=GCI%20susu",
       //url:myurl,
        method: "GET",      // The HTTP method to use for the request
        dataType: "json" ,
        error: function () {

            // A function to be called if the request fails.					
        },
        beforeSend: function () {

            // A function to be called if before the request is made.
        },
        success: function (response) {
            
            
            showhidediv('loadingdiv');

            // A function to be called if the request succeeds.
        },
        complete: function (response) {

            // A function to be called when the request finishes
        }
    });
}

// Create our number formatter.
var formatter = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'GHS',

  // These options are needed to round to whole numbers if that's what you want.
  //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
  //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
});

 /* $2,500.00 */
 
  
  
 </script>
 
  
  
  
  
 
 
   <?php echo $__env->make('layouts.genrandnumbers', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
</head>
<?php /**PATH /home/banqgego/public_html/nobs001/resources/views/partials/admin/head.blade.php ENDPATH**/ ?>