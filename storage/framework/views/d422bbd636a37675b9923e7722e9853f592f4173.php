<?php if(\Auth::user()->type == 'owner' || \Auth::user()->type == 'Admin'): ?>
    <?php echo e(Form::open(array('url'=>'user','method'=>'post','enctype'=>'multipart/form-data'))); ?>

    <div class="row">

   
                
        <div class="col-12" >
            <div class="form-group">
                
                <input    id="created_by_user" name="created_by_user" class="form-control" type="text" value="" />

            </div>
        </div>

        <div class="col-12">
            <div class="form-group">
                <?php echo e(Form::label('name',__('User Name'))); ?>

                <?php echo e(Form::text('username',null,array('class'=>'form-control','placeholder'=>__('Enter User Name'),'required'=>'required'))); ?>

            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <?php echo e(Form::label('name',__('Name'))); ?>

                <?php echo e(Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))); ?>

            </div>
        </div>
        <div class="col-12" style="display:none;">
            <div class="form-group">
                <?php echo e(Form::label('name',__('Title'))); ?>

                <?php echo e(Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter Title')))); ?>

            </div>
        </div>

        <div class="col-12">
            <div class="form-group">
                <?php echo e(Form::label('name',__('Phone'))); ?>

                <?php echo e(Form::text('phone',null,array('class'=>'form-control','placeholder'=>__('Enter Phone'),'required'=>'required'))); ?>

            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <?php echo e(Form::label('name',__('Gender'))); ?>

                <?php echo Form::select('gender', $gender, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')); ?>

            </div>
        </div>
        <div class="col-12 p-0">
            <hr class="m-0 mb-3">
            <h6><?php echo e(__('Login Details')); ?></h6>
        </div>
        <div class="col-12">
            <div class="form-group">
                <?php echo e(Form::label('email',__('Email'))); ?>

                <?php echo e(Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email'),'required'=>'required'))); ?>

            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php echo e(Form::label('name',__('Password'))); ?>

                <?php echo e(Form::password('password',array('class'=>'form-control','placeholder'=>__('Enter Password'),'required'=>'required'))); ?>

            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <?php echo e(Form::label('user_roles',__('Roles'))); ?>

                <?php echo Form::select('user_roles', $roles, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')); ?>

            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <?php echo e(Form::label('name',__('Is Active'))); ?>

                <div>
                    <input type="checkbox" class="align-middle" name="is_active" checked>
                </div>
            </div>
        </div>
        
         
            
                 
        
        
        
        <div class="col-12 p-0">
            <hr class="m-0 mb-3">
            <h6><?php echo e(__('Avatar')); ?></h6>
        </div>
        <div class="col-12 mb-3 field" data-name="avatar">
            <div class="attachment-upload">
                <div class="attachment-button">
                    <div class="pull-left">
                        <?php echo e(Form::file('avatar',array('class'=>'form-control'))); ?>

                    </div>
                </div>
                <div class="attachment"></div>
            </div>
        </div>
        <div class="w-100 text-right">
            <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))); ?><?php echo e(Form::close()); ?>

        </div>
    </div>
    <?php echo e(Form::close()); ?>

<?php else: ?>
    <?php echo e(Form::open(array('url'=>'user','method'=>'post','enctype'=>'multipart/form-data'))); ?>

    <div class="form-group">
        <?php echo e(Form::label('name',__('User Name'))); ?>

        <?php echo e(Form::text('username',null,array('class'=>'form-control','placeholder'=>__('Enter User Name'),'required'=>'required'))); ?>

    </div>
    <div class="form-group">
        <?php echo e(Form::label('name',__('Name'),array('class'=>''))); ?>

        <?php echo e(Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter User Name'),'required'=>'required'))); ?>

    </div>
    <div class="form-group">
        <?php echo e(Form::label('email',__('Email'))); ?>

        <?php echo e(Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter User Email'),'required'=>'required'))); ?>

    </div>
    <div class="form-group">
        <?php echo e(Form::label('password',__('Password'))); ?>

        <?php echo e(Form::password('password',array('class'=>'form-control','placeholder'=>__('Enter User Password'),'required'=>'required','minlength'=>"6"))); ?>

    </div>
    <div class="modal-footer">
        <?php echo e(Form::submit(__('Create'),array('class'=>'btn btn-sm btn-primary rounded-pill'))); ?>

    </div>
    <?php echo e(Form::close()); ?>

<?php endif; ?>


<script>

$(function() {
  // Handler for .ready() called.
  setunique();
});


function setunique(){
    let uniqid = uuidv4();
$("#created_by_user").val(uniqid);
}

function uuidv4() {
  return ([1e7]+1e3+4e3+8e3+1e11).replace(/[018]/g, c =>
    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
  );
}
    </script><?php /**PATH /home/banqgego/public_html/nobs001/resources/views/user/create.blade.php ENDPATH**/ ?>