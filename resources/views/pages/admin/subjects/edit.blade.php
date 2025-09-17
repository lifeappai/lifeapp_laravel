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
                    <form method="POST" action="{{ route('admin.subjects.update', $subject->id) }}" id="languageForm"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
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
                                    @if (count($subject->title) > 0)
                                        @foreach ($subject->title as $keyData => $title)
                                            <tr data-repeater-item="">
                                                <td><select name="language" id="language1" class="form-control" required>
                                                        @foreach ($languages as $language)
                                                            <option value="{{ $language->slug }}"
                                                                @if ($language->slug == $keyData) selected @endif>
                                                                {{ $language->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td> <input type="text" class="form-control" id="title"
                                                        name="title" placeholder="Enter Title" value="{{ $title }}"
                                                        autocomplete="off" required></td>
                                                <td> <input type="text" class="form-control" id="heading"
                                                        name="heading" placeholder="Enter Heading"
                                                        @if (isset($subject->heading[$keyData])) value="{{ $subject->heading[$keyData] }}" @endif
                                                        autocomplete="off" required></td>
                                                <td>
                                                    <input type="file" class="form-control" id="image" name="image"
                                                        autocomplete="off" onchange="loadFile(this)">
                                                </td>
                                                <td>
                                                    @if (isset($subject->image[$keyData]))
                                                        @php $mediaPath = $subject->getMediaPath($subject->image[$keyData]); @endphp
                                                        @if ($mediaPath)
                                                            <input type="hidden" class="form-control" id="media_id"
                                                                name="media_id" value="{{ $subject->image[$keyData] }}">
                                                            <a target="_blank"
                                                                href="{{ $imageBaseUrl . $mediaPath->path }}">
                                                                View Image
                                                            </a>
                                                            <br>
                                                        @endif
                                                    @endif
                                                    <input data-repeater-delete="" type="button"
                                                        class="btn btn-danger waves-effect waves-light" value="Delete">
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
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
                    $(this).find('a').css("display", "none");
                    $(this).find('input[type="file"]').attr("required", true);
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
