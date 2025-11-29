<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Deals')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Deals')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Deals')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <a href="<?php echo e(route('opportunities.index')); ?>" class="btn btn-sm btn-primary bor-radius ml-4">
        <?php echo e(__('List View')); ?>

    </a>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Opportunities')): ?>
        <a href="#" data-size="lg" data-url="<?php echo e(route('opportunities.create',['opportunities',0])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Deals')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('script-page'); ?>
    <script src="<?php echo e(asset('assets/libs/dragula/dist/dragula.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/libs/autosize/dist/autosize.min.js')); ?>"></script>
    <script>


        !function (a) {
            "use strict";
            var t = function () {
                this.$body = a("body")
            };
            t.prototype.init = function () {
                a('[data-toggle="dragula"]').each(function () {

                    var t = a(this).data("containers"), n = [];
                    if (t) for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]); else n = [a(this)[0]];
                    var r = a(this).data("handleclass");
                    r ? dragula(n, {
                        moves: function (a, t, n) {
                            return n.classList.contains(r)
                        }
                    }) : dragula(n).on('drop', function (el, target, source, sibling) {
                        console.log(el);
                        var order = [];
                        $("#" + target.id + " > div").each(function () {
                            order[$(this).index()] = $(this).attr('data-id');
                        });

                        var id = $(el).attr('data-id');
                        var stage_id = $(target).attr('data-id');

                        $.ajax({
                            url: '<?php echo e(route('opportunities.change.order')); ?>',
                            type: 'POST',
                            data: {opo_id: id, stage_id: stage_id, order: order, "_token": $('meta[name="csrf-token"]').attr('content')},
                            success: function (data) {
                                show_toastr('Success', 'Opportunities successfully updated', 'success');
                            },
                            error: function (data) {
                                data = data.responseJSON;
                                show_toastr('Error', data.error, 'error')
                            }
                        });
                    });
                })
            }, a.Dragula = new t, a.Dragula.Constructor = t
        }(window.jQuery), function (a) {
            "use strict";
            a.Dragula.init()
        }(window.jQuery);
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('filter'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="container-kanban">
                    <?php
                        $json = [];
                        foreach ($stages as $stage){
                            $json[] = 'kanban-blacklist-'.$stage->id;
                        }
                    ?>
                    <div class="kanban-board" data-toggle="dragula" data-containers='<?php echo json_encode($json); ?>'>
                        <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $opportunities =$stage->opportunity($stage->id)
                            ?>
                            <div class="kanban-col px-0">
                                <div class="card-list card-list-flush">
                                    <div class="card-list-title row align-items-center mb-3">
                                        <div class="col">
                                            <h6 class="mb-0"><?php echo e($stage->name); ?></h6>
                                        </div>
                                        <div class="col text-right">
                                            <span class="badge badge-secondary rounded-pill"><?php echo e(count($opportunities)); ?></span>
                                        </div>
                                    </div>
                                    <div class="card-list-body" id="kanban-blacklist-<?php echo e($stage->id); ?>" data-id="<?php echo e($stage->id); ?>">
                                        <?php $__currentLoopData = $opportunities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opportunity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="card card-progress draggable-item border shadow-none" data-id="<?php echo e($opportunity->id); ?>">
                                                <div class="card-body">
                                                    <div class="row align-items-center mb-3">
                                                        <div class="col-6">
                                                            <h5 class="h6 mb-0">
                                                                <a data-size="lg" href="<?php echo e(route('opportunities.edit',$opportunity->id)); ?>" data-title="<?php echo e(__('Edit Opportunities')); ?>">
                                                                    <?php echo e(ucfirst($opportunity->name)); ?>

                                                                </a>
                                                            </h5>
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <div class="actions">
                                                                <?php if(Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities')): ?>
                                                                    <div class="dropdown">
                                                                        <a href="#" class="action-item" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                            <i class="fas fa-ellipsis-h"></i>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(22px, 31px, 0px);">
                                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Opportunities')): ?>
                                                                                <a href="#" data-size="lg" data-url="<?php echo e(route('opportunities.show', $opportunity->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Opportunities Details')); ?>" class="dropdown-item">
                                                                                    <?php echo e(__('View')); ?>

                                                                                </a>
                                                                            <?php endif; ?>
                                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Opportunities')): ?>
                                                                                <a class="dropdown-item" data-size="lg" href="<?php echo e(route('opportunities.edit',$opportunity->id)); ?>" data-title="<?php echo e(__('Edit Opportunities')); ?>"> <?php echo e(__('Edit')); ?></a>
                                                                            <?php endif; ?>
                                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Opportunities')): ?>
                                                                                <a class="dropdown-item" href="#" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="document.getElementById('task-delete-form-<?php echo e($opportunity->id); ?>').submit();"> <?php echo e(__('Delete')); ?></a>

                                                                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['opportunities.destroy', $opportunity->id],'id'=>'task-delete-form-'.$opportunity->id]); ?>

                                                                                <?php echo Form::close(); ?>

                                                                            <?php endif; ?>

                                                                        </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row align-items-center mb-3">
                                                       
                                                        <div class="col-6">
                                                            <h5 class="h6 mb-0">
                                                                <a href="#" class="text-sm" title="Ladna Barka"><?php echo e(ucfirst(!empty($opportunity->accounts->name)?$opportunity->accounts->name:'-')); ?></a>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <div class="row align-items-right">
                                                        <div class="text-right">
                                                            <div class="actions">
                                                                <div class="dropdown" data-toggle="dropdown">
                                                                    <a href="#" class="action-item outline-none">


                                                                        <span><?php echo e(\Auth::user()->priceFormat($opportunity->amount)); ?></span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row align-items-center">
                                                        <div class="col-8">
                                                            <div class="actions d-inline-block">
                                                                <span title="<?php echo e(\Auth::user()->dateFormat($opportunity->created_at->diffForHumans())); ?>" class="small">
                                                                       
                                                                    <?php echo e($opportunity->created_at->diffForHumans()); ?></span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/opportunities/grid.blade.php ENDPATH**/ ?>