<?php $__env->startSection('title'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-btn'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<?php echo $__env->make('layouts.inlinecss', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row dashboardtext" style="padding-bottom:100px;">
    <h4 class="card-title">
        <span type="text" class="text-info" id="ledgername"><?php echo e($ledgername); ?></span></label> > <?php echo e($parentname); ?>

        (<?php echo e($ledgergeneralsub_sum); ?>)
    </h4>

    <div id="mainsearchdiv">



        <a href="#" onclick="$('#ledgerdetailcreateform').modal('show');" style="position:absolute;right:15px;top:8px;"
            href="#" class="btn btn-purple  mr-1 btn-fab btn-sm">
            <i class="fa fa-plus"></i>
        </a>

        <table id="accountstable" class="table listdiv2 stripes">
            <thead>
                <tr>
                    <th style="width:49% !important;">Account</th>
                    <th style="width:49% !important;">Debit</th>
                    <th style="width:49% !important;">Credit</th>

                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $ledgerdetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



                <tr>


                    <td style="width:49% !important;word-wrap:break-word;border-bottom:solid 1px #e2dede !important;">
                        <?php echo e($item->name); ?>


                    </td>


                    <td style="width:49% !important;word-wrap:break-word;border-bottom:solid 1px #e2dede !important;">
                        <?php if($item->dr_or_cr == 1): ?>
                        <b><?php echo e($item->dr_amount); ?></b>
                        <?php endif; ?>

                    </td>
                    <td style="width:49% !important;word-wrap:break-word;border-bottom:solid 1px #e2dede !important;">
                        <?php if($item->dr_or_cr == 0): ?>
                        <b><?php echo e($item->cr_amount); ?></b>
                        <?php endif; ?>

                    </td>



                </tr>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </tbody>
        </table>


        <div class="modal fade" id="ledgerdetailcreateform" tabindex="-1" role="dialog"
            aria-labelledby="ledgerdetailcreateform" aria-hidden="true">
            <div class="modal-dialog modal-dialog-slideout modal-lg" role="document">
                <div style="padding-bottom:0 !important;margin-bottom:0 !important;" class="modal-content">

                    <div class="modal-body" style="padding:5px 5px !important;margin-bottom:0 !important;">

                        <div
                            style="padding-top:10px;margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;border-radius:10px 10px;">

                            <?php echo e(Form::open(array('id'=>'mysubmitform','url'=>'ledgergeneralsub/storesubledger/','method'=>'post','enctype'=>'multipart/form-data'))); ?>

                            <?php echo csrf_field(); ?>

                            <button type="button" style="position:absolute;right:0;top:2px;"
                                class="btn btn-danger mr-1 btn-fab btn-sm" data-dismiss="modal">x</button>
                               
                            <div>
                                <div style="display:none;" class="col-12">
                                    <div class="form-group">
                                        <label>Sub Ledger Name</label>
                                        <input id="dr_name" name="dr_name" class="form-control" type="text" value="" />
                                        <input id="cr_name" name="cr_name" class="form-control" type="text" value="" />
                                        <input type="hidden" name="myselected_ledger" id="myselected_ledger"
                                            value="<?php echo e($ledgername); ?>" />

                                        <input id="name" name="name" class="form-control" type="text"
                                            value="<?php echo e($parentname); ?>" />
                                        

                                        <input type="text" id="ac_type" name="ac_type" class="form-control"
                                            value="<?php echo e($ledgertype); ?>" readonly />
                                        <input type="text" id="amount" name="amount" class="form-control" value="" />
                                        <input type="text" id="parent_id" name="parent_id" class="form-control"
                                            value="<?php echo e($parentid); ?>" readonly />
                                        <input type="text" id="trans_id" name="trans_id" class="form-control" value=""
                                            readonly />
                                        <input type="number" id="actual_value" name="actual_value" class="form-control"
                                            value="" />
                                            <input type="number" id="isdisbursement" name="isdisbursement" class="form-control"
                                            value="0" />
                                    </div>
                                </div>



                                <div class="col-12">

                                    <div class="form-group">
                                        <label>Transaction Type</label>
                                        <select onchange="changedisplaymode_cr_dr(this)" class="form-control" data-togle="select" name="debitorcredit_id"
                                             id="debitorcredit_id">
                                            <option value="Debit">Debit</option>
                                            <option value="Credit">Credit</option>
                                        </select>

                                    </div>
                                    
                                    <div class="dropdown form-group">
                                        <label id="debit_div">Debit Account</label>
                                    <label style="display:none;" id="credit_div">Credit Account</label>
                                        
                                        <button cat="" value="empty" id="myFunctionid" name="myFunctionid"
                                            onclick="event.preventDefault();myFunction()"
                                            class="btn btn-light btn-round dropdown-toggle mr-1 form-control" style="margin-bottom:6px !important;text-align:left;">  
                                           
                                            <span id="selectdbaccountlabel">Select Account</span></button>
                                        <div id="myDropdown" class="dropdown-content">
                                            <input type="text" placeholder="Search.." id="myInput"
                                                onkeyup="filterFunction()">
                                        <?php $__currentLoopData = $ledger_type_list_query2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledgerlist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="#" cat="<?php echo e($ledgerlist->name); ?>" onclick="selectme(this)" id="selectmeid_<?php echo e($ledgerlist->id); ?>"><span class="text-info"><?php echo e($ledgerlist->acname); ?></span> > <?php echo e($ledgerlist->name); ?></a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            
                                        </div>
                                    </div>
 

                                    <div style="display:none" class="form-group">
                                        <label>Debit Account</label>
                                        <?php echo e(Form::select('dr_account',
                                        $ledger_type_list_query,null,array('onchange'=>'dr_getselectedtexter()','data-toggle'=>'select','id'=>'dr_account'))); ?>

                                    </div>

                                    <div style="display:none" class="form-group">
                                        <label>Credit Account</label>
                                        <?php echo e(Form::select('cr_account',
                                        $ledger_type_list_query,null,array('onchange'=>'cr_getselectedtexter()','data-toggle'=>'select','id'=>'cr_account'))); ?>

                                    </div>

                                    <div style="display:none;" class="form-group">
                                        <label>Amount</label>
                                        <input type="number" id="dr_amount" name="dr_amount" class="form-control" />
                                    </div>
                                </div>



                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Amount</label>
                                        <input type="number" id="cr_amount" name="cr_amount" class="form-control"
                                            value="" />
                                    </div>
                                </div>


                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <?php echo e(Form::textarea('description',null,array('rows'=>'1','class'=>'form-control','max'=>'200'))); ?>

                                    </div>
                                </div>


                                <div class="col-12">
                                    <div class="form-group">
                                        <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-sm btn-purple rounded-pill
                                        mr-auto','id'=>'submitdata'))); ?><?php echo e(Form::close()); ?>

                                    </div>
                                </div>
                            </div>

                            <?php echo e(Form::close()); ?>


                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>



</div>


</div>

<?php echo $__env->make('ledgergeneralsub.style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
 
<?php echo $__env->make('ledgergeneralsub.script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->make("layouts.modalview1", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->make("layouts.modalscripts", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/banqgego/public_html/nobs001/resources/views/ledgergeneralsub/create.blade.php ENDPATH**/ ?>