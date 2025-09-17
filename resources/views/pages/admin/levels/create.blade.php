@extends('layouts.admin')
@section('css')
    <style>
        .error {
            color: red;
            margin-top: 5px;
        }
    </style>
@endsection
@section('breadcrumb')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Levels') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Levels</li>
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
                    <form method="POST" action="{{ route('admin.levels.store') }}" id="languageForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4  mg-b-20">
                                <h6>Mission Points<span class="text-danger">*</span></h6>
                                <input type="text" name="mission_points" class="form-control"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
                            </div>
                            <div class="col-md-4  mg-b-20">
                                <h6>Quiz Points<span class="text-danger">*</span></h6>
                                <input type="text" name="quiz_points" class="form-control"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
                            </div>
                            <div class="col-md-4  mg-b-20">
                                <h6>Puzzle Points<span class="text-danger">*</span></h6>
                                <input type="text" name="puzzle_points" class="form-control"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
                            </div>
                            <div class="col-md-4  mg-b-20">
                                <h6>Riddle Points<span class="text-danger">*</span></h6>
                                <input type="text" name="riddle_points" class="form-control"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
                            </div>
                            <div class="col-md-4  mg-b-20">
                                <h6>Jigyasa Points<span class="text-danger">*</span></h6>
                                <input type="text" name="jigyasa_points" class="form-control"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
                            </div>
                            <div class="col-md-4  mg-b-20">
                                <h6>Pragya Points<span class="text-danger">*</span></h6>
                                <input type="text" name="pragya_points" class="form-control"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
                            </div>
                            <div class="col-md-4  mg-b-20">
                                <h6>Quiz Timing<span class="text-danger">*</span></h6>
                                <input type="text" name="quiz_time" class="form-control"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
                            </div>
                            <div class="col-md-4  mg-b-20">
                                <h6>Puzzle Timing<span class="text-danger">*</span></h6>
                                <input type="text" name="puzzle_time" class="form-control"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
                            </div>
                            <div class="col-md-4  mg-b-20">
                                <h6>Riddle Timing<span class="text-danger">*</span></h6>
                                <input type="text" name="riddle_time" class="form-control"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
                            </div>
                            <table class="table table-bordered repeater">
                                <thead class="align-middle">
                                    <tr>
                                        <th style="width: 30%">Language</th>
                                        <th style="width: 30%">Title</th>
                                        <th style="width: 30%">Description</th>
                                        <th style="width: 10%">
                                            <input data-repeater-create="" type="button"
                                                class="btn btn-success mt-3 mt-lg-0" value="Add">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle" data-repeater-list="level_translation">
                                    <tr data-repeater-item="">
                                        <td>
                                            <select name="language" id="language1" class="form-control" required>
                                                @foreach ($languages as $key => $language)
                                                    <option value="{{ $language->slug }}">{{ $language->title }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" id="title" name="title"
                                                placeholder="Enter Title" required="" autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" id="description" name="description"
                                                placeholder="Enter Description" required="" autocomplete="off">
                                        </td>
                                        <td>
                                            <input data-repeater-delete="" type="button"
                                                class="btn btn-danger waves-effect waves-light" value="Delete">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
    <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".repeater").repeater({
                show: function() {
                    var specificSelect = $(this).find('#language1');
                    specificSelect.val(specificSelect.find('option:first').val());
                    $(this).slideDown()
                },
                hide: function(e) {
                    confirm("Are you sure you want to delete this element?") && $(this).slideUp(e)
                },
                ready: function(e) {},
                isFirstItemUndeletable: true
            });
            $("#languageForm").parsley();
        });
    </script>
@endsection
