@extends('layouts.admin')
@section('breadcrumb')
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Queries Replies') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Queries Replies</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection
@section('css')
    <style>
        .custom-thumbnail {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }

        .message-wrapper {
            padding: 30px;
            background-color: #EDEDED;
            box-shadow: 0px 2px 10px rgba(124, 141, 181, 0.12);
            border-radius: 10px;
        }

        .message-scroll {
            height: 668px;
            overflow-y: auto;
            margin-bottom: 18px;
        }

        .message-inner {
            padding-right: 8px;
            display: flex;
            flex-direction: column;
            justify-content: end;
        }

        .message-sender {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footnote {
            font-weight: 400;
            font-size: 14px;
            line-height: 150%;
        }

        .msg-box {
            padding: 10px 16px;
            border-radius: 12px !important;
            filter: drop-shadow(0px 2px 10px 0px #7C8DB51F);
            box-shadow: none !important;
            width: fit-content;
            margin-bottom: 12px;
            max-width: 400px;
        }

        .msg-box.align-left {
            margin-right: auto;
        }

        .msg-box.align-right {
            margin-left: auto;
        }

        .msg-box .footnote {
            color: var(--black);
            margin-bottom: 8px;
            display: inline-block;
        }

        .msg-box .caption-medium {
            color: var(--secondary);
        }

        .msg-box .footer-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .msg-box .footer-card i {
            color: var(--secondary);
            font-size: 12px;
        }

        .msg-box .footer-card i.active {
            color: var(--indigo);
        }

        .caption-medium {
            font-weight: 500;
        }

        .caption-regular,
        .caption-medium {
            font-size: 12px;
            line-height: 150%;
        }
    </style>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="card-title mg-b-0 mt-2">Student: {{ $laQuery->createdBy->name }} </h6>
                            <h6 class="card-title mg-b-0 mt-2">Mentor: {{ $laQuery->mentor->name }}</h6>
                            <h6 class="card-title mg-b-0 mt-2">Query: {{ $laQuery->description }}</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <div class="d-flex">
                            <div class="col-md-10"></div>
                            <div class="col-md-2">
                                @if ($laQuery->status_id == App\Models\LaQuery::STATUS_OPEN)
                                    <a class="form-control btn btn-success"
                                        onclick="sweetAlertAjax('patch','{{ route('admin.queries.replies', $laQuery->id) }}', 'Are You Sure Want To Close a Query')">Close
                                        Query</a>
                                @else
                                    <a class="form-control btn btn-success"
                                        onclick="sweetAlertAjax('patch','{{ route('admin.queries.replies', $laQuery->id) }}', 'Are You Sure Want To Open a Query')">Open
                                        Query</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="message-wrapper">
                        <div class="message-scroll" id="queries_list">
                            @include('pages.admin.queries.query-replies-html')
                        </div>
                        <div class="message-sender">
                            <input type="text" name="message" class="form-control" onkeypress="msgsent(event)"
                                id="message" placeholder="Write your message"
                                @if ($laQuery->status_id == App\Models\LaQuery::STATUS_CLOSED) disabled @endif>
                            <button type="button" class="btn btn-primary px-5 sent_messages_loader" id="send_message"
                                onclick="sentmsg({{ $queryId }}, {{ $loginUser }})"
                                @if ($laQuery->status_id == App\Models\LaQuery::STATUS_CLOSED) disabled @endif>Send</button>
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
        $(document).ready(function() {
            $('#focus-bottom').focus();
        });
        var queriesId = "{{ $queryId }}";

        function sentmsg(queryId, userId) {
            var message = $("#message").val();
            if (message == "") {
                return false;
            }
            var url = "{{ route('admin.queries.replies', ':queryId') }}";
            url = url.replace(':queryId', queryId);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            });
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    '_token': '{{ csrf_token() }}',
                    'userId': userId,
                    'message': message,
                },
                cache: false,
                beforeSend: function() {
                    $('#message').val('');
                    $('.sent_messages_loader').html(
                        '<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
                    $('.sent_messages_loader').attr('disabled', true);
                },
                success: function(response) {
                    $('.sent_messages_loader').html(
                        'Send');
                    $('.sent_messages_loader').attr('disabled', false);
                    chatrefresh();
                }
            });
        }

        function msgsent(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                $("#send_message").trigger('click');
            }
        }

        function chatrefresh() {
            var url = "{{ route('admin.queries.replies', ':queryId') }}";
            url = url.replace(':queryId', queriesId);
            $.ajax({
                url: url,
                type: "GET",
                cache: false,
                beforeSend: function() {
                    $('.message-inner').html(
                        '<img src="{{ asset('assets/img/loaders/loader-4.svg') }}" class="loader-img" alt="Loader">'
                    );
                },
                success: function(response) {
                    $("#queries_list").html(response.html);
                    $('#focus-bottom').focus();
                }
            });
        }
        if ("{{ $laQuery->status_id }}" == "{{ App\Models\LaQuery::STATUS_OPEN }}") {
            setInterval(function() {
                chatrefresh();
            }, 10000);
        }
    </script>
@endsection
