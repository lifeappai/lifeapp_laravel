@extends('layouts.admin')
@section('css')
    <style>
        .error {
            color: red;
            margin-top: 5px;
        }
        .custom-thumbnail {
            width: 100px;
            height: 100px;
        }
    </style>
@endsection
@section('breadcrumb')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Coupons') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Coupons</li>
                </ol>
            </nav>
        </div>
    </div>
    <div id="error">
        @include('includes.message')
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.coupons.update', $coupon->id) }}" id="languageForm"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-12  mg-b-20">
                                <h6>Title<span class="text-danger">*</span></h6>
                                <input type="text" name="title" class="form-control" value="{{ $coupon->title }}"
                                    required>
                            </div>
                            <div class="col-md-6  mg-b-20">
                                <h6>Coins<span class="text-danger">*</span></h6>
                                <input type="text" name="coin" class="form-control"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57'
                                    value="{{ $coupon->coin }}" required>
                            </div>
                            <div class="col-md-6  mg-b-20">
                                <h6>Link<span class="text-danger">*</span></h6>
                                <input type="text" name="link" class="form-control" value="{{ $coupon->link }}"
                                    required>
                            </div>
                            <div class="col-md-12  mg-b-20">
                                <h6>Description<span class="text-danger">*</span></h6>
                                <textarea name="details" id="details" rows="10" required class="form-control">{{ $coupon->details }}</textarea>
                            </div>
                            <div class="col-md-6  mg-b-20">
                                <h6>Image</h6>
                                <input type="file" class="form-control" id="image" name="image" autocomplete="off"
                                    onchange="loadFile(this)">
                            </div>
                            <div class="col-md-6  mg-b-20">
                                @php
                                    $couponImage = $coupon->media ? $imageBaseUrl . $coupon->media->path : '';
                                @endphp
                                @if ($couponImage)
                                    <a class="image-popup-no-margins" href="{{ $couponImage }}">
                                        <img alt="" src="{{ $couponImage }}" class="custom-thumbnail">
                                    </a>
                                @endif
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/js/parsley.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#languageForm").parsley();
        });
        var loadFile = function(event) {
            var filePath = event.value;
            var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.jfif)$/i;
            if (!allowedExtensions.exec(filePath)) {
                alert('Please upload file having extensions .jpeg, .jpg .png only.');
                event.value = '';
            } else {
                if (event.files && event.files[0]) {
                    var reader = new FileReader();
                    reader.readAsDataURL(event.files[0]);
                }
            }
        };
    </script>
@endsection
