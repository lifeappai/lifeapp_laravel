@extends('layouts.admin')
@section('css')
@endsection
@section('breadcrumb')
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Categories') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Categories</li>
                </ol>
            </nav>
        </div>
    </div>
    <div id="error">
        @include('includes.message')
    </div>
@endsection
@section('content')
    <form method="get" id="category_form" action="">
        @foreach (request()->all() as $key => $value)
            @if (!is_array($value))
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @else
                @foreach ($value as $item)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                @endforeach
            @endif
        @endforeach
        @php
            $i = 1;
        @endphp
        <div class="row row-sm">
            @foreach ($categories as $key => $category)
                <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
                    <label class="card radio-card" for="category{{ $key }}">
                        <input type="radio" class="category" name="type" id="category{{ $key }}" hidden
                            value="{{ $i }}" required>
                        <div class="card-header text-right">
                        </div>
                        <div class="card-body text-center ">
                            <h6 class="mb-1 text-muted">{{ ucfirst($key) }}</h6>
                            <h3 class="font-weight-semibold">{{ $category }}</h3>
                        </div>
                    </label>
                </div>
                @php $i++; @endphp
            @endforeach
        </div>
        <button type="submit" id="submit" class="btn btn-success">Next</button>
    </form>

    <input type="hidden" id="mission_url" value="{{ route('admin.la.missions.index', request()->all()) }}">
    <input type="hidden" id="topic_url" value="{{ route('admin.topics.index', request()->all()) }}">
@endsection
@section('js')
    <script>
        $(".category").change(function() {
            if ($(this).val() == "1" || $(this).val() == "5" || $(this).val() == "6") {
                $("#category_form").attr("action", $("#mission_url").val());
            } else {
                $("#category_form").attr("action", $("#topic_url").val());
            }
        })

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
