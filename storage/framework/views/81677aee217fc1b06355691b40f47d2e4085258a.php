<?php echo e(Form::open(array('url'=>'product','method'=>'post','enctype'=>'multipart/form-data'))); ?>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('name',__('Name'))); ?>

            <?php echo e(Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))); ?>

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
            <?php echo e(Form::label('category',__('Category'))); ?>

            <?php echo Form::select('category', $category, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('brand',__('Brand'))); ?>

            <?php echo Form::select('brand', $brand, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')); ?>


        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('price',__('Price'))); ?>

            <?php echo e(Form::text('price',null,array('class'=>'form-control','placeholder'=>__('Enter Price'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('tax', __('Tax'))); ?>

            <?php echo e(Form::select('tax[]', $tax,null, array('class' => 'form-control','data-toggle'=>'select','multiple'))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('part_number',__('Part Number'))); ?>

            <?php echo e(Form::text('part_number',null,array('class'=>'form-control','placeholder'=>__('Enter Part Number'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('weight',__('Weight'))); ?>

            <?php echo e(Form::text('weight',null,array('class'=>'form-control','placeholder'=>__('Enter Weight')))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('URL',__('URL'))); ?>

            <?php echo e(Form::text('URL',null,array('class'=>'form-control','placeholder'=>__('Enter URL'),'required'=>'required'))); ?>

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


<?php /**PATH /Applications/MAMP/htdocs/nobsbackend/resources/views/product/create.blade.php ENDPATH**/ ?>