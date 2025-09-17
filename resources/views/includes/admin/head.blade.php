
		<!-- Title -->
		<title></title>

		<link rel="icon" href="{{asset('assets/img/brand/logo.png')}}" type="image/x-icon"/>


		@yield('css')

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<!--- Style css -->
		<link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
		<link href="{{asset('assets/css/skin-modes.css')}}" rel="stylesheet">

		<!--- Sidemenu css -->
		<link href="{{asset('assets/css/sidemenu.css')}}" rel="stylesheet">

		<!--- Animations css -->
		<link href="{{ asset('assets/css/sweetalert2.min.css') }}" rel="stylesheet">
		<link href="{{asset('assets/css/animate.css')}}" rel="stylesheet">
		<link rel="stylesheet" href="{{ asset('assets/css/jquery.dataTables.min.css') }}">
		<link href="{{ asset('assets/css/magnific-popup.css') }}" rel="stylesheet" type="text/css">
		<style>
			.table.dataTable thead .sorting::after,
			table.dataTable thead .sorting_asc::after,
			table.dataTable thead .sorting_desc::after {
				content: "" !important;
			}

			.card.radio-card {
				border: 2px solid #fff !important;
			}

			.radio-card.active {
				border: 2px solid #8a9ae8 !important;
			}
		</style>
		