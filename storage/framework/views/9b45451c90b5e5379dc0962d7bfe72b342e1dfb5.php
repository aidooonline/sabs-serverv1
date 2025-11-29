<?php $__env->startPush('script-page'); ?>
    <script>
        EngagementChart = function () {
            var e = $("#plan_order");
            e.length && e.each(function () {
                !function (e) {
                    var t = {
                        chart: {width: "100%", zoom: {enabled: !1}, toolbar: {show: !1}, shadow: {enabled: !1}},
                        stroke: {width: 7, curve: "smooth"},
                        series: [
                            {
                                name: "<?php echo e(__('Order')); ?>",
                                data: <?php echo json_encode($chartData['data']); ?>

                            }
                        ],
                        xaxis: {
                            labels: {
                                format: "MMM",
                                style: {
                                    colors: PurposeStyle.colors.gray[600], fontSize: "14px", fontFamily: PurposeStyle.fonts.base, cssClass: "apexcharts-xaxis-label"
                                }
                            },
                            axisBorder: {
                                show: !1
                            },
                            axisTicks: {
                                show: !0, borderType: "solid", color: PurposeStyle.colors.gray[300], height: 6, offsetX: 0, offsetY: 0
                            },
                            type: "MMM",
                            categories: <?php echo json_encode($chartData['label']); ?>

                        },
                        yaxis: {
                            labels: {
                                style: {
                                    color: PurposeStyle.colors.gray[600], fontSize: "12px", fontFamily: PurposeStyle.fonts.base
                                }
                            },
                            axisBorder: {
                                show: !1
                            },
                            axisTicks: {
                                show: !0, borderType: "solid", color: PurposeStyle.colors.gray[300], height: 6, offsetX: 0, offsetY: 0
                            }
                        },
                        fill: {type: "solid"},
                        markers: {size: 4, opacity: .7, strokeColor: "#fff", strokeWidth: 3, hover: {size: 7}},
                        grid: {borderColor: PurposeStyle.colors.gray[300], strokeDashArray: 5},
                        dataLabels: {enabled: !1}
                    }, a = (e.data().dataset, e.data().labels, e.data().color), n = e.data().height, o = e.data().type;
                    t.colors = [PurposeStyle.colors.theme[a]], t.markers.colors = [PurposeStyle.colors.theme[a]], t.chart.height = n || 350, t.chart.type = o || "line";
                    var i = new ApexCharts(e[0], t);
                    setTimeout(function () {
                        i.render()
                    }, 300)
                }($(this))
            })
        }()
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Dashboard')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0"><?php echo e(__('Dashboard')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Dashboard')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-xl-4 col-sm-6">
            <div class="card card-stats border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-muted mb-1"><?php echo e(__('Total Users')); ?></h6>
                            <span class="h6 font-weight-bold mb-0 "><?php echo e($user->total_user); ?></span>
                        </div>
                        <div class="col-auto">
                            <h6 class="text-muted mb-1"><?php echo e(__('Paid Users')); ?></h6>
                            <span class="h6 font-weight-bold mb-0 "><?php echo e($user['total_paid_user']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card card-stats border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-muted mb-1"><?php echo e(__('Total Orders')); ?></h6>
                            <span class="h6 font-weight-bold mb-0 "><?php echo e($user->total_orders); ?></span>
                        </div>
                        <div class="col-auto">
                            <h6 class="text-muted mb-1"><?php echo e(__('Total Order Amount')); ?></h6>
                            <span class="h6 font-weight-bold mb-0 "><?php echo e(env("CURRENCY_SYMBOL").$user['total_orders_price']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card card-stats border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-muted mb-1"><?php echo e(__('Total Plans')); ?></h6>
                            <span class="h6 font-weight-bold mb-0 "><?php echo e(env("CURRENCY_SYMBOL").$user['total_orders_price']); ?></span>
                        </div>
                        <div class="col-auto">
                            <h6 class="text-muted mb-1"><?php echo e(__('Most Purchase Plan')); ?></h6>
                            <span class="h6 font-weight-bold mb-0 "><?php echo e(!empty($user['most_purchese_plan'])?$user['most_purchese_plan']:'-'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-md-12">
            <div class="card card-fluid">
                <div class="card-header">
                    <h6 class="mb-0"><?php echo e(__('Recent Order')); ?></h6>
                </div>
                <div class="card-body">
                    <div id="plan_order" data-color="primary" data-height="280"></div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/super_admin.blade.php ENDPATH**/ ?>