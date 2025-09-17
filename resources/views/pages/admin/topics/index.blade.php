@extends('layouts.admin')
@section('css')
@endsection
@section('breadcrumb')
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Topics') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Topics</li>
                </ol>
            </nav>
        </div>
    </div>
    <div id="error">
        @include('includes.message')
    </div>
@endsection
@section('content')
    <form method="get" action="{{ route('admin.questions.index', request()->all()) }}">
        @foreach (request()->all() as $key => $value)
            @if (!is_array($value))
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @else
                @foreach ($value as $item)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                @endforeach
            @endif
        @endforeach
        <div class="row row-sm">
            <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
                <div class="card">
                    <div class="card-header text-right">
                    </div>
                    <div class="card-body text-center ">
                        <div class="feature widget-2 text-center mt-0 mb-3">
                            <i class="ti-pie-chart project bg-pink-transparent mx-auto text-pink "></i>
                        </div>
                        <h3 class="font-weight-semibold"><a href="{{ route('admin.topics.create', request()->all()) }}" target="_blank">Add
                                Topic</a> </h3>
                    </div>
                </div>
            </div>
            @if (count($laTopics) > 0)
                @foreach ($laTopics as $key => $laTopic)
                    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
                        <label class="card radio-card" for="laTopic_id{{ $key }}">
                            <input type="radio" name="la_topic_id" id="laTopic_id{{ $key }}" hidden
                                value="{{ $laTopic->id }}" required>
                            <div class="card-header text-right">
                                <a href="{{ route('admin.topics.edit', $laTopic->id) }}"
                                    class="btn btn-sm btn-danger waves-effect waves-light" target="_blank">Edit</a>
                                @if (App\Enums\StatusEnum::DEACTIVE == $laTopic->status)
                                    <button type="button" class="btn btn-sm btn-danger waves-effect waves-light"
                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.topics.status', $laTopic->id) }}', 'Are You Sure To Publish')">Draft</button>
                                @else
                                    <button type="button" class="btn btn-sm btn-success waves-effect waves-light"
                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.topics.status', $laTopic->id) }}', 'Are You Sure To Draft')">Publish</button>
                                @endif
                            </div>
                            <div class="card-body text-center ">
                                <div class="feature widget-2 text-center mt-0 mb-3">
                                    <i class="ti-pie-chart project bg-pink-transparent mx-auto text-pink "></i>
                                </div>
                                <h6 class="mb-1 text-muted">{{ $laTopic->default_title }}</h6>
                                <h3 class="font-weight-semibold">{{ $laTopic->default_description }}</h3>
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
                alert("Please Select Level");
                return false;
            } else {
                $("#submit").attr("type", "submit");
            }
        });
    </script>
@endsection
