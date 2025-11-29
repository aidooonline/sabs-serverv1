<script src="<?php echo e(asset('assets/js/site.core.js')); ?>"></script>
<!-- Page JS -->
<script src="<?php echo e(asset('assets/libs/dropzone/dist/min/dropzone.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/progressbar.js/dist/progressbar.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/apexcharts/dist/apexcharts.min.js')); ?>"></script>

<script src="<?php echo e(asset('assets/js/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/bootstrap-notify/bootstrap-notify.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/select2/dist/js/select2.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/moment/min/moment.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/fullcalendar/dist/fullcalendar.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/flatpickr/dist/flatpickr.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/quill/dist/quill.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/autosize/dist/autosize.min.js')); ?>"></script>

<script src="<?php echo e(asset('assets/js/site.js')); ?>"></script>
<!-- Demo JS - remove it when starting your project -->
<script type="text/javascript" src="<?php echo e(asset('assets/js/custom.js')); ?>"></script>


<?php if(Session::has('success')): ?>
    <script>
        show_toastr('<?php echo e(__('Success')); ?>', '<?php echo session('success'); ?>', 'success');
    </script>
    <?php echo e(Session::forget('success')); ?>

<?php endif; ?>
<?php if(Session::has('error')): ?>
    <script>
        show_toastr('<?php echo e(__('Error')); ?>', '<?php echo session('error'); ?>', 'error');
    </script>
    <?php echo e(Session::forget('error')); ?>

<?php endif; ?>
<?php echo $__env->yieldPushContent('script-page'); ?>
<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/partials/admin/footer.blade.php ENDPATH**/ ?>