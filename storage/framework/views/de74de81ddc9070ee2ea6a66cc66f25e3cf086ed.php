<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Report')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Lead Analytic')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Lead Analytic')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="cardcard-body">
                    <div class="collapse show float-right" id="collapseExample" style="">
                        <?php echo e(Form::open(array('route' => array('report.leadsanalytic'),'method'=>'get'))); ?>

                        <div class="row filter-css">
                            <div class="col-auto">
                                <?php echo e(Form::month('start_month',isset($_GET['start_month'])?$_GET['start_month']:date('Y-01'),array('class'=>'form-control'))); ?>

                            </div>
                            <div class="col-auto">
                                <?php echo e(Form::month('end_month',isset($_GET['end_month'])?$_GET['end_month']:date('Y-12'),array('class'=>'form-control'))); ?>

                            </div>

                            <div class="col-auto">
                                <?php echo e(Form::select('leadsource',$leadsource,isset($_GET['leadsource'])?$_GET['leadsource']:'', array('class' => 'form-control ','data-toggle'=>'select'))); ?>

                            </div>
                            <div class="col-auto">
                                <?php echo e(Form::select('status', [''=>'Select Status']+$status,isset($_GET['status'])?$_GET['status']:'', array('class' => 'form-control ','data-toggle'=>'select'))); ?>

                            </div>
                            <div class="col-auto pt-2">
                                <button type="submit" class="btn btn-xs btn-primary btn-icon-only rounded-circle" data-toggle="tooltip" data-title="<?php echo e(__('Apply')); ?>"><i class="fas fa-search"></i></button>
                            </div>                     
                            <?php echo e(Form::close()); ?>

                            <div class="col-auto pt-2">
                                <a href="<?php echo e(route('report.leadsanalytic')); ?>" data-toggle="tooltip" data-title="<?php echo e(__('Reset')); ?>" class="btn btn-xs btn-danger btn-icon-only rounded-circle"><i class="fas fa-trash-restore"></i></a>
                            </div>
                            <div class="col-auto p-2">
                                <a href="#" onclick="saveAsPDF();" class="btn btn-xs btn-primary btn-icon-only rounded-circle" data-toggle="tooltip" data-title="<?php echo e(__('Download')); ?>" id="download-buttons">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="printableArea">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <dl class="row">
                            <?php if(isset($report['startDateRange']) || isset($report['endDateRange'])): ?>
                                <input type="hidden" value="<?php echo e(__('Lead Report of').' '.$report['startDateRange'].' to '.$report['endDateRange']); ?>" id="filesname">
                            <?php else: ?>
                                <input type="hidden" value="<?php echo e(__('Lead Report')); ?>" id="filesname">
                            <?php endif; ?>

                            <div class="col">
                                <?php echo e(__('Report')); ?> : <h6><?php echo e(__('Lead Summary')); ?></h6>
                            </div>
                            <div class="col">
                                <?php echo e(__('Duration')); ?> : <h6><?php echo e($report['startDateRange'].' to '.$report['endDateRange']); ?></h6>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div id="report-chart" data-color="primary"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table align-items-center" id="reportTable">
                    <thead>
                    <tr>
                        <th scope="col" class="sort" data-sort="name"><?php echo e(__('Name')); ?></th>
                        <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Account Name')); ?></th>
                        <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Campaign Name')); ?></th>
                        <th scope="col" class="sort" data-sort="name"><?php echo e(__('Email')); ?></th>
                        <th scope="col" class="sort" data-sort="name"><?php echo e(__('Phone')); ?></th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    <?php $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <?php echo e($result['name']); ?>

                            </td>
                            <td>
                                <?php echo e(!empty($result['account_name'])?$result['account_name']:'-'); ?>

                            </td>
                            <td>
                                <?php echo e(!empty($result['campaign_name'])?$result['campaign_name']:'-'); ?>

                            </td>
                            <td class="">
                                <?php echo e($result['email']); ?>

                            </td>
                            <td class="">
                                <?php echo e($result['phone']); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/html2pdf.bundle.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/dataTables.buttons.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/jszip.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/pdfmake.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/vfs_fonts.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/buttons.html5.min.js')); ?>"></script>

    <script>
        $(document).ready(function () {
            var filename = $('#filename').val();
            setTimeout(function () {
                $('#reportTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            title: filename
                        }, {
                            extend: 'csvHtml5',
                            title: filename
                        }, {
                            extend: 'pdfHtml5',
                            title: filename
                        },
                    ],
                
                });
            }, 500);

        });
    </script>

    <script>
        var filename = $('#filesname').val();

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {type: 'jpeg', quality: 1},
                html2canvas: {scale: 4, dpi: 72, letterRendering: true},
                jsPDF: {unit: 'in', format: 'A2'}
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>

    <script>
        var WorkedHoursChart = (function () {
            var $chart = $('#report-chart');

            function init($this) {
                var options = {
                    chart: {
                        width: '100%',
                        type: 'bar',
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        },
                        shadow: {
                            enabled: false,
                        },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '30%',
                            endingShape: 'rounded'
                        },
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    series: [{
                        name: 'Lead',
                        data: <?php echo json_encode($data); ?>,
                    }],
                    xaxis: {
                        labels: {
                         
                            style: {
                                colors: PurposeStyle.colors.gray[600],
                                fontSize: '14px',
                                fontFamily: PurposeStyle.fonts.base,
                                cssClass: 'apexcharts-xaxis-label',
                            },
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: true,
                            borderType: 'solid',
                            color: PurposeStyle.colors.gray[300],
                            height: 6,
                            offsetX: 0,
                            offsetY: 0
                        },
                        title: {
                            text: '<?php echo e(__('Lead')); ?>'
                        },
                        categories: <?php echo json_encode($labels); ?>,
                    },
                    yaxis: {
                        labels: {
                            style: {
                                color: PurposeStyle.colors.gray[600],
                                fontSize: '12px',
                                fontFamily: PurposeStyle.fonts.base,
                            },
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: true,
                            borderType: 'solid',
                            color: PurposeStyle.colors.gray[300],
                            height: 6,
                            offsetX: 0,
                            offsetY: 0
                        }
                    },
                    fill: {
                        type: 'solid'
                    },
                    markers: {
                        size: 4,
                        opacity: 0.7,
                        strokeColor: "#fff",
                        strokeWidth: 3,
                        hover: {
                            size: 7,
                        }
                    },
                    grid: {
                        borderColor: PurposeStyle.colors.gray[300],
                        strokeDashArray: 5,
                    },
                    dataLabels: {
                        enabled: false
                    }
                }
                // Get data from data attributes
                var dataset = $this.data().dataset,
                    labels = $this.data().labels,
                    color = $this.data().color,
                    height = $this.data().height,
                    type = $this.data().type;
                // Inject synamic properties
                options.colors = [
                    PurposeStyle.colors.theme[color]
                ];
                options.markers.colors = [
                    PurposeStyle.colors.theme[color]
                ];
                options.chart.height = height ? height : 350;
                // Init chart
                var chart = new ApexCharts($this[0], options);
                // Draw chart
                setTimeout(function () {
                    chart.render();
                }, 300);
            }

            // Events
            if ($chart.length) {
                $chart.each(function () {
                    init($(this));
                });
            }
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/report/leadsanalytic.blade.php ENDPATH**/ ?>