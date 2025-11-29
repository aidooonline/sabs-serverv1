{{Form::open(array('url'=>'target_list','method'=>'post'))}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Target List')) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Target List'),'required'=>'required'))}}
        </div>
        <div class="form-group">
            {{Form::label('description',__('Description')) }}
            {{Form::textarea('description',null,array('class'=>'form-control','rows'=>'3','placeholder'=>__('Enter Description')))}}
        </div>
    </div>
    <div class="w-100 text-right">
        {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}
    </div>
</div>
{{Form::close()}}
