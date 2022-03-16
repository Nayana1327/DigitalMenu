<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CMS</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" >

    <!-- Styles -->
    <link href="{{ asset('admin/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/custom.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link href="{{ asset('admin/css/buttons.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admin/css/jquery.timepicker.min.css') }}">
    <link href="{{ asset('admin/css/jquery-ui.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"/> -->

    <!-- Toastr -->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/css/toastr.min.css')}}">

</head>
<body>
    @guest
        <div id="app">
            <main class="py-4">
                @yield('content')
            </main>
        </div>
    @else
        <div id="app">
            @include('admin.layouts.sections.header')
            @include('admin.layouts.sections.sidebar')
            <main class="py-4">
                @yield('content')
            </main>
            @include('admin.layouts.sections.footer')
        </div>
    @endguest
    

    <!-- Scripts -->
    <script src="{{ asset('admin/js/app.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('admin/js/dataTables.buttons.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/js/buttons.colVis.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/js/jszip.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/js/pdfmake.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/js/vfs_fonts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/js/buttons.html5.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('admin/js/jquery.timepicker.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('admin/js/jquery-ui.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <!-- Toastr -->
	<script type="text/javascript" src="{{ asset('admin/js/toastr.min.js') }}"></script>
    <script type="text/javascript">
        toastr.options.closeButton = true;
        toastr.options.escapeHtml = true;
        toastr.options.newestOnTop = false;
    </script>
    
    @yield('script')
</body>
</html>
