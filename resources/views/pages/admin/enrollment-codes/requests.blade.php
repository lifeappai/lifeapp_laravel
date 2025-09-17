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
                <h4 class="content-title mb-2">{{ __('Game Enrollment Codes') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Game Enrollment Codes</li>
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
                        <h4 class="card-title mg-b-0 mt-2">Game Enrollment Requested List
                            ({{ $laRequestGameEnrollments->total() }})</h4>
                    </div>
                    <div class="d-flex justify-content-end">
                        <div class="col-md-12">
                            <form method="get" action="">
                                <div class=" d-flex row mb-3 justify-content-end mt-3">
                                    <div class="col-md-3">
                                        <select name="status" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="pending" @if ($request->status == 'pending') selected @endif>
                                                Pending</option>
                                            <option value="approved" @if ($request->status == 'approved') selected @endif>
                                                Approved</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="type" class="form-control">
                                            <option value="">Select Type</option>
                                            @foreach (App\Enums\LessionPlanCategoryEnum::Category as $key => $gameType)
                                                <option value="{{ $gameType }}"
                                                    @if ($request->type == $gameType) selected @endif>
                                                    {{ ucwords(str_replace('_', ' ', strtolower($key))) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-success">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" scope="col">Sr No.</th>
                                    <th class="border-bottom-0" scope="col">User</th>
                                    <th class="border-bottom-0" scope="col">Type</th>
                                    <th class="border-bottom-0" scope="col">Status</th>
                                    <th class="border-bottom-0" scope="col">Code</th>
                                    <th class="border-bottom-0" scope="col">Approve At</th>
                                </tr>
                            </thead>
                            <?php $i = 1; ?>
                            <tbody>
                                <?php $i = $laRequestGameEnrollments->perPage() * ($laRequestGameEnrollments->currentPage() - 1) + 1; ?>
                                @if (count($laRequestGameEnrollments) > 0)
                                    @foreach ($laRequestGameEnrollments as $laRequestGameEnrollment)
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ $laRequestGameEnrollment->user->name ?? '' }}
                                            <td>
                                                {{ ucwords(str_replace('_', ' ', strtolower(array_search($laRequestGameEnrollment->type, App\Enums\GameEnrollmentTypeEnum::TYPE)))) }}
                                            </td>
                                            <td>
                                                @if ($laRequestGameEnrollment->approved_at != null)
                                                    Approved
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger waves-effect waves-light"
                                                        onclick="sweetAlertAjax('POST','{{ route('admin.game.enrollment.requests.approve', $laRequestGameEnrollment->id) }}', 'Are You Sure To Approve')">Approve</button>
                                                @endif
                                            </td>
                                            <td>{{ $laRequestGameEnrollment->laGameEnrollment->enrollment_code ?? null }}
                                            </td>
                                            <td>
                                                {{ $laRequestGameEnrollment->approved_at != null ? date('d-m-Y', strtotime($laRequestGameEnrollment->approved_at)) : '' }}
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
                        {{ $laRequestGameEnrollments->appends(Request::all())->links() }}
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
