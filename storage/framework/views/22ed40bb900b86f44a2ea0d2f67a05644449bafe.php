<?php echo e(Form::open(array('url'=>'lead','method'=>'post','enctype'=>'multipart/form-data'))); ?>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('name',__('Name'))); ?>

            <?php echo e(Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-6" style="display:none;">
        <div class="form-group">
            <?php echo e(Form::label('account',__('Account'))); ?>

            <?php echo Form::select('account', $account, null,array('class' => 'form-control','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('email',__('Email'))); ?>

            <?php echo e(Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('phone',__('Phone'))); ?>

            <?php echo e(Form::text('phone',null,array('class'=>'form-control','placeholder'=>__('Enter Phone'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-6" style="display:none;">
        <div class="form-group">
            <?php echo e(Form::label('title',__('Title'))); ?>

            <?php echo e(Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter Title')))); ?>

        </div>
    </div>
   
    <div class="col-6" style="display:none;">
        <div class="form-group">
            <?php echo e(Form::label('lead_address',__('Address'))); ?>

            <?php echo e(Form::text('lead_address',null,array('class'=>'form-control','placeholder'=>__('Address')))); ?>

        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <?php echo e(Form::label('lead_country',__('Country'))); ?>

                       <?php echo Form::select('lead_country', $countries, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')); ?> 
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <?php echo e(Form::label('lead_city',__('City'))); ?>

            <?php echo e(Form::text('lead_city',null,array('class'=>'form-control','placeholder'=>__('City')))); ?>

        </div>
    </div>
    <div class="col-3" style="display:none;">
        <div class="form-group">
            <?php echo e(Form::label('lead_state',__('State'))); ?>

            <?php echo e(Form::text('lead_state',null,array('class'=>'form-control','placeholder'=>__('State')))); ?>

        </div>
    </div>
    <div class="col-3" style="display :none;">
        <div class="form-group">
            <?php echo e(Form::label('lead_postalcode',__('Postal Code'))); ?>

            <?php echo e(Form::number('lead_postalcode',null,array('class'=>'form-control','placeholder'=>__('Postal Code')))); ?>

        </div>
    </div>
    
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('status',__('Lead Status'))); ?>

            <?php echo Form::select('status',$status, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')); ?>

        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('stage',__('Lead Response'))); ?>

            <?php echo Form::select('lead_temperature', $leadtemperature, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')); ?>

            <?php $__errorArgs = ['source'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="invalid-source" role="alert">
            <strong class="text-danger"><?php echo e($message); ?></strong>
            </span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('source',__('Source'))); ?>

            <?php echo Form::select('source', $leadsource, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')); ?>

        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('website',__('Referral URL (if any)'))); ?>

            <?php echo e(Form::text('website',null,array('class'=>'form-control','placeholder'=>__('Enter Website')))); ?>

        </div>
    </div>

    <div class="col-6" style="display:none;"> 
        <div class="form-group">
            <?php echo e(Form::label('opportunity_amount',__('Deal Amount'))); ?>

            <?php echo Form::text('opportunity_amount', null,array('class' => 'form-control')); ?>

        </div>
    </div>
    <?php if($type == 'campaign'): ?>
        <div class="col-6" style="display:none;">
            <div class="form-group">
                <?php echo e(Form::label('campaign',__('Campaign'))); ?>

                <?php echo Form::select('campaign', $campaign, $id,array('class' => 'form-control','data-toggle'=>'select')); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="col-6" style="display:none;">
            <div class="form-group">
                <?php echo e(Form::label('campaign',__('Campaign'))); ?>

                <?php echo Form::select('campaign', $campaign, null,array('class' => 'form-control','data-toggle'=>'select')); ?>

            </div>
        </div>
    <?php endif; ?>
    <div class="col-6" style="display:none;">
        <div class="form-group">
            <?php echo e(Form::label('industry',__('Industry'))); ?>

            <?php echo Form::select('industry', $industry, null,array('class' => 'form-control','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="col-12" style="display:none;">
        <div class="form-group">
            <?php echo e(Form::label('Description',__('Description'))); ?>

            <?php echo e(Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description')))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('Assign User',__('Assign Lead to User'))); ?>

            <?php echo Form::select('user', $user, null,array('class' => 'form-control','data-toggle'=>'select')); ?>

        </div>
    </div>
</div>
<div class="w-100 text-right">
    <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))); ?><?php echo e(Form::close()); ?>

</div>
</div>
<?php echo e(Form::close()); ?>

<style>

    .col-6,.col-12,.col-3{
        margin-top:10px;margin-bottom:10px;
    }
    .form-control{
        padding: 0.35rem 1.1;
    padding-top: 0.35rem;
    padding-right: 1.25rem;
    padding-bottom: 0.35rem;
    padding-left: 1.25rem;
    }
</style>
<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/lead/create.blade.php ENDPATH**/ ?>