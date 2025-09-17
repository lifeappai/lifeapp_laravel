@extends('layouts.admin')
@section('css')
@endsection
<style>
    .card.radio-card {
        border: 2px solid #fff !important;
    }

    .radio-card.active {
        border: 2px solid #8a9ae8 !important;
    }
</style>
@section('breadcrumb')
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Subjects') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Subjects</li>
                </ol>
            </nav>
        </div>
    </div>
    <div id="error">
        @include('includes.message')
    </div>
@endsection
@section('content')
    <form method="get" action="{{ route('admin.levels.index') }}">

        <div class="row row-sm">
            <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
                <div class="card">
                    <div class="card-header text-right">
                    </div>
                    <div class="card-body text-center ">
                        <div class="feature widget-2 text-center mt-0 mb-3">
                            <i class="ti-pie-chart project bg-pink-transparent mx-auto text-pink "></i>
                        </div>
                        <h3 class="font-weight-semibold"><a href="{{ route('admin.subjects.create') }}" target="_blank">Add
                                Subject</a> </h3>
                    </div>
                </div>
            </div>
            @if (count($subjects) > 0)
                @foreach ($subjects as $key => $subject)
                    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
                        <label class="card radio-card" for="subject_id{{ $key }}">
                            <input type="radio" name="la_subject_id" id="subject_id{{ $key }}" hidden
                                value="{{ $subject->id }}" required>
                            <div class="card-header text-right">
                                <a href="{{ route('admin.subjects.edit', $subject->id) }}"
                                    class="btn btn-sm btn-danger waves-effect waves-light" target="_blank">Edit</a>
                                @if (App\Enums\StatusEnum::DEACTIVE == $subject->status)
                                    <button type="button" class="btn btn-sm btn-danger waves-effect waves-light"
                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.subjects.status', $subject->id) }}', 'Are You Sure To Publish')">Draft</button>
                                @else
                                    <button type="button" class="btn btn-sm btn-success waves-effect waves-light"
                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.subjects.status', $subject->id) }}', 'Are You Sure To Draft')">Publish</button>
                                @endif
                            </div>
                            <div class="card-body text-center ">
                                <div class="feature widget-2 text-center mt-0 mb-3">
                                    <i class="ti-pie-chart project bg-pink-transparent mx-auto text-pink "></i>
                                </div>
                                <h6 class="mb-1 text-muted">{{ $subject->default_title }}</h6>
                                <h3 class="font-weight-semibold">{{ $subject->default_heading }}</h3>
                            </div>
                        </label>
                    </div>
                @endforeach
            @endif
        </div>
        <button type="submit" id="submit" class="btn btn-success">Next</button>
    </form>
@endsection
@section('js')
    <script>
        document.getElementById("submit").addEventListener("click", function() {
            var type = 0;
            let radioButtons = document.querySelectorAll('input[type=radio]');
            for (let radio of radioButtons) {
                if (radio.checked) {
                    var type = 1;
                }
            }
            if (type == 0) {
                alert("Please Select Subject");
                return false;
            } else {
                $("#submit").attr("type", "submit");
            }
        });
    </script>
@endsection
