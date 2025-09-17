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
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.subjects.store') }}" id="languageForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            {{-- <div class="col-md-6  mg-b-20">
                                <h6>Is Coupon Added<span class="text-danger">*</span></h6>
                                <select name="is_coupon_available" id="is_coupon_available" class="form-control" required>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-6  mg-b-20">
                                <h6>Coupon Code Count<span class="text-danger">*</span></h6>
                                <input type="text" name="coupon_code_count" id="coupon_code_count" class="form-control"
                                    value="0" onkeypress='return event.charCode >= 48 && event.charCode <= 57'
                                    required>
                                <label id="coupon_code_count_error" style="color:red">
                            </div> --}}
                            <table class="table table-bordered repeater">
                                <thead class="align-middle">
                                    <tr>
                                        <th>Language</th>
                                        <th>Title</th>
                                        <th>Heading</th>
                                        <th>Image</th>
                                        <th>
                                            <input data-repeater-create="" type="button"
                                                class="btn btn-success mt-3 mt-lg-0" value="Add">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle" data-repeater-list="subject_translation">
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
                                            <input type="text" class="form-control" id="heading" name="heading"
                                                placeholder="Enter Heading" required="" autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="file" class="form-control" id="image" name="image"
                                                required="" autocomplete="off" onchange="loadFile(this)">
                                        </td>
                                        <td>
                                            <input data-repeater-delete="" type="button"
                                                class="btn btn-danger waves-effect waves-light" value="Delete">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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

        // document.getElementById("submit").addEventListener("click", function() {
        //     var type = 0;
        //     $("#coupon_code_count_error").html("");
        //     if ($('#is_coupon_available').val() == 0) {
        //         $("#coupon_code_count").val(0);
        //         type = 1;
        //     } else {
        //         if ($("#coupon_code_count").val() > 0) {
        //             type = 1;
        //         }
        //     }

        //     if (type == 0) {
        //         $("#coupon_code_count_error").html("Please Enter Minimum 1 Coupon Code Count");
        //         $("#coupon_code_count").focus();
        //         return false;
        //     } else {
        //         $("#submit").attr("type", "submit");
        //     }

        // });
    </script>
@endsection
