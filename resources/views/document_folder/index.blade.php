@extends('layouts.admin')
@section('page-title')
    {{__('Document Folders')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Document Folders')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Document Folders')}}</li>
@endsection
@section('action-btn')
    @can('Create DocumentFolder')
        <a href="#" data-size="lg" data-url="{{ route('document_folder.create') }}" data-ajax-popup="true" data-title="{{__('Create New Document Folders')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="">
        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name">{{__('Folder Name')}}</th>
                    @if(Gate::check('Edit DocumentFolder') || Gate::check('Delete DocumentFolder'))
                        <th class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                    @foreach($folders as $folder)
                        <tr>
                            <td class="sorting_1">{{$folder->name}}</td>
                            @if(Gate::check('Edit DocumentFolder') || Gate::check('Delete DocumentFolder'))
                                <td class="action text-right">
                                    @can('Edit DocumentFolder')
                                        <a href="#" data-size="lg" data-url="{{ route('document_folder.edit',$folder->id) }}" data-ajax-popup="true" data-title="{{__('Edit type')}}" class="action-item">
                                            <i class="far fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('Delete DocumentFolder')
                                        <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$folder->id}}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['document_folder.destroy', $folder->id],'id'=>'delete-form-'.$folder->id]) !!}
                                        {!! Form::close() !!}
                                    @endcan
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('scrip-page')
    <script>
        $(document).delegate("li .li_title", "click", function (e) {
            $(this).closest("li").find("ul:first").slideToggle(300);
            $(this).closest("li").find(".location_picture_row:first").slideToggle(300);
            if ($(this).find("i").attr('class') == 'glyph-icon simple-icon-arrow-down') {
                $(this).find("i").removeClass("simple-icon-arrow-down").addClass("simple-icon-arrow-right");
            } else {
                $(this).find("i").removeClass("simple-icon-arrow-right").addClass("simple-icon-arrow-down");
            }
        });
    </script>
@endpush
