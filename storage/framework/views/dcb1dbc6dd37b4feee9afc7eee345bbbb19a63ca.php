<?php echo e(Form::open(array('url' => 'plan', 'enctype' => "multipart/form-data"))); ?>

<div class="row">
    <div class="form-group col-md-6">
        <?php echo e(Form::label('name',__('Name'))); ?>

        <?php echo e(Form::text('name',null,array('class'=>'form-control font-style','placeholder'=>__('Enter Plan Name'),'required'=>'required'))); ?>

    </div>
    <div class="form-group col-md-6">
        <?php echo e(Form::label('price',__('Price'))); ?>

        <?php echo e(Form::number('price',null,array('class'=>'form-control','placeholder'=>__('Enter Plan Price'),'step'=>'0.01'))); ?>

    </div>

    <div class="form-group col-md-6">
        <?php echo e(Form::label('max_user',__('Maximum Users'))); ?>

        <?php echo e(Form::number('max_user',null,array('class'=>'form-control','required'=>'required'))); ?>

        <span class="small"><?php echo e(__('Note: "-1" for Unlimited')); ?></span>
    </div>
    <div class="form-group col-md-6">
        <?php echo e(Form::label('max_account',__('Maximum Accounts'))); ?>

        <?php echo e(Form::number('max_account',null,array('class'=>'form-control','required'=>'required'))); ?>

        <span class="small"><?php echo e(__('Note: "-1" for Unlimited')); ?></span>
    </div>
    <div class="form-group col-md-6">
        <?php echo e(Form::label('max_contact',__('Maximum Contacts'))); ?>

        <?php echo e(Form::number('max_contact',null,array('class'=>'form-control','required'=>'required'))); ?>

        <span class="small"><?php echo e(__('Note: "-1" for Unlimited')); ?></span>
    </div>
   
    <div class="form-group col-md-6">
        <?php echo e(Form::label('duration', __('Duration'))); ?>

        <?php echo Form::select('duration', $arrDuration, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')); ?>

    </div>
    <div class="form-group col-md-12">
        <?php echo e(Form::label('image', __('Image'))); ?>

        <?php echo e(Form::file('image', array('class' => 'form-control'))); ?>

    </div>
    <div class="form-group col-md-12">
        <?php echo e(Form::label('description', __('Description'))); ?>

        <?php echo Form::textarea('description', null, ['class'=>'form-control','rows'=>'2']); ?>

    </div>
    <div class="modal-footer col-md-12">
        <?php echo e(Form::submit(__('Create'),array('class'=>'btn btn-sm btn-primary rounded-pill'))); ?>

    </div>
</div>
<?php echo e(Form::close()); ?>


<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/plan/create.blade.php ENDPATH**/ ?>