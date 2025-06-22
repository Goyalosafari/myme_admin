<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title> @yield('title')</title>

<link rel="stylesheet" href="{{asset('css/bootstrap.css')}}">

<link rel="stylesheet" href="{{asset('vendors/chartjs/Chart.min.css')}}">

<link rel="stylesheet" href="{{asset('vendors/perfect-scrollbar/perfect-scrollbar.css')}}">
<link rel="stylesheet" href="{{asset('css/app.css')}}">
<link rel="shortcut icon" href="{{asset('images/favicon.svg')}}" type="image/x-icon">