<!DOCTYPE html>
<html>
<head>

    <title>EpiCollect5</title>

    {{--Favicon stuff --}}
    <link rel="apple-touch-icon" sizes="57x57" href="{{!! asset('/images/favicons/apple-touch-icon-57x57.png') !!}}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{!! asset('/images/favicons/apple-touch-icon-60x60.png') !!}}">
    <link rel="apple-touch-icon" sizes="72x72" href="{!! asset('/images/favicons/apple-touch-icon-72x72.png') !!}">
    <link rel="apple-touch-icon" sizes="76x76" href="{!! asset('/images/favicons/apple-touch-icon-76x76.png') !!}">
    <link rel="apple-touch-icon" sizes="114x114" href="{!! asset('/images/favicons/apple-touch-icon-114x114.png') !!}">
    <link rel="apple-touch-icon" sizes="120x120" href="{!! asset('/images/favicons/apple-touch-icon-120x120.png') !!}">
    <link rel="apple-touch-icon" sizes="144x144" href="{!! asset('/images/favicons/apple-touch-icon-144x144.png') !!}">
    <link rel="apple-touch-icon" sizes="152x152" href="{!! asset('/images/favicons/apple-touch-icon-152x152.png') !!}">
    <link rel="apple-touch-icon" sizes="180x180" href="{!! asset('/images/favicons/apple-touch-icon-180x180.png') !!}">
    <link rel="icon" type="image/png" href="{!! asset('/images/favicons/favicon-32x32.png') !!}" sizes="32x32">
    <link rel="icon" type="image/png" href="{!! asset('/images/favicons/android-chrome-192x192.png') !!}"
          sizes="192x192">
    <link rel="icon" type="image/png" href="{!! asset('/images/favicons/favicon-96x96.png') !!}" sizes="96x96">
    <link rel="icon" type="image/png" href="{!! asset('/images/favicons/favicon-16x16.png') !!}" sizes="16x16">
    <link rel="manifest" href="{!! asset('/images/favicons/manifest.json') !!}">
    <link rel="mask-icon" href="{!! asset('/images/favicons/safari-pinned-tab.svg') !!}" color="#673C90">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{!! asset('/images/favicons/mstile-144x144.png') !!}">
    <meta name="theme-color" content="#673C90">

    <link rel="stylesheet" type="text/css" href="{!! asset('css/vendor.css') !!}">
    <link rel="stylesheet" type="text/css" href="{!! asset('css/app.css') !!}">


    @yield ('css')
    <script src="{!! asset('/js/vendor.js') !!}"></script>
    <script>
        var SITE_URL = '{{ url('') }}'

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    </script>

</head>
<body>
<!--[if lt IE 9]>
<p closeass="browsehappy">You are using an <strong>outdated</strong> browser.
    Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.
</p>
<![endif]-->
<div class="container-fluid">

    @include('navbar')

    @yield('content')

</div>

@include('footer')

@yield ('scripts')

</body>
</html>
