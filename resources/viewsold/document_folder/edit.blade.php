{{Form::model($documentFolder, array('route' => array('document_folder.update', $documentFolder->id), 'method' => 'PUT')) }}
<div class="row">
    <div class="col-6">
        <div class="form-group">
            {{Form::label('name',__('Name'))}}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
            @error('name')
            <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-6">
        {{Form::label('parent',__('parent')) }}
        {!! Form::select('parent', $parent, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
        @error('parent')
        <span class="invalid-parent" role="alert">
        <strong class="text-danger">{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('description',__('Description')) }}
            {!! Form::textarea('description',null,array('class' =>'form-control ','data-toggle'=>'select','rows'=>3)) !!}
            @error('description')
            <span class="invalid-description" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
</div>
<div class="text-right">
    {{Form::submit(__('Update'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
</div>
{{Form::close()}}
