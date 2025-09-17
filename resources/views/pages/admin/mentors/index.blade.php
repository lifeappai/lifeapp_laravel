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
                <h4 class="content-title mb-2">{{ __('Mentors') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mentors</li>
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
                                    <a class="form-control btn btn-success" href="{{ route('admin.mentors.create') }}">Add
                                        Mentor</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap data-table">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" scope="col">Sr No.</th>
                                    <th class="border-bottom-0" scope="col">Name</th>
                                    <th class="border-bottom-0" scope="col">Email</th>
                                    <th class="border-bottom-0" scope="col">Mobile Number</th>
                                    <th class="border-bottom-0" scope="col">Mentor Code</th>
                                    <th class="border-bottom-0" scope="col">Action</th>
                                </tr>
                            </thead>
                             <?php $i = $mentors->perPage() * ($mentors->currentPage() - 1) + 1; ?>
                             <tbody>
                                @if (count($mentors) > 0)
                                    @foreach ($mentors as $mentor)
                                        <tr class="user_list">
                                          <td>{{ $i }}</td>
                                            <td>{{ $mentor->name ?? '-' }}</td>
                                            <td>{{ $mentor->email ?? '-' }}</td>
                                            <td>{{ $mentor->mobile_no ?? '-' }}</td>
                                            <td>{{ $mentor->pin ?? '-' }}</td>
                                             <td><a class="mr-2" href="{{ route('admin.mentors.edit', $mentor->id) }}"><button
                                                        class="btn btn-warning">Edit</button></a></td>
                                         </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                @else
                                    <td class="text-center" colspan="7">No Mentors Found </td>
                                @endif
                            </tbody>
                        </table>
                        @if (count($mentors) > 0)
                            {{ $mentors->appends(Request::all())->links() }}
                        @endif
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
