@extends('layouts.admin')
@section('page-title')
    {{__('Calender')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Calender')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Calender')}}</li>
@endsection
@section('action-btn')
    @can('Create Call')
        <a href="#" data-size="lg" data-url="{{ route('call.create') }}" data-ajax-popup="true" data-title="{{__('Create New Call')}}" class="btn btn-sm btn-primary bor-radius">
            {{__('Add Call')}}
        </a>
    @endcan
    @can('Create Meeting')
        <a href="#" data-size="lg" data-url="{{ route('meeting.create') }}" data-ajax-popup="true" data-title="{{__('Create New Meeting')}}" class="btn btn-sm btn-info bor-radius">
            {{__('Add Meeting')}}
        </a>
    @endcan
    @can('Create Task')
        <a href="#" data-size="lg" data-url="{{ route('task.create') }}" data-ajax-popup="true" data-title="{{__('Create New Task')}}" class="btn btn-sm btn-success bor-radius">
            {{__('Add Task')}}
        </a>
    @endcan
    <div class="float-right">
            <select name="calenderdata" data-toggle='select' class="form-control py-0 px-4" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                <option value="{{ route('calendar.index','all') }}" {{ ((Request::segment(2) == 'all' || empty(Request::segment(2))) ? 'selected' : '') }}>{{ __('Show All') }}</option>
                <option value="{{ route('calendar.index','meeting') }}" {{ ((Request::segment(2) == 'meeting') ? 'selected' : '') }}>{{ __('Show Meeting') }}</option>
                <option value="{{ route('calendar.index','call') }}" {{ ((Request::segment(2) == 'call') ? 'selected' : '') }}>{{ __('Show Call') }}</option>
                <option value="{{ route('calendar.index','task') }}" {{ ((Request::segment(2) == 'task') ? 'selected' : '') }}>{{ __('Show Task') }}</option>
            </select>
    </div>

@endsection
@section('filter')

@endsection
@push('script-page')

    <script>
        Fullcalendar = function () {
            var e, t, a = $('[data-toggle="calendar"]');
            a.length && (t = {
                header: {right: "", center: "", left: ""},
                buttonIcons: {prev: "calendar--prev", next: "calendar--next"},
                theme: !1,
                selectable: !0,
                selectHelper: !0,
                editable: !1,
                events: {!! $calandar !!},
                dayClick: function (e) {
                    var t = moment(e).toISOString();
                    $("#new-event").modal("show"), $(".new-event--title").val(""), $(".new-event--start").val(t), $(".new-event--end").val(t)
                },
                viewRender: function (t) {
                    e.fullCalendar("getDate").month(), $(".fullcalendar-title").html(t.title)
                },
                eventClick: function (e, t) {
                    $("#edit-event input[value=" + e.className + "]").prop("checked", !0), $("#edit-event").modal("show"), $(".edit-event--id").val(e.id), $(".edit-event--title").val(e.title), $(".edit-event--description").val(e.description)
                }
            }, (e = a).fullCalendar(t), $("body").on("click", ".new-event--add", function () {
                var t = $(".new-event--title").val(), a = {
                    Stored: [], Job: function () {
                        var e = Date.now().toString().substr(6);
                        return this.Check(e) ? this.Job() : (this.Stored.push(e), e)
                    }, Check: function (e) {
                        for (var t = 0; t < this.Stored.length; t++) if (this.Stored[t] == e) return !0;
                        return !1
                    }
                };
                "" != t ? (e.fullCalendar("renderEvent", {id: a.Job(), title: t, start: $(".new-event--start").val(), end: $(".new-event--end").val(), allDay: !0, className: $(".event-tag input:checked").val()}, !0), $(".new-event--form")[0].reset(), $(".new-event--title").closest(".form-group").removeClass("has-danger"), $("#new-event").modal("hide")) : ($(".new-event--title").closest(".form-group").addClass("has-danger"), $(".new-event--title").focus())
            }), $("body").on("click", "[data-calendar]", function () {
                var t = $(this).data("calendar"), a = $(".edit-event--id").val(), n = $(".edit-event--title").val(), o = $(".edit-event--description").val(), i = $("#edit-event .event-tag input:checked").val(), s = e.fullCalendar("clientEvents", a);
                "update" === t && ("" != n ? (s[0].title = n, s[0].description = o, s[0].className = [i], console.log(i), e.fullCalendar("updateEvent", s[0]), $("#edit-event").modal("hide")) : ($(".edit-event--title").closest(".form-group").addClass("has-error"), $(".edit-event--title").focus())), "delete" === t && ($("#edit-event").modal("hide"), setTimeout(function () {
                    swal({title: "Are you sure?", text: "You won't be able to revert this!", type: "warning", showCancelButton: !0, buttonsStyling: !1, confirmButtonClass: "btn btn-danger", confirmButtonText: "Yes, delete it!", cancelButtonClass: "btn btn-secondary"}).then(function (t) {
                        t.value && (e.fullCalendar("removeEvents", a), swal({title: "Deleted!", text: "The event has been deleted.", type: "success", buttonsStyling: !1, confirmButtonClass: "btn btn-primary"}))
                    })
                }, 200))
            }), $("body").on("click", "[data-calendar-view]", function (t) {
                t.preventDefault(), $("[data-calendar-view]").removeClass("active"), $(this).addClass("active");
                var a = $(this).attr("data-calendar-view");
                e.fullCalendar("changeView", a)
            }), $("body").on("click", ".fullcalendar-btn-next", function (t) {
                t.preventDefault(), e.fullCalendar("next")
            }), $("body").on("click", ".fullcalendar-btn-prev", function (t) {
                t.preventDefault(), e.fullCalendar("prev")
            }))
        }()
    </script>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="row justify-content-between align-items-center">
                <div class="col d-flex align-items-center">
                    <h5 class="fullcalendar-title h4 d-inline-block font-weight-400 mb-0">{{__('Calendar')}}</h5>
                </div>
                <div class="col-lg-6 mt-3 mt-lg-0 text-lg-right">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <a href="#" class="fullcalendar-btn-prev btn btn-sm btn-neutral">
                            <i class="fas fa-angle-left"></i>
                        </a>
                        <a href="#" class="fullcalendar-btn-next btn btn-sm btn-neutral">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    </div>
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <a href="#" class="btn btn-sm btn-neutral" data-calendar-view="month">{{__('Month')}}</a>
                        <a href="#" class="btn btn-sm btn-neutral" data-calendar-view="basicWeek">{{__('Week')}}</a>
                        <a href="#" class="btn btn-sm btn-neutral" data-calendar-view="basicDay">{{__('Day')}}</a>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <div class="card overflow-hidden">
                        <div class="calendar" data-toggle="calendar" id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
