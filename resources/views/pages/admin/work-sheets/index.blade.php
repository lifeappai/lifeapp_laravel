@extends('layouts.admin')
@section('css')
@endsection
@section('breadcrumb')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Work Sheets') }} ({{ $laWorkSheets->total() }})</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Work Sheets</li>
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
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-0 mt-2"></h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>

                    </div>
                </div>
                <div class="card-header pb-0">
                    <div class="d-flex">
                        <div class="col-md-10">
                            <form method="GET">
                                <div class="d-flex">
                                    <div class="col-md-3">
                                        <select name="la_subject_id" class="form-control">
                                            <option value="">Select subject</option>
                                            @foreach ($subjects as $key => $subject)
                                                <option value="{{ $subject->id }}"
                                                    @if ($request->la_subject_id == $subject->id) selected @endif>
                                                    {{ $subject->default_title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="status" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="{{ App\Enums\StatusEnum::ACTIVE }}"
                                                @if (isset($request->status) && $request->status == App\Enums\StatusEnum::ACTIVE) selected @endif>Published</option>
                                            <option value="{{ App\Enums\StatusEnum::DEACTIVE }}"
                                                @if (isset($request->status) && $request->status == App\Enums\StatusEnum::DEACTIVE) selected @endif>Drafted</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex">
                                        <button type="submit" class="btn btn-success  mr-3">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <form method="get" action="">
                            <div class="d-flex">
                                <div class="col-md-10"></div>
                                <div class="col-md-2">
                                    <a class="form-control btn btn-success"
                                        href="{{ route('admin.work.sheets.create') }}">Add Work Sheet</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" scope="col">Subject</th>
                                    <th class="border-bottom-0" scope="col">Grade</th>
                                    <th class="border-bottom-0" scope="col">Title</th>
                                    <th class="border-bottom-0" scope="col">Document</th>
                                    <th class="border-bottom-0" scope="col">Status</th>
                                    <th class="border-bottom-0" scope="col">Action</th>
                                </tr>
                            </thead>
                            <?php $i = 1; ?>
                            <tbody>
                                @if (count($laWorkSheets) > 0)
                                    @foreach ($laWorkSheets as $laWorkSheet)
                                        <tr>
                                            <td>{{ $laWorkSheet->subject ? $laWorkSheet->subject->default_title : '' }}
                                            </td>
                                            <td>{{ $laWorkSheet->laGrade ? $laWorkSheet->laGrade->name : '' }}
                                            </td>
                                            <td>{{ $laWorkSheet->title }}</td>
                                            <td>
                                                @if (isset($laWorkSheet->media))
                                                    <a target="_blank"
                                                        href="{{ $imageBaseUrl . $laWorkSheet->media->path }}">
                                                        View Document
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if (App\Enums\StatusEnum::DEACTIVE == $laWorkSheet->status)
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger waves-effect waves-light"
                                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.work.sheets.status', $laWorkSheet->id) }}', 'Are You Sure To Publish')">Draft</button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-success waves-effect waves-light"
                                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.work.sheets.status', $laWorkSheet->id) }}', 'Are You Sure To Draft')">Publish</button>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('admin.work.sheets.edit', $laWorkSheet->id) }}"><button
                                                            class='btn btn-purple btn-sm'> Edit </button></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="10">No Data Found </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        {{ $laWorkSheets->appends(Request::all())->links() }}
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
