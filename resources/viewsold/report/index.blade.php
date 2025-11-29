@extends('layouts.admin')
@section('page-title')
    {{__('Report')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Report')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Report')}}</li>
@endsection
@section('action-btn')
       @can('Create Report')
    <a href="#" data-size="lg" data-url="{{ route('report.create',['report',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Report')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
        <i class="fa fa-plus"></i>
    </a>
       @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="card">
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                    <th scope="col" class="sort" data-sort="budget">{{__('Entity Type')}}</th>
                    <th scope="col" class="sort" data-sort="budget">{{__('Group By')}}</th>
                    <th scope="col" class="sort" data-sort="budget">{{__('Chart Type')}}</th>
                    @if(Gate::check('Show Report') || Gate::check('Edit Report') || Gate::check('Delete Report'))
                    <th scope="col" class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($reports as $report)
                    <tr>
                        <td>
                            <a href="{{ route('report.show',$report->id) }}" class="action-item">
                                {{ $report->name }}
                            </a>
                        </td>
                        <td class="budget">
                            <span>{{ __(\App\Report::$entity_type[$report->entity_type]) }}</span>
                        </td>
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
                        </td>
                        <td class="budget">
                            {{__(\App\Report::$chart_type[$report->chart_type])}}
                        </td>
                        @if(Gate::check('Show Report') || Gate::check('Edit Report') || Gate::check('Delete Report'))
                        <td>
                            <div class="d-flex float-right">
                            @can('Show Report')
                                <a href="{{ route('report.show',$report->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-title="{{__('Report Details')}}">
                                    <i class="far fa-eye"></i>
                                </a>
                            @endcan
                            @can('Edit Report')
                                <a href="{{ route('report.edit',$report->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Report Edit')}}"><i class="far fa-edit"></i></a>
                            @endcan
                            @can('Delete Report')
                                <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$report->id}}').submit();">
                                    <i class="fas fa-trash"></i>
                                </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['report.destroy', $report->id],'id'=>'delete-form-'.$report ->id]) !!}
                                {!! Form::close() !!}
                            @endcan
                            </div>
                        </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('script-page')
    <script>
        $(document).on('change', 'select[name=entity_type]', function () {
            var parent = $(this).val();
            getparent(parent);
        });

        function getparent(bid) {
            console.log(bid);
            $.ajax({
                url: '{{route('report.getparent')}}',
                type: 'POST',
                data: {
                    "parent": bid, "_token": "{{ csrf_token() }}",
                },
                success: function (data) {
                    console.log(data);
                    $('#group_by').empty();
                    {{--$('#group_by').append('<option value="">{{__('Select Parent')}}</option>');--}}

                    $.each(data, function (key, value) {
                        $('#group_by').append('<option value="' + key + '">' + value + '</option>');
                    });
                    if (data == '') {
                        $('#group_by').empty();
                    }
                }
            });
        }
    </script>
@endpush
