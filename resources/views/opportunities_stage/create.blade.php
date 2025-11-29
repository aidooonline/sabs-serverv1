{{Form::open(array('url'=>'opportunities_stage','method'=>'post'))}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Opportunities Stage')) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Opportunities Stage'),'required'=>'required'))}}
        </div>
    </div>
    <div class="w-100 text-right">
        {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}
    </div>
</div>
{{Form::close()}}
