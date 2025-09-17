@extends('layouts.admin')
@section('css')
    <style>
    </style>
@endsection
@section('breadcrumb')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Questions') }} ({{ $questions->total() }})</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Questions</li>
                </ol>
            </nav>
        </div>
    </div>
    <div id="error">
        @include('includes.message')
    </div>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-0 mt-2"></h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                </div>
                <div class="card-header pb-0">
                    <div class="d-flex">
                        <div class="col-md-10">
                            <form method="GET">
                                <div class="d-flex">
                                    <div class="col-md-3">
                                        <select name="la_level_id" class="form-control">
                                            <option value="">Select Level</option>
                                            @foreach ($levels as $key => $level)
                                                <option value="{{ $level->id }}"
                                                    @if ($levelId == $level->id) selected @endif>
                                                    {{ $level->default_title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="la_subject_id" class="form-control">
                                            <option value="">Select subject</option>
                                            @foreach ($subjects as $key => $subject)
                                                <option value="{{ $subject->id }}"
                                                    @if ($subjectId == $subject->id) selected @endif>
                                                    {{ $subject->default_title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="la_topic_id" class="form-control">
                                            <option value="">Select Topic</option>
                                            @foreach ($laTopics as $key => $laTopic)
                                                <option value="{{ $laTopic->id }}"
                                                    @if ($topicId == $laTopic->id) selected @endif>
                                                    {{ $laTopic->default_title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="type" class="form-control">
                                            <option value="">Select Type</option>
                                            <option value="{{ App\Enums\GameType::QUIZ }}"
                                                @if ($type == App\Enums\GameType::QUIZ) selected @endif>Quiz</option>
                                            <option value="{{ App\Enums\GameType::RIDDLE }}"
                                                @if ($type == App\Enums\GameType::RIDDLE) selected @endif>Riddle</option>
                                            <option value="{{ App\Enums\GameType::PUZZLE }}"
                                                @if ($type == App\Enums\GameType::PUZZLE) selected @endif>Puzzle</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="status" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="{{ App\Enums\StatusEnum::ACTIVE }}"
                                                @if (isset($statusId) && $statusId == App\Enums\StatusEnum::ACTIVE) selected @endif>Published</option>
                                            <option value="{{ App\Enums\StatusEnum::DEACTIVE }}"
                                                @if (isset($statusId) && $statusId == App\Enums\StatusEnum::DEACTIVE) selected @endif>Drafted</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-3 mt-3 d-flex">
                                        <button type="submit" class="btn btn-success  mr-3">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <div class="d-flex">
                            <div class="col-md-7"></div>
                            <div class="col-md-2">
                                <a class="form-control btn btn-success"
                                    href="{{ route('admin.questions.create', request()->all()) }}">Add
                                    Questions</a>
                            </div>
                            <div class="col-md-3">
                                <a class="form-control btn btn-success"
                                    href="{{ route('admin.import-questions.index', request()->all()) }}">Add
                                    Questions view Excel Sheet</a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" scope="col">Sr No.</th>
                                    <th class="border-bottom-0" scope="col">Subject</th>
                                    <th class="border-bottom-0" scope="col">Level</th>
                                    <th class="border-bottom-0" scope="col">Topic</th>
                                    <th class="border-bottom-0" scope="col">Type</th>
                                    <th class="border-bottom-0" scope="col">Title</th>
                                    <th class="border-bottom-0" scope="col">Status</th>
                                    <th class="border-bottom-0" scope="col">Index</th>
                                    <th class="border-bottom-0" scope="col">Action</th>
                                </tr>
                            </thead>
                            <?php $i = 1; ?>
                            <tbody>
                                <?php $i = $questions->perPage() * ($questions->currentPage() - 1) + 1; ?>
                                @if (count($questions) > 0)
                                    @foreach ($questions as $question)
                                        <tr>
                                            <td @if ($question->questionOptions->count() == 0 || $question->answer_option_id == null) style="background: red;" @endif>
                                                {{ $i }}</td>
                                            <td>{{ $question->subject ? $question->subject->default_title : '' }}</td>
                                            <td>{{ $question->laLevel ? $question->laLevel->default_title : '' }}
                                            </td>
                                            <td>{{ $question->laTopic ? $question->laTopic->default_title : '' }}</td>
                                            <td>
                                                @if ($question->type == App\Enums\GameType::QUIZ)
                                                    Quiz
                                                @elseif($question->type == App\Enums\GameType::RIDDLE)
                                                    Riddle
                                                @elseif($question->type == App\Enums\GameType::PUZZLE)
                                                    Puzzle
                                                @endif
                                            </td>
                                            <td>
                                                @if (count($question->title) > 0)
                                                    @foreach ($question->title as $key => $title)
                                                        @if ($question->question_type == App\Enums\GameType::QUESTION_TYPE['IMAGE'])
                                                            @php
                                                                $media = $question->getMedia($title);
                                                            @endphp
                                                            @if ($media)
                                                                {{ $key }} => <a target="_blank"
                                                                    href="{{ $imageBaseUrl . $media->path }}">
                                                                    View Image
                                                                </a>
                                                                <br>
                                                            @endif
                                                        @else
                                                            {{ $key }} => {{ $title }} <br>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if (App\Enums\StatusEnum::DEACTIVE == $question->status)
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger waves-effect waves-light"
                                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.questions.status', $question->id) }}', 'Are You Sure To Publish')">Draft</button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-success waves-effect waves-light"
                                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.questions.status', $question->id) }}', 'Are You Sure To Draft')">Publish</button>
                                                @endif
                                            </td>
                                            <td>
                                                <input type="text" class="form-control index-{{ $question->id }}"
                                                    id="index" name="index" value="{{ $question->index }}"
                                                    data-id="{{ $question->id }}"
                                                    onkeyup="this.value=this.value.replace(/[^0-9]/g, '')">
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('admin.questions.edit', $question->id) }}"><button
                                                            class='btn btn-purple btn-sm'> Edit </button></a>
                                                    <a href="{{ route('admin.questions.answers', $question->id) }}"><button
                                                            class='btn btn-pink ml-2 btn-sm'> Answers </button></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="10">No Data Found </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        {{ $questions->appends(Request::all())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).on('change', '#index', function(event) {
            var laQuestionId = $(this).data("id");
            var url = "{{ route('admin.questions.index.sequence', ':laQuestionId') }}";
            url = url.replace(':laQuestionId', laQuestionId);
            $.ajax({
                url: url,
                type: "PATCH",
                data: {
                    _token: "{{ csrf_token() }}",
                    'index': this.value,
                },
                cache: false,
                beforeSend: function() {},
                success: function(html) {
                    $(".index-" + laQuestionId).val(html.index);
                    alert(html.message);
                }
            });
        });
    </script>
@endsection
