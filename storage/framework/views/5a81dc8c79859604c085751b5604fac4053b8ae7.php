<?php echo e(Form::open(array('url'=>'invoice','method'=>'post','enctype'=>'multipart/form-data'))); ?>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('name',__('Name'))); ?>

            <?php echo e(Form::text('name',null,array('id'=>'name','class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))); ?>

        </div>
    </div>
    <?php if($type == 'salesorder'): ?>
        <div class="col-6">
            <div class="form-group">
                <?php echo e(Form::label('salesorder',__('Sales Orders'))); ?>

                <?php echo Form::select('salesorder', $salesorder,$id,array('id'=>'salesorder','class' => 'form-control','data-toggle'=>'select')); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="col-6">
            <div class="form-group">
                <?php echo e(Form::label('salesorder',__('Sales Orders'))); ?>

                <?php echo Form::select('salesorder', $salesorder, null,array('id'=>'salesorder','class' => 'form-control','data-toggle'=>'select')); ?>

            </div>
        </div>
    <?php endif; ?>
    <?php if($type == 'quote'): ?>
        <div class="col-6">
            <div class="form-group">
                <?php echo e(Form::label('quote',__('Quote'))); ?>

                <?php echo Form::select('quote', $quote, $id,array('class' => 'form-control','data-toggle'=>'select')); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="col-6">
            <div class="form-group">
                <?php echo e(Form::label('quote',__('Quote'))); ?>

                <?php echo Form::select('quote', $quote, null,array('class' => 'form-control','data-toggle'=>'select')); ?>

            </div>
        </div>
    <?php endif; ?>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('opportunity',__('opportunity'))); ?>

            <?php echo Form::select('opportunity', $opportunities, null,array('id'=>'opportunity','class' => 'form-control','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('status',__('Status'))); ?>

            <?php echo Form::select('status', $status, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('account',__('Account'))); ?>

            <?php echo e(Form::text('account',null,array('id'=>'account_name','class'=>'form-control','placeholder'=>__('Enter account'),'disabled'))); ?>

            <input type="hidden" name="account" id="account_id">
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('amount',__('Amount'))); ?>

            <?php echo e(Form::number('amount',null,array('id'=>'amount','class'=>'form-control','placeholder'=>__('Enter Amount')))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('date_quoted',__('Date Quoted'))); ?>

            <?php echo e(Form::date('date_quoted',null,array('class'=>'form-control','required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <?php echo e(Form::label('quote_number',__('Quote Number'))); ?>

            <?php echo e(Form::text('quote_number',null,array('class'=>'form-control','placeholder'=>__('Enter Quote Number'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('billing_address',__('Billing Address'))); ?>

            <a class="btn btn-xs small btn-primary rounded-pill mr-auto float-right p-1 px-4" id="billing_data" data-toggle="tooltip" data-placement="top" title="Same As Billing Address"><i class="fas fa-copy"></i></a>
            <span class="clearfix"></span>
            <?php echo e(Form::text('billing_address',null,array('id'=>'billing_address','class'=>'form-control','placeholder'=>__('Billing Address'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('shipping_address',__('Shipping Address'))); ?>

            <?php echo e(Form::text('shipping_address',null,array('id'=>'shipping_address','class'=>'form-control','placeholder'=>__('Shipping Address'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('billing_city',null,array('id'=>'billing_city','class'=>'form-control','placeholder'=>__('Billing city'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('billing_state',null,array('id'=>'billing_state','class'=>'form-control','placeholder'=>__('Billing State'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('shipping_city',null,array('id'=>'shipping_city','class'=>'form-control','placeholder'=>__('Shipping City'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('shipping_state',null,array('id'=>'shipping_state','class'=>'form-control','placeholder'=>__('Shipping State'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('billing_country',null,array('id'=>'billing_country','class'=>'form-control','placeholder'=>__('Billing Country'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('billing_postalcode',null,array('id'=>'billing_postalcode','class'=>'form-control','placeholder'=>__('Billing Postal Code'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('shipping_country',null,array('id'=>'shipping_country','class'=>'form-control','placeholder'=>__('Shipping Country'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <?php echo e(Form::text('shipping_postalcode',null,array('id'=>'shipping_postalcode','class'=>'form-control','placeholder'=>__('Shipping Postal Code'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('billing_contact',__('Billing Contact'))); ?>

            <?php echo Form::select('billing_contact', $contact, null,array('class' => 'form-control','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('shipping_contact',__('Shipping Contact'))); ?>

            <?php echo Form::select('shipping_contact', $contact, null,array('class' => 'form-control','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('tax',__('Tax'))); ?>

            <?php echo e(Form::select('tax[]', $tax,null, array('class' => 'form-control','data-toggle'=>'select','data-toggle'=>'select','multiple'))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('shipping_provider',__('Shipping Provider'))); ?>

            <?php echo Form::select('shipping_provider', $shipping_provider, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')); ?>

        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <?php echo e(Form::label('description',__('Description'))); ?>

            <?php echo e(Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description')))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('Assign User',__('Assign User'))); ?>

            <?php echo Form::select('user', $user, null,array('class' => 'form-control','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="w-100 text-right">
        <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))); ?><?php echo e(Form::close()); ?>

    </div>
</div>
</div>
<?php echo e(Form::close()); ?>


<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/invoice/create.blade.php ENDPATH**/ ?>