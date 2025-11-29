<?php echo e(Form::open(array('url'=>'report','method'=>'post','enctype'=>'multipart/form-data'))); ?>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('name',__('Name'))); ?>

            <?php echo e(Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('chart_type',__('Chart Type'))); ?>

            <?php echo Form::select('chart_type', $chart_type,null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('entity_type',__('Entity Type'))); ?>

            <?php echo Form::select('entity_type', $entity_type,null,array('class' => 'form-control ','name'=>'entity_type','id'=>'entity_type','data-toggle'=>'select','required'=>'required')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('group_by',__('Group By'))); ?>

            <select class="form-control select2" data-toggle="select" name="group_by" id="group_by">

            </select>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('Assign User',__('Assign User'))); ?>

            <?php echo Form::select('user', $user, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')); ?>

        </div>
    </div>
</div>
<div class="w-100 text-right">
    <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))); ?><?php echo e(Form::close()); ?>

</div>
</div>
<?php echo e(Form::close()); ?>

<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/report/create.blade.php ENDPATH**/ ?>