@extends('layouts.admin')
@section('page-title')
    {{__('Report')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Sales Order Analytic')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Sales Order Analytic')}}</li>
   
@endsection
@section('action-btn')
  
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="cardcard-body">
                    <div class="collapse show float-right" id="collapseExample" style="">
                        
                        {{ Form::open(array('route' => array('report.salesorderanalytic'),'method'=>'get')) }}
                        <div class="row filter-css">
                            <div class="col-auto">
                                {{Form::month('start_month',isset($_GET['start_month'])?$_GET['start_month']:date('Y-01'),array('class'=>'form-control'))}}
                            </div>
                            <div class="col-auto">
                                {{Form::month('end_month',isset($_GET['end_month'])?$_GET['end_month']:date('Y-12'),array('class'=>'form-control'))}}
                            </div>
                            <div class="col-auto">
                                {{ Form::select('status', [''=>'Select Status']+$status,isset($_GET['status'])?$_GET['status']:'', array('class' => 'form-control ','data-toggle'=>'select')) }}
                            </div>

                            <div class="col-auto pt-2">
                                <button type="submit" class="btn btn-xs btn-primary btn-icon-only rounded-circle" data-toggle="tooltip" data-title="{{__('Apply')}}"><i class="fas fa-search"></i></button>
                            </div>
                            <div class="col-auto pt-2">
                                <a href="{{route('report.salesorderanalytic')}}" data-toggle="tooltip" data-title="{{__('Reset')}}" class="btn btn-xs btn-danger btn-icon-only rounded-circle"><i class="fas fa-trash-restore"></i></a>
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
                            @if(isset($report['startDateRange']) || isset($report['endDateRange']))
                                <input type="hidden" value="{{__('Salesorder Report of').' '.$report['startDateRange'].' to '.$report['endDateRange']}}" id="filesname">
                            @else
                                <input type="hidden" value="{{__('Salesorder Report')}}" id="filesname">
                            @endif

                            <div class="col">
                                {{__('Report')}} : <h6>{{__('Salesorder Summary')}}</h6>
                            </div>
                            <div class="col">
                                {{__('Duration')}} : <h6>{{$report['startDateRange'].' to '.$report['endDateRange']}}</h6>
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
                        <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                        <th scope="col" class="sort" data-sort="budget">{{__('Quote Name')}}</th>
                        <th scope="col" class="sort" data-sort="name">{{__('Account')}}</th>
                        <th scope="col" class="sort" data-sort="budget">{{__('Status')}}</th>
                        <th scope="col" class="sort" data-sort="name">{{__('Date Sales Order')}}</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    @foreach($salesorder as $result)
                        <tr>
                            <td>
                                {{$result['name']}}
                            </td>
                            <td>
                                {{!empty($result['quotes']['name'])?$result['quotes']['name']:'-'}}
                            </td>
                            <td class="">
                                {{!empty($result['accounts']['name'])?$result['accounts']['name']:'-'}}
                            </td>
                            <td>
                                @if($result->status == 0)
                                    <span class="badge badge-info">{{ __(\App\SalesOrder::$status[$result->status]) }}</span>
                                @elseif($result->status == 1)
                                    <span class="badge badge-info">{{ __(\App\SalesOrder::$status[$result->status]) }}</span>
                                @elseif($result->status == 2)
                                    <span class="badge badge-info">{{ __(\App\SalesOrder::$status[$result->status]) }}</span>
                                @elseif($result->status == 3)
                                    <span class="badge badge-success">{{ __(\App\SalesOrder::$status[$result->status]) }}</span>
                                @elseif($result->status == 4)
                                    <span class="badge badge-warning">{{ __(\App\SalesOrder::$status[$result->status]) }}</span>
                                @elseif($result->status == 5)
                                    <span class="badge badge-danger">{{ __(\App\SalesOrder::$status[$result->status]) }}</span>
                                @endif
                            </td>
                            <td class="">
                                {{\Auth::user()->dateFormat($result['date_quoted'])}}
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
                        name: 'Salesorder',
                        data: {!! json_encode($salesorderTotal) !!},
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
                            text: '{{__('Salesorder')}}'
                        },
                        categories: {!! json_encode($monthList) !!},
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
@endpush
