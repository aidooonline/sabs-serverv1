@extends('layouts.admin')
@section('page-title')
    {{__('Lead')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Transactions')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Transactions')}}</li>
@endsection
 
@section('filter')
@endsection

@section('content')
    <div class="card">
        <!-- Table -->
       
        <script>
   function exportTasks(_this) {
      let _url = $(_this).data('href');
      window.location.href = _url;
   }
</script>
        <div class="table-responsive">
             <span data-href="{{route('export.transactions')}}" id="export" class="btn btn-light" onclick="exportTasks(event.target);">Export</span>
            <table class="align-items-left dataTable table-sm table-striped table-hover table-light">
                <thead>
                <tr >
                    <th scope="col" class="sort" data-sort="name">{{__('Tr ID')}}</th>
                    <th scope="col" style="padding-left:15px !important;" class="sort" data-sort="created_at">{{__('Datetime')}}</th>
                    <th scope="col" class="sort" style="width:40px !important;" data-sort="lead_temperature">{{__('Acc No')}}</th>
                    <th scope="col" class="sort" data-sort="acctype">{{__('Acc Type')}}</th>
                    <th scope="col" class="sort" data-sort="transactionname">{{__('Tr Name')}}</th>
                    <th scope="col" class="sort" data-sort="amount">{{__('Amount')}}</th>
                    <th scope="col" class="sort" data-sort="accname">{{__('Acc Name')}}</th> 
                    <th scope="col" class="sort" data-sort="phone">{{__('Phone')}}</th>
                    <th scope="col" class="sort" data-sort="agentname">{{__('Agent Name')}}</th>
                    
                    <th scope="col" class="sort" data-sort="userid">{{__('User ID')}}</th>
                </tr>
                </thead>
                <tbody class="list">
                @foreach($leads as $lead)
                
                
                
                        <tr>
                          <td title="Transaction ID" style="padding-left:15px !important;"> 
                           {{$lead->transaction_id}} 
                        </td>
                        <td title="{{$lead->created_at->diffForHumans()}}" style="padding-left:15px !important;"> 
                          {{\Auth::user()->dateFormat($lead->created_at)}}   
                        </td>
                          <td title="Account Number" style="padding-left:15px !important;"> 
                           {{$lead->account_number}}  
                           
                           
                        </td>
                        
                        
                           
                        
                        <td title="Account Type" style="padding-left:15px !important;"> 
                           {{$lead->account_type}}  
                        </td>
                        
                         <td>
                            <span class=@if($lead->name_of_transaction == 'Deposit'){{"coldlead"}}@elseif($lead->name_of_transaction == 'Withdraw'){{"hotlead"}}@else{{"warmlead"}} @endif>
                                {{$lead->name_of_transaction}} 
                            </span>
                        </td>
                       
                        
                        <td title="Amount" style="padding-left:15px !important;"> 
                           GHS {{$lead->amount}}  
                        </td>
                        
                        <td title="Account Name" style="padding-left:15px !important;"> 
                           {{$lead->det_rep_name_of_transaction}}  
                        </td>
                        
                        <td title="Phone" style="padding-left:15px !important;"> 
                           {{$lead->phone_number}}  
                        </td>
                        
                        <td title="Agent Name" style="padding-left:15px !important;"> 
                           {{$lead->agentname}}  
                        </td>
                        <td title="Agent ID" style="padding-left:15px !important;"> 
                           {{$lead->users}}  
                        </td>
                         
                         
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
