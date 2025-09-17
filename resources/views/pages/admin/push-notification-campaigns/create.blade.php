@extends('layouts.admin')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.timepicker.min.css') }}">
    <style>
        .error {
            color: red;
            margin-top: 5px;
        }
    </style>
@endsection
@section('breadcrumb')
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Push Notification Campaigns') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Push Notification Campaigns</li>
                </ol>
            </nav>
        </div>
    </div>
    <div id="error">
        @include('includes.message')
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.push.notification.campaigns.store') }}" id="schoolForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6 mg-b-20">
                                <h6>Name<span class="text-danger">*</span></h6>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <hr>
                        <h5 class="mb-3">Notification </h5>
                        <div class="row mb-3">
                            <div class="col-md-6 mg-b-20">
                                <h6>Title<span class="text-danger">*</span></h6>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="col-md-6 mg-b-20">
                                <h6>Image</h6>
                                <input type="file" class="form-control" id="media" name="media" autocomplete="off"
                                    onchange="loadFile(this)">
                            </div>
                            <div class="col-md-12 mg-b-20">
                                <h6>Body<span class="text-danger">*</span></h6>
                                <textarea name="body" id="" rows="5" class="form-control" required></textarea>
                            </div>
                        </div>
                        <hr>
                        <h5 class="mb-3">Audience </h5>
                        <div class="row mb-3">
                            <div class="col-md-6 mg-b-20">
                                <label>{{ __('School Name') }}</label>
                                <select name="school_id" id="school_id" class="form-control">
                                    <option value="">Select School</option>
                                    @if (count($schools) > 0)
                                        @foreach ($schools as $school)
                                            <option value="{{ $school->id }}">{{ $school->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Grade</h6>
                                <select name="grade" class="form-control" id="">
                                    <option value="" selected>Select Grade</option>
                                    @foreach ($countLists as $countList)
                                        <option value="{{ $countList }}">
                                            {{ $countList }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Name</h6>
                                <input class="form-control" placeholder="Enter Name" type="text" name="name" id="name">
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Mobile</h6>
                                <input class="form-control" placeholder="Enter Mobile" type="number" name="mobile_no" id="mobile_no" minlength="10" maxlength="10">
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>State</h6>
                                <select name="state" id="state" class="form-control" onchange="setCities(this);">
                                    <option value="">Select State</option>
                                    @foreach ($states as $key => $state)
                                        @php $state = (array)$state; @endphp
                                        @if (isset($state['state_name']))
                                            <option value="{{ $state['state_name'] }}">{{ $state['state_name'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>City</h6>
                                <select name="city" id="city" class="form-control">
                                    <option value="">Select City</option>
                                </select>
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Missions</h6>
                                <select name="missionType" class="form-control" id="">
                                    <option value="all">All Missions</option>
                                    <option value="approved">Approved Missions
                                    </option>
                                    <option value="requested">Requested Missions
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Missions Requested</h6>
                                <select name="mission_requested" class="form-control" id="">
                                    <option value="">Select Mission Requested</option>
                                    @foreach ($countLists as $countList)
                                        <option value="{{ $countList }}">
                                            {{ $countList }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Missions Approved</h6>
                                <select name="mission_approved" class="form-control" id="">
                                    <option value="">Select Mission Approved</option>
                                    @foreach ($countLists as $countList)
                                        <option value="{{ $countList }}">
                                            {{ $countList }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Earn Coins</h6>
                                <select name="earn_coins" class="form-control" id="">
                                    <option value="">Select Earn Coins</option>
                                    <option value="1">0-1000</option>
                                    <option value="2">1001-5000
                                    </option>
                                    <option value="3">more than 5000
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Register Date</h6>
                                <input class="form-control fc-datepicker" placeholder="DD/MM/YYYY" type="text" name="register_date" id="register_date">
                            </div>
                        </div>
                        <hr>
                        <h5 class="mb-3">Schedule </h5>
                        <div class="row mt-5 align-items-end justify-content-between">
                            <div class="col-lg-6">

                                <div class="form-group d-flex align-items-center gap-3 mb-4">
                                    <div class="radio-container">
                                        <input class="sendItLaterClass" type="radio" name="schedule_type" checked
                                            value="send_now">
                                        <span class="radiomark"></span>
                                    </div>
                                    <label class="mb-0">Send it now</label>
                                </div>
                                <div class="form-group d-flex align-items-center gap-3 mb-4">
                                    <div class="radio-container">
                                        <input class="sendItLaterClass" type="radio" name="schedule_type"
                                            value="send_later">
                                        <span class="radiomark"></span>
                                    </div>
                                    <label class="mb-0">Schedule it</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 sendLaterDateTimeDiv" style="display: none">
                            <div class="col-md-6 mg-b-20">
                                <h6>Scheduled Date<span class="text-danger">*</span></h6>
                                <input class="form-control fc-datepicker" placeholder="DD/MM/YYYY" type="text"
                                    name="scheduled_date" id="scheduled_date">
                            </div>

                            <div class="col-md-6 mg-b-20">
                                <h6>Scheduled Time<span class="text-danger">*</span></h6>
                                <input readonly class="form-control fc-timepicker" placeholder="MM:SS" type="text"
                                    name="scheduled_time" id="scheduled_time">
                            </div>


                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/js/parsley.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.timepicker.min.js') }}"></script>

    <script>
        $('.sendItLaterClass').click(function() {
            var value = $(this).val();
            $("#scheduled_date").attr('required', false);
            $("#scheduled_time").attr('required', false);
            $('.sendLaterDateTimeDiv').hide();
            if (value == 'send_later') {
                $("#scheduled_date").attr('required', true);
                $("#scheduled_time").attr('required', true);
                $('.sendLaterDateTimeDiv').show();
            }
        })


        $(document).ready(function() {
            $("#schoolForm").parsley();

            $('.fc-timepicker').timepicker({
                timeFormat: 'HH:mm',
                interval: 15,
                defaultTime: '00',
                startTime: '08:00',
                dynamic: true,
                dropdown: true,
                scrollbar: true,
                zindex: 9999999
            });
            $('.fc-datepicker').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'dd-mm-yy'
            });
        });

        function setCities(sel) {
            var url = '{{ route('admin.cities', ':state') }}';
            url = url.replace(':state', sel.value);

            const xhttp = new XMLHttpRequest();
            xhttp.open("GET", url, false);
            xhttp.send();
            const res = xhttp.responseText

            const cities = JSON.parse(res)
            var options = `<option selected value="">Select a city</option>`
            for (let i = 0; i < cities.length; i++) {
                const city = cities[i];
                options = options + `<option value="${city["city_name"]}">${city["city_name"]}</option>`
            }
            $('#city').html(options)
        }

        var loadFile = function(event) {
            var filePath = event.value;
            var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.jfif)$/i;
            if (!allowedExtensions.exec(filePath)) {
                alert('Please upload file having extensions .jpeg, .jpg .png only.');
                event.value = '';
            } else {
                if (event.files && event.files[0]) {
                    var reader = new FileReader();
                    reader.readAsDataURL(event.files[0]);
                }
            }
        };
    </script>
@endsection
