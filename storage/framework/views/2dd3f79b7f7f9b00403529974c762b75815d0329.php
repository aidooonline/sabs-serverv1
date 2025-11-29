<?php echo e(Form::open(array('url' => 'coupon','method' =>'post'))); ?>

<div class="row">
    <div class="form-group col-md-12">
        <?php echo e(Form::label('name',__('Name'))); ?>

        <?php echo e(Form::text('name',null,array('class'=>'form-control font-style','required'=>'required'))); ?>

    </div>

    <div class="form-group col-md-6">
        <?php echo e(Form::label('discount',__('Discount'))); ?>

        <?php echo e(Form::number('discount',null,array('class'=>'form-control','required'=>'required','step'=>'0.01'))); ?>

        <span class="small"><?php echo e(__('Note: Discount in Percentage')); ?></span>
    </div>
    <div class="form-group col-md-6">
        <?php echo e(Form::label('limit',__('Limit'))); ?>

        <?php echo e(Form::number('limit',null,array('class'=>'form-control','required'=>'required'))); ?>

    </div>
    <div class="form-group">
        <div class="row">
            <div class="btn-group btn-group-toggle btn-sm" data-toggle="buttons">
                <label class="btn btn-primary btn-sm active code">
                    <input type="radio" class="icon-input"  name="icon-input" id="radioButton2" value="manual" autocomplete="off" checked> <?php echo e(__('Manual')); ?>

                </label>
                <label class="btn btn-primary btn-sm code">
                    <input type="radio" class="icon-input"  name="icon-input" id="radioButton3" value="auto" autocomplete="off"> <?php echo e(__('Auto Generate')); ?>

                </label>
            </div>
        </div>
    </div>
    <div class="form-group col-md-12 d-block" id="manual">
        <input class="form-control font-uppercase" name="manualCode" type="text">
    </div>
    <div class="form-group col-md-12 d-none" id="auto">
        <div class="row">
            <div class="col-md-10">
                <input class="form-control" name="autoCode" type="text" id="auto-code">
            </div>
            <div class="col-md-2">
                <a href="#" class="btn btn-sm btn-secondary btn-icon rounded-pill" id="code-generate"><i class="fas fa-history"></i></a>
            </div>
        </div>
    </div>
    <div class="modal-footer col-md-12">
        <?php echo e(Form::submit(__('Create'),array('class'=>'btn btn-sm btn-primary rounded-pill'))); ?>

    </div>
</div>
<?php echo e(Form::close()); ?>


<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/coupon/create.blade.php ENDPATH**/ ?>