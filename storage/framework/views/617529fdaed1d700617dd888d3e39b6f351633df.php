<?php echo e(Form::open(array('url'=>'opportunities','method'=>'post','enctype'=>'multipart/form-data'))); ?>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('name',__('Name'))); ?>

            <?php echo e(Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))); ?>

        </div>
    </div>
    <?php if($type == 'account'): ?>
        <div class="col-6">
            <div class="form-group">
                <?php echo e(Form::label('account',__('Account Name'))); ?>

                <?php echo Form::select('account', $account_name, $id,array('class' => 'form-control ','data-toggle'=>'select')); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="col-6">
            <div class="form-group">
                <?php echo e(Form::label('account',__('Account'))); ?>

                <?php echo Form::select('account', $account_name, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

            </div>
        </div>
    <?php endif; ?>
    <?php if($type == 'contact'): ?>
        <div class="col-6" style="display:none; ">
            <div class="form-group">
                <?php echo e(Form::label('contact',__('Contact'))); ?>

                <?php echo Form::select('contact', $contact, $id,array('class' => 'form-control ','data-toggle'=>'select')); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="col-6" style="display:none;">
            <div class="form-group">
                <?php echo e(Form::label('contact',__('Contact'))); ?>

                <?php echo Form::select('contact', $contact, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

            </div>
        </div>
    <?php endif; ?>
    <?php if($type == 'campaign'): ?>
        <div class="col-6">
            <div class="form-group">
                <?php echo e(Form::label('campaign_id',__('Campaign'))); ?>

                <?php echo Form::select('campaign_id', $campaign_id,$id,array('class' => 'form-control ','data-toggle'=>'select')); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="col-6">
            <div class="form-group">
                <?php echo e(Form::label('campaign',__('Campaign'))); ?>

                <?php echo Form::select('campaign', $campaign_id, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

            </div>
        </div>
    <?php endif; ?>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('stage',__('Deal Stage'))); ?>

            <?php echo Form::select('stage', $opportunities_stage, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('amount',__('Amount'))); ?>

            <?php echo Form::number('amount', null,array('class' => 'form-control ','placeholder'=>__('Enter Amount'),'required'=>'required')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('probability',__('Probability'))); ?>

            <?php echo e(Form::number('probability',null,array('class'=>'form-control','placeholder'=>__('Enter Probability'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('close_date',__('Close Date'))); ?>

            <?php echo e(Form::date('close_date',null,array('class'=>'form-control','placeholder'=>__('Enter Title'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('lead_source',__('Lead Source'))); ?>

            <?php echo Form::select('lead_source', $leadsource, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <?php echo e(Form::label('Description',__('Description'))); ?>

            <?php echo e(Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description')))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('Assign User',__('Assign User'))); ?>

            <?php echo Form::select('user', $user, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

        </div>
    </div>
</div>
<div class="w-100 text-right">
    <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))); ?><?php echo e(Form::close()); ?>

</div>
</div>
<?php echo e(Form::close()); ?>

<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/opportunities/create.blade.php ENDPATH**/ ?>