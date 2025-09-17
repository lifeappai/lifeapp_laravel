@extends('layouts.admin')
@section('css')
    <style>
    </style>
@endsection
@section('breadcrumb')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Subject Coupon Codes') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Subject Coupon Codes</li>
                </ol>
            </nav>
        </div>
    </div>
    <div id="error">
        @include('includes.message')
    </div>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="col-md-12">
                        <h4 class="card-title mg-b-0 mt-2">Subject Coupon List ({{ $laSubjectCouponCodes->total() }})</h4>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-11">
                                    <form method="get" action="{{ route('admin.subjects.coupon.codes') }}">
                                        <div class=" d-flex row mb-3 justify-content-end">
                                            <div class="col-md-5">
                                            </div>
                                            <div class="col-md-5">
                                                <select name="la_subject_id" class="form-control">
                                                    <option value="">Select Subject</option>
                                                    @foreach ($subjects as $key => $subject)
                                                        <option value="{{ $subject->id }}"
                                                            @if ($laSubjectId == $subject->id) selected @endif>
                                                            {{ $subject->default_title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="submit" class="btn btn-success">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-1">
                                    <form method="get" action="{{ route('admin.subjects.coupon.codes') }}">
                                        <input type="hidden" name="type" value="export">
                                        <input type="hidden" name="la_subject_id" value="{{ $laSubjectId }}">
                                        <button type="submit" data-toggle="tooltip" data-placement="top" title="export"
                                            class="btn btn-purple ml-2">Export</button>
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
                                    <th class="border-bottom-0" scope="col">Sr No.</th>
                                    <th class="border-bottom-0" scope="col">Subject</th>
                                    <th class="border-bottom-0" scope="col">Coupon Code</th>
                                    <th class="border-bottom-0" scope="col">Assign Coupon Student</th>
                                    <th class="border-bottom-0" scope="col">Unlock Coupon Date Time</th>
                                </tr>
                            </thead>
                            <?php $i = 1; ?>
                            <tbody>
                                <?php $i = $laSubjectCouponCodes->perPage() * ($laSubjectCouponCodes->currentPage() - 1) + 1; ?>
                                @if (count($laSubjectCouponCodes) > 0)
                                    @foreach ($laSubjectCouponCodes as $laSubjectCouponCode)
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ $laSubjectCouponCode->laSubject ? $laSubjectCouponCode->laSubject->default_title : '' }}
                                            </td>
                                            <td>{{ $laSubjectCouponCode->coupon_code }}</td>
                                            <td>{{ $laSubjectCouponCode->user ? $laSubjectCouponCode->user->name : '' }}
                                            </td>
                                            <td>{{ $laSubjectCouponCode->unlock_coupon_at ? date('d-m-Y H:i:s', strtotime($laSubjectCouponCode->unlock_coupon_at)) : '' }}
                                            </td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="10">No Data Found </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        {{ $laSubjectCouponCodes->appends(Request::all())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
@endsection
@section('js')
@endsection
