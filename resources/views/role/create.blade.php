{{Form::open(array('url'=>'role','method'=>'post'))}}

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {{Form::label('name',__('Name'))}}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Role Name')))}}
            @error('name')
            <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            @if(!empty($permissions))
                <h6>{{__('Assign Permission to Roles')}} </h6>
                <table class="table table-striped mb-0" id="dataTable-1">
                    <thead>
                    <tr>
                        <th>{{__('Module')}} </th>
                        <th>{{__('Permissions')}} </th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $modules=['Role','User','Account','Contact','Lead','Opportunities','CommonCase','Meeting','Call','Task','Document','Campaign','Quote','SalesOrder','Invoice','Product','AccountType','AccountIndustry','LeadSource','OpportunitiesStage','CaseType','DocumentFolder','DocumentType','TargetList','CampaignType','ProductCategory','ProductBrand','ProductTax','ShippingProvider','TaskStage'];
                    @endphp
                    @foreach($modules as $module)
                        <tr>
                            <td>{{ ucfirst($module) }}</td>
                            <td>
                                <div class="row ">
                                    @if(in_array('Manage '.$module,(array) $permissions))
                                        @if($key = array_search('Manage '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,false, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Manage',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('Create '.$module,(array) $permissions))
                                        @if($key = array_search('Create '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,false, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Create',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('Edit '.$module,(array) $permissions))
                                        @if($key = array_search('Edit '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,false, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Edit',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('Delete '.$module,(array) $permissions))
                                        @if($key = array_search('Delete '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,false, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Delete',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('Show '.$module,(array) $permissions))
                                        @if($key = array_search('Show '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,false, ['class'=>'custom-control-input','id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Show',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <div class="col-md-12 text-right">
        {{Form::submit(__('Create'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
    </div>
</div>
{{Form::close()}}
