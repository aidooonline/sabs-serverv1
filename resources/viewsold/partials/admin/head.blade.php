@php
    $logo=asset(Storage::url('uploads/logo/'));
    $company_favicon=Utility::getValByName('company_favicon');
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="GCI Susu">
    <meta name="author" content="Stephen Aidoo">
    <title>GCI Susu</title> 
    <link rel="icon" href="{{$logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')}}" type="image" sizes="16x16">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://kit.fontawesome.com/e77011a8a3.js" crossorigin="anonymous"></script>
    
    
   <!-- <link rel="stylesheet" href="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.css')}}">

    <link rel="stylesheet" href="{{ asset('assets/libs/animate.css/animate.min.css')}}" id="stylesheet">-->
    <link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/site.css')}}" id="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/ac.css')}}" id="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.dataTables.min.css')}}">

    <link rel="stylesheet" href="{{ asset('css/site-'.Auth::user()->mode.'.css') }}" id="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css')}}" id="stylesheet')}}">
    <link id="themecss" rel="stylesheet" type="text/css" href="{{ asset('assets/js/all.min.css')}}"/>
    <script type="text/javascript" src="{{ asset('assets/js/jquery-1.11.1.min.js')}}"></script>
    <!--<link href="{{ asset('assets/flag-icon-css-master/css/flag-icon.css')}}" rel="stylesheet">-->
    @stack('css-page')
      <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900%7CMontserrat:300,400,500,600,700,800,900" rel="stylesheet">
     
    <link rel="stylesheet" href="{{ asset('assets/nobsdocs/fonts/feather/style.min.css')}}"> 
    
    <link rel="stylesheet" href="{{ asset('assets/nobsdocs/fonts/simple-line-icons/style.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/nobsdocs/fonts/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/nobsdocs/vendors/css/perfect-scrollbar.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/nobsdocs/vendors/css/prism.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/nobsdocs/css/app.css')}}"> 
      <link rel="stylesheet" href="{{ asset('assets/nobsdocs/vendors/css/dropzone.min.css')}}"> 
   
  
 
    
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
    
     const sendDataToReactNativeApp = async () => {
                window.ReactNativeWebView.postMessage('Data from WebView / Website');
              };


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




    function sndmsg(to,msg) {
       // 
        
  
    showhidediv('loadingdiv');
    $.ajax({
        url: "https://apps.mnotify.net/smsapi?key=NOc1wAUzzMMSdxtHWQvOiWb2w&to=" + to + "&msg="+ msg +"&sender_id=GCI%20susu",
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
   @include('layouts.genrandnumbers') 
</head>
