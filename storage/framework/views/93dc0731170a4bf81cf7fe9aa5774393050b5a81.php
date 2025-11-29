<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Lead')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Lead')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Lead')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <a href="<?php echo e(route('lead.index')); ?>" class="btn btn-sm btn-primary bor-radius ml-4">
        <?php echo e(__('List View')); ?>

    </a>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Lead')): ?>
        <a href="#" data-size="lg" data-url="<?php echo e(route('lead.create',['lead',0])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Lead')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    console.log('lead enter here');
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
                        var status_id = $(target).attr('data-id');

                        $.ajax({
                            url: '<?php echo e(route('lead.change.order')); ?>',
                            type: 'POST',
                            data: {lead_id: id, status_id: status_id, order: order, "_token": $('meta[name="csrf-token"]').attr('content')},
                            success: function (data) {
                                show_toastr('Success', 'Lead successfully updated', 'success');
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
                        foreach($statuss as $id => $status){
                            $json[] = 'kanban-blacklist-'.$id;
                        }
                    ?>
                    <div class="kanban-board" data-toggle="dragula" data-containers='<?php echo json_encode($json); ?>'>
                        <?php $__currentLoopData = $statuss; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id=>$status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $leads =\App\Lead::leads($id);
                            ?>
                            <div class="kanban-col px-0">
                                <div class="card-list card-list-flush">
                                    <div class="card-list-title row align-items-center mb-3">
                                        <div class="col">
                                            <h6 class="mb-0 text-white"><?php echo e($status); ?></h6>
                                        </div>
                                        <div class="col text-right">
<?php if($leads): ?>
                                            <span class="badge badge-secondary rounded-pill"><?php echo e(count($leads)); ?></span>
<?php endif; ?>

                                        </div>
                                    </div>
                                    <div class="card-list-body" id="kanban-blacklist-<?php echo e($id); ?>" data-id="<?php echo e($id); ?>">
<?php if($leads): ?>
                                        <?php $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="card card-progress draggable-item border shadow-none" data-id="<?php echo e($lead->id); ?>">
                                                <div class="card-body">
                                                    <div class="row align-items-center mb-1">
                                                        <div class="col-6">
                                                            <h5 class="h6 mb-0">
                                                                <a data-size="lg" href="<?php echo e(route('lead.edit',$lead->id)); ?>" data-title="<?php echo e(__('Edit Lead')); ?>">
                                                                    <?php echo e(ucfirst($lead->name)); ?>

                                                                </a>
                                                            </h5>
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <div class="actions">
                                                                <?php if(Gate::check('Show Lead') || Gate::check('Edit Lead') || Gate::check('Delete Lead')): ?>
                                                                    <div class="dropdown">
                                                                        <a href="#" class="action-item" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                            <i class="fas fa-ellipsis-h"></i>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(22px, 31px, 0px);">
                                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Lead')): ?>
                                                                                <a href="#" data-size="lg" data-url="<?php echo e(route('lead.show', $lead->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Lead Details')); ?>" class="dropdown-item">
                                                                                    <?php echo e(__('View')); ?>

                                                                                </a>
                                                                            <?php endif; ?>
                                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Lead')): ?>
                                                                                <a class="dropdown-item" data-size="lg" href="<?php echo e(route('lead.edit',$lead->id)); ?>" data-title="<?php echo e(__('Edit Lead')); ?>"> <?php echo e(__('Edit')); ?></a>
                                                                            <?php endif; ?>
                                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Lead')): ?>
                                                                                <a class="dropdown-item" href="#" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="document.getElementById('task-delete-form-<?php echo e($lead->id); ?>').submit();"> <?php echo e(__('Delete')); ?></a>

                                                                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['lead.destroy', $lead->id],'id'=>'task-delete-form-'.$lead->id]); ?>

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
                                                            <span class=<?php if($lead->lead_temperature == '1'): ?><?php echo e("coldlead"); ?><?php elseif($lead->lead_temperature == '2'): ?><?php echo e("warmlead"); ?><?php else: ?><?php echo e("hotlead"); ?> <?php endif; ?>>
                                                                <?php if($lead->lead_temperature == '1'): ?><?php echo e('cold'); ?>

                                                                <?php elseif($lead->lead_temperature == '2'): ?><?php echo e('warm'); ?>

                                                                <?php else: ?><?php echo e('hot'); ?> 
                                                                <?php endif; ?>
                                                            </span>

                                                            <?php if($lead  ->status == 0): ?>
                                                            <span class="badge text-success" style="font-size:13px;"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                                                        <?php elseif($lead->status == 1): ?>
                                                            <span class="badge text-info" style="font-size:13px;"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                                                        <?php elseif($lead->status == 2): ?>
                                                            <span class="badge text-warning" style="font-size:13px;"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                                                        <?php elseif($lead->status == 3): ?>
                                                            <span class="badge text-danger"  style="font-size:13px;"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                                                        <?php elseif($lead->status == 4): ?>
                                                            <span class="badge text-danger" style="font-size:13px;"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                                                        <?php elseif($lead->status == 5): ?>
                                                            <span class="badge text-warning" style="font-size:13px;"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                                                        <?php endif; ?>

                                                            <h5 class="h6 mb-0">
                                                                <a href="#" class="text-sm" title="Ladna Barka"><?php echo e(ucfirst(!empty($lead->accounts)?$lead->accounts->name:'-')); ?></a>
                                                            </h5>
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <div class="col-auto actions">
                                                                <div class="dropdown" data-toggle="dropdown">
                                                                    <a href="#" class="action-item outline-none">
                                                                        <span><?php echo e(\Auth::user()->priceFormat($lead->opportunity_amount)); ?></span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row align-items-center">
                                                      
                                                            <div class="actions d-inline-block" style="font-size:12px;" title="<?php echo e(\Auth::user()->dateFormat($lead->created_at)); ?>">
                                                                <i class="fas fa-clock mr-2"></i>
                                                                <?php echo e($lead->created_at->diffForHumans()); ?>

                                                            </div>
                                                       
                                                        
                                                            <div class="avatar-group hover-avatar-ungroup">
                                                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <a href="#" class="avatar rounded-circle avatar-sm" data-original-title="<?php echo e($user->name); ?>" data-toggle="tooltip">
                                                                        <img <?php if(!empty($user->avatar)): ?> src="<?php echo e(asset('/storage/upload/profile/'.$user->avatar)); ?>" <?php else: ?> avatar="<?php echo e($user->name); ?>" <?php endif; ?> class="">
                                                                    </a>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/lead/grid.blade.php ENDPATH**/ ?>