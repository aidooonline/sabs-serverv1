<style>
    .bgpurple {
        color: #807f81;
        background-color: #dbd8dd57 !important;
        font-weight: :normal !important;

    }

    .textcolor1 {
        color: #29B6F6;
        font-weight: :normal !importan;
    }
</style>

<h5 style="width:100%;position:relative;padding-left:20px;" class="text-warning">({{$loanrequestcounts}}) Requested
    Loans <br />({{$loanrequestsum}})
</h5>

<a style="position:absolute;right:20px;top:40px;" href="{{route('loanrequests.create')}}" href="#"
    class="btn btn-purple  mr-1 btn-fab btn-sm">
    <i class="fa fa-plus"></i>
</a>


@foreach($loanrequests as $loanrequest)
<!-- TODAY -->

<div class="displaydivs" id="todaydiv">

    @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
    <div id="accountbtnpanel_{{$loanrequest->id}}" class="col-xl-4 col-lg-6 col-md-6 col-12">

        <div style="padding-top:0 !important;" class="card bg-white pt-2 pl-2">
            <div class="card-body">
                <div class="card-block pt-1 pb-0 pl-0">

                    <ul style="border:0 !important;padding-left:15px;" class="list-group">

                        @if($loanrequest->customer_picture)
                        <img src="" profilevalue="{{$loanrequest->customer_picture}}" class="profilepic"
                            style="width:70px;height:auto;position:absolute;top:10px;right:5px;z-index:2;border-radius:20px;" />

                        @else
                        <img src="{{env('NOBS_IMAGES')}}/useraccounts/profileimage.png"
                            style="width:70px;height:auto;position:absolute;top:10px;right:5px;z-index:2;border-radius:20px;" />

                        @endif

                        <li style="padding-top:0 !important;padding-bottom:0 !important;padding-left:10px;">
                            <span class="row  mt-1"><strong class="font-medium-2 mb-0 pl-0 ml-0" style="color:#1a84b6">
                                    {{number_format($loanrequest->amount, 2, '.', ',')}}</strong></span>
                        </li>
                        <li style="padding-top:0 !important;padding-bottom:0 !important" class="list-group-item">
                            <span class="row mt-1"><span style="padding-left:10px;" class="textcolor1">
                                    {{$loanrequest->first_name}} {{$loanrequest->last_name}}
                                </span>
                        </li>
                        <li style="padding-top:0 !important;padding-bottom:0 !important" class="list-group-item">
                            <span class="row mt-1"><span style="padding-left:10px;font-weight:bold;color:#0e72a0"
                                    class="textcolor1">
                                    {{$loanrequest->name}},<span class="textcolor1"
                                        style="font-weight:normal !important;padding-left:5px;">
                                        {{$loanrequest->purposename}}
                                    </span></span></span>
                        </li>
                        <li style="padding-top:0 !important;padding-bottom:0 !important" class="list-group-item">
                            <span class="row mt-1"><span style="padding-left:10px;">{{$loanrequest->occupation}},<span
                                        style="font-weight:normal !important;padding-left:5px;">
                                        {{$loanrequest->residential_address}}
                                    </span></span></span>
                        </li>
                    </ul>

                    <div class="media-right text-left mr-2">
                        <a href="{{ route('loanrequestdetail.edit',$loanrequest->id) }}"
                            class="btn btn-light mr-1 btn-fab btn-sm">
                            <i class="fa fa-pencil"></i>
                        </a>

                        <a href="{{env('BASE_URL')}}loanrequestdetail/detail/{{$loanrequest->id}}"
                            class="btn btn-light mr-1 btn-fab btn-sm">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="#" class="btn btn-light mr-1 btn-fab btn-sm">
                            <?php $loanrating = $loanrequest->loan_request_rating; 
                                      for ($x = 0; $x <= $loanrating; $x++) { 
                                          echo '<span style="color:rgb(202, 133, 41) !important;" class="fa fa-star checked"></span>';
                                      }?>
                        </a>

                        <a style="position:absolute;right:2px !important;bottom:5px;"
                            href="{{route('loanmigrations.migrate')}}/{{$loanrequest->id}}/{{$loanrequest->customer_account_id}}"
                            class="btn btn-purple mr-1 btn-fab btn-sm">
                            Migrate <i class="fa fa-angle-double-right"></i>
                        </a>

                        <input type="hidden" class='loan_request_rating'
                            value="{{$loanrequest->loan_request_rating}}" />
                    </div>
                </div>

            </div>
        </div>
    </div>


    @endif

</div>


<!-- END TODAY -->
@endforeach

<nav style="margin-left:13px;margin-right:13px;padding-bottom:10px;padding-left:5px;padding-right:5px;padding-top:0 !important;"
    aria-label="Page navigation" class="card">
    {{$loanrequests->links()}}
</nav>

<style>
    .pagination .page-item:active a {
        background-color: purple;
    }

    body>div.container-fluid.container-application>div.main-content.position-relative>div.page-content>div.row.dashboardtext>div.col-xl-3.col-md-6 {
        z-index: 3 !important;
    }

    a.fa-star {
        width: 100%;
        display: inline-block;
        color: #F62;
    }

    input.star {
        display: none;
    }

    label.star {
        float: right;
        padding: 2px;
        font-size: 15px;
        color: #444;
        transition: all .2s;
    }

    a.fa-star:checked~label.star:before {
        content: '\f005';
        transition: all .25s;
    }



    label.star:hover {
        transform: rotate(-15deg) scale(1.3);
    }

    label.star:before {
        content: '\f006';
        font-family: FontAwesome;
    }


    table td[class='mintd'] {
        padding: 5px 25px !important;
    }


    ul.list-group {
        padding: 5px 5px;
    }

    .list-group .list-group-item span {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }


    .list-group .list-group-item {
        padding: 0 2px !important;
        border: solid 0 !important;
        padding-top: 0;
        padding-bottom: 0;
        margin-bottom: 0 !important;
        margin-bottom: 0 !important;
        height: 20p
    }

    .ghs {
        font-weight: normal !important;
        font-size: 13px;
    }

    .displaydivs {}

    .tabpanel,
    .tabpanel p {
        background-color: #ffffff !important;
        padding: 10px 10px;
    }


    #thisweekdiv,
    #thismonthdiv,
    #thisyeardiv,
    #alltimediv {
        display: none;
    }
</style>

<script type="text/javascript">


    function getfilter(that) {
        switch (that) {
            case 'Today':

                $('.displaydivs').hide();
                $('#todaydiv').show(300);
                $('#filtertext').html(that);
                break;

            case 'This Week':
                $('.displaydivs').hide();
                $('#thisweekdiv').show(300);
                $('#filtertext').html(that);

                break;

            case 'This Month':
                $('.displaydivs').hide();
                $('#thismonthdiv').show(300);
                $('#filtertext').html(that);

                break;

            case 'This Year':
                $('.displaydivs').hide();
                $('#thisyeardiv').show(300);
                $('#filtertext').html(that);

                break;

            case 'All Time':
                $('.displaydivs').hide();
                $('#alltimediv').show(300);
                $('#filtertext').html(that);

                break;


        }
    }

    function getfilterbyagent(that) {

        if (that == 'agentid_agentallid12345') {
            showhidediv('loadingdiv');
            location.href = "{{route('dashboard.index')}}";

        } else {
            showhidediv('loadingdiv');
            location.href = "{{route('agentquerydashboard.index')}}/" + that;

        }
    }

</script>