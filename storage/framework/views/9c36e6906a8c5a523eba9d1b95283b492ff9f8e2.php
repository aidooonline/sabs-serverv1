<?php echo e(Form::open(array('url'=>'task','method'=>'post','enctype'=>'multipart/form-data'))); ?>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('name',__('Name'))); ?>

            <?php echo e(Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('stage',__('Task Stage'))); ?>

            <?php echo Form::select('stage', $stage, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')); ?>

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

            <?php echo Form::date('due_date', null,array('class' => 'form-control','required'=>'required')); ?>


        </div>
    </div>

    <div class="col-6" data-name="parent">
        <?php echo e(Form::label('parent',__('Assign'))); ?>

        <?php echo Form::select('parent', $parent, null,array('class' => 'form-control','data-toggle'=>'select','name'=>'parent','id'=>'parent','required'=>'required')); ?>

    </div>
    <div class="col-6" data-name="parent">
        <div class="form-group">
            <?php echo e(Form::label('parent_id',__('Assign Name'))); ?>

            <select class="form-control" data-toggle='select' name="parent_id" id="parent_id">

            </select>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('priority',__('Priority'))); ?>

            <?php echo Form::select('priority', $priority, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')); ?>

        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <?php echo e(Form::label('description',__('Description'))); ?>

            <?php echo e(Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description')))); ?>

        </div>
    </div>
    <div class="col-12 mb-3 field" data-name="attachments">
        <div class="attachment-upload">
            <div class="attachment-button">
                <div class="pull-left">
                    <?php echo e(Form::label('attachment',__('Attachment'))); ?>

                    <?php echo e(Form::file('attachment',array('class'=>'form-control'))); ?>

                </div>
            </div>
            <div class="attachments"></div>
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


<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/task/create.blade.php ENDPATH**/ ?>