{{ Form::open(array('route' => ['form.field.store',$formbuilder->id])) }}
<div class="row" id="frm_field_data">
    <div class="col-12 form-group">
        {{ Form::label('name', __('Question Name'),['class'=>'form-control-label']) }}
        {{ Form::text('name[]', '', array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="col-12 form-group">
        {{ Form::label('type', __('Type'),['class'=>'form-control-label']) }}
        {{ Form::select('type[]', $types,null, array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) }}
    </div>
    <div class="modal-footer">
        {{Form::submit(__('Create'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
    </div>
</div>
{{ Form::close() }}
