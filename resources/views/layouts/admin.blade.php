<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="Description" content="">
		<meta name="Author" content="">
		<meta name="Keywords" content=""/>
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		<input type="hidden" id="csrf-token" value="{{ csrf_token() }}">
		@include('includes.admin.head')
	</head>

	<body class="main-body  app sidebar-mini">

		<!-- Loader -->
		<div id="global-loader">
			<img src="{{asset('assets/img/loaders/loader-4.svg')}}" class="loader-img" alt="Loader">
		</div>
		<!-- /Loader -->

		@include('includes.admin.sidebar')
		<!-- main-content -->
		<div class="main-content">
		@include('includes.admin.header')

			<!-- container -->
			<div class="container-fluid">
					@yield('breadcrumb')
					@yield('content')
		
		@include('includes.admin.footer')	
	</body>
</html>