@extends('layouts.admin')
@section('page-title')
    {{__('Report')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Report')}} {{ '('. $report->name .')' }}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('report.index')}}">{{__('Report')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Details')}}</li>
@endsection
@section('action-btn')
    @can('Edit Report')
        <a href="{{ route('report.edit',$report->id) }}" class="btn btn-sm btn-primary bor-radius" data-title="{{__('Report Edit')}}"><i class="far fa-edit"></i>
        </a>
    @endcan
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="cardcard-body">
                    <div class="collapse show float-right" id="collapseExample" style="">
                        {{ Form::open(array('route' => array('report.show',$report->id),'method'=>'get')) }}
                        <div class="row filter-css">
                            <div class="col-auto">
                                {{Form::date('start_date',isset($_GET['start_date'])?$_GET['start_date']:'',array('class'=>'form-control'))}}
                            </div>
                            <div class="col-auto">
                                {{Form::date('end_date',isset($_GET['end_date'])?$_GET['end_date']:'',array('class'=>'form-control'))}}
                            </div>
                                    <div class="col-auto pt-2">
                                <button type="submit" class="btn btn-xs btn-primary btn-icon-only rounded-circle" data-toggle="tooltip" data-title="{{__('Apply')}}"><i class="fas fa-search"></i></button>
                            </div>
                            <div class="col-auto pt-2">
                                <a href="{{route('report.show',$report->id)}}" data-toggle="tooltip" data-title="{{__('Reset')}}" class="btn btn-xs btn-danger btn-icon-only rounded-circle"><i class="fas fa-trash-restore"></i></a>
                            </div>
                            <div class="col-auto pt-2">
                                <a href="#" onclick="saveAsPDF();" class="btn btn-xs btn-primary btn-icon-only rounded-circle" data-toggle="tooltip" data-title="{{__('Download')}}" id="download-buttons">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                        {{ Form::close() }}
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
                
                            @if(!empty($report['startDateRange']) || $report['endDateRange'])
                                <input type="hidden" value="{{$report['name'].' '.__('Report of').' '.$report['startDateRange'].' to '.$report['endDateRange']}}" id="filesname">
                            @else
                                <input type="hidden" value="{{$report['name'].' '.__('Report')}}" id="filesname">
                            @endif
                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Name')}}</span></dt>
                            <dd class="col-sm-3"><span class="text-sm">{{ $report->name }}</span></dd>

                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Entity Type')}}</span></dt>
                            <dd class="col-sm-3"><span class="text-sm">{{ucfirst($report->entity_type)}}</span></dd>

                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Assigned User')}}</span></dt>
                            <dd class="col-sm-3"><span class="text-sm">{{ !empty($report->assign_user)?$report->assign_user->name:''}}</span></dd>


                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Group By')}}</span></dt>
                            <dd class="col-sm-3"><span class="text-sm">

                        <td>
                             <span class="badge badge-pill badge-primary text-xs small">
                            @if($report->entity_type == 'users')
                                     {{__(\App\Report::$users[$report->group_by])}}
                                 @elseif($report->entity_type == 'quotes')
                                     {{__(\App\Report::$quotes[$report->group_by])}}
                                 @elseif($report->entity_type == 'accounts')
                                     {{__(\App\Report::$accounts[$report->group_by])}}
                                 @elseif($report->entity_type == 'contacts')
                                     {{__(\App\Report::$contacts[$report->group_by])}}
                                 @elseif($report->entity_type == 'leads')
                                     {{__(\App\Report::$leads[$report->group_by])}}
                                 @elseif($report->entity_type == 'opportunities')
                                     {{__(\App\Report::$opportunities[$report->group_by])}}
                                 @elseif($report->entity_type == 'invoices')
                                     {{__(\App\Report::$invoices[$report->group_by])}}
                                 @elseif($report->entity_type == 'cases')
                                     {{__(\App\Report::$cases[$report->group_by])}}
                                 @elseif($report->entity_type == 'products')
                                     {{__(\App\Report::$products[$report->group_by])}}
                                 @elseif($report->entity_type == 'tasks')
                                     {{__(\App\Report::$tasks[$report->group_by])}}
                                 @elseif($report->entity_type == 'calls')
                                     {{__(\App\Report::$calls[$report->group_by])}}
                                 @elseif($report->entity_type == 'campaigns')
                                     {{__(\App\Report::$campaigns[$report->group_by])}}
                                 @elseif($report->entity_type == 'sales_orders')
                                     {{__(\App\Report::$sales_orders[$report->group_by])}}
                                 @else
                                     {{__(\App\Report::$users[$report->group_by])}}
                                 @endif
                            </span>
                        </td></span></dd>

                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Chart Type')}}</span></dt>
                            <dd class="col-sm-3">
                            <span class="text-sm">
                            @if($report->chart_type == 0)
                                    <span>{{ __(\App\Report::$chart_type[$report->chart_type]) }}</span>
                                @endif
                            </span>
                            </dd>

                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                            <dd class="col-sm-3"><span class="text-sm">{{\Auth::user()->dateFormat($report->created_at)}}</span></dd>

                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Report')}}</span></dt>
                            <dd class="col-sm-3"><span class="text-sm">{{ucfirst($entity_type).' '.__('Summary')}}</span></dd>
                            @if(!empty($report['startDateRange'] || $report['endDateRange'] ))
                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Duration')}}</span></dt>
                            <dd class="col-sm-3"><span class="text-sm">{{ $report['startDateRange'] .' '. 'to' . ' '.  $report['endDateRange']}}</span></dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body" style="@if($report->chart_type == 'pie') align-self: center;@endif">
                        <div id="report-chart" data-color="primary"></div>
                    </div>
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
                        <th scope="col" class="sort" data-sort="name">#</th>
                        <th scope="col" class="sort" data-sort="budget">{{__('Total')}}</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    @foreach($data as $result)
                        @php( $groupBy = $group_by . '_name')
                        <tr>
                            <td>
                                {{ $result[$groupBy] }}
                            </td>
                            <td class="">
                                {{ $result['count'] }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
@push('script-page')
    <script type="text/javascript" src="{{ asset('assets/js/html2pdf.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jszip.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pdfmake.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/vfs_fonts.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/buttons.html5.min.js')}}"></script>

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
        var chart_type = '{{$report->chart_type}}';
        if (chart_type == 'bar_vertical' || chart_type == 'bar_horizontal') {
            if (chart_type == 'bar_vertical') {
                chart_type = 'bar';
                var types = false;
            } else {
                chart_type = 'bar';
                var types = true;
            }

            var WorkedHoursChart = (function () {
                var $chart = $('#report-chart');

                function init($this) {
                    var options = {
                        chart: {
                            width: '100%',
                            type: chart_type,
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
                                horizontal: types,
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
                            name: '{!! $entity_type !!}',
                            data: {!! json_encode($record) !!},
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
                                text: '{!! ucfirst(str_replace('_', ' ', $group_by)) !!}'
                            },
                            categories: {!! json_encode($label) !!},
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
        } else if (chart_type == 'line') {
            var e = $("#report-chart");
            !function (e) {
                var t = {
                    chart: {width: "100%", zoom: {enabled: !1}, toolbar: {show: !1}, shadow: {enabled: !1}},
                    stroke: {width: 6, curve: "smooth"},
                    series: [{
                        name: "{{__('Order')}}",
                        data: {!! json_encode($record) !!}}],
                    xaxis: {labels: {format: "MMM", style: {colors: PurposeStyle.colors.gray[600], fontSize: "14px", fontFamily: PurposeStyle.fonts.base, cssClass: "apexcharts-xaxis-label"}}, axisBorder: {show: !1}, axisTicks: {show: !0, borderType: "solid", color: PurposeStyle.colors.gray[300], height: 6, offsetX: 0, offsetY: 0}, type: "text", categories:{!! json_encode($label) !!}},
                    yaxis: {labels: {style: {color: PurposeStyle.colors.gray[600], fontSize: "12px", fontFamily: PurposeStyle.fonts.base}}, axisBorder: {show: !1}, axisTicks: {show: !0, borderType: "solid", color: PurposeStyle.colors.gray[300], height: 6, offsetX: 0, offsetY: 0}},
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
            }($("#report-chart"));
        } else {
            var options = {
                series: {!! json_encode($record) !!},
                chart: {
                    width: 600,
                    type: 'pie',
                },
                labels:{!! json_encode($label) !!},
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom',
                        }
                    }
                }]
            };

            var chart = new ApexCharts(document.querySelector("#report-chart"), options);
            chart.render();
        }
    </script>
@endpush
