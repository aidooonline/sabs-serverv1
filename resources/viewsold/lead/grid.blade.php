@extends('layouts.admin')
@section('page-title')
    {{__('Lead')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Lead')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Lead')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('lead.index') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('List View')}}
    </a>
    @can('Create Lead')
        <a href="#" data-size="lg" data-url="{{ route('lead.create',['lead',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Lead')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@push('script-page')
    <script src="{{asset('assets/libs/dragula/dist/dragula.min.js')}}"></script>
    <script src="{{asset('assets/libs/autosize/dist/autosize.min.js')}}"></script>
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
                            url: '{{route('lead.change.order')}}',
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
@endpush
@section('filter')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="container-kanban">
                    @php
                        $json = [];
                        foreach($statuss as $id => $status){
                            $json[] = 'kanban-blacklist-'.$id;
                        }
                    @endphp
                    <div class="kanban-board" data-toggle="dragula" data-containers='{!! json_encode($json) !!}'>
                        @foreach($statuss as $id=>$status)
                            @php
                                $leads =\App\Lead::leads($id);
                            @endphp
                            <div class="kanban-col px-0">
                                <div class="card-list card-list-flush">
                                    <div class="card-list-title row align-items-center mb-3">
                                        <div class="col">
                                            <h6 class="mb-0 text-white">{{$status}}</h6>
                                        </div>
                                        <div class="col text-right">
@if($leads)
                                            <span class="badge badge-secondary rounded-pill">{{count($leads)}}</span>
@endif

                                        </div>
                                    </div>
                                    <div class="card-list-body" id="kanban-blacklist-{{$id}}" data-id="{{$id}}">
@if($leads)
                                        @foreach($leads as $lead)
                                            <div class="card card-progress draggable-item border shadow-none" data-id="{{$lead->id}}">
                                                <div class="card-body">
                                                    <div class="row align-items-center mb-1">
                                                        <div class="col-6">
                                                            <h5 class="h6 mb-0">
                                                                <a data-size="lg" href="{{ route('lead.edit',$lead->id) }}" data-title="{{__('Edit Lead')}}">
                                                                    {{ ucfirst($lead->name)}}
                                                                </a>
                                                            </h5>
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <div class="actions">
                                                                @if(Gate::check('Show Lead') || Gate::check('Edit Lead') || Gate::check('Delete Lead'))
                                                                    <div class="dropdown">
                                                                        <a href="#" class="action-item" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                            <i class="fas fa-ellipsis-h"></i>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(22px, 31px, 0px);">
                                                                            @can('Show Lead')
                                                                                <a href="#" data-size="lg" data-url="{{ route('lead.show', $lead->id) }}" data-ajax-popup="true" data-title="{{__('Lead Details')}}" class="dropdown-item">
                                                                                    {{__('View')}}
                                                                                </a>
                                                                            @endcan
                                                                            @can('Edit Lead')
                                                                                <a class="dropdown-item" data-size="lg" href="{{ route('lead.edit',$lead->id) }}" data-title="{{__('Edit Lead')}}"> {{__('Edit')}}</a>
                                                                            @endcan
                                                                            @can('Delete Lead')
                                                                                <a class="dropdown-item" href="#" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="document.getElementById('task-delete-form-{{$lead->id}}').submit();"> {{__('Delete')}}</a>

                                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['lead.destroy', $lead->id],'id'=>'task-delete-form-'.$lead->id]) !!}
                                                                                {!! Form::close() !!}
                                                                            @endcan

                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row align-items-center mb-3">
                                                       
                                                        <div class="col-6">
                                                            <span class=@if($lead->lead_temperature == '1'){{"coldlead"}}@elseif($lead->lead_temperature == '2'){{"warmlead"}}@else{{"hotlead"}} @endif>
                                                                @if($lead->lead_temperature == '1'){{'cold'}}
                                                                @elseif($lead->lead_temperature == '2'){{'warm'}}
                                                                @else{{'hot'}} 
                                                                @endif
                                                            </span>

                                                            @if($lead  ->status == 0)
                                                            <span class="badge text-success" style="font-size:13px;">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                                                        @elseif($lead->status == 1)
                                                            <span class="badge text-info" style="font-size:13px;">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                                                        @elseif($lead->status == 2)
                                                            <span class="badge text-warning" style="font-size:13px;">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                                                        @elseif($lead->status == 3)
                                                            <span class="badge text-danger"  style="font-size:13px;">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                                                        @elseif($lead->status == 4)
                                                            <span class="badge text-danger" style="font-size:13px;">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                                                        @elseif($lead->status == 5)
                                                            <span class="badge text-warning" style="font-size:13px;">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                                                        @endif

                                                            <h5 class="h6 mb-0">
                                                                <a href="#" class="text-sm" title="Ladna Barka">{{ucfirst(!empty($lead->accounts)?$lead->accounts->name:'-')}}</a>
                                                            </h5>
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <div class="col-auto actions">
                                                                <div class="dropdown" data-toggle="dropdown">
                                                                    <a href="#" class="action-item outline-none">
                                                                        <span>{{\Auth::user()->priceFormat($lead->opportunity_amount)}}</span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row align-items-center">
                                                      
                                                            <div class="actions d-inline-block" style="font-size:12px;" title="{{\Auth::user()->dateFormat($lead->created_at)}}">
                                                                <i class="fas fa-clock mr-2"></i>
                                                                {{$lead->created_at->diffForHumans()}}
                                                            </div>
                                                       
                                                        
                                                            <div class="avatar-group hover-avatar-ungroup">
                                                                @foreach($users as $user)
                                                                    <a href="#" class="avatar rounded-circle avatar-sm" data-original-title="{{$user->name}}" data-toggle="tooltip">
                                                                        <img @if(!empty($user->avatar)) src="{{asset('/storage/upload/profile/'.$user->avatar)}}" @else avatar="{{$user->name}}" @endif class="">
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
@endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
