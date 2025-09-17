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
                <h4 class="content-title mb-2">{{ __('Sessions Participants') }} ({{ $participants->total() }})</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Session</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sessions Participants</li>
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
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap data-table">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" scope="col">Sr No.</th>
                                    <th class="border-bottom-0" scope="col">User Name</th>
                                    <th class="border-bottom-0">Mobile Number</th>
                                    <th class="border-bottom-0">School</th>
                                    <th class="border-bottom-0">School Code</th>
                                    <th class="border-bottom-0">Date</th>
                                </tr>
                            </thead>
                            <?php $i = $participants->perPage() * ($participants->currentPage() - 1) + 1; ?>
                            <tbody>
                                @if (count($participants) > 0)
                                    @foreach ($participants as $participant)
                                        <tr class="user_list">
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $participant->user->name ?? '' }}</td>
                                            <td>{{ $participant->user->mobile_no }}</td>
                                            <td>{{ $participant->user->school->name ?? '' }}
                                            </td>
                                            <td>{{ $participant->user->school->code ?? '' }}
                                            </td>
                                            <td>{{ date('d-m-Y', strtotime($participant->created_at)) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <td class="text-center" colspan="7">No Session Participants Found </td>
                                @endif
                            </tbody>
                        </table>
                        @if (count($participants) > 0)
                            {{ $participants->appends(Request::all())->links() }}
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
