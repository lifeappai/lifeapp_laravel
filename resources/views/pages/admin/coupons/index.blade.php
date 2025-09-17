@extends('layouts.admin')
@section('css')
    <style>
        .custom-thumbnail {
            width: 30px;
            height: 30px;
            border-radius: 50%;
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
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-0 mt-2"></h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <form method="get" action="">
                            <div class="d-flex">
                                <div class="col-md-10"></div>
                                <div class="col-md-2">
                                    <a class="form-control btn btn-success" href="{{ route('admin.coupons.create') }}">Add
                                        Coupon</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" scope="col">Sr No.</th>
                                    <th class="border-bottom-0" scope="col">Title</th>
                                    <th class="border-bottom-0" scope="col">Image</th>
                                    <th class="border-bottom-0" scope="col">Coins</th>
                                    <th class="border-bottom-0" scope="col">Index</th>
                                    <th class="border-bottom-0" scope="col">Link</th>
                                    <th class="border-bottom-0" scope="col">Action</th>
                                </tr>
                            </thead>
                            <?php $i = 1; ?>
                            <tbody>
                                <?php $i = $coupons->perPage() * ($coupons->currentPage() - 1) + 1; ?>
                                @if (count($coupons) > 0)
                                    @foreach ($coupons as $coupon)
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>
                                                {{ $coupon->title }}
                                            </td>
                                            <td>
                                                @php
                                                    $couponImage = $coupon->media ? $imageBaseUrl . $coupon->media->path : '';
                                                @endphp
                                                @if ($couponImage)
                                                    <a class="image-popup-no-margins" href="{{ $couponImage }}">
                                                        <img alt="" src="{{ $couponImage }}"
                                                            class="custom-thumbnail">
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $coupon->coin }}
                                            </td>
                                            <td>
                                                <input type="text" class="form-control index-{{ $coupon->id }}"
                                                    id="index" name="index" value="{{ $coupon->index }}"
                                                    data-id="{{ $coupon->id }}"
                                                    onkeyup="this.value=this.value.replace(/[^0-9]/g, '')">

                                            </td>
                                            <td>
                                                {{ $coupon->link }}
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}"><button
                                                            class='btn btn-purple btn-sm mr-2'> Edit </button></a>
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger waves-effect waves-light ml-2"
                                                        onclick="sweetAlertAjax('delete','{{ route('admin.coupons.delete', $coupon->id) }}', 'Are You Sure Want To Delete')">Delete</button>
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
                        {{ $coupons->appends(Request::all())->links() }}
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
            var couponId = $(this).data("id");
            var url = "{{ route('admin.coupons.index.sequence', ':couponId') }}";
            url = url.replace(':couponId', couponId);
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
                    $(".index-" + couponId).val(html.index);
                    alert(html.message);
                }
            });
        });
    </script>
@endsection
