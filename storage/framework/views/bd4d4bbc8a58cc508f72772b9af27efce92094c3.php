<?php
    $logo=asset(Storage::url('uploads/logo/'));
       $company_logo=Utility::getValByName('company_logo');
       $company_small_logo=Utility::getValByName('company_small_logo');
       $company_favicon=Utility::getValByName('company_favicon');
   $lang=\App\Utility::getValByName('default_language');
?>
<?php $__env->startPush('css-page'); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script-page'); ?>
    <script>
        $(document).ready(function () {
            $('.list-group-item').on('click', function () {
                var href = $(this).attr('data-href');
                $('.tabs-card').addClass('d-none');
                $(href).removeClass('d-none');
                $('#tabs .list-group-item').removeClass('text-primary');
                $(this).addClass('text-primary');
            });
        });
    </script>
    <script>
        $(document).on("change", "select[name='quote_template'], input[name='quote_color']", function () {
            var template = $("select[name='quote_template']").val();
            var color = $("input[name='quote_color']:checked").val();
            $('#quote_frame').attr('src', '<?php echo e(url('/quote/preview')); ?>/' + template + '/' + color);
        });
        $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function () {
            var template = $("select[name='invoice_template']").val();
            var color = $("input[name='invoice_color']:checked").val();
            $('#invoice_frame').attr('src', '<?php echo e(url('/invoice/preview')); ?>/' + template + '/' + color);
        });
        $(document).on("change", "select[name='salesorder_template'], input[name='salesorder_color']", function () {
            var template = $("select[name='salesorder_template']").val();
            var color = $("input[name='salesorder_color']:checked").val();
            $('#salesorder_frame').attr('src', '<?php echo e(url('/salesorder/preview')); ?>/' + template + '/' + color);
        });
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Settings')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">   <?php echo e(__('Settings')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Settings')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <ul class="nav nav-tabs nav-overflow profile-tab-list" role="tablist">
                    <?php if(\Auth::user()->type=='super admin'): ?>
                        <li class="nav-item ml-4">
                            <a href="#business-setting" id="business-setting_tab" class="nav-link active" data-toggle="tab" role="tab" aria-controls="home" aria-selected="true">
                                <i class="fas fa-sitemap mr-2"></i><?php echo e(__('Site Setting')); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='super admin'): ?>
                        <li class="nav-item ml-4">
                            <a href="#email-setting" id="email-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-mail-bulk mr-2"></i><?php echo e(__('Mailer Settings')); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='super admin'): ?>
                        <li class="nav-item ml-4">
                            <a href="#pusher-setting" id="pusher-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-comment-dots mr-2"></i><?php echo e(__('Pusher Settings')); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='super admin'): ?>
                        <li class="nav-item ml-4">
                            <a href="#payment-setting" id="payment-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-money-check-alt mr-2"></i><?php echo e(__('Payment Settings')); ?>

                            </a>
                        </li>
                    <?php endif; ?>



                    <?php if(\Auth::user()->type=='owner'): ?>
                        <li class="nav-item ml-4">
                            <a href="#company-business-setting" id="company-business-setting_tab" class="nav-link active" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-sitemap mr-2"></i><?php echo e(__('Site Setting')); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='owner'): ?>
                        <li class="nav-item ml-4">
                            <a href="#company-setting" id="company-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="far fa-building mr-2"></i><?php echo e(__('Company Settings')); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='owner'): ?>
                        <li class="nav-item ml-4">
                            <a href="#system-setting" id="system-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-cogs mr-2"></i><?php echo e(__('System Settings')); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='owner'): ?>
                        <li class="nav-item ml-4">
                            <a href="#quote-setting" id="quote-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-receipt mr-2"></i><?php echo e(__('Quote Settings')); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='owner'): ?>
                        <li class="nav-item ml-4">
                            <a href="#invoice-setting" id="invoice-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-file-invoice-dollar mr-2"></i><?php echo e(__('Invoice Setting')); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='owner'): ?>
                        <li class="nav-item ml-4">
                            <a href="#salesorder-setting" id="salesorder-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-money-check-alt mr-2"></i><?php echo e(__('Sales Order Settings')); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="tab-content">
                    <?php if(\Auth::user()->type=='super admin'): ?>
                        <div class="tab-pane fade active show" id="business-setting" role="tabpanel" aria-labelledby="orders-tab">
                            <?php echo e(Form::model($settings,array('route'=>'business.setting','method'=>'POST','enctype' => "multipart/form-data"))); ?>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="logo" class="form-control-label"><?php echo e(__('Logo')); ?></label>
                                            <input type="file" name="logo" id="logo" class="custom-input-file">
                                            <label for="logo">
                                                <i class="fa fa-upload"></i>
                                                <span><?php echo e(__('Choose a file')); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6 text-center">
                                        <div class="logo-div">
                                            <img src="<?php echo e($logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo.png')); ?>" width="170px" class="img_setting">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="favicon" class="form-control-label"><?php echo e(__('Favicon')); ?></label>
                                            <input type="file" name="favicon" id="favicon" class="custom-input-file">
                                            <label for="favicon">
                                                <i class="fa fa-upload"></i>
                                                <span><?php echo e(__('Choose a file')); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6 text-center">
                                        <div class="logo-div">
                                            <img src="<?php echo e($logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')); ?>" width="50px" class="img_setting">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="row">
                                    <span class="invalid-logo" role="alert">
                                        <strong class="text-danger"><?php echo e($message); ?></strong>
                                     </span>
                                        </div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('title_text',__('Title Text'))); ?>

                                        <?php echo e(Form::text('title_text',null,array('class'=>'form-control','placeholder'=>__('Title Text')))); ?>

                                        <?php $__errorArgs = ['title_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-title_text" role="alert">
                                     <strong class="text-danger"><?php echo e($message); ?></strong>
                                 </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <?php if(\Auth::user()->type=='super admin'): ?>
                                        <div class="form-group col-md-6">
                                            <?php echo e(Form::label('footer_text',__('Footer Text'))); ?>

                                            <?php echo e(Form::text('footer_text',null,array('class'=>'form-control','placeholder'=>__('Footer Text')))); ?>

                                            <?php $__errorArgs = ['footer_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-footer_text" role="alert">
                                        <strong class="text-danger"><?php echo e($message); ?></strong>
                                     </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <?php echo e(Form::label('default_language',__('Default Language'))); ?>

                                            <div class="changeLanguage">
                                                <select name="default_language" id="default_language" class="form-control custom-select" data-toggle="select">
                                                    <?php $__currentLoopData = \App\Utility::languages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option <?php if($lang == $language): ?> selected <?php endif; ?> value="<?php echo e($language); ?>"><?php echo e(Str::upper($language)); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <?php echo e(Form::label('display_landing_page_',__('Landing Page Display'))); ?>

                                            <div class="col-12 mt-2">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" name="display_landing_page" id="display_landing_page" <?php echo e($settings['display_landing_page'] == 'on' ? 'checked="checked"' : ''); ?>>
                                                    <label class="custom-control-label form-control-label" for="display_landing_page"></label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('footer_link_1',__('Footer Link Title 1'))); ?>

                                        <?php echo e(Form::text('footer_link_1',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link Title 1')))); ?>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('footer_value_1',__('Footer Link href 1'))); ?>

                                        <?php echo e(Form::text('footer_value_1',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link 1')))); ?>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('footer_link_2',__('Footer Link Title 2'))); ?>

                                        <?php echo e(Form::text('footer_link_2',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link Title 2')))); ?>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('footer_value_2',__('Footer Link href 2'))); ?>

                                        <?php echo e(Form::text('footer_value_2',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link 2')))); ?>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('footer_link_3',__('Footer Link Title 3'))); ?>

                                        <?php echo e(Form::text('footer_link_3',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link Title 3')))); ?>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('footer_value_3',__('Footer Link href 3'))); ?>

                                        <?php echo e(Form::text('footer_value_3',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link 3')))); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <?php echo e(Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))); ?>

                            </div>
                            <?php echo e(Form::close()); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='super admin'): ?>
                        <div class="tab-pane fade" id="email-setting" role="tabpanel" aria-labelledby="orders-tab">
                            <?php echo e(Form::open(array('route'=>'email.setting','method'=>'post'))); ?>

                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('mail_driver',__('Mail Driver'))); ?>

                                        <?php echo e(Form::text('mail_driver',env('MAIL_DRIVER'),array('class'=>'form-control','placeholder'=>__('Enter Mail Driver')))); ?>

                                        <?php $__errorArgs = ['mail_driver'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-mail_driver" role="alert">
                                     <strong class="text-danger"><?php echo e($message); ?></strong>
                                     </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('mail_host',__('Mail Host'))); ?>

                                        <?php echo e(Form::text('mail_host',env('MAIL_HOST'),array('class'=>'form-control ','placeholder'=>__('Enter Mail Driver')))); ?>

                                        <?php $__errorArgs = ['mail_host'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-mail_driver" role="alert">
                                                 <strong class="text-danger"><?php echo e($message); ?></strong>
                                                 </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('mail_port',__('Mail Port'))); ?>

                                        <?php echo e(Form::text('mail_port',env('MAIL_PORT'),array('class'=>'form-control','placeholder'=>__('Enter Mail Port')))); ?>

                                        <?php $__errorArgs = ['mail_port'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-mail_port" role="alert">
                                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                                </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('mail_username',__('Mail Username'))); ?>

                                        <?php echo e(Form::text('mail_username',env('MAIL_USERNAME'),array('class'=>'form-control','placeholder'=>__('Enter Mail Username')))); ?>

                                        <?php $__errorArgs = ['mail_username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-mail_username" role="alert">
                                                 <strong class="text-danger"><?php echo e($message); ?></strong>
                                                 </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('mail_password',__('Mail Password'))); ?>

                                        <?php echo e(Form::text('mail_password',env('MAIL_PASSWORD'),array('class'=>'form-control','placeholder'=>__('Enter Mail Password')))); ?>

                                        <?php $__errorArgs = ['mail_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-mail_password" role="alert">
                                                 <strong class="text-danger"><?php echo e($message); ?></strong>
                                                 </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('mail_encryption',__('Mail Encryption'))); ?>

                                        <?php echo e(Form::text('mail_encryption',env('MAIL_ENCRYPTION'),array('class'=>'form-control','placeholder'=>__('Enter Mail Encryption')))); ?>

                                        <?php $__errorArgs = ['mail_encryption'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-mail_encryption" role="alert">
                                                 <strong class="text-danger"><?php echo e($message); ?></strong>
                                                 </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('mail_from_address',__('Mail From Address'))); ?>

                                        <?php echo e(Form::text('mail_from_address',env('MAIL_FROM_ADDRESS'),array('class'=>'form-control','placeholder'=>__('Enter Mail From Address')))); ?>

                                        <?php $__errorArgs = ['mail_from_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-mail_from_address" role="alert">
                                                 <strong class="text-danger"><?php echo e($message); ?></strong>
                                                 </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('mail_from_name',__('Mail From Name'))); ?>

                                        <?php echo e(Form::text('mail_from_name',env('MAIL_FROM_NAME'),array('class'=>'form-control','placeholder'=>__('Enter Mail Encryption')))); ?>

                                        <?php $__errorArgs = ['mail_from_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-mail_from_name" role="alert">
                                                 <strong class="text-danger"><?php echo e($message); ?></strong>
                                                 </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <a href="#" data-url="<?php echo e(route('test.mail' )); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Send Test Mail')); ?>" class="btn btn-sm btn-info rounded-pill">
                                            <?php echo e(__('Send Test Mail')); ?>

                                        </a>
                                    </div>
                                    <div class="form-group col-md-6 text-right">
                                        <?php echo e(Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))); ?>

                                    </div>
                                </div>
                            </div>
                            <?php echo e(Form::close()); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='super admin'): ?>
                        <div class="tab-pane fade" id="pusher-setting" role="tabpanel" aria-labelledby="orders-tab">
                            <?php echo e(Form::model($settings,array('route'=>'pusher.setting','method'=>'post'))); ?>

                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('pusher_app_id *',__('Pusher App Id *'))); ?>

                                        <?php echo e(Form::text('pusher_app_id',env('PUSHER_APP_ID'),array('class'=>'form-control font-style'))); ?>

                                        <?php $__errorArgs = ['pusher_app_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-pusher_app_id" role="alert">
                                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                                </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('pusher_app_key',__('Pusher App Key'))); ?>

                                        <?php echo e(Form::text('pusher_app_key',env('PUSHER_APP_KEY'),array('class'=>'form-control font-style'))); ?>

                                        <?php $__errorArgs = ['pusher_app_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-pusher_app_key" role="alert">
                                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                                </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('pusher_app_secret',__('Pusher App Secret'))); ?>

                                        <?php echo e(Form::text('pusher_app_secret',env('PUSHER_APP_SECRET'),array('class'=>'form-control font-style'))); ?>

                                        <?php $__errorArgs = ['pusher_app_secret'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-pusher_app_secret" role="alert">
                                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                                </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('pusher_app_cluster',__('Pusher App Cluster'))); ?>

                                        <?php echo e(Form::text('pusher_app_cluster',env('PUSHER_APP_CLUSTER'),array('class'=>'form-control font-style'))); ?>

                                        <?php $__errorArgs = ['pusher_app_cluster'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-pusher_app_cluster" role="alert">
                                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                                </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <?php echo e(Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))); ?>

                            </div>
                            <?php echo e(Form::close()); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='super admin'): ?>
                        <div class="tab-pane fade" id="payment-setting" role="tabpanel" aria-labelledby="orders-tab">
                            <div class="card-body">
                                <?php echo e(Form::open(array('route'=>'payment.setting','method'=>'post'))); ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php echo e(Form::label('currency_symbol',__('Currency Symbol *'))); ?>

                                            <?php echo e(Form::text('currency_symbol',env('CURRENCY_SYMBOL'),array('class'=>'form-control','required'))); ?>

                                            <?php $__errorArgs = ['currency_symbol'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-currency_symbol" role="alert">
                                            <strong class="text-danger"><?php echo e($message); ?></strong>
                                        </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php echo e(Form::label('currency',__('Currency *'))); ?>

                                            <?php echo e(Form::text('currency',env('CURRENCY'),array('class'=>'form-control font-style','required'))); ?>

                                            <small> <?php echo e(__('Note: Add currency code as per three-letter ISO code.')); ?><br> <a href="https://stripe.com/docs/currencies" target="_blank"><?php echo e(__('you can find out here..')); ?></a></small> <br>
                                            <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-currency" role="alert">
                                            <strong class="text-danger"><?php echo e($message); ?></strong>
                                        </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <hr>
                                    </div>
                                    <div class="col-6 py-2">
                                        <h5 class="h5"><?php echo e(__('Stripe')); ?></h5>
                                    </div>
                                    <div class="col-6 py-2 text-right">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="enable_stripe" id="enable_stripe" <?php echo e(env('ENABLE_STRIPE') == 'on' ? 'checked="checked"' : ''); ?>>
                                            <label class="custom-control-label form-control-label" for="enable_stripe"><?php echo e(__('Enable Stripe')); ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php echo e(Form::label('stripe_key',__('Stripe Key'))); ?>

                                            <?php echo e(Form::text('stripe_key',env('STRIPE_KEY'),['class'=>'form-control','placeholder'=>__('Enter Stripe Key')])); ?>

                                            <?php $__errorArgs = ['stripe_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-stripe_key" role="alert">
                                             <strong class="text-danger"><?php echo e($message); ?></strong>
                                         </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php echo e(Form::label('stripe_secret',__('Stripe Secret'))); ?>

                                            <?php echo e(Form::text('stripe_secret',env('STRIPE_SECRET'),['class'=>'form-control ','placeholder'=>__('Enter Stripe Secret')])); ?>

                                            <?php $__errorArgs = ['stripe_secret'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-stripe_secret" role="alert">
                                             <strong class="text-danger"><?php echo e($message); ?></strong>
                                         </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <hr>
                                    </div>
                                    <div class="col-6 py-2">
                                        <h5 class="h5"><?php echo e(__('PayPal')); ?></h5>
                                    </div>
                                    <div class="col-6 py-2 text-right">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="enable_paypal" id="enable_paypal" <?php echo e(env('ENABLE_PAYPAL') == 'on' ? 'checked="checked"' : ''); ?>>
                                            <label class="custom-control-label form-control-label" for="enable_paypal"><?php echo e(__('Enable Paypal')); ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 pb-4">
                                        <label class="paypal-label form-control-label" for="paypal_mode"><?php echo e(__('Paypal Mode')); ?></label> <br>
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-primary btn-sm active">
                                                <input type="radio" name="paypal_mode" value="sandbox" <?php echo e(env('PAYPAL_MODE') == '' || env('PAYPAL_MODE') == 'sandbox' ? 'checked="checked"' : ''); ?>><?php echo e(__('Sandbox')); ?>

                                            </label>
                                            <label class="btn btn-primary btn-sm ">
                                                <input type="radio" name="paypal_mode" value="live" <?php echo e(env('PAYPAL_MODE') == 'live' ? 'checked="checked"' : ''); ?>><?php echo e(__('Live')); ?>

                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="paypal_client_id"><?php echo e(__('Client ID')); ?></label>
                                            <input type="text" name="paypal_client_id" id="paypal_client_id" class="form-control" value="<?php echo e(env('PAYPAL_CLIENT_ID')); ?>" placeholder="<?php echo e(__('Client ID')); ?>"/>
                                            <?php if($errors->has('paypal_client_id')): ?>
                                                <span class="invalid-feedback d-block">
                                            <?php echo e($errors->first('paypal_client_id')); ?>

                                        </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="paypal_secret_key"><?php echo e(__('Secret Key')); ?></label>
                                            <input type="text" name="paypal_secret_key" id="paypal_secret_key" class="form-control" value="<?php echo e(env('PAYPAL_SECRET_KEY')); ?>" placeholder="<?php echo e(__('Secret Key')); ?>"/>
                                            <?php if($errors->has('paypal_secret_key')): ?>
                                                <span class="invalid-feedback d-block">
                                            <?php echo e($errors->first('paypal_secret_key')); ?>

                                        </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <?php echo e(Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))); ?>

                                </div>
                                <?php echo e(Form::close()); ?>

                            </div>
                        </div>
                    <?php endif; ?>


                    <?php if(\Auth::user()->type=='owner'): ?>
                        <div class="tab-pane fade active show" id="company-business-setting" role="tabpanel" aria-labelledby="orders-tab">
                            <?php echo e(Form::model($settings,array('route'=>'business.setting','method'=>'POST','enctype' => "multipart/form-data"))); ?>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="full_logo" class="form-control-label"><?php echo e(__('Logo')); ?></label>
                                            <input type="file" name="full_logo" id="full_logo" class="custom-input-file">
                                            <label for="full_logo">
                                                <i class="fa fa-upload"></i>
                                                <span><?php echo e(__('Choose a file')); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6 d-flex align-items-center justify-content-center mt-3">
                                        <div class="logo-div">
                                            <img src="<?php echo e($logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo.png')); ?>" width="170px" class="img_setting">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="favicon" class="form-control-label"><?php echo e(__('Favicon')); ?></label>
                                            <input type="file" name="favicon" id="favicon" class="custom-input-file">
                                            <label for="favicon">
                                                <i class="fa fa-upload"></i>
                                                <span><?php echo e(__('Choose a file')); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6 d-flex align-items-center justify-content-center mt-3">
                                        <div class="logo-div">
                                            <img src="<?php echo e($logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')); ?>" width="50px" class="img_setting">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="row">
                                    <span class="invalid-logo" role="alert">
                                        <strong class="text-danger"><?php echo e($message); ?></strong>
                                     </span>
                                        </div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('title_text',__('Title Text'))); ?>

                                        <?php echo e(Form::text('title_text',null,array('class'=>'form-control','placeholder'=>__('Title Text')))); ?>

                                        <?php $__errorArgs = ['title_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-title_text" role="alert">
                                     <strong class="text-danger"><?php echo e($message); ?></strong>
                                 </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('footer_text',__('Footer Text'))); ?>

                                        <?php echo e(Form::text('footer_text',null,array('class'=>'form-control','placeholder'=>__('Footer Text')))); ?>

                                        <?php $__errorArgs = ['footer_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-footer_text" role="alert">
                                        <strong class="text-danger"><?php echo e($message); ?></strong>
                                     </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('footer_link_1',__('Footer Link Title 1'))); ?>

                                        <?php echo e(Form::text('footer_link_1',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link Title 1')))); ?>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('footer_value_1',__('Footer Link href 1'))); ?>

                                        <?php echo e(Form::text('footer_value_1',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link 1')))); ?>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('footer_link_2',__('Footer Link Title 2'))); ?>

                                        <?php echo e(Form::text('footer_link_2',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link Title 2')))); ?>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('footer_value_2',__('Footer Link href 2'))); ?>

                                        <?php echo e(Form::text('footer_value_2',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link 2')))); ?>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('footer_link_3',__('Footer Link Title 3'))); ?>

                                        <?php echo e(Form::text('footer_link_3',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link Title 3')))); ?>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('footer_value_3',__('Footer Link href 3'))); ?>

                                        <?php echo e(Form::text('footer_value_3',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link 3')))); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <?php echo e(Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))); ?>

                            </div>
                            <?php echo e(Form::close()); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='owner'): ?>
                        <div class="tab-pane fade" id="company-setting" role="tabpanel" aria-labelledby="orders-tab">
                            <?php echo e(Form::model($settings,array('route'=>'company.setting','method'=>'post'))); ?>

                            <div class="card-body">

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('company_name *',__('Company Name *'))); ?>

                                        <?php echo e(Form::text('company_name',null,array('class'=>'form-control font-style'))); ?>

                                        <?php $__errorArgs = ['company_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-company_name" role="alert">
                                        <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('company_address',__('Address'))); ?>

                                        <?php echo e(Form::text('company_address',null,array('class'=>'form-control font-style'))); ?>

                                        <?php $__errorArgs = ['company_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-company_address" role="alert">
                                                            <strong class="text-danger"><?php echo e($message); ?></strong>
                                                        </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('company_city',__('City'))); ?>

                                        <?php echo e(Form::text('company_city',null,array('class'=>'form-control font-style'))); ?>

                                        <?php $__errorArgs = ['company_city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-company_city" role="alert">
                                                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                                                </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('company_state',__('State'))); ?>

                                        <?php echo e(Form::text('company_state',null,array('class'=>'form-control font-style'))); ?>

                                        <?php $__errorArgs = ['company_state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-company_state" role="alert">
                                                            <strong class="text-danger"><?php echo e($message); ?></strong>
                                                        </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('company_zipcode',__('Zip/Post Code'))); ?>

                                        <?php echo e(Form::text('company_zipcode',null,array('class'=>'form-control'))); ?>

                                        <?php $__errorArgs = ['company_zipcode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-company_zipcode" role="alert">
                                                            <strong class="text-danger"><?php echo e($message); ?></strong>
                                                        </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group  col-md-6">
                                        <?php echo e(Form::label('company_country',__('Country'))); ?>

                                        <?php echo e(Form::text('company_country',null,array('class'=>'form-control font-style'))); ?>

                                        <?php $__errorArgs = ['company_country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-company_country" role="alert">
                                                            <strong class="text-danger"><?php echo e($message); ?></strong>
                                                        </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('company_telephone',__('Telephone'))); ?>

                                        <?php echo e(Form::text('company_telephone',null,array('class'=>'form-control'))); ?>

                                        <?php $__errorArgs = ['company_telephone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-company_telephone" role="alert">
                                                            <strong class="text-danger"><?php echo e($message); ?></strong>
                                                        </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('company_email',__('System Email *'))); ?>

                                        <?php echo e(Form::text('company_email',null,array('class'=>'form-control'))); ?>

                                        <?php $__errorArgs = ['company_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-company_email" role="alert">
                                                            <strong class="text-danger"><?php echo e($message); ?></strong>
                                                        </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('company_email_from_name',__('Email (From Name) *'))); ?>

                                        <?php echo e(Form::text('company_email_from_name',null,array('class'=>'form-control font-style'))); ?>

                                        <?php $__errorArgs = ['company_email_from_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-company_email_from_name" role="alert">
                                                            <strong class="text-danger"><?php echo e($message); ?></strong>
                                                        </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <?php echo e(Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))); ?>

                            </div>
                            <?php echo e(Form::close()); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='owner'): ?>
                        <div class="tab-pane fade" id="system-setting" role="tabpanel" aria-labelledby="orders-tab">
                            <?php echo e(Form::model($settings,array('route'=>'system.setting','method'=>'post'))); ?>

                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('site_currency',__('Currency *'))); ?>

                                        <?php echo e(Form::text('site_currency',null,array('class'=>'form-control font-style'))); ?>

                                        <?php $__errorArgs = ['site_currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-site_currency" role="alert">
                                                            <strong class="text-danger"><?php echo e($message); ?></strong>
                                                        </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('site_currency_symbol',__('Currency Symbol *'))); ?>

                                        <?php echo e(Form::text('site_currency_symbol',null,array('class'=>'form-control'))); ?>

                                        <?php $__errorArgs = ['site_currency_symbol'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-site_currency_symbol" role="alert">
                                                            <strong class="text-danger"><?php echo e($message); ?></strong>
                                                        </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="example3cols3Input"><?php echo e(__('Currency Symbol Position')); ?></label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="custom-control custom-radio mb-3">

                                                        <input type="radio" id="customRadio5" name="site_currency_symbol_position" value="pre" class="custom-control-input" <?php if(@$settings['site_currency_symbol_position'] == 'pre'): ?> checked <?php endif; ?>>
                                                        <label class="custom-control-label" for="customRadio5"><?php echo e(__('Pre')); ?></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="custom-control custom-radio mb-3">
                                                        <input type="radio" id="customRadio6" name="site_currency_symbol_position" value="post" class="custom-control-input" <?php if(@$settings['site_currency_symbol_position'] == 'post'): ?> checked <?php endif; ?>>
                                                        <label class="custom-control-label" for="customRadio6"><?php echo e(__('Post')); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="site_date_format" class="form-control-label"><?php echo e(__('Date Format')); ?></label>
                                        <select type="text" name="site_date_format" class="form-control selectric" id="site_date_format">
                                            <option value="M j, Y" <?php if(@$settings['site_date_format'] == 'M j, Y'): ?> selected="selected" <?php endif; ?>>Jan 1,2015</option>
                                            <option value="d-m-Y" <?php if(@$settings['site_date_format'] == 'd-m-Y'): ?> selected="selected" <?php endif; ?>>d-m-y</option>
                                            <option value="m-d-Y" <?php if(@$settings['site_date_format'] == 'm-d-Y'): ?> selected="selected" <?php endif; ?>>m-d-y</option>
                                            <option value="Y-m-d" <?php if(@$settings['site_date_format'] == 'Y-m-d'): ?> selected="selected" <?php endif; ?>>y-m-d</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="site_time_format" class="form-control-label"><?php echo e(__('Time Format')); ?></label>
                                        <select type="text" name="site_time_format" class="form-control selectric" id="site_time_format">
                                            <option value="g:i A" <?php if(@$settings['site_time_format'] == 'g:i A'): ?> selected="selected" <?php endif; ?>>10:30 PM</option>
                                            <option value="g:i a" <?php if(@$settings['site_time_format'] == 'g:i a'): ?> selected="selected" <?php endif; ?>>10:30 pm</option>
                                            <option value="H:i" <?php if(@$settings['site_time_format'] == 'H:i'): ?> selected="selected" <?php endif; ?>>22:30</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('quote_prefix',__('Quote Prefix'))); ?>

                                        <?php echo e(Form::text('quote_prefix',null,array('class'=>'form-control'))); ?>

                                        <?php $__errorArgs = ['quote_prefix'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-quote_prefix" role="alert">
                                                <strong class="text-danger"><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('salesorder_prefix',__('Sales Order Prefix'))); ?>

                                        <?php echo e(Form::text('salesorder_prefix',null,array('class'=>'form-control'))); ?>

                                        <?php $__errorArgs = ['salesorder_prefix'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-salesorder_prefix" role="alert">
                                                <strong class="text-danger"><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('invoice_prefix',__('Invoice Prefix'))); ?>

                                        <?php echo e(Form::text('invoice_prefix',null,array('class'=>'form-control'))); ?>

                                        <?php $__errorArgs = ['invoice_prefix'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-invoice_prefix" role="alert">
                                                <strong class="text-danger"><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('footer_title',__('Quote/SalesOrder/Invoice Footer Title'))); ?>

                                        <?php echo e(Form::text('footer_title',null,array('class'=>'form-control'))); ?>

                                        <?php $__errorArgs = ['footer_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-footer_title" role="alert">
                                                <strong class="text-danger"><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?php echo e(Form::label('shipping_display',__('Quote / Invoice / Sales-Order Shipping Display'))); ?>

                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="shipping_display" class="custom-control-input" id="shipping_display" <?php echo e($settings['shipping_display']=='on' ? 'checked="checked"' : ''); ?>>
                                            <label name="shipping_display" class="custom-control-label form-control-label" for="shipping_display"></label>
                                        </div>
                                        <?php $__errorArgs = ['shipping_display'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-shipping_display" role="alert">
                                            <strong class="text-danger"><?php echo e($message); ?></strong>
                                        </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <?php echo e(Form::label('footer_notes',__('Quote/SalesOrder/Invoice Footer Notes'))); ?>

                                        <?php echo e(Form::textarea('footer_notes', null, ['class'=>'form-control','rows'=>'3'])); ?>

                                        <?php $__errorArgs = ['footer_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-footer_notes" role="alert">
                                                <strong class="text-danger"><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <?php echo e(Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))); ?>

                            </div>
                            <?php echo e(Form::close()); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='owner'): ?>
                        <div class="tab-pane fade" id="quote-setting" role="tabpanel" aria-labelledby="orders-tab">
                            <form id="setting-form" method="post" action="<?php echo e(route('quote.template.setting')); ?>">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <form id="setting-form" method="post" action="<?php echo e(route('quote.template.setting')); ?>">
                                                <?php echo csrf_field(); ?>
                                                <div class="form-group">
                                                    <label for="address"><?php echo e(__('Quote Template')); ?></label>
                                                    <select class="form-control" name="quote_template" data-toggle="select">
                                                        <?php $__currentLoopData = Utility::templateData()['templates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($key); ?>" <?php echo e((isset($settings['quote_template']) && $settings['quote_template'] == $key) ? 'selected' : ''); ?>> <?php echo e($template); ?> </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label"><?php echo e(__('Color Input')); ?></label>
                                                    <div class="row gutters-xs">
                                                        <?php $__currentLoopData = Utility::templateData()['colors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="col-auto">
                                                                <label class="colorinput">
                                                                    <input name="quote_color" type="radio" value="<?php echo e($color); ?>" class="colorinput-input" <?php echo e((isset($settings['quote_color']) && $settings['quote_color'] == $color) ? 'checked' : ''); ?>>
                                                                    <span class="colorinput-color" style="background:#<?php echo e($color); ?>"></span>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </div>
                                                <button class="btn btn-sm btn-primary rounded-pill">
                                                    <?php echo e(__('Save')); ?>

                                                </button>
                                            </form>
                                        </div>
                                        <div class="col-md-10">
                                            <?php if(isset($settings['quote_template']) && isset($settings['quote_color'])): ?>
                                                <iframe id="quote_frame" class="w-100 h-1450" frameborder="0" src="<?php echo e(route('quote.preview',[$settings['quote_template'],$settings['quote_color']])); ?>"></iframe>
                                            <?php else: ?>
                                                <iframe id="quote_frame" class="w-100 h-1450" frameborder="0" src="<?php echo e(route('quote.preview',['template1','fffff'])); ?>"></iframe>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='owner'): ?>
                        <div class="tab-pane fade" id="invoice-setting" role="tabpanel" aria-labelledby="orders-tab">
                            <form id="setting-form" method="post" action="<?php echo e(route('invoice.template.setting')); ?>">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <form id="setting-form" method="post" action="<?php echo e(route('invoice.template.setting')); ?>">
                                                <?php echo csrf_field(); ?>
                                                <div class="form-group">
                                                    <label for="address"><?php echo e(__('Invoice Template')); ?></label>
                                                    <select class="form-control" name="invoice_template" data-toggle="select">
                                                        <?php $__currentLoopData = Utility::templateData()['templates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($key); ?>" <?php echo e((isset($settings['invoice_template']) && $settings['invoice_template'] == $key) ? 'selected' : ''); ?>> <?php echo e($template); ?> </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label"><?php echo e(__('Color Input')); ?></label>
                                                    <div class="row gutters-xs">
                                                        <?php $__currentLoopData = Utility::templateData()['colors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="col-auto">
                                                                <label class="colorinput">
                                                                    <input name="invoice_color" type="radio" value="<?php echo e($color); ?>" class="colorinput-input" <?php echo e((isset($settings['invoice_color']) && $settings['invoice_color'] == $color) ? 'checked' : ''); ?>>
                                                                    <span class="colorinput-color" style="background:#<?php echo e($color); ?>"></span>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </div>
                                                <button class="btn btn-sm btn-primary rounded-pill">
                                                    <?php echo e(__('Save')); ?>

                                                </button>
                                            </form>
                                        </div>
                                        <div class="col-md-10">
                                            <?php if(isset($settings['invoice_template']) && isset($settings['invoice_color'])): ?>
                                                <iframe id="invoice_frame" class="w-100 h-1450" frameborder="0" src="<?php echo e(route('invoice.preview',[$settings['invoice_template'],$settings['invoice_color']])); ?>"></iframe>
                                            <?php else: ?>
                                                <iframe id="invoice_frame" class="w-100 h-1450" frameborder="0" src="<?php echo e(route('invoice.preview',['template1','fffff'])); ?>"></iframe>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                    <?php if(\Auth::user()->type=='owner'): ?>
                        <div class="tab-pane fade" id="salesorder-setting" role="tabpanel" aria-labelledby="orders-tab">
                            <form id="setting-form" method="post" action="<?php echo e(route('salesorder.template.setting')); ?>">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <form id="setting-form" method="post" action="<?php echo e(route('salesorder.template.setting')); ?>">
                                                <?php echo csrf_field(); ?>
                                                <div class="form-group">
                                                    <label for="address"><?php echo e(__('Sales Order Template')); ?></label>
                                                    <select class="form-control" name="salesorder_template" data-toggle="select">
                                                        <?php $__currentLoopData = Utility::templateData()['templates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($key); ?>" <?php echo e((isset($settings['salesorder_template']) && $settings['salesorder_template'] == $key) ? 'selected' : ''); ?>> <?php echo e($template); ?> </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label"><?php echo e(__('Color Input')); ?></label>
                                                    <div class="row gutters-xs">
                                                        <?php $__currentLoopData = Utility::templateData()['colors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="col-auto">
                                                                <label class="colorinput">
                                                                    <input name="salesorder_color" type="radio" value="<?php echo e($color); ?>" class="colorinput-input" <?php echo e((isset($settings['salesorder_color']) && $settings['salesorder_color'] == $color) ? 'checked' : ''); ?>>
                                                                    <span class="colorinput-color" style="background: #<?php echo e($color); ?>"></span>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </div>
                                                <button class="btn btn-sm btn-primary rounded-pill">
                                                    <?php echo e(__('Save')); ?>

                                                </button>
                                            </form>
                                        </div>
                                        <div class="col-md-10">
                                            <?php if(isset($settings['salesorder_template']) && isset($settings['salesorder_color'])): ?>
                                                <iframe id="salesorder_frame" class="w-100 h-1450" frameborder="0" src="<?php echo e(route('salesorder.preview',[$settings['salesorder_template'],$settings['salesorder_color']])); ?>"></iframe>
                                            <?php else: ?>
                                                <iframe id="salesorder_frame" class="w-100 h-1450" frameborder="0" src="<?php echo e(route('salesorder.preview',['template1','fffff'])); ?>"></iframe>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/settings/index.blade.php ENDPATH**/ ?>