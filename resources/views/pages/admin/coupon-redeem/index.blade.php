@extends('layouts.admin')
@section('css')

    
    <style>

        .canvas-container {
            width: 80%;
            margin: 20px auto;
        }
    </style>
@endsection
@section('breadcrumb')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Coupon Redeems') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Coupon Redeems</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="col-md-12 mt-3">
                        <div class="row">
                            <div class="col-md-12">
                                <form method="get" action="{{ route('admin.coupon.redeems.list') }}">
                                    <div class=" d-flex row mb-3">
                                        <div class="col-md-3 mb-3">
                                            <input type="text" name="userName" class="form-control"
                                                @if ($request->userName) value="{{ $request->userName }}" @endif
                                                placeholder="Search With User Name">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <input type="text" name="mobileNumber" class="form-control"
                                                @if ($request->mobileNumber) value="{{ $request->mobileNumber }}" @endif
                                                placeholder="Search With Mobile Number">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <select name="coupon_id" class="form-control" id="">
                                                <option value="">Select Coupon</option>
                                                @foreach ($coupons as $coupon)
                                                    <option value="{{ $coupon->id }}"
                                                        @if ($coupon->id == $request->coupon_id) selected @endif>
                                                        {{ $coupon->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">

                                        <input type="text" class="form-control" name="school_name" id="school_name" placeholder="Search School Name" value="{{ request('school_name') }}">
                                        <div id="school-suggestions" class="position-absolute d-none" style="width: 100%; z-index: 1000;"></div>

                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <select name="grade" class="form-control" id="">
                                                <option value="" selected>Select Grade</option>
                                                @foreach ($countLists as $countList)
                                                    <option value="{{ $countList }}"
                                                        @if ($request->grade == $countList) selected @endif>
                                                        {{ $countList }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <select name="state" id="state" class="form-control">
                                                <option value="">Select State</option>
                                                @if (count($states) > 0)
                                                    @foreach ($states as $stateData)
                                                        <option value="{{ $stateData->state_name }}"
                                                            @if ($stateData->state_name == $request->state) selected @endif>
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
                                                            @if ($cityData->city_name == $request->city) selected @endif>
                                                            {{ $cityData->city_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-success">Search</button>
                                            <a href={{ route('admin.coupon.redeems.list') }}
                                                class="btn btn-warning">clear</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <a class="form-control btn btn-purple  ml-2"
                                href="{{ route('admin.coupon.redeems.graph') }}">View Graph</a>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="col-md-6">
                            <h4 class="card-title mg-b-0 mt-2">Coupon Redeems</h4>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-10">
                                </div>
                                <div class="col-md-2">
                                    <form method="get" action="{{ route('admin.coupon.redeems.list') }}">
                                        <input type="hidden" name="type" value="export">
                                        <button type="submit" data-toggle="tooltip" data-placement="top" title="export"
                                            class="btn btn-purple">Export</button>
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
                                    <th class="border-bottom-0">User Name</th>
                                    <th class="border-bottom-0">Mobile Number</th>
                                    <th class="border-bottom-0">School Name</th>
                                    <th class="border-bottom-0">State</th>
                                    <th class="border-bottom-0">City</th>
                                    <th class="border-bottom-0">Grade</th>
                                    <th class="border-bottom-0">Coupon Name</th>
                                    <th class="border-bottom-0">Coins Redeemed</th>
                                    <th class="border-bottom-0">Coins left</th>
                                    <th class="border-bottom-0">Date</th>
                                </tr>
                            </thead>
                            <?php $i = $couponRedeems->perPage() * ($couponRedeems->currentPage() - 1) + 1; ?>
                            <tbody>
                                @if (count($couponRedeems) > 0)
                                    @foreach ($couponRedeems as $couponRedeem)
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ $couponRedeem->user ? $couponRedeem->user->name : '' }}</td>
                                            <td>{{ $couponRedeem->user ? $couponRedeem->user->mobile_no : '' }}</td>
                                            <td>{{ $couponRedeem->user ? ($couponRedeem->user->school ? $couponRedeem->user->school->name : '') : '' }}
                                            </td>
                                            <td>{{ $couponRedeem->user ? $couponRedeem->user->state : '' }}</td>
                                            <td>{{ $couponRedeem->user ? $couponRedeem->user->city : '' }}</td>
                                            <td>{{ $couponRedeem->user ? $couponRedeem->user->grade : '' }}</td>
                                            <td>{{ $couponRedeem->coupon ? $couponRedeem->coupon->title : '' }}</td>
                                            <td>{{ $couponRedeem->coins }}</td>
                                            <td>{{ $couponRedeem->user ? $couponRedeem->user->earn_coins : '' }}
                                                @if ($couponRedeem->user)
                                                    <button class="btn btn-sm btn-warning"
                                                        style="width: 20px;height: 20px;padding: 0;"
                                                        onclick="editCoins({{ $couponRedeem->user->id }})"><i
                                                            class="fa fa-edit"></i></button>
                                                @endif
                                            </td>
                                            <td>{{ date('d-m-Y', strtotime($couponRedeem->created_at)) }}</td>
                                        </tr>
                                        @php $i++; @endphp
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="10">No Data Found </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        {{ $couponRedeems->links() }}
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
    <script>
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
