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
                <h4 class="content-title mb-2">{{ __('Boards') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Boards</li>
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
                <div class="card-body">
                    <div class="mb-5">
                        <form method="get" action="">
                            <div class="d-flex">
                                <div class="col-md-10"></div>
                                <div class="col-md-2">
                                    <a class="form-control btn btn-success" href="{{ route('admin.boards.create') }}">Add
                                        Board</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" scope="col">Sr No.</th>
                                    <th class="border-bottom-0" scope="col">Name</th>
                                    <th class="border-bottom-0" scope="col">Status</th>
                                    <th class="border-bottom-0" scope="col">Action</th>
                                </tr>
                            </thead>
                            <?php $i = 1; ?>
                            <tbody>
                                @if (count($laBoards) > 0)
                                    @foreach ($laBoards as $laBoards)
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>
                                                {{ $laBoards->name }}
                                            </td>
                                            <td>
                                                @if (App\Enums\StatusEnum::DEACTIVE == $laBoards->status)
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger waves-effect waves-light"
                                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.boards.status', $laBoards->id) }}', 'Are You Sure To Publish')">Draft</button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-success waves-effect waves-light"
                                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.boards.status', $laBoards->id) }}', 'Are You Sure To Draft')">Publish</button>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('admin.boards.edit', $laBoards->id) }}"><button
                                                            class='btn btn-purple btn-sm'> Edit </button></a>
                                                </div>
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
