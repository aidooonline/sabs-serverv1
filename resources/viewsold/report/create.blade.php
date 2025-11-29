{{Form::open(array('url'=>'report','method'=>'post','enctype'=>'multipart/form-data'))}}
<div class="row">
    <div class="col-6">
        <div class="form-group">
            {{Form::label('name',__('Name')) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('chart_type',__('Chart Type')) }}
            {!!Form::select('chart_type', $chart_type,null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('entity_type',__('Entity Type')) }}
            {!!Form::select('entity_type', $entity_type,null,array('class' => 'form-control ','name'=>'entity_type','id'=>'entity_type','data-toggle'=>'select','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('group_by',__('Group By')) }}
            <select class="form-control select2" data-toggle="select" name="group_by" id="group_by">

            </select>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('Assign User',__('Assign User')) }}
            {!! Form::select('user', $user, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
        </div>
    </div>
</div>
<div class="w-100 text-right">
    {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}{{Form::close()}}
</div>
</div>
{{Form::close()}}
