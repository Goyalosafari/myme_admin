<!DOCTYPE html>
<html lang="en">
    <head>
        @include('Layouts.head')
        @stack('styles')
    </head>
    <body>
        <div id="app">
            @include('Layouts.sidebar')
            <div id="main">
                @include('Layouts.navbar')
                @yield('content')
                @include('Layouts.footer')
            </div>
        </div>
        <script src="{{asset('js/feather-icons/feather.min.js')}}"></script>
        <script src="{{asset('vendors/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
        <script src="{{asset('js/app.js')}}"></script>
        


        <script src="{{asset('js/main.js')}}"></script>
        @stack('scripts')
    </body>
</html>
