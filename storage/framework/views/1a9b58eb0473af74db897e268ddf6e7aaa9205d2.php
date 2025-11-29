<?php echo e(Form::open(array('url'=>'document','method'=>'post','enctype'=>'multipart/form-data'))); ?>

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
                <?php echo e(Form::label('account',__('Account'))); ?>

                <?php echo Form::select('account', $account, $id,array('class' => 'form-control ','data-toggle'=>'select')); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="col-6">
            <div class="form-group">
                <?php echo e(Form::label('account',__('Account'))); ?>

                <?php echo Form::select('account', $account, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

            </div>
        </div>
    <?php endif; ?>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('folder',__('Folder'))); ?>

            <?php echo Form::select('folder', $folder, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('type',__('Type'))); ?>

            <?php echo Form::select('type', $types, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <?php echo e(Form::label('opportunities',__('Opportunities'))); ?>

            <?php echo Form::select('opportunities', $opportunities, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

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
            <?php echo e(Form::label('publish_date',__('Publish Date'))); ?>

            <?php echo Form::date('publish_date', null,array('class' => 'form-control','required'=>'required')); ?>

        </div>
    </div>
    <div class="col-6" style="display:none">
        <div class="form-group">
            <?php echo e(Form::label('expiration_date',__('Expiration Date'))); ?>

            <?php echo Form::date('expiration_date', null,array('class' => 'form-control')); ?>

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

            <?php echo Form::select('user', $user, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

        </div>
    </div>
    <div class="w-100 text-right">
        <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))); ?><?php echo e(Form::close()); ?>

    </div>
</div>
</div>
<?php echo e(Form::close()); ?>


<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/document/create.blade.php ENDPATH**/ ?>