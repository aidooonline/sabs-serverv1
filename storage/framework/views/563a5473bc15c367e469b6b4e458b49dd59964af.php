<style>
  .bgpurple {
      color: #807f81;


      background-color: #dbd8dd57 !important;
      font-weight: :normal !important;

  }

  .textcolor1 {
      color: #29B6F6;
      font-weight: :normal !importan;

  }
</style>

 
 
    <h4 style="width:100%;position:relative;padding-left:20px;" class="text-warning"> Requested Loans



    </h4>
 
    <a style="position:absolute;right:20px;top:40px;" href="<?php echo e(route('loanrequests.create')); ?>"  href="#"
    class="btn btn-purple  mr-1 btn-fab btn-sm">
    <i class="fa fa-plus"></i>
    </a>



   
 
 

  

 

  <?php $__currentLoopData = $loanrequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loanrequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <!-- TODAY -->
      
        <div class="displaydivs" id="todaydiv">
      
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      
            <div id="accountbtnpanel_<?php echo e($loanrequest->id); ?>" class="col-xl-4 col-lg-6 col-md-6 col-12">
                <div class="card bg-white pt-2">
                    <div class="card-body">
                        <div class="card-block pt-2 pb-0">
                            <ul style="border:0 !important;" class="list-group">
                                <li class="list-group-item"><span class="row  mt-1"><strong
                                            class="font-medium-5 mb-0 text-purple pl-0 ml-0">
                                            <?php echo e(number_format($loanrequest->amount, 2, '.', ',')); ?></strong></span></li>
                                <li class="list-group-item"> <span class="row mt-1">Customer: <span
                                            style="padding-left:10px;"
                                            class="textcolor1"><?php echo e($loanrequest->account_number); ?></span></span> </li>
                                <li class="list-group-item"> <span class="row mt-1">Loan: <span style="padding-left:10px;"
                                            class="textcolor1"><?php echo e($loanrequest->loan_name); ?></span></span> </li>
                                <li class="list-group-item"><span class="row mt-1">Purpose: <span class="textcolor1"
                                            style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->loan_purpose); ?></span>
                                </li>
                            </ul>
      
                            <div class="media-right text-left mr-2">
                                
                                <a href="<?php echo e(route('loanrequestdetail.edit',$loanrequest->id)); ?>" class="btn btn-light mr-1 btn-fab btn-sm">
                                    <i class="fa fa-pencil"></i>
                                  </a>
                                  
                                <a href="<?php echo e(env('BASE_URL')); ?>loanrequestdetail/detail/<?php echo e($loanrequest->id); ?>"
                                    class="btn btn-light mr-1 btn-fab btn-sm">
                                    <i class="fa fa-eye"></i>
                                </a>
      
                                
      
      
                            </div>
                        </div>
      
                    </div>
                </div>
            </div>
      
      
            <?php endif; ?>
      
        </div>
      
      
        <!-- END TODAY -->
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



 

 


  <style>
      ul.list-group {

          padding: 10px 10px;
      }



      .list-group .list-group-item {
          padding: 1px 2px;
          border: solid 0 !important;
      }


      .ghs {
          font-weight: normal !important;
          font-size: 13px;
      }

      .displaydivs {
          

      }

      .tabpanel, .tabpanel p{
        background-color:#ffffff !important;
        padding:10px 10px;
      }


      #thisweekdiv,
      #thismonthdiv,
      #thisyeardiv,
      #alltimediv {
          display: none;
      }
  </style>

  <script type="text/javascript">

      function getfilter(that) {
          switch (that) {
              case 'Today':

                  $('.displaydivs').hide();
                  $('#todaydiv').show(300);
                  $('#filtertext').html(that);
                  break;

              case 'This Week':
                  $('.displaydivs').hide();
                  $('#thisweekdiv').show(300);
                  $('#filtertext').html(that);

                  break;

              case 'This Month':
                  $('.displaydivs').hide();
                  $('#thismonthdiv').show(300);
                  $('#filtertext').html(that);

                  break;

              case 'This Year':
                  $('.displaydivs').hide();
                  $('#thisyeardiv').show(300);
                  $('#filtertext').html(that);

                  break;

              case 'All Time':
                  $('.displaydivs').hide();
                  $('#alltimediv').show(300);
                  $('#filtertext').html(that);

                  break;


          }
      }

      function getfilterbyagent(that) {

          if (that == 'agentid_agentallid12345') {
              showhidediv('loadingdiv');
              location.href = "<?php echo e(route('dashboard.index')); ?>";

          } else {
              showhidediv('loadingdiv');
              location.href = "<?php echo e(route('agentquerydashboard.index')); ?>/" + that;

          }
      }

  </script><?php /**PATH /home/banqgego/public_html/nobs/resources/views/loanrequests/savingspartial_alltotal.blade.php ENDPATH**/ ?>