@extends('layouts.admin')
@section('css')
    <style>
        .error {
            color: red;
            margin-top: 5px;
        }

        .custom-thumbnail {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }
    </style>
@endsection
@section('breadcrumb')
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Missions') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Missions</li>
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
                    <div class="row mb-3 mt-3">
                        <div class="col-md-12">
                            <h6 for="">Points: <span class="ml-2"> {{ $laMission->points }}</span></h6>
                        </div>
                        <div class="col-md-12">
                            @php $i=1; @endphp
                            <h6 for="">Title:</h6>
                            @if (count($laMission->title) > 0)
                                @foreach ($laMission->title as $key => $title)
                                    {{ $i . '. ' }} {{ $key }} = {{ $title }} <br>
                                    <?php $i++; ?>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <hr>
                    <form method="POST" action="{{ route('admin.la.missions.resources', $laMission->id) }}"
                        id="languageForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3 mt-3">
                            <div class="col-md-12  mg-b-20">
                                <h4>Resources<span class="text-danger">*</span></h4>
                            </div>
                            @if (count($resources) > 0)
                                <div class="repeater col-md-12">
                                    <div data-repeater-list="new_resources" class="mb-5">
                                        @foreach ($resources as $masterKey => $resource)
                                            <div data-repeater-item class="mb-3" style="border: 1px solid;padding: 15px;">
                                                <div class="new_resources_div">
                                                    <div class="row">
                                                        <div class="col-md-11 m-auto">
                                                            <div class="row">
                                                                <div class="col-md-2 mb-3">
                                                                    Language
                                                                </div>
                                                                <div class="col-md-4">
                                                                    Title
                                                                </div>
                                                                <div class="col-md-4">
                                                                    Image
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-11 inner-repeater m-auto mt-2"
                                                            style="border: 1px solid;padding: 10px;background: #e6ebef;">
                                                            <div data-repeater-list="resource">
                                                                @php
                                                                    $resourcesData = $resource->getResourcesData($resource->index);
                                                                @endphp
                                                                @foreach ($resourcesData as $keyData => $resourceData)
                                                                    <div data-repeater-item class="row mb-3">
                                                                        <div class="col-md-2">
                                                                            <select name="language" id="language1"
                                                                                class="form-control" required>
                                                                                @foreach ($languages as $key => $language)
                                                                                    <option value="{{ $language->slug }}"
                                                                                        @if ($resourceData->locale == $language->slug) selected @endif>
                                                                                        {{ $language->title }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <input type="text" class="form-control"
                                                                                id="title" name="title"
                                                                                placeholder="Enter Title" required=""
                                                                                value="{{ $resourceData->title }}"
                                                                                autocomplete="off">
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <input type="file" class="form-control"
                                                                                id="image" name="image"
                                                                                autocomplete="off"
                                                                                onchange="loadFile(this)">
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            @if ($resourceData->media)
                                                                                <input type="hidden" class="form-control"
                                                                                    id="media_id" name="media_id"
                                                                                    value="{{ $resourceData->media_id }}">
                                                                                <a class="image-popup-no-margins"
                                                                                    href="{{ $imageBaseUrl . $resourceData->media->path }}">
                                                                                    <img alt=""
                                                                                        src="{{ $imageBaseUrl . $resourceData->media->path }}"
                                                                                        class="custom-thumbnail">
                                                                                </a>
                                                                            @endif
                                                                            <input data-repeater-delete="" type="button"
                                                                                class="btn btn-danger waves-effect waves-light"
                                                                                value="Delete">
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            {{-- <div class="text-right mt-2">
                                                                <input data-repeater-create="" type="button"
                                                                    class="btn btn-success mt-3 mt-lg-0"
                                                                    value="Add Translation">
                                                            </div> --}}
                                                        </div>
                                                    </div>
                                                    <div class="text-right mt-2">
                                                        <input data-repeater-delete="" type="button"
                                                            class="btn btn-danger waves-effect waves-light"
                                                            value="Delete This Resource">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="text-right mb-2">
                                        <input data-repeater-create type="button" class="btn btn-success mt-3 mt-lg-0"
                                            value="Add New Resource">
                                    </div>
                                </div>
                            @else
                                <div class="repeater col-md-12">
                                    <div data-repeater-list="new_resources" class="mb-5">
                                        <div data-repeater-item class="mb-3" style="border: 1px solid;padding: 15px;">
                                            <div class="new_resources_div">
                                                <div class="row">
                                                    <div class="col-md-11 m-auto">
                                                        <div class="row">
                                                            <div class="col-md-2 mb-3">
                                                                Language
                                                            </div>
                                                            <div class="col-md-4">
                                                                Title
                                                            </div>
                                                            <div class="col-md-4">
                                                                Image
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-11 inner-repeater m-auto mt-2"
                                                        style="border: 1px solid;padding: 10px;background: #e6ebef;">
                                                        <div data-repeater-list="resource">
                                                            <div data-repeater-item class="row mb-3">
                                                                <div class="col-md-2">
                                                                    <select name="language" id="language1"
                                                                        class="form-control" required>
                                                                        @foreach ($languages as $key => $language)
                                                                            <option value="{{ $language->slug }}">
                                                                                {{ $language->title }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input type="text" class="form-control"
                                                                        id="title" name="title"
                                                                        placeholder="Enter Title" required=""
                                                                        autocomplete="off">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input type="file" class="form-control"
                                                                        id="image" name="image" required=""
                                                                        autocomplete="off" onchange="loadFile(this)">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <input data-repeater-delete="" type="button"
                                                                        class="btn btn-danger waves-effect waves-light"
                                                                        value="Delete">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="text-right mt-2">
                                                            <input data-repeater-create="" type="button"
                                                                class="btn btn-success mt-3 mt-lg-0"
                                                                value="Add Translation">
                                                        </div> --}}
                                                    </div>
                                                </div>
                                                <div class="text-right mt-2">
                                                    <input data-repeater-delete="" type="button"
                                                        class="btn btn-danger waves-effect waves-light"
                                                        value="Delete This Resource">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right mb-2">
                                        <input data-repeater-create type="button" class="btn btn-success mt-3 mt-lg-0"
                                            value="Add New Resource">
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-12">
                                <button type="submit" id="submit" class="btn btn-success">Submit</button>
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
    <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
    <script>
        var $repeater = $('.repeater').repeater({
            repeaters: [{
                selector: '.inner-repeater',
                show: function() {
                    $(this).find('a').css("display", "none");
                    $(this).find('input[type="file"]').attr("required", true);
                    $(this).slideDown()
                },
                hide: function(e) {
                    confirm("Are you sure you want to delete this option value?") && $(this).slideUp(e)
                },
                ready: function(e) {},
                isFirstItemUndeletable: true
            }],
            show: function() {
                $(this).find('a').css("display", "none");
                $(this).find('input[type="file"]').attr("required", true);
                var specificSelect = $(this).find('#language1');
                specificSelect.val(specificSelect.find('option:first').val());
                $(this).slideDown();
            },
            hide: function(e) {
                confirm("Are you sure you want to delete this option?") && $(this).slideUp(e)
            },
            ready: function(e) {},
            isFirstItemUndeletable: true

        });
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
