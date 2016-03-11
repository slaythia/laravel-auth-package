@extends('app')

@section('content')

    @foreach($errors->all() as $error)
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ trans('status_codes.' . $error) }}
        </div>
    @endforeach

    {{-- Success Message --}}
    @if (session('message'))
        <div class="var-holder-success" data-message="{{trans('status_codes.'.session('message'))}}"></div>
        <script>
            // Display a success toast, with a title
            toastr.options = {
                closeButton: true,
                positionClass: 'toast-top-center',
                preventDuplicates: true,
                onclick: null,
                showDuration: 500,
                hideDuration: 500,
                timeOut: 3000,
                extendedTimeOut: 0,
                showMethod: 'fadeIn',
                hideMethod: 'fadeOut'
            };
            toastr.success($('.var-holder-success').attr('data-message'));
        </script>
    @endif
    <div class="container">

        <h2 class="page-title">Login</h2>

        <div class="row page-login">

            @if (in_array('local', $authMethods))
                <div class="col-lg-{{$colSize}} col-md-{{$colSize}}">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <form method="POST" action="{{ url('login') }}" accept-charset="UTF-8">

                                {!! csrf_field() !!}

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input class="form-control" required="required" placeholder="Email Address"
                                           name="email" type="email" id="email">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input class="form-control" required="required" placeholder="Password"
                                           name="password" type="password" value="" id="password">
                                </div>
                                {{--<div class="form-group">--}}
                                    {{--<label for="remember">Remember Me</label>--}}
                                    {{--<input name="remember" type="checkbox" value="1" id="remember">--}}
                                {{--</div>--}}
                                <div class="form-group">
                                    <input class="btn btn-default btn-action pull-right" type="submit" value="Login">
                                </div>

                            </form>
                            {{--<div class="form-group">--}}
                                {{--<a href="{{url('password')}}">Forgot Password?</a>--}}
                            {{--</div>--}}
                        </div>
                    </div>
                </div>
            @endif
            @if (in_array('google', $authMethods))
                <div class="col-lg-{{$colSize}} col-md-{{$colSize}}">
                    <div class="panel panel-default ">
                        <div class="panel-body page-login__google-login">
                            <a class="btn full-width" href="{{ url('redirect/google') }}">
                                <img class="img-responsive center" src="{!! asset('/images/gplus-signin.png') !!}"
                                     width="300">
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            @if (in_array('ldap', $authMethods))
                <div class="col-lg-{{$colSize}} col-md-{{$colSize}}">
                    <div class="panel panel-default row-eq-height">
                        <div class="panel-body">

                            <form method="POST" action="{{ url('login/ldap') }}" accept-charset="UTF-8">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <label for="email">Ldap Username</label>
                                    <input class="form-control" required="required" placeholder="Email Address"
                                           name="username" type="text">
                                </div>
                                <div class="form-group">
                                    <label for="password">Ldap Password</label>
                                    <input class="form-control" required="required" placeholder="Password"
                                           name="password" type="password" value="">
                                </div>
                                <div class="form-group">
                                    <input class="btn btn-default pull-right" type="submit" value="Ldap Login">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop
