<?php echo e(Form::open(array('url'=>'account','method'=>'post','enctype'=>'multipart/form-data'))); ?>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('name',__('Name'))); ?>

            <?php echo e(Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))); ?>

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
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('website',__('Website'))); ?>

            <?php echo e(Form::text('website',null,array('class'=>'form-control','placeholder'=>__('Website')))); ?>

        </div>
    </div>
    <div style="display:none;" class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('billingaddress',__('Billing Address'))); ?>

            <a class="btn btn-xs small btn-primary rounded-pill mr-auto float-right p-1 px-4" id="billing_data" data-toggle="tooltip" data-placement="top" title="Same As Billing Address"><i class="fas fa-copy"></i></a>
            <span class="clearfix"></span>
            <?php echo e(Form::text('billing_address',null,array('class'=>'form-control','placeholder'=>__('Billing Address')))); ?>

        </div>
    </div>
    <div style="display:none;" class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('shippingaddress',__('Shipping Address'))); ?>

            <?php echo e(Form::text('shipping_address',null,array('class'=>'form-control','placeholder'=>__('Shipping Address')))); ?>

        </div>
    </div>
    <div style="display:none;" class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('billing_city',null,array('class'=>'form-control','placeholder'=>__('Billing City')))); ?>

        </div>
    </div>
    <div style="display:none;" class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('billing_state',null,array('class'=>'form-control','placeholder'=>__('Billing State')))); ?>

        </div>
    </div>
    <div style="display:none;" class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('shipping_city',null,array('class'=>'form-control','placeholder'=>__('Shipping City'),))); ?>

        </div>
    </div>
    <div style="display:none;" class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('shipping_state',null,array('class'=>'form-control','placeholder'=>__('Shipping State')))); ?>

        </div>
    </div>
    <div style="display:none;" class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('billing_country',null,array('class'=>'form-control','placeholder'=>__('Billing Country')))); ?>

        </div>
    </div>
    <div style="display:none;" class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('billing_postalcode',null,array('class'=>'form-control','placeholder'=>__('Billing Postal Code')))); ?>

        </div>
    </div>
    <div style="display:none;" class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('shipping_country',null,array('class'=>'form-control','placeholder'=>__('Shipping Country')))); ?>

        </div>
    </div>
    <div style="display:none;" class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('shipping_postalcode',null,array('class'=>'form-control','placeholder'=>__('Shipping Postal Code')))); ?>

        </div>
    </div>
    <div class="col-12" style="display:none;">
      <hr class="mt-2 mb-2">
        <h6><?php echo e(__('Detail')); ?></h6>
    </div>

    <div class="col-6" style="display:none;">
        <div class="form-group">
            <?php echo e(Form::label('type',__('Type'))); ?>

            <?php echo Form::select('type', $accountype, null,array('class' => 'form-control','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('Industry',__('Industry'))); ?>

            <?php echo Form::select('industry', $industry, null,array('class' => 'form-control','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('document_id',__('Document'))); ?>

            <?php echo Form::select('document_id', $document_id, null,array('class' => 'form-control','data-toggle'=>'select')); ?>

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

        <?php echo Form::select('user', $user, null,array('class' => 'form-control','data-toggle'=>'select')); ?>

    </div>
</div>
</div>
<div class="w-100 text-right">
    <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))); ?><?php echo e(Form::close()); ?>

</div>
</div>
<?php echo e(Form::close()); ?>

<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/account/create.blade.php ENDPATH**/ ?>