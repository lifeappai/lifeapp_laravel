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
                        <h4 class="card-title mg-b-0 mt-2">Game Enrollment List ({{ $laGameEnrollments->total() }})</h4>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-9">
                                    <form method="get" action="">
                                        <div class=" d-flex row mb-3 justify-content-end">
                                            <div class="col-md-5">
                                            </div>
                                            <div class="col-md-5">
                                                <select name="type" class="form-control">
                                                    <option value="">Select Type</option>
                                                    @foreach (App\Enums\GameEnrollmentTypeEnum::TYPE as $key => $gameType)
                                                        <option value="{{ $gameType }}"
                                                            @if ($type == $gameType) selected @endif>
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
                                <div class="col-md-1">
                                    <form method="get" action="">
                                        <input type="hidden" name="type" value="{{ $type }}">
                                        <input type="hidden" name="type_export" value="export">
                                        <button type="submit" data-toggle="tooltip" data-placement="top" title="export"
                                            class="btn btn-purple ml-2">Export</button>
                                    </form>
                                </div>
                                <div class="col-md-2">
                                    <a class="form-control btn btn-success"
                                        href="{{ route('admin.game.enrollments.create') }}">Add Enrollments</a>
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
                                    <th class="border-bottom-0" scope="col">Type</th>
                                    <th class="border-bottom-0" scope="col">Enrollment Code</th>
                                    <th class="border-bottom-0" scope="col">Assign Eenrollment Student</th>
                                    <th class="border-bottom-0" scope="col">Unlock Eenrollment Date Time</th>
                                </tr>
                            </thead>
                            <?php $i = 1; ?>
                            <tbody>
                                <?php $i = $laGameEnrollments->perPage() * ($laGameEnrollments->currentPage() - 1) + 1; ?>
                                @if (count($laGameEnrollments) > 0)
                                    @foreach ($laGameEnrollments as $laGameEnrollment)
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>
                                                {{ ucwords(str_replace('_', ' ', strtolower(array_search($laGameEnrollment->type, App\Enums\GameEnrollmentTypeEnum::TYPE)))) }}
                                            </td>
                                            <td>{{ $laGameEnrollment->enrollment_code }}</td>
                                            <td>{{ $laGameEnrollment->user ? $laGameEnrollment->user->name : '' }}
                                            </td>
                                            <td>{{ $laGameEnrollment->unlock_enrollment_at ? date('d-m-Y H:i:s', strtotime($laGameEnrollment->unlock_enrollment_at)) : '' }}
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
                        {{ $laGameEnrollments->appends(Request::all())->links() }}
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
