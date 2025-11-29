{{Form::open(array('url'=>'permission','method'=>'post'))}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Name')) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter User Name'),'required'=>'required'))}}
        </div>
        <div class="form-group">
            @if(!$roles->isEmpty())
                <h6>{{__('Assign Permission to Roles')}}</h6>
                @foreach ($roles as $role)
                    <div class="custom-control custom-checkbox">
                        {{Form::checkbox('roles[]',$role->id,false, ['class'=>'custom-control-input','id' =>'role'.$role->id])}}
                        {{Form::label('role'.$role->id, __(ucfirst($role->name)),['class'=>'custom-control-label '])}}
                    </div>
                @endforeach
            @endif
            @error('roles')
            <span class="invalid-roles" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="w-100 text-right">
        {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}
    </div>
</div>
{{Form::close()}}
