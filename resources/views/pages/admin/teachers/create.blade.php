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
                <h4 class="content-title mb-2">{{ __('Teachers') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Teachers</li>
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
                    <form method="POST" action="{{ route('admin.teachers.store') }}" id="teacherForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6 mg-b-20">
                                <h6>Name<span class="text-danger">*</span></h6>
                                <input type="text" name="name" class="form-control" required
                                    onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) ||  (event.charCode = 32)" value="{{ old('name') }}">
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Email<span class="text-danger">*</span></h6>
                                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Mobile Number<span class="text-danger">*</span></h6>
                                <input type="text" name="mobile_no" class="form-control" required maxlength="10"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' minlength="10" value="{{ old('mobile_no') }}">
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>School Code<span class="text-danger">*</span></h6>
                                <input type="text" name="school_code" class="form-control" required
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' value="{{ old('school_code') }}">
                            </div>
                            <table class="table table-bordered repeater">
                                <thead class="align-middle">
                                    <tr>
                                        <th style="width: 25%">Subject</th>
                                        <th style="width: 25%">Grade</th>
                                        <th style="width: 25%">Section</th>
                                        <th style="width: 25%">
                                            <input data-repeater-create="" type="button"
                                                class="btn btn-success mt-3 mt-lg-0" value="Add">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle" data-repeater-list="teacher_grades">
                                    <tr data-repeater-item="">
                                        <td>
                                            <select name="subjects" id="" class="form-control" required>
                                                <option value="">Select Subject</option>
                                                @foreach ($laSubjects as $laSubject)
                                                    <option value="{{ $laSubject->id }}">{{ $laSubject->default_title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="grades" id="" class="form-control" required>
                                                <option value="">Select Grade</option>
                                                @foreach ($laGrades as $laGrade)
                                                    <option value="{{ $laGrade->id }}">{{ $laGrade->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="sections" id="" class="form-control" required>
                                                <option value="">Select Section</option>
                                                @foreach ($laSections as $laSection)
                                                    <option value="{{ $laSection->id }}">{{ $laSection->name }}</option>
                                                @endforeach
                                            </select>
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
                    $(this).slideDown()
                },
                hide: function(e) {
                    confirm("Are you sure you want to delete this element?") && $(this).slideUp(e)
                },
                ready: function(e) {},
                isFirstItemUndeletable: true
            });
            $("#teacherForm").parsley();
        });
    </script>
@endsection
