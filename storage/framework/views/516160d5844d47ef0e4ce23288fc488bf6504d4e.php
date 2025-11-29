<?php echo e(Form::open(array('url'=>'meeting','method'=>'post','enctype'=>'multipart/form-data'))); ?>

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

            <?php echo Form::select('status', $status, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('start_date',__('Start Date'))); ?>

            <?php echo Form::date('start_date', null,array('class' => 'form-control','required'=>'required')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('end_date',__('End Date'))); ?>

            <?php echo Form::date('end_date', null,array('class' => 'form-control','required'=>'required')); ?>

        </div>
    </div>
    <div class="col-6" data-name="parent">
        <?php echo e(Form::label('parent',__('Parent'))); ?>

        <?php echo Form::select('parent', $parent, null,array('class' => 'form-control ','data-toggle'=>'select','name'=>'parent','id'=>'parent','required'=>'required')); ?>

    </div>
    <div class="col-6" data-name="parent">
        <div class="form-group">
            <?php echo e(Form::label('parent_id',__('Parent User'))); ?>

            <select class="form-control" data-toggle='select' name="parent_id" id="parent_id">

            </select>
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

            <?php echo Form::select('user', $user, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')); ?>

        </div>
    </div>
    <div class="col-12">
        <hr class="mt-2 mb-2">
        <h6><?php echo e(__('Attendees')); ?></h6>
    </div>

    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('attendees_user',__('Attendees User'))); ?>

            <?php echo Form::select('attendees_user', $user, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('attendees_contact',__('Attendees Contact'))); ?>

            <?php echo Form::select('attendees_contact', $attendees_contact, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('attendees_lead',__('Attendees Lead'))); ?>

            <?php echo Form::select('attendees_lead', $attendees_lead, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="w-100 text-right">
        <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))); ?><?php echo e(Form::close()); ?>

    </div>
</div>
</div>
<?php echo e(Form::close()); ?>


<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/meeting/create.blade.php ENDPATH**/ ?>