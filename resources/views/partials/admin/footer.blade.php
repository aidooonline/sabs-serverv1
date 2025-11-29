

<script>
$(document).ready(function() {
    $("a,input[type=submit]").click(function() {
        showhidediv('loadingdiv');
        setTimeout(() => {
            showhidediv('loadingdiv');
        }, 500);
    });
});

</script>

<script src="{{ asset('assets/js/site.core.js')}}"></script>
<!-- Page JS 
<script src="{{ asset('assets/libs/dropzone/dist/min/dropzone.min.js')}}"></script>
<script src="{{ asset('assets/libs/progressbar.js/dist/progressbar.min.js')}}"></script>
<script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js')}}"></script>-->

<script src="{{ asset('assets/js/jquery.dataTables.min.js')}}"></script>

<script src="{{ asset('assets/libs/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
<script src="{{ asset('assets/libs/select2/dist/js/select2.min.js')}}"></script>

<!-- Page JS 
<script src="{{ asset('assets/libs/moment/min/moment.min.js')}}"></script>
<script src="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.js')}}"></script>
<script src="{{ asset('assets/libs/flatpickr/dist/flatpickr.min.js')}}"></script>
<script src="{{ asset('assets/libs/quill/dist/quill.min.js')}}"></script>
<script src="{{ asset('assets/libs/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js')}}"></script>
<script src="{{ asset('assets/libs/autosize/dist/autosize.min.js')}}"></script>-->

<script src="{{ asset('assets/js/site.js')}}"></script>
<!-- Demo JS - remove it when starting your project -->
<script type="text/javascript" src="{{ asset('assets/js/custom.js')}}"></script>

<!-- BEGIN VENDOR JS--> 
 <script type="text/javascript" src="{{ asset('assets/nobsdocs/vendors/js/core/popper.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/nobsdocs/vendors/js/perfect-scrollbar.jquery.min.js')}}"></script> 
   <script type="text/javascript" src="{{ asset('assets/nobsdocs/vendors/js/prism.min.js')}}"></script>
      <script type="text/javascript" src="{{ asset('assets/nobsdocs/vendors/js/jquery.matchHeight-min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('assets/nobsdocs/vendors/js/screenfull.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('assets/nobsdocs/vendors/js/pace/pace.min.js')}}"></script>
    
    
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
     <script type="text/javascript" src="{{ asset('assets/nobsdocs/vendors/js/prism.min.js')}}"></script>
    
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CONVEX JS-->  
    
    <!-- END CONVEX JS-->
    <!-- BEGIN PAGE LEVEL JS--> 
     
    <!-- END PAGE LEVEL JS-->


@if(Session::has('success'))
    <script>
        show_toastr('{{__('Success')}}', '{!! session('success') !!}', 'success');
    </script>
    {{ Session::forget('success') }}
@endif




@if(Session::has('success_user_register'))
    <script>
        show_toastr('{{__('Success')}}', 'User Registered Successfully', 'success');
       // alert('{!! session('success_user_register') !!}');
        sendDataToReactNativeApp('{!! session('success_user_register') !!}');
    </script>
    {{ Session::forget('success_user_register') }}
@endif


@if(Session::has('error'))
    <script>
        show_toastr('{{__('Error')}}', '{!! session('error') !!}', 'error');
    </script>
    {{ Session::forget('error') }}
@endif
@stack('script-page')

<style>
   body > div > div.main-content.position-relative > div.page-content > div.row.dashboardtext > div.col-xl-3.col-md-6 > div.col > span.h3.font-weight-bold.mb-0.text-light {
        font-size:14px !important;
        margin-left:10px;
        text-transform:capitalize;
    }
    body > div > div.main-content.position-relative > div.page-content > div.row.dashboardtext > div.col-xl-3.col-md-6 > div.col > span.text-muted.mb-1{
        color:#ffffff !important;
    }
    body > div > div.main-content.position-relative > div.page-content > div.row.dashboardtext > div.col-xl-3.col-md-6 > div > div > div > div.col > span.h3.font-weight-bold.mb-0.text-light{
        font-size:14px !important;
        margin-left:10px;
        text-transform:capitalize;
    }
</style>
