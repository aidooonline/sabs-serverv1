<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $lead-> name }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Account name')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($lead->accounts)?$lead->accounts->name:'-'}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Email')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $lead-> email }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Phone')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $lead-> phone }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Title')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $lead-> title }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Website')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $lead-> website }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('lead Address')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $lead-> lead_address }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">City</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $lead-> lead_city }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">State</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $lead-> lead_state }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">Country</span></dt>

                    @php
                    $leadcountry =\App\Country::getcountry($lead->lead_country);
                    $leadiso =\App\Country::getcountryiso($lead->lead_country);
                    @endphp
                   
                    <dd class="col-sm-8">
                        <span class="flag-icon flag-icon-{{strtolower($leadiso[0])}}"></span>
                        <span class="text-sm">{{ $leadcountry[0] }}</span>
                    </dd>

                  
                    <div class="col-12">
                        <hr class="mt-2 mb-2">
                        <h5>{{__('Details')}}</h5>
                    </div>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Status')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">
                            @if($lead  ->status == 0)
                                <span class="badge badge-success">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                            @elseif($lead->status == 1)
                                <span class="badge badge-info">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                            @elseif($lead->status == 2)
                                <span class="badge badge-warning">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                            @elseif($lead->status == 3)
                                <span class="badge badge-danger">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                            @elseif($lead->status == 4)
                                <span class="badge badge-danger">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                            @elseif($lead->status == 5)
                                <span class="badge badge-warning">{{ __(\App\Lead::$status[$lead->status]) }}</span>
                            @endif
                        </span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Source')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($lead->LeadSource)?$lead->LeadSource->name:''}}</span></dd>

                    <dd class="col-sm-8"><span class="text-sm">{{\Auth::user()->priceFormat($lead->opportunity_amount)}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Campaign')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($lead->campaigns)?$lead->campaigns->name:'-'}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Industry')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($lead->accountIndustry)?$lead->accountIndustry->name:''}}</span></dd>


                </dl>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card">
            <div class="card-footer py-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <div class="row align-items-center">
                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Assigned User')}}</span></dt>
                            <dd class="col-sm-12"><span class="text-sm">{{ !empty($lead->assign_user)?$lead->assign_user->name:''}}</span></dd>

                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                            <dd class="col-sm-12"><span class="text-sm">{{\Auth::user()->dateFormat($lead->created_at)}}</span></dd>
                        </div>
                    </li>
                </ul>

                <div >

                    @if($lead->call_made)
                    <div class="form-group"> 
                        <div class="form-check">
                            <input    type="checkbox" value="{{$lead->call_made === 1 ? true :false}}"  {{$lead->call_made === 1 ? 'checked' :''}} >
                            <label class="form-check-label" for="flexCheckDefault">
                              Call Made
                            </label>
                          </div>
                       
                    </div>
                    @endif
                    
                    @if($lead->mail_sent)
                    <div class="form-group"> 
                        <div class="form-check">
                            <input  type="checkbox" value="{{$lead->mail_sent === 1 ? true :false}}"  {{$lead->mail_sent === 1 ? 'checked' :''}} >
                            <label class="form-check-label">
                             Mail Sent
                            </label>
                          </div>
                    </div>
                    @endif


                    @if($lead->visited_site)
                    <div class="form-group"> 
                        <div class="form-check">
                            <input     type="checkbox" value="{{$lead->visited_site === 1 ? true :false}}"  {{$lead->visited_site === 1 ? 'checked' :''}} >
                            <label class="form-check-label" >
                            Visited Site
                            </label>
                          </div>
                    </div>
                    @endif

                    @if($lead->offer_letter)
                    <div class="form-group"> 
                        <div class="form-check">
                            <input      type="checkbox" value="{{$lead->offer_letter === 1 ? true :false}}"  {{$lead->offer_letter === 1 ? 'checked' :''}} >
                            <label class="form-check-label">
                            Offer Letter Sent
                            </label>
                          </div>
                    </div>
                    @endif

                    @if($lead->contract)
                    <div class="form-group"> 
                        <div class="form-check">
                            <input    type="checkbox" value="{{$lead->contract === 1 ? true :false}}"  {{$lead->contract === 1 ? 'checked' :''}} >
                            <label class="form-check-label">
                           Contract Sent
                            </label>
                          </div>
                    </div>
                    @endif

                    @if($lead->payment)
                    <div class="form-group"> 
                        <div class="form-check">
                            <input     type="checkbox" value="{{$lead->payment === 1 ? true :false}}"  {{$lead->payment === 1 ? 'checked' :''}} >
                            <label class="form-check-label">
                            Payment Made
                            </label>
                          </div>
                    </div>
                    @endif

                    @if($lead->receipt)
                    <div class="form-group"> 
                        <div class="form-check">
                            <input    type="checkbox" value="{{$lead->receipt === 1 ? true :false}}"  {{$lead->receipt === 1 ? 'checked' :''}} >
                            <label class="form-check-label" >
                            Receipt Sent
                            </label>
                          </div>
                    </div>
                    @endif 

                    
                </div>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        @can('Edit Lead')
            <a href="{{ route('lead.edit',$lead->id) }}" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="{{__('Lead Edit')}}"><i class="far fa-edit"></i>
            </a>
        @endcan
    </div>
</div>

<style>
    input[type=checkbox][disabled]{
color:darkolivegreen !important; // or whatever
}

label{
    font-size:13px !important;
}
    </style>