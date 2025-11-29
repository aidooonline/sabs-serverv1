{{Form::open(array('url'=>'product','method'=>'post','enctype'=>'multipart/form-data'))}}
<div class="row">
    <div class="col-6">
        <div class="form-group">
            {{Form::label('name',__('Name')) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('status',__('Status')) }}
            {!!Form::select('status', $status, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('category',__('Category')) }}
            {!!Form::select('category', $category, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('brand',__('Brand')) }}
            {!!Form::select('brand', $brand, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('price',__('Price')) }}
            {{Form::text('price',null,array('class'=>'form-control','placeholder'=>__('Enter Price'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('tax', __('Tax')) }}
            {{ Form::select('tax[]', $tax,null, array('class' => 'form-control','data-toggle'=>'select','multiple')) }}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('part_number',__('Part Number')) }}
            {{Form::text('part_number',null,array('class'=>'form-control','placeholder'=>__('Enter Part Number'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('weight',__('Weight')) }}
            {{Form::text('weight',null,array('class'=>'form-control','placeholder'=>__('Enter Weight')))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('URL',__('URL')) }}
            {{Form::text('URL',null,array('class'=>'form-control','placeholder'=>__('Enter URL'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('description',__('Description')) }}
            {{Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description')))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('Assign User',__('Assign User')) }}
            {!! Form::select('user', $user, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
        </div>
    </div>
    <div class="w-100 text-right">
        {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}{{Form::close()}}
    </div>
</div>
</div>
{{Form::close()}}

