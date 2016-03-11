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

    <div class="title">JWT Test</div>

    <div class="row">

        <div class="col-lg-12 col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">

                    <form method="POST" action="" accept-charset="UTF-8" class="jwt">

                        <div class="form-group">
                            <textarea cols="80" rows="10" name="jwt"></textarea>
                        </div>

                        <div class="form-group">
                            <input class="btn btn-default" type="submit" value="Submit">
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
    <script>

        // bind on click to activate/disable (state) buttons
        $('.panel-body').on('submit', '.jwt', function (e) {

            e.preventDefault();

            // retrieve form data
            var formData = $(this).serialize();

            $.ajax({
                url: '',
                type: 'POST',
                data: formData,
                statusCode: {
                    // determine status code and respond accordingly
                    200:
                            function () {
                                alert('success');
                            },
                    500:
                            function (data) {
                                alert('fail');
                            },
                    401:
                            function (data) {
                                alert('fail');
                            }
                }

            });

        });

    </script>

</div><!-- end container-fliud -->

@yield ('scripts')

</body>
</html>


