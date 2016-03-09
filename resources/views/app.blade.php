<!DOCTYPE html>
<html>
<head>

    <title>EpiCollect5</title>

    <link rel="stylesheet" type="text/css" href="{!! asset('css/bootstrap.min.css') !!}">

    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    @yield ('css')
    <script src="{!! asset('/js/jquery-1.11.3.min.js') !!}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    </script>
    <script src="{!! asset('/js/jquery.autocomplete.min.js') !!}"></script>
    <script src="{!! asset('/js/bootstrap.min.js') !!}"></script>
    <script>var SITE_URL = '{{ url('') }}';</script>
    
</head>
<body>
<div class="container-fluid">

    @include('navbar')

    @yield('content')

</div><!-- end container-fliud -->

    @yield ('scripts')

</body>
</html>
