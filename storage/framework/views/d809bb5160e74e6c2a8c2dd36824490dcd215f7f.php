
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Home')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Dashboard')); ?></h5>
       
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-btn'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <?php if(\Auth::user()->type == 'owner'): ?>
        
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h6 class="text-muted mb-1"><?php echo e(__('Total Users')); ?></h6>
                                <span class="h3 font-weight-bold mb-0 "><?php echo e($data['totalUser']); ?></span>
                            </div>
                            <div class="col-auto">
                                <div class="icon bg-gradient-primary text-white rounded-circle icon-shape">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h6 class="text-muted mb-1"><?php echo e(__('Total Accounts')); ?></h6>
                                <span class="h3 font-weight-bold mb-0 "><?php echo e($data['totalAccount']); ?></span>
                            </div>
                            <div class="col-auto">
                                <div class="icon bg-gradient-primary text-white rounded-circle icon-shape">
                                    <i class="fas fa-building"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats">
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h6 class="text-muted mb-1"><?php echo e(__('Total Leads')); ?></h6>
                                <span class="h3 font-weight-bold mb-0 "><?php echo e($data['totalLead']); ?></span>
                            </div>
                            <div class="col-auto">
                                <div class="icon bg-gradient-primary text-white rounded-circle icon-shape">
                                    <i class="fas fa-address-card"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats">
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h6 class="text-muted mb-1"><?php echo e(__('Total Deals')); ?></h6>
                                <span class="h3 font-weight-bold mb-0 "><?php echo e($data['totalOpportunities']); ?></span>
                            </div>
                            <div class="col-auto">
                                <div class="icon bg-gradient-primary text-white rounded-circle icon-shape">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h6 class="text-muted mb-1"><?php echo e(__('Total Projects')); ?></h6>
                                <span class="h3 font-weight-bold mb-0 "><?php echo e($data['totalContact']); ?></span>
                            </div>
                            <div class="col-auto">
                                <div class="icon bg-gradient-primary text-white rounded-circle icon-shape">
                                    <i class="fas fa-id-badge"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h6 class="text-muted mb-1"><?php echo e(__('Total Properties')); ?></h6>
                                <span class="h3 font-weight-bold mb-0 "><?php echo e($data['totalProduct']); ?></span>
                            </div>
                            <div class="col-auto">
                                <div class="icon bg-gradient-primary text-white rounded-circle icon-shape">
                                    <i class="fas fa-cube"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h6 class="text-muted mb-1"><?php echo e(__('Total Campaigns')); ?></h6>
                                <span class="h3 font-weight-bold mb-0 "><?php echo e($data['totalProduct']); ?></span>
                            </div>
                            <div class="col-auto">
                                <div class="icon bg-gradient-primary text-white rounded-circle icon-shape">
                                    <i class="fas fa-cube"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats">
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h6 class="text-muted mb-1"><?php echo e(__('Total Documents')); ?></h6>
                                <span class="h3 font-weight-bold mb-0 "><?php echo e($data['totalInvoice']); ?></span>
                            </div>
                            <div class="col-auto">
                                <div class="icon bg-gradient-primary text-white rounded-circle icon-shape">
                                    <i class="fas fa-receipt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
          
        <?php endif; ?>
    </div>
    
    <div class="row">
        <div class="col-xl-6">
            <!-- Calendar -->
            <div class="card widget-calendar pt-4">
                <div class="col-md-12">
                    <div class="row justify-content-between align-items-center">
                        <div class="col d-flex align-items-center">
                            <h5 class="fullcalendar-title h4 d-inline-block font-weight-400 mb-0 pl-2"><?php echo e(__('Calendar')); ?></h5>
                        </div>
                        <div class="col-lg-6 mt-3 mt-lg-0 text-lg-right">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <a href="#" class="fullcalendar-btn-prev btn btn-sm btn-neutral">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                                <a href="#" class="fullcalendar-btn-next btn btn-sm btn-neutral">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                            </div>
                            <div class="btn-group pr-1" role="group" aria-label="Basic example">
                                <a href="#" class="btn btn-sm btn-neutral" data-calendar-view="month"><?php echo e(__('Month')); ?></a>
                                <a href="#" class="btn btn-sm btn-neutral" data-calendar-view="basicWeek"><?php echo e(__('Week')); ?></a>
                                <a href="#" class="btn btn-sm btn-neutral" data-calendar-view="basicDay"><?php echo e(__('Day')); ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <div class="card">
                                <div class="calendar" data-toggle="calendar" id="calendar"></div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <div class="col-xl-6">
            <div class="card card-fluid">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><?php echo e(__('Top Due Tasks')); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="list-group overflow-auto list-group-flush dashboard-box">
                    <?php $__empty_1 = true; $__currentLoopData = $data['topDueTask']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topDueTask): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col ml-n2">
                                    <a href="#!" class=" h6 mb-0"><?php echo e($topDueTask->name); ?></a>
                                    <div>
                                        <small><?php echo e(__('Assign to')); ?> <?php echo e(!empty($topDueTask->assign_user)?$topDueTask->assign_user->name  :''); ?></small>
                                    </div>
                                </div>
                                <div class="col">
                                    <span data-toggle="tooltip" data-title="<?php echo e(__('Project Title')); ?>"><?php echo e($topDueTask->description); ?></span>
                                </div>
                                <div class="col-auto">
                                    <span data-toggle="tooltip" data-title="<?php echo e(__('Due Date')); ?>"><?php echo e(\Auth::user()->dateFormat($topDueTask->due_date)); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-md-12 text-center">
                            <h6 class="m-3"><?php echo e(__('Task record not found')); ?></h6>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card card-fluid">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><?php echo e(__('Meeting Schedule')); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="list-group overflow-auto list-group-flush dashboard-box">
                    <?php $__empty_1 = true; $__currentLoopData = $data['topMeeting']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meeting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col ml-n2">
                                    <a href="#!" class=" h6 mb-0"><?php echo e($meeting->name); ?></a>
                                    <div>
                                        <small><?php echo e($meeting->description); ?></small>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <span data-toggle="tooltip" data-title="<?php echo e(__('Meetign Date')); ?>"><?php echo e($meeting->start_date); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-md-12 text-center">
                            <h6 class="m-3"><?php echo e(__('Meeting schedule not found')); ?></h6>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card card-fluid">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><?php echo e(__('Calls')); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="list-group overflow-auto list-group-flush dashboard-box">
                    <?php $__empty_1 = true; $__currentLoopData = $data['thisMonthCall']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $call): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col ml-n2">
                                    <a href="#!" class=" h6 mb-0"><?php echo e($call->name); ?></a>
                                    <div>
                                        <small><?php echo e($call->description); ?></small>
                                    </div>
                                </div>

                                <div class="col-auto">
                                    <span data-toggle="tooltip" data-title="<?php echo e(__('Start Date')); ?>"><?php echo e($call->start_date); ?></span>
                                </div>
                                <div class="col-auto">
                                    <span data-toggle="tooltip" data-title="<?php echo e(__('End Date')); ?>"><?php echo e($call->end_date); ?></span>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-md-12 text-center ">
                            <h6 class="'m-3"><?php echo e(__('Call not found')); ?></h6>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>
    <script>
        var options = {
            series: [
                {
                    name: "<?php echo e(__('Quote')); ?>",
                    data:  <?php echo json_encode($data['lineChartData']['quoteAmount']); ?>

                },
                {
                    name: "<?php echo e(__('Invoice')); ?>",
                    data: <?php echo json_encode($data['lineChartData']['invoiceAmount']); ?>

                },
                {
                    name: "<?php echo e(__('Sales Order')); ?>",
                    data: <?php echo json_encode($data['lineChartData']['salesorderAmount']); ?>

                }
            ],
            chart: {
                height: 350,
                type: 'line',
                dropShadow: {
                    enabled: true,
                    color: '#000',
                    top: 18,
                    left: 7,
                    blur: 10,
                    opacity: 0.2
                },
                toolbar: {
                    show: false
                }
            },
            colors: ['#77B6EA', '#51cb97', '#011c4b'],
            dataLabels: {
                enabled: true,
            },
            stroke: {
                curve: 'smooth'
            },
            title: {
                text: '',
                align: 'left'
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },
            },
            markers: {
                size: 1
            },
            xaxis: {
                categories: <?php echo json_encode($data['lineChartData']['day']); ?>,
                title: {
                    text: 'Days'
                }
            },
            yaxis: {
                title: {
                    text: '<?php echo e(__('Amount')); ?>'
                },
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: -25,
                offsetX: -5
            }
        };
        var chart = new ApexCharts(document.querySelector("#report-chart"), options);
        chart.render();


        Fullcalendar = function () {
            var e, t, a = $('[data-toggle="calendar"]');
            a.length && (t = {
                header: {right: "", center: "", left: ""},
                buttonIcons: {prev: "calendar--prev", next: "calendar--next"},
                theme: !1,
                selectable: !0,
                selectHelper: !0,
                editable: !1,
                events: <?php echo $data['calendar']; ?>,
               
                dayClick: function (e) {
                    var t = moment(e).toISOString();
                    $("#new-event").modal("show"), $(".new-event--title").val(""), $(".new-event--start").val(t), $(".new-event--end").val(t)
                },
                viewRender: function (t) {
                    e.fullCalendar("getDate").month(), $(".fullcalendar-title").html(t.title)
                },
                eventClick: function (e, t) {
                    $("#edit-event input[value=" + e.className + "]").prop("checked", !0), $("#edit-event").modal("show"), $(".edit-event--id").val(e.id), $(".edit-event--title").val(e.title), $(".edit-event--description").val(e.description)
                }
            }, (e = a).fullCalendar(t), $("body").on("click", ".new-event--add", function () {
                var t = $(".new-event--title").val(), a = {
                    Stored: [], Job: function () {
                        var e = Date.now().toString().substr(6);
                        return this.Check(e) ? this.Job() : (this.Stored.push(e), e)
                    }, Check: function (e) {
                        for (var t = 0; t < this.Stored.length; t++) if (this.Stored[t] == e) return !0;
                        return !1
                    }
                };
                "" != t ? (e.fullCalendar("renderEvent", {id: a.Job(), title: t, start: $(".new-event--start").val(), end: $(".new-event--end").val(), allDay: !0, className: $(".event-tag input:checked").val()}, !0), $(".new-event--form")[0].reset(), $(".new-event--title").closest(".form-group").removeClass("has-danger"), $("#new-event").modal("hide")) : ($(".new-event--title").closest(".form-group").addClass("has-danger"), $(".new-event--title").focus())
            }), $("body").on("click", "[data-calendar]", function () {
                var t = $(this).data("calendar"), a = $(".edit-event--id").val(), n = $(".edit-event--title").val(), o = $(".edit-event--description").val(), i = $("#edit-event .event-tag input:checked").val(), s = e.fullCalendar("clientEvents", a);
                "update" === t && ("" != n ? (s[0].title = n, s[0].description = o, s[0].className = [i], console.log(i), e.fullCalendar("updateEvent", s[0]), $("#edit-event").modal("hide")) : ($(".edit-event--title").closest(".form-group").addClass("has-error"), $(".edit-event--title").focus())), "delete" === t && ($("#edit-event").modal("hide"), setTimeout(function () {
                    swal({title: "Are you sure?", text: "You won't be able to revert this!", type: "warning", showCancelButton: !0, buttonsStyling: !1, confirmButtonClass: "btn btn-danger", confirmButtonText: "Yes, delete it!", cancelButtonClass: "btn btn-secondary"}).then(function (t) {
                        t.value && (e.fullCalendar("removeEvents", a), swal({title: "Deleted!", text: "The event has been deleted.", type: "success", buttonsStyling: !1, confirmButtonClass: "btn btn-primary"}))
                    })
                }, 200))
            }), $("body").on("click", "[data-calendar-view]", function (t) {
                t.preventDefault(), $("[data-calendar-view]").removeClass("active"), $(this).addClass("active");
                var a = $(this).attr("data-calendar-view");
                e.fullCalendar("changeView", a)
            }), $("body").on("click", ".fullcalendar-btn-next", function (t) {
                t.preventDefault(), e.fullCalendar("next")
            }), $("body").on("click", ".fullcalendar-btn-prev", function (t) {
                t.preventDefault(), e.fullCalendar("prev")
            }))
        }()
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/home.blade.php ENDPATH**/ ?>