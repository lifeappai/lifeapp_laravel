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
                    <li class="breadcrumb-item active" aria-current="page">Coins</li>
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
    <style>
        .custom-thumbnail {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }
    </style>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="card-title mg-b-0 mt-2">Earned Coins By Quiz</h4>
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
                                    <th class="border-bottom-0">Coins</th>
                                    <th class="border-bottom-0">Quiz Name</th>
                                    <th class="border-bottom-0">Time</th>
                                    <th class="border-bottom-0">Date</th>
                                </tr>
                            </thead>
                            <?php $i = $quizCoins->perPage() * ($quizCoins->currentPage() - 1) + 1; ?>
                            <tbody>
                                @if (count($quizCoins) > 0)
                                    @foreach ($quizCoins as $coins)
                                        <tr class="user_list">
                                            <td>{{ $i }}</td>
                                            <td>{{ $coins->amount }}</td>
                                            <td>
                                                @if(isset($coins->coinable_object) && !empty($coins->coinable_object) && $coins->coinable_object != '') 
                                                    @foreach ($coins->coinable_object['title'] as $key => $title)
                                                        {{ $key }} => {{ $title }} <br>
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ isset($coins->coinable_object) && !empty($coins->coinable_object) && $coins->coinable_object != '' ? $coins->coinable_object['time'].' Seconds' : '-' }}</td>
                                            <td>{{ date('Y-m-d',strtotime($coins->created_at)) }}</td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                    <tr class="user_list">
                                        <td>Total</td>
                                        <td>{{ $totalQuizCoins }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @else
                                    <td class="text-center" colspan="7">No Data Found </td>
                                @endif
                            </tbody>
                        </table>
                        @if (count($quizCoins) > 0)
                            {{ $quizCoins->appends(Request::all())->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="card-title mg-b-0 mt-2">Earned Coins By Missions</h4>
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
                                    <th class="border-bottom-0">Coins</th>
                                    <th class="border-bottom-0">Mission Name</th>
                                    <th class="border-bottom-0">Date</th>
                                </tr>
                            </thead>
                            <?php $i = $missionsCoins->perPage() * ($missionsCoins->currentPage() - 1) + 1; ?>
                            <tbody>
                                @if (count($missionsCoins) > 0)
                                    @foreach ($missionsCoins as $coins)
                                        <tr class="user_list">
                                            <td>{{ $i }}</td>
                                            <td>{{ $coins->amount }}</td>
                                            <td>{{ isset($coins->coinable_object) && !empty($coins->coinable_object) && $coins->coinable_object != '' ? $coins->coinable_object['title'] : '-' }}</td>
                                            <td>{{ date('Y-m-d',strtotime($coins->created_at)) }}</td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                    <tr class="user_list">
                                        <td>Total</td>
                                        <td>{{ $totalMissionsCoins }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @else
                                    <td class="text-center" colspan="7">No Data Found </td>
                                @endif
                            </tbody>
                        </table>
                        @if (count($missionsCoins) > 0)
                            {{ $missionsCoins->appends(Request::all())->links() }}
                        @endif
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
