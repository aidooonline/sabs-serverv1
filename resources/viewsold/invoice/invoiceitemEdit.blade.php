{{Form::model($invoiceItem,array('route' => array('invoice.item.update', $invoiceItem->id), 'method' => 'POST')) }}
<div class="row">
    <div class="form-group col-md-6">
        {{ Form::label('item', __('Item')) }}
        {{ Form::select('item', $items,null, array('class' => 'form-control items','data-toggle'=>'select','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('quantity', __('Quantity')) }}
        {{ Form::number('quantity',null, array('class' => 'form-control quantity','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('price', __('Price')) }}
        {{ Form::number('price',null, array('class' => 'form-control price','required'=>'required','stage'=>'0.01')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('discount', __('Discount')) }}
        {{ Form::number('discount',null, array('class' => 'form-control discount')) }}
    </div>
    <div class="form-group col-md-12">
        {{ Form::label('tax', __('Tax')) }}
        {{ Form::hidden('tax',null, array('class' => 'form-control taxId')) }}
        <div class="row">
            <div class="col-md-12">
                <div class="tax"></div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-12">
        {{ Form::label('description', __('Description')) }}
        {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'2']) !!}
    </div>
    <div class="col-md-12">
        <div class="modal-footer">
            {{Form::submit(__('Update'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
        </div>
    </div>
</div>
{{ Form::close() }}
<script>
    $('.items').val({{$invoiceItem->item}}).trigger("change")
</script>
