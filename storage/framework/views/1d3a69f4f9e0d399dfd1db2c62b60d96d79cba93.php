<?php echo e(Form::open(array('route' => ['form.field.store',$formbuilder->id]))); ?>

<div class="row" id="frm_field_data">
    <div class="col-12 form-group">
        <?php echo e(Form::label('name', __('Question Name'),['class'=>'form-control-label'])); ?>

        <?php echo e(Form::text('name[]', '', array('class' => 'form-control','required'=>'required'))); ?>

    </div>
    <div class="col-12 form-group">
        <?php echo e(Form::label('type', __('Type'),['class'=>'form-control-label'])); ?>

        <?php echo e(Form::select('type[]', $types,null, array('class' => 'form-control','data-toggle'=>'select','required'=>'required'))); ?>

    </div>
    <div class="modal-footer">
        <?php echo e(Form::submit(__('Create'),array('class'=>'btn btn-sm btn-primary rounded-pill'))); ?>

    </div>
</div>
<?php echo e(Form::close()); ?>

<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/form_builder/field_create.blade.php ENDPATH**/ ?>