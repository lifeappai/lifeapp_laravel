@extends('layouts.admin')
@section('breadcrumb')
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Users') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
        </div>
    </div>
    <div id="error">
        @include('includes.message')
    </div>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    
    <style>


        .ui-datepicker .ui-datepicker-prev::before,
        .ui-datepicker .ui-datepicker-next::before {
            content: ''; 
            display: none; 
        }


        #loader_new {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.75) url({{ asset('assets/img/loaders/spinner.gif') }}) no-repeat center center;
            z-index: 10000;
        }

        .custom-thumbnail {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }
    </style>
@endsection
@section('content')
    <div id="loader_new" style="display: none">
    </div>

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="card-title mg-b-0 mt-2">User List ({{ $users->total() }})</h4>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <form method="get" action="{{ route('admin.users.list') }}">
                                        <div class=" d-flex row mb-3">
                                            <div class="col-md-3 mb-3">
                                                <select name="missionType" class="form-control" id="">
                                                    <option value="all">All Missions</option>
                                                    <option value="approved"
                                                        @if ($missionType == 'approved') selected @endif>Approved Missions
                                                    </option>
                                                    <option value="requested"
                                                        @if ($missionType == 'requested') selected @endif>Requested Missions
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">

                                                <input type="text" class="form-control" name="school_name" id="school_name" placeholder="Search School Name" value="{{ $schoolName ?? '' }}">
                                                <div id="school-suggestions" class="position-absolute d-none" style="width: 100%; z-index: 1000;"></div>

                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <select name="grade" class="form-control" id="">
                                                    <option value="" selected>Select Grade</option>
                                                    @for ($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}" 
                                                            @if (isset($grade) && $grade !== '' && (int)$grade === $i) selected @endif>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <select name="state" id="state" class="form-control">
                                                    <option value="">Select State</option>
                                                    @if (count($states) > 0)
                                                        @foreach ($states as $stateData)
                                                            <option value="{{ $stateData->state_name }}"
                                                                @if ($stateData->state_name == $state) selected @endif>
                                                                {{ $stateData->state_name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <select name="city" id="city" class="form-control">
                                                    <option value="">Select City</option>
                                                    @if (count($cities) > 0)
                                                        @foreach ($cities as $cityData)
                                                            <option value="{{ $cityData->city_name }}"
                                                                @if ($cityData->city_name == $city) selected @endif>
                                                                {{ $cityData->city_name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <!-- Mission Requested Dropdown -->
                                            <div class="col-md-3 mb-3">
                                                <select name="mission_requested" class="form-control" id="missionRequested">
                                                    <option value="">Select Mission Requested</option>
                                                    @for ($i = 0; $i <= 50; $i++)
                                                        <option value="{{ $i }}" 
                                                            @if (isset($missionRequested) && $missionRequested !== '' && (int)$missionRequested === $i) selected @endif>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <!-- Mission Approved Dropdown -->
                                            <div class="col-md-3 mb-3">
                                                <select name="mission_approved" class="form-control" id="missionApproved">
                                                    <option value="">Select Mission Approved</option>
                                                    @for ($i = 0; $i <= 50; $i++)
                                                        <option value="{{ $i }}" 
                                                            @if (isset($missionApproved) && $missionApproved !== '' && (int)$missionApproved === $i) selected @endif>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <select name="earn_coins" class="form-control" id="">
                                                    <option value="">Select Earn Coins</option>
                                                    <option value="1"
                                                        @if ($earnCoins == 1) selected @endif>0-1000</option>
                                                    <option value="2"
                                                        @if ($earnCoins == 2) selected @endif>1001-5000</option>
                                                    <option value="3" 
                                                        @if ($earnCoins == 3) selected @endif>5001-10000</option>    
                                                    <option value="4" 
                                                        @if ($earnCoins == 4) selected @endif>more than 10001</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <select name="type" class="form-control" id="">
                                                    <option value="">Select User Type</option>
                                                    <option value="{{ App\Enums\UserType::Student }}"
                                                        @if ($userType == App\Enums\UserType::Student) selected @endif>Student</option>
                                                    <option value="{{ App\Enums\UserType::Teacher }}"
                                                        @if ($userType == App\Enums\UserType::Teacher) selected @endif>Teacher
                                                    </option>
                                                    <option value="{{ App\Enums\UserType::Mentor }}"
                                                        @if ($userType == App\Enums\UserType::Mentor) selected @endif>Mentor
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <input class="form-control fc-datepicker" placeholder="DD/MM/YYYY"
                                                    type="text"
                                                    @if ($registerDate) value="{{ $registerDate }}" @endif
                                                    name="register_date" id="register_date">
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <input class="form-control fc-datepicker" placeholder="DD/MM/YYYY"
                                                    type="text"
                                                    @if ($registerEndDate) value="{{ $registerEndDate }}" @endif
                                                    name="register_end_date" id="register_end_date">
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <input type="text" name="mobileNumber" class="form-control"
                                                    @if ($mobileNumber) value="{{ $mobileNumber }}" @endif
                                                    placeholder="Search With Mobile Number">
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <input type="text" name="district_name" class="form-control"
                                                    @if ($districtName) value="{{ $districtName }}" @endif
                                                    placeholder="Search With District Name">
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <input type="text" name="block_name" class="form-control"
                                                    @if ($blockName) value="{{ $blockName }}" @endif
                                                    placeholder="Search With Block Name">
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <input type="text" name="cluster_name" class="form-control"
                                                    @if ($clusterName) value="{{ $clusterName }}" @endif
                                                    placeholder="Search With Cluster Name">
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <input type="text" name="school_code" class="form-control"
                                                    @if ($schoolCode) value="{{ $schoolCode }}" @endif
                                                    placeholder="Search With School code">
                                            </div>

                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-success">Search</button>
                                                <a href={{ route('admin.users.list') }} class="btn btn-warning">clear</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-1">
                                    @php
                                        $currentDate = date('Y-m-d');
                                    @endphp
                                    <a href="{{ url('/exports/users-' . $currentDate . '.csv') }}"
                                        class="btn btn-purple ml-2">Export</a>
                                </div>
                                
                                <div class="col-md-2">
                                    <a class="form-control btn btn-success  ml-2"
                                        href="{{ route('admin.users.create') }}">Add
                                        Student</a>
                                </div>
                                <div class="col-md-2">
                                    <a class="form-control btn btn-purple  ml-2"
                                        href="{{ route('admin.users.graph') }}">View Graph</a>
                                </div>
                                <!-- School Code Export Form -->
                                <div class="col-md-3">
                                    <form method="get" action="{{ route('admin.users.exportBySchoolCode') }}" class="d-flex flex-column align-items-start">
                                        <div class="d-flex w-100">
                                            <input 
                                                type="text" 
                                                name="school_code" 
                                                value="{{ request('school_code') }}" 
                                                class="form-control me-2" 
                                                placeholder="Enter School Code" 
                                                required
                                            >
                                            <button type="submit" class="btn btn-primary">
                                                Export
                                            </button>
                                        </div>
                                        <small class="text-muted mt-2">
                                            Export filtered data based on school code.
                                        </small>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">Sr No.</th>
                                    <th class="border-bottom-0">Name</th>
                                    <th class="border-bottom-0">Image</th>
                                    <th class="border-bottom-0">Mobile Number</th>
                                    <th class="border-bottom-0">School</th>
                                    <th class="border-bottom-0">School Code</th>
                                    <th class="border-bottom-0">District Name</th>
                                    <th class="border-bottom-0">Block Name</th>
                                    <th class="border-bottom-0">Cluster Name</th>
                                    <th class="border-bottom-0">Grade</th>
                                    <th class="border-bottom-0">Section</th>
                                    <th class="border-bottom-0">Type</th>
                                    <th class="border-bottom-0">Address</th>
                                    <th class="border-bottom-0">State</th>
                                    <th class="border-bottom-0">City</th>
                                    <th class="border-bottom-0">DOB</th>
                                    <th class="border-bottom-0">Mission Approved</th>
                                    <th class="border-bottom-0">Mission Requested</th>
                                    <th class="border-bottom-0">Quiz</th>
                                    <th class="border-bottom-0">Earn Coins</th>
                                    <th class="border-bottom-0">Quiz Coins</th>
                                    <th class="border-bottom-0">Mission Coins</th>
                                    <th class="border-bottom-0">Coins Redeemed</th>
                                    <th class="border-bottom-0">Product Redeemed</th>
                                    <th class="border-bottom-0">Rating</th>
                                    <th class="border-bottom-0">Register Date</th>
                                    <th class="border-bottom-0">Edit coins</th>
                                    <th class="border-bottom-0">Action</th>
                                </tr>
                            </thead>
                            <?php $i = $users->perPage() * ($users->currentPage() - 1) + 1; ?>
                            <tbody>
                                @if (count($users) > 0)
                                    @foreach ($users as $user)
                                        <tr class="user_list">
                                            <td>{{ $i }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>
                                                @php
                                                    $userImage = $user->image_path ? $imageBaseUrl . $user->image_path : '';
                                                @endphp
                                                @if ($userImage)
                                                    <a class="image-popup-no-margins" href="{{ $userImage }}">
                                                        <img alt="" src="{{ $userImage }}"
                                                            class="custom-thumbnail">
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $user->mobile_no }}</td>
                                            <td>{{ $user->school ? $user->school->name : '' }}</td>
                                            <td>{{ $user->school ? $user->school->code : '' }}</td>
                                            <td>{{ $user->school->district ?? '-' }}</td>
                                            <td>{{ $user->school->block ?? '-' }}</td>
                                            <td>{{ $user->school->cluster ?? '-' }}</td>
                                            <td>{{ $user->laGrade->name ?? '' }}</td>
                                            <td>{{ $user->laSection->name ?? '' }}</td>
                                            <td>
                                                @if ($user->type == App\Enums\UserType::Student)
                                                    Student
                                                @elseif ($user->type == App\Enums\UserType::Teacher)
                                                    Teacher
                                                @elseif ($user->type == App\Enums\UserType::Mentor)
                                                    Mentor
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <!-- <td>{{ $user->grade }}</td> -->
                                            <td>{{ $user->address }}</td>
                                            <td>{{ $user->state }}</td>
                                            <td>{{ $user->city }}</td>
                                            <td>{{ $user->dob ? date('d-m-Y', strtotime($user->dob)) : '-' }}</td>
                                            <td>{{ $user->laMissionApproved()->count() }}</td>
                                            <td>{{ $user->laMissionRequests()->count() }}</td>
                                            <td>{{ $user->laQuizGameResults->count() }}
                                            <td>{{ $user->earn_coins }}</td>
                                            <td>{{ $user->earnCoinsByType('quiz') }}</td>
                                            <td>{{ $user->earnCoinsByType('mission') }}</td>
                                            <td>{{ $user->couponRedeems->sum('coins') }}</td>
                                            <td>{{ $user->laSubjectCouponCodes->count() }}</td>
                                            <td>{{ $user->user_rank }}</td>
                                            <td>{{ $user->created_at ? date('d-m-Y', strtotime($user->created_at)) : '-' }}
                                            </td>
                                            <td><button class="btn btn-warning"
                                                    onclick="editCoins({{ $user->id }})"><i
                                                        class="fa fa-edit"></i></button></td>
                                            <td><a href="{{ route('admin.users.edit', $user->id) }}"><button
                                                        class="btn btn-warning">Edit</button></a></td>
                                            <td><a href="{{ route('admin.users.la.missions', $user->id) }}"><button
                                                        class="btn btn-success">View</button></a></td>
                                            <td><a href="{{ route('admin.users.earned.coins', $user->id) }}"><button
                                                        class="btn btn-success">Coins</button></a></td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                @else
                                    <td class="text-center" colspan="7">No Users Found </td>
                                @endif
                            </tbody>
                        </table>
                        @if (count($users) > 0)
                            {{ $users->appends(Request::all())->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <div class="modal fade" id="editCoinsModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Coins</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" id="editCoinsForm">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <input type="text" name="amount" id="amount" class="form-control" required
                                    onkeypress='return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 45'>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script>
        var spinner_new = $('#loader_new');
        $('.fc-datepicker').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'dd-mm-yy'
        });

        $("#state").change(function() {
            var state = $(this).val();
            $("#city").html('<option value="">Select City</option>');
            var url = "{{ route('admin.cities', ':state') }}";
            url = url.replace(':state', state);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: url,
                type: "get",
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $("#city").html(data);
                }
            });
        });

        function editCoins(userId) {
            var url = "{{ route('admin.users.coins', ':userId') }}";
            url = url.replace(':userId', userId);
            $("#editCoinsForm").attr("action", url);
            $("#editCoinsModal").modal('show');
        }
    </script>

    
        //schoolfilter
    <script>
        
        $(document).ready(function() {
            $('#school_name').on('keyup', function() {
                var query = $(this).val();
                
                if(query.length >= 2) { // Only search if 2 or more characters
                    $.ajax({
                        url: "{{ route('admin.search.schools') }}",
                        method: 'GET',
                        data: {query: query},
                        success: function(data) {
                            var suggestions = '';
                            data.forEach(function(school) {
                                suggestions += `<div class="suggestion p-2 bg-white border cursor-pointer hover:bg-gray-100">${school.name}</div>`;
                            });
                            
                            if(suggestions) {
                                $('#school-suggestions')
                                    .html(suggestions)
                                    .removeClass('d-none');
                            } else {
                                $('#school-suggestions').addClass('d-none');
                            }
                        }
                    });
                } else {
                    $('#school-suggestions').addClass('d-none');
                }
            });

            // Handle clicking on a suggestion
            $(document).on('click', '.suggestion', function() {
                $('#school_name').val($(this).text());
                $('#school-suggestions').addClass('d-none');
            });

            // Hide suggestions when clicking outside
            $(document).on('click', function(e) {
                if(!$(e.target).closest('#school_name, #school-suggestions').length) {
                    $('#school-suggestions').addClass('d-none');
                }
            });
        });

    </script>
@endsection
